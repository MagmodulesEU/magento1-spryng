<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_CheckoutController extends Mage_Core_Controller_Front_Action
{

    /**
     * @var Spryng_Payment_Helper_Data
     */
    public $spryngHelper;

    /**
     * @var Spryng_Payment_Model_Spryng
     */
    public $spryngModel;

    /**
     * Redirect Action
     */
    public function redirectAction()
    {
        $this->spryngHelper = Mage::helper('spryng');

        try {
            $order = $this->spryngHelper->getOrderFromSession();
            $methodInstance = $order->getPayment()->getMethodInstance();
            $transaction = $methodInstance->startTransaction($order);
            if (!empty($transaction['error_msg'])) {
                $msg = $transaction['error_msg'];
                Mage::getSingleton('core/session')->addError($msg);
                $this->spryngHelper->restoreCart();
                $this->_redirect('checkout/cart');
            }

            if (!empty($transaction['approval_url'])) {
                $redirectUrl = $transaction['approval_url'];
                $this->_redirectUrl($redirectUrl);
            }
        } catch (\Exception $e) {
            $msg = $this->spryngHelper->__('An error occured while processing your payment request, please try again later');
            Mage::getSingleton('core/session')->addError($msg);
            $this->spryngHelper->addTolog('error', $e->getMessage());
            $this->spryngHelper->restoreCart();
            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Success/Return Action
     */
    public function successAction()
    {
        $this->spryngHelper = Mage::helper('spryng');

        try {
            $order = $this->spryngHelper->getOrderFromSession();
            $methodInstance = $order->getPayment()->getMethodInstance();
            $status = $methodInstance->processTransaction($order, 'success');
        } catch (\Exception $e) {
            $this->spryngHelper->addTolog('error', $e);
            $msg = $this->spryngHelper->__('There was an error checking the transaction status.');
            Mage::getSingleton('core/session')->addError($msg);
            $this->_redirect('checkout/cart');
        }

        if (!empty($status['success'])) {
            $this->_redirect('checkout/onepage/success?utm_nooverride=1');
        } else {
            $this->spryngHelper->restoreCart();
            if (!empty($status['msg'])) {
                Mage::getSingleton('core/session')->addError($status['msg']);
            } else {
                $msg = $this->spryngHelper->__('Something went wrong.');
                Mage::getSingleton('core/session')->addError($msg);
            }

            $this->_redirect('checkout/cart');
        }
    }

    /**
     * Webhook Action
     */
    public function webhookAction()
    {
        $this->spryngHelper = Mage::helper('spryng');
        $this->spryngModel = Mage::getModel('spryng/spryng');

        $payload = file_get_contents('php://input');
        $this->spryngHelper->addTolog('webhook', $payload);
        $json = json_decode($payload);

        if ($json && $json->type == 'transaction') {
            $order = $this->spryngModel->getOrderByTransactionId($json->_id);
            if (!$order->getEntityId()) {
                $msg = array('error' => true, 'msg' => __('No order found for id: %1', $json->_id));
                $this->spryngHelper->addTolog('error', $msg);
                return;
            }

            $this->spryngModel->processTransaction($order, 'webhook');
        }

        if ($json && $json->type == 'refund') {
            $storeId = $this->getRequest()->getParams('store_id');
            $this->spryngModel->processRefund($json->_id, $storeId, 'webhook');
        }
    }

}