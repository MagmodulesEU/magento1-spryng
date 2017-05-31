<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Model_Ideal extends Spryng_Payment_Model_Spryng
{

    protected $_code = 'spryng_ideal';
    protected $_formBlockType = 'spryng/payment_ideal_form';
    protected $_infoBlockType = 'spryng/payment_ideal_info';

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     */
    public function startTransaction($order)
    {
        $issuer = null;
        $storeId = $order->getStoreId();
        $incrementId = $order->getIncrementId();
        $apiKey = $this->spryngHelper->getApiKey($storeId);
        $accountId = $this->spryngHelper->getAccount($this->_code, $storeId);
        $additionalData = $order->getPayment()->getAdditionalInformation();
        if (isset($additionalData['issuer_id'])) {
            $issuer = $additionalData['issuer_id'];
        }

        $paymentData = array(
            'account'                    => $accountId,
            'amount'                     => ($order->getBaseGrandTotal() * 100),
            'customer_ip'                => $order->getRemoteIp(),
            'dynamic_descriptor'         => $this->spryngHelper->getDynamicDescriptor($incrementId, $storeId),
            'user_agent'                 => $this->spryngHelper->getUserAgent(),
            'merchant_reference'         => $this->spryngHelper->getMerchantReference($storeId),
            'webhook_transaction_update' => $this->spryngHelper->getWebhookUrl(),
            'details'                    => array(
                'issuer'       => $issuer,
                'redirect_url' => $this->spryngHelper->getReturnUrl()
            )
        );

        $this->spryngHelper->addTolog('request', $paymentData);
        $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
        $transaction = $spryngApi->iDeal->initiate($paymentData);
        $approvalUrl = $transaction->details->approval_url;

        $message = $this->spryngHelper->__('Customer redirected to Spryng, url: %s', $approvalUrl);
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

        if ($data->getData('issuer_id')) {
            $this->getInfoInstance()->setAdditionalInformation('issuer_id', $data->getData('issuer_id'));
        }
    }

}