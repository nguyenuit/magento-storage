<?php


namespace Nguyen\ProductNotification\Controller\Adminhtml\Rule;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    protected $_ruleFactory;

    protected $_productFactory;

    protected $_storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Nguyen\ProductNotification\Model\RuleFactory $ruleFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_ruleFactory = $ruleFactory;
        $this->_productFactory = $productFactory;
        $this->_storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('rule_id');
        
            $rule = $this->_ruleFactory->create()->load($id);
            if (!$rule->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Notification Rule no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $rule->setData($data);
        
            try {
                $rule->save();
                $this->messageManager->addSuccessMessage(__('You saved the Notification Rule.'));
                $this->dataPersistor->clear('nguyen_productnotification_rule');

                $storeId = $this->_storeManager->getStore()->getId();

                $productIds = $this->filterProductsPostData();

                $productsSelected = $this->_ruleFactory->create()->getProducts($rule);

                $ignoreProductIds = $this->unassignList($productsSelected, $productIds);

                $this->ignoreProduct($ignoreProductIds, $rule->getId(), $storeId);

                $applyProductIds = $this->newAssignList($productsSelected, $productIds);

                $this->applyProduct($applyProductIds, $rule->getId(), $storeId);


                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['rule_id' => $rule->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Notification Rule.'));
            }
        
            $this->dataPersistor->set('nguyen_productnotification_rule', $data);
            return $resultRedirect->setPath('*/*/edit', ['rule_id' => $this->getRequest()->getParam('rule_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

   
    protected function filterProductsPostData()
    {
        if (null !== $this->getRequest()->getPost('products')) {
            $productList = $this->getRequest()->getPost('products');

            $productList = explode("&", $productList);
            foreach ($productList as $key => $value) {
                if (!is_numeric($value)) {
                    array_splice($productList, $key, 1);
                }
            }
            return $productList;
        }
        return;
    }

    protected function newAssignList($oldList, $newList)
    {
        $newAssign =[];
        if (null!== $newList) {
            $newAssign = array_diff($newList, $oldList);
        }
        return $newAssign;
    }

    
    protected function unassignList($oldList, $newList)
    {
        $unassigned =[];
        if (null!== $newList) {
            $unassigned = array_diff($oldList, $newList);
        }
        return $unassigned;
    }

    protected function applyProduct($productList, $ruleId, $storeId)
    {
        foreach ($productList as $key => $productId) {
            $product = $this->getProductByIdAndStore($productId, $storeId);
            $ruleList = $product->getData('nguyen_productnotification');
            $product->addData(['nguyen_productnotification' => $this->addRuleToAttribute($ruleList, $ruleId)]);
            $this->saveProduct($product);
        }
    }

    protected function addRuleToAttribute($attribute, $ruleId)
    {
        if (!empty($attribute)) {
            $ruleList = explode(",", $attribute);

            if (!in_array($ruleId, $ruleList)) {
                $ruleList[] = $ruleId;
            }

            $ruleList = implode(",", $ruleList);
            return $ruleList;
        }
        return $ruleId;
    }

    protected function getProductByIdAndStore($id, $storeId)
    {
        return $this->_productFactory->create()->setStoreId($storeId)->load($id);
    }

    protected function ignoreProduct($productList, $ruleId, $storeId)
    {
        foreach ($productList as $key => $productId) {
            $product = $this->getProductByIdAndStore($productId, $storeId);
            $ruleList = $product->getData('nguyen_productnotification');
            $product->addData(
                ['nguyen_productnotification' =>
                    $this->removeRuleFromAttribute($ruleList, $ruleId)
                ]
            );
            $this->saveProduct($product);
        }
    }

    protected function removeRuleFromAttribute($attribute, $ruleId)
    {
        $res = "";
        $ruleList = explode(",", $attribute);
        if (count($ruleList) > 1) {
            $key = array_search($ruleId, $ruleList);
            if ($key !== FALSE) {
                unset($ruleList[$key]);
            }

            $res = implode(",", $ruleList);
        }
        return $res;
    }

    protected function saveProduct($product)
    {
        $product->save();
    }
}
