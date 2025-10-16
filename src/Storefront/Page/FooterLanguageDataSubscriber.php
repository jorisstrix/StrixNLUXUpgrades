<?php

declare(strict_types=1);

namespace StrixNLUxUpgrades\Storefront\Page;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\GenericPageLoadedEvent;
use Shopware\Storefront\Pagelet\Header\HeaderPageletLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FooterLanguageDataSubscriber implements EventSubscriberInterface
{
    private const ATTR = '_strix_header_for_footer';

    public function __construct(
        private readonly HeaderPageletLoader $headerLoader,
        private readonly SystemConfigService $systemConfig
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            GenericPageLoadedEvent::class => ['onGenericPageLoaded', 1000],
        ];
    }

    public function onGenericPageLoaded(GenericPageLoadedEvent $event): void
    {
        $scId = $event->getSalesChannelContext()->getSalesChannelId();

        $enabled = (bool) $this->systemConfig->get(
            'StrixNLUxUpgrades.config.showFooterLanguageSelector',
            $scId
        );

        if (! $enabled) {
            // Do nothing when disabled
            return;
        }

        // Provide header languages via request attribute (reliable for Twig)
        $header = $this->headerLoader->load($event->getRequest(), $event->getSalesChannelContext());
        $event->getRequest()->attributes->set(self::ATTR, $header);
    }
}
