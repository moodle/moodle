Description of PHPMailer 6.0.1 library import into Moodle

We now use a vanilla version of phpmailer and do our customisations in a
subclass.

When doing the import we remove directories/files:
.github/
.phan/
docs/
examples/
src/OAuth.php
src/POP3.php
test/
.gitattributes
.gitignore
.php_cs
.scrutinizer.yml
.travis.yml
SECURITY.md
UPGRADING.md
composer.json
get_oauth_token.php
phpdoc.dist.xml
travis.phpunit.xml.dist