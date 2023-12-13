<?php

class RozetkaPay_RozetkaPay_PaymentController extends Mage_Core_Controller_Front_Action
{
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
     * Load order by increment id
     * @param mixed $incrementId Order transaction id
     * @return Mage_Sales_Model_Order Order object
     */
    protected function getOrderByIncrementId($incrementId) {
        if ($incrementId){
            return Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        }
    }

    /**
     * Customer return processing
     */
    public function callbackAction()
    {
        try {
            $postData = Mage::helper('core')->jsonDecode($this->getRequest()->getRawBody());

            if (is_array($postData)) {
                if (isset($postData['is_success']) || isset($postData['message'])) {
                    $orderId = $this->getRequest()->getParam('order_id');
                    try {
                        /*@var $order Mage_Sales_Model_Order*/
                        $order = $this->getOrderByIncrementId($orderId);
                        if (is_object($order) && $order->getId()) {
                            if (isset($postData['message'])) {
                                $comment = $postData['message'];
                            } elseif (isset($result['details']['status_description']) && !empty($result['details']['status_description'])) {
                                $comment .= $result['details']['status_description'];
                            } else {
                                $comment = 'NULL';
                            }

                            if (isset($postData['is_success']) && $postData['is_success']) {
                                if ($comment) {
                                    $order->addStatusHistoryComment($comment, $this->rozetkapayHelper()->getOrderStatusProcessing())
                                        ->save();
                                }
                            } else {
                                if ($order->canCancel()) {
                                    $order->cancel();
                                }
                                if ($comment) {
                                    $order->addStatusHistoryComment($comment, Mage_Sales_Model_Order::STATE_CANCELED)->save();
                                }
                            }
                            $order->save();
                        }
                    } catch (Exception $e) {
                        Mage::log('Не вдалося змінити замовлення: ' . $e->getMessage(), null, 'rozetkapay.log');
                    }
                }
            }
            $this->_redirect('checkout/onepage/success');
            return;
        } catch (Mage_Core_Exception $e) {
            $this->checkoutSession()->addError($e->getMessage());
        } catch(Exception $e) {
            $this->checkoutSession()->addError($e->getMessage());
        }
        $this->_redirect('checkout/cart');
    }

}