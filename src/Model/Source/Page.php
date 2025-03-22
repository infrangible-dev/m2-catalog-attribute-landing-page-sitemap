<?php

declare(strict_types=1);

namespace Infrangible\CatalogAttributeLandingPageSitemap\Model\Source;

use Infrangible\CatalogAttributeLandingPage\Model\ResourceModel\Page\CollectionFactory;
use Infrangible\Core\Helper\Stores;
use Infrangible\Sitemap\Model\ISitemapUrl;
use Infrangible\Sitemap\Model\SitemapUrlFactory;
use Infrangible\Sitemap\Model\Source\ISource;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Page implements ISource
{
    /** @var Stores */
    protected $storeHelper;

    /** @var CollectionFactory */
    protected $pageCollectionFactory;

    /** @var SitemapUrlFactory */
    protected $sitemapUrlFactory;

    public function __construct(
        CollectionFactory $pageCollectionFactory,
        SitemapUrlFactory $sitemapUrlFactory,
        Stores $storeHelper
    ) {
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->sitemapUrlFactory = $sitemapUrlFactory;
        $this->storeHelper = $storeHelper;
    }

    public function getStoreData(int $storeId): array
    {
        $export = $this->storeHelper->getStoreConfigFlag(
            'infrangible_sitemap/catalog_attribute_landing_page/page/export',
            true,
            $storeId
        );

        if (! $export) {
            return [];
        }

        $collection = $this->pageCollectionFactory->create();

        $collection->addStoreFilter($storeId);
        $collection->addActiveFilter();

        $items = [];

        foreach ($collection->getItems() as $page) {
            if ($page->getData('exclude_from_sitemap')) {
                continue;
            }

            $items[] = $page;
        }

        return $items;
    }

    /**
     * @param \Infrangible\CatalogAttributeLandingPage\Model\Page[] $storeData
     *
     * @return ISitemapUrl[]
     */
    public function transformStoreData(int $storeId, array $storeData): array
    {
        $changeFrequency = $this->storeHelper->getStoreConfig(
            'infrangible_sitemap/catalog_attribute_landing_page/page/changeFrequency',
            'yearly',
            false,
            $storeId
        );
        $priority = $this->storeHelper->getStoreConfig(
            'infrangible_sitemap/catalog_attribute_landing_page/page/priority',
            '0.5',
            false,
            $storeId
        );

        $urls = [];

        foreach ($storeData as $attributeLandingPage) {
            $sitemapUrl = $this->sitemapUrlFactory->create();

            $sitemapUrl->setUrl($attributeLandingPage->getUrl());
            $sitemapUrl->setLastModified(
                date(
                    'Y-m-d',
                    strtotime($attributeLandingPage->getUpdatedAt())
                )
            );
            $sitemapUrl->setChangeFrequency($changeFrequency);
            $sitemapUrl->setPriority($priority);

            $urls[] = $sitemapUrl;
        }

        return $urls;
    }
}
