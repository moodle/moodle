SimpleSAMLphp AuthMemCookie module
==================================

This module implements [Auth MemCookie](https://zenprojects.github.io/Apache-Authmemcookie-Module/) support for SimpleSAMLphp. This allows
you to integrate SimpleSAMLphp with web applications written in languages other than PHP.

*AuthMemCookie* works by reading authentication data from a *memcache* server and setting environment variables based on
the attributes found in this data. It also allows you to use the default **Apache access control** features to restrict
access to your site.

Requisites
----------

This module requires you to install and set up the following requirements:

* SimpleSAMLphp running as a [Service Provider](https://simplesamlphp.org/docs/stable/simplesamlphp-sp).
* A *memcache* server.
* [Auth MemCookie](https://zenprojects.github.io/Apache-Authmemcookie-Module/) .

Installation
------------

Once you have installed SimpleSAMLphp, installing this module is very simple. First of all, you will need to [download
Composer](https://getcomposer.org/) if you haven't already. After installing Composer, just execute the following
command in the root of your SimpleSAMLphp installation:

```
./composer.phar require simplesamlphp/simplesamlphp-module-memcookie:dev-master
```

where `dev-master` instructs Composer to install the `master` branch from the Git repository. See the
[releases](https://github.com/simplesamlphp/simplesamlphp-module-memcookie/releases) available if you want to use a
stable version of the module.

The module is enabled by default. If you want to disable the module once installed, you just need to create a file named
`disable` in the `modules/memcookie` directory inside your SimpleSAMLphp installation.

Configuration
-------------

The first step to use this module is to configure *Auth MemCookie* appropriately. The following example (that you can
find also in `extra/auth_memcookie.conf`) might be helpful:

```
<Location />
    # This is a list of memcache servers which Auth MemCookie
    # should use. 
    # Note that this list must list the same servers as the
    # 'authmemcookie.servers'-option in config.php in the
    # configuration for simpleSAMLphp.
    #
    # The syntax for this option is inherited from: http://docs.libmemcached.org/libmemcached_configuration.html 
    Auth_memCookie_Memcached_Configuration "--SERVER=127.0.0.1:11211"

    # This must be set to 'on' to enable Auth MemCookie for
    # this directory.
    Auth_memCookie_Authoritative on

    # This adjusts the maximum number of data elements in the
    # session data. The default is 10, which can be to low.
    Auth_memCookie_SessionTableSize "40"

    # These two commands are required to enable access control
    # in Apache.
    AuthType Cookie
    AuthName "My Login"

    # This command causes apache to redirect to the given
    # URL when we receive a '401 Authorization Required'
    # error. We redirect to "/simplesaml/module.php/memcookie/auth.php",
    # which initializes a login to the IdP.
    ErrorDocument 401 "/simplesaml/module.php/memcookie/auth.php"
</Location>

<Location /protected>
    # This allows all authenticated users to access the
    # directory. To learn more about the 'Require' command,
    # please look at:
    # http://httpd.apache.org/docs/2.0/mod/core.html#require
    Require valid-user
</Location>
```

Once *Auth MemCookie* has been correctly configured, you need to configure the module itself by editing the
`config/authmemcookie.php` file. Set the `username` configuration option to the name of an attribute that you are sure
to receive and that will identify the user unambiguously. Read the instructions in the file itself if you need help to
configure it.

If you already have an *auth source* configured and working in SimpleSAMLphp, and all your memcookie configuration
options are correct, you are ready to go! Make sure to reload Apache so that it uses the new configuration and *Auth
MemCookie* is loaded. Then you can point your browser to the location that you have protected in Apache and it should
redirect you automatically to the IdP for authentication.

In order to see all the environment variables you have available in the protected location, you can drop a PHP script
like the following in there and access it from your browser after authenticating to your IdP:

```
<html>
 <body>
  <table>
<?php
    foreach ($_SERVER as $key => $value) {
        echo "   <tr><td>".htmlspecialchars($key)."</td><td>".htmlspecialchars($value)."</td></tr>\n";
    }
?>
  </table>
 </body>
</html>
```
