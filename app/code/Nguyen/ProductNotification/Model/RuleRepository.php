<?php


namespace Nguyen\ProductNotification\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Nguyen\ProductNotification\Api\Data\RuleSearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Nguyen\ProductNotification\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Nguyen\ProductNotification\Api\Data\RuleInterfaceFactory;
use Nguyen\ProductNotification\Api\RuleRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Nguyen\ProductNotification\Model\ResourceModel\Rule as ResourceRule;

class RuleRepository implements RuleRepositoryInterface
{

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $ruleFactory;

    protected $dataRuleFactory;

    protected $extensionAttributesJoinProcessor;

    protected $ruleCollectionFactory;

    private $collectionProcessor;

    protected $resource;

    private $storeManager;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceRule $resource
     * @param RuleFactory $ruleFactory
     * @param RuleInterfaceFactory $dataRuleFactory
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param RuleSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceRule $resource,
        RuleFactory $ruleFactory,
        RuleInterfaceFactory $dataRuleFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        RuleSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->ruleFactory = $ruleFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRuleFactory = $dataRuleFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Nguyen\ProductNotification\Api\Data\RuleInterface $rule
    ) {
        /* if (empty($rule->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $rule->setStoreId($storeId);
        } */
        
        $ruleData = $this->extensibleDataObjectConverter->toNestedArray(
            $rule,
            [],
            \Nguyen\ProductNotification\Api\Data\RuleInterface::class
        );
        
        $ruleModel = $this->ruleFactory->create()->setData($ruleData);
        
        try {
            $this->resource->save($ruleModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Notification Rule: %1',
                $exception->getMessage()
            ));
        }
        return $ruleModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($ruleId)
    {
        $rule = $this->ruleFactory->create();
        $this->resource->load($rule, $ruleId);
        if (!$rule->getId()) {
            throw new NoSuchEntityException(__('Notification Rule with id "%1" does not exist.', $ruleId));
        }
        return $rule->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->ruleCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Nguyen\ProductNotification\Api\Data\RuleInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Nguyen\ProductNotification\Api\Data\RuleInterface $rule
    ) {
        try {
            $ruleModel = $this->ruleFactory->create();
            $this->resource->load($ruleModel, $rule->getRuleId());
            $this->resource->delete($ruleModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Notification Rule: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($ruleId)
    {
        return $this->delete($this->getById($ruleId));
    }
}
