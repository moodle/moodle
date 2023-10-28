This library was originally published by the IMS at https://code.google.com/p/ims-dev/ which no longer exists. The
current code was taken from https://github.com/jfederico/ims-dev/tree/master/basiclti/php-simple/ims-blti - with
several changes to the code (including bug fixes). As the library is no longer supported upgrades are not possible.
In future releases we should look into using a supported library.

2022-01-05 - MDL-73502 - Removed get_magic_quotes_gpc() use, was returning false since ages ago.
2022-01-20 - MDL-73523 - Conditional openssl_free_key() use, deprecated by PHP 8.0
2022-03-05 - MDL-73520 - replace deprecated php_errormsg with error_get_last(), deprecated by PHP 8.0
2023-05-03 - MDL-77840 - Throw exception on openssl_sign to avoid null reaching base64_encode, deprecated by PHP 8.1
