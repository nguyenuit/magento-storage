<?php


namespace Nguyen\ProductNotification\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $table_nguyen_productnotification_rule = $setup->getConnection()->newTable($setup->getTable('nguyen_productnotification_rule'));

        $table_nguyen_productnotification_rule->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,],
            'Entity ID'
        );

        $table_nguyen_productnotification_rule->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Title'
        );

        $table_nguyen_productnotification_rule->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'Description'
        );

        $table_nguyen_productnotification_rule->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Status'
        );

        $table_nguyen_productnotification_rule->addColumn(
            'rule_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Notification Rule Type'
        );

        $table_nguyen_productnotification_rule->addColumn(
            'rule_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Notification Condition'
        );

        $setup->getConnection()->createTable($table_nguyen_productnotification_rule);
    }
}
