# Changelog

## 2.0.0

### Breaking changes

- Requires Magento 2.4.6 or newer and PHP 8.2 or newer. Older versions of Magento and PHP 7.1 to 8.1 are no longer
  supported; use version 1.x for these.
- Requires `infrangible/m2-catalog-attribute-landing-page` 3.0, `infrangible/m2-sitemap` 3.0,
  `infrangible/m2-backend-widget` 4.0, `infrangible/m2-core` 5.0, `infrangible/m2-foundation` 3.0 and
  `fewedev/base` 2.4.
- The columns "exclude_from_sitemap" are declared in `db_schema.xml`; the class `InstallSchema` is removed.
- All classes have native types, and the constructors use constructor property promotion. The sources and item
  providers of pages and groups share their code in the classes `Model\Loader`, `Model\Source\AbstractSource` and
  `Model\ItemProvider\AbstractItemProvider`, whose constructors changed.

### Fixed

- Invalid dates of the last change are left out instead of becoming 1970-01-01.

### Changed

- The URLs in the sitemap of Infrangible Sitemap no longer depend on the request that generates it (cron, console
  or admin): they are built for the store of the sitemap, with HTTPS if the store uses it in the frontend, like the
  URLs of Infrangible Sitemap itself.
- Both sitemaps take the URL suffix of categories of the store of the sitemap, not of the current store.
- Excluded pages and groups are filtered in the database instead of after loading them.
- The texts are translated into 24 languages.
- Unit tests.
