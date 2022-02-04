<?php

namespace Kruzhalin\Threshold\Model\Api;

use Kruzhalin\Threshold\Api\ThresholdInterface;
use Kruzhalin\Threshold\Model\Config;
use Psr\Log\LoggerInterface;

class Threshold implements ThresholdInterface
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Config          $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config          $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        try {
            $this->config->setThresholdValue($value);
            $response = ['success' => true, 'message' => __('Threshold values was changes successfully.')];
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
            $this->logger->info($e->getMessage());
        }

        return json_encode($response);
    }
}
