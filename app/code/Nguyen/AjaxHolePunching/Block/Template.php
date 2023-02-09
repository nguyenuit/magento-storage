<?php

namespace Nguyen\AjaxHolePunching\Block;

class Template extends \Magento\Framework\View\Element\Template {

    public $_coreRegistry;

    public $_storeManager;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }
}