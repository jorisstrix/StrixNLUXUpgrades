<?php declare(strict_types=1);

namespace StrixNLUxUpgrades\Storefront\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class StyleguideNoIndexSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }

    public function onResponse(ResponseEvent $event): void
    {
        // Front controller requests only
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = (string) $request->attributes->get('_route', '');

        // Apply only to the styleguide page
        if ($route !== 'frontend.page.styleguide') {
            return;
        }

        $response = $event->getResponse();
        // Enforce noindex/nofollow via HTTP header
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
    }
}
