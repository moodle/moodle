Fonts
-----

GD font operations need unicode capable font to do the server-side
rendering of text. Default font lib/default.ttf (FreeSans Medium
revision 1.76 from http://savannah.nongnu.org/projects/freefont/)
is suitable for most languages.

Unsupported languages may add special font
lang/xx/fonts/default.ttf, it will be used for that language
only.

If you want to override the default font dirroot/lib/default.ttf,
save another one as dataroot/lang/default.ttf - it will
be used for all site languages without it's font file.

The list of suitable TrueType fonts can be found at:
* http://en.wikipedia.org/wiki/Unicode_fonts
* http://www.alanwood.net/unicode/fonts.html#general
