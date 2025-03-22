<?php

declare(strict_types=1);

namespace Infrangible\CatalogAttributeLandingPageSitemap\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $connection = $setup->getConnection();

        if (! $connection->tableColumnExists(
            $setup->getTable('catalog_attribute_landing_page'),
            'exclude_from_sitemap'
        )) {
            $connection->addColumn(
                $setup->getTable('catalog_attribute_landing_page'),
                'exclude_from_sitemap',
                [
                    'type'     => Table::TYPE_SMALLINT,
                    'length'   => 3,
                    'nullable' => false,
                    'default'  => '0',
                    'comment'  => 'Exclude from Sitemap'
                ]
            );
        }

        if (! $connection->tableColumnExists(
            $setup->getTable('catalog_attribute_landing_group'),
            'exclude_from_sitemap'
        )) {
            $connection->addColumn(
                $setup->getTable('catalog_attribute_landing_group'),
                'exclude_from_sitemap',
                [
                    'type'     => Table::TYPE_SMALLINT,
                    'length'   => 3,
                    'nullable' => false,
                    'default'  => '0',
                    'comment'  => 'Exclude from Sitemap'
                ]
            );
        }

        $setup->endSetup();
    }
}
