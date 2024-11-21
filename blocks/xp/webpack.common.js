const webpack = require('webpack');
const path = require('path');
const WrapperPlugin = require('wrapper-webpack-plugin');

module.exports = {
    target: ['web', 'es2021'],
    entry: {
        'ui-completion-rules': './ui/src/completion-rules.tsx',
        'ui-levels': './ui/src/levels.tsx',
    },
    output: {
        filename: '[name]-lazy.js',
        path: path.resolve(__dirname, './amd/src'),
        libraryTarget: 'amd',
    },
    module: {
        rules: [
            {
                test: /\.tsx?$/,
                use: 'ts-loader',
                exclude: /node_modules/,
            },
        ],
    },
    resolve: {
        extensions: ['.tsx', '.ts', '.js'],
    },
    optimization: {
        splitChunks: {
            cacheGroups: {
                commons: {
                    test: /[\\/]node_modules[\\/]/,
                    name: 'ui-commons',
                    chunks: 'all'
                }
            }
        }
    },
    plugins: [
        // Wrap the ui-commons to make it an AMD module.
        new WrapperPlugin({
            test: /ui-commons-lazy\.js$/,
            header: 'define(() => {\n',
            footer: '\n});'
        }),
        // Without this, Moodle prevents grunt from compiling the files.
        new WrapperPlugin({
            test: /-lazy\.js$/,
            header: '/* eslint-disable */\n/* Do not edit directly, refer to ui/ folder. */\n\n',
            footer: ''
        }),
    ],
};
