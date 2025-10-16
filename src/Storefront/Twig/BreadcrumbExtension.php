<?php

declare(strict_types=1);

namespace StrixNLUxUpgrades\Storefront\Twig;

use Shopware\Core\Content\Product\SalesChannel\Detail\ProductDetailRoute;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class BreadcrumbExtension extends AbstractExtension
{
    private readonly RequestStack $requestStack;

    private readonly ProductDetailRoute $productDetailRoute;

    public function __construct(RequestStack $requestStack, ProductDetailRoute $productDetailRoute)
    {
        $this->requestStack = $requestStack;
        $this->productDetailRoute = $productDetailRoute;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('current_product', [$this, 'getCurrentProduct']),
        ];
    }

    public function getCurrentProduct(): ?SalesChannelProductEntity
    {
        $request = $this->requestStack->getCurrentRequest();
        if (! $request instanceof Request) {
            return null;
        }

        $route = (string) $request->attributes->get('_route');
        if ($route !== 'frontend.detail.page') {
            return null;
        }

        $params = (array) $request->attributes->get('_route_params', []);
        $productId = $params['productId'] ?? null;
        if (! $productId) {
            return null;
        }

        $salesChannelContext = $request->attributes->get('sw-sales-channel-context');
        if (! $salesChannelContext instanceof SalesChannelContext) {
            return null;
        }

        $detail = $this->productDetailRoute->load($productId, $request, $salesChannelContext, new Criteria());
        return $detail->getProduct();
    }
}
