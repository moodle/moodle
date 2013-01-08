Google APIs Client Library for PHP
==================================

Only the source and the README files have been kept in this directory.

The configuration (local_config.php) of this API specifies a different ioClass
than the default one. This new ioClass has been defined in curlio.php and is
named moodle_google_curlio, extending Google_CurlIO. The only reason to do
that was that we could force Google lib to use our implementation of curl.
If you upgrade the library, please check if the method Google_CurlIO::makeRequest()
has been updated and would require change in moodle_google_curlio.

Information
-----------

URL: http://code.google.com/p/google-api-php-client/
Download from: http://code.google.com/p/google-api-php-client/downloads/list
Documentation: http://code.google.com/p/google-api-php-client/w/list
Global documentation: https://developers.google.com

Downloaded version: 0.6.0
