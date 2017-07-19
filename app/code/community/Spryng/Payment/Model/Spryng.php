<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Model_Spryng extends Mage_Payment_Model_Method_Abstract
{

    /**
     * @var Spryng_Payment_Helper_Data
     */
    public $spryngHelper;
    public $additionalData;

    protected $_code = 'spryng_general';
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canUseCheckout = true;
    protected $_canUseInternal = false;
    protected $_canUseForMultishipping = false;
    protected $_canRefund = false;
    protected $_canRefundInvoicePartial = true;
    protected $_canCapture = true;

    /**
     * Spryng_Payment_Model_Spryng constructor.
     */
    public function __construct()
    {
        parent::_construct();
        $this->spryngHelper = Mage::helper('spryng');
    }

    /**
     * @param null $quote
     *
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (!$this->spryngHelper->isAvailable($quote->getStoreId())) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string                 $type
     *
     * @return array
     */
    public function processTransaction($order, $type = 'webhook')
    {
        $msg = '';
        $storeId = $order->getStoreId();
        $orderId = $order->getEntityId();

        $transactionId = $order->getSpryngTransactionId();
        if (empty($transactionId)) {
            $msg = array('error' => true, 'msg' => $this->spryngHelper->__('Transaction ID not found'));
            $this->spryngHelper->addTolog('error', $msg);
            return $msg;
        }

        $apiKey = $this->spryngHelper->getApiKey($storeId);
        if (empty($apiKey)) {
            $msg = array('error' => true, 'msg' => $this->spryngHelper->__('Api key not found'));
            $this->spryngHelper->addTolog('error', $msg);

            return $msg;
        }

        $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
        $transaction = $spryngApi->transaction->getTransactionById($transactionId);
        $this->spryngHelper->addTolog($type, $transaction);
        $statusPending = $this->spryngHelper->getStatusPending($storeId);

        switch ($transaction->status) {
            case 'SETTLEMENT_REQUESTED':
            case 'SETTLEMENT_COMPLETED':
                $payment = $order->getPayment();

                if (!$payment->getIsTransactionClosed() && $type == 'webhook') {
                    $payment->setTransactionId($transactionId);
                    $order->setTotalPaid($order->getGrandTotal())->save();

                    if (!$order->getEmailSent()) {
                        $order->setEmailSent(true);
                        $order->sendNewOrderEmail();
                        $order->save();
                    }

                    $invoice = $this->invoiceOrder($order);
                    $sendInvoice = $this->spryngHelper->sendInvoice($storeId);

                    if ($invoice && $sendInvoice && !$invoice->getEmailSent()) {
                        $invoice->setEmailSent(true);
                        $invoice->sendEmail();
                        $invoice->save();
                    }
                }

                $msg = array(
                    'success'  => true,
                    'status'   => $transaction->status,
                    'order_id' => $orderId,
                    'type'     => $type
                );

                break;

            case 'INITIATED':
                if ($type == 'webhook') {
                    $message = $this->spryngHelper->__(
                        'Transaction with ID %s has started. Your iDEAL approval URL is %s. Your order with ID %s will 
                        be updated automatically when you have paid.',
                        $transactionId,
                        $transaction->details->approval_url,
                        $order->getIncrementId()
                    );

                    if ($statusPending != $order->getStatus()) {
                        $statusPending = $order->getStatus();
                    }

                    $order->addStatusToHistory($statusPending, $message, false)->save();
                }

                $msg = array(
                    'success'  => true,
                    'status'   => $transaction->status,
                    'order_id' => $orderId,
                    'type'     => $type
                );

                break;

            case 'SETTLEMENT_PROCESSED':
                if ($type == 'webhook') {
                    $message = $this->spryngHelper->__(
                        'Transaction with ID %s is processed. Your order with ID %s should be updated automatically 
                        when the status on the payment is updated.',
                        $transactionId,
                        $order->getIncrementId()
                    );

                    if ($statusPending != $order->getStatus()) {
                        $statusPending = $order->getStatus();
                    }

                    $order->addStatusToHistory($statusPending, $message, false)->save();

                    if (!$order->getEmailSent()) {
                        $order->setEmailSent(true);
                        $order->sendNewOrderEmail();
                        $order->save();
                    }
                }

                $msg = array(
                    'success'  => true,
                    'status'   => $transaction->status,
                    'order_id' => $orderId,
                    'type'     => $type
                );

                break;

            case 'FAILED':
            case 'AUTHORIZATION_VOIDED':
                if ($type == 'webhook') {
                    $this->cancelOrder($order);
                }

                $msg = array(
                    'success'  => false,
                    'status'   => 'cancel',
                    'order_id' => $orderId,
                    'type'     => $type,
                    'msg'      => $this->spryngHelper->__('Payment was cancelled, please try again')
                );
                break;

            default:
                if ($type == 'webhook') {
                    $message = $this->spryngHelper->__(
                        'The status of your order with ID %s is %s. The order should be updated automatically 
                        when the status changes',
                        $transactionId,
                        $transaction->status
                    );

                    if ($statusPending != $order->getStatus()) {
                        $statusPending = $order->getStatus();
                    }

                    $order->addStatusToHistory($statusPending, $message, false)->save();
                }

                $msg = array(
                    'success'  => true,
                    'status'   => $transaction->status,
                    'order_id' => $orderId,
                    'type'     => $type
                );
                break;
        }

        $this->spryngHelper->addTolog($type, $msg);

        return $msg;
    }

    /**
     * @param $apiKey
     * @param $storeId
     *
     * @return \SpryngPaymentsApiPhp\Client|string
     */
    public function loadSpryngApi($apiKey, $storeId)
    {
        if (!$path = $this->spryngHelper->getAutoloadPath()) {
           $this->spryngHelper->addTolog('error', 'Spryng API not installed!');
           return '';
        }

        try {
            require_once($path);
            $spryngApi = new \SpryngPaymentsApiPhp\Client($apiKey, $this->spryngHelper->isSandbox($storeId));
        } catch (\Exception $e) {
            $this->spryngHelper->addTolog('error', 'Function: loadSpryngApi: ' . $e->getMessage());
            return '';
        }

        return $spryngApi;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return mixed
     */
    public function invoiceOrder($order)
    {
        if ($order->canInvoice()) {
            $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $invoice->pay()->save();
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
            $transactionSave->save();
            return $invoice;
        }
    }

    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool
     */
    public function cancelOrder($order)
    {
        if ($order->getId() && $order->getState() != Mage_Sales_Model_Order::STATE_CANCELED) {
            $comment = $this->spryngHelper->__("The order was canceled");
            $this->spryngHelper->addTolog('info', $order->getIncrementId() . ' ' . $comment);
            $order->registerCancellation($comment)->save();
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl(
            'spryng/checkout/redirect',
            array(
                '_secure' => true,
            )
        );
    }

    /**
     * @param $storeId
     * @param $websiteId
     *
     * @return array
     */
    public function getAccounts($storeId, $websiteId)
    {
        $accounts = array();

        $apiKey = $this->spryngHelper->getApiKey($storeId, $websiteId);
        if (empty($apiKey)) {
            return array('-1' => $this->spryngHelper->__('Please provide a valid API Key.'));
        }

        $path = $this->spryngHelper->getAutoloadPath();
        if (empty($path)) {
            return array('-1' => $this->spryngHelper->__('Spryng API not installed correctly.'));
        }

        try {
            $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
            $apiAccounts = $spryngApi->account->getAll();
        } catch (\Exception $e) {
            $this->spryngHelper->addTolog('error', 'Function: getAccounts: ' . $e->getMessage());
            return array('-1' => $this->spryngHelper->__('The API Key you provided is invalid.'));
        }

        foreach ($apiAccounts as $apiAccount) {
            $accounts[$apiAccount->_id] = $apiAccount->name;
        }

        return $accounts;
    }

    /**
     * @param $storeId
     * @param $websiteId
     *
     * @return array
     */
    public function getOrganisations($storeId, $websiteId)
    {
        $organisations = array();

        $apiKey = $this->spryngHelper->getApiKey($storeId, $websiteId);
        if (empty($apiKey)) {
            return array('-1' => $this->spryngHelper->__('Please provide a valid API Key.'));
        }

        $path = $this->spryngHelper->getAutoloadPath();
        if (empty($path)) {
            return array('-1' => $this->spryngHelper->__('Spryng API not installed correctly.'));
        }

        try {
            $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
            $apiOrganisations = $spryngApi->organisation->getAll();
        } catch (\Exception $e) {
            $this->spryngHelper->addTolog('error', 'Function: getAccounts: ' . $e->getMessage());
            return array('-1' => $this->spryngHelper->__('The API Key you provided is invalid.'));
        }

        foreach ($apiOrganisations as $apiOrganisation) {
            $organisations[$apiOrganisation->_id] = $apiOrganisation->name;
        }

        return $organisations;
    }

    /**
     * @param Mage_Sales_Model_Order       $order
     * @param \SpryngPaymentsApiPhp\Client $spryngApi
     * @param                              $prefix
     * @param string                       $dateOfBirth
     *
     * @return string
     */
    public function getSpryngCustomerId($order, $spryngApi, $prefix, $dateOfBirth = '')
    {
        $customerId = $order->getCustomerId();

        if ($customerId) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $spryngCustomerId = $customer->getSpryngCustomerId();
            if (empty($spryngCustomerId)) {
                $spryngCustomer = $this->createNewSpryngCustomer($order, $spryngApi, $prefix, $dateOfBirth);
            } else {
                $spryngCustomer = $this->updateCustomer(
                    $order,
                    $spryngApi,
                    $spryngCustomerId,
                    $prefix,
                    $dateOfBirth
                );
            }
        } else {
            $spryngCustomer = $this->createNewSpryngCustomer($order, $spryngApi, $prefix, $dateOfBirth);
        }

        return $spryngCustomer;
    }

    /**
     * @param Mage_Sales_Model_Order       $order
     * @param \SpryngPaymentsApiPhp\Client $spryngApi
     * @param                              $prefix
     * @param                              $dateOfBirth
     *
     * @return string
     */
    public function createNewSpryngCustomer($order, $spryngApi, $prefix, $dateOfBirth)
    {
        $spryngCustomer = '';

        if (!$customerId = $order->getCustomerId()) {
            $customerId = $order->getIncrementId();
        }

        $billing = $order->getBillingAddress();

        $postCode = $billing->getPostcode();
        if (strlen($postCode) == 6) {
            $postCode = wordwrap($postCode, 4, ' ', true);
        }

        $phoneNumber = $this->spryngHelper->getFormattedPhoneNumber($billing->getTelephone(), $billing->getCountryId());

        $customerData = array(
            'account'        => $customerId,
            'title'          => $prefix,
            'first_name'     => $billing->getFirstname(),
            'last_name'      => $billing->getLastname(),
            'email_address'  => $order->getCustomerEmail(),
            'country_code'   => $billing->getCountryId(),
            'city'           => $billing->getCity(),
            'street_address' => $billing->getStreetFull(),
            'postal_code'    => $postCode,
            'phone_number'   => $phoneNumber,
            'gender'         => ($prefix == 'ms') ? 'female' : 'male',
        );

        if (!empty($dateOfBirth)) {
            $customerData['date_of_birth'] = $dateOfBirth;
        }

        $this->spryngHelper->addTolog('request', $customerData);

        try {
            $spryngCustomer = $spryngApi->customer->create($customerData);
            if ($order->getCustomerId()) {
                Mage::getModel('customer/customer')->load($order->getCustomerId())
                    ->setSpryngCustomerId($spryngCustomer->_id)
                    ->save();
            }
        } catch (\Exception $e) {
            $msg = $this->spryngHelper->__('Error creating customer data, %s', $e->getMessage());
            $this->spryngHelper->addTolog('error', 'Function: createNewSpryngCustomer: ' . $msg);
        }

        return $spryngCustomer;
    }

    /**
     * @param Mage_Sales_Model_Order       $order
     * @param \SpryngPaymentsApiPhp\Client $spryngApi
     * @param                              $spryngCustomerId
     * @param                              $prefix
     * @param                              $dateOfBirth
     *
     * @return string
     */
    public function updateCustomer($order, $spryngApi, $spryngCustomerId, $prefix, $dateOfBirth)
    {
        $spryngCustomer = '';
        $customerId = $order->getCustomerId();
        $billing = $order->getBillingAddress();

        $postCode = $billing->getPostcode();
        if (strlen($postCode) == 6) {
            $postCode = wordwrap($postCode, 4, ' ', true);
        }

        $phoneNumber = $this->spryngHelper->getFormattedPhoneNumber($billing->getTelephone(), $billing->getCountryId());

        $customerData = array(
            'account'        => $customerId,
            'title'          => $prefix,
            'first_name'     => $billing->getFirstname(),
            'last_name'      => $billing->getLastname(),
            'email_address'  => $order->getCustomerEmail(),
            'country_code'   => $billing->getCountryId(),
            'city'           => $billing->getCity(),
            'street_address' => $billing->getStreetFull(),
            'postal_code'    => $postCode,
            'phone_number'   => $phoneNumber,
            'gender'         => ($prefix == 'ms') ? 'female' : 'male',
        );

        if (!empty($dateOfBirth)) {
            $customerData['date_of_birth'] = $dateOfBirth;
        }

        $this->spryngHelper->addTolog('request', $customerData);

        try {
            $spryngCustomer = $spryngApi->customer->update($spryngCustomerId, $customerData);
        } catch (\Exception $e) {
            $msg = $this->spryngHelper->__('Error updating customer data, %s', $e->getMessage());
            $this->spryngHelper->addTolog('error', 'Function: updateCustomer: ' . $msg);
        }

        return $spryngCustomer;
    }

    /**
     * @param $code
     * @param $storeId
     *
     * @return array
     */
    public function getPaymentClasses($code, $storeId)
    {
        $data = array();

        try {
            $apiKey = $this->spryngHelper->getApiKey($storeId);
            $account = $this->spryngHelper->getAccount($code, $storeId);
            $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
            $pclasses = $spryngApi->Klarna->getPClasses($account);
            foreach ($pclasses as $pclass) {
                $data[] = array(
                    'id'   => $pclass->_id,
                    'name' => $pclass->description . ' - (' . ($pclass->interest_rate / 100) . '% interest)'
                );
            }
        } catch (\Exception $e) {
            $this->spryngHelper->addTolog('error', 'Function: getPClasses: ' . $e->getMessage());
        }

        return $data;
    }

    /**
     * @param $refundId
     * @param $storeId
     * @param $type
     *
     * @return string
     */
    public function processRefund($refundId, $storeId, $type)
    {
        $apiKey = $this->spryngHelper->getApiKey($storeId);

        if (empty($apiKey)) {
            $msg = array('error' => true, 'msg' => __('Api key not found'));
            $this->spryngHelper->addTolog('error', $msg);
            return '';
        }

        $spryngApi = $this->loadSpryngApi($apiKey, $storeId);
        $refund = $spryngApi->refund->getRefundById($refundId);
        $this->spryngHelper->addTolog($type, $refund);

        if ($refund->status == 'PROCESSED' && isset($refund->transaction->_id)) {
            $order = $this->getOrderByTransactionId($refund->transaction->_id);
            $orderItem = $order->getAllVisibleItems();

            $service = Mage::getModel('sales/service_order', $order);
            $qtys = array();
            foreach ($orderItem as $item) {
                $qtys[$item->getId()] = $item->getQtyOrdered();
            }

            $data = array(
                'qtys' => $qtys
            );

            $creditMemo = $service->prepareCreditmemo($data)->register()->save();

            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($creditMemo)
                ->addObject($creditMemo->getOrder());
            if ($creditMemo->getInvoice()) {
                $transactionSave->addObject($creditMemo->getInvoice());
            }

            $transactionSave->save();
        }
    }

    /**
     * @param $transactionId
     *
     * @return mixed
     */
    public function getOrderByTransactionId($transactionId)
    {
        $order = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('spryng_transaction_id', $transactionId)
            ->getFirstItem();

        if ($order) {
            return $order;
        }
    }

}