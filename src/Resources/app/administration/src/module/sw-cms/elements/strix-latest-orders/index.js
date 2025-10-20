import template from './component/sw-cms-el-strix-latest-orders.html.twig';
import preview from './preview/sw-cms-el-preview-strix-latest-orders.html.twig';
import './config';

Shopware.Component.register('sw-cms-el-strix-latest-orders', { template });
Shopware.Component.register('sw-cms-el-preview-strix-latest-orders', {
    template: preview,
});

Shopware.Service('cmsService').registerCmsElement({
    name: 'strix-latest-orders',
    label: 'Strix: Latest Orders',
    component: 'sw-cms-el-strix-latest-orders',
    previewComponent: 'sw-cms-el-preview-strix-latest-orders',
    configComponent: 'sw-cms-el-config-strix-latest-orders',
    defaultConfig: {
        numberOfOrders: { source: 'static', value: 3 },
        showStatus: { source: 'static', value: true },
    },
    required: {},
});
