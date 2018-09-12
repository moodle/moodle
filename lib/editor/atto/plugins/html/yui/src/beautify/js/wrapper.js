// JSBeautify adds itself to the exports object if one exists.
// Define exports here and it will work as if by magic.
// This is safe to put in a function and will not be exported to the global
// namespace. Note, we'll have to remove this when YUI supports, and we move to ES6.
var exports = {};

var define = null; // Remove require.js support in this context.

// JSBeautify calls require() in order to get the existing exported modules.
var require = function() {
    return exports;
};
require();

// Actually define beautify in our namespace.
Y.namespace('M.atto_html').beautify = exports;
