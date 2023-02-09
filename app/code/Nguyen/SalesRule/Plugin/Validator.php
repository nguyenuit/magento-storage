<?php

namespace Nguyen\SalesRule\Plugin;
use Psr\Log\LoggerInterface;

class Validator
{

    protected $validators;

    protected $_productRepository;

    protected $_scopeConfig;

    protected $_logger;

    public function __construct(
        \Magento\SalesRule\Model\Validator\Pool $validators,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    )
    {
        $this->validators = $validators;
        $this->_productRepository = $productRepository;
        $this->_scopeConfig = $scopeConfig;
        $this->_logger = $logger;
    }

    protected function getCouponcodeEnabledLimitByPallet()
    {     
        return $this->_scopeConfig->getValue('salesrule_couponcode/general/enabled');
    }

    protected function getCouponcodeTierPriceLevel()
    {     
        return $this->_scopeConfig->getValue('salesrule_couponcode/general/tier_price_level');
    }

    protected function getCouponcodeForPalletOnly()
    {     
        return $this->_scopeConfig->getValue('salesrule_couponcode/general/coupon_code');
    }

    public function limitCouponCodeByPallet($subject, $item){
        $result = true;
        /** @var \Zend_Validate_Interface $validator */
        foreach ($this->validators->getValidators('discount') as $validator) {
            $result = $validator->isValid($item);
            if (!$result) {
                break;
            }
        }
        
        try {

            $product = $this->getProductBySku($item->getData('sku'));

            $couponCode = $subject->getCouponCode();    

            if ($product && $couponCode){

                $allowCouponCodeWithTierPriceApplied = $this->allowCouponCodeWithTierPriceApplied($product, $item->getData('qty'), $couponCode);      

                return ($result && $allowCouponCodeWithTierPriceApplied);
            }

        } catch (\Exception $e) {

            $this->_logger->critical($e->getMessage());
        }

        return $result;
    }

    public function aroundCanApplyDiscount(\Magento\SalesRule\Model\Validator $subject, \Closure $proceed, $item)
    {

        if ($this->getCouponcodeEnabledLimitByPallet()){
            
            $result = $this->limitCouponCodeByPallet($subject, $item);
        }else{
            // NO LIMIT for pallet/highest tier price
            $couponCode = $subject->getCouponCode();  
            $isSpecificCouponCodeApplied = false;
            if ($couponCode){
                
                $isSpecificCouponCodeApplied = $couponCode == $this->getCouponcodeForPalletOnly() ? true : false;                
            }

            $product = $this->getProductBySku($item->getData('sku'));

            $isValidPallet = $this->checkProductHasPallet($product, $item->getData('qty'));

            if ($isSpecificCouponCodeApplied){

                $result = $isValidPallet;
            }else{
                
                // default case
                $result = $proceed($item);
            }
        }
        return $result;
    }
    
    public function checkProductHasPallet($product, $itemQty)
    {
        $tierPrice = $product->getTierPrice();

        $pallet = array_pop($tierPrice);

        $tierPriceQty = 0;

        if (isset($pallet) && isset($pallet['price_qty'])){

            $tierPriceQty = number_format($pallet['price_qty']);
        }

        if ($tierPriceQty != 0 && $itemQty >= $tierPriceQty){

            return true;
        }

        return false;
    }

    public function allowCouponCodeWithTierPriceApplied($product, $itemQty, $couponCode)
    {
        $tierPrice = $product->getTierPrice();

        if ($this->isTierPriceHasQtyOne($tierPrice)){
            
            $level = (int)$this->getCouponcodeTierPriceLevel();
        }else{

            $level = (int)$this->getCouponcodeTierPriceLevel() - 1;    
        }
       
        $tierPriceQty = 0;

        if (isset($tierPrice[$level])){

            $tierPriceQty = number_format($tierPrice[$level]['price_qty']);
        }

        if ($tierPriceQty != 0 && $itemQty >= $tierPriceQty && $couponCode){

            return false;
        }

        return true;
    }

    public function isTierPriceHasQtyOne($tierPrice){
        if (
            count($tierPrice) > 0 &&
            isset($tierPrice['0']) &&
            isset($tierPrice['0']['price_qty']) &&
            $tierPrice['0']['price_qty'] == 1
        ){
            return true;
        }
        return false;
    }

    public function getProductBySku($sku){

        try {

            return $this->_productRepository->get($sku);

        } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
            
            return null;
        } 
    }
}