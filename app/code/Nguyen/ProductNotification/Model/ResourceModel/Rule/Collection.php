<?php


namespace Nguyen\ProductNotification\Model\ResourceModel\Rule;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'rule_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'nguyen_productnotification_rule_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'rule_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Nguyen\ProductNotification\Model\Rule::class,
            \Nguyen\ProductNotification\Model\ResourceModel\Rule::class
        );
    }
}
