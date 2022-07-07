Description of Markdown Extra import into Moodle

Procedure:
* download latest version from http://michelf.ca/projects/php-markdown/
* copy the classes and readme file to lib/markdown/* , Note .inc files need not be copied.
* update function markdown_to_html() in weblib.php if necessary,
  note that we require the php files manually for performance reasons
* reapply the following commit if it's not present in the release we are upgrading to:
      https://github.com/michelf/php-markdown/commit/a35858f0409e5f01474f5cd562d17289fe8e5435
      (if there is any problem you can, alternatively, reapply MDL-66964 php fix commit itself))
  (revove this step once the release includes the commit)

* run phpunit tests (all PHP versions)

Petr Skoda

* Currently using the 1.8.0 release + (php74 fixup commit)

Mathew May
