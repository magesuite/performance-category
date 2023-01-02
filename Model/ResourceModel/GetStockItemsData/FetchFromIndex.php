<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Model\ResourceModel\GetStockItemsData;

/**
 * Version of core logic, altered in order to use multiple products at once
 *
 * @see \Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData
 */
class FetchFromIndex
{
    protected \Magento\Framework\App\ResourceConnection $resource;
    protected \Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface $stockIndexTableNameResolver;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface $stockIndexTableNameResolver
    ) {
        $this->resource = $resource;
        $this->stockIndexTableNameResolver = $stockIndexTableNameResolver;
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
    public function execute(array $skus, int $stockId): array
    {
        $connection = $this->resource->getConnection();
        $select = $connection->select();

        $select->from(
            $this->stockIndexTableNameResolver->execute($stockId),
            [
                \Magento\InventoryIndexer\Indexer\IndexStructure::SKU,
                \Magento\InventorySalesApi\Model\GetStockItemDataInterface::QUANTITY => \Magento\InventoryIndexer\Indexer\IndexStructure::QUANTITY,
                \Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE => \Magento\InventoryIndexer\Indexer\IndexStructure::IS_SALABLE,
            ]
        )->where(
            sprintf('%s IN (?)', \Magento\InventoryIndexer\Indexer\IndexStructure::SKU),
            $skus
        );

        $stockItems = $connection->fetchAll($select);

        return $this->prepareResult($stockItems, $stockId);
    }

    protected function prepareResult(array $stockItems, int $stockId): array
    {
        $result = [];

        foreach ($stockItems as $stockItem) {
            $sku = $stockItem['sku'];

            $result[$sku] = [
                \Magento\InventorySalesApi\Model\GetStockItemDataInterface::QUANTITY => $stockItem[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::QUANTITY],
                \Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE => $stockItem[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE],
            ];
        }

        return $result;
    }
}
