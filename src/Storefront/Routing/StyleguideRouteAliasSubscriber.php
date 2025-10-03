<?php declare(strict_types=1);

namespace StrixNLUxUpgrades\Storefront\Routing;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class StyleguideRouteAliasSubscriber implements EventSubscriberInterface
{
    private const CANONICAL_PATH = '/styleguide';

    public function __construct(private readonly SystemConfigService $systemConfig) {}

    public static function getSubscribedEvents(): array
    {
        // Run BEFORE RouterListener (~32). 64 is a safe bet.
        return [ KernelEvents::REQUEST => [['onKernelRequest', 64]] ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Read global config (earliest stage); if you truly need per-sales-channel here,
        // you can later add a lightweight resolver once SC id is available.
        $enabled = (bool) ($this->systemConfig->get('StrixNLUxUpgrades.config.styleguideEnabled') ?? false);
        if (!$enabled) {
            return;
        }

        $configuredPath = (string) ($this->systemConfig->get('StrixNLUxUpgrades.config.styleguidePath') ?? self::CANONICAL_PATH);

        // Normalize both sides
        $configuredPath = '/' . ltrim($configuredPath, '/');
        $configuredPath = rtrim($configuredPath, '/') ?: '/';

        $requestPath = rtrim($request->getPathInfo(), '/') ?: '/';
        $canonical   = rtrim(self::CANONICAL_PATH, '/') ?: '/';

        // Already on canonical → do nothing
        if ($requestPath === $canonical) {
            return;
        }

        // Visiting the configured alias → 301 to canonical
        if ($requestPath === $configuredPath) {
            $event->setResponse(new RedirectResponse(self::CANONICAL_PATH, 301));
        }
    }
}
