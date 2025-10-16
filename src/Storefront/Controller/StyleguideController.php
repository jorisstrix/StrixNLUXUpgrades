<?php

declare(strict_types=1);

namespace StrixNLUxUpgrades\Storefront\Controller;

use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: [
    '_routeScope' => ['storefront'],
])]
class StyleguideController extends StorefrontController
{
    public function __construct(
        private readonly SystemConfigService $systemConfig
    ) {
    }

    #[Route(path: '/styleguide', name: 'frontend.page.styleguide', methods: ['GET'])]
    public function index(): Response
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $salesChannelId = $request?->attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_ID);

        $enabled = (bool) ($this->systemConfig->get('StrixNLUxUpgrades.config.styleguideEnabled', $salesChannelId) ?? false);
        if (! $enabled) {
            throw new NotFoundHttpException();
        }

        $response = $this->renderStorefront('@StrixNLUxUpgrades/storefront/page/strix-styleguide/index.html.twig');

        // Clean, route-scoped crawler directive (works regardless of <meta> content):
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
