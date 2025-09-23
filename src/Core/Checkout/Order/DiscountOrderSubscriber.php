<?php declare(strict_types=1);

namespace StrixNLUxUpgrades\Core\Checkout\Order;

use Shopware\Core\Checkout\Order\OrderEvents;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\Snippet\SnippetService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DiscountOrderSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityRepository $orderRepository,
        private readonly EntityRepository $orderLineItemRepository,
        private readonly SnippetService $snippetService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderEvents::ORDER_WRITTEN_EVENT => 'onOrderWritten',
        ];
    }

    public function onOrderWritten($event): void
    {
        $writeResults = $event->getWriteResults();
        $context = $event->getContext();

        foreach ($writeResults as $writeResult) {
            if ($writeResult->getEntityName() !== 'order') {
                continue;
            }

            $orderId = $writeResult->getPrimaryKey();
            if (!$orderId || !is_string($orderId)) {
                continue;
            }

            $this->addDiscountLineItemToOrder($orderId, $context);
        }
    }

    private function addDiscountLineItemToOrder(string $orderId, Context $context): void
    {
        $criteria = new Criteria([$orderId]);
        $criteria->addAssociation('lineItems');

        $order = $this->orderRepository->search($criteria, $context)->first();
        if (!$order) {
            return;
        }

        $lineItems = $order->getLineItems();
        if (!$lineItems) {
            return;
        }

        $discountTotal = 0.0;
        $hasStrixDiscount = false;

        $discountLabel = 'Product Discount';
        
        foreach ($lineItems as $lineItem) {
            if ($lineItem->getType() === 'custom' && $lineItem->getLabel() === $discountLabel) {
                $hasStrixDiscount = true;
                break;
            }

            if ($lineItem->getType() !== 'product') {
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
                $discountTotal += ($listPrice - $unitPrice) * $qty;
            }
        }

        if ($discountTotal <= 0 || $hasStrixDiscount) {
            return;
        }

        $discountLineItemData = [
            'id' => Uuid::randomHex(),
            'orderId' => $orderId,
            'identifier' => 'strix-discount-' . Uuid::randomHex(),
            'type' => 'custom',
            'label' => $discountLabel,
            'quantity' => 1,
            'price' => [
                'unitPrice' => -$discountTotal,
                'totalPrice' => -$discountTotal,
                'quantity' => 1,
                'calculatedTaxes' => [],
                'taxRules' => [],
            ],
            'good' => false,
            'removable' => false,
            'stackable' => false,
        ];

        $this->orderLineItemRepository->create([$discountLineItemData], $context);
    }
}
