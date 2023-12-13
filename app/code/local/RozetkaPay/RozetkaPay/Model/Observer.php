<?php

class RozetkaPay_RozetkaPay_Model_Observer extends Mage_Core_Model_Abstract
{

    /**
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function addRozetkaPayButton($observer)
    {
        $container = $observer->getBlock();
        $rozetkaPayHelper = Mage::helper('rozetkapay_rozetkapay');
        if (null !== $container && $container->getType() == 'adminhtml/sales_order_view') {
            $order = $this->_getOrder();
            if ($order->getPayment()->getMethod() == 'rozetkapay_rozetkapay') {
                $message = $rozetkaPayHelper->__('Confirm');
                $container->addButton('rozetkapay_confirm', array(
                    'label' => "{$message}",
                    'onclick' => "confirmSetLocation('{$message}', '{$this->_getRozetkaPayActionUrl('confirm')}')",
                    'style' => 'background:green;',
                ));

                $message = $rozetkaPayHelper->__('Cancel');
                $container->addButton('rozetkapay_cancel', array(
                    'label' => "{$message}",
                    'onclick' => "confirmSetLocation('{$message}', {$this->_getRozetkaPayActionUrl('cancel')}')",
                    'style' => 'background:green;',
                ));

                $message = $rozetkaPayHelper->__('Refaund');
                $container->addButton('rozetkapay_refund', array(
                    'label' => "{$message}",
                    'onclick' => "confirmSetLocation('{$message}', '{$this->_getRozetkaPayActionUrl('refund')}')",
                    'style' => 'background:green;',
                ));

                $message = $rozetkaPayHelper->__('Іnformation');
                $container->addButton('rozetkapay_info', array(
                    'label' => "{$message}",
                    'onclick' => "confirmSetLocation('{$message}', '{$this->_getRozetkaPayActionUrl('info')}')",
                    'style' => 'background:green;',
                ));
            }
        }

        return $this;
    }

    protected function _getRozetkaPayActionUrl($action)
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/rozetkapay/', array('action' => $action, 'order_id' => $this->_getOrder()->getId()));
    }

    protected function _getOrder()
    {
        return Mage::registry('current_order');
    }

}