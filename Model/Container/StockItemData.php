<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Model\Container;

class StockItemData extends \Magento\Framework\DataObject
{
    /**
     * Use sprintf for generating array key. Parameters: sku, stock_id.
     */
    public const ARRAY_KEY_PATTERN = '%s-%s';

    protected \MageSuite\PerformanceCategory\Model\ResourceModel\GetStockItemsData $getStockItemsData;

    public function __construct(
        \MageSuite\PerformanceCategory\Model\ResourceModel\GetStockItemsData $getStockItemsData,
        array $data = []
    ) {
        $this->getStockItemsData = $getStockItemsData;

        parent::__construct($data);
    }

    /**
     * @throws \Exception
     */
    public function initProducts(array $skus, int $stockId): void
    {
        $stockItems = $this->getStockItemsData->execute($skus, $stockId);
        $data = $this->updateKeys($stockItems, $stockId);

        $this->addData($data);
    }

    public function getProductStockItem(string $sku, int $stockId): ?array
    {
        $key = $this->getKey($sku, $stockId);

        return $this->_getData($key);
    }

    public function getKey(string $sku, int $stockId): string
    {
        return sprintf(self::ARRAY_KEY_PATTERN, $sku, $stockId);
    }

    protected function updateKeys(array $stockItems, int $stockId): array
    {
        $result = [];

        foreach ($stockItems as $sku => $stockItem) {
            $key = $this->getKey((string)$sku, $stockId);

            $result[$key] = $stockItem;
        }

        return $result;
    }
}
