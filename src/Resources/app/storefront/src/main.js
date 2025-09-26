import CartFlyoutPlugin from "./plugin/cart-flyout/cart-flyout.plugin";

const PluginManager = window.PluginManager;

PluginManager.register("CartFlyoutPlugin", CartFlyoutPlugin, ".offcanvas-cart");
