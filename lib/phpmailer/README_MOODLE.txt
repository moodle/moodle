Description of PHPMailer 5.2.23 library import into Moodle

We now use a vanilla version of phpmailer and do our customisations in a
subclass.

When doing the import we remove directories/files:
aboutus.html
class.pop3.php
class.phpmailer.oauth.php
class.phpmailer.oauthgoogle.php
get_oauth_token.php
.travis.yml
.scrutinizer.yml
composer.json
composer.lock
travis.phpunit.xml.dist
PHPMailerAutoload.php (make sure all files are included in moodle_phpmailer.php)
docs/
examples/
test/
extras/
