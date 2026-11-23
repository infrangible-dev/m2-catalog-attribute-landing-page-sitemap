<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogAttributeLandingPageSitemap\Test\Unit\Model;

use FeWeDev\Base\Variables;
use Infrangible\CatalogAttributeLandingPage\Model\Group;
use Infrangible\CatalogAttributeLandingPage\Model\Page;
use Infrangible\CatalogAttributeLandingPage\Model\ResourceModel\Group\Collection as GroupCollection;
use Infrangible\CatalogAttributeLandingPage\Model\ResourceModel\Group\CollectionFactory as GroupCollectionFactory;
use Infrangible\CatalogAttributeLandingPage\Model\ResourceModel\Page\Collection as PageCollection;
use Infrangible\CatalogAttributeLandingPage\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use Infrangible\CatalogAttributeLandingPageSitemap\Model\Loader;
use Infrangible\Core\Helper\Stores;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests the loading of the landing pages and groups for the sitemaps.
 */
#[AllowMockObjectsWithoutExpectations]
class LoaderTest extends TestCase
{
    /**
     * Tests that the active pages of the store that are not excluded are loaded.
     *
     * @return void
     */
    public function testGetObjectsPages(): void
    {
        $page = $this->createStub(Page::class);

        $collection = $this->createMock(PageCollection::class);
        $collection->expects($this->once())->method('addStoreFilter')->with(2);
        $collection->expects($this->once())->method('addActiveFilter');
        $collection->expects($this->once())->method('addFieldToFilter')->with(
            'main_table.exclude_from_sitemap',
            ['eq' => 0]
        );
        $collection->method('getItems')->willReturn([5 => $page]);

        $pageCollectionFactory = $this->createStub(PageCollectionFactory::class);
        $pageCollectionFactory->method('create')->willReturn($collection);

        $groupCollectionFactory = $this->createMock(GroupCollectionFactory::class);
        $groupCollectionFactory->expects($this->never())->method('create');

        $loader = new Loader(
            $pageCollectionFactory,
            $groupCollectionFactory,
            $this->createStub(Stores::class),
            new Variables()
        );

        $this->assertSame([$page], $loader->getObjects(Loader::TYPE_PAGE, 2));
    }

    /**
     * Tests that groups are loaded from the collection of the groups.
     *
     * @return void
     */
    public function testGetObjectsGroups(): void
    {
        $group = $this->createStub(Group::class);

        $collection = $this->createStub(GroupCollection::class);
        $collection->method('getItems')->willReturn([$group]);

        $groupCollectionFactory = $this->createStub(GroupCollectionFactory::class);
        $groupCollectionFactory->method('create')->willReturn($collection);

        $loader = new Loader(
            $this->createStub(PageCollectionFactory::class),
            $groupCollectionFactory,
            $this->createStub(Stores::class),
            new Variables()
        );

        $this->assertSame([$group], $loader->getObjects(Loader::TYPE_GROUP, 1));
    }

    /**
     * Tests the URL path with the URL suffix of categories of the store of the sitemap, and the update time.
     *
     * @return void
     */
    public function testUrlPathAndUpdatedAt(): void
    {
        $storeHelper = $this->createMock(Stores::class);
        $storeHelper->expects($this->exactly(2))->method('getStoreConfig')
            ->with('catalog/seo/category_url_suffix', null, false, 3)
            ->willReturnOnConsecutiveCalls('.html', null);

        $page = $this->createStub(Page::class);
        $page->method('getUrlKey')->willReturn('red');
        $page->method('getData')->willReturnMap([['updated_at', null, '2026-10-01 12:00:00']]);

        $loader = new Loader(
            $this->createStub(PageCollectionFactory::class),
            $this->createStub(GroupCollectionFactory::class),
            $storeHelper,
            new Variables()
        );

        $this->assertSame('red.html', $loader->getUrlPath($page, 3));
        $this->assertSame('red', $loader->getUrlPath($page, 3));
        $this->assertSame('2026-10-01 12:00:00', $loader->getUpdatedAt($page));
        $this->assertNull($loader->getUpdatedAt($this->createStub(Group::class)));
    }
}
