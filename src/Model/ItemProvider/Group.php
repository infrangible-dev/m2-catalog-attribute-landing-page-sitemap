<?php

declare(strict_types=1);

namespace Infrangible\CatalogAttributeLandingPageSitemap\Model\ItemProvider;

use Infrangible\CatalogAttributeLandingPage\Model\ResourceModel\Group\CollectionFactory;
use Infrangible\Core\Helper\Stores;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Group implements ItemProviderInterface
{
    /** @var Stores */
    protected $storeHelper;

    /** @var CollectionFactory */
    protected $groupCollectionFactory;

    /** @var SitemapItemInterfaceFactory */
    protected $itemFactory;

    public function __construct(
        Stores $storeHelper,
        CollectionFactory $groupCollectionFactory,
        SitemapItemInterfaceFactory $itemFactory
    ) {
        $this->storeHelper = $storeHelper;
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->itemFactory = $itemFactory;
    }

    public function getItems($storeId): array
    {
        $storeId = (int)$storeId;

        $export = $this->storeHelper->getStoreConfigFlag(
            'sitemap/catalog_attribute_landing_page/group/export',
            true,
            $storeId
        );

        if (! $export) {
            return [];
        }

        $collection = $this->groupCollectionFactory->create();

        $collection->addStoreFilter($storeId);
        $collection->addActiveFilter();

        $changeFrequency = $this->storeHelper->getStoreConfig(
            'sitemap/catalog_attribute_landing_page/group/changeFrequency',
            'yearly',
            false,
            $storeId
        );
        $priority = $this->storeHelper->getStoreConfig(
            'sitemap/catalog_attribute_landing_page/group/priority',
            '0.5',
            false,
            $storeId
        );

        $items = [];

        /** @var \Infrangible\CatalogAttributeLandingPage\Model\Group $group */
        foreach ($collection as $group) {
            if ($group->getData('exclude_from_sitemap')) {
                continue;
            }

            $items[] = $this->itemFactory->create(
                [
                    'url'             => $group->getUrlPath(),
                    'updatedAt'       => $group->getUpdatedAt(),
                    'changeFrequency' => $changeFrequency,
                    'priority'        => $priority
                ]
            );
        }

        return $items;
    }
}
