<?php declare(strict_types=1);

namespace StrixNLUxUpgrades\Core\Checkout\Cart\Processor;

use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Checkout\Cart\CartBehavior;

class CartDiscountProcessor implements CartProcessorInterface
{
    public function process(
        CartDataCollection $data,
        Cart $original,
        Cart $toCalculate,
        SalesChannelContext $context,
        CartBehavior $behavior
    ): void {
        // Example: calculate preSubtotal and discount
        $preSubtotal = 0.0;
        $discountTotal = 0.0;

        foreach ($toCalculate->getLineItems() as $lineItem) {
            $price = $lineItem->getPrice();
            if (!$price) {
                continue;
            }

            $qty = $lineItem->getQuantity();
            $unitPrice = $price->getUnitPrice();
            $listPrice = $price->getListPrice()?->getPrice();

            if ($listPrice && $listPrice > $unitPrice) {
                $preSubtotal += $listPrice * $qty;
                $discountTotal += ($listPrice - $unitPrice) * $qty;
            } else {
                $preSubtotal += $unitPrice * $qty;
            }
        }

        $toCalculate->addExtension('strix_discount_summary', new \Shopware\Core\Framework\Struct\ArrayStruct([
            'preSubtotal' => $preSubtotal,
            'discountTotal' => $discountTotal,
        ]));
    }
}
