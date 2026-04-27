<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Bundle\Observer;

use Magento\Bundle\Model\Product\Type as BundleType;
use Magento\Framework\Event\ObserverInterface;

class SetAttributeTabBlockObserver implements ObserverInterface
{
    /**
     * Catalog helper
     *
     * @var \Magento\Catalog\Helper\Catalog
     */
    protected $helperCatalog;

    /**
     * @param \Magento\Catalog\Helper\Catalog $helperCatalog
     */
    public function __construct(\Magento\Catalog\Helper\Catalog $helperCatalog)
    {
        $this->helperCatalog = $helperCatalog;
    }

    /**
     * Setting attribute tab block for bundle
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == BundleType::TYPE_CODE) {
            $this->helperCatalog->setAttributeTabBlock(
                \Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes::class
            );
        }
        return $this;
    }
}
