<?php


namespace Nguyen\ProductNotification\Model\Data;

use Nguyen\ProductNotification\Api\Data\RuleInterface;

class Rule extends \Magento\Framework\Api\AbstractExtensibleObject implements RuleInterface
{

    /**
     * Get rule_id
     * @return string|null
     */
    public function getRuleId()
    {
        return $this->_get(self::RULE_ID);
    }

    /**
     * Set rule_id
     * @param string $ruleId
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setRuleId($ruleId)
    {
        return $this->setData(self::RULE_ID, $ruleId);
    }

    /**
     * Get title
     * @return string|null
     */
    public function getTitle()
    {
        return $this->_get(self::TITLE);
    }

    /**
     * Set title
     * @param string $title
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Nguyen\ProductNotification\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Nguyen\ProductNotification\Api\Data\RuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Nguyen\ProductNotification\Api\Data\RuleExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive()
    {
        return $this->_get(self::IS_ACTIVE);
    }

    /**
     * Set is_active
     * @param string $isActive
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get rule_type
     * @return string|null
     */
    public function getRuleType()
    {
        return $this->_get(self::RULE_TYPE);
    }

    /**
     * Set rule_type
     * @param string $ruleType
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setRuleType($ruleType)
    {
        return $this->setData(self::RULE_TYPE, $ruleType);
    }

    /**
     * Get rule_value
     * @return string|null
     */
    public function getRuleValue()
    {
        return $this->_get(self::RULE_VALUE);
    }

    /**
     * Set rule_value
     * @param string $ruleValue
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setRuleValue($ruleValue)
    {
        return $this->setData(self::RULE_VALUE, $ruleValue);
    }

    /**
     * Get email
     * @return string|null
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * Set email
     * @param string $email
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }
}
