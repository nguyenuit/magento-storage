<?php

namespace Nguyen\ProductNotification\Controller\Adminhtml\Rule;

class InlineEdit extends \Magento\Backend\App\Action
{

    protected $jsonFactory;

    protected $_ruleFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Nguyen\ProductNotification\Model\RuleFactory $ruleFactory
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->_ruleFactory = $ruleFactory;
        parent::__construct($context);
    }

    /**
     * Inline edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        
        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $ruleId) {
                    /** @var \Nguyen\ProductNotification\Model\Rule $rule */
                    $rule = $this->_ruleFactory->create()->load($ruleId);
                    try {
                        $rule->setData(array_merge($rule->getData(), $postItems[$ruleId]));
                        $rule->save();
                    } catch (\Exception $e) {
                        $messages[] = "[Rule ID: {$ruleId}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }
        
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
