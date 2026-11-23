<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogAttributeLandingPageSitemap\Observer;

use Infrangible\BackendWidget\Block\Form as FormBlock;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Adds the field "Exclude from Sitemap" to the forms of the landing pages and groups.
 */
class Form implements ObserverInterface
{
    /**
     * Adds the fieldset "Sitemap" with the field.
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $form = $observer->getData('form');
        $block = $observer->getData('block');

        if (! $form instanceof DataForm || ! $block instanceof FormBlock) {
            return;
        }

        $fieldSet = $form->addFieldset('sitemap', ['legend' => __('Sitemap')]);

        $block->addYesNoField($fieldSet, 'exclude_from_sitemap', __('Exclude from Sitemap')->render());
    }
}
