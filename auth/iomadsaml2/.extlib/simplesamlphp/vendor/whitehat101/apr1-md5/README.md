# Apache's APR1 MD5 Hashing Algorithm in PHP
[![Build Status](https://travis-ci.org/whitehat101/apr1-md5.svg)](https://travis-ci.org/whitehat101/apr1-md5)

There is no way that the best way to generate Apache's apr1-md5 password hashes is from a 7-year-old comment on php.net. Only a n00b would trust a crypto algorithm from a non-security website's forum. Sadly, that is how the PHP community has accessed this algorithm, until now.

Here is a tested, referenced, documented, and packaged implementation of Apache's APR1 MD5 Hashing Algorithm in pure PHP.

## Install

composer.json:
```json
{
    "require": {
        "whitehat101/apr1-md5": "~1.0"
    }
}
```

## Use

```php
use WhiteHat101\Crypt\APR1_MD5;

// Check plaintext password against an APR1-MD5 hash
echo APR1_MD5::check('plaintext', '$apr1$PVWlTz/5$SNkIVyogockgH65nMLn.W1');

// Hash a password with a known salt
echo APR1_MD5::hash('PASSWORD', '__SALT__');

// Hash a password with a secure random salt
echo APR1_MD5::hash('PASSWORD');

// Generate a secure random salt
echo APR1_MD5::salt();
```

The ideal `__SALT__` is an 8 character string. Valid salts are alphanumeric and `.` or `/`. Shorter salts are allowed. Longer salts are truncated after the 8th character.

## Generate Hashes via Other Tools

### htpasswd
```bash
$ htpasswd -nmb apache apache
apache:$apr1$rOioh4Wh$bVD3DRwksETubcpEH90ww0

$ htpasswd -nmb ChangeMe1 ChangeMe1
ChangeMe1:$apr1$PVWlTz/5$SNkIVyogockgH65nMLn.W1

$ htpasswd -nmb WhiteHat101 WhiteHat101
WhiteHat101:$apr1$HIcWIbgX$G9YqNkCVGlFAN63bClpoT/
```

### openssl
```bash
$ openssl passwd -apr1 -salt rOioh4Wh apache
$apr1$rOioh4Wh$bVD3DRwksETubcpEH90ww0

$ openssl passwd -apr1 -salt PVWlTz/5 ChangeMe1
$apr1$PVWlTz/5$SNkIVyogockgH65nMLn.W1

$ openssl passwd -apr1 -salt HIcWIbgX WhiteHat101
$apr1$HIcWIbgX$G9YqNkCVGlFAN63bClpoT/
```

## Testing

```bash
composer install
vendor/bin/phpunit
```
