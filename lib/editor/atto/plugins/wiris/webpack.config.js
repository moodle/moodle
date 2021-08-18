const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

module.exports = {
    entry: {
        app: './src/global.js'
    },
    output: {
        path: path.resolve(__dirname, ''),
        filename: 'core.js'
    },
    // Set watch to true for dev purposes.
    watch: false,
    optimization: {
        minimizer: [
            // Javascript optimizer mainly to minimize js files.
            new UglifyJsPlugin({
                cache: true,
                parallel: true,
                sourceMap: true // Set to true if you want JS source maps.
            }),
            // CSS optimizer mainly to minimize css files.
            new OptimizeCSSAssetsPlugin({})
        ]
    },
    module: {
        rules: [
            {
                // Rule to translate ES5 javascript files to ES6.
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/env']
                    }
                }
            },
            {
                test: /\.css$/,
                use: ['style-loader', MiniCssExtractPlugin.loader, 'css-loader']
            },
            {
                test: /\.(png|jpg|gif)$/i,
                use: [
                  {
                    loader: 'url-loader',
                    options: {
                      limit: 8192
                    }
                  }
                ]
            }
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            // Output path for css.
            filename: './styles/styles.css',
        }),
    ],
    stats: {
        colors: true
    }
};
