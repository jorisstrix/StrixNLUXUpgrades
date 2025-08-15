import template from "./sw-cms-el-config-strix-latest-orders.html.twig";

Shopware.Component.register("sw-cms-el-config-strix-latest-orders", {
    template,
    props: {
        element: { type: Object, required: true },
    },
});
