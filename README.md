# STRIX UX Upgrades (v1.8.0)

## What this plugin does

1. **Latest Orders CMS element & block**

    - Adds a CMS element `strix-latest-orders` and a block you can place anywhere in Shopping Experiences.
    - Renders the same “Last order” card as on the My Account overview, with the option to show more than 1.
    - Only renders for logged-in customers (nothing is shown to guests or customers without orders).
    - The number of orders to show can be configured in the plugin settings.

2. **Cart quality-of-life** (merged in v1.2.0)

    - Cart items with quantity set to 0 are removed automatically (offcanvas cart + full cart page).  
      Can be activated in the plugin settings.

3. **Sort cart by latest added line item first** (merged in v1.3.1)

    - Shows the latest added line item first in off-canvas cart and cart page (oldest last).

4. **Show the amount of products present in Product listings** (merged in v1.3.1)

    - By default, the number of products found on category listing pages is shown.  
      Can be activated in the plugin settings.

5. **Show the sum of all discounts in cart/checkout** (merged in v1.4.1)

    - When active, the Total line in the cart/checkout summary shows the sum of all items × quantity **before** discount.  
      A new line in the summary is added called **“Discount”** (translatable snippet), which is the sum of all line-item discounts.  
      Can be activated in the plugin settings.

6. **Language selector in the footer** (merged in v1.5.0)

    - When active, the language selector is shown in the footer.  
      Can be activated in the plugin settings.

7. **USP bar above the header** (merged in v1.6.0)

    - When active, the USP bar is shown above the header.  
      Can be activated in the plugin settings.
    - The **duration** and **interval** of the USP bar can be configured in the plugin settings.
    - The USP bar is a carousel that shows up to **3** USPs (default) and can be configured in the plugin settings.
    - The USP bar is responsive and will show **1** USP at a time with animation at the **md** breakpoint (768px).
    - The USP bar is translatable.
    - Background color of the USP bar is set by the variable `--bs-secondary-bg`.

8. **Sticky header** (merged in v1.7.0)

    - When active, the header is sticky.  
      Can be activated in the plugin settings.
    - The sticky header is responsive and will hide the logo below the **lg** breakpoint (992px).
    - Background color of the sticky header is set by the variable `--bs-tertiary-bg`.

9. **Styleguide** (merged in v1.8.0)

    - Storefront page to review core tokens and UI components.
    - Route frontend.page.styleguide (default path /styleguide); always sent with X-Robots-Tag: noindex, nofollow.
    - Config (per sales channel): styleguideEnabled, styleguidePath (override e.g. /design-system).
    - Includes: Typography, Links, Colors, Buttons & Messages, Images & Figures, Forms (incl. quantity), Breadcrumb & Pagination, Table, Lists/Tabs/Accordion, Badges, Icons, Borders & Radius, Elevation & Spacing, Background, Utilities.

---

## Compatibility

-   Shopware **6.7.x**

---

## Installation (local plugin)

1. Place the plugin in: `custom/plugins/StrixNLUxUpgrades`
2. In your project root run:
    ```bash
    composer require strixnl/uxupgrades
    bin/console plugin:refresh
    bin/console plugin:install --activate StrixNLUxUpgrades
    bin/console cache:clear
    bin/console administration:build
    bin/console theme:compile
    ```

---

## How to use the Latest Orders Element in the Admin

1. Go to **Content → Shopping Experiences**.
2. Create or edit a layout.
3. Find the category **“Strix Advanced CMS”** in the block picker.
4. Drag **“Strix: Latest Orders”** onto your layout **or** use it as a replacement element in an existing block.
5. Select the element to open **Settings** and set **Number of orders** (default: **3**).
6. Save the layout and assign it to your categories.

---

## What to expect in the Storefront

-   The element shows the customer’s latest order(s) with statuses, payment/shipping info, and items.
-   A link to **All orders** is shown in the header and displays the customer’s total order count.
-   If the visitor is not logged in or has no orders, the element does not render (no empty box).
-   In the cart, changing an item’s quantity to **0** removes it immediately (no manual refresh required).
-   On the same row as the sorting options, the number of products found on the listing is shown.
-   Everything seen on the storefront is translatable by a (new) snippet.

---

## Translations with default values

**Administration:**

-   `sw-cms.detail.label.blockCategory.strix-advanced-cms` → “Strix Advanced CMS”
-   `sw-cms.elements.strix-latest-orders.label` → “Strix: Latest Orders”
-   `sw-cms.blocks.strix-block-latest-orders.label` → “Strix: Latest Orders”

**Storefront:**

-   `strix.account.ShowAllOrders` → “Show all orders”
-   `strix.cart.discountTotal` → “Discount”
-   `listing.actions.results.label` → “results”
-   `footer.languageList` → “Available languages”

---

## Troubleshooting (quick)

-   **Block category missing in admin**

    -   Rebuild administration after installing/renaming the plugin:  
        `bin/console administration:build`
    -   Ensure `main.js` imports your CMS element/block modules and admin snippet files.

-   **Element renders nothing**

    -   Log in with a customer that has orders, or reduce the configured amount if the shop has very few orders.

-   **Cart quantity “0” does not remove items**
    -   Recompile theme after installing:  
        `bin/console theme:compile`

---

## Notes

-   Default number of orders shown is **3** (configurable in the element settings).
-   No additional CSS frameworks or heavy libraries are required; the plugin leverages Twig, PHP, and the Bootstrap framework included with Shopware, ensuring compatibility with the Shopware core.
