<?php
/**
 * Flint Technology Ltd
 *
 * This module was developed by Flint Technology Ltd (http://www.flinttechnology.co.uk).
 * For support or questions, contact us via feefo@flinttechnology.co.uk 
 * Support website: https://www.flinttechnology.co.uk/support/projects/feefo/
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA bundled with this package in the file LICENSE.txt.
 * It is also available online at http://www.flinttechnology.co.uk/store/module-license-1.0
 *
 * @package     flint_feefo-ce-2.0.9.zip
 * @registrant  Andi Briggs, Prestige Drinks
 * @license     E5824A84-C060-4C80-8D33-0293B407325E
 * @eula        Flint Module Single Installation License (http://www.flinttechnology.co.uk/store/module-license-1.0
 * @copyright   Copyright (c) 2014 Flint Technology Ltd (http://www.flinttechnology.co.uk)
 */
?>
<?php
class Flint_Feefo_Model_Config_Product extends Flint_Feefo_Model_Config_Abstract
{

    protected $_defaultPath = 'flint_feefo/product';

    public function active( $store = null ) {
        return $this->getConfigData( $store, 'active' );
    }

    public function getLogoPosition( $store = null ) {
        return $this->getConfigData( $store, 'logo_position' );
    }

    public function getLogoTemplateProduct( $store = null ) {
        return $this->getConfigData( $store, 'logo_template_product' );
    }

    public function getLogoTemplateCatalog( $store = null ) {
        return $this->getConfigData( $store, 'logo_template_catalog' );
    }

    public function getLogoTemplateBasket( $store = null ) {
        return $this->getConfigData( $store, 'logo_template_basket' );
    }

    public function getMode( $store = null ) {
        return $this->getConfigData( $store, 'mode' );
    }

    public function getForfeedback( $store = null ) {
        return $this->getConfigData( $store, 'forfeedback' );
    }

    public function getOrder( $store = null ) {
        return $this->getConfigData( $store, 'order' );
    }

    public function getSince( $store = null ) {
        return $this->getConfigData( $store, 'since' );
    }

    public function getLimit( $store = null ) {
        return $this->getConfigData( $store, 'limit' );
    }

    public function getAdditional( $store = null ) {
        return $this->getConfigData( $store, 'additional' );
    }

}
