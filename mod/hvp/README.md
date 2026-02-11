# H5P Moodle Plugin

Create and add rich content inside your LMS for free. Some examples of what you
get with H5P are Interactive Video, Quizzes, Collage and Timeline.

## Usage

If you intend to use the repository directly in production, make sure that you're using the "Stable" branch, as this is the production branch.
There are no guarantees for the state of the other branches at any given time.
Also make sure that all submodules are pulled as well using:

```
git submodule update --init
```

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

### Development Version
Warning! Never use the development version in production, there are no guarantees for which state the development branches are in at a given time.

Inside your `moodle/mod` folder you run the following command:
```
git clone -b master https://github.com/h5p/h5p-moodle-plugin.git hvp && cd hvp && git submodule update --init
```

### Enabling The Plugin
In Moodle, go to administrator -> plugin overview, and press 'Update database'.

## Settings
Settings can be found at: Site Administration -> Plugins -> Activity Modules -> H5P

## Contributing
Feel free to contribute by:
* Submitting translations to the [Moodle AMOS translator](https://lang.moodle.org/local/amos/view.php)
* Testing and creating issues. But remember to check if the issues is already
reported before creating a new one. Perhaps you can contribute to an already
existing issue?
* Solving issues and submitting code through Pull Requests to the 'master' branch or on a separate feature branch.
