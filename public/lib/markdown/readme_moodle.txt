Description of Markdown Extra import into Moodle

Procedure:
* download latest version from https://github.com/michelf/php-markdown/tags
* copy the classes file to lib/markdown/Michelf/* , Note .inc files need not be copied.
* copy the readme file to lib/markdown/.
* update function markdown_to_html() in lib/weblib.php if necessary,
  note that we require the php files manually for performance reasons
* run phpunit tests (all PHP versions)
