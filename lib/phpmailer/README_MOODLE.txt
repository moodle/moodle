Description of PHPMailer 5.1 library import into Moodle

We now use a vanilla version of phpmailer and do our customisations in a
subclass.

When doing the import we remove directories/files:
aboutus.html
class.pop3.php
docs/
examples/
test/

Our changes:
 * Modified EncodeQP() in class.phpmailer.php to not use php's implementation of quoted
   printable encoding as it was causing problems for some users MDL-23240

