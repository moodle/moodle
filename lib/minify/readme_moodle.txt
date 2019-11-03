Description of MatthiasMullie\Minify import into Moodle

1) Download https://github.com/matthiasmullie/minify/archive/1.3.51.zip and unzip

mv minify-1.3.51/src /path/to/moodle/lib/minify/matthiasmullie-minify/
mv minify-1.3.51/data /path/to/moodle/lib/minify/matthiasmullie-minify/

2) Download https://github.com/matthiasmullie/path-converter/archive/1.1.0.zip and unzip

mv path-converter-1.1.0/src/ /path/to/moodle/lib/minify/matthiasmullie-pathconverter/

Local changes applied:

MDL-67115: php 74 compliance - implode() params order. Note this has been fixed upstream
  by https://github.com/matthiasmullie/minify/pull/300 so, whenever this library is updated
  check if the fix is included and remove this note.
