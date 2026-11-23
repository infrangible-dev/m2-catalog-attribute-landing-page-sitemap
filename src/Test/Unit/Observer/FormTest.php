<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogAttributeLandingPageSitemap\Test\Unit\Observer;

use Infrangible\BackendWidget\Block\Form as FormBlock;
use Infrangible\CatalogAttributeLandingPageSitemap\Observer\Form;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

/**
 * Tests the field "Exclude from Sitemap" of the forms of the landing pages and groups.
 */
#[AllowMockObjectsWithoutExpectations]
class FormTest extends TestCase
{
    /**
     * Tests that the field is added in a fieldset of its own.
     *
     * @return void
     */
    public function testExecute(): void
    {
        $fieldSet = $this->createStub(Fieldset::class);

        $form = $this->createMock(DataForm::class);
        $form->expects($this->once())->method('addFieldset')->with('sitemap')->willReturn($fieldSet);

        $block = $this->createMock(FormBlock::class);
        $block->expects($this->once())->method('addYesNoField')->with(
            $fieldSet,
            'exclude_from_sitemap',
            'Exclude from Sitemap'
        );

        (new Form())->execute(new Observer(['form' => $form, 'block' => $block]));
    }

    /**
     * Tests that events without form or block are ignored.
     *
     * @return void
     */
    public function testExecuteWithoutForm(): void
    {
        $block = $this->createMock(FormBlock::class);
        $block->expects($this->never())->method('addYesNoField');

        (new Form())->execute(new Observer(['block' => $block]));
    }
}
