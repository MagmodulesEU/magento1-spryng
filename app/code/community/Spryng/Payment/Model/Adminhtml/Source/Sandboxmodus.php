<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Model_Adminhtml_Source_Sandboxmodus
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'sandbox',
                'label' => Mage::helper('spryng')->__('Enabled (sandbox mode)'),
            ),
            array(
                'value' => 'live',
                'label' => Mage::helper('spryng')->__('Disabled (live)'),
            ),
        );
    }
}

