OTPHP
--------------

Instructions to import OTPHP into Moodle:

1. Download the latest release from https://github.com/Spomky-Labs/otphp/releases/tag/vx.x.x
   (choose "Source code")
2. Unzip the source code
3. Copy the following files from otphp-x.x/src into admin/tool/mfa/factor/totp/extlib/OTPHP:
   1. InternalClock.php
   2. OTP.php
   3. OTPInterface.php
   4. ParameterTrait.php
   5. TOTP.php
   6. TOTPInterface.php

4. Copy the following files from otphp-x.x into admin/tool/mfa/factor/totp/extlib/OTPHP:
   1. LICENSE
   2. composer.json
