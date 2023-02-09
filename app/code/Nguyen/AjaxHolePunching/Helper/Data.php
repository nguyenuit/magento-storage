<?php

namespace Nguyen\AjaxHolePunching\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	
    protected $_customerSession;

    protected $_request;

    protected $_productRepository;

 	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\AjaxHolePunchinger\Model\Session $session,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->_customerSession = $session;
        $this->_request = $request;
        $this->_productRepository = $productRepository;

        parent::__construct($context);
    }

    public function isLoggedIn() {

        return $this->_customerSession->isLoggedIn();
    }

    public function getModuleName() {

        return $this->_request->getModuleName();
    }

    public function getProductBySku($sku) {

        return $this->_productRepository->get($sku);
    }
}
