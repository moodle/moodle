Description of import of various jQuery libraries into Moodle:

1/ download jQuery JS from http://jquery.com/download/,
   delete old files and edit plugins.php and lib/requirejs/moodle-config.js

2/ download jQuery UI files from http://jqueryui.com/download/all/,
   delete old files and edit plugins.php and lib/requirejs/moodle-config.js
   delete unnecessary files: external folder, index.html, AUTHORS.txt, package.json

3/ download all UI themes and update smoothness theme

4/ run phpunit tests

5/ open http://127.0.0.1/lib/tests/other/jquerypage.php

6/ Update the version of jquery in core_privacy\local\request\moodle_content_writer::write_html_data()

Petr Skoda
