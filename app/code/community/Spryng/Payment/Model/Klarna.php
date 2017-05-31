<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Model_Klarna extends Spryng_Payment_Model_Spryng
{

    const FLAG_SHIPMENT_FEE = 8;
    const FLAG_INCL_VAT = 32;

    protected $_code = 'spryng_klarna';
    protected $_formBlockType = 'spryng/payment_klarna_form';
    protected $_infoBlockType = 'spryng/payment_klarna_info';

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return array
     */
    public function startTransaction($order)
    {
        $prefix = null;
        $pclass = null;
        $dateOfBirth = null;
        $storeId = $order->getStoreId();
        $incrementId = $order->getIncrementId();
        $additionalData = $order->getPayment()->getAdditionalInformation();

        if (isset($additionalData['prefix'])) {
            $prefix = $additionalData['prefix'];
        }

        if (isset($additionalData['pclass'])) {
            $pclass = $additionalData['pclass'];
        }

        if (isset($additionalData['dob'])) {
            $dateOfBirth = $additionalData['dob'];
        }

        $apiKey = $this->spryngHelper->getApiKey($storeId);
        $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
        $accountId = $this->spryngHelper->getAccount($this->_code, $storeId);
        $customer = $this->getSpryngCustomerId($order, $spryngApi, $prefix, $dateOfBirth);

        if (empty($customer)) {
            return array('success' => false, 'error_msg' => __('Error creating Klarna customer data'));
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
                'redirect_url' => $this->spryngHelper->getReturnUrl(),
                'pclass'       => $pclass,
                'goods_list'   => $this->generateOrderListForOrder($order)
            )
        );

        $this->spryngHelper->addTolog('request', $paymentData);
        $transaction = $spryngApi->Klarna->initiate($paymentData);
        $this->spryngHelper->addTolog('klarna', $transaction);
        $transactionId = $transaction->_id;
        $order->setSpryngTransactionId($transactionId)->save();

        $approvalUrl = $this->spryngHelper->getReturnUrl($order->getId());
        return array('success' => true, 'approval_url' => $approvalUrl);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return \SpryngPaymentsApiPhp\Object\GoodsList
     */
    public function generateOrderListForOrder($order)
    {
        $goods = new \SpryngPaymentsApiPhp\Object\GoodsList();

        foreach ($order->getAllVisibleItems() as $item) {
            $good = new \SpryngPaymentsApiPhp\Object\Good();
            $good->title = preg_replace("/[^a-zA-Z0-9]+/", "", $item->getName());
            $good->reference = preg_replace("/[^a-zA-Z0-9]+/", "", $item->getSku());
            $good->quantity = round($item->getQtyOrdered());
            $good->price = ($item->getPriceInclTax() * 100);

            if ($item->getOriginalPrice() > $item->getPrice()) {
                $discountRate = (100 - (($item->getPrice() / $item->getOriginalPrice()) * 100));
                $good->discount = (int)round($discountRate);
            } else {
                $good->discount = 0;
            }

            if ($item->getTaxPercent() > 0) {
                $good->flags = array(self::FLAG_INCL_VAT);
                $good->vat = round($item->getTaxPercent());
            } else {
                $good->flags = array();
                $good->vat = 0;
            }

            $goods->add($good);
        }

        if ($order->getBaseShippingAmount() > 0) {
            $good = new \SpryngPaymentsApiPhp\Object\Good();
            $good->title = 'Shipping';
            $good->reference = 'Shipping';
            $good->quantity = 1;
            $good->price = ($order->getShippingInclTax() * 100);
            $good->discount = '0';

            if ($order->getShippingTaxAmount() > 0) {
                $good->flags = array(self::FLAG_SHIPMENT_FEE, self::FLAG_INCL_VAT);
                $good->vat = round(($order->getShippingTaxAmount() / $order->getShippingAmount()) * 100);
            } else {
                $good->flags = array(self::FLAG_SHIPMENT_FEE);
                $good->vat = 0;
            }

            $goods->add($good);
        }

        return $goods;
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

        if ($data->getData('pclass')) {
            $this->getInfoInstance()->setAdditionalInformation('pclass', $data->getData('pclass'));
        }

        if ($data->getData('dob')) {
            $this->getInfoInstance()->setAdditionalInformation('dob', $data->getData('dob'));
        }
    }
}