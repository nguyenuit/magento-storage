<?php

namespace Nguyen\ProductNotification\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    protected $_resource;
    
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ){
        $this->_resource = $resource;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.9') < 0) {

            $write = $this->_resource->getConnection('core_write');

            $write->query("update eav_attribute set frontend_label='Product Notification Rules' where attribute_code='nguyen_productnotification'");
        }

        $setup->endSetup();
    }
}