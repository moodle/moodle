const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyPlugin = require("copy-webpack-plugin");

module.exports = {
  output: {
    path: path.resolve(__dirname, "styles/css")
  },
  entry: {
    index: [path.join(path.resolve(__dirname, 'styles'), "scss", "application.scss")]
  },
  mode: "production",
  module: {
    rules: [
      {
        test: /\.s[ac]ss$/i,
        use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader']
      },
      {
        test: /\.(woff(2)?|ttf|eot|svg)$/,
        type: 'asset/resource',
        generator: {
          filename: './fonts/[name][ext]'
        }
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "application.css"
    }),
    new CopyPlugin({
      patterns: [
        {
          from: path.join(path.resolve(__dirname, 'node_modules'), 'h5p-image-cropper', 'cropper.js'),
          to: path.join(path.resolve(__dirname, 'libs'), 'cropper.js')
        },
        {
          from: path.join(path.resolve(__dirname, 'node_modules'), 'h5p-image-cropper', 'cropper.css'),
          to: path.join(path.resolve(__dirname, 'libs'), 'cropper.css')
        },
        {
          from: path.join(path.resolve(__dirname, 'node_modules'), 'h5p-image-cropper', 'images'),
          to: path.join(path.resolve(__dirname, 'images'), 'cropper')
        },
        {
          from: path.join(path.resolve(__dirname, 'node_modules'), 'zebra_datepicker', 'dist', 'zebra_datepicker.min.js'),
          to: path.join(path.resolve(__dirname, 'libs'), 'zebra_datepicker.min.js')
        },
        {
          from: path.join(path.resolve(__dirname, 'node_modules'), 'zebra_datepicker', 'dist', 'css', 'bootstrap', 'zebra_datepicker.min.css'),
          to: path.join(path.resolve(__dirname, 'styles'), 'css', 'libs', 'zebra_datepicker.min.css')
        },
        {
          from: path.join(path.resolve(__dirname, 'node_modules'), 'zebra_datepicker', 'dist', 'css', 'bootstrap', 'icons.png'),
          to: path.join(path.resolve(__dirname, 'styles'), 'css', 'libs', 'icons.png')
        }
      ]
    })
  ]
};
