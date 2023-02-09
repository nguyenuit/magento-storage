<?php

namespace Nguyen\CustomOptions\Controller\Adminhtml\Image;

use Nguyen\CustomOptions\Model\ImageUploader;
use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Upload extends Action implements HttpPostActionInterface
{
    /**
     * Image uploader
     *
     * @var \Nguyen\CustomOptions\Model\ImageUploader
     */
    protected $imageUploader;

    public function __construct(Action\Context $context, ImageUploader $imageUploader)
    {

        $this->imageUploader = $imageUploader;
        parent::__construct($context);
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $imageId = $this->_request->getParam('param_name', 'image');
        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
