Description of Markdown Extra import into Moodle

Procedure:
* download latest version from http://michelf.ca/projects/php-markdown/
* copy the classes and readme file to lib/markdown/*
* update function markdown_to_html() in weblib.php if necessary,
  note that we require the php files manually for performance reasons
* run phpunit tests

Petr Skoda
