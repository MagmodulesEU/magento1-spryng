<?php
/**
 * Copyright © 2017 Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $this->getTable('sales/order'), 'spryng_transaction_id', array(
    'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'   => 255,
    'nullable' => true,
    'comment'  => 'Spryng Payment Transaction ID',
    )
);

$installer->addAttribute(
    'customer', 'spryng_customer_id', array(
    'type'         => 'varchar',
    'label'        => 'Spryng Customer ID',
    'input'        => 'text',
    'required'     => false,
    'visible'      => false,
    'user_defined' => true,
    'sort_order'   => 1000,
    'position'     => 1000,
    'system'       => 0,
    )
);

$installer->endSetup();
