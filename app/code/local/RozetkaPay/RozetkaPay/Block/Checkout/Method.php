<?php

/**
 * RozetkaPay notification "form"
 */
class RozetkaPay_RozetkaPay_Block_Checkout_Method extends Mage_Payment_Block_Form
{

      /**
     * Must be extended in child classes
     *
     * @var null
     */
    protected $_paymentModel = null;

    /**
     * Set template with message
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('rozetkapay/checkout/method.phtml');
    }
    
    /**
     * RozetkaPay helper
     * @return RozetkaPay_RozetkaPay_Helper_Data
     */
    protected function rozetkapayHelper() {
        return Mage::helper('rozetkapay_rozetkapay/data');
    }
    
    /**
     * RozetkaPay description
     * @return string
     */
    public function getDescription() {
        return $this->rozetkapayHelper()->getRozetkaPayDescription();
    }

}