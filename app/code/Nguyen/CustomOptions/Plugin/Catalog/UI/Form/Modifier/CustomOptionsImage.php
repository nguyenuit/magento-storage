<?php

declare(strict_types=1);

namespace Nguyen\CustomOptions\Plugin\Catalog\UI\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Nguyen\CustomOptions\Model\Config;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Media;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Nguyen\CustomOptions\Model\ImageUploader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class CustomOptionsImage
{
    const FIELD_IMAGE_UPLOAD_NAME = 'image';
    const FIELD_IMAGE_DISPLAY_NAME = 'store_image';
    const FIELD_IMAGE_DELETE_NAME = 'delete_image';
    const MAX_FILE_SIZE = 2097152;

    protected $uploader;

    /**
     * @var LocatorInterface
     * @since 101.0.0
     */
    protected $locator;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;


    public function __construct(
        Config $config,
        ImageUploader $uploader,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        LocatorInterface $locator
    ) {
        $this->locator = $locator;
        $this->config = $config;
        $this->uploader = $uploader;
        $this->filesystem = $filesystem;
        $this->_storeManager = $storeManager;
    }

    public function afterModifyMeta(CustomOptions $subject, array $meta): array
    {
        if (!$this->config->isActiveModule()) {
            return $meta;
        }
        return array_replace_recursive($meta, [
            CustomOptions::GROUP_CUSTOM_OPTIONS_NAME => [
                'children' => [
                    CustomOptions::GRID_OPTIONS_NAME => [
                        'children' => [
                            'record' => [
                                'children' => [
                                    CustomOptions::CONTAINER_OPTION => [
                                        'children' => [
                                            CustomOptions::GRID_TYPE_SELECT_NAME => [
                                                'children' => [
                                                    'record' => [
                                                        'children' => [
                                                            static::FIELD_IMAGE_UPLOAD_NAME => $this->getImgConfig(51),
                                                            // static::FIELD_IMAGE_DELETE_NAME => $this->getIsDeleteImgFieldConfig(52),
                                                            // static::FIELD_IMAGE_DISPLAY_NAME => $this->getImgDisplayConfig(53),
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function afterModifyData(CustomOptions $subject, array $data): array
    {
        foreach ($data[$this->locator->getProduct()->getId()]['product']['options'] as $k => $item) {
            if (array_key_exists('values', $item)) {
                foreach ($item['values'] as $key => $value) {
                    if (array_key_exists('image', $value) && $value['image'] != "") {
                        $data[$this->locator->getProduct()->getId()]['product']['options'][$k]['values'][$key]['image'] = $this->getImageInfo($value['image']);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param $filePath
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getImageInfo($fileName)
    {
        $arrImage = [];
        $filePath =  $this->uploader->getBasePath() . DIRECTORY_SEPARATOR . $fileName;
        $fileAbsolutePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $fileAbsolutePath = $fileAbsolutePath . $filePath;
        $imageInfo = @getimagesize($fileAbsolutePath);

        $arrImage[] = [
            'type' => $imageInfo['mime'],
            'name' => $fileName,
            'file' => $filePath,
            'size' => @filesize($fileAbsolutePath),
            'url'  => $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $filePath
        ];
        return $arrImage;
    }


    protected function getMediaFieldConfig($sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Image'),
                        'componentType' => 'image',
                        'formElement' => 'fileUploader',
                        'dataScope' => static::CUSTOM_OPTIONS_IMAGE,
                        'dataType' => Media::NAME,
                        'sortOrder' => $sortOrder
                    ],
                ],
            ],
        ];
    }

    protected function getImgConfig($sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Images'),
                        'componentType' => 'imageUploader',
                        'formElement' => 'imageUploader',
                        'fileInputName' => static::FIELD_IMAGE_UPLOAD_NAME,
                        'dataScope' => static::FIELD_IMAGE_UPLOAD_NAME,
                        'maxFileSize' => static::MAX_FILE_SIZE,
                        'allowedExtensions' => 'jpg jpeg png',
                        'sortOrder' => $sortOrder,
                        'uploaderConfig' => [
                            'url' => "custom_options/image/upload"
                        ],
                        'isMultipleFiles' => false
                    ],
                ],
            ],
        ];
    }

    /**
     * Get config for "Required" field
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getIsDeleteImgFieldConfig($sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Delete Image'),
                        'componentType' => Field::NAME,
                        'formElement' => Checkbox::NAME,
                        'dataScope' => static::FIELD_IMAGE_DELETE_NAME,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'value' => '0',
                        'valueMap' => [
                            'true' => '1',
                            'false' => '0'
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getImgDisplayConfig($sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Image Path'),
                        'componentType' =>  Field::NAME,
                        'formElement' => Input::NAME,
                        'dataScope' => static::FIELD_IMAGE_DISPLAY_NAME,
                        'dataType' => Text::NAME,
                        'visible' => false,
                        'sortOrder' => $sortOrder,
                        'validation' => [
                            'required-entry' => false
                        ],
                    ],
                ],
            ],
        ];
    }
}
