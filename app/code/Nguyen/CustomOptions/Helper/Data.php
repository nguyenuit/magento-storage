<?php
 namespace Nguyen\CustomOptions\Helper;

 class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Magento\Framework\Serialize\SerializerInterface $serializerInterface = null
    ) {
        $this->serializer = $serializerInterface ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\JsonHexTag::class);
    }

    /**
     * @inheritdoc
     */
    public function serialize($data)
    {
        return $this->serializer->serialize($data);
    }
}