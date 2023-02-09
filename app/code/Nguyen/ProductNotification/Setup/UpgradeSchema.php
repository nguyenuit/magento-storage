<?php

namespace Nguyen\ProductNotification\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.6', '<')) {

            $installer->getConnection()->addColumn(
                $installer->getTable('nguyen_productnotification_rule'),
                'email',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 50,
                    'comment' =>'Notification Email',
                    'nullable' => false,
                    'default' => ''
                ]

            );
        }

        $installer->endSetup();
    }
}
