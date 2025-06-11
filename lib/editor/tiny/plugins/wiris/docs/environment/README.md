# Environment

This project can be run in two different environments. To run in a development environment, follow the instructions in the [development](#development) section of this very document. If what you need are instructions about how to use it in a production environment, please head to the [usage documentation](../usage/README.md#production).

## Requirements

To use the MathType for TinyMCE editor's MathType and ChemType toolbar buttons for editing and generating mathematical expressions, you need to have an environment for a Moodle instance:

There is a clean environment Docker provided by the official Moodle team:

- https://github.com/moodlehq/moodle-docker

With the previous environment, you also need [MathType Moodle filter plugin](https://github.com/wiris/moodle-filter_wiris) installed in order to use this plugin.

## Development

### Install MathType Moodle plugin for TinyMCE

Install the plugin like any other plugin in the folder `lib/editor/tiny/plugins/wiris`.

You can use git:

```sh
$ git clone https://github.com/wiris/moodle-tiny_wiris.git lib/editor/tiny/plugins/wiris
```

Alternatively, you can [download the plugin](https://github.com/wiris/moodle-tiny_wiris/archive/main.zip) and unzip the file into previous folder, then rename the new folder to `wiris`.

## Dependencies of MathType Moodle plugin for TinyMCE

This project contains the following external dependency:

* MathType Web Integration for TinyMCE.

The **MathType Web Integration for TinyMCE** is open source ([@wiris/html-integrations](https://github.com/wiris/html-integrations/tree/master/packages/tinymce6)) and is released under GNU GPLv3 license as a npm package: [@wiris/mathtype-tinymce6](https://www.npmjs.com/package/@wiris/mathtype-tinymce6).

**Note:** More details on the `thirdpartylibs.xml` file.
