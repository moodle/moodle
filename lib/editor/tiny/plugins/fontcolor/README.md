moodle-tiny_fontcolor
========================

![Release](https://img.shields.io/badge/Release-1.2-blue.svg)
[![Moodle Plugin CI](https://github.com/bfh/moodle-tiny_fontcolor/actions/workflows/moodle-plugin-ci.yml/badge.svg?branch=main)](https://github.com/bfh/moodle-tiny_fontcolor/actions/workflows/moodle-plugin-ci.yml)
[![PHP Support](https://img.shields.io/badge/php-8.1--8.4-blue)](https://github.com/bfh/moodle-tiny_fontcolor/action)
[![Moodle Support](https://img.shields.io/badge/Moodle-4.1--5.1-orange)](https://github.com/bfh/moodle-tiny_fontcolor/actions)
[![License GPL-3.0](https://img.shields.io/github/license/bfh/moodle-tiny_fontcolor?color=lightgrey)](https://github.com/bfh/moodle-tiny_fontcolor/blob/main/LICENSE)
[![GitHub contributors](https://img.shields.io/github/contributors/bfh/moodle-tiny_fontcolor)](https://github.com/bfh/moodle-tiny_fontcolor/graphs/contributors)

## Installation

- Unzip the contents of the zip archive into the Moodle `<moodle_base>/lib/editor/tiny/plugins/fontcolor` directory,
  (`<moodle_base>/public/lib/editor/tiny/plugins/fontcolor` as of Moodle 5.1)
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
- The color picker in the editor is not available when the css classes are in use.
  With the color picker you may use an arbitrary color that is not defined in the
  css classes, and so no corresponding class name can be found.

There is no easy switch between the use of css classes and the use of the style
attributes. Whenever you change the setting, remember that existing content is
not changed. When editing the content, the plugin will only change the colors in
the specified mode.

## Version History

See [Changes](CHANGES.md)
