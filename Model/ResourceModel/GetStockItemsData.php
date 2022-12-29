<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Model\ResourceModel;

/**
 * Version of core logic, altered in order to use multiple products at once
 *
 * @see \Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData
 */
class GetStockItemsData
{
    protected \MageSuite\PerformanceCategory\Model\ResourceModel\GetStockItemsData\FetchFromIndex $fetchFromIndex;
    protected \MageSuite\PerformanceCategory\Model\ResourceModel\GetStockItemsData\FetchFromInventory $fetchFromInventory;
    protected \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider;

    public function __construct(
        \MageSuite\PerformanceCategory\Model\ResourceModel\GetStockItemsData\FetchFromIndex $fetchFromIndex,
        \MageSuite\PerformanceCategory\Model\ResourceModel\GetStockItemsData\FetchFromInventory $fetchFromInventory,
        \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider,
    ) {
        $this->fetchFromIndex = $fetchFromIndex;
        $this->fetchFromInventory = $fetchFromInventory;
        $this->defaultStockProvider = $defaultStockProvider;
    }

    /**
     * @throws \Exception
     */
    public function execute(array $skus, int $stockId): array
    {
        if ($this->defaultStockProvider->getId() === $stockId) {
            return $this->fetchFromInventory->execute($skus, $stockId);
        } else {
            return $this->fetchFromIndex->execute($skus, $stockId);
        }
    }
}
