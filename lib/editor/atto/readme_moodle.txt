Description of the import of libraries associated with the Atto editor.

1)  Rangy (version 1.2.3)
    * Download the latest stable release;
    * Copy the content of the 'currentrelease/uncompressed' folder into yui/src/rangy/js
    * Run shifter against yui/src/rangy

    Notes:
    * We have patched 1.2.3 with a backport fix from the next release of Rangy which addresses an incompatibility
      between Rangy and HTML5Shiv which is used in the bootstrapclean theme. See MDL-44798 for further information.
