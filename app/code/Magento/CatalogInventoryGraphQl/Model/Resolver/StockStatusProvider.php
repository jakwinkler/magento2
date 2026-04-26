<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventoryGraphQl\Model\Resolver;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use Magento\CatalogInventory\Api\StockStatusRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Quote\Model\Quote\Item;

/**
 * @inheritdoc
 */
class StockStatusProvider implements ResolverInterface
{
    /**
     * In Stock return code
     */
    private const IN_STOCK = "IN_STOCK";

    /**
     * Out of Stock return code
     */
    private const OUT_OF_STOCK = "OUT_OF_STOCK";

    /**
     * StockStatusProvider Constructor
     *
     * @param StockStatusRepositoryInterface $stockStatusRepository
     * @param ProductRepositoryInterface $productRepositoryInterface
     */
    public function __construct(
        private readonly StockStatusRepositoryInterface $stockStatusRepository,
        private readonly ProductRepositoryInterface $productRepositoryInterface,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (!array_key_exists('model', $value) || !$value['model'] instanceof ProductInterface) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        /** @var Item $cartItem */
        $cartItem = $value['cart_item'] ?? [];
        if (!$cartItem instanceof Item) {
            $product = $value['model'];
            $stockStatus = $this->stockStatusRepository->get($product->getId());

            return ((int)$stockStatus->getStockStatus()) ? self::IN_STOCK : self::OUT_OF_STOCK;
        }

        if ($cartItem->getHasChildren() && !empty($cartItem->getQtyOptions())) {
            return $this->getCompositeProductStockStatus($cartItem);
        }

        $product = $this->getVariantProduct($cartItem) ?? $cartItem->getProduct();
        $stockStatus = $this->stockStatusRepository->get($product->getId());

        return ((int)$stockStatus->getStockStatus()) ? self::IN_STOCK : self::OUT_OF_STOCK;
    }

    /**
     * Get stock status of composite product by checking its children options
     *
     * @param \Magento\Quote\Model\Quote\Item $cartItem
     * @return string
     */
    private function getCompositeProductStockStatus(Item $cartItem): string
    {
        $qtyOptions = $cartItem->getQtyOptions();
        foreach ($qtyOptions as $qtyOption) {
            $stockStatus = $this->stockStatusRepository->get($qtyOption->getProduct()->getId());
            if (!(int)$stockStatus->getStockStatus()) {
                return self::OUT_OF_STOCK;
            }
        }

        return self::IN_STOCK;
    }

    /**
     * Returns variant product if available
     *
     * @param Item $cartItem
     * @return ProductInterface|null
     * @throws NoSuchEntityException
     */
    private function getVariantProduct(Item $cartItem): ?ProductInterface
    {
        $children = $cartItem->getChildren();
        if (!empty($children) && $cartItem->getProductType() !== $cartItem->getRealProductType()) {
            if ($children[0] !== null) {
                return $this->productRepositoryInterface->get($cartItem->getSku());
            }
        }
        return null;
    }
}
