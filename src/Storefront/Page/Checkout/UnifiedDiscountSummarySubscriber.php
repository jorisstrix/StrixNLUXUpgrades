<?php declare(strict_types=1);

namespace StrixNLUxUpgrades\Storefront\Page\Checkout;

use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Finish\CheckoutFinishPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\Struct\ArrayStruct;

class UnifiedDiscountSummarySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutCartPageLoadedEvent::class => 'onCartOrConfirmOrFinish',
            CheckoutConfirmPageLoadedEvent::class => 'onCartOrConfirmOrFinish',
            CheckoutFinishPageLoadedEvent::class => 'onCartOrConfirmOrFinish',
        ];
    }

    public function onCartOrConfirmOrFinish($event): void
    {
        $lineItems = [];

        if ($event instanceof CheckoutCartPageLoadedEvent || $event instanceof CheckoutConfirmPageLoadedEvent) {
            $lineItems = $event->getPage()->getCart()->getLineItems();
        }

        if ($event instanceof CheckoutFinishPageLoadedEvent) {
            $lineItems = $event->getPage()->getOrder()->getLineItems();
        }

        if (empty($lineItems)) {
            return;
        }

        $preSubtotal = 0.0;
        $discountTotal = 0.0;

        foreach ($lineItems as $lineItem) {
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

        $event->getPage()->addExtension('strix_discount_summary', new ArrayStruct([
            'preSubtotal' => $preSubtotal,
            'discountTotal' => $discountTotal,
        ]));
    }
}
