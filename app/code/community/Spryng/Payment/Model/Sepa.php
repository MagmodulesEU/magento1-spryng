<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Model_Sepa extends Spryng_Payment_Model_Spryng
{

    protected $_code = 'spryng_sepa';
    protected $_formBlockType = 'spryng/payment_sepa_form';
    protected $_infoBlockType = 'spryng/payment_sepa_info';

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     */
    public function startTransaction($order)
    {
        $prefix = null;
        $storeId = $order->getStoreId();
        $incrementId = $order->getIncrementId();
        $additionalData = $order->getPayment()->getAdditionalInformation();
        if (isset($additionalData['prefix'])) {
            $prefix = $additionalData['prefix'];
        }

        $apiKey = $this->spryngHelper->getApiKey($storeId);
        $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
        $accountId = $this->spryngHelper->getAccount($this->_code, $storeId);
        $customer = $this->getSpryngCustomerId($order, $spryngApi, $prefix);

        if (empty($customer)) {
            return array(
                'success'   => false,
                'error_msg' => $this->spryngHelper->__('Error creating SEPA customer data')
            );
        }

        $paymentData = array(
            'account'                    => $accountId,
            'customer'                   => $customer->_id,
            'amount'                     => ($order->getBaseGrandTotal() * 100),
            'customer_ip'                => $order->getRemoteIp(),
            'user_agent'                 => $this->spryngHelper->getUserAgent(),
            'dynamic_descriptor'         => $this->spryngHelper->getDynamicDescriptor($incrementId, $storeId),
            'merchant_reference'         => $this->spryngHelper->getMerchantReference($storeId),
            'webhook_transaction_update' => $this->spryngHelper->getWebhookUrl(),
            'details'                    => array(
                'redirect_url' => $this->spryngHelper->getReturnUrl()
            )
        );

        $this->spryngHelper->addTolog('request', $paymentData);
        $transaction = $spryngApi->Sepa->initiate($paymentData);

        if (isset($transaction->details->approval_url)) {
            $approvalUrl = $transaction->details->approval_url;
            $message = $this->spryngHelper->__('Customer redirected to Spryng, url: %1', $approvalUrl);
            $status = $this->spryngHelper->getStatusPending($storeId);
            $order->addStatusToHistory($status, $message, false);
            $order->setSpryngTransactionId($transaction->_id);
            $order->save();
        } else {
            $transactionId = $transaction->_id;
            $order->setSpryngTransactionId($transactionId)->save();
            $approvalUrl = $this->spryngHelper->getReturnUrl();
        }

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

        if ($data->getData('prefix')) {
            $this->getInfoInstance()->setAdditionalInformation('prefix', $data->getData('prefix'));
        }
    }

}