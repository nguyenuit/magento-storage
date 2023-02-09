<?php


namespace Nguyen\ProductNotification\Model;

use Nguyen\ProductNotification\Api\Data\RuleInterface;
use Magento\Framework\Api\DataObjectHelper;
use Nguyen\ProductNotification\Api\Data\RuleInterfaceFactory;

class Rule extends \Magento\Framework\Model\AbstractModel
{

    protected $dataObjectHelper;

    protected $ruleDataFactory;

    protected $_eventPrefix = 'nguyen_productnotification_rule';

    protected $_productCollection;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param RuleInterfaceFactory $ruleDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Nguyen\ProductNotification\Model\ResourceModel\Rule $resource
     * @param \Nguyen\ProductNotification\Model\ResourceModel\Rule\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        RuleInterfaceFactory $ruleDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Nguyen\ProductNotification\Model\ResourceModel\Rule $resource,
        \Nguyen\ProductNotification\Model\ResourceModel\Rule\Collection $resourceCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->ruleDataFactory = $ruleDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_productCollection = $collectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve rule model with rule data
     * @return RuleInterface
     */
    public function getDataModel()
    {
        $ruleData = $this->getData();
        
        $ruleDataObject = $this->ruleDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $ruleDataObject,
            $ruleData,
            RuleInterface::class
        );
        
        return $ruleDataObject;
    }


    public function getProducts($rule)
    {
        $productSelected = [];
        $productCollection = $this->_productCollection->create();

        $collection = $productCollection->addAttributeToSelect('nguyen_productnotification')->load();

        foreach ($collection as $product) {
            if ($product->getData('nguyen_productnotification')) {
                $rules = explode(',', $product->getData('nguyen_productnotification'));
                if (in_array($rule->getId(), $rules)) {
                    array_push($productSelected, $product->getId());
                }
            }
        }
        return array_unique($productSelected);
    }

}
