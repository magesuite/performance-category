<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Plugin\InventoryIndexer\CatalogInventory\Model\Indexer\Stock\CacheCleaner;

class CleanContainerDataAfterCacheClean
{
    protected \MageSuite\PerformanceCategory\Model\Container\StockItemData $stockItemDataContainer;
    protected \Magento\Catalog\Model\ResourceModel\Product $productResource;

    public function __construct(
        \MageSuite\PerformanceCategory\Model\Container\StockItemData $stockItemDataContainer,
        \Magento\Catalog\Model\ResourceModel\Product $productResource
    ) {
        $this->stockItemDataContainer = $stockItemDataContainer;
        $this->productResource = $productResource;
    }

    public function afterClean(\Magento\CatalogInventory\Model\Indexer\Stock\CacheCleaner $subject, $result, array $productIds)
    {
        $records = $this->productResource->getProductsSku($productIds);
        $skus = array_map(function ($record) {
            return $record['sku'];
        }, $records);

        $this->stockItemDataContainer->cleanProducts($skus);
    }
}
