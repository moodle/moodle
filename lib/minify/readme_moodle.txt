Description of MatthiasMullie\Minify import into Moodle

1) Go to from https://github.com/matthiasmullie/minify/releases

Download lastest minify Source code (zip) and unzip
mv minify-X.Y.ZZ/src /path/to/moodle/lib/minify/matthiasmullie-minify/
mv minify-X.Y.ZZ/data /path/to/moodle/lib/minify/matthiasmullie-minify/

2) Go to https://github.com/matthiasmullie/path-converter/releases/A.B.C.zip and unzip

Download lastest path-converter Source code (zip) and unzip
mv path-converter-A.B.C/src/ /path/to/moodle/lib/minify/matthiasmullie-pathconverter/

3) Apply the following patches:

MDL-68191: https://github.com/matthiasmullie/minify/issues/317 is a bug that stops
  large sections of the CSS from being minimised, and also is a huge performance drain.
  We have applied the fix sent upstream because the performance win is so big.
  (E.g. one case I measured, with the bug was 40 seconds to minify CSS, with the fix was
  a few seconds. This is one of the reasons Behat runs in the browser are so slow.)
  Whenever this library is updated check if the fix is included and remove this note.
  NOTE: As of 2020/12/08, only the first commit was brought into Moodle

Note:
 * 2023-02-10 Apply commit https://github.com/matthiasmullie/minify/commit/aa8010c2fa3c26f018874141d36025085754abff
   to avoid PHP 8.2 deprecations. See MDL-76413 for more details.
   Since new version already have this fix if someone executing the upgrading version and
   it has already the patch please ignore this note.
