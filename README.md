# STRIX UX Upgrades (v1.4.0)

## What this plugin does

1. Latest Orders CMS element & block

    - Adds a CMS element `strix-latest-orders` and a block you can place anywhere in Shopping Experiences.
    - Renders the same “Last order” card as on the My Account overview, with the addition to show more then 1.
    - Only renders for logged-in customers (nothing is shown to guests or customers without orders).

2. Cart quality-of-life (merged in v1.2.0)

    - Cart items with quantity set to 0 are removed automatically (offcanvas cart + full cart page). Can be activated in the plugin settings.

3. Sort cart by latest added line item first (merged in v1.3.1)

    - Show the latest added line item first in off-canvas cart and cart page and sort to oldest last.

4. Show the amount of product present in the Product listings (merged in v1.3.1)

    - By default the amount of products present on the product listings in Category pages are shown. Can be activated in the plugin settings.

5. Feature to show the sum of all discounts in cart/checkout (merged in v1.4.0)
    - When active the Total line in the cart/checkout summary shows the sum of all items x quantity before discount. A new line in the summary is added called "Discount" (translatable snippet), which is the sum of all line item discounts in the cart. Can be activated in the plugin settings.

## Compatibility

-   Shopware 6.7.x

## Installation (local plugin)

1. Place the plugin in: custom/plugins/StrixNLUxUpgrades
2. In your project root run:
    - composer require strixnl/uxupgrades
    - bin/console plugin:refresh
    - bin/console plugin:install --activate StrixNLUxUpgrades
    - bin/console cache:clear
    - bin/console administration:build
    - bin/console theme:compile

## How to use the Latest Orders Element in the Admin

1. Go to: Content → Shopping Experiences.
2. Create or edit a layout.
3. Find the category **“Strix Advanced CMS”** in the block picker.
4. Drag **“Strix: Latest Orders”** onto your layout OR use it as a replacement element in an existing block.
5. Select the element to open **Settings** and set **Number of orders** (default: 3).
6. Save the layout and assign it to your categories.

## What to expect in the Storefront

-   The element shows the customer’s latest order(s) with statuses, payment/shipping info, and items.
-   A link to **All orders** is shown in the header; and displays the customer’s total order count.
-   If the visitor is not logged in or has no orders, the element does not render (no empty box).
-   In the cart, changing an item’s quantity to **0** removes it immediately (no manual refresh required).
-   On the same row as the sorting options, the amount of products found on the listing are shown.
-   Everyrthing seen on the storefront is translatable by a (new) snippet.

## Translations (keys to provide)

Administration (block picker & labels):

-   sw-cms.detail.label.blockCategory.strix-advanced-cms → “Strix Advanced CMS”
-   sw-cms.elements.strix-latest-orders.label → “Strix: Latest Orders”
-   sw-cms.blocks.strix-block-latest-orders.label → “Strix: Latest Orders”

Storefront:

-   strix.account.ShowAllOrders → “Show all orders”
-   strix.cart.discountTotal → "Discount"
-   listing.actions.results.label → “results“

## Troubleshooting (quick)

-   Block category missing in admin:
    -   Rebuild administration after installing/renaming the plugin: bin/console administration:build
    -   Ensure main.js imports your CMS element and block modules and your admin snippet files.
-   Element renders nothing:
    -   Log in with a customer that has orders, or reduce the configured amount if the shop has very few orders.
-   Cart quantity “0” does not remove items:
    -   Recompile theme after installing: bin/console theme:compile

## Notes

-   Default number of orders shown is **3** (configurable in the element settings).
-   Calculation of the Total and Discount is done in the Twig file → src/Resources/views/storefront/page/checkout/summary.html.twig.
-   No additional CSS, Vue.js, or JavaScript is introduced. The plugin leverages only Twig, PHP, and the Bootstrap framework included with Shopware, ensuring full compatibility with the Shopware core.
