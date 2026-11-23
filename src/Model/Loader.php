<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogAttributeLandingPageSitemap\Model;

use FeWeDev\Base\Variables;
use Infrangible\CatalogAttributeLandingPage\Model\AbstractLanding;
use Infrangible\CatalogAttributeLandingPage\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Infrangible\CatalogAttributeLandingPage\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Infrangible\Core\Helper\Stores;

/**
 * Loads the landing pages and groups of a store for the sitemaps.
 */
class Loader
{
    /** Type of the landing pages. */
    public const TYPE_PAGE = 'page';

    /** Type of the landing groups. */
    public const TYPE_GROUP = 'group';

    /**
     * @param PageCollectionFactory $pageCollectionFactory
     * @param GroupCollectionFactory $groupCollectionFactory
     * @param Stores $storeHelper
     * @param Variables $variables
     */
    public function __construct(
        protected PageCollectionFactory $pageCollectionFactory,
        protected GroupCollectionFactory $groupCollectionFactory,
        protected Stores $storeHelper,
        protected Variables $variables
    ) {
    }

    /**
     * Returns the active landing pages or groups of the store that are not excluded from the sitemap.
     *
     * Objects of all stores (store ID 0) belong to every store.
     *
     * @param string $type page or group
     * @param int $storeId
     *
     * @return AbstractLanding[]
     */
    public function getObjects(string $type, int $storeId): array
    {
        $collection = $type === self::TYPE_GROUP ? $this->groupCollectionFactory->create() :
            $this->pageCollectionFactory->create();

        $collection->addStoreFilter($storeId);
        $collection->addActiveFilter();
        $collection->addFieldToFilter('main_table.exclude_from_sitemap', ['eq' => 0]);

        return array_values(
            array_filter(
                $collection->getItems(),
                static fn(mixed $object): bool => $object instanceof AbstractLanding
            )
        );
    }

    /**
     * Returns the path of the URL of the landing page or group in the store.
     *
     * The URL key with the URL suffix of categories of that store; the model itself uses the suffix of the current
     * store.
     *
     * @param AbstractLanding $object
     * @param int $storeId
     *
     * @return string
     */
    public function getUrlPath(AbstractLanding $object, int $storeId): string
    {
        $suffix = $this->storeHelper->getStoreConfig('catalog/seo/category_url_suffix', null, false, $storeId);

        return $object->getUrlKey() . (is_string($suffix) ? $suffix : '');
    }

    /**
     * Returns the time of the last update of the landing page or group, or null if it is unknown.
     *
     * @param AbstractLanding $object
     *
     * @return string|null
     */
    public function getUpdatedAt(AbstractLanding $object): ?string
    {
        $updatedAt = $object->getData('updated_at');

        return $this->variables->isEmpty($updatedAt) ? null : $this->variables->stringValue($updatedAt);
    }
}
