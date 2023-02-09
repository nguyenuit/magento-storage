<?php

namespace Nguyen\AjaxHolePunching\Controller\Block;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\View\Result\LayoutFactory as LayoutResultFactory;

class Index extends Action
{

    private $layoutResultFactory;

    private $httpResponse;

    public function __construct(
        Context $context,
        LayoutResultFactory $layoutResultFactory,
        HttpResponse $httpResponse        
    ) {
        $this->layoutResultFactory = $layoutResultFactory;
        $this->httpResponse = $httpResponse;
        parent::__construct($context);
    }
    
    public function execute()
    {

        $device = $this->getRequest()->getParam('device');
        $moduleName = $this->getRequest()->getParam('moduleName');

        $result = $this->layoutResultFactory->create();

        if ($device == 'desktop'){

            $result->addHandle('nguyen_ajax_header_snippet');

            $result->getLayout()->getBlock('ajax-header-snippet')->setData('moduleName', $moduleName);
        }else{
            $result->addHandle('nguyen_ajax_header_snippet_mobile');

            $result->getLayout()->getBlock('ajax-header-snippet-mobile')->setData('moduleName', $moduleName);
        }
        
        return $result; 
    }
}