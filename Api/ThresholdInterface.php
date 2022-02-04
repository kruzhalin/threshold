<?php

namespace Kruzhalin\Threshold\Api;

interface ThresholdInterface
{
    /**
     * Set Value for Threshold Value
     *
     * @param string $value
     * @return string
     */
    public function setValue($value);
}
