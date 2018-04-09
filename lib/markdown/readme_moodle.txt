Description of Markdown Extra import into Moodle

Procedure:
* download latest version from http://michelf.ca/projects/php-markdown/
* copy the classes and readme file to lib/markdown/* , Note .inc files need not be copied.
* update function markdown_to_html() in weblib.php if necessary,
  note that we require the php files manually for performance reasons
* pull commits https://github.com/michelf/php-markdown/commit/0c1337a4d483b1e0b66bfdc3ffa644eafd40aa27
  and https://github.com/michelf/php-markdown/commit/251ffcce7582d4b26936679e340abca973d55220
  if they are not included in the next release (or remove this step)
* run phpunit tests

Petr Skoda
