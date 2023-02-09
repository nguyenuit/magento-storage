<?php

namespace Nguyen\ProductNotification\Model\Source;

class NotificationType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Status values
     */
    const TYPE_QUANTITY = 0;
    const TYPE_SALES = 1;

    /**
     * Get Option Array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $optionArray = ['' => ' '];
        foreach ($this->toOptionArray() as $option) {
            $optionArray[$option['value']] = $option['label'];
        }
        return $optionArray;
    }

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_QUANTITY,  'label' => __('Product Quantity')],
            ['value' => self::TYPE_SALES,  'label' => __('Product Sales')],
        ];
    }
}
