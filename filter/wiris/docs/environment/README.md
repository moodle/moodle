# Environment

This project can be run in two different environments. To run in a development environment, follow the instructions in the [development](#development) section of this very document. If what you need are instructions about how to use it in a production environment, please head to the [usage documentation](../usage/README.md#production).

## Requirements

In order to use MathType and ChemType for editing and generate mathematical expressions, you need to install this plugin along with one of the following:

- [MathType for Atto](https://github.com/wiris/mooodle-atto_wiris) for Moodle versions under 5.0.
- [MathType for TinyMCE 3.x](https://github.com/wiris/moodle-tinymce_tiny_mce_wiris) for Moodle versions under 4.2.
- [MathType for TinyMCE 6.x](https://github.com/wiris/moodle-tinymce6_wiris) for Moodle versions above 4.2.

> You can also generate MathML and/or LaTeX on your own, and the MathType filter will render it.

## Development

### Install MathType filter

It is necessary to have a [Moodle development environment](https://github.com/moodlehq/moodle-docker) configured. 

Install the plugin like any other plugin in the folder `/filter/wiris`.

You can use git:

```sh
$ git clone https://github.com/wiris/moodle-filter_wiris.git filter/wiris
```

Alternatively, you can [download the plugin](https://github.com/wiris/moodle-filter_wiris/archive/main.zip) and unzip the file into filters folder, then rename the new folder to `wiris`.

### Uninstall MathType filter

This project is a dependency of the following:

* MathType for Atto
* MathType for TinyMCE
* MathType for WirisQuizzes


To uninstall the Wiris filter in Moodle, all the previous plugins mentioned must also be uninstalled. If any of these plugins are still present, the MathType filter cannot be removed.

## Dependencies of MathType filter

This project contains the following external dependencies:

* MathType Web Integration JavaScript SDK.
* MathType Web Integration PHP library.

The **MathType Web Integration JavaScript SDK** is open source ([@wiris/html-integrations](https://github.com/wiris/html-integrations)) and is released under GNU GPLv3 license as a npm package: [@wiris/mathtype-html-integration-devkit](https://www.npmjs.com/package/@wiris/mathtype-html-integration-devkit).

**Note:** More details on the `thirdpartylibs.xml` file.
