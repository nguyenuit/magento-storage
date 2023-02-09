<?php

namespace Nguyen\ClearCache\Cron;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Psr\Log\LoggerInterface;

class Update
{

    protected $_logger;

    protected $cacheTypeList;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        TypeListInterface $cacheTypeList 
    ) {
        $this->_logger = $logger;
        $this->cacheTypeList = $cacheTypeList;
    }

    public function execute()
    {
        $invalidatedCaches = $this->cacheTypeList->getInvalidated();

        if (!empty($invalidatedCaches)){

            foreach($invalidatedCaches as $key => $value){

                if (isset($invalidatedCaches[$key])){

                    try{
                        $this->cacheTypeList->cleanType($key);

                        $this->_logger->info('Clearing '. $key .' cache successfully!');

                    }catch (\Exception $e) {
                        $this->_logger->critical($e->getMessage());
                    }
                }

            }
        }
        return;
    }

}