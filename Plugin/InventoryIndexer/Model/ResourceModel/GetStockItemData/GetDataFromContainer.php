<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Plugin\InventoryIndexer\Model\ResourceModel\GetStockItemData;

class GetDataFromContainer
{
    protected \MageSuite\PerformanceCategory\Model\Container\StockItemData $stockItemContainer;

    public function __construct(
        \MageSuite\PerformanceCategory\Model\Container\StockItemData $stockItemContainer
    ) {
        $this->stockItemContainer = $stockItemContainer;
    }

    public function aroundExecute(\Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData $subject, callable $proceed, string $sku, int $stockId): ?array
    {
        return $this->stockItemContainer->getProductStockItem($sku, $stockId) ?? $proceed($sku, $stockId);
    }
}
