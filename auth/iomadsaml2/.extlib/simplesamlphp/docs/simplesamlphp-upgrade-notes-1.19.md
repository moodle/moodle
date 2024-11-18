Upgrade notes for SimpleSAMLphp 1.19
====================================

The minimum PHP version required is now PHP 7.1.

SAML 1 / Shib 1.3 support is now deprecated and will start logging notices
when used. It will be removed in SimpleSAMLphp 2.0.

SimpleSAMLphp 1.19 will automatically try to determine whether to set the sameSite-flag on cookies.
Some browser require to set the Secure-flag as well for sameSite to work. Therefore, the default for
the `session.cookie.secure` setting has been changed to TRUE. This will be the right setting for most
setups anyway, however if you really need to use insecure cookies, you have to manually set it to false and
figure out a value for `session.cookie.samesite` that works for your environment.
