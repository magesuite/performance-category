<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Model\ResourceModel\GetStockItemsData;

/**
 * Version of core logic, altered in order to use multiple products at once
 *
 * @see \Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData
 */
class FetchFromInventory
{
    protected \Magento\Framework\App\ResourceConnection $resource;
    protected \Magento\InventoryCatalog\Model\GetProductIdsBySkus $getProductIdsBySkus;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\InventoryCatalog\Model\GetProductIdsBySkus $getProductIdsBySkus
    ) {
        $this->resource = $resource;
        $this->getProductIdsBySkus = $getProductIdsBySkus;
    }

    /**
     * @return array
     * [
     *   (string) sku => [
     *     'quantity' => float,
     *     'is_salable => int,
     *   ],
     * ]
     */
    public function execute(array $skus): array
    {
        try {
            $productIdsBySkus = $this->getProductIdsBySkus->execute($skus);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return [];
        }

        $productIds = array_values($productIdsBySkus);

        $allStockItems = $this->getStockItems($productIds);

        $foundProductIds = array_column($allStockItems, 'product_id');
        $missingProductIds = array_diff($productIds, $foundProductIds);

        $skusByProductIds = array_flip($productIdsBySkus);

        if (empty($missingProductIds)) {
            return $this->prepareResult($skusByProductIds, $allStockItems);
        }

        $legacyStockItems = $this->getLegacyStockItems($missingProductIds);
        $allStockItems = array_merge($allStockItems, $legacyStockItems);

        return $this->prepareResult($skusByProductIds, $allStockItems);
    }

    /**
     * Fallback to the legacy cataloginventory_stock_item table. Caused by data absence in legacy
     * cataloginventory_stock_status table for disabled products assigned to the default stock.
     *
     * @see \Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData::getStockItemDataFromStockItemTable
     */
    protected function getStockItems(array $productIds): array
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select();

        $select->from(
            $this->resource->getTableName('cataloginventory_stock_status'),
            [
                'product_id',
                \Magento\InventorySalesApi\Model\GetStockItemDataInterface::QUANTITY => 'qty',
                \Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE => 'stock_status',
            ]
        )->where(
            'product_id IN (?)',
            $productIds
        );

        return $connection->fetchAll($select);
    }

    /**
     * @see \Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData::getStockItemDataFromStockItemTable
     */
    protected function getLegacyStockItems(array $missingProductIds): array
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select();

        $select->from(
            $this->resource->getTableName('cataloginventory_stock_item'),
            [
                \Magento\InventorySalesApi\Model\GetStockItemDataInterface::QUANTITY => 'qty',
                \Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE => 'is_in_stock',
                'product_id',
            ]
        )->where(
            'product_id IN (?)',
            $missingProductIds
        );

        return $connection->fetchAll($select);
    }

    protected function prepareResult(array $skusByProductIds, array $stockItems): array
    {
        $result = [];

        foreach ($stockItems as $stockItem) {
            $sku = $skusByProductIds[$stockItem['product_id']];

            $result[$sku] = [
                \Magento\InventorySalesApi\Model\GetStockItemDataInterface::QUANTITY => $stockItem[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::QUANTITY],
                \Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE => $stockItem[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE],
            ];
        }

        return $result;
    }
}
