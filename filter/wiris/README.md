# ![MathType](./pix/logo-mathtype.png) MathType Moodle filter plugin by WIRIS

[![Moodle Plugin CI](https://github.com/wiris/moodle-filter_wiris/actions/workflows/moodle-ci.yml/badge.svg)](https://github.com/wiris/moodle-filter_wiris/actions/workflows/moodle-ci.yml)

MathType filter allows Moodle to render MathML using MathType image service. This plugin is part of set [WIRIS math & science](https://moodle.org/plugins/browse.php?list=set&id=66).

MathType image service can be used for **free** up to a certain level of uses per natural year. Please read **license** conditions and prices at [MathType store](https://www.wiris.com/en/pricing/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=integrations).

## Requirements

In order to use MathType and ChemType for editing you need to install this plugin along with one of the following:

- [MathType for Atto](https://github.com/wiris/mooodle-atto_wiris)
- [MathType for TinyMCE 3.x](https://github.com/wiris/moodle-tinymce_tiny_mce_wiris) for Moodle versions under 4.2.
- [MathType for TinyMCE 6.x](https://github.com/wiris/moodle-tinymce6_wiris) for Moodle versions above 4.2.

## Installation

Install the plugin like any other plugin to folder `/filter/wiris`.

You can use git:

```sh
$ git clone https://github.com/wiris/moodle-filter_wiris.git filter/wiris
```

Alternatively, you can [download the plugin](https://github.com/wiris/moodle-filter_wiris/archive/main.zip) and unzip the file into filters folder, and then rename the new folder to `wiris`.

## Releases

Since version 7.24.0, all notable changes to this project are documented in the [CHANGES.md](CHANGES.md) file. You can download any release of this plugin from the [Official Moodle's page](https://moodle.org/plugins/filter_wiris).

## Libraries

The MathType Web Integration JavaScript SDK is released as a npm package: [@wiris/mathtype-html-integration-devkit](https://www.npmjs.com/package/@wiris/mathtype-html-integration-devkit).

This plugin uses the **MathType Web Integration JavaScript SDK** ([@wiris/html-integrations](https://github.com/wiris/html-integrations)), released under GNU GPLv3 license as a npm package: [@wiris/mathtype-html-integration-devkit](https://www.npmjs.com/package/@wiris/mathtype-html-integration-devkit).

> The library's source code can be found at [@wiris/html-integrations](https://www.github.com/wiris/html-integrations) repository

**Note:** More details on the `thirdpartylibs.xml` file.

## Contributing

We would love for you to contribute to this project and help make it better.

As a contributor, the guidelines we would like you to follow are documented in the [CONTRIBUTING.md](CONTRIBUTING.md) file.

## Technical Support

If you have questions or need help integrating MathType, please contact us (support@wiris.com) instead of opening an issue.

## Privacy policy

The [MathType Privacy Policy](https://www.wiris.com/en/mathtype-privacy-policy/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=integrations) covers the data processing operations for the MathType users. It is an addendum of the company’s general Privacy Policy and the [general Privacy Policy](https://www.wiris.com/en/privacy-policy?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=integrations) still applies to MathType users.

## License

MathType filter by [WIRIS](https://www.wiris.com/en/terms-of-use/?utm_source=github&utm_medium=referral&utm_campaign=readme&utm_content=integrations) is licensed under the [GNU General Public, License Version 3](https://www.gnu.org/licenses/gpl-3.0.en.html).
