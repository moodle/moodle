Constant-Time Encoding
--------------

Instructions to import Constant-Time Encoding into Moodle:

1. Download the latest release from https://github.com/paragonie/constant_time_encoding/releases/tag/vx.x.x
   (choose "Source code")
2. Unzip the source code
3. Copy the following files from constant_time_encoding-x.x/src into admin/tool/mfa/factor/totp/extlib/ParagonIE/ConstantTime:
   1. Base32.php
   2. Binary.php
   3. EncoderInterface.php

4. Copy the following files from constant_time_encoding-x.x into admin/tool/mfa/factor/totp/extlib/ParagonIE/ConstantTime:
   1. LICENSE
   2. README.md
   3. composer.json
