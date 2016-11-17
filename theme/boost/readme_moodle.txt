Description of Twitter bootstrap import into Moodle

Twitter bootstrap
-----------------

Sass:
This theme uses the original unmodified version 4.0.0-alpha-3 Twitter bootstrap sass files.
The bootstrap repository is available on:

https://github.com/twitter/bootstrap.git

To update to the latest release of twitter bootstrap:
* re-apply /* rtl:begin:ignore */ on the top of _popover.scss before .popover rule and /* rtl:end:ignore */ before
  .popover-arrow::after rule. See MDL-56763 commit (1a4faf9b).
* remove all files from scss/bootstrap,
* download the new scss files and store them in scss/bootstrap
* update ./thirdpartylibs.xml

Javascript:

This theme uses the transpiled javascript from bootstrap4 as amd modules.

To update the javascript files:
Checkout the latest branch of bootstrap to a folder, in that folder run:

> mkdir "out"
> npm install babel-cli babel-preset-es2015 babel-plugin-transform-es2015-modules-amd
> ./node_modules/babel-cli/bin/babel.js --presets es2015 --plugins transform-es2015-modules-amd -d out/ js/src/

Copy the transpiled files from out/ into the amd/src/ folder for the theme.
Run grunt to re-compile the JS files.

