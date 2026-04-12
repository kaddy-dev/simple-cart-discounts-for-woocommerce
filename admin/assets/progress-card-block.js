(function (wp) {
    const { registerBlockType } = wp.blocks;

    registerBlockType('discounts-cart/progress-card', {
        title: 'Discounts cart - Progress block',
        description: 'Discounts cart - Progress block',
        category: 'common',
        icon: 'smiley',
        supports: {
            html: false,
        },
        edit: function () {
            return (
                wp.element.createElement(
                    'div',
                    { className: 'dcw-progress-card' },
                    'Discounts cart - Progress block'
                )
            );
        },
        save: function () {
            return null;
        },
    });
})(window.wp);