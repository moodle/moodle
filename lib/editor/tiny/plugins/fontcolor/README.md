moodle-tiny_fontcolor
========================

![Release](https://img.shields.io/badge/Release-1.0-blue.svg)
[![Moodle Plugin CI](https://github.com/bfh/moodle-tiny_fontcolor/actions/workflows/moodle-plugin-ci.yml/badge.svg?branch=main)](https://github.com/bfh/moodle-tiny_fontcolor/actions/workflows/moodle-plugin-ci.yml)
[![PHP Support](https://img.shields.io/badge/php-7.4--8.4-blue)](https://github.com/bfh/moodle-tiny_fontcolor/action)
[![Moodle Support](https://img.shields.io/badge/Moodle-4.1--5.0-orange)](https://github.com/bfh/moodle-tiny_fontcolor/actions)
[![License GPL-3.0](https://img.shields.io/github/license/bfh/moodle-tiny_fontcolor?color=lightgrey)](https://github.com/bfh/moodle-tiny_fontcolor/blob/main/LICENSE)
[![GitHub contributors](https://img.shields.io/github/contributors/bfh/moodle-tiny_fontcolor)](https://github.com/bfh/moodle-tiny_fontcolor/graphs/contributors)

## Installation

- Unzip the contents of the zip archive into the Moodle `.../lib/editor/tiny/plugins/fontcolor` directory.
- As a Moodle Admin go to Site Administration -> Plugins -> Text Editors -> TinyMCE editor -> Tiny text color/text background color settings
and add a view color names and color codes for at least on of the setting "Available text colors" or "Available text background colors".
- You may also enable the color picker for text color or background color.
 
If no colors are available and the color picker is disabled then the
menu item and button in the TinyMCE editor will not appear. This is valid for both,
the text color setting and the background color setting.

The color name can be an arbitrary string e.g. Red or Dark Green or whatever you name
your color. The name can be also the "corporate name" e.g. that is used in any style guides
of the corporate identity at your institution.

### Colorscheme

If you want a predefined color scheme, then you may add the json from
the file `colorscheme.json` into the settings `textcolors` and/or `backgroundcolors`
in the plugin settings. This can be done by e.g.

```
UPDATE config_plugins
SET value = '<json_string>'
WHERE plugin = 'tiny_fontcolor' AND name = 'textcolors';
```

### Multilanguage support

Color names may also use language tags for the color names. Text filters
are applied. For example setting black and white with German and English
labels would look like this:

```
[
    {
        "name": "<span class=\"multilang\" lang=\"de\">Schwarz</span><span class=\"multilang\" lang=\"en\">Black</span>",
        "value": "#000000"
    },
    {
        "name": "<span class=\"multilang\" lang=\"de\">Weiss</span><span class=\"multilang\" lang=\"en\">White</span>",
        "value": "#ffffff"
    }
]
```

The value of the `name` property can be copied as it is, in the admin settings area.

The name of the color is used as a tooltip in the editor when hovering
over the appropriate color square.

### Use CSS classes

Since version 1.0 there is a mode to use css classes for defining colors, instead of having
the color code defined directly in the `style` attribute. This feature addresses
the issue [CSS Rules rather than style attribute #18](https://github.com/bfh/moodle-tiny_fontcolor/issues/18).

There are a few things to mention when using css classes over the color codes:

- Export and import courses/activities across Moodle sites will loose the color
  information because the css classes with the colors may not exist on the target
  site. In most use cases you will probably duplicate activities or courses within
  the same instance (course copy, sharing cart). In these cases the colors will also
  work on the copied item.
- Deinstalling the plugin will leave the colors because the css is stored in the
  theme settings.
- The class names are derived from the descriptive color names. Invalid characters
  will be filtered, html is stripped (in case you use multilanguage annotation from
  Moodle).
- CSS classes with the appropriate styles are stored in the custom scss settings
  of all installed themes. Whenever these settings are changed manually, be careful
  not to remove the fontcolor styles. They can be applied again when saving the
  color settings of the plugin in the site administration.

There is no easy switch between the use of css classes and the use of the style
attributes. Whenever you change the setting, remember that existing content is
not changed. When editing the content, the plugin will only change the colors in
the specified mode.

## Version History

### 1.0
- New setting `usecssclassnames` that allow to have css clases instead of using color codes
  in the `style` attribute.
- Use a color picker for defining colors in the admin settings.
- Allow to define an alphachannel for the color codes in the admin settings.

### 0.10
- Adjust capability context level according to [MDL-84884](https://tracker.moodle.org/browse/MDL-84884)

### 0.9

- Adjustments for Moodle 5.0.
- Remove Github actions to sync master and main branch.

### 0.8

- Adjustments for Moodle 4.5

### 0.7
- Fix [HTML areas without files seem to fail with the tiny_fontcolor enabled #16](https://github.com/bfh/moodle-tiny_fontcolor/issues/16) and 
[TinyMCE fails when creating a qtype_multichoice question #17](https://github.com/bfh/moodle-tiny_fontcolor/issues/17). The introduced context menu items in 0.6 didn't show up under
some conditions (e.g. in mc questions for answers and feedback) when there was no previously
defined contextmenu.

### 0.6
- [Add quickbar support #14](https://github.com/bfh/moodle-tiny_fontcolor/issues/14)
by [Thomas Ludwig](https://github.com/tholudwig)

### 0.5
- Add support for Moodle 4.4 and PHP 8.3.
- Add json for a comprehensive color scheme (thanks to Joseph Rézeau).

### 0.4
- Add CI stack for Moodle 4.3

### 0.3
- Fix CI issue: (#1) HTML Validation info, line 10: Trailing slash on void elements has no effect and interacts badly with unquoted attribute values.
- [Fix behat by switching to trait class](https://github.com/bfh/moodle-tiny_fontcolor/pull/12)
by [Jason Platts](https://github.com/jason-platts)
- [Preparing for PHP 8.2 Support](https://github.com/bfh/moodle-tiny_fontcolor/pull/13)
by [Luca Bösch](https://github.com/lucaboesch)
- Lifted software maturity to stable

### 0.2.3
- Lift software maturity level to RC.
- Fix issue [The close button of the color picker can't be reach by keyboard](https://github.com/bfh/moodle-tiny_fontcolor/issues/10)

### 0.2.2

- Lift software maturity level to STABLE.
- Adapt CI to test against Moodle 4.2.
- Fix example JSON in mustache templates and make CI have templates checked.
- Fix issue [Probably, $string['helplinktext'] = 'Font colour'; is needed in the lang strings](https://github.com/bfh/moodle-tiny_fontcolor/issues/6).

### 0.2.1

- Add behat test for the admin settings page and reorganize tests.
- Remove function `str_contains` to be PHP7.x compliant.
- Change maturity of plugin to release candidate.
- Privacy Provider was added.

### 0.2.0
Initial release
