<?php


namespace Nguyen\ProductNotification\Api\Data;

interface RuleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Rule list.
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface[]
     */
    public function getItems();

    /**
     * Set title list.
     * @param \Nguyen\ProductNotification\Api\Data\RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
