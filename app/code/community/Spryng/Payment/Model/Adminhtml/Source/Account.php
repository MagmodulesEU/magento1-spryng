<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See
 * COPYING.txt for license details.
 */
class Spryng_Payment_Model_Adminhtml_Source_Account
{

    /**
     * @return mixed
     */
    public function toOptionArray()
    {
        $storeId = (int)Mage::app()->getRequest()->getParam('store', 0);
        $websiteId = (int)Mage::app()->getRequest()->getParam('website', 0);
        $accounts = Mage::getModel('spryng/spryng')->getAccounts($storeId, $websiteId);
        return $accounts;
    }

}

