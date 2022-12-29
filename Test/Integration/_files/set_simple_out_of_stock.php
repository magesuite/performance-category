<?php

declare(strict_types=1);

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);

$product = $productRepository->get('simple');
$extensionAttributes = $product->getExtensionAttributes();
$stockItem = $extensionAttributes->getStockItem();
$stockItem->setIsInStock(false);
$productRepository->save($product);
