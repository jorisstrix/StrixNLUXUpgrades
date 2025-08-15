<?php declare(strict_types=1);

namespace StrixNLUxUpgrades\Core\Content\Cms;

use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\ArrayStruct;

class StrixLatestOrdersCmsElementResolver extends AbstractCmsElementResolver
{
    public function __construct(private readonly EntityRepository $orderRepository)
    {
    }

    public function getType(): string
    {
        return 'strix-latest-orders';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $sc = $resolverContext->getSalesChannelContext();
        if (!$sc || !$sc->getCustomerId()) {
            return null;
        }

        $limit = (int) ($slot->getFieldConfig()->get('numberOfOrders')?->getValue() ?? 3);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('orderCustomer.customerId', $sc->getCustomerId()));
        $criteria->addSorting(new FieldSorting('orderDateTime', FieldSorting::DESCENDING));

        $criteria->addAssociation('stateMachineState');
        $criteria->addAssociation('transactions.stateMachineState');
        $criteria->addAssociation('transactions.paymentMethod');
        $criteria->addAssociation('deliveries.stateMachineState');
        $criteria->addAssociation('deliveries.shippingMethod');
        $criteria->addAssociation('lineItems');
        $criteria->addAssociation('lineItems.product');
        $criteria->addAssociation('lineItems.product.cover');
        $criteria->addAssociation('orderCustomer');

        $criteria->setLimit($limit);
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);

        $collection = new CriteriaCollection();
        $collection->add('orders_' . $slot->getUniqueIdentifier(), OrderDefinition::class, $criteria);

        return $collection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $key = 'orders_' . $slot->getUniqueIdentifier();
        $search = $result->get($key);

        $orders = $search ? $search->getEntities() : [];
        $total  = $search && $search->getTotal() !== null ? $search->getTotal() : 0;

        $slot->setData(new ArrayStruct([
            'orders' => $orders,
            'totalOrders' => $total,
        ]));
    }
}
