<?php

namespace Kruzhalin\Threshold\Plugin\Checkout\Controller\Cart;

use Kruzhalin\Threshold\Model\Config;
use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class Add
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Configurable
     */
    private $configurable;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @param Configurable               $configurable
     * @param Config                     $config
     * @param ManagerInterface           $messageManager
     * @param StoreManagerInterface      $storeManager
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Configurable               $configurable,
        Config                     $config,
        ManagerInterface           $messageManager,
        StoreManagerInterface      $storeManager,
        ProductRepositoryInterface $productRepository
    ) {
        $this->config            = $config;
        $this->configurable      = $configurable;
        $this->messageManager    = $messageManager;
        $this->productRepository = $productRepository;
        $this->storeManager      = $storeManager;

    }

    /**
     * @param \Magento\Checkout\Controller\Cart\Add $subject
     * @param \Closure                              $proceed
     * @return mixed
     * @throws StateException
     */
    public function aroundExecute(
        \Magento\Checkout\Controller\Cart\Add $subject,
        \Closure                              $proceed
    ) {

        if (!$this->config->isThresholdValidationEnabled()) {
            return $proceed();
        }
        $mainProduct = null;

        $productId = (int)$subject->getRequest()->getParam('product');
        if ($product = $this->initProduct($productId)) {
            if ($product->getTypeId() == Configurable::TYPE_CODE) {
                $params       = $subject->getRequest()->getParams();
                $childProduct = $this->configurable->getProductByAttributes($params['super_attribute'], $product);
                if ($childProduct->getId()) {
                    $mainProduct = $childProduct;
                }
            }

            if ($product->getTypeId() == Type::TYPE_SIMPLE) {
                $mainProduct = $product;
            }
        }

        if ($mainProduct->getId()) {
            $stockItem      = $mainProduct->getExtensionAttributes()->getStockItem();
            $thresholdValue = $this->config->getThresholdValue();
            if ($stockItem->getQty() > $thresholdValue) {
                return $proceed();
            } else {
                $this->messageManager->addErrorMessage(
                    __("Threshold Validation Failed. Verify the product and try again.")
                );
                $subject->getRequest()->setParam('product', false);
            }
        }
        return $proceed();
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function initProduct($productId)
    {
        if ($productId) {
            $storeId = $this->storeManager->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }
}
