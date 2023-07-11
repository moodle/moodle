Assert 2.1
--------------
https://github.com/beberlei/assert/releases/tag/v2.1

Instructions to import WebAuthn into Moodle:

1. Download the latest release from https://github.com/beberlei/assert/releases/tag/vx.x
   (choose "Source code")
2. Unzip the source code
3. Copy the following files from assert-x.x/lib/Assert into admin/tool/mfa/factor/totp/extlib/Assert:
   1. Assertion.php
   2. AssertionFailedException.php
   3. InvalidArgumentException.php

4. Copy the following files from assert-x.x into admin/tool/mfa/factor/totp/extlib/Assert:
   1. LICENSE
   2. composer.json
