<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogAttributeLandingPageSitemap\Test\Unit\Model\ItemProvider;

use FeWeDev\Base\Variables;
use Infrangible\CatalogAttributeLandingPage\Model\Group as LandingGroup;
use Infrangible\CatalogAttributeLandingPageSitemap\Model\ItemProvider\Group;
use Infrangible\CatalogAttributeLandingPageSitemap\Model\Loader;
use Infrangible\Core\Helper\Stores;
use Magento\Sitemap\Model\SitemapItemInterface;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests the item provider of the landing groups for the sitemap of Magento.
 */
#[AllowMockObjectsWithoutExpectations]
class GroupTest extends TestCase
{
    /**
     * Tests that no groups are loaded if they are not exported.
     *
     * @return void
     */
    public function testGetItemsNotExported(): void
    {
        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('getStoreConfigFlag')->willReturn(false);

        $loader = $this->createMock(Loader::class);
        $loader->expects($this->never())->method('getObjects');

        $provider = new Group(
            $loader,
            $storeHelper,
            $this->createStub(SitemapItemInterfaceFactory::class),
            new Variables()
        );

        $this->assertSame([], $provider->getItems(1));
    }

    /**
     * Tests the items of the groups: the store ID of Magento's sitemap can be a text.
     *
     * @return void
     */
    public function testGetItems(): void
    {
        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('getStoreConfigFlag')->willReturn(true);
        $storeHelper->method('getStoreConfig')->willReturnMap(
            [
                ['sitemap/catalog_attribute_landing_page/group/changeFrequency', 'yearly', false, 2, 'monthly'],
                ['sitemap/catalog_attribute_landing_page/group/priority', '0.5', false, 2, '1.0']
            ]
        );

        $group = $this->createStub(LandingGroup::class);

        $loader = $this->createMock(Loader::class);
        $loader->expects($this->once())->method('getObjects')->with(Loader::TYPE_GROUP, 2)->willReturn([$group]);
        $loader->method('getUrlPath')->willReturn('colors.html');
        $loader->method('getUpdatedAt')->willReturn('2026-09-15 00:00:00');

        $item = $this->createStub(SitemapItemInterface::class);

        $itemFactory = $this->createMock(SitemapItemInterfaceFactory::class);
        // the arguments of the constructor of Magento's sitemap item
        $itemFactory->expects($this->once())->method('create')->with(
            [
                'url'             => 'colors.html',
                'updatedAt'       => '2026-09-15 00:00:00',
                'changeFrequency' => 'monthly',
                'priority'        => '1.0'
            ]
        )->willReturn($item);

        $provider = new Group($loader, $storeHelper, $itemFactory, new Variables());

        $this->assertSame([$item], $provider->getItems('2'));
    }
}
