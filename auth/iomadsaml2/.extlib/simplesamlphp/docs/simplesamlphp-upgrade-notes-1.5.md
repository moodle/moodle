Upgrade notes for SimpleSAMLphp 1.5
===================================

  * `SimpleSAML_Session::isValid()`

    If your code calls `$session->isValid()` without an argument, you will now have to update it to pass an argument (probably `saml2`).
    The reason for this change is that calling `$session->isValid()` without an argument can easily create a security hole.


  * We have introduced a new module for SAML authentication.
    This authentication module supports both SAML 1.1 and SAML 2.0 IdPs.

    We have also added a new authentication framework which should replace the previous redirects to the initSSO-scripts.
    Relating to this change, we have also deprecated the `initSSO`-scripts for SAML 1.1 and SAML 2.0 authentication.
    The old methods will still be supported for a while, but new code should probably use the new code.

    See the [migration guide](simplesamlphp-sp-migration) for more information about this.

  * The `request.signing` option has been removed.
    That option was replaced with the `redirect.sign` and `redirect.validate` options, and has been depreceated for one year.

  * The `aggregator` module's configuration file has changed name.
    It was changed from `aggregator.php` to `module_aggregator.php`.
