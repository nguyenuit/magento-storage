<?php
namespace Nguyen\PracticeProxy\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_testFastLoading;

 	public function __construct(
        \Magento\Framework\App\Helper\Context $helperContext,
        \Nguyen\PracticeProxy\Model\FastLoading $testFastLoading
    ) {
        $this->_testFastLoading = $testFastLoading;
        parent::__construct($helperContext);
    }

    public function testProxy(){

       return $this->_testFastLoading->getSlowValue();
    }
}
