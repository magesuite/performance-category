<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Test\Integration\Model\Container;

class StockItemDataTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData $getStockItemsData = null;
    protected ?\Magento\InventorySales\Model\AreProductsSalable $areProductsSalable = null;
    protected ?\MageSuite\PerformanceCategory\Model\Container\StockItemData $container = null;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->areProductsSalable = $objectManager->get(\Magento\InventorySales\Model\AreProductsSalable::class);
        $this->container = $objectManager->get(\MageSuite\PerformanceCategory\Model\Container\StockItemData::class);
        $this->getStockItemsData = $objectManager->get(\Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData::class);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/products.php
     */
    public function testCompareLoadingStockItemForInStockProduct(): void
    {
        $this->container->initProducts(['simple'], 1);

        $containerData = $this->container->getProductStockItem('simple', 1);

        $this->assertEquals(1, $containerData[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE]);
        $this->assertEquals(22, $containerData[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::QUANTITY]);

        $originalData = $this->getStockItemsData->execute('simple', 1);

        $this->assertEquals($originalData, $containerData);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/products.php
     * @magentoDataFixture MageSuite_PerformanceCategory::Test/Integration/_files/set_simple_out_of_stock.php
     */
    public function testCompareLoadingStockItemForOutOfStockProduct(): void
    {
        $this->container->initProducts(['simple'], 1);

        $containerData = $this->container->getProductStockItem('simple', 1);

        $this->assertEquals(0, $containerData[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE]);
        $this->assertEquals(22, $containerData[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::QUANTITY]);

        $originalData = $this->getStockItemsData->execute('simple', 1);

        $this->assertEquals($originalData, $containerData);
    }
}
