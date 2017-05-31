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
}
