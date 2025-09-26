import CartFlyoutPlugin from "./plugin/cart-flyout/cart-flyout.plugin";
import UspRotatorPlugin from "./plugin/usp-rotator/usp-rotator.plugin";

const PluginManager = window.PluginManager;

PluginManager.register("CartFlyoutPlugin", CartFlyoutPlugin, ".offcanvas-cart");
PluginManager.register(
    "UspRotator",
    UspRotatorPlugin,
    '[data-plugin="UspRotator"]'
);
