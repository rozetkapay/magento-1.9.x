<?php

class RozetkaPay_RozetkaPay_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_AVAILABLE_PERIOD        = 'payment/rozetkapay_rozetkapay/available_period';
    const XML_PATH_MERCHANT_TYPE           = 'payment/rozetkapay_rozetkapay/merchant_type';
    const XML_PATH_SHOP_ID                 = 'payment/rozetkapay_rozetkapay/shopident';
    const XML_PATH_ORDER_STATUS_PROCESSING = 'payment/rozetkapay_rozetkapay/order_status_processing';
    const XML_PATH_ORDER_STATUS_NEW        = 'payment/rozetkapay_rozetkapay/order_status_new';
    const XML_PATH_PAYMENT_DESCRIPTION     = 'payment/rozetkapay_rozetkapay/payment_description';

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {

        $currencySymbol = Mage::app()
            ->getLocale()
            ->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
            ->getSymbol();

        return $currencySymbol;
    }

    /**
     * Description
     * @return string
     */
    public function getRozetkaPayDescription() {
        return $this->escapeHtml(Mage::getStoreConfig(self::XML_PATH_PAYMENT_DESCRIPTION));
    }

    /**
     * Order status new
     * @return string
     */
    public function getOrderStatusNew() {
        return Mage::getStoreConfig(self::XML_PATH_ORDER_STATUS_NEW);
    }

    /**
     * Order status processing
     * @return string
     */
    public function getOrderStatusProcessing() {
        return Mage::getStoreConfig(self::XML_PATH_ORDER_STATUS_PROCESSING);
    }

    /**
     * Merchant id
     * @return string
     */
    public function getShopId() {
        return Mage::getStoreConfig(self::XML_PATH_SHOP_ID);
    }

    /**
     * Merchant type
     * @return string
     */
    public function getMerchantType() {
        return Mage::getStoreConfig(self::XML_PATH_MERCHANT_TYPE);
    }

}
