Fonts
-----

This directory contains fonts that are used when creating 
images with text.

The only one used currently is default.ttf

If a language doesn't have the font here then the one in
/lang/en/fonts/default.ttf is used instead.

Multibyte strings will need decoding, because the Truetype 
routines expect ISO fonts or Unicode strings.  If there is a 
file here called lang_decode.php, containing a function 
called lang_decode(), then it will be used on each string.

