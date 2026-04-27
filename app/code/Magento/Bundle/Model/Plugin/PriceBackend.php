<?php

/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Model\Plugin;

use Magento\Bundle\Model\Product\Type as BundleType;

/**
 * Make price validation optional for bundle dynamic
 */
class PriceBackend
{
    /**
     * Around validate
     *
     * @param \Magento\Catalog\Model\Product\Attribute\Backend\Price $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $object
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundValidate(
        \Magento\Catalog\Model\Product\Attribute\Backend\Price $subject,
        \Closure $proceed,
        $object
    ) {
        if ($object instanceof \Magento\Catalog\Model\Product
            && $object->getTypeId() == BundleType::TYPE_CODE
            && $object->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_DYNAMIC
        ) {
            return true;
        }

        return $proceed($object);
    }
}
