# Strix Advanced CMS Extensions

Latest Orders CMS element and block for Shopware 6.7.

## Overview

This plugin adds a CMS element (`strix-latest-orders`) and a CMS block that let you place the customer’s latest order anywhere in Shopping Experiences. The storefront output mirrors the “Last order” card from the My Account overview. The element renders only for logged-in customers.

## Compatibility

-   Shopware: 6.7.x
-   PHP: per Shopware 6.7 requirements

## Features

-   CMS element `strix-latest-orders`
-   CMS block under a custom category “Strix Advanced CMS”
-   Storefront rendering identical to the account overview “Last order” card
-   Link to “All orders” with a translation key and total order count
-   Element setting to configure the amount of last orders to be shown
-   Proper admin previews and min-heights
-   Resolver loads transactions, deliveries, payment/shipping methods, and line items

## File Structure

```
.
├── composer.json
├── phpunit.xml
├── README.md
├── src
│   ├── Core
│   │   └── Content
│   │       └── Cms
│   │           └── StrixLatestOrdersCmsElementResolver.php
│   ├── Resources
│   │   ├── app
│   │   │   └── administration
│   │   │       ├── build
│   │   │       │   └── administration.json
│   │   │       └── src
│   │   │           ├── asset
│   │   │           │   └── style
│   │   │           │       └── strix-cms.scss
│   │   │           ├── main.js
│   │   │           ├── module
│   │   │           │   └── sw-cms
│   │   │           │       ├── blocks
│   │   │           │       │   └── strix-advanced-cms
│   │   │           │       │       └── latest-orders
│   │   │           │       │           ├── component
│   │   │           │       │           │   └── sw-cms-block-strix-block-latest-orders.html.twig
│   │   │           │       │           ├── index.js
│   │   │           │       │           └── preview
│   │   │           │       │               └── sw-cms-preview-strix-block-latest-orders.html.twig
│   │   │           │       └── elements
│   │   │           │           └── strix-latest-orders
│   │   │           │               ├── component
│   │   │           │               │   └── sw-cms-el-strix-latest-orders.html.twig
│   │   │           │               ├── index.js
│   │   │           │               └── preview
│   │   │           │                   └── sw-cms-el-preview-strix-latest-orders.html.twig
│   │   │           └── snippet
│   │   │               ├── de-DE.json
│   │   │               ├── en-GB.json
│   │   │               └── nl-NL.json
│   │   ├── config
│   │   │   ├── config.xml
│   │   │   ├── plugin.png
│   │   │   └── services.xml
│   │   ├── public
│   │   │   └── administration
│   │   │       └── assets
│   │   │           ├── strix-advanced-c-m-s-extensions-HaOksjiW.js
│   │   │           ├── strix-advanced-c-m-s-extensions-HaOksjiW.js.map
│   │   │           └── strix-advanced-c-m-s-extensions-LqVPT7H-.css
│   │   ├── snippet
│   │   │   ├── de-DE.json
│   │   │   ├── en-GB.json
│   │   │   └── nl-NL.json
│   │   └── views
│   │       └── storefront
│   │           ├── block
│   │           │   └── cms-block-strix-block-latest-orders.html.twig
│   │           └── element
│   │               └── cms-element-strix-latest-orders.html.twig
│   └── StrixAdvancedCMSExtensions.phpd
├── structure.txt
└── tests
    └── TestBootstrap.php

34 directories, 30 files
```

## Usage

1. In Administration → Shopping Experiences, add the block from category Strix Advanced CMS or replace an element in an existing block with Strix: Latest Orders.
    - The amount of last orders to show can be configured in the element settings. Default is the latest 3.
2. The element renders only for logged-in customers with Orders. Guests and customer without orders see nothing.
3. The title row includes a link to the My Account Order list. Optional total order count can be shown next to the link.

## Translations

Snippet: strix.account.ShowAllOrders

## Admin Category Label

The CMS block appears under a custom category. Provide a label via Administration snippets so the category name displays in the block picker. Ensure your admin snippet files contain a key for the category, and that main.js imports the admin snippets.

## Developer Notes

-   Element type: strix-latest-orders
-   Resolver signature (Shopware 6.7):
    -   collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    -   enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
-   Associations loaded:
    -   transactions.stateMachineState, transactions.paymentMethod
    -   deliveries.stateMachineState, deliveries.shippingMethod
    -   lineItems, lineItems.product, lineItems.product.cover
-   Total order count:
    -   Exposed as element.data.totalOrders for use in the link text.

## Troubleshooting

-   Block selectable but element missing inside block: add storefront block template views/storefront/block/cms-block-<name>.html.twig and include its slot element templates.
-   Admin error Object.keys(undefined): ensure the block uses expanded slots with default.config and the element defines defaultConfig.
-   Order detail route error for deep link: use path('frontend.account.order.single.page', { orderId: order.id, deepLinkCode: order.deepLinkCode }) or fall back to frontend.account.order.page.
-   Missing line items in table: add lineItems and related associations in the resolver criteria.
