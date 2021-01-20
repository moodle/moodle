Description of MatthiasMullie\Minify import into Moodle

1) Download https://github.com/matthiasmullie/minify/archive/1.3.51.zip and unzip

mv minify-1.3.51/src /path/to/moodle/lib/minify/matthiasmullie-minify/
mv minify-1.3.51/data /path/to/moodle/lib/minify/matthiasmullie-minify/

2) Download https://github.com/matthiasmullie/path-converter/archive/1.1.0.zip and unzip

mv path-converter-1.1.0/src/ /path/to/moodle/lib/minify/matthiasmullie-pathconverter/

Local changes applied:

MDL-68191: https://github.com/matthiasmullie/minify/issues/317 is a bug that stops
  large sections of the CSS from being minimised, and also is a huge performance drain.
  We have applied the fix sent upstream because the performance win is so big.
  (E.g. one case I measured, with the bug was 40 seconds to minify CSS, with the fix was
  a few seconds. This is one of the reasons Behat runs in the browser are so slow.)
  Whenever this library is updated check if the fix is included and remove this note.
  NOTE: As of 2020/12/08, only the first commit was brought into Moodle


2020-12-07 - Peter Dias
-----------------------
* Removed php74 compliance step as it is now part of the library
* Updated minify to 1.3.63 and pathconverter to 1.1.3