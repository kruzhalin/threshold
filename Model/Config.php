<?php

namespace Kruzhalin\Threshold\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{

    const XML_PATH_THRESHOLD_VALIDATION_ACTIVE = 'sales/threshold_validation/active';
    const XML_PATH_THRESHOLD_VALUE             = 'sales/threshold_validation/threshold_value';

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
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
    public function getThresholdValue($storeId = null){
        return $this->scopeConfig->getValue(
            self::XML_PATH_THRESHOLD_VALUE,
            ScopeInterface::SCOPE_STORES,
            $storeId
        );
    }
}
