<?php

declare(strict_types=1);

namespace Infrangible\CatalogAttributeLandingPageSitemap\Model\Source;

use Infrangible\CatalogAttributeLandingPage\Model\ResourceModel\Group\CollectionFactory;
use Infrangible\Core\Helper\Stores;
use Infrangible\Sitemap\Model\ISitemapUrl;
use Infrangible\Sitemap\Model\SitemapUrlFactory;
use Infrangible\Sitemap\Model\Source\ISource;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Group implements ISource
{
    /** @var Stores */
    protected $storeHelper;

    /** @var CollectionFactory */
    protected $groupCollectionFactory;

    /** @var SitemapUrlFactory */
    protected $sitemapUrlFactory;

    public function __construct(
        CollectionFactory $groupCollectionFactory,
        SitemapUrlFactory $sitemapUrlFactory,
        Stores $storeHelper
    ) {
        $this->groupCollectionFactory = $groupCollectionFactory;
        $this->sitemapUrlFactory = $sitemapUrlFactory;
        $this->storeHelper = $storeHelper;
    }

    public function getStoreData(int $storeId): array
    {
        $export = $this->storeHelper->getStoreConfigFlag(
            'infrangible_sitemap/catalog_attribute_landing_page/group/export',
            true,
            $storeId
        );

        if (! $export) {
            return [];
        }

        $collection = $this->groupCollectionFactory->create();

        $collection->addStoreFilter($storeId);
        $collection->addActiveFilter();

        $items = [];

        foreach ($collection->getItems() as $group) {
            if ($group->getData('exclude_from_sitemap')) {
                continue;
            }

            $items[] = $group;
        }

        return $items;
    }

    /**
     * @param \Infrangible\CatalogAttributeLandingPage\Model\Group[] $storeData
     *
     * @return ISitemapUrl[]
     */
    public function transformStoreData(int $storeId, array $storeData): array
    {
        $changeFrequency = $this->storeHelper->getStoreConfig(
            'infrangible_sitemap/catalog_attribute_landing_page/group/changeFrequency',
            'yearly',
            false,
            $storeId
        );
        $priority = $this->storeHelper->getStoreConfig(
            'infrangible_sitemap/catalog_attribute_landing_page/group/priority',
            '0.5',
            false,
            $storeId
        );

        $urls = [];

        foreach ($storeData as $attributeLandingPagesGroup) {
            $sitemapUrl = $this->sitemapUrlFactory->create();

            $sitemapUrl->setUrl($attributeLandingPagesGroup->getUrl());
            $sitemapUrl->setLastModified(
                date(
                    'Y-m-d',
                    strtotime($attributeLandingPagesGroup->getUpdatedAt())
                )
            );
            $sitemapUrl->setChangeFrequency($changeFrequency);
            $sitemapUrl->setPriority($priority);

            $urls[] = $sitemapUrl;
        }

        return $urls;
    }
}
