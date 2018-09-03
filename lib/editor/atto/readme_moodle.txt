Description of the import of libraries associated with the Atto editor.

1)  Rangy (version 1.2.3)
    * Download the latest stable release;
    * Delete all files in yui/src/rangy/js
    * Copy the content of the 'currentrelease/uncompressed' folder into yui/src/rangy/js
    * Patch out the AMD / module support from rangy (because we are loading it with YUI)
      To do this - change the code start of each js file to look like (just delete the other lines):

(function(factory, root) {
    // No AMD or CommonJS support so we use the rangy property of root (probably the global variable)
    factory(root.rangy);
})(function(rangy) {

    * Run shifter against yui/src/rangy


    Notes:
    * We have patched 1.2.3 with a backport fix from the next release of Rangy which addresses an incompatibility
      between Rangy and HTML5Shiv which is used in the bootstrapclean theme. See MDL-44798 for further information.
