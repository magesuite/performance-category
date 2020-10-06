<?php

namespace MageSuite\PerformanceCategory\Observer;

class ExcludeEmptyCategoryFromCache implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\View\Layout
     */
    protected $layout;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Framework\View\Layout $layout,
        \Magento\Framework\Registry $registry
    )
    {
        $this->layout = $layout;
        $this->registry = $registry;
    }

    /**
     * @inheritDoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $response = $observer->getData('response');
        $actionName = $observer->getData('request')->getFullActionName();

        if($actionName != 'catalog_category_view') {
            return;
        }

        $category = $this->getCategory();

        if (!$category instanceof \Magento\Catalog\Model\Category
            || $category->getDisplayMode() == \Magento\Catalog\Model\Category::DM_PAGE) {
            return;
        }

        $productsList = $this->layout->getBlock('category.products.list');

        if ($productsList instanceof \Magento\Catalog\Block\Product\ListProduct) {
            $productCollection = $productsList->getLoadedProductCollection();

            if ($productCollection->count() == 0) {
                $response->setNoCacheHeaders();
            }
        }

        return;
    }

    protected function getCategory()
    {
        return $this->registry->registry('current_category');
    }

}
