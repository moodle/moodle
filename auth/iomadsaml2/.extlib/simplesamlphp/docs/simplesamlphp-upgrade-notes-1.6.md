Upgrade notes for SimpleSAMLphp 1.6
===================================

  * This release requires PHP version >= 5.2.0, as that was the first version to include `json_decode()`.
    It is possible that it may work with version of PHP >= 5.1.2 if the [JSON PECL extesion](http://pecl.php.net/package/json) is enabled, but this is untested.

  * The secure-flag is no longer automatically set on the session cookie.
    This was changed to avoid hard to diagnose session problems.
    There is a new option `session.cookie.secure` in `config.php`, which can be used to enable secure cookies.

  * Dictionaries have moved to JSON format.
    The PHP format is still supported, but all dictionaries included with SimpleSAMLphp are in JSON format.

  * The iframe-specific logout endpoints on the IdP have been merged into the normal logout endpoints.
    This means that the metadata no longer needs to be changed when switching between logout handlers.
    The old iframe logout endpoints are now deprecated, and the generated metadata will only include the normal logout endpoint.

  * As a result of the changed metadata classes, all metadata elements now have a `md:`-prefix.
    This does not change the content of the metadata, just its expression.

  * The deprecated functions `init(...)` and `setAuthenticated(...)` in the `SimpleSAML_Session` class have been removed.
    Code which relies on those functions should move to using `SimpleSAML_Session::getInstance()` and `$session->doLogin(...)`.
