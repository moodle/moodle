const plugin = require('tailwindcss/plugin');

module.exports = {
    prefix: 'xp-',
    important: '.block_xp',
    content: [
        './renderer.php',
        './templates/**/*.mustache',
        './classes/form/**/*.php',
        './classes/local/controller/**/*.php',
        './classes/local/shortcode/handler.php',
        './classes/rule_*.php',
        './css/safelist.txt',
        './ui/src/**/*.{js,ts,tsx}',
    ],
    theme: {
        extend: {
            animation: {
                'badge-incoming': 'pulse-in 500ms ease-in-out 2000ms both',
                'badge-leaving': [
                    'fade-in 300ms 1.5s backwards',
                    'wiggle 200ms ease-in-out 2.5s 1',
                    'scale-out 500ms ease-in-out 3.5s forwards'
                ].join(', '),
                'badge-ping': 'ping-sm 2s cubic-bezier(0, 0, 0.2, 1) infinite 4000ms',
                'fade-in': 'fade-in 300ms both'
            },
            flex: {
                "0": '0 0 auto',
                "2": '2 2 0%',
            },
            fontSize: {
                '2xs': ['0.6875rem', '1'],
                '3xs': ['0.6875rem', '1']
            },
            keyframes: {
                'fade-in': {
                    '0%': {opacity: 0},
                    '100%': {opacity: 1},
                },
                'ping-sm': {
                    '0%': {opacity: 1},
                    '50%, 100%': {
                        transform: 'scale(1.25)',
                        opacity: 0
                    }
                },
                'pulse-in': {
                    '0%': {
                        transform: 'scale(0.8)',
                        opacity: '0',
                    },
                    '50%': {
                        opacity: '.5',
                        transform: 'scale(1)',
                    },
                    '80%': {
                        opacity: '1',
                        transform: 'scale(1.25)',
                    },
                    '100%': {
                        transform: 'scale(1)',
                    },
                },
                'scale-out': {
                    '0%': {
                        opacity: '1',
                    },
                    '100%': {
                        opacity: 0,
                        transform: 'scale(2)',
                    },
                },
                wiggle: {
                    '0%, 100%': {transform: 'rotate(-3deg)'},
                    '50%': {transform: 'rotate(3deg)'}
                },
            },
            maxWidth: (theme) => theme('spacing'),
            minHeight: (theme) => theme('spacing'),
            minWidth: (theme) => theme('spacing'),
            spacing: {
                full: '100%',
            },
            transitionProperty: {
                'height': 'height'
            }
        },
    },
    corePlugins: {
    // Older versions of Moodle do not understand rgb(... / opacity).
        backgroundOpacity: false,
        borderOpacity: false,
        divideOpacity: false,
        ringOpacity: false,
        textOpacity: false,
        // Removes the @base.
        preflight: false,
        // Divide, space breaks compatibility with older Moodle.
        divideWidth: false,
        space: false,
    },
    plugins: [
    // Redefine the 'space' plugin because Moodle 3.11 (and older most likely) do not
    // properly parse its generated CSS. This disables the utility `space-[x/y]-reverse`.
        plugin(function({matchUtilities, theme, variants}) {
            matchUtilities(
                {
                    'space-x': (value) => {
                        value = value === '0' ? '0px' : value;
                        return {
                            '& > :not([hidden]) ~ :not([hidden])': {
                                'margin-right': `0`,
                                'margin-left': `${value}`,
                            },
                        };
                    },
                    'space-y': (value) => {
                        value = value === '0' ? '0px' : value;
                        return {
                            '& > :not([hidden]) ~ :not([hidden])': {
                                'margin-top': `${value}`,
                                'margin-bottom': `0`,
                            },
                        };
                    },
                },
                {
                    values: theme('space'),
                    variants: variants('space'),
                    type: 'any',
                }
            );
        }),
        // Redefine the 'divide' plugin because Moodle 3.11 (and older most likely) do not
        // properly parse its generated CSS. This disables the utility `divide-[x/y]-reverse`.
        plugin(function({matchUtilities, theme}) {
            matchUtilities(
                {
                    'divide-x': (value) => {
                        value = value === '0' ? '0px' : value;
                        return {
                            '& > :not([hidden]) ~ :not([hidden])': {
                                'border-style': 'solid',
                                'border-width': '0',
                                'border-right-width': `${value}`,
                            },
                        };
                    },
                    'divide-y': (value) => {
                        value = value === '0' ? '0px' : value;
                        return {
                            '& > :not([hidden]) ~ :not([hidden])': {
                                'border-style': 'solid',
                                'border-width': '0',
                                'border-top-width': `${value}`,
                            },
                        };
                    },
                },
                {
                    values: theme('divideWidth'),
                    type: ['line-width', 'length', 'any']
                }
            );
        }),
        plugin(function({addVariant}) {
            addVariant('supports-hover', '@media (hover: hover) and (pointer: fine)');
        }),
        plugin(function({matchUtilities}) {
            matchUtilities({
                'animation-delay': (value) => ({
                    animationDelay: value
                })
            });
        }),
    ],
};
