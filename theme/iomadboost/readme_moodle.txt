Description of Twitter bootstrap import into Moodle

Twitter bootstrap
-----------------

Sass:
This theme uses the version 4.0.0 Twitter bootstrap sass files.
The bootstrap repository is available on:

https://github.com/twitter/bootstrap.git

To update to the latest release of twitter bootstrap:

* remove all files from scss/bootstrap,
* download the new scss files and store them in scss/bootstrap
* re-apply /* rtl:begin:ignore */ on the top of _popover.scss before .popover rule and /* rtl:end:ignore */ before
  .popover-arrow::after rule. See MDL-56763 commit (1a4faf9b).
* comment out all uses of the @supports syntax in SCSS (see https://github.com/sabberworm/PHP-CSS-Parser/issues/127). In Bootstrap 4.0 The @supports rules are used for carousal transitions (nice sliding) and the .sticky-top helper class. The carousel bootstrap component will still be functional.
* update ./thirdpartylibs.xml

Javascript:

This theme uses the transpiled javascript from bootstrap4 as amd modules.

To update the javascript files:
Checkout the latest branch of bootstrap to a folder, Run the follwing inside the cloned Bootstrap repository:

```
$ npm install @babel/cli@7.0.0-beta.37 @babel/preset-env@7.0.0-beta.37 babel-plugin-transform-es2015-modules-amd @babel/plugin-proposal-object-rest-spread
$ mkdir out
$ ./node_modules/@babel/cli/bin/babel.js --presets @babel/preset-env --plugins transform-es2015-modules-amd,@babel/plugin-proposal-object-rest-spread -d ./out/ js/src/
```

Copy the transpiled files from out/ into the amd/src/ folder for the theme.
Run grunt to re-compile the JS files. (thanks to Joby Harding)