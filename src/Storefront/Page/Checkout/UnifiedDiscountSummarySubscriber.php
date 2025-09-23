<?php declare(strict_types=1);

namespace StrixNLUxUpgrades\Storefront\Page\Checkout;

use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Finish\CheckoutFinishPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Contracts\Translation\TranslatorInterface;

class UnifiedDiscountSummarySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

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
        $discountLabel = 'Product Discount';

        $lineItems = null;

        if ($event instanceof CheckoutCartPageLoadedEvent || $event instanceof CheckoutConfirmPageLoadedEvent) {
            $lineItems = $event->getPage()->getCart()->getLineItems();
        }

        if ($event instanceof CheckoutFinishPageLoadedEvent) {
            $lineItems = $event->getPage()->getOrder()->getLineItems();
            $filteredLineItems = $lineItems->filter(fn ($lineItem) => !($lineItem->getType() === 'custom' && $lineItem->getLabel() === $discountLabel));
            $event->getPage()->getOrder()->setLineItems($filteredLineItems);
        }

        if ($lineItems === null || $lineItems->count() === 0) {
            return;
        }

        $preSubtotal = 0.0;
        $discountTotal = 0.0;

        foreach ($lineItems as $lineItem) {
            if ($lineItem->getType() === 'custom' && $lineItem->getLabel() === $discountLabel) {
                continue;
            }

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
