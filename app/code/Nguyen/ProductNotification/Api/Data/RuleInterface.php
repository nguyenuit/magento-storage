<?php


namespace Nguyen\ProductNotification\Api\Data;

interface RuleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const TITLE = 'title';
    const IS_ACTIVE = 'is_active';
    const RULE_TYPE = 'rule_type';
    const RULE_ID = 'rule_id';
    const DESCRIPTION = 'description';
    const RULE_VALUE = 'rule_value';

    /**
     * Get rule_id
     * @return string|null
     */
    public function getRuleId();

    /**
     * Set rule_id
     * @param string $ruleId
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setRuleId($ruleId);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setTitle($title);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Nguyen\ProductNotification\Api\Data\RuleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Nguyen\ProductNotification\Api\Data\RuleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Nguyen\ProductNotification\Api\Data\RuleExtensionInterface $extensionAttributes
    );

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setDescription($description);

    /**
     * Get is_active
     * @return string|null
     */
    public function getIsActive();

    /**
     * Set is_active
     * @param string $isActive
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setIsActive($isActive);

    /**
     * Get rule_type
     * @return string|null
     */
    public function getRuleType();

    /**
     * Set rule_type
     * @param string $ruleType
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setRuleType($ruleType);

    /**
     * Get rule_value
     * @return string|null
     */
    public function getRuleValue();

    /**
     * Set rule_value
     * @param string $ruleValue
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setRuleValue($ruleValue);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Nguyen\ProductNotification\Api\Data\RuleInterface
     */
    public function setEmail($email);
}
