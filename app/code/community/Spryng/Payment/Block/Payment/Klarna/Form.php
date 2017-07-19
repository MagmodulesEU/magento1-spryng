<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Block_Payment_Klarna_Form extends Mage_Payment_Block_Form
{

    /**
     * Constructor
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('spryng/form/klarna.phtml');
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    public function getQuote()
    {
        $quoteId = Mage::getSingleton('checkout/session')->getLastQuoteId();
        $quote = Mage::getModel('sales/quote')->load($quoteId);
        return $quote;
    }

}
