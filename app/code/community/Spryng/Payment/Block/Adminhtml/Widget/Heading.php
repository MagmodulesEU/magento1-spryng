<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

class Spryng_Payment_Block_Adminhtml_Widget_Heading extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     *
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = sprintf(
            '
            <tr id="row_%s">
                <td colspan="5">
                    <h4 id="%s" style="border-bottom: 1px solid #dddddd;padding: 20px 5px 5px 5px;">%s</h4>
                    <div class="comment">
                        <span>%s</span>
                    </div>
                </td>
            </tr>',
            $element->getHtmlId(), $element->getHtmlId(), $element->getLabel(), $element->getComment()
        );

        return $html;
    }

}
