<?php

class Ebizmarts_SagePayReporting_Model_Cron {

	/**
	 * Retrieve fraud score (3rd man) for transactions that do not have score.
	 * @param  $cron Cron object
	 * @return Ebizmarts_SagePayReporting_Model_Cron
	 */
	public function getThirdmanScores($cron) {

        $logPrefix = "[CRON] ";
        //Sage_Log::log($logPrefix . "Starting fraud checks... ", null, 'SagePaySuite_Thirdman.log');

		$fraudTblName = Mage::getSingleton('core/resource')->getTableName('sagepayreporting_fraud');
		$transactions = Mage::getResourceModel('sagepaysuite2/sagepaysuite_transaction_collection');
		$transactions->addFieldToSelect(array('order_id', 'vendor_tx_code', 'vps_tx_id', 'tx_type'));

		$transactions
		->getSelect()
		->where("`main_table`.`order_id` IS NOT NULL AND (`main_table`.`order_id` NOT IN (SELECT `order_id` FROM ". $fraudTblName ."))")
		->order("main_table.created_at DESC")
		->limit(20);

		$now = strtotime("now");


		foreach($transactions as $_trn) {

			$update = $_trn->updateFromApi();

			if (!$update->getFraud()) {
                //Sage_Log::log($logPrefix . "3rd man check for " . $_trn->getVendorTxCode() . ": NO RESULT", null, 'SagePaySuite_Thirdman.log');
				continue;
			}

			try {

				$rs             = $update->getFraud();
				$noresult       = ((string)$rs->getThirdmanAction() == 'NORESULT');
				$orderPlusOneDay = strtotime("+1 day", strtotime($_trn->getCreatedAt()));

                Sage_Log::log($logPrefix . "3rd man check for " . $_trn->getVendorTxCode() . ": " . (string)$rs->getThirdmanAction(), null, 'SagePaySuite_Thirdman.log');

				if(!$noresult || ($now > $orderPlusOneDay)) {

					/**
					 *  Automatic fulfill
					 */
					$canAuthorise = ($_trn->getTxType() == 'AUTHENTICATE' && !$_trn->getAuthorised());
					$canRelease   = ($_trn->getTxType() == 'DEFERRED' && !$_trn->getReleased());

                    if(($canAuthorise || $canRelease) && $noresult && ($now > $orderPlusOneDay)){
                        Sage_Log::log($logPrefix . "Auto invoicing (FORCED) for " . $_trn->getVendorTxCode(), null, 'SagePaySuite_Thirdman.log');
                        Mage::getModel('sagepaysuite/api_payment')->invoiceOrder($_trn->getOrderId(), Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                    }else{

                        $rank = ( $this->_getCanRank() && ($this->_getRank() >= (int)$rs->getThirdmanScore()) );

                        if(($canAuthorise || $canRelease) && $rank){
                            Sage_Log::log($logPrefix . "Auto invoicing for " . $_trn->getVendorTxCode(), null, 'SagePaySuite_Thirdman.log');
                            Mage::getModel('sagepaysuite/api_payment')->invoiceOrder($_trn->getOrderId(), Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                        }else{
                            if($this->_getRank() < (int)$rs->getThirdmanScore()){
                                Sage_Log::log($logPrefix . "No rank for " . $_trn->getVendorTxCode() . ": " . (int)$rs->getThirdmanScore(), null, 'SagePaySuite_Thirdman.log');
                            }elseif(!$canAuthorise && !$canRelease){
                                Sage_Log::log($logPrefix . "Can not authorize nor release " . $_trn->getVendorTxCode(), null, 'SagePaySuite_Thirdman.log');
                            }
                        }
                    }
				}

			} catch (Exception $e) {
				Sage_Log::logException($e);
			}

		}
	}

	protected function _getCanRank()
	{
		return Mage::getStoreConfigFlag('payment/sagepaysuite/auto_fulfill_low_risk_trn');
	}

	protected function _getRank()
	{
		return (int)Mage::getStoreConfig('payment/sagepaysuite/auto_fulfill_low_risk_trn_value');
	}

}