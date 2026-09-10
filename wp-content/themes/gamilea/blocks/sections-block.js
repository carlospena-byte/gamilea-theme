(function (wp) {
    'use strict';
    const el = wp.element.createElement;
    const { InnerBlocks, useBlockProps } = wp.blockEditor;
    wp.blocks.registerBlockType('gamilea/sections', {
        apiVersion: 3, title: 'GA·MI·LEA · Contenedor de inicio', icon: 'layout', category: 'gamilea',
        supports: { html: false, multiple: false },
        edit: () => el('div', useBlockProps({ className: 'gamilea-editor-sections' }),
            el(InnerBlocks, { template: [['acf/categories'], ['acf/products'], ['acf/steps'], ['acf/brands'], ['acf/newsletter']], templateLock: false })),
        save: () => el(InnerBlocks.Content),
    });
})(window.wp);
