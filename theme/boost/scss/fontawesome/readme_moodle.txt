Description of Font awesome import into Moodle

Font awesome comes in 3 parts relating to Moodle.

1. The font. Put the woff font in lib/fonts/fontawesome-webfont.woff. Update lib/thirdpartylibs.xml.
2. SCSS. Replace the SCSS in this folder (/theme/boost/scss/fontawesome). Update theme/boost/thirdpartylibs.xml.
3. The @import "path" is commented because we provide the font path differently e.g. "[[font:core|fontawesome-webfont.eot]]"
