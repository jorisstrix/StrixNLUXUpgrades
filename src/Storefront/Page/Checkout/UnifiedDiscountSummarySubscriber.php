<?php declare(strict_types=1);

namespace StrixNLUxUpgrades\Storefront\Page\Checkout;

use Shopware\Storefront\Page\Checkout\Cart\CheckoutCartPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Finish\CheckoutFinishPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Register\CheckoutRegisterPageLoadedEvent;
use Shopware\Core\Checkout\Cart\LineItem\LineItemCollection;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UnifiedDiscountSummarySubscriber implements EventSubscriberInterface
{
    private const CONFIG_KEY = 'StrixNLUxUpgrades.config.showCartDiscountSummary';

    public function __construct(
        private readonly SystemConfigService $systemConfigService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutCartPageLoadedEvent::class => 'onCartOrConfirmOrFinish',
            CheckoutConfirmPageLoadedEvent::class => 'onCartOrConfirmOrFinish',
            CheckoutFinishPageLoadedEvent::class => 'onCartOrConfirmOrFinish',
            CheckoutRegisterPageLoadedEvent::class => 'onCartOrConfirmOrFinish',
        ];
    }

    public function onCartOrConfirmOrFinish(object $event): void
    {
        $salesChannelId = $event->getSalesChannelContext()->getSalesChannelId();
        if (!(bool) $this->systemConfigService->get(self::CONFIG_KEY, $salesChannelId)) {
            return;
        }

        $lineItems = $this->resolveLineItems($event);
        if (!$lineItems || $lineItems->count() === 0) {
            return;
        }

        if ($event instanceof CheckoutFinishPageLoadedEvent) {
            $filtered = $lineItems->filter(fn ($li) => !($li->getType() === 'custom' && ($li->getPayload()['is_strix_discount'] ?? false)));
            $event->getPage()->getOrder()->setLineItems($filtered);
            $lineItems = $filtered;
        }

        [$preSubtotal, $discountTotal] = $this->calculateTotals($lineItems);

        $event->getPage()->addExtension('strix_discount_summary', new ArrayStruct([
            'preSubtotal' => $preSubtotal,
            'discountTotal' => $discountTotal,
        ]));
    }

    private function resolveLineItems(object $event): ?LineItemCollection
    {
        if ($event instanceof CheckoutCartPageLoadedEvent || $event instanceof CheckoutConfirmPageLoadedEvent || $event instanceof CheckoutRegisterPageLoadedEvent) {
            return $event->getPage()->getCart()->getLineItems();
        }

        if ($event instanceof CheckoutFinishPageLoadedEvent) {
            return $event->getPage()->getOrder()->getLineItems();
        }

        return null;
    }

    private function calculateTotals(LineItemCollection $items): array
    {
        $preSubtotal = 0.0;
        $discountTotal = 0.0;

        foreach ($items as $li) {
            if ($li->getType() === 'custom' && ($li->getPayload()['is_strix_discount'] ?? false)) {
                continue;
            }

            $price = $li->getPrice();
            if (!$price) {
                continue;
            }

            $qty = $li->getQuantity();
            $unitPrice = $price->getUnitPrice();
            $listPrice = $price->getListPrice()?->getPrice();

            if ($listPrice && $listPrice > $unitPrice) {
                $preSubtotal += $listPrice * $qty;
                $discountTotal += ($listPrice - $unitPrice) * $qty;
            } else {
                $preSubtotal += $unitPrice * $qty;
            }
        }

        return [$preSubtotal, $discountTotal];
    }
}
