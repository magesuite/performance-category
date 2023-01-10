<?php
declare(strict_types=1);

namespace MageSuite\PerformanceCategory\Plugin\Smile\ElasticsuiteVirtualCategory\Model\VirtualCategory\Root;

class CacheVirtualCategoryRoot
{
    protected $instancesById = [];

    public function aroundGetVirtualCategoryRoot(
        \Smile\ElasticsuiteVirtualCategory\Model\VirtualCategory\Root $subject,
        callable $proceed,
        \Magento\Catalog\Api\Data\CategoryInterface $category
    ): ?\Magento\Catalog\Api\Data\CategoryInterface {
        if (!array_key_exists($category->getId(), $this->instancesById)) {
            $this->instancesById[$category->getId()] = $proceed($category);
        }

        return $this->instancesById[$category->getId()];
    }
}
