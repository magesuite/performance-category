<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Plugin\InventorySales\Model\AreProductsSalable;

class PreloadProductsStockData
{
    protected \MageSuite\PerformanceCategory\Model\Container\StockItemData $stockItemContainer;
    protected \Psr\Log\LoggerInterface $logger;

    public function __construct(
        \MageSuite\PerformanceCategory\Model\Container\StockItemData $stockItemContainer,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->stockItemContainer = $stockItemContainer;
        $this->logger = $logger;
    }

    public function beforeExecute(\Magento\InventorySales\Model\AreProductsSalable $subject, array $skus, int $stockId)
    {
        $this->stockItemContainer->initProducts($skus, $stockId);

        return null;
    }
}
