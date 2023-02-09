<?php
/**
 * Copyright © 2015 Nguyen . All rights reserved.
 */
namespace Nguyen\CategoriesNavigation\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_NGUYEN_NAVIGATION_ELEMENTS_GENERAL_ENABLED = 'nguyen_navigation_elements/general/is_enabled';

    public function getConfigValue($field, $storeId = null) {

        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isEnabled($storeId = null) {

        return $this->getConfigValue(self::XML_PATH_NGUYEN_NAVIGATION_ELEMENTS_GENERAL_ENABLED, $storeId);
    }
}