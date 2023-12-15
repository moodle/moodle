Description of the import of libraries associated with the Atto editor.

    * Download the latest stable release from https://github.com/timdown/rangy/releases ("rangy-X.Z.Y"
      rather than the "Source code" version)
    * Delete all files in yui/src/rangy/js
    * Copy the content of the 'currentrelease/uncompressed' folder into yui/src/rangy/js
    * Patch out the AMD / module support from rangy (because we are loading it with YUI)
      To do this - change the code start of each js file except rangy-core.js to look like (just delete the other lines):

(function(factory, root) {
    // No AMD or CommonJS support so we use the rangy property of root (probably the global variable)
    factory(root.rangy);
})(function(rangy) {

    * rangy-core.js should look like this:

(function(factory, root) {
    // No AMD or CommonJS support so we place Rangy in (probably) the global variable
    root.rangy = factory();
})(function() {

    * Run shifter against yui/src/rangy
