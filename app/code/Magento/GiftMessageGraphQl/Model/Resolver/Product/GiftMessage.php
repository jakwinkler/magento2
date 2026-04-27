<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\GiftMessageGraphQl\Model\Resolver\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GiftMessageGraphQl\Model\Config\Messages;

class GiftMessage implements ResolverInterface
{
    /**
     * Product types that do not support gift messages.
     *
     * @var array
     */
    private array $nonGiftMessageProductTypes;

    /**
     * @param \Magento\GiftMessageGraphQl\Model\Config\Messages $messagesConfig
     * @param array $nonGiftMessageProductTypes
     */
    public function __construct(
        private readonly Messages $messagesConfig,
        array $nonGiftMessageProductTypes = []
    ) {
        $this->nonGiftMessageProductTypes = $nonGiftMessageProductTypes;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): bool {
        if (!isset($value['model']) || !$value['model'] instanceof ProductInterface) {
            throw new LocalizedException(__('The product model is not available.'));
        }

        if (in_array($value['model']['type_id'], $this->nonGiftMessageProductTypes, true)) {
            return false;
        }

        return $this->messagesConfig->isGiftMessageAllowedForProduct(
            $value['model']->getGiftMessageAvailable(),
            $context->getExtensionAttributes()->getStore()
        );
    }
}
