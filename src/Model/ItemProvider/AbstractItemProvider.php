<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogAttributeLandingPageSitemap\Model\ItemProvider;

use FeWeDev\Base\Variables;
use Infrangible\CatalogAttributeLandingPage\Model\AbstractLanding;
use Infrangible\CatalogAttributeLandingPageSitemap\Model\Loader;
use Infrangible\Core\Helper\Stores;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemInterface;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;

/**
 * Provides the landing pages or groups for the sitemap of Magento, configured in
 * sitemap/catalog_attribute_landing_page/<type>.
 */
abstract class AbstractItemProvider implements ItemProviderInterface
{
    /**
     * @param Loader $loader
     * @param Stores $storeHelper
     * @param SitemapItemInterfaceFactory $itemFactory
     * @param Variables $variables
     */
    public function __construct(
        protected Loader $loader,
        protected Stores $storeHelper,
        protected SitemapItemInterfaceFactory $itemFactory,
        protected Variables $variables
    ) {
    }

    /**
     * Returns the type of the provider: page or group.
     *
     * @return string
     */
    abstract protected function getType(): string;

    /**
     * Returns the sitemap items of the landing pages or groups of the store, none if they are not exported.
     *
     * @param int|string $storeId
     *
     * @return SitemapItemInterface[]
     */
    public function getItems($storeId): array
    {
        // Magento passes the store ID of the sitemap, which can be a text
        $storeId = $this->variables->intValue($storeId);
        $configPath = sprintf('sitemap/catalog_attribute_landing_page/%s', $this->getType());

        if (! $this->storeHelper->getStoreConfigFlag($configPath . '/export', true, $storeId)) {
            return [];
        }

        $changeFrequency = $this->storeHelper->getStoreConfig(
            $configPath . '/changeFrequency',
            'yearly',
            false,
            $storeId
        );
        $priority = $this->storeHelper->getStoreConfig($configPath . '/priority', '0.5', false, $storeId);

        return array_map(
            fn(AbstractLanding $object): SitemapItemInterface => $this->itemFactory->create(
                [
                    'url'             => $this->loader->getUrlPath($object, $storeId),
                    'updatedAt'       => $this->loader->getUpdatedAt($object),
                    'changeFrequency' => $changeFrequency,
                    'priority'        => $priority
                ]
            ),
            $this->loader->getObjects($this->getType(), $storeId)
        );
    }
}
