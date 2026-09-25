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
   4. TOTP.php
   5. TOTPInterface.php

4. Copy the following directory from otphp-x.x/src into admin/tool/mfa/factor/totp/extlib/OTPHP:
   1. Exception/

5. Copy the following files from otphp-x.x into admin/tool/mfa/factor/totp/extlib/OTPHP:
   1. LICENSE
   2. composer.json

Note: since 11.4.0, upstream folded the old ParameterTrait into OTP.php directly, so that
file no longer exists upstream and must not be copied. OTP.php/TOTP.php now throw exceptions
from the OTPHP\Exception namespace, which is why that directory must be vendored too - see
the require_once calls in classes/factor.php.
