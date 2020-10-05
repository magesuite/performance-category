<?php
namespace MageSuite\PerformanceCategory\Test\Integration\Plugin\Catalog\Controller\Category\View;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ExcludeEmptyCategoryFromCacheTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/category.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testEmptyCategoryReturnNoCacheHeaders()
    {
        $this->getRequest()->setMethod(\Magento\Framework\App\Request\Http::METHOD_GET);
        $this->getRequest()->setParam('id', 333);
        $this->dispatch('/catalog/category/view');
        $this->assertHeaderPcre('Pragma', '/no-cache/i');
        $this->assertHeaderPcre('Cache-Control', '/(max-age=0|must-revalidate|no-cache|no-store)/i');
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/category_product.php
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testNotEmptyCategoryReturnNoCacheHeaders()
    {
        $this->getRequest()->setMethod(\Magento\Framework\App\Request\Http::METHOD_GET);
        $this->getRequest()->setParam('id', 333);
        $this->dispatch('/catalog/category/view');
        $this->assertHeaderPcre('Pragma', '/cache/i');
        $this->assertHeaderPcre('Cache-Control', '/(max-age|public)/i');
    }
}
