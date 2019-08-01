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

Local changes (to verify/apply with new imports):

- MDL-63967: PHP 7.3 compatibility.
    lib/phpmailer/src/PHPMailer.php: FILTER_FLAG_HOST_REQUIRED is deprecated and
    implied with FILTER_VALIDATE_URL. This was fixed upstream by
    https://github.com/PHPMailer/PHPMailer/pull/1551

- MDL-65749: Applied security patch for mitigating CVE-2018-19296
    https://github.com/PHPMailer/PHPMailer/commit/8e653bb79643abad30ae60b1aad6966c0810b896