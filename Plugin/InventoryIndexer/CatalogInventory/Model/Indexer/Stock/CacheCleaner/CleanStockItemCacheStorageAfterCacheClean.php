<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Plugin\InventoryIndexer\CatalogInventory\Model\Indexer\Stock\CacheCleaner;

class CleanStockItemCacheStorageAfterCacheClean
{
    protected \Magento\InventoryIndexer\Model\GetStockItemData\CacheStorage $cacheStorage;
    protected \Magento\Catalog\Model\ResourceModel\Product $productResource;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\InventoryIndexer\Model\GetStockItemData\CacheStorage $cacheStorage
    ) {
        $this->productResource = $productResource;
        $this->cacheStorage = $cacheStorage;
    }

    public function afterClean(\Magento\CatalogInventory\Model\Indexer\Stock\CacheCleaner $subject, $result, array $productIds)
    {
        $records = $this->productResource->getProductsSku($productIds);
        $skus = array_map(function ($record) {
            return $record['sku'];
        }, $records);

        foreach ($skus as $sku) {
            $this->cacheStorage->delete(\Magento\CatalogInventory\Model\Stock::DEFAULT_STOCK_ID, $sku);
        }
    }
}
