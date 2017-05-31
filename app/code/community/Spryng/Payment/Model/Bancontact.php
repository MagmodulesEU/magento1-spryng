<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See
 * COPYING.txt for license details.
 */
class Spryng_Payment_Model_Bancontact extends Spryng_Payment_Model_Spryng
{

    protected $_code = 'spryng_bancontact';
    protected $_formBlockType = 'spryng/payment_bancontact_form';
    protected $_infoBlockType = 'spryng/payment_bancontact_info';

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
            'payment_product'            => 'bancontact',
            'dynamic_descriptor'         => $this->spryngHelper->getDynamicDescriptor($incrementId, $storeId),
            'customer_ip'                => $order->getRemoteIp(),
            'user_agent'                 => $this->spryngHelper->getUserAgent(),
            'merchant_reference'         => $this->spryngHelper->getMerchantReference($storeId),
            'webhook_transaction_update' => $this->spryngHelper->getWebhookUrl(),
            'details'                    => array(
                'redirect_url' => $this->spryngHelper->getReturnUrl()
            )
        );

        $this->spryngHelper->addTolog('request', $paymentData);

        $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
        $transaction = $spryngApi->Bancontact->initiate($paymentData);
        $approvalUrl = $transaction->details->approval_url;

        $message = $this->spryngHelper->__('Customer redirected to Spryng, url: %1', $approvalUrl);
        $status = $this->spryngHelper->getStatusPending($storeId);
        $order->addStatusToHistory($status, $message, false);
        $order->setSpryngTransactionId($transaction->_id);
        $order->save();

        return array('success' => true, 'approval_url' => $approvalUrl);
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