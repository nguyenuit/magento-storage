<?php

namespace Nguyen\ProductNotification\Observer;

class Notification implements \Magento\Framework\Event\ObserverInterface
{

    protected $_orderRepository;

    protected $_productNotificationHelper;

    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Nguyen\ProductNotification\Helper\Data $productNotificationHelper
    ){

        $this->_orderRepository = $orderRepository;
        $this->_productNotificationHelper = $productNotificationHelper;  
    }

    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if($this->_productNotificationHelper->isEnabled() == 1 && $observer->getEvent()) {

            $orderIds = $observer->getEvent()->getOrderIds();
            $lastorderId = $orderIds[0];
            $order = $this->_orderRepository->get($lastorderId);

            $orderData = $order->getData();

            // process product notification
            $this->_productNotificationHelper->processProductNotification($order);
        }
    }
}
