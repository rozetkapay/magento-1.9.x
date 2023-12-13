<?php

class RozetkaPay_RozetkaPay_Model_Method_Payment extends Mage_Payment_Model_Method_Abstract {

    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_CANCEL  = 'CANCELED';

    protected $_code = 'rozetkapay_rozetkapay';

    protected $_formBlockType = 'rozetkapay_rozetkapay/checkout_method';
    protected $_infoBlockType = 'rozetkapay_rozetkapay/info_payment';

    protected $_canUseForMultishipping = false;
    protected $_canUseInternal = false;
    protected $_canOrder       = true;

    public function assignData($data) {
        return $this;
    }

    /**
     * Rozetkapay helper
     * @return RozetkaPay_RozetkaPay_Helper_Data
     */
    protected function rozetkapayHelper() {
        return Mage::helper('rozetkapay_rozetkapay');
    }

    /**
     * Checkout session
     * @return Mage_Checkout_Model_Session
     */
    protected function checkoutSession() {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Pay action getter compatible with payment model
     *
     * @see Mage_Sales_Model_RozetkaPay::place()
     * @return string
     */
    public function getConfigPaymentAction(){
        return Mage_Payment_Model_Method_Abstract::ACTION_ORDER;
    }


    /**
     * Return Order place redirect url
     * @return string
     */
    public function getOrderPlaceRedirectUrl() {
        $token = $this->checkoutSession()->getRozetkaPayToken();
        if ($token) {
            return Mage::getUrl('checkout/onepage/success');
        }
        return Mage::getUrl('checkout/onepage', array('_secure' => true));
    }

    /**
     * Order payment
     *
     * @param Mage_Sales_Model_Order_RozetkaPay $payment
     * @param float $amount
     * @return RozetkaPay_RozetkaPay_Model_Method_RozetkaPay
     */
    public function order(Varien_Object $payment, $amount)
    {
        /*@var $order Mage_Sales_Model_Order*/
        $order = $payment->getOrder();
        if ($order instanceof Mage_Sales_Model_Order){
            $result = $this->getRozetkaPayToken($order);
            if (!$result->getStatus()) {
                Mage::throwException($result->getMessage());
            } elseif ($result->getToken()) {
                $payment->setIsTransactionPending(true);
                $payment->setIsFraudDetected(false);

                $this->checkoutSession()->setRozetkaPayToken($result->getToken());
                $order->addStatusHistoryComment("order_id {$result->getToken()}", Mage_Sales_Model_Order::STATE_PROCESSING);
                $order->save();
            }
        }

        parent::order($payment, $amount);
    }

    /**
     * Get response from gateway
     * 
     * @param Mage_Sales_Model_Order $order
     * @return Varien_Object
     */
    private function getRozetkaPayToken($order) {
        $rozetkapayHelper = Mage::helper('rozetkapay_rozetkapay/rozetkapay');

        $result = array('status' => false);
        $response = $rozetkapayHelper->createPayment($order)->getResponse();
        if (isset($response['is_success']) && $response['is_success']) {
            $result['status'] = true;
            $result['token'] = $response['id'];
        } else {
            $result['message'] = isset($response['message']) ? $response['message'] : '';
        }

        return new Varien_Object($result);
    }

}
