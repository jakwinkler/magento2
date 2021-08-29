<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Eav\Model\Config;
use Magento\Catalog\Model\Product;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/multiselect_attribute_text.php');

/** Create product with options and multiselect attribute */
$eavConfig = Bootstrap::getObjectManager()->get(Config::class);
$attribute = $eavConfig->getAttribute(Product::ENTITY, 'multiselect_entity_text');
/** @var $installer \Magento\Catalog\Setup\CategorySetup */
$installer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Setup\CategorySetup::class
);

/** @var $options \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection */
$options = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection::class
);
$options->setAttributeFilter($attribute->getId());
$optionIds = $options->getAllIds();

$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId($optionIds[2] * 10)
    ->setAttributeSetId($installer->getAttributeSetId('catalog_product', 'Default'))
    ->setWebsiteIds([1])
    ->setName('With Multiselect 3')
    ->setSku('simple_ms_3')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setMultiselectEntityText(array_values($optionIds))
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->save();

$resource = $product->getResource()->load($product, 'simple_ms_3', 'sku');
var_dump($product->getMultiselectEntityText());
var_dump($product->getData('multiselect_entity_text'));
