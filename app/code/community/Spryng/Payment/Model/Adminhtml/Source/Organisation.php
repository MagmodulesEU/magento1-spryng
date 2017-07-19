<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Model_Adminhtml_Source_Organisation
{

    /**
     * @return mixed
     */
    public function toOptionArray()
    {
        $storeId = (int)Mage::app()->getRequest()->getParam('store', 0);
        $websiteId = (int)Mage::app()->getRequest()->getParam('website', 0);
        $organisations = Mage::getModel('spryng/spryng')->getOrganisations($storeId, $websiteId);
        return $organisations;
    }

}

