<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Block_Payment_Bancontact_Form extends Mage_Payment_Block_Form
{

    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('spryng/form/bancontact.phtml');
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('payment/config');
    }

    /**
     * Retrieve credit card expire months
     *
     * @return array
     */
    public function getCcMonths()
    {
        $months = $this->getData('cc_months');
        if ($months === null) {
            $months[0] = $this->__('Month');
            $months = array_merge($months, $this->_getConfig()->getMonths());
            $this->setData('cc_months', $months);
        }

        return $months;
    }

    /**
     * Retrieve credit card expire years
     *
     * @return array
     */
    public function getCcYears()
    {
        $years = $this->getData('cc_years');
        if ($years === null) {
            $years = array();
            $years[0] = $this->__('Year');
            foreach ($this->_getConfig()->getYears() as $k => $v) {
                $years[substr($k, -2)] = $v;
            }

            $this->setData('cc_years', $years);
        }

        return $years;
    }
}
