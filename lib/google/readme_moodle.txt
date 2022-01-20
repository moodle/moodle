Google APIs Client Library for PHP
==================================

Only the source, LICENSE, README and autoloader files have been kept in this directory:

- Copy /src/Google to /src/Google
- Copy /LICENSE to LICENSE
- Copy /README.md to README.md
- Copy /autoload.php to autoload.php

Here are the files that we have added.

/lib.php

    Is a wrapper to get a Google_Client object with the default configuration
    that should be used throughout Moodle. It also takes care of including the
    required files and updating the include_path.

    Every use of the Google PHP API should always start by requiring this file.
    Apart from the wrapping of Google_Client above... it's also responsible for
    enabling the autoload of all the API classes.

    So, basically, every use of the Google Client API should be something like:

        require_once($CFG->libdir . '/google/lib.php');
        $client = get_google_client();

    And, from there, use the Client API normally. Everything will be autoloaded.

/curlio.php

    An override of the default Google_IO_Curl class to use our Curl class
    rather then their implementation. When upgrading the library the default
    Curl class should be checked to ensure that its functionalities are covered
    in this file.

    This should not ever be used directly. The wrapper above uses it automatically.

Local changes (to reapply until upstream upgrades contain them):
    * MDL-67034 php74 compliance fixes
    * MDL-67115 php74 implode() compliance fixes. This is fixed in upstream library v2.2.4
      (verify that https://github.com/googleapis/google-api-php-client/pull/1683 is applied)
    * MDL-73523 php80 compliance. openssl_xxx_free() methods deprecated. I've been unable to
      find any issue upstream and the current library versions are way different from the ones
      we are using here.

Information
-----------

Repository: https://github.com/google/google-api-php-client
Documentation: https://developers.google.com/api-client-library/php/
Global documentation: https://developers.google.com

Downloaded version: 1.1.7
