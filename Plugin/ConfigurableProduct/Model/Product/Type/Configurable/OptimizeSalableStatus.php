<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Plugin\ConfigurableProduct\Model\Product\Type\Configurable;

class OptimizeSalableStatus
{
    protected \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration;

    public function __construct(
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
    ) {
        $this->stockConfiguration = $stockConfiguration;
    }

    public function afterGetUsedProducts(
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $subject,
        array $result
    ) {
        foreach ($result as $key => $product) {
            if ($product->hasData('quantity') && $product->getData('is_salable') == 0) {
                $product->setIsSalable(0);

                if (!$this->stockConfiguration->isShowOutOfStock()) {
                    unset($result[$key]);
                }
            }
        }

        return $result;
    }
}
