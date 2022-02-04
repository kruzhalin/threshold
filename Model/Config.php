<?php

namespace Kruzhalin\Threshold\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{

    const XML_PATH_THRESHOLD_VALIDATION_ACTIVE = 'sales/threshold_validation/active';
    const XML_PATH_THRESHOLD_VALUE             = 'sales/threshold_validation/threshold_value';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @param WriterInterface      $configWriter
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        WriterInterface      $configWriter,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->configWriter = $configWriter;
        $this->scopeConfig  = $scopeConfig;
    }

    /**
     * @param $storeId
     * @return bool
     */
    public function isThresholdValidationEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_THRESHOLD_VALIDATION_ACTIVE,
            ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getThresholdValue($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_THRESHOLD_VALUE,
            ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }

    /**
     * @param $value
     * @return void
     */
    public function setThresholdValue($value)
    {
        $this->configWriter->save(
            self::XML_PATH_THRESHOLD_VALUE,
            $value
        );
    }
}
