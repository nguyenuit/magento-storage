<?php

namespace Nguyen\ProductNotification\Controller\Adminhtml\Rule;

class Edit extends \Nguyen\ProductNotification\Controller\Adminhtml\Rule
{

    protected $resultPageFactory;

    protected $_ruleFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Nguyen\ProductNotification\Model\RuleFactory $ruleFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_ruleFactory = $ruleFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('rule_id');
        $rule = $this->_ruleFactory->create();
        
        // 2. Initial checking
        if ($id) {
            $rule->load($id);
            if (!$rule->getId()) {
                $this->messageManager->addErrorMessage(__('This Notification Rule no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('nguyen_productnotification_rule', $rule);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Notification Rule') : __('New Notification Rule'),
            $id ? __('Edit Notification Rule') : __('New Notification Rule')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Notification Rules'));
        $resultPage->getConfig()->getTitle()->prepend($rule->getId() ? __('Edit Notification Rule %1', $rule->getId()) : __('New Notification Rule'));
        return $resultPage;
    }
}
