# H5P Moodle Plugin

This is the 1.0-rc version of the H5P plugin for Moodle. That means that this
version is feature complete but you might encounter bugs or issues that will
not be present in the final version.

Create and add rich content inside your LMS for free. Some examples of what you
get with H5P are Interactive Video, Quizzes, Collage and Timeline.

## Description

One of the great benefits with using H5P is that it gives you access to lots of
different [interactive content types](https://h5p.org/content-types-and-applications).

Another great benefit with H5P is that it allows you to easily share and reuse
content. To reuse content, you just download the H5P you would like to edit and
make your changes – e.g. translate to a new language or adjust it to a new
situation.

H5P is:

* Open Source
* Free to Use
* HTML5
* Responsive

The H5P community is actively contributing to improve H5P. Updates and new
features are continuously made available on the community portal
[H5P.org](https://h5p.org).

View our [setup for Moodle](https://h5p.org/moodle) to get information on how
to get started with H5P.

### GDPR Compliance
Information useful to help you achieve GDPR compliance while using this plugin
can be found at [H5P.org's GDPR Compliance](https://h5p.org/plugin-gdpr-compliance) page.

## Install

### Beta Version
If you can't wait for the final release or simply wish to help test the plugin,
you can download the beta version.
Here is an example. Remember to replace the version number with the latest from
the [releases](https://github.com/h5p/h5p-moodle-plugin/releases) page:
```
git clone --branch 1.0-rc.2 https://github.com/h5p/h5p-moodle-plugin.git hvp && cd hvp && git submodule update --init
```

It's not recommended to download the tag/version from the
[releases](https://github.com/h5p/h5p-moodle-plugin/releases) page as you would
also have to download the appropriate version of
[h5p-php-library](https://github.com/h5p/h5p-php-library/releases) and
[h5p-editor-php-library](https://github.com/h5p/h5p-php-library/releases) to
put inside the `library` and `editor` folders.

### Development Version
Warning! Using the development version may cause strange bugs, so do not use it
for production!

Inside your `moodle/mod` folder you run the following command:
```
git clone https://github.com/h5p/h5p-moodle-plugin.git hvp && cd hvp && git submodule update --init
```

### Enabling The Plugin
In Moodle, go to administrator -> plugin overview, and press 'Update database'.

## Settings

Settings can be found at: Site Administration -> Plugins -> Activity Modules -> H5P

## Contributing

Feel free to contribute by:
* Submitting translations
* Testing and creating issues. But remember to check if the issues is already
reported before creating a new one. Perhaps you can contribute to an already
existing issue?
* Solving issues and submitting code through Pull Requests.
