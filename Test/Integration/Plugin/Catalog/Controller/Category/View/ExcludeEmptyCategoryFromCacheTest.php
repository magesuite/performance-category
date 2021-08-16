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

    /**
     * Method overwritten to get rid of deprecated assertRegExp method in PHPUnit 10
     * @param string $headerName
     * @param string $valueRegex
     */
    public function assertHeaderPcre($headerName, $valueRegex)
    {
        $headerFound = false;
        $headers = $this->getResponse()->getHeaders();
        foreach ($headers as $header) {
            if ($header->getFieldName() === $headerName) {
                $headerFound = true;

                $assertMethod = method_exists($this, 'assertMatchesRegularExpression') ?  'assertMatchesRegularExpression' : 'assertRegExp';
                $this->$assertMethod($valueRegex, $header->getFieldValue());
            }
        }
        if (!$headerFound) {
            $this->fail("Header '{$headerName}' was not found. Headers dump:\n" . var_export($headers, 1));
        }
    }
}
