# GeoPattern

[![npm version][npm-image]][npm-url]
[![build status][travis-image]][travis-url]
[![downloads][downloads-image]][npm-url]

This is a JavaScript port of [jasonlong/geo_pattern](https://github.com/jasonlong/geo_pattern) with a [live preview page](http://btmills.github.io/geopattern/) and is derived from the background generator originally used for [GitHub Guides](http://guides.github.com/).

## Usage

### Web

Include the [minified script](https://github.com/btmills/geopattern/releases/download/v1.2.3/geopattern-1.2.3.min.js) from your server. jQuery is optional.

```html
<script src="js/jquery.min.js"></script> <!-- optional -->
<script src="js/geopattern.min.js"></script>
```

Or reference it from a [CDN](https://cdnjs.com/libraries/geopattern).

```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script> <!-- optional -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/geopattern/1.2.3/js/geopattern.min.js"></script>
```

Use either the `GeoPattern` browser global or the jQuery plugin:

```js
// Use the global...
var pattern = GeoPattern.generate('GitHub');
$('#geopattern').css('background-image', pattern.toDataUrl());

// ...or the plugin
$('#geopattern').geopattern('GitHub');
```

For backwards compatibility with the script on the [Guides](http://guides.github.com/), the source hash for generation can be supplied with a `data-title-sha` attribute on the element. If the attribute exists, the generator will ignore the input string and use the supplied hash.

To run on Internet Explorer 9, the GeoPattern script requires polyfills for [`window.btoa()`](https://github.com/btmills/geopattern/blob/gh-pages/js/base64.min.js) and [`Uint32Array`](https://github.com/btmills/geopattern/blob/gh-pages/js/typedarray.js).

View [`index.html` on the `gh-pages` branch](https://github.com/btmills/geopattern/blob/gh-pages/index.html) for a complete example.

### Node.js

```bash
npm install geopattern
```

After requiring `geopattern`, the API is identical to the browser version, minus the jQuery plugin.

```js
var GeoPattern = require('geopattern');
var pattern = GeoPattern.generate('GitHub');
pattern.toDataUrl(); // url("data:image/svg+xml;...
```

PS - If you are going to use **Webpack** (or any other bundler) to bundle `geopattern` and it will be used in a browser, ignore `buffer` shim from the bundling to decrease its size. See [#32](https://github.com/btmills/geopattern/issues/32) for more details. 


### API

#### GeoPattern.generate(string, options)

Returns a newly-generated, tiling SVG Pattern.

- `string` Will be hashed using the SHA1 algorithm, and the resulting hash will be used as the seed for generation.

- `options.color` Specify an exact background color. This is a CSS hexadecimal color value.

- `options.baseColor` Controls the relative background color of the generated image. The color is not identical to that used in the pattern because the hue is rotated by the generator. This is a CSS hexadecimal color value, which defaults to `#933c3c`.

- `options.generator` Determines the pattern. [All of the original patterns](https://github.com/jasonlong/geo_pattern#available-patterns) are available in this port, and their names are camelCased.

#### Pattern.color

Gets the pattern's background color as a hexadecimal string.

```js
GeoPattern.generate('GitHub').color // => "#455e8a"
```

#### Pattern.toString() and Pattern.toSvg()

Gets the SVG string representing the pattern.

#### Pattern.toBase64()

Gets the SVG as a Base64-encoded string.

#### Pattern.toDataUri()

Gets the pattern as a data URI, i.e. `data:image/svg+xml;base64,PHN2ZyB...`.

#### Pattern.toDataUrl()

Gets the pattern as a data URL suitable for use as a CSS `background-image`, i.e. `url("data:image/svg+xml;base64,PHN2ZyB...")`.

## License

Licensed under the terms of the MIT License, the full text of which can be read in [LICENSE](LICENSE).


[downloads-image]: https://img.shields.io/npm/dm/geopattern.svg?style=flat-square
[npm-image]: https://img.shields.io/npm/v/geopattern.svg?style=flat-square
[npm-url]: https://www.npmjs.com/package/geopattern
[travis-image]: https://img.shields.io/travis/btmills/geopattern/master.svg?style=flat-square
[travis-url]: https://travis-ci.org/btmills/geopattern
