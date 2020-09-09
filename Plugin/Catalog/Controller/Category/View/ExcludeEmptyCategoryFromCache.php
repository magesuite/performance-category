<?php
namespace MageSuite\PerformanceCategory\Plugin\Catalog\Controller\Category\View;

class ExcludeEmptyCategoryFromCache
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->coreRegistry = $coreRegistry;
    }

    public function afterExecute(
        \Magento\Catalog\Controller\Category\View $subject,
        $result
    ) {
        if (!$result instanceof \Magento\Framework\View\Result\Page) {
            return $result;
        }

        $productsList = $result->getLayout()->getBlock('category.products.list');
        $category = $this->getCategory();

        if (!$category instanceof \Magento\Catalog\Model\Category
            || $category->getDisplayMode() == \Magento\Catalog\Model\Category::DM_PAGE) {
            return $result;
        }

        if ($productsList instanceof \Magento\Catalog\Block\Product\ListProduct) {
            $productCollection = $productsList->getLoadedProductCollection();

            if ($productCollection->count() == 0) {
                $subject->getResponse()->setNoCacheHeaders();
            }
        }

        return $result;
    }

    protected function getCategory()
    {
        return $this->coreRegistry->registry('current_category');
    }
}
