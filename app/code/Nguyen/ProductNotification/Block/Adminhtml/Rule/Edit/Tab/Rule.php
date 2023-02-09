<?php

namespace Nguyen\ProductNotification\Block\Adminhtml\Rule\Edit\Tab;

class Rule extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_statusOptions;
  
    protected $_notificationType;
   
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Nguyen\ProductNotification\Model\Source\Status $statusOptions,
        \Nguyen\ProductNotification\Model\Source\NotificationType $notificationType,
        $data = []
    ) {
        $this->_statusOptions = $statusOptions;
        $this->_notificationType = $notificationType;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare Form
     *
     * @return Rule|\Magento\Backend\Block\Widget\Form\Generic
     */
    protected function _prepareForm()
    {
       
        $form = $this->_formFactory->create();
        $rule = $this->_coreRegistry->registry('nguyen_productnotification_rule');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('General Information'),
                'class'  => 'fieldset-wide'
            ]
        );

        if ($rule->getId()) {
            $fieldset->addField(
                'rule_id',
                'hidden',
                ['name' => 'rule_id']
            );
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name'  => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'description',
            'text',
            [
                'name'  => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => false,
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'name'  => 'is_active',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $this->_statusOptions->toOptionArray(),
                'required' => true,
            ]
        )->setValue('1');

        $fieldset->addField(
            'rule_type',
            'select',
            [
                'name'  => 'rule_type',
                'label' => __('Notification Type'),
                'title' => __('Notification Type'),
                'values' => $this->_notificationType->toOptionArray(),
                'required' => true,
            ]
        )->setValue('1');

         $fieldset->addField(
            'rule_value',
            'text',
            [
                'name'  => 'rule_value',
                'label' => __('Notification Condition'),
                'title' => __('Notification Condition'),
                'after_element_html' => '<small>Minimum condition to apply notification</small>',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name'  => 'email',
                'label' => __('Notification Email'),
                'title' => __('Notification Email'),
                'after_element_html' => '<small>Add many emails seperated by commas</small>',
                'required' => true,
            ]
        );


        $form->addValues($rule->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
