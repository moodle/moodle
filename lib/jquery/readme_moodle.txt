Description of import of various jQuery libraries into Moodle:

1/ Download compressed and uncompressed jQuery JS from http://jquery.com/download/,
   copy the new version of the jquery-X.Y.Z.js and jquery-X.Y.Z.min.js files
   delete old files
   edit plugins.php and lib/requirejs/moodle-config.js

2/ Download jQuery UI files from http://jqueryui.com/download/all/,
   copy the folder ui-A.B.C, with the new version of the JQuery UI library
   delete old files
   edit plugins.php and lib/requirejs/moodle-config.js
   delete unnecessary files: external folder, index.html, AUTHORS.txt, package.json

3/ Download all UI themes,
   create the theme folder into ui-A.B.C
   copy the smoothness theme

4/ Update the version of jquery in core_privacy\local\request\moodle_content_writer::write_html_data() and privacy/templates/htmlpage.mustache.

5/ Run phpunit tests

6/ Open http://127.0.0.1/lib/tests/other/jquerypage.php



Note: jQuery.trim() function and :first pseudo-class are deprecated. We use String.prototype.trim() and .first()
in Moodle code instead. Please note that in third party libraries there are still usages of jQuery.trim
for example xhprof and jQuery UI.
