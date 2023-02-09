<?php

namespace Nguyen\ProductNotification\Controller\Adminhtml\Rule;

class Delete extends \Nguyen\ProductNotification\Controller\Adminhtml\Rule
{

    protected $_ruleFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Nguyen\ProductNotification\Model\RuleFactory $ruleFactory
    ) {

        $this->_ruleFactory = $ruleFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('rule_id');
        if ($id) {
            try {
                // init model and delete
                $rule = $this->_ruleFactory->create();
                $rule->load($id);
                $rule->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Notification Rule.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['rule_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Notification Rule to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
