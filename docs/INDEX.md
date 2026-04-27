# Magento 2 Decouplifier — Documentation Index

## Module Decoupling Reports

| Document | Scope | Status |
|----------|-------|--------|
| [Configurable Product](decoupling/configurable_product.md) | ConfigurableProduct, ConfigurableImportExport, ConfigurableProductGraphQl, ConfigurableProductSales — constants scan + cross-module coupling | Scanned. 3 modules to decouple (NewRelicReporting, Wishlist, Weee) |
| [Downloadable Product](decoupling/downloadable_product.md) | Downloadable, DownloadableGraphQl, DownloadableImportExport — constants scan + cross-module coupling | Scanned. 6 modules to decouple. GiftMessageGraphQl phantom dep fixed |
| [CatalogInventory](decoupling/catalog_inventory.md) | CatalogInventory coupling into 24 modules, 83 PHP imports, 5 coupling patterns | Scanned. 5 phantom composer deps removed. Parent stock recalc pattern documented |
| [Sales / Shipping / Payment](decoupling/sales_shipping_payment.md) | Bidirectional coupling between Sales, Shipping (6 modules), Payment (4 modules) | Scanned. Circular dep found (Sales ↔ Payment). 3 phantom deps identified |
| [Payment ↔ Sales Circular](decoupling/payment_sales_circular.md) | Plan to break Payment → Sales circular dependency | Plan documented. 3-phase approach (move adapters, move observers, cleanup) |
| [Newsletter](decoupling/newsletter.md) | Newsletter ↔ Customer bidirectional coupling | Customer has 11 Newsletter imports. Plan: move newsletter UI from Customer to Newsletter module |
| [Review](decoupling/review.md) | Review, ReviewAnalytics, ReviewGraphQl — can it be fully removed? | Yes. Catalog has null-object `DefaultProvider`. Only blocker: 16 review report files in Reports module |
| [Weee (FPT)](decoupling/weee.md) | Weee, WeeeGraphQl — inbound/outbound coupling | Zero inbound coupling — already fully optional. WEEE fields baked into Sales/Quote API interfaces (design debt) |
| [Cron & Message Queue](decoupling/cron_and_message_queue.md) | Cron, Amqp, MysqlMq, MessageQueue — can MQ be fully optional? | Cron: already clean. MQ transports: zero coupling. Framework/MessageQueue (158 files): needs extraction. Proposed `Framework\Async\PublisherInterface` |

### GraphQL Isolation

Scanned all 44 GraphQL modules. Result: **nearly clean** — only 1 violation found and fixed (`Downloadable/etc/di.xml` referencing `GiftMessageGraphQl`). Zero core modules depend on GraphQL.

## Framework Modernization

| Document | Scope | Key Findings |
|----------|-------|-------------|
| [Framework Modernization (PHP 8.4)](magento_framework_modernization.md) | Proxy generation, DataObject/getters, enums, deprecations, component replacement | 206 Proxy classes → native lazy objects; 99 constant bags → enums; 401 `@deprecated` → `#[\Deprecated]`; Mail/HTTP/Validator replaceable |
| [View/Layout Modernization](magento_view_modernization.md) | Layout pipeline, block rendering, asset pipeline, template resolution | 6 proposals: lazy blocks, compiled layout cache, component model, template map, asset extraction, better serialization |

## Changes Made

| Change | Files Modified |
|--------|---------------|
| Fix Downloadable → GiftMessageGraphQl coupling | `Downloadable/etc/di.xml`, `GiftMessageGraphQl/etc/graphql/di.xml` |
| Remove phantom CatalogInventory deps | `AdvancedPricingImportExport/composer.json`, `Fedex/composer.json`, `Reports/composer.json`, `Shipping/composer.json`, `Usps/composer.json` |
