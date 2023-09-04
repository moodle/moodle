Upgrade notes for SimpleSAMLphp 1.9
===================================

  * The OpenID client "linkback" URL has changed from `.../module.php/openid/consumer.php` to `.../module.php/openid/linkback.php`.
  * Support for CA path validation has been removed from SAML 2.0.
  * The X-Frame-Options has been added to the default templates, to prevent the pages from being loaded in iframes.
  * Access permissions of generated files are now restricted to the current user.
  * The code to set cookies now requires PHP version >= 5.2. (PHP version 5.2.0 or newer has been the only supported version for a while, but it has in some cases been possible to run SimpleSAMLphp with older versions.)
  * It used to be possible to set an array of endpoints for the SingleSignOnService in `saml20-idp-hosted.php`. That is no longer supported.
  * The `aselect` module has been replaced with a new module. The new module gives us better error handling and support for request signing, but we lose support for A-Select Cross.
  * There has been various fixes in the session exipration handling. As a result of this, sessions may get a shorter lifetime (if the IdP places a limit on the lifetime, this limit will now be honored).
