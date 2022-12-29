<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Test\Integration\Plugin\InventoryIndexer\Model\ResourceModel\GetStockItemData;

class GetDataFromContainerTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\InventorySales\Model\AreProductsSalable $areProductsSalable = null;
    protected ?\MageSuite\PerformanceCategory\Model\Container\StockItemData $container = null;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->areProductsSalable = $objectManager->get(\Magento\InventorySales\Model\AreProductsSalable::class);
        $this->container = $objectManager->get(\MageSuite\PerformanceCategory\Model\Container\StockItemData::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testContainerIsUsed(): void
    {
        $stockData = $this->areProductsSalable->execute(['simple'], 1);

        /** @var \Magento\InventorySalesApi\Api\Data\IsProductSalableResultInterface $resultStockItem */
        $resultStockItem = current($stockData);
        $containerStockItem = $this->container->getProductStockItem('simple', 1);

        $this->assertEquals($resultStockItem->isSalable(), $containerStockItem[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE]);
    }
}
