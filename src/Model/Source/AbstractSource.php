<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogAttributeLandingPageSitemap\Model\Source;

use Infrangible\CatalogAttributeLandingPage\Model\AbstractLanding;
use Infrangible\CatalogAttributeLandingPageSitemap\Model\Loader;
use Infrangible\Core\Helper\Stores;
use Infrangible\Sitemap\Helper\Data;
use Infrangible\Sitemap\Model\ISitemapUrl;
use Infrangible\Sitemap\Model\SitemapUrlFactory;
use Infrangible\Sitemap\Model\Source\ISource;

/**
 * Source of the landing pages or groups for the sitemap of Infrangible Sitemap, configured in
 * infrangible_sitemap/catalog_attribute_landing_page/<type>.
 */
abstract class AbstractSource implements ISource
{
    /**
     * @param Loader $loader
     * @param Stores $storeHelper
     * @param Data $sitemapHelper
     * @param SitemapUrlFactory $sitemapUrlFactory
     */
    public function __construct(
        protected Loader $loader,
        protected Stores $storeHelper,
        protected Data $sitemapHelper,
        protected SitemapUrlFactory $sitemapUrlFactory
    ) {
    }

    /**
     * Returns the type of the source: page or group.
     *
     * @return string
     */
    abstract protected function getType(): string;

    /**
     * Returns the landing pages or groups of the store, none if they are not exported.
     *
     * @param int $storeId
     *
     * @return AbstractLanding[]
     */
    public function getStoreData(int $storeId): array
    {
        $export = $this->storeHelper->getStoreConfigFlag(
            sprintf('infrangible_sitemap/%s/export', $this->getConfigGroup()),
            true,
            $storeId
        );

        return $export ? $this->loader->getObjects($this->getType(), $storeId) : [];
    }

    /**
     * Transforms the landing pages or groups into sitemap URLs of the store.
     *
     * @param int $storeId
     * @param array $storeData
     *
     * @phpstan-param array<int|string, mixed> $storeData
     *
     * @return ISitemapUrl[]
     */
    public function transformStoreData(int $storeId, array $storeData): array
    {
        $changeFrequency = $this->sitemapHelper->getChangeFrequency($this->getConfigGroup(), $storeId, 'yearly');
        $priority = $this->sitemapHelper->getPriority($this->getConfigGroup(), $storeId, '0.5');

        $urls = [];

        foreach ($storeData as $object) {
            if (! $object instanceof AbstractLanding) {
                continue;
            }

            $sitemapUrl = $this->sitemapUrlFactory->create();

            $sitemapUrl->setUrl($this->sitemapHelper->getUrl($this->loader->getUrlPath($object, $storeId), $storeId));
            $sitemapUrl->setLastModified($this->sitemapHelper->getLastModified($this->loader->getUpdatedAt($object)));
            $sitemapUrl->setChangeFrequency($changeFrequency);
            $sitemapUrl->setPriority($priority);

            if ($sitemapUrl->isValid()) {
                $urls[] = $sitemapUrl;
            }
        }

        return $urls;
    }

    /**
     * Returns the group of the configuration of the source.
     *
     * @return string
     */
    protected function getConfigGroup(): string
    {
        return sprintf('catalog_attribute_landing_page/%s', $this->getType());
    }
}
