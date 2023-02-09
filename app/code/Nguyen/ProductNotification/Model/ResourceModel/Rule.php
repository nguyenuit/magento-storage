<?php


namespace Nguyen\ProductNotification\Model\ResourceModel;

class Rule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('nguyen_productnotification_rule', 'rule_id');
    }
}
