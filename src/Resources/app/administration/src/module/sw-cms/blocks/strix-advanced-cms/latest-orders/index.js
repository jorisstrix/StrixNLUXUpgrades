import blockTemplate from "./component/sw-cms-block-strix-block-latest-orders.html.twig";
import preview from "./preview/sw-cms-preview-strix-block-latest-orders.html.twig";

Shopware.Component.register("sw-cms-block-strix-block-latest-orders", {
    template: blockTemplate,
});
Shopware.Component.register("sw-cms-preview-strix-block-latest-orders", {
    template: preview,
});

Shopware.Service("cmsService").registerCmsBlock({
    name: "strix-block-latest-orders",
    category: "strix-advanced-cms",
    label: "Strix: Latest Orders",
    component: "sw-cms-block-strix-block-latest-orders",
    previewComponent: "sw-cms-preview-strix-block-latest-orders",
    defaultConfig: {
        marginBottom: "20px",
        marginTop: "20px",
        marginLeft: "0px",
        marginRight: "0px",
        sizingMode: "boxed",
    },
    slots: {
        content: {
            type: "strix-latest-orders",
            default: {
                config: {
                    numberOfOrders: { source: "static", value: 5 },
                    showStatus: { source: "static", value: true },
                },
            },
        },
    },
});
