AuthCrypt
=========

This module provides two methods for authentication:

`authcrypt:Hash`
: Username & password authentication with hashed passwords.

`authcrypt:Htpasswd`
: Username & password authentication against an `.htpasswd` file.


`authcrypt:Hash`
----------------

This is based on `exampleAuth:UserPass`, and adds support for hashed passwords.
Hashes can be generated with the included command line tool `bin/pwgen.sh`.
This tool will interactively ask for a password, a hashing algorithm , and whether or not you want to use a salt:

    [user@server simplesamlphp]$ bin/pwgen.php
    Enter password: hackme
    The following hashing algorithms are available:
    md2          md4          md5          sha1         sha224       sha256
    sha384       sha512       ripemd128    ripemd160    ripemd256    ripemd320
    whirlpool    tiger128,3   tiger160,3   tiger192,3   tiger128,4   tiger160,4
    tiger192,4   snefru       snefru256    gost         adler32      crc32
    crc32b       salsa10      salsa20      haval128,3   haval160,3   haval192,3
    haval224,3   haval256,3   haval128,4   haval160,4   haval192,4   haval224,4
    haval256,4   haval128,5   haval160,5   haval192,5   haval224,5   haval256,5

    Which one do you want? [sha256]
    Do you want to use a salt? (yes/no) [yes]

      {SSHA256}y1mj3xsZ4/+LoQyPNVJzXUFfBcLHfwcHx1xxltxeQ1C5MeyEX/RxWA==

Now create an authentication source in `config/authsources.php` and use the resulting string as the password:

    'example-hashed' => array(
        'authCrypt:Hash',
        'student:{SSHA256}y1mj3xsZ4/+LoQyPNVJzXUFfBcLHfwcHx1xxltxeQ1C5MeyEX/RxWA==' => array(
            'uid' => array('student'),
            'eduPersonAffiliation' => array('member', 'student'),
            ),
    ),

This example creates a user `student` with password `hackme`, and some attributes.

### Compatibility ###
The generated hashes can also be used in `config.php` for the administrative password:

    'auth.adminpassword'        => '{SSHA256}y1mj3xsZ4/+LoQyPNVJzXUFfBcLHfwcHx1xxltxeQ1C5MeyEX/RxWA==',

Instead of generating hashes, you can also use existing ones from OpenLDAP, provided that the `userPassword` attribute is stored as MD5, SMD5, SHA, or SSHA.


`authCrypt:Htpasswd`
--------------------

Authenticate users against an [`.htpasswd`](http://httpd.apache.org/docs/2.2/programs/htpasswd.html) file. It can be used for example when you migrate a web site from basic HTTP authentication to SimpleSAMLphp.

The simple structure of the `.htpasswd` file does not allow for per-user attributes, but you can define some static attributes for all users.

An example authentication source in `config/authsources.php` could look like this:

    'htpasswd' => array(
        'authcrypt:Htpasswd',
            'htpasswd_file' => '/var/www/foo.edu/legacy_app/.htpasswd',
            'static_attributes' => array(
                'eduPersonAffiliation' => array('member', 'employee'),
                'Organization' => array('University of Foo'),
        ),
    ),

