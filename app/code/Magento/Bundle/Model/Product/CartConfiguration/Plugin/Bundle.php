<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Model\Product\CartConfiguration\Plugin;

use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\CartConfiguration;

/**
 * Plugin to add bundle product cart configuration check.
 */
class Bundle
{
    /**
     * Decide whether product has been configured for cart or not
     *
     * @param \Magento\Catalog\Model\Product\CartConfiguration $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @param array $config
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsProductConfigured(
        CartConfiguration $subject,
        \Closure $proceed,
        Product $product,
        $config
    ) {
        if ($product->getTypeId() === Type::TYPE_CODE) {
            return isset($config['bundle_option']);
        }

        return $proceed($product, $config);
    }
}
