Google APIs Client Library for PHP
==================================

Only the source and the README files have been kept in this directory:

- Copy /src/Google to /Google
- Copy /LICENSE to LICENSE
- Copy /README.md to README.md

Here are the files that we have added.

/lib.php

    Is a wrapper to get a Google_Client object with the default configuration
    that should be used throughout Moodle. It also takes care of including the
    required files and updating the include_path.

/curlio.php

    An override of the default Google_IO_Curl class to use our Curl class
    rather then their implementation. When upgrading the library the default
    Curl class should be checked to ensure that its functionalities are covered
    in this file.


Information
-----------

Repository: https://github.com/google/google-api-php-client
Documentation: https://developers.google.com/api-client-library/php/
Global documentation: https://developers.google.com

Downloaded version: 1.0.5-beta
