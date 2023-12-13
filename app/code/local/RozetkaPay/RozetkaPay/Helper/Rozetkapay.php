<?php
class RozetkaPay_RozetkaPay_Helper_Rozetkapay extends Mage_Core_Helper_Abstract
{

    protected $test_store_id = 'a6a29002-dc68-4918-bc5d-51a6094b14a8';

    protected $test_key = 'XChz3J8qrr';

    protected $store_id;

    protected $key;

    protected $link = 'https://api.rozetkapay.com/';

    protected $response;

    protected $test_mode;

    public function __construct($test_mode = false) {
        $this->key = Mage::getStoreConfig('payment/rozetkapay_rozetkapay/shoppassword');
        $this->store_id = Mage::getStoreConfig('payment/rozetkapay_rozetkapay/shopident');

        if (Mage::getStoreConfig('payment/rozetkapay_rozetkapay/test_mode')) {
          $this->testMode();
        }
    }

    public function getLink() {
        return $this->link;
    }

    public function getStoreId() {
        if ($this->test_mode) {
          return $this->test_store_id;
        }
        return $this->store_id;
    }

    public function getKey() {
        if ($this->test_mode) {
          return $this->test_key;
        }
        return $this->key;
    }

    public function testMode() {
        $this->test_mode = true;
        return $this;
    }


    public function getResponse($to_array = true) {
        return $to_array ? json_decode($this->response, true) : $this->response;
    }

    public function createPayment($order) {
        $grand_total = $order->getShippingAmount() ? intval($order->getGrandTotal()) - $order->getShippingAmount() : intval($order->getGrandTotal());
        $data = array(
            "external_id" => $order->getIncrementId(),
            "amount" => number_format($grand_total, 2, '.', ''),
            "currency" => $order->getOrderCurrencyCode(),
            "callback_url" => Mage::getUrl('rozetkapay/payment/callback', array('order_id' => $order->getRealOrderId())),
        );

        $logString = __FUNCTION__;
        $this->_log($logString . PHP_EOL, $data);

        $this->_requestData($data, 'api/payments/v1/new');

        $this->_log($logString . PHP_EOL . $this->response);

        return $this;
    }

    public function confirmPayment($order) {
        $data["external_id"] = $order->getIncrementId();

        $logString = __FUNCTION__;
        $this->_log($logString . PHP_EOL, $data);

        $this->_requestData($data, 'api/payments/v1/confirm');

        $this->_log($logString . PHP_EOL . $this->response);

        return $this;
    }

    public function cancelPayment($order) {
        $data["external_id"] = $order->getIncrementId();

        $logString = __FUNCTION__;
        $this->_log($logString . PHP_EOL, $data);

        $this->_requestData($data, 'api/payments/v1/cancel');

        $this->_log($logString . PHP_EOL . $this->response);

        return $this;
    }

    public function refundPayment($order) {
        $data["external_id"] = $order->getIncrementId();

        $logString = __FUNCTION__;
        $this->_log($logString . PHP_EOL, $data);

        $this->_requestData($data, 'api/payments/v1/refund');

        $this->_log($logString . PHP_EOL . $this->response);

        return $this;
    }

    public function infoPayment($order) {
        $data["external_id"] = $order->getIncrementId();

        $logString = __FUNCTION__;
        $this->_log($logString . PHP_EOL, $data);

        $this->_requestData($data, 'api/payments/v1/info' . '?external_id=' . $data['external_id'], false);

        $this->_log($logString . PHP_EOL . $this->response);

        return $this;
    }

    protected function _requestData($data, $url, $post = true) {
        $requestData = is_array($data) ? json_encode($data) : $data;
        $url = "{$this->getLink()}$url";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . $this->_getToken(),
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
        }

        $result = curl_exec($ch);
        if ($result) {
            $this->response = $result;
        } elseif ($error = curl_error($ch)) {
            $this->response = json_encode(array('message' => $error . '. CURL.'));
        } else {
            $this->response = json_encode(array('message' => 'Undefined error.'));
        }

        curl_close($ch);
    }

    protected function _getToken() {
        return base64_encode($this->getStoreId() . ":" . $this->getKey());
    }

    protected function _log($string, $data = null) {
        if (!is_null($data)) {
            $string .= is_array($data) ? json_encode($data) : $data;
        }

        Mage::log($string, null, 'rozetkapay.log');
    }
}
