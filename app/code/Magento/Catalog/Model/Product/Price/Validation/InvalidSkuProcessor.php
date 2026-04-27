<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\Product\Price\Validation;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductIdLocatorInterface;

/**
 * Class is responsible to detect list of invalid SKU values from list of provided skus and allowed product types.
 */
class InvalidSkuProcessor
{
    /**
     * @param \Magento\Catalog\Model\ProductIdLocatorInterface $productIdLocator
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param array $priceTypeRestrictedTypes Product types that require price type validation
     */
    public function __construct(
        private readonly ProductIdLocatorInterface $productIdLocator,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly array $priceTypeRestrictedTypes = []
    ) {
    }

    /**
     * Retrieve not found or invalid SKUs which product types are included to allowed types list.
     *
     * @param array $skus
     * @param array $allowedProductTypes
     * @param int|null $allowedPriceTypeValue
     * @return array
     */
    public function retrieveInvalidSkuList(array $skus, array $allowedProductTypes, $allowedPriceTypeValue = null)
    {
        $idsBySku = $this->productIdLocator->retrieveProductIdsBySkus($skus);
        $existingSkus = array_keys($idsBySku);
        $skuDiff = array_udiff(
            $skus,
            $existingSkus,
            'strcasecmp'
        );

        foreach ($idsBySku as $sku => $ids) {
            foreach ($ids as $type) {
                $valueTypeIsAllowed = false;

                if ($allowedPriceTypeValue
                    && in_array($type, $this->priceTypeRestrictedTypes, true)
                    && $this->productRepository->get($sku)->getPriceType() != $allowedPriceTypeValue
                ) {
                    $valueTypeIsAllowed = true;
                }

                if (!in_array($type, $allowedProductTypes) || $valueTypeIsAllowed) {
                    $skuDiff[] = $sku;
                    break;
                }
            }
        }

        return $skuDiff;
    }

    /**
     * Filter invalid values in SKUs list.
     *
     * @param array $skus
     * @param array $allowedProductTypes
     * @param int|null $allowedPriceTypeValue
     * @return array
     */
    public function filterSkuList(array $skus, array $allowedProductTypes, $allowedPriceTypeValue = null)
    {
        $failedItems = $this->retrieveInvalidSkuList($skus, $allowedProductTypes, $allowedPriceTypeValue);
        return array_diff($skus, $failedItems);
    }
}
