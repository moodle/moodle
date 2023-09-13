OTPHP 9.1.1
--------------
https://github.com/Spomky-Labs/otphp/releases/tag/v9.1.1

Instructions to import WebAuthn into Moodle:

1. Download the latest release from https://github.com/Spomky-Labs/otphp/releases/tag/vx.x.x
   (choose "Source code")
2. Unzip the source code
3. Copy the following files from otphp-x.x/lib/OTPHP into admin/tool/mfa/factor/totp/extlib/OTPHP:
   1. OTP.php
   2. OTPInterface.php
   3. ParameterTrait.php
   4. TOTP.php
   5. TOTPInterface.php

4. Copy the following files from otphp-x.x into admin/tool/mfa/factor/totp/extlib/OTPHP:
   1. LICENSE
   2. composer.json
