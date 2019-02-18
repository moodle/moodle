Description of Twitter bootstrap import into Moodle

Twitter bootstrap
-----------------

Sass:
This theme uses the version 4.3.1 Twitter bootstrap sass files.
The bootstrap repository is available on:

https://github.com/twitter/bootstrap.git

To update to the latest release of twitter bootstrap:

* remove all files from scss/bootstrap,
* download the new scss files and store them in scss/bootstrap
* remove left: 0; from .popover {} in scss/bootstrap/_popover.scss. In RTL mode this prevents popovers from showing and it is not required in LTR mode.
* update ./thirdpartylibs.xml

Javascript:

This theme uses the transpiled javascript from bootstrap4 as amd modules.

To update the javascript files:
Checkout the version you are updating to in a folder, Run the follwing inside the cloned Bootstrap repository:

```
$ npm install @babel/cli@7.0.0-beta.41 @babel/preset-env@7.0.0-beta.41 babel-plugin-transform-es2015-modules-amd @babel/plugin-proposal-object-rest-spread
$ mkdir out
$ ./node_modules/@babel/cli/bin/babel.js --presets @babel/preset-env --plugins transform-es2015-modules-amd,@babel/plugin-proposal-object-rest-spread -d ./out/ js/src/
```

Copy the transpiled files from out/ into the amd/src/ folder for the theme.

Moodle core includes the popper.js Library, so make sure each of the new js files references the "core/popper" library instead of "popper.js".
For version 4.3.1 these files were: tooltip.js and dropdown.js

Move the amd/src/tools/sanatizer.js to amd/src/sanatizer.js and update libraries including sanatizer.js.
For version 4.3.1 this file was: tooltip.js

Run grunt to re-compile the JS files. (thanks to Joby Harding)