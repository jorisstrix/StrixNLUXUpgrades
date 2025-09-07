<?php declare(strict_types=1);

namespace StrixNLUxUpgrades\Core\Checkout\Cart\Processor;

use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class CartDiscountProcessor implements CartProcessorInterface
{
    public const EXTENSION_KEY = 'strix_discount_summary';

    public function __construct(private readonly SystemConfigService $systemConfigService)
    {
    }

    public function process(
        CartDataCollection $data,
        Cart $original,
        Cart $toCalculate,
        SalesChannelContext $context,
        CartBehavior $behavior
    ): void {
        $enabled = (bool) $this->systemConfigService->get(
            'StrixNLUxUpgrades.config.showCartDiscountSummary',
            $context->getSalesChannelId()
        );

        if (!$enabled) {
            return;
        }

        if ($toCalculate->getLineItems()->count() === 0) {
            return;
        }

        $preSubtotal = 0.0;
        $discountTotal = 0.0;

        foreach ($toCalculate->getLineItems() as $lineItem) {
            $this->accumulateItem($lineItem, $preSubtotal, $discountTotal);
        }

        $toCalculate->addExtension(self::EXTENSION_KEY, new ArrayStruct([
            'preSubtotal' => $preSubtotal,
            'discountTotal' => $discountTotal,
        ]));
    }

    private function accumulateItem(LineItem $item, float &$preSubtotal, float &$discountTotal): void
    {
        $price = $item->getPrice();
        if ($price !== null) {
            $qty = $item->getQuantity();
            $unit = $price->getUnitPrice();
            $list = $price->getListPrice();

            if ($list !== null && $list->getPrice() > $unit) {
                $preSubtotal += $list->getPrice() * $qty;
                $discountTotal += ($list->getPrice() - $unit) * $qty;
            } else {
                $preSubtotal += $unit * $qty;
            }
        }

        foreach ($item->getChildren() as $child) {
            $this->accumulateItem($child, $preSubtotal, $discountTotal);
        }
    }
}
