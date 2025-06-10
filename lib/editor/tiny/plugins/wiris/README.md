# ![MathType](./pix/logo-mathtype.png) MathType Moodle plugin for TinyMCE 6

[![Moodle Plugin CI](https://github.com/wiris/moodle-tinymce_tiny_mce_wiris/actions/workflows/moodle-ci.yml/badge.svg)](https://github.com/wiris/moodle-tinymce_tiny_mce_wiris/actions/workflows/moodle-ci.yml)

Type and handwrite mathematical notation in Moodle with [MathType](https://www.wiris.com/en/mathtype/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=TinyMCE) for TinyMCE 6 editor.

![Wiris mathtype plugin example](pix/snapshot.png)

## Introduction

[MathType](https://www.wiris.com/en/mathtype/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=TinyMCE) is a mathematical **visual (WYSIWYG) editor** containing a large collection of icons nicely organized in thematic tabs in order to create formulas or equations for any web content. **Maths and chemistry** toolbars are available from different icons in TinyMCE toolbar.

**Note**: MathType can be used for free up to a certain level of uses per natural year. Read [license conditions and prices](https://www.wiris.com/en/pricing/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=TinyMCE) on our website.

## Requirements

The [MathType filter plugin](https://github.com/wiris/moodle-filter_wiris) is required in order to use this plugin.

## Installation

Install the plugin like any other plugin to folder `lib/editor/tiny/plugins/wiris`.

You can use git:

```sh
$ cd <your-moodle-root-path>
$ git clone https://github.com/wiris/moodle-tiny_wiris.git lib/editor/tiny/plugins/wiris
```

Alternatively, you can [download the plugin](https://github.com/wiris/moodle-tinymce_tiny_mce_wiris/archive/main.zipx) and unzip the file into the Tiny plugins folder, and then rename the new folder to `wiris`.

## Releases

All notable changes to this project are documented in the [CHANGES.md](CHANGES.md) file. You can download any release of this plugin from the [Official Moodle's page](https://moodle.org/plugins/tiny_wiris).

## Libraries

This plugin uses the **MathType for TinyMCE6** ([@wiris/mathtype-tinymce6](https://www.npmjs.com/package/@wiris/mathtype-tinymce6)), released under GNU GPLv3 license.

The library's source code can be found at [@wiris/html-integrations](https://github.com/wiris/html-integrations) repository.

**Note:** More details on the `thirdpartylibs.xml` file.

## Contributing

We would love for you to contribute to this project and help make it better.

As a contributor, the guidelines we would like you to follow are documented in the [CONTRIBUTING.md](CONTRIBUTING.md) file.

### Source code

The MathType for TinyMCE6 library (@wiris/mathtype-tinymce6) is located at `js` folder.

You can update `@wiris/mathtype-tinymce6` library to its latest version, using these commands:

```sh
# Install project dependencies.
$ npm install
# Update MathType for TinyMCE6 to its latests version.
$ npm run update-mathtype
```

### Update AMD module

You can build all modules in Moodle by using the grunt amd command. To update the amd module from this plugin:
1. Execute `npm install` on the root of the Moodle project.
2. Navigate to `<moodle-root>/lib/editor/tiny/wiris/amd/` and execute:

```
$ npx grunt amd
```

### Development mode

In development mode Moodle will also send the browser the corresponding source map files for each of the JavaScript modules. The source map files will tell the browser how to map the minified source code back to the un-minified original source code so that the original source files will be displayed in the sources section of the browser's development tools.

To enable development mode set the cachejs config value to false in the admin settings or directly in your config.php file:

```
// Prevent JS caching
$CFG->cachejs = false;
```

## Further information

- [Official plugin in Moodle's website](https://moodle.org/plugins/tinymce_tiny_mce_wiris).
- [TinyMCE page at Moodle's documentation website](https://moodledev.io/docs/apis/plugintypes/tiny).
- [MathType Tutorials](https://docs.wiris.com/mathtype/en/user-interfaces/mathtype-web-interface/introductory-tutorials.html?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=TinyMCE).

## Technical Support

If you have questions or need help integrating MathType, please contact us (support@wiris.com) instead of opening an issue.

## Privacy policy

The [MathType Privacy Policy](https://www.wiris.com/en/mathtype-privacy-policy/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=TinyMCE) covers the data processing operations for the MathType users. It is an addendum of the company’s general Privacy Policy and the [general Privacy Policy](https://www.wiris.com/en/privacy-policy?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=TinyMCE) still applies to MathType users.

## License

**MathType for TinyMCE6** by [WIRIS](https://www.wiris.com/en/terms-of-use/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=TinyMCE) is licensed under the [GNU General Public, License Version 3](https://www.gnu.org/licenses/gpl-3.0.en.html).
