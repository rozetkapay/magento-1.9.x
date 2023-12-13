<?php

class RozetkaPay_RozetkaPay_Adminhtml_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{

    public function rozetkaPayAction() {
        $requestParams = $this->getRequest()->getParams();
        $order = Mage::getModel('sales/order')->load($requestParams['order_id']);
        $rozetkapayHelper = Mage::helper('rozetkapay_rozetkapay/rozetkapay');
        $action = $requestParams['action'];
        if ($action == 'confirm') {
            $result = $rozetkapayHelper->confirmPayment($order)->getResponse();
        } elseif ($action == 'cancel') {
            $result = $rozetkapayHelper->cancelPayment($order)->getResponse();
        } elseif ($action == 'info') {
            $result = $rozetkapayHelper->infoPayment($order)->getResponse();
        } elseif ($action == 'refund') {
            $result = $rozetkapayHelper->refundPayment($order)->getResponse();
        }

        $message = $action . "<br>";
        if (isset($result['message']) && !empty($result['message'])) {
            $message .= $result['message'];
        } elseif (isset($result['details']['status_description']) && !empty($result['details']['status_description'])) {
            $message .= $result['details']['status_description'];
        } elseif (isset($result['purchase_details']['status_description']) && !empty($result['purchase_details']['status_description'])) {
            $message .= $result['purchase_details']['status_description'];
        } else {
            $message .= 'NULL';
        }

        if (
            (isset($result['is_success']) && $result['is_success']) ||
            (isset($result['id']) && $result['id'])
        ) {
            $order->addStatusHistoryComment($message, Mage_Sales_Model_Order::STATE_PROCESSING)
                ->save();

            $this->_getSession()->addSuccess($message);
        } else {
            $order->addStatusHistoryComment($message, Mage_Sales_Model_Order::STATE_CANCELED)->save();

            $this->_getSession()->addError($message);
        }

        $this->_redirect('*/sales_order/view', array('order_id' => $order->getId()));
    }

}