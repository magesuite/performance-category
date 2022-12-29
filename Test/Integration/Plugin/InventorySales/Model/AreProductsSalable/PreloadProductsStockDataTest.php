<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Test\Integration\Plugin\InventorySales\Model\AreProductsSalable;

class PreloadProductsStockDataTest extends \PHPUnit\Framework\TestCase
{
    protected ?\MageSuite\PerformanceCategory\Model\Container\StockItemData $container = null;
    protected ?\Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData $getStockItemData = null;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->container = $objectManager->get(\MageSuite\PerformanceCategory\Model\Container\StockItemData::class);
        $this->getStockItemData = $objectManager->get(\Magento\InventoryIndexer\Model\ResourceModel\GetStockItemData::class);

        $data = [
            \Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE => 0,
            \Magento\InventorySalesApi\Model\GetStockItemDataInterface::QUANTITY => 65,
        ];

        $this->container->setData('simple-1', $data);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     */
    public function testContainerIsUsed(): void
    {
        $stockData = $this->getStockItemData->execute('simple', 1);

        $this->assertEquals(0, $stockData[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::IS_SALABLE]);
        $this->assertEquals(65, $stockData[\Magento\InventorySalesApi\Model\GetStockItemDataInterface::QUANTITY]);
    }
}
