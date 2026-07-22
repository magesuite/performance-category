<?php

declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Plugin\Magento\CatalogInventory\Model\Quote\Item\QuantityValidator;

class PreloadStockForQuote
{
    protected $isCalculatedAlready = false;

    public function __construct(
        protected \MageSuite\PerformanceCategory\Model\Container\StockItemData $stockItemContainer,
        protected \Magento\InventorySales\Model\StockResolver $stockResolver,
    ) {}

    public function beforeValidate(
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator $subject,
        \Magento\Framework\Event\Observer $observer
    ): array
    {
        $quoteItem = $observer->getEvent()->getItem();

        if (!$quoteItem?->getQuote() || $this->isCalculatedAlready) {
            return [$observer];
        }

        $this->isCalculatedAlready = true;
        $quote = $quoteItem->getQuote();
        $skus = [];

        foreach ($quote->getItemsCollection() as $item) {
            $skus[] = $item->getSku();
        }

        $websiteCode = $quote->getStore()->getWebsite()->getCode();
        $stock = $this->stockResolver->execute(\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
        $stockId = (int) $stock->getStockId();
        $this->stockItemContainer->initProducts($skus, $stockId);

        return [$observer];
    }
}
