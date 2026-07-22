<?php
declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Plugin\Magento\CatalogInventory\Model\ResourceModel\Stock\Status;

class AddQuantityColumnToSelect
{
    protected \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite;
    protected \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider;

    public function __construct(
        \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider
    ) {
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->defaultStockProvider = $defaultStockProvider;
    }

    public function afterAddStockDataToCollection(
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $subject,
        $result,
        $collection,
        $isFilterInStock
    ): \Magento\Catalog\Model\ResourceModel\Product\Collection {
        $stockId = $this->getStockIdForCurrentWebsite->execute();

        if ($stockId === $this->defaultStockProvider->getId()) {
            $result->getSelect()->columns(['quantity' => 'stock_status_index.qty']);
        } else {
            $result->getSelect()->columns(['quantity' => 'stock_status_index.quantity']);
        }

        return $result;
    }
}
