<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Model_Sofort extends Spryng_Payment_Model_Spryng
{

    protected $_code = 'spryng_sofort';

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     */
    public function startTransaction($order)
    {
        $storeId = $order->getStoreId();
        $incrementId = $order->getIncrementId();
        $apiKey = $this->spryngHelper->getApiKey($storeId);
        $accountId = $this->spryngHelper->getAccount($this->_code, $storeId);
        $countryId = $order->getBillingAddress()->getCountryId();

        $paymentData = array(
            'account'                    => $accountId,
            'amount'                     => ($order->getBaseGrandTotal() * 100),
            'customer_ip'                => $order->getRemoteIp(),
            'dynamic_descriptor'         => $this->spryngHelper->getDynamicDescriptor($incrementId, $storeId),
            'user_agent'                 => $this->spryngHelper->getUserAgent(),
            'country_code'               => $countryId,
            'merchant_reference'         => $this->spryngHelper->getMerchantReference($storeId),
            'webhook_transaction_update' => $this->spryngHelper->getWebhookUrl(),
            'details'                    => array(
                'redirect_url' => $this->spryngHelper->getReturnUrl(),
                'project_id'   => $this->spryngHelper->getProjectId($this->_code, $storeId)
            )
        );

        $this->spryngHelper->addTolog('request', $paymentData);
        $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
        $transaction = $spryngApi->SOFORT->initiate($paymentData);
        $approvalUrl = $transaction->details->approval_url;

        $message = $this->spryngHelper->__('Customer redirected to Spryng, url: %s', $approvalUrl);
        $status = $this->spryngHelper->getStatusPending($storeId);
        $order->addStatusToHistory($status, $message, false);
        $order->setSpryngTransactionId($transaction->_id);
        $order->save();

        return array('success' => true, 'approval_url' => $approvalUrl);
    }

}