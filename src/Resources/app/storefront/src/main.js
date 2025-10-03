import CartFlyoutPlugin from "./plugin/cart-flyout/cart-flyout.plugin";
import UspRotatorPlugin from "./plugin/usp-rotator/usp-rotator.plugin";
import StickyHeaderNavPlugin from "./plugin/sticky-header/sticky-header.plugin";
import StrixStyleguidePlugin from "./plugin/styleguide/styleguide.plugin";

const PluginManager = window.PluginManager;

PluginManager.register("CartFlyoutPlugin", CartFlyoutPlugin, ".offcanvas-cart");
PluginManager.register(
    "UspRotator",
    UspRotatorPlugin,
    '[data-plugin="UspRotator"]'
);
PluginManager.register(
    "StickyHeaderNav",
    StickyHeaderNavPlugin,
    '[data-sticky-header="true"]'
);
PluginManager.register(
    "StrixStyleguide",
    StrixStyleguidePlugin,
    "[data-strix-styleguide]"
);
