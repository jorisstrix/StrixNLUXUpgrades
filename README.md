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

    A compact, storefront-rendered styleguide you can open in the shop to quickly review tokens and UI components.

    **Highlights**

    - Route: `frontend.page.styleguide` (default path: `/styleguide`)
    - **Always** sent with `X-Robots-Tag: noindex, nofollow` so search engines won’t index it.
    - Enabled/disabled and **path configurable** via plugin settings.
    - Organized into **accordion sections**; each section is a Twig partial you can maintain independently.
    - No custom JS required (only Bootstrap/Shopware defaults). Any interactive components are shown as static examples.

    **Configure**

    - **Administration → Extensions → My extensions → StrixNLUxUpgrades → Config**
        - `styleguideEnabled` (bool): Turn the styleguide on/off per sales channel.
        - `styleguidePath` (text): Override the path (e.g. `/design-system`, `/brand/styleguide`).
    - After changing the path or enabling:
        ```bash
        bin/console cache:clear
        bin/console theme:compile
        ```
    - Open your styleguide at the configured path (default `/styleguide`).

    **What’s included**

    - **Typography**: body, headings (H1–H6), display headings, weights (300/400/600/700), inline emphasis, truncate, font size utilities (`.fs-1`..`.fs-6`), line-height (`.lh-*`), wrap/break, and blockquote.
    - **Links**: defaults & hover notes, helper examples.
    - **Colors**: semantic colors and background subtles (`--bs-*` variables shown).
    - **Buttons & Messages**: button variants/sizes/disabled, and Bootstrap alerts (success/info/warning/danger) with Shopware icons.
    - **Images & Figures**: responsive images, figure/figcaption examples.
    - **Forms**: single text input, selects, checkbox/radio/switch (+ disabled), file input, validation states, **quantity selector** markup (static, no JS).
    - **Breadcrumb & Pagination**: typical storefront usage styles.
    - **Table**: table header/body, actions column, responsive container.
    - **Lists, Tabs & Accordion**: unordered/ordered/inline lists + static examples of nav tabs/pills and accordion.
    - **Badges**: solid badges only (primary/secondary/success/info/warning/danger/dark).
    - **Icons**: Shopware icon examples grouped by purpose; note that Shopware’s pack uses `arrow-*` (not `chevron-*`). Only icons that exist render.
    - **Borders & Radius**: border utilities and radius scale examples.
    - **Elevation & Spacing**: `.shadow-*` levels and spacing scale reference.
    - **Background**: background utilities (grays/semantic) and basic gradient usage.
    - **Utilities**: opacity/visibility, text extras, aspect ratio (`.ratio`), floats/clearfix, and responsive float helpers.

    **Where the files live**

    ```
    custom/plugins/StrixNLUxUpgrades/src/Resources/views/storefront/page/strix-styleguide/
      index.html.twig
      partials/
        _macros.html.twig
        _typography.html.twig
        _links.html.twig
        _colors.html.twig
        _buttons-messages.html.twig
        _images-figures.html.twig
        _forms.html.twig
        _breadcrumb-pagination.html.twig
        _table.html.twig
        _lists-tabs-accordion.html.twig
        _badges.html.twig
        _icons.html.twig
        _borders-radius.html.twig
        _elevation-spacing.html.twig
        _background.html.twig
        _utilities.html.twig
        _load-states.html.twig
    ```

    **Notes**

    - The controller (`StyleguideController`) sets `X-Robots-Tag: noindex, nofollow` on the response so other pages are unaffected.
    - An optional route-alias subscriber can map the configurable path to `frontend.page.styleguide`.
    - Icons reference Shopware’s distributed sprites; if a given icon name doesn’t exist it won’t render—use the names found under `vendor/shopware/storefront/Resources/app/storefront/dist/assets/icon/{default,solid}`.

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

-   **Styleguide not found**
    -   Make sure **Styleguide** is enabled in plugin config and browse to the configured path (default `/styleguide`).
    -   After changing the path: `bin/console cache:clear && bin/console theme:compile`.
    -   Verify the controller service is public and tagged as a controller (it is by default).

---

## Notes

-   Default number of orders shown is **3** (configurable in the element settings).
-   No additional CSS frameworks or heavy libraries are required; the plugin leverages Twig, PHP, and the Bootstrap framework included with Shopware, ensuring compatibility with the Shopware core.
