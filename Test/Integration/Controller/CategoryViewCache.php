<?php

declare(strict_types=1);

namespace Integration\Controller;

class CategoryViewCache extends \Magento\TestFramework\TestCase\AbstractController
{
    protected ?\Magento\Framework\App\Cache\Manager $cacheManager = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->cacheManager = $this->_objectManager->create(\Magento\Framework\App\Cache\Manager::class);
    }

    /**
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/categories_with_product_ids.php
     * @magentoCache full_page enabled
     * @magentoConfigFixture system/full_page_cache/caching_application 2
     * @return void
     */
    public function testCategoryPageCacheableWhenVarnishFpcIsEnabled(): void
    {
        $this->cacheManager->clean(['full_page', 'layout']);
        $this->getRequest()->setParam('id', 5);
        $this->dispatch('catalog/category/view');
        $this->assertHeaderPcre('Pragma', '/^cache/i');
    }

    /**
     * @magentoDataFixture Magento/CatalogUrlRewrite/_files/categories_with_product_ids.php
     * @magentoCache full_page enabled
     * @magentoConfigFixture system/full_page_cache/caching_application 1
     * @return void
     */
    public function testCategoryPageCacheableForNonVarnishFpcNoCached(): void
    {
        $this->cacheManager->clean(['full_page', 'layout']);
        $this->getRequest()->setParam('id', 5);
        $this->dispatch('catalog/category/view');
        $this->assertHeaderPcre('Pragma', '/^no-cache/i');

        $this->getRequest()->setParam('id', 1);
        $this->dispatch('catalog/product/view');

        $this->getRequest()->setParam('id', 5);
        $this->dispatch('catalog/category/view');
        $this->assertHeaderPcre('Pragma', '/^cache/i');
    }

}
