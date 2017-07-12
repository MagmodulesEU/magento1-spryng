<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Model_Creditcard extends Spryng_Payment_Model_Spryng
{

    protected $_code = 'spryng_creditcard';
    protected $_formBlockType = 'spryng/payment_creditcard_form';
    protected $_infoBlockType = 'spryng/payment_creditcard_info';
    protected $_canRefund = true;

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     */
    public function startTransaction($order)
    {
        $cardToken = null;
        $storeId = $order->getStoreId();
        $incrementId = $order->getIncrementId();
        $apiKey = $this->spryngHelper->getApiKey($storeId);
        $accountId = $this->spryngHelper->getAccount($this->_code, $storeId);
        $additionalData = $order->getPayment()->getAdditionalInformation();
        if (isset($additionalData['card_token'])) {
            $cardToken = $additionalData['card_token'];
        }

        $paymentData = array(
            'account'                    => $accountId,
            'amount'                     => ($order->getBaseGrandTotal() * 100),
            'card'                       => $cardToken,
            'dynamic_descriptor'         => $this->spryngHelper->getDynamicDescriptor($incrementId, $storeId),
            'payment_product'            => 'card',
            'customer_ip'                => $order->getRemoteIp(),
            'user_agent'                 => $this->spryngHelper->getUserAgent(),
            'capture'                    => true,
            'merchant_reference'         => $this->spryngHelper->getMerchantReference($storeId),
            'webhook_transaction_update' => $this->spryngHelper->getWebhookUrl(),
        );

        $this->spryngHelper->addTolog('request', $paymentData);

        $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
        $transaction = $spryngApi->transaction->create($paymentData);
        $this->spryngHelper->addTolog('request', $transaction);
        $order->setSpryngTransactionId($transaction->_id)->save();

        $approvalUrl = $this->spryngHelper->getReturnUrl($order->getId());
        return array('success' => true, 'approval_url' => $approvalUrl);
    }

    /**
     * @param Varien_Object $payment
     * @param float         $amount
     *
     * @return $this
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $order = $payment->getOrder();
        $storeId = $order->getStoreId();
        $transactionId = $order->getSpryngTransactionId();
        if (empty($transactionId)) {
            $msg = array('error' => true, 'msg' => $this->spryngHelper->__('Transaction ID not found'));
            $this->spryngHelper->addTolog('error', $msg);
            return $this;
        }

        $apiKey = $this->spryngHelper->getApiKey($storeId);
        if (empty($apiKey)) {
            $msg = array('error' => true, 'msg' => $this->spryngHelper->__('Api key not found'));
            $this->spryngHelper->addTolog('error', $msg);
            return $this;
        }

        $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
        try {
            $amount = $amount * 100;
            $spryngApi->transaction->refund($transactionId, $amount, '');
        } catch (\Exception $e) {
            $this->spryngHelper->addTolog('error', $e->getMessage());
        }

        return $this;
    }

    /**
     * @param mixed $data
     *
     * @return Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }

        if ($data->getData('card_token')) {
            $this->getInfoInstance()->setAdditionalInformation('card_token', $data->getData('card_token'));
        }
    }
}