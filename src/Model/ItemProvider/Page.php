<?php

declare(strict_types=1);

namespace Infrangible\CatalogAttributeLandingPageSitemap\Model\ItemProvider;

use Infrangible\CatalogAttributeLandingPage\Model\ResourceModel\Page\CollectionFactory;
use Infrangible\Core\Helper\Stores;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Page implements ItemProviderInterface
{
    /** @var Stores */
    protected $storeHelper;

    /** @var CollectionFactory */
    protected $pageCollectionFactory;

    /** @var SitemapItemInterfaceFactory */
    protected $itemFactory;

    public function __construct(
        Stores $storeHelper,
        CollectionFactory $pageCollectionFactory,
        SitemapItemInterfaceFactory $itemFactory
    ) {
        $this->storeHelper = $storeHelper;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->itemFactory = $itemFactory;
    }

    public function getItems($storeId): array
    {
        $storeId = (int)$storeId;

        $export = $this->storeHelper->getStoreConfigFlag(
            'sitemap/catalog_attribute_landing_page/page/export',
            true,
            $storeId
        );

        if (! $export) {
            return [];
        }

        $collection = $this->pageCollectionFactory->create();

        $collection->addStoreFilter($storeId);
        $collection->addActiveFilter();

        $changeFrequency = $this->storeHelper->getStoreConfig(
            'sitemap/catalog_attribute_landing_page/page/changeFrequency',
            'yearly',
            false,
            $storeId
        );
        $priority = $this->storeHelper->getStoreConfig(
            'sitemap/catalog_attribute_landing_page/page/priority',
            '0.5',
            false,
            $storeId
        );

        $items = [];

        /** @var \Infrangible\CatalogAttributeLandingPage\Model\Page $page */
        foreach ($collection as $page) {
            if ($page->getData('exclude_from_sitemap')) {
                continue;
            }

            $items[] = $this->itemFactory->create(
                [
                    'url'             => $page->getUrlPath(),
                    'updatedAt'       => $page->getUpdatedAt(),
                    'changeFrequency' => $changeFrequency,
                    'priority'        => $priority
                ]
            );
        }

        return $items;
    }
}
