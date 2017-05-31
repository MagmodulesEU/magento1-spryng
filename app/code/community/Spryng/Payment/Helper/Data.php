<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Helper_Data extends Mage_Core_Helper_Abstract
{

    const API_ENDPOINT = 'https://api.spryngpayments.com/v1/';
    const API_ENDPOINT_SANDBOX = 'https://sandbox.spryngpayments.com/v1/';
    const XML_PATH_MODULE_ACTIVE = 'payment/spryng_general/enabled';
    const XML_PATH_API_MODUS = 'payment/spryng_general/type';
    const XML_PATH_LIVE_APIKEY = 'payment/spryng_general/apikey_live';
    const XML_PATH_SANDBOX_APIKEY = 'payment/spryng_general/apikey_sandbox';
    const XML_PATH_DYNAMIC_DESCRIPTOR = 'payment/spryng_general/dynamic_descriptor';
    const XML_PATH_DEBUG = 'payment/spryng_general/debug';
    const XML_PATH_STATUS_PROCESSING = 'payment/spryng_general/order_status_processing';
    const XML_PATH_STATUS_PENDING = 'payment/spryng_general/order_status_pending';
    const XML_PATH_INVOICE_NOTIFY = 'payment/spryng_general/invoice_notify';
    const XML_PATH_MERCHANT_REFERENCE = 'payment/spryng_general/merchant_reference';

    /**
     * @param $storeId
     *
     * @return bool
     */
    public function isAvailable($storeId)
    {
        $active = $this->getStoreConfig(self::XML_PATH_MODULE_ACTIVE, $storeId);
        if (!$active) {
            return false;
        }

        return true;
    }

    /**
     * @param     $storeId
     * @param int $websiteId
     *
     * @return mixed
     */
    public function getApiKey($storeId, $websiteId = 0)
    {
        if ($this->isSandbox($storeId, $websiteId)) {
            return $this->getStoreConfig(self::XML_PATH_SANDBOX_APIKEY, $storeId, $websiteId);
        } else {
            return $this->getStoreConfig(self::XML_PATH_LIVE_APIKEY, $storeId, $websiteId);
        }
    }

    /**
     * @param     $storeId
     * @param int $websiteId
     *
     * @return bool
     */
    public function isSandbox($storeId, $websiteId = 0)
    {
        $modus = $this->getStoreConfig(self::XML_PATH_API_MODUS, $storeId, $websiteId);
        if ($modus == 'sandbox') {
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getExtensionVersion()
    {
        return Mage::getConfig()->getNode()->modules->Spryng_Payment->version;
    }

    /**
     * @param $type
     * @param $data
     */
    public function addTolog($type, $data)
    {
        $debug = $this->getStoreConfig(self::XML_PATH_DEBUG);
        if ($debug) {
            if (is_array($data)) {
                $log = $type . ': ' . json_encode($data, true);
            } elseif (is_object($data)) {
                $log = $type . ': ' . json_encode($data, true);
            } else {
                $log = $type . ': ' . $data;
            }

            Mage::log($log, null, 'spryng.log');
        }
    }

    /**
     * @return array
     */
    public function getIdealIssuers()
    {
        return array(
            array('id' => 'ABNANL2A', 'name' => 'ABN Ambro'),
            array('id' => 'ASNBNL21', 'name' => 'ASN Bank'),
            array('id' => 'BUNQNL2A', 'name' => 'Bunq'),
            array('id' => 'FVLBNL22', 'name' => 'Van Lanschot Bankiers'),
            array('id' => 'INGBNL2A', 'name' => 'ING'),
            array('id' => 'KNABNL2H', 'name' => 'Knab'),
            array('id' => 'RABONL2U', 'name' => 'Rabobank'),
            array('id' => 'RBRBNL21', 'name' => 'Regiobank'),
            array('id' => 'SNSNML2A', 'name' => 'SNS Bank'),
            array('id' => 'TRIONL2U', 'name' => 'Triodos Bank')
        );
    }

    public function getCustomerPrefixes()
    {
        return array(
            array('id' => 'mr', 'name' => 'Mr.'),
            array('id' => 'ms', 'name' => 'Ms.')
        );
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    public function getOrderFromSession()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if (!empty($orderId)) {
            $order = Mage::getModel('sales/order')->load($orderId);
            return $order;
        }
    }

    /**
     * Restore Cart
     */
    public function restoreCart()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if (!empty($orderId)) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $quoteId = $order->getQuoteId();
            $quote = Mage::getModel('sales/quote')->load($quoteId)->setIsActive(true)->save();
            Mage::getSingleton('checkout/session')->replaceQuote($quote);
        }
    }

    /**
     * @param     $_code
     * @param int $storeId
     *
     * @return mixed
     */
    public function getAccount($_code, $storeId = 0)
    {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getStoreId();
        }

        $path = 'payment/' . $_code . '/account';
        return $this->getStoreConfig($path, $storeId);
    }

    /**
     * @param $incrementId
     * @param $storeId
     *
     * @return mixed
     */
    public function getDynamicDescriptor($incrementId, $storeId)
    {
        $descriptor = $this->getStoreConfig(self::XML_PATH_DYNAMIC_DESCRIPTOR, $storeId);
        return str_replace('%id%', $incrementId, $descriptor);
    }

    /**
     * @param $storeId
     *
     * @return mixed|string
     */
    public function getMerchantReference($storeId)
    {
        $merchantReference = $this->getStoreConfig(self::XML_PATH_MERCHANT_REFERENCE, $storeId);
        if (empty($merchantReference)) {
            $baseUrl = parse_url(Mage::app()->getStore($storeId)->getBaseUrl());
            return 'Magento Plugin installed at ' . $baseUrl['host'];
        }

        return $this->getStoreConfig(self::XML_PATH_MERCHANT_REFERENCE, $storeId);
    }

    /**
     * @return mixed
     */
    public function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * @return mixed|string
     */
    public function getReturnUrl()
    {
        $url = Mage::getUrl('spryng/checkout/success/', array('_secure' => true)) . '?&utm_nooverride=1';
        return strpos($url, 'https') !== false ? $url : str_replace('http', 'https', $url);
    }

    /**
     * @return mixed|string
     */
    public function getWebhookUrl()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $url = Mage::getUrl('spryng/checkout/webhook/', array('_secure' => true,  '_query' => array('store_id' => $storeId)));
        return strpos($url, 'https') !== false ? $url : str_replace('http', 'https', $url);
    }

    /**
     * @param     $_code
     * @param int $storeId
     *
     * @return mixed
     */
    public function getProjectId($_code, $storeId = 0)
    {
        $path = 'payment/' . $_code . '/project_id';
        return $this->getStoreConfig($path, $storeId);
    }

    /**
     * @param int $storeId
     *
     * @return mixed
     */
    public function getStatusProcessing($storeId = 0)
    {
        return $this->getStoreConfig(self::XML_PATH_STATUS_PROCESSING, $storeId);
    }

    /**
     * @param int $storeId
     *
     * @return mixed
     */
    public function getStatusPending($storeId = 0)
    {
        return $this->getStoreConfig(self::XML_PATH_STATUS_PENDING, $storeId);
    }

    /**
     * @param int $storeId
     *
     * @return int
     */
    public function sendInvoice($storeId = 0)
    {
        return (int)$this->getStoreConfig(self::XML_PATH_INVOICE_NOTIFY, $storeId);
    }

    /**
     * @param $telephone
     * @param $countryId
     *
     * @return string
     */
    public function getFormattedPhoneNumber($telephone, $countryId)
    {
        $libphonenumber = \libphonenumber\PhoneNumberUtil::getInstance();
        $phoneNumber = $libphonenumber->parse($telephone, $countryId);
        $formattedPhoneNumber = $libphonenumber->format($phoneNumber, \libphonenumber\PhoneNumberFormat::E164);
        return $formattedPhoneNumber;
    }

    /**
     * @param      $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getPaymentClasses($code, $storeId = null)
    {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getStoreId();
        }

        return Mage::getModel('spryng/spryng')->getPaymentClasses($code, $storeId);
    }

    /**
     * @param      $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getOrganisation($code, $storeId = null)
    {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getStoreId();
        }

        $path = 'payment/' . $code . '/organisation';
        return $this->getStoreConfig($path, $storeId);
    }

    /**
     * @param      $type
     * @param null $storeId
     *
     * @return string
     */
    public function getApiEndpoint($type, $storeId = null)
    {
        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getStoreId();
        }

        if ($this->isSandbox($storeId)) {
            return self::API_ENDPOINT_SANDBOX . $type;
        } else {
            return self::API_ENDPOINT . $type;
        }
    }

    /**
     * @param     $path
     * @param int $storeId
     * @param int $websiteId
     *
     * @return mixed
     */
    public function getStoreConfig($path, $storeId = 0, $websiteId = 0)
    {
        if ($websiteId > 0) {
            return Mage::app()->getWebsite($websiteId)->getConfig($path);
        } else {
            return Mage::getStoreConfig($path, $storeId);
        }
    }
}