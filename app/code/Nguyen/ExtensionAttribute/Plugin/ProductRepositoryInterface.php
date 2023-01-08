<?php
namespace Nguyen\ExtensionAttribute\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface as MagentoRepository;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class ProductRepositoryInterface
{
    protected $_collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ){
        $this->_collectionFactory = $collectionFactory;
    }

    public function afterGet(MagentoRepository $subject, ProductInterface $product){

        // add featured
        if ($product->getExtensionAttributes() && $product->getExtensionAttributes()->getIsFeatured()){
            return $product;
        }
        $isFeatured = $this->getIsFeatured($product->getId());

        $extensionAttributes = $product->getExtensionAttributes()->setIsFeatured($isFeatured);
        $product->setExtensionAttributes($extensionAttributes);

        // add handmade
        if ($product->getExtensionAttributes() && $product->getExtensionAttributes()->getIsHandMade()){
            return $product;
        }
        $isHandMade = $this->getIsHandMade($product->getId());

        $extensionAttributes = $product->getExtensionAttributes()->setIsHandMade($isHandMade);
        $product->setExtensionAttributes($extensionAttributes);

        return $product;
    }

    public function getIsFeatured($productId){
        return $this->_collectionFactory->create()
            ->addFieldToFilter('entity_id', ['eq' => $productId])
            ->getFirstItem()->getData('is_featured');
    }

    public function getIsHandMade($productId){
        return $this->_collectionFactory->create()
            ->addFieldToFilter('entity_id', ['eq' => $productId])
            ->getFirstItem()->getData('is_handmade');
    }
}

