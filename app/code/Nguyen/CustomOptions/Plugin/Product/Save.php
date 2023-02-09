<?php

namespace Nguyen\CustomOptions\Plugin\Product;

class Save
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected  $_logger;

    /**
     * Save constructor.
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_logger = $logger;
    }

    /**
     * @var \Nguyen\CustomOptions\Model\ImageUploader
     */
    private $imageUploader;

    public function aroundExecute(\Magento\Catalog\Controller\Adminhtml\Product\Save $subject, \Closure $proceed)
    {
        $productPostValues = $subject->getRequest()->getParam('product');
        if(array_key_exists('options',$productPostValues)){
          foreach ($productPostValues['options'] as $key => $value) {
            if(!array_key_exists('values',$value)){
              break;
            }
            foreach($value['values'] as $key1 => $value1){
              if(array_key_exists('image',$value1) && $value1['image'] != ""){
                try { 
                    $imageName = $value1['image'][0]['name'];
                    $this->getImageUploader()->moveFileFromTmp($imageName);
                    $productPostValues['options'][$key]['values'][$key1]['image'] = $imageName;
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                }
              }else if( in_array($value['type'], ['radio', 'checkbox','select', 'multiple','drop_down'])){
                $productPostValues['options'][$key]['values'][$key1]['image'] = '';
              }
            }
          }
        }
        $subject->getRequest()->setPostValue('product', $productPostValues);
        $returnValue = $proceed();

        return $returnValue;
    }

    /**
     * Get Instance of Oroduct Image Uploader.
     *
     * @return \Nguyen\ProductAttribute\Model\ImageUploader
     *
     * @deprecated 101.0.0
     */
    private function getImageUploader()
    {
        if ($this->imageUploader === null) {
            $this->imageUploader = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Nguyen\CustomOptions\Model\ImageUploader::class);
        }

        return $this->imageUploader;
    }
}
