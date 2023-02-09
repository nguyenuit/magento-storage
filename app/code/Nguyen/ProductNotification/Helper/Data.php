<?php

namespace Nguyen\ProductNotification\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\Store;
use Magetop\Smtp\Helper\Data as SmtpData;
use Magetop\Smtp\Mail\Rse\Mail;
use Psr\Log\LoggerInterface;
use Magento\Email\Model\Template\SenderResolver;
use Magento\Framework\App\Area;

class Data extends AbstractHelper
{
   
    protected $_scopeConfig;

    protected $_logger;

    protected $_smtpDataHelper;

    protected $_mailResource;
    
    protected $_transportBuilder;
    
    protected $_senderResolver;    

    protected $_ruleCollectionFactory;

    protected $_productRepository;

    protected $_backendUrl;

    public function __construct (
        \Magento\Framework\App\Helper\Context $context,
        LoggerInterface $logger,
        SmtpData $smtpDataHelper,
        Mail $mailResource,
        TransportBuilder $transportBuilder,
        SenderResolver $senderResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Nguyen\ProductNotification\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        $this->_logger = $logger;
        $this->_smtpDataHelper = $smtpDataHelper;
        $this->_mailResource = $mailResource;
        $this->_transportBuilder = $transportBuilder;
        $this->_senderResolver = $senderResolver;
        $this->_scopeConfig = $scopeConfig;
        $this->_ruleCollectionFactory = $ruleCollectionFactory;
        $this->_productRepository = $productRepository;
        $this->_backendUrl = $backendUrl;

        parent::__construct ($context);     
    }

    public function getProductsGridUrl() {

        return $this->_backendUrl->getUrl('nguyen_productnotification/rule/products', ['_current' => true]);
    }

    public function isEnabled() {

        return $this->_scopeConfig->getValue('nguyenproductnotification/general/is_enabled');
    }

    public function getEmailFrom() {

        return $this->_scopeConfig->getValue('nguyenproductnotification/general/email_from');
    }

    public function getSmtpHost() {

        return $this->_scopeConfig->getValue('smtp/configuration_option/host');
    }

    public function getSmtpPort() {

        return $this->_scopeConfig->getValue('smtp/configuration_option/port');
    }

    public function getSmtpAuthentication() {

        return $this->_scopeConfig->getValue('smtp/configuration_option/authentication');
    }

    public function getSmtpUserName() {

        return $this->_scopeConfig->getValue('smtp/configuration_option/username');
    }

    public function getSmtpProtocol() {

        return $this->_scopeConfig->getValue('smtp/configuration_option/protocol');
    }

    public function sendNotificationMail($notificationEmail, $orderItem, $order, $notificationType, $productSales = 0 ) {
        
        $params['from'] = $this->getEmailFrom();
        $notificationEmail = explode(',', trim($notificationEmail));

        $params['to'] = $notificationEmail;

        $config = [
            'type'       => 'smtp',
            'host'       => $this->getSmtpHost(),
            'auth'       => $this->getSmtpAuthentication(),
            'username'   => $this->getSmtpUserName(),
            'ignore_log' => true,
            'force_sent' => true,
            'ssl'        => $this->getSmtpProtocol(),
            'port'       => $this->getSmtpPort(),
            'password'   => $this->_smtpDataHelper->getPassword(),
            'return_path'=> ''
        ];

        $this->_mailResource->setSmtpOptions(Store::DEFAULT_STORE_ID, $config);

        $from = $this->_senderResolver->resolve(
            isset($params['from']) ? $params['from'] : $config['username'],
            $this->_smtpDataHelper->getScopeId()
        );

        $orderData = $order->getData();

        $billingAddressData = $order->getBillingAddress()->getData();

        if ($notificationType == 'quantity'){

            $emailTemplate = 'product_quantity_notification_email_template';
            $orderedValue = (int)$orderItem->getData('qty_ordered');
        }else{
            $emailTemplate = 'product_sales_notification_email_template';
            $orderedValue = number_format($productSales, 2, ',', '');
        }

        $data = [
            'sku'               => $orderItem->getData('sku_value'),
            'ordered_value'     => $orderedValue,
            'customer_id'       => $orderData['customer_id'],
            'customer_email'    => $billingAddressData['email'],
            'order_increment_id'=> $orderData['increment_id']
        ];

        $this->_transportBuilder
            ->setTemplateIdentifier($emailTemplate)
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => Store::DEFAULT_STORE_ID])
            ->setTemplateVars($data)
            ->setFrom($from)
            ->addTo($params['to']);

        try {
        
            $this->_transportBuilder->getTransport()->sendMessage();
        } catch (Exception $e) {
        
            $this->_logger->critical($e);
        }
    }

    public function processProductNotification($order) {

        try {
            if ($this->isEnabled() == 1){
                
                $ruleData = $this->loadRuleData();                    
                if ($ruleData){

                    $orderItems = $this->processOrder($order);
                    $skus = array_keys($orderItems);
                    foreach ($skus as $sku){

                        $product = $this->getProductBySku($sku);

                        $nguyenProductNotification = $product->getResource()->getAttribute('nguyen_productnotification')->getFrontend()->getValue($product);

                        $notificationRules = explode(',', $nguyenProductNotification);
                        if ($notificationRules){

                            foreach ($notificationRules as $notificationRule){

                                if (isset($ruleData[$notificationRule])){

                                    $this->processNotification($ruleData, $notificationRule, $orderItems[$sku], $order);
                                }

                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
        
            $this->_logger->critical($e);
        }
    }

    public function processNotification($ruleData, $notificationRule, $orderItem, $order){

        if ($ruleData[$notificationRule]['rule_type'] == 0){
            // notify by product quantity
            if ($orderItem->getData('qty_ordered') >= $ruleData[$notificationRule]['rule_value']){

                if (isset($ruleData[$notificationRule]['email'])){

                    $this->sendNotificationMail($ruleData[$notificationRule]['email'], $orderItem, $order, 'quantity');
                }
            }
        }else{
            // notify by product sales
            // total excl. tax
            $productSales = $orderItem->getBaseRowTotal() - $orderItem->getBaseDiscountAmount();
            if ($productSales >= $ruleData[$notificationRule]['rule_value']){

                if (isset($ruleData[$notificationRule]['email'])){

                    $this->sendNotificationMail($ruleData[$notificationRule]['email'], $orderItem, $order, 'sales', $productSales);
                }
            }

        }
    }

    public function processOrder($order){

        $orderItems = array();
        foreach ($order->getItems() as $item) {
            $skuValue = $this->processSku($item->getData('sku'));
            $orderItems[$skuValue] = $item;        
        }

        return $orderItems;
    }

    public function processSku($sku){

        $skuValues = explode('-', $sku);
        $skuValue  = isset($skuValues[0]) ? $skuValues[0] : '';

        return $skuValue;
    }

    public function loadRuleData(){
        // load all active rules data
        $ruleCollection = $this->_ruleCollectionFactory->create();
        $ruleCollection->addFieldToFilter('is_active', array('eq' => 1));

        $ruleData = array();
        foreach ($ruleCollection as $rule){
            $ruleData[$rule->getData('rule_id')] = $rule->getData();
        }

        return $ruleData;
    }

    public function getProductBySku($sku){

        return $this->_productRepository->get($sku);
    }
}
