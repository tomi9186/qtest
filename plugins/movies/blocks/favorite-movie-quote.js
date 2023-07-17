wp.blocks.registerBlockType('movies/favorite-quote', {
    title: 'Favorite Movie Quote',
    icon: 'format-quote',
    category: 'common',
    attributes: {
        quote: {
            type: 'string',
            source: 'text',
            selector: '.movie-quote',
        },
    },
    edit: function(props) {
        function onChangeQuote(event) {
            props.setAttributes({ quote: event.target.value });
        }

        return wp.element.createElement(
            'div',
            null,
            wp.element.createElement('input', {
                type: 'text',
                value: props.attributes.quote,
                onChange: onChangeQuote,
            })
        );
    },
    save: function(props) {
        return wp.element.createElement(
            'p',
            { className: props.className },
            props.attributes.quote
        );
    },
});