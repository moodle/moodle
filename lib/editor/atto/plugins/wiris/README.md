# ![MathType](./pix/logo-mathtype.png) MathType Moodle plugin for Atto

[![Moodle Plugin CI](https://github.com/wiris/moodle-atto_wiris/actions/workflows/moodle-ci.yml/badge.svg)](https://github.com/wiris/moodle-atto_wiris/actions/workflows/moodle-ci.yml)

Type and handwrite mathematical notation in Moodle with [MathType](https://www.wiris.com/en/mathtype/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=Atto) for Atto Editor.

![Wiris mathtype plugin example](pix/snapshot.png)

## Introduction

[MathType](https://www.wiris.com/en/mathtype/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=Atto) is a mathematical **visual (WYSIWYG) editor** containing a large collection of icons nicely organized in thematic tabs in order to create formulas or equations for any web content. Maths and chemistry toolbars are available from different icons in Atto toolbar.

**Note**: MathType can be used for free up to a certain level of uses per natural year. Read [license conditions and prices](https://www.wiris.com/en/pricing/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=Atto) on our website.

## Requirements

The [MathType filter plugin](https://github.com/wiris/moodle-filter_wiris) is required in order to use this plugin.

## Installation

Install the plugin like any other plugin to folder `lib/editor/atto/plugins/wiris`.

You can use git:

```sh
$ git clone https://github.com/wiris/moodle-atto_wiris.git lib/editor/atto/plugins/wiris
```

Alternatively, you can [download the plugin](https://github.com/wiris/moodle-atto_wiris/archive/main.zip) and unzip the file into the Atto plugins folder at `lib/editor/atto/plugins`, and then rename the unzipped folder to `wiris`.

## Releases

Since version 7.24.0, all notable changes to this project are documented in the [CHANGES.md](CHANGES.md) file. You can download any release of this plugin from the [Official Moodle's page](https://moodle.org/plugins/atto_wiris).

## Libraries

This plugin uses the **MathType Web Integration JavaScript SDK** ([@wiris/mathtype-html-integration-devkit](https://www.npmjs.com/package/@wiris/mathtype-html-integration-devkit)), released under GNU GPLv3 license.

The library's source code can be found at [@wiris/html-integrations](https://github.com/wiris/html-integrations) repository.

**Note:** More details on the `thirdpartylibs.xml` file.

## Contributing

We would love for you to contribute to this project and help make it better.

As a contributor, the guidelines we would like you to follow are documented in the [CONTRIBUTING.md](CONTRIBUTING.md) file.

> :warning: Make sure to disable `YUI Combo Loading` and `Cache Javascript` Moodle options.

### Source code

The Javascript source code of this Atto editor's button is located at `yui/src/button/button.js`, following Moodle's Atto development conventions.

If you plan to make changes on this code you will need to rebuild the Javascript files used by Moodle, in order to be executed.

Then, you will be able to use `grunt` to rebuild this plugin's source code to a set of Javascript files on the `yui/build/moodle-atto_wiris-button` folder.

```sh
# Run this command on the 'yui' folder of this project.
$ grunt
```

More information at [Running grunt](https://docs.moodle.org/dev/Grunt#Running_grunt) on Moodle's documentation site.

### Hot reload with Shifter

Shifter is a tool that takes the source JavaScript file `yui/src/button/button.js` and rebuilt the plugin's code each time you make changes to the source file.

To install shifter globally with NPM:

```sh
npm install shifter@0.4.6 -g
```

During development you can run shifter to detect changes to your files. Using a terminal, move into the `yui/src/` and run:

```sh
shifter --watch
```

More information at [YUI/Shifter](https://docs.moodle.org/dev/YUI/Shifter) on Moodle's documentation site.

## Further information

- [Official plugin in Moodle's website](https://moodle.org/plugins/atto_wiris).
- [Grunt page at Moodle's documentation website](https://docs.moodle.org/dev/Grunt).
- [Javascript Modules at Moodle's documentation website](https://docs.moodle.org/dev/Javascript_Modules).
- [MathType Tutorials](https://docs.wiris.com/mathtype/en/user-interfaces/mathtype-web-interface/introductory-tutorials.html?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=Atto).

## Technical Support

If you have questions or need help integrating MathType, please contact us (support@wiris.com) instead of opening an issue.

## Privacy policy

The [MathType Privacy Policy](https://www.wiris.com/en/mathtype-privacy-policy/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=Atto) covers the data processing operations for the MathType users. It is an addendum of the company’s general Privacy Policy and the [general Privacy Policy](https://www.wiris.com/en/privacy-policy?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=Atto) still applies to MathType users.

## License

**MathType for Atto** by [WIRIS](https://www.wiris.com/en/terms-of-use/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=Atto) is licensed under the [GNU General Public, License Version 3](https://www.gnu.org/licenses/gpl-3.0.en.html).
