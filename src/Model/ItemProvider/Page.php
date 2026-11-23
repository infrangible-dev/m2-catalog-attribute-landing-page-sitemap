<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogAttributeLandingPageSitemap\Model\ItemProvider;

use Infrangible\CatalogAttributeLandingPageSitemap\Model\Loader;

/**
 * The landing pages for the sitemap of Magento.
 */
class Page extends AbstractItemProvider
{
    /**
     * Returns the type: page.
     *
     * @return string
     */
    protected function getType(): string
    {
        return Loader::TYPE_PAGE;
    }
}
