<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogAttributeLandingPageSitemap\Test\Unit\Model\Source;

use Infrangible\CatalogAttributeLandingPage\Model\Page as LandingPage;
use Infrangible\CatalogAttributeLandingPageSitemap\Model\Loader;
use Infrangible\CatalogAttributeLandingPageSitemap\Model\Source\Page;
use Infrangible\Core\Helper\Stores;
use Infrangible\Sitemap\Helper\Data;
use Infrangible\Sitemap\Model\SitemapUrl;
use Infrangible\Sitemap\Model\SitemapUrlFactory;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests the source of the landing pages for Infrangible Sitemap.
 */
#[AllowMockObjectsWithoutExpectations]
class PageTest extends TestCase
{
    /**
     * Tests that no pages are loaded if they are not exported.
     *
     * @return void
     */
    public function testGetStoreDataNotExported(): void
    {
        $storeHelper = $this->createMock(Stores::class);
        $storeHelper->expects($this->once())->method('getStoreConfigFlag')
            ->with('infrangible_sitemap/catalog_attribute_landing_page/page/export', true, 1)
            ->willReturn(false);

        $loader = $this->createMock(Loader::class);
        $loader->expects($this->never())->method('getObjects');

        $this->assertSame([], $this->createSource($loader, $storeHelper)->getStoreData(1));
    }

    /**
     * Tests that the exported pages are loaded.
     *
     * @return void
     */
    public function testGetStoreData(): void
    {
        $page = $this->createStub(LandingPage::class);

        $storeHelper = $this->createStub(Stores::class);
        $storeHelper->method('getStoreConfigFlag')->willReturn(true);

        $loader = $this->createMock(Loader::class);
        $loader->expects($this->once())->method('getObjects')->with(Loader::TYPE_PAGE, 1)->willReturn([$page]);

        $this->assertSame([$page], $this->createSource($loader, $storeHelper)->getStoreData(1));
    }

    /**
     * Tests the sitemap URLs: the absolute URL in the store, the date of the last change, the change frequency and
     * the priority of the configuration of the pages; other data is skipped.
     *
     * @return void
     */
    public function testTransformStoreData(): void
    {
        $page = $this->createStub(LandingPage::class);

        $loader = $this->createStub(Loader::class);
        $loader->method('getUrlPath')->willReturn('red.html');
        $loader->method('getUpdatedAt')->willReturn('2026-10-01 12:00:00');

        $urls = $this->createSource($loader, $this->createStub(Stores::class))
            ->transformStoreData(1, [$page, 'no page']);

        $this->assertCount(1, $urls);
        $this->assertSame('https://example.com/red.html', $urls[ 0 ]->getUrl());
        $this->assertSame('2026-10-01', $urls[ 0 ]->getLastModified());
        $this->assertSame('weekly', $urls[ 0 ]->getChangeFrequency());
        $this->assertSame('0.8', $urls[ 0 ]->getPriority());
    }

    /**
     * Returns the source with the loader and the store helper.
     *
     * @param Loader $loader
     * @param Stores $storeHelper
     *
     * @return Page
     */
    private function createSource(Loader $loader, Stores $storeHelper): Page
    {
        $sitemapHelper = $this->createMock(Data::class);
        $sitemapHelper->method('getChangeFrequency')
            ->with('catalog_attribute_landing_page/page', 1, 'yearly')
            ->willReturn('weekly');
        $sitemapHelper->method('getPriority')->willReturn('0.8');
        $sitemapHelper->method('getUrl')->willReturnCallback(
            static fn(string $path): string => 'https://example.com/' . $path
        );
        $sitemapHelper->method('getLastModified')->willReturn('2026-10-01');

        $sitemapUrlFactory = $this->createStub(SitemapUrlFactory::class);
        $sitemapUrlFactory->method('create')->willReturnCallback(static fn(): SitemapUrl => new SitemapUrl());

        return new Page($loader, $storeHelper, $sitemapHelper, $sitemapUrlFactory);
    }
}
