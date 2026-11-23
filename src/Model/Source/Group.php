<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogAttributeLandingPageSitemap\Model\Source;

use Infrangible\CatalogAttributeLandingPageSitemap\Model\Loader;

/**
 * The landing groups for the sitemap of Infrangible Sitemap.
 */
class Group extends AbstractSource
{
    /**
     * Returns the type: group.
     *
     * @return string
     */
    protected function getType(): string
    {
        return Loader::TYPE_GROUP;
    }
}
