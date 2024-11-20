SimpleSAMLphp changelog
=======================

<!-- {{TOC}} -->

This document lists the changes between versions of SimpleSAMLphp.
See the upgrade notes for specific information about upgrading.

## Version 1.19.7

Released 5-12-2022

  * Backported fix to error report page (#1637)
  * Many doc fixes
  * Fixed the handling of SAML AttributeQuery and SOAP-binding (#314 @ saml2)
  * Fixed serialization of complex AttributeValue structures
  * Bump composer + npm dependencies (includes a fix for CVE-2022-39261)
  * Many updated translations
  * Handle ETag/If-None-Match logic (#1672 + #1673)
  
## Version 1.19.6

Released 1-7-2022

  * Fix several translations (#1572, #1573, #1577, #1578, #1603)
  * Fix HTTP status code for error pages (#1585)
  * \SimpleSAML\Utils\HTTP::getFirstPathElement() was marked deprecated
  * Bumped twig and minimist dependencies due to known vulnerabilities (CVE-2022-23614 and CVE-2021-44906)
  * Minor fixes to the old UI (#1632)
  * Fix several translations (#1572, #1573, #1577, #1578)

### saml2 library
  * A mis-use of a constant was fixed (#249) that caused an error with HTTP-Artifact binding.

### metarefresh
  * Added regex-template config keyword to apply a template to entityIDs matching a pattern. (v0.10)

## Version 1.19.5

Released 24-01-2021

  * Fix composer-file to prevent warnings
  * Fix database persistence (#1555)
  * Dropped dependency on jquery-ui and selectize

### adfs
  * Bump the module version to the 1.0.x branch;  the 0.9 branch only works with versions before 1.19

### saml2 library
  * Fix an issue with PHP 7.x support that was introduced in 1.19.4 (#1559)

## Version 1.19.4

Released 13-12-2021

### core
  * Fix translations for included templates (i.e. metadata not found error)

### ldap
  * Added the possibility to escape the additional search filters that were introduced in 1.19.2

### saml2 library
  * The library has been quick-fixed to support PHP 8.1 (#1545)

### metarefresh
  * Reverted an unintended update of the module. The v1,0-branch is intended for use with SSP 2.0 (dev-master) only

## Version 1.19.3

Released 2021-10-28

  * Fixed a wrong variable name introduced in v1.19.2 (#1480) that rendered the PHP session handler useless.

## Version 1.19.2

Released 2021-10-27

  * Restored PHP 8.0 compatibility (#1461), also on the saml2 library (v4.2.3)
  * Revert #1435; should not have ended up in a bugfix release. If you need the authproc-filters, please install the
    simplesamlphp-module-subjectidattrs module.
  * Fixed a bug in the logger that would break encoded urls in the message
  * Return a proper HTTP/405 code when incorrect method is used (#1400)
  * Fixed the 'rememberenabled' config setting of the built-in IdP discovery.
  * Fixed a bug where code from external modules would run even though the module is explicitly enabled (#1463)
  * Fix unsolicited response with no RelayState (#1473)
  * Fix statistics being logged despite a configured loglevel that excludes statistics.
  * Fixed an issue with the PHP session handler (#1480, #1350, #1478) causing superfluous log messages.
  * Fixed the MetaDataStorageHandlerPdo for MySQL backends (#1392)
  * Use getVersion instead of getStats to determine whether a memcache-server is up (#1528)

### adfs
  * Fixed several issues that rendered the old UI useless for this module (v0.9.8)

### admin
  * Fix warning in FederationController (#1475)
  * Fix displayed metadata for hosted entities differing from actual metadata.

### consent
  * Add possibility to set the sameSite flag on cookies set by this module (v0.9.7)

### discopower
  * Fixed a dependency issue that caused the module to not install under some PHP-versions (v0.10.0)

### ldap
  * Added search-filters to AttributeAddUserGroups and made the return-attribute configurable (v0.9.11)

### negotiate
  * Fixed a regression that rendered the new UI useless for this module (v0.9.11)

### sqlauth
  * Fixed a bug that rendered the module useless due to missing use-statements.

## Version 1.19.1

Released 2021-04-29

  * Added authproc-filters for generating the subject-id and pairwise-id (#1435)
  * Restore support for custom error messages (#1326)
  * Fixed a bug in the Artifact Resolution Service (#1428)
  * Fixed compatibility with Composer pre 1.8.5 (Debian 10) (#1427)
  * Updated npm dependencies up to April 23, 2021
  * Fixed a bug where it was impossible to set WantAssertionsSigned=true on SP-metadata (#1433)
  * Make inResponseTo available in state array (#1447)

### admin
  * Fixed a bug in the metadata-coverter where the coverted metadata would contain newline-characters

### authorize
  * Fix a bug in the Twig-template that causes an exception in Twig strict vars mode

### memcacheMonitor
  * Fix a bug in the Twig-template that causes an exception on newer Twig-versions

### negotiate
  * Fix a bug that was breaking the module when using the old UI

### oauth
  * Fixed a namespace bug that was breaking the module

### statistics
  * Fix a bug in the Twig-template that causes an exception on newer Twig-versions

### sqlauth
  * Fix a security bug where in rare cases the database user credentials would be printed in exception messages

## Version 1.19.0

Released 2021-01-21

  * This version will be the last of the 1.x branch and will provide a migration path to our new
    templating system, routing system and translation system.
  * SAML 1 / Shib 1.3 support is now marked deprecated and will be removed in SimpleSAMLphp 2.0.
  * Raised minimum PHP version to 7.1
  * Dropped support for Symfony 3.x
  * Update the SAML2 library dependency to 4.1.9
  * Fix a bug where SSP wouldn't write to the tmp-directory if it didn't own it, but could write to it (#1314)
  * Fixed several bugs in saml:NameIDAttribute (#1245)
  * Fix artifact resolution (#1343)
  * Allow additional audiences to be specified (#1345)
  * Allow configurable ProviderName (#1348)
  * Support saml:Extensions in saml:SP authsources (#1349)
  * The `attributename`-setting in the core:TargetedID authproc-filter has been deprecated in
    favour of the `identifyingAttribute`-setting.
  * Filter multiauth authentication sources from SP using AuthnContextClassRef (#1362)
  * Allow easy enabling of SameSite = 'None' (#1382)
  * Do not accept the hashed admin password for authentication (#1418)

## Version 1.18.8

Released 2020-09-02

  * Fixed Artifact Resolution due to incorrect use of Issuer objects (#1343).
  * Fixed some of the German translations (#1331). Thanks @htto!
  * Harden against CVE-2020-13625;  this package is not affected, but 3rd party modules may (#1333).
  * Harden against sevaral JS issues (npm update & npm audit fix)
  * Fixed inconsistent configuration of backtraces logging
  * Support for Symfony 3.x is now deprecated
  * Support for Twig 1.x is now deprecated

### authcrypt
  * The dependency for whitehat101/apr1-md5 was moved from the base repository to the module (v0.9.2)

### authx509
  * Restore PHP 5.6 compatibility (v0.9.5)

### cron
  * Fixed old-ui (#1248)

### ldap
  * Moved array with binary attributes to authsource config (v0.9.9)
    Instead of having to edit code, you can now set 'attributes.binary' in the authsource configuration.

### metarefresh
  * Add attributewhitelist to support e.g. R&S+Sirtfi (v0.9.5)
  * Restore PHP 5.6 compatibility (v0.9.6)

### negotiate ###
  * Restore PHP 5.6 compatibility (v0.9.8)
  * Fixed a link (v0.9.9)

### saml2 library
  * Fixed a bug in the AuthnRequest-class that would raise an InvalidArgumentException when setting
      the AssertionConsumerServiceIndex as an integer on an saml:SP authsource.
      Thanks to Andrea @ Oracle for reporting this.

## Version 1.18.7

Released 2020-05-12

  * Fix spurious warnings when session_create_id() fails to create ID (#1291)
  * Fix inconsistency in the way PATH_INFO is being used (#1227).
  * Fix a potential security issue [CVE-2020-11022](https://nvd.nist.gov/vuln/detail/CVE-2020-11022) by updating jQuery. If any of your custom modules rely on jQuery,
      make sure you read the following [update notes](https://jquery.com/upgrade-guide/3.5/), since jQuery has solved this in a non-BC way (#1321).
  * Fix incorrect Polish translations (#1311).
  * Fix a broken migration query in the LogoutStore (#1324).
  * Fix an issue with the SameSite cookie parameter when running on PHP versions older than 7.3 (#1320).

### adfs
  * Fixed a broken link to one of the assets (v0.9.6).

### ldap
  * Handle binary attributes in a generic way (v0.9.5).

### oauth
  * Fix PHP 7.4 incompatibility (v0.9.2).

### preprodwarning
  * Fix Dutch translations (v0.9.2).

### sanitycheck
  * Fix broken HTML (v0.9.1).

### saml
  * Fix several issues in the saml:NameIDAttribute authproc filter (#1325).

### saml2 library
  * fixed a standards compliance issue regarding ContactPerson EMail addresses (v3.4.4).
  * fixed an issue parsing very large metadata files (v3.4.3).

## Version 1.18.6

Released 2020-04-17

  * Fix source code disclosure on case-insensitive file systems. See
    [SSPSA 202004-01](https://simplesamlphp.org/security/202004-01).
  * Fix spurious error in logs when using a custom theme (#1312).
  * Fix broken metadata converter (#1305).

## Version 1.18.5

Released 2020-03-19

  * Make the URLs for the cron module work again (#1248).
  * Email error reports now include metadata again (#1269).
  * Fix exampleauth module when using the legacy UI (#1275).
  * Fix authorize module when using custom reject message.
  * Documentation improvements.
  * Fix connection persistence for deployments that switched to memcached.

## Version 1.18.4

Released 2020-01-24

  * Resolved a security issue in email reports. See
    [SSPSA 202001-01](https://simplesamlphp.org/security/202001-01).
  * Resolved a security issue with the logging system. See
    [SSPSA 202001-02](https://simplesamlphp.org/security/202001-02).
  * Fixed SQL store index creation for PostgreSQL.
  * Handle case where cookie 'domain' parameter was not set.
  * Update versions of included JavaScript dependencies.

## Version 1.18.3

Released 2019-12-09

  * Fixed an issue with several modules being enabled by default (#1257).
  * Fixed an issue with metadata generation for trusted entities (#1247, #1251).

### ldap
  * Fixed an issue affecting the installation in case-insensitive file systems (#1253).

## Version 1.18.2

Released 2019-11-26

  * Fixed an issue with the `ldap` module that prevented installing SimpleSAMLphp from the repository (#1241).

## Version 1.18.1

Released 2019-11-26

   * Fixed an issue that prevented custom themes from working (#1240).
   * Fixed an issue with translations in the discovery service (#1244).
   * Fixed an issue with schema validation.

## Version 1.18.0

Released 2019-11-19

  * Fixed an issue with warnings being logged when using PHP 7.2 or newer (#1168).
  * Fixed an issue with web server aliases or rewritten URLs not working (#1023, #1093).
  * Fixed an issue that prevented errors to be logged if the log file was not writeable (#1194).
  * Fixed an issue with old-style NameIDPolicy configurations that disallowed creating new NameIDs (#1230).
  * Resolved a security issue that exposed host information to unauthenticated users. See
    [SSPSA 201911-02](https://simplesamlphp.org/security/201911-02).
  * Replaced custom Email class with the phpmailer library.
  * Allow logging to STDERR in the `logging.handler` option by setting it to `stderr`.
  * Allow use of stream wrappers (e.g. s3://) in paths.
  * Improved 'update or insert' handling for different SQL drivers.
  * The default algorithm within the TimeLimitedToken class has been bumped from SHA-1 to SHA-256
    as announced by deprecation notice in 1.15-RC1.
  * Most modules have been externalized. They will not be included in our future releases by default,
    but will be easily installable using Composer. For now, they are still included in the package.
  * Many minor fixes to code, css, documentation

### metarefresh
  * The algorithm to compute the fingerprint of the certificate that signed
    metadata can be specified with the new `validateFingerprintAlgorithm`
    configuration option.

### saml
  * Make the id of the generated signed metadata only change when metadata content changes.
  * New SP metadata configuration options `AssertionConsumerService` and `SingleLogoutServiceLocation`
    to allow overriding the default URL paths.
  * Added support for per-IDP configurable `AuthnContextClassRef`/`AuthnContextComparison`.

## Version 1.17.8

Released 2019-11-20

  * Resolved a security issue that exposed host information to unauthenticated users. See
    [SSPSA 201911-02](https://simplesamlphp.org/security/201911-02).

### consentAdmin

  * Fixed an issue with CSS and Javascript not loading for the module in the new user
    interface.

## Version 1.17.7

Released 2019-11-06

  * Resolved a security issue that allows to bypass signature validation. See
    [SSPSA 201911-01](https://simplesamlphp.org/security/201911-01).

## Version 1.17.6

Released 2019-08-29

  * Fixed a regression with logout database initialization when using MySQL (#1177).
  * Fixed an issue with logout when using iframes (#1191).
  * Fixed an issue causing log entries to be logged with incorrect relative order (#1107).

## Version 1.17.5

Released 2019-08-02

  * Fixed a bug in the SP API where NameID objects weren't taken care of (introduced in 1.17.0).
  * Fixed a regression where MetaDataStorageHandlerPdo::getMetaData() would not return a value (#1165).
  * Fixed an issue with table indexes (#1089).
  * Fixed an issue with table migrations on SQlite (#1169).
  * Fixed an issue with generated eduPersonTargetedID lacking a format specified (#1135).
  * Updated composer dependencies.

## Version 1.17.4

Released 2019-07-11

  * Fix an issue introduced in 1.17.3 with `enable.http_post`.
  
## Version 1.17.3

Released 2019-07-10

  * Resolved a security issue that could lead to a reflected XSS.  See
    [SSPSA 201907-01](https://simplesamlphp.org/security/201907-01).
  * Add new options `session.cookie.samesite` and `language.cookie.samesite` that can be
    used to set a specific value for the cookies' SameSite attribute. The default it not
    to set it.
  * Upgraded jQuery to version 3.4.
  * HHVM is no longer supported.
  * Fixed a bug (#926) where dynamic metadata records where not loaded from a database.
  * Fixed an issue when an error occurs during a passive authentication request.
  * Handle duplicate insertions for SQL Server.
  * Fix a bug in Short SSO Interval warning filter.
  * Apply a workaround for SIGSEGVs during session creation with PHP-FPM 7.3.

### adfs
  * Fixed a missing option to supply a passphrase for the ADFS IDP signing certificate.

### authlinkedin
  * This module has been removed now that LinkedIn no longer supports OAuth1.
    If you relied on this module, you may consider migrating to the
    [authoauth2 module](https://github.com/cirrusidentity/simplesamlphp-module-authoauth2).
    A migration guide for LinkedIn authentication is included in their README.

## Version 1.17.2

Released 2019-04-02

  * Fixed that generated metadata was missing some information
    when PHP's zend.assertions option is set to < 1.
  * Fixed that MDUI Keywords and Logo were not parsed from metadata.
  * Fixed DiscoPower module tab display.
  * Fixed use group name in Attribute Add Users Groups filter.
  * Add metadatadir setting to the default config template.
  * Fixed exception processing in loadExceptionState().
  * Fixed preferredidp in built-in 'links'-style discovery.

## Version 1.17.1

Released 2019-03-07

  * Fixed an issue with composer that made it impossible to install modules
    if SimpleSAMLphp was installed itself with the provided package (tar.gz file).

## Version 1.17.0

Released 2019-03-07

  * Introduce a new experimental user interface based on Twig templates.
    The new Twig templates co-exist next to the old ones and come
    with a new look-and-feel for SimpleSAMLphp and independent interfaces for
    users and administrators. This new interface uses also a new build system
    to generate bundled assets.
  * Introduce Symfony-style routing and dependency injection(#966).
  * Generate session IDs complying with PHP config settings when using the PHP
    session handler (#569).
  * Update OpenSSL RSA bit length in docs (#993).
  * Update all code, configuration templates and documentation to PHP
    short array syntax.
  * All classes moved to namespaces and code reformatted to PSR-2.
  * Use bcrypt for new password hashes, old ones will remain working (#996).
  * Many code cleanups.
  * Update the SAML2 library dependency to 3.2.5.
  * Update the Clipboard.JS library dependency to 2.0.4.
  * Translated to Zulu and Xhosa.
  * Multiple bug fixes and corrections.

### Interoperability
  * The minimum PHP version required is now 5.5.
  * Fixed compatibility with PHP 7.3 and HVVM.
  * SimpleSAMLphp can now be used with applications that use Twig 2 and/or Symfony 4.
  * The SAML2 library now uses getters/setters to manipulate objects properties.

### authfacebook
  * Fix facebook compatibility (query parameters).

### authorize
  * Add the possibility to configure a custom rejecttion message.

### consent
  * The module is now disabled by default.

### core
  * Allow `core:PHP` to manipulate the entire state array.
  * IdP initiated login: add compatibility with Shibboleth parameters.

### multiauth
  * Added a `preselect` configuration option to skip authsource selection (#1005).

### negotiate
  * The `keytab` setting now allows for relative paths too.

### preprodwarning
  * This module is now deprecated. Use the `production` configuration
    option instead; set it to `false` to show a pre-production warning
    before authentication.

### saml
  * Add initial support for SAML Subject ID Attributes.
  * Allow to specify multiple supported NameIdFormats in IdP hosted and SP
    remote metadata.
  * Allow to specify NameIDPolicy Format and AllowCreate in hosted SP
    and remote IdP configuration. Restore the possibility to omit it from
    AuthnRequests entirely (#984).
  * Add a `assertion.allowed_clock_skew` setting to influence how lenient
    we should be with the timestamps in received SAML messages.
  * If the Issuer of a SAML response does not match the entity we sent the
    request to, log a warning instead of bailing out with an exception.
  * Allow setting the AudienceRestriction in SAML2 requests (#998).
  * Allow disabling the Scoping element in SP and remote IdP configuration with
    the `disable_scoping` option, for compatibility with ADFS which does not
    accept the element (#985).
  * Receiving an eduPersonTargetedID in string form will no longer break
    parsing of the assertion.

### sanitycheck
  * Translated into several languages.

## Version 1.16.3

Released 2018-12-20

  * Resolved a security issue that could expose the user's credentials locally.  See
    [SSPSA 201812-01](https://simplesamlphp.org/security/201812-01).
  * Downgraded the level of log messages regarding the `userid.attribute` configuration option
    from _warning_ to _debug_.
  * Make the `attr` configuration option of the _negotiate_ allow both a string and an array.
  * Look for the _keytab_ file used by the _negotiate_ module in the `cert` directory, accepting
    both absolute and relative paths.
  * Fixed some broken links.
  * Other minor bugfixes.

## Version 1.16.2

Released 2018-09-28

  * Fixed an issue with PHP sessions in PHP 7.2.
  * Fixed a bug in the OAuth module.
  * Make schema validation work again.
  * Properly document the `saml:AuthnContextClassRef` authentication processing filter.
  * Fixed an issue that made it impossible to install the software with composer using the
    "stable" minimum-stability setting.
  * Changed the default authentication context class to "PasswordProtectedTransport" by default
    when authentication happened on an HTTPS exchange.

## Version 1.16.1

Released 2018-09-07

  * Fix a bug preventing the consent page from showing.
  * Add Catalan to the list of available languages.

## Version 1.16.0

Released 2018-09-06

### Changes
  * Default signature algorithm is now RSA-SHA256.
  * Renamed class `SimpleSAML_Error_BadUserInnput` to `SimpleSAML_Error_BadUserInput`
  * PHP 7.2 compatibility, including removing deprecated use of assert with string.
  * Avoid logging database credentials in backtraces.
  * Fix edge case in getServerPort.
  * Updated Spanish translation.
  * Improvements to documentation, testsuite, code quality and coding style.

### New features
  * Added support for SAML "Enhanced Client or Proxy" (ECP) protocol,
    IdP side with HTTP Basic Authentication as authentication method.
    See the [ECP IdP documentation](./simplesamlphp-ecp-idp) for details.
  * New option `sendmail_from`, the from address for email sent by SSP.
  * New option `options` for PDO database connections, e.g. for TLS setup.
  * New option `search.scope` for LDAP authsources.
  * Add support for the DiscoHints IPHint metadata property.
  * Add support to specify metadata XML in config with the `xml` parameter,
    next to the exising `file` and `url` options.
  * Also support CGI/RewriteRule setups that set the `REDIRECT_SIMPLESAMLPHP_CONFIG_DIR`
    environment variable next to regular `SIMPLESAMLPHP_CONFIG_DIR`.
  * Support creating an AuthSource via factory, for example useful in tests.
  * Support preloading of a virtual config file via `SimpleSAML_Configuration::setPreLoadedConfig`
    to allow for dynamic population of authsources.php.
  * Add basic documentation on Nginx configuration.
  * Test authentication: optionally show AuthData array.
  * Improve performance of PDO Metadata Storage handler entity lookup.

### adfs
  * Make signature algorithm configurable with `signature.algorithm`.
  * Use configuration assertion lifetime when available.
  * Use `adfs:wreply` parameter when available.

### authmyspace
  * Module removed because service is no longer available.

### cas
  * Respect all LDAP options in LDAP call.

### casserver
  * Module removed; superseded by externally hosted module.

### consent
  * Sort attribute values for consent.
  * Fix table layout for MySQL > 5.6.
  * Rename `noconsentattributes` to `attributes.exclude`; the former
    is now considered deprecated.

### consentAdmin
  * Work better with TargetedIDs when operating as a proxy.
  * Add `attributes.exclude` option to correspond to the same option
    in the Consent module.

### core
  * StatisticsWithAttribute: add `passive-` prefix when logging passive
    requests, set new option `skipPassive` to skip logging these altogether.
  * Replace deprecated `create_function` with an anonymous function.
  * New authproc filter Cardinality to enforce attribute cardinality.
  * SQLPermanentStorage: proper expiration of stored values.
  * AttributeLimit: new options `regex` and `ignoreCase`.
  * AttributeMap: prevent possible infinite loop with some PHP versions.

### ldap
  * AttributeAddUsersGroups: if `attribute.groupname` is set, use the
    configured attribute as the group name rather than the DN.
  * Also base64encode the `ms-ds-consistencyguid` attribute.

### metarefresh
  * Return XML parser error for better debugging of problems.
  * Only actually parse metadata types that have been enabled.
  * Fix missing translation.

### Oauth
  * Make module HTTP proxy-aware.
  * Remove unused demo app.

### saml
  * AttributeConsumingService: allow to set isDefault and index options.
  * Encrypted attributes in an assertion are now decrypted correctly.
  * Prefer the HTTP-Redirect binding for AuthnRequests if available.

### smartattributes
  * Fix to make the `add_authority` option work.

### sqlauth
  * The module is now disabled by default.

### statistics
  * Show a decent error message when no data is available.

## Version 1.15.4

Released 2018-03-02

  * Resolved a security issue related to signature validation in the SAML2 library. See [SSPSA 201803-01](https://simplesamlphp.org/security/201803-01).

## Version 1.15.3

Released 2018-02-27

  * Resolved a security issue related to signature validation in the SAML2 library. See [SSPSA 201802-01](https://simplesamlphp.org/security/201802-01).
  * Fixed edge-case scenario where an application uses one of the known LoggingHandlers' name as a defined class
  * Fixed issue #793 in the PHP logging handler.

## Version 1.15.2

Released 2018-01-31

  * Resolved a Denial of Service security issue when validating timestamps in the SAML2 library. See [SSPSA 201801-01](https://simplesamlphp.org/security/201801-01).
  * Resolved a security issue with the open redirect protection mechanism. See [SSPSA 201801-02](https://simplesamlphp.org/security/201801-02).
  * Fix _undefined method_ error when using memcacheD.

### `authfacebook`
  * Fix compatibility with Facebook strict URI match.

### `consent`
  * Fix statistics not being gathered.

### `sqlauth`
  * Prevented a security issue with the connection charset used for MySQL backends. See [SSPSA 201801-03](https://simplesamlphp.org/security/201801-03).

## Version 1.15.1

Released 2018-01-12

### Bug fixes
  * AuthX509 error messages were broken.
  * Properly calculate supported protocols based on config.
  * NameIDAttribute filter: update to use SAML2\XML\saml\NameID.
  * Replace remaining uses of SimpleSAML_Logger with namespace version.
  * Statistics: prevent mixed content errors.
  * Add 'no-store' to the cache-control header to avoid Chrome
    caching redirects.

## Version 1.15.0

Released 2017-11-20

### New features
  * Added support for authenticated web proxies with the `proxy.auth` setting.
  * Added new `AttributeValueMap` authproc filter.
  * Added attributemaps for OIDs from SIS (Swedish Standards Institute) and
    for eduPersonUniqueId, eduPersonOrcid and sshPublicKey.
  * Added an option to specify metadata signing and digest algorithm
    `metadata.sign.algorithm`.
  * Added an option for regular expression matching of trusted.url.domains via new
    `trusted.url.regex` setting.
  * The `debug` option is more finegrained and allows one to specify whether
    to log full SAML messages, backtraces or schema validations separately.
  * Added a check for the latest SimpleSAMLphp version on the front page.
    It can be disabled via the new setting `admin.checkforupdates`.
  * Added a warning when there's a probable misconfiguration of PHP sessions.
  * Added ability to define additional attributes on ContactPerson elements
    in metatada, e.g. for use in Sirtfi contacts.
  * Added option to set a secure flag also on the language cookie.
  * Added option to specify the base URL for the application protected.
  * Added support for PHP Memcached extension next to Memcache extension.
  * Added Redis as possible session storage mechanism.
  * Added support to specify custom metadata storage handlers.
  * Invalidate opcache after writing a file, so simpleSAMLphp works when
    `opcache.validate_timestamps` is disabled.
  * Metadata converter will deal properly with XML with leading whitespace.
  * Update `ldapwhoami()` call for PHP 7.3.
  * Made response POST page compatible with strict Content Security Policy on
    calling webpage.
  * Updated Greek, Polish, Traditional Chinese and Spanish translations and
    added Afrikaans.

### Bug fixes
  * The deprecated OpenIdP has been removed from the metadata template.
  * Trailing slash is no longer required in `baseurlpath`.
  * Make redirections more resilient.
  * Fixed empty protocolSupportEnumeration in AttributeAuthorityDescriptor.
  * Other bug fixes and numerous documentation enhancements.
  * Fixed a bug in the Redis store that could lead to incorrect
    _duplicate assertion_ errors.

### API and user interface
  * Updated to Xmlseclibs 3.0.
    Minimum PHP version is now 5.4, mcrypt requirement dropped.
  * Added a PSR-4 autoloader for modules. Now modules can declare their
    classes under the SimpleSAML\Module namespace.
  * Added new hook for module loader exception handling `exception_handler`.
  * Expose RegistrationInfo in parsed SAML metadata.
  * The AuthnInstant is now available in the state array.
  * Introduced Twig templating for user interface.
  * Lots of refactoring, code cleanup and added many unit tests.

### `adfs`
  * Fixed POST response form parameter encoding.

### `authYubiKey`
  * Fixed PHP 7 support.

### `authfacebook`
  * Updated to work with latest Facebook API.

### `authlinkedin`
  * Added setting `attributes` to specify which attributes to request
    from LinkedIn.

### `authtwitter`
  * Added support for fetching the user's email address as attribute.

### `consent`
  * Added support for regular expressions in `consent.disable`.

### `core`
  * Added logging of `REMOTE_ADDR` on successful login.
  * `AttributeMap`: allow fetching mapping files from modules.
  * `ScopeAttribute`: added option `onlyIfEmpty` to add a scope only if
     none was present.
  * `AttributeCopy`: added option to copy to multiple destination attributes.

### `cron`
  * Allow invocation via PHP command line interface.

### `discopower`
  * Added South Africa tab.

### `ldap`
  * Added `search.filter` setting to limit LDAP queries to a custom search
    filter.
  * Added OpenLDAP support in AttributeAddUsersGroups.
  * Fixed for using non standard LDAP port numbers.
  * Fixed configuration option of whether to follow LDAP referrals.

### `memcacheMonitor`
  * Fixed several missing strings.

### `metarefresh`
  * Fixed several spurious PHP notices.

### `multiauth`
  * Fixed selected source timeout.

### `negotiate`
  * Fixed authentication failure on empty attributes-array.
  * Fixed PHP notices concerning missing arguments.

### `oauth`
  * Updated library to improve support for OAuth 1.0 Revision A.

### `radius`
  * Improved error messages.
  * Added parameter `realm` that will be suffixed to the username entered.

### `saml`
  * Handle instead of reject assertions that do not contain a NameID.
  * Added options to configure `AllowCreate` and `SPNameQualifier`.
  * Added option `saml:NameID` to set the Subject NameID in a SAML AuthnRequest.
  * Added filter `FilterScopes` to remove values which are not properly scoped.
  * Make sure we log the user out before reauthenticating.
  * More robust handling of IDPList support in proxy mode.
  * Increased `_authSource` field length in Logout Store.
  * We now send the eduPersonTargetedID attribute in the correct
    NameID XML form, instead of the incorrect simple string. We will also
    refuse to parse an assertion with an eduPersonTargetedID in 'string' format.

### `smartattributes`
  * Fix SmartName authproc that failed to load.

### `sqlauth`
  * Fixed SQL schema for usergroups table.

## Version 1.14.17

Released 2017-10-25

  * Resolved a security issue with the SAML 1.1 Service Provider. See [SSPSA 201710-01](https://simplesamlphp.org/security/201710-01).

## Version 1.14.16

Released 2017-09-04

  * Resolved a security issue in the consentAdmin module. See [SSPSA 201709-01](https://simplesamlphp.org/security/201709-01).

## Version 1.14.15

Released 2017-08-08

  * Resolved a security issue with the creation and validation of time-limited tokens. See [SSPSA 201708-01](https://simplesamlphp.org/security/201708-01).
  * Fixed an issue with session handling that could lead to crashes after upgrading from earlier 1.14.x versions.
  * Fixed issue #557 with instances of SimpleSAMLphp installed from the repository as well as custom modules.
  * Fixed issue #648 to properly handle SAML responses being sent to reply the same request, but using different response IDs.
  * Fixed issues #612 and #618 with the mobile view of the web interface.
  * Fixed issue #639 related to IdP names containing special characters not being properly displayed by discopower.
  * Fixed issue #571 causing timeouts when using Active Directory as a backend.
  * Other minor fixes.

## Version 1.14.14

Released 2017-05-05

  * Resolved a security issue with in the authcrypt module (Htpasswd authentication source) and in SimpleSAMLphp's session validation. See [SSPSA 201705-01](https://simplesamlphp.org/security/201705-01).
  * Resolved a security issue with in the multiauth module. See [SSPSA 201704-02](https://simplesamlphp.org/security/201704-02).

## Version 1.14.13

Released 2017-04-27

  * Resolved a security issue with unauthenticated encryption in the SimpleSAML\Utils\Crypto class. See [SSPSA 201704-01](https://simplesamlphp.org/security/201704-01).
  * Added requirement for the Multibyte String PHP extension and the corresponding checks.
  * Set a default name for SimpleSAMLphp sessions in the configuration template for the PHP session handler.
  
## Version 1.14.12

Released 2017-03-30

  * Resolved a security issue in the authcrypt module (Htpasswd authentication source) and in SimpleSAMLphp's session validation. See [SSPSA 201703-01](https://simplesamlphp.org/security/201703-01).
  * Resolved a security issue with IV generation in the  `SimpleSAML\Utils\Crypto::_aesEncrypt()` method. See [SSPSA 201703-02](https://simplesamlphp.org/security/201703-02).
  * Fixed an issue with the authfacebook module, broken after a change in Facebook's API.
  * Fixed an issue in the discopower module that ignored the `hide.from.discovery` metadata option.
  * Fixed an issue with trusted URLs validation that prevented a URL from being accepted if a standard port was explicitly included but not specified in the configuration.
  * Fixed an issue that prevented detecting a Memcache server being down when fetching Memcache statistics.
  * Fixed an issue with operating system detection that made SimpleSAMLphp identify OSX as Windows.

## Version 1.14.11

Released 2016-12-12

  * Resolved a security issue involving signature validation of SAML 1.1 messages. See [SSPSA 201612-02](https://simplesamlphp.org/security/201612-02).
  * Fixed an issue when the user identifier used to generate a persistent NameID was missing due to a misconfiguration, causing SimpleSAMLphp to generate the nameID based on the null data type.
  * Fixed an issue when persistent NameIDs were generated out of attributes with empty strings or multiple values.
  * Fixed issue #530. An empty SubjectConfirmation element was causing SimpleSAMLphp to crash. On the other hand, invalid SubjectConfirmation elements were ignored in PHP 7.0.

## Version 1.14.10

Released 2016-12-02

  * Resolved a security issue involving signature validation. See [SSPSA 201612-01](https://simplesamlphp.org/security/201612-01).
  * Fixed issue #517. A misconfigured session when acting as a service provider was leading to a PHP fatal error.
  * Fixed issue #519. Prevent persistent NameIDs from being generated from empty strings.
  * Fixed issue #520. It was impossible to verify Apache's custom MD5 passwords when using the Htpasswd authentication source.
  * Fixed issue #523. Avoid problems caused by different line-ending strategies in the project files.
  * Other minor fixes and enhancements.

## Version 1.14.9

Released 2016-11-10

  * Fixed an issue that resulted in PHP 7 errors being masked.
  * Fixed the smartattributes:SmartName authentication processing filter.
  * Fixed issue #500. When parsing metadata, two 'attributes.required' options were generated.
  * Fixed the list of requirements in composer, the documentation, and the configuration page.
  * Fixed issue #479. There were several minor issues with XHTML compliance.
  * Other minor fixes.

## Version 1.14.8

Released 2016-08-23

  * Fixed an issue in AuthMemCookie causing it to crash when an attribute received contains XML as its value.
  * Fixed an issue in AuthMemCookie that made it impossible to set its own cookie.
  * Fixed an issue when acting as a proxy and receiving attributes that contain XML as their values.
  * Fixed an issue that led to incorrect URL guessing when a script is invoked with a URI that doesn't include its name.

## Version 1.14.7

Released 2016-08-01

  * Fixed issue #424. Attributes containing XML as their values (like eduPersonTargetedID) were empty.

## Version 1.14.6

Released 2016-07-18

  * Fixed issue #418. SimpleSAMLphp was unable to obtain the current URL correctly when invoked from third-party applications.

## Version 1.14.5

Released 2016-07-12

  * Fixed several issues with session handling when cookies couldn't be set for some reason.
  * Fixed an issue that caused wrong URLs to be generated in the web interface under certain circumstances.
  * Fixed the exception handler to be compatible with PHP 7.
  * Fixed an issue in the dropdown IdP selection page that prevented it to work with PHP 5.3.
  * Fixed compatibility with Windows machines.
  * Fixed an issue with the PDO and Serialize metadata storage handlers.
  * Fixed the authwindowslive module. It stopped working after the former API was discontinued.
  * Other minor issues and fixes.

## Version 1.14.4

Released 2016-06-08

  * Fixed two minor security issues that allowed malicious URLs to be presented to the user in a link. Reported by John Page.
  * Fixed issue #366. The LDAP class was trying to authenticate even when no password was provided (using the CAS module).
  * Fixed issue #401. The authenticate.php script was printing exceptions instead of throwing them for the exception handler to capture them.
  * Fixed issue #399. The size limitation of the TEXT type in MySQL was creating problems in certain setups.
  * Fixed issue #5. Incoherent population of the $_SERVER variable was creating broken links when running PHP with FastCGI.
  * Other typos and minor bugs: #389, #392.

## Version 1.14.3

Released 2016-04-19

  * Fixed a bug in the login form that prevented the login button to be displayed in mobile devices.
  * Resolved an issue in the PHP session handler that made it impossible to use PHP sessions simultaneously with other applications.

## Version 1.14.2

Released 2016-03-11

  * Use stable versions of the externalized modules to prevent possible issues when further developing them.

## Version 1.14.1

Released 2016-03-08

  * Resolved an information leakage security issue in the sanitycheck module. See [SSPSA 201603-01](/security/201603-01).

## Version 1.14.0

Released 2016-02-15

### Security

  * Resolved a security issue with multiple modules that were not validating the URLs they were redirecting to.
  * Added a security check to disable loading external entities in XML documents.
  * Enforced admin access to the metadata converter tool.
  * Changed `xmlseclibs` dependency to point to `robrichards/xmlseclibs` version 1.4.1.

### New features

  * Allow setting the location of the configuration directory with an environment variable.
  * Added support for the Metadata Query Protocol by means of the new MDX metadata storage handler.
  * Added support for the Sender-Vouches method.
  * Added support for WantAssertionsSigned and AuthnRequestsSigned in SAML 2.0 SP metadata.
  * Added support for file uploads in the metadata converter.
  * Added support for setting the prefix for Memcache keys.
  * Added support for the Hide From Discovery REFEDS Entity Category.
  * Added support for the eduPersonAssurance attribute.
  * Added support for the full SCHAC 1.5.0 schema.
  * Added support for UNIX sockets when configuring memcache servers.
  * Added the SAML NameID to the attributes status page, when available.
  * Added attribute definitions for schacGender (schac), sisSchoolGrade and sisLegalGuardianFor (skolfederation.se).
  * Attributes required in metadata are now taken into account when parsing.

### Bug fixes

  * Fixed an issue with friendly names in the attributes released.
  * Fixed an issue with memcache that would result in a push for every fetch, when several servers configured.
  * Fixed an issue with memcache that would result in an endless loop if all servers are down.
  * Fixed an issue with HTML escaping in error reports.
  * Fixed an issue with the 'admin.protectmetadata' option not being enforced for SP metadata.
  * Fixed an issue with SAML 1.X SSO authentications that removed the NameID of the subject from available data.
  * Fixed an issue with the login form that resulted in a `NOSTATE` error if the user clicked the login button twice.
  * Fixed an issue with replay detection in IdP-initiated flows.
  * Fixed an issue with SessionNotOnOrAfter that kept moving forward in the future with every SSO authentication.
  * Fixed an issue with the session cookie being set twice for the first time.
  * Fixed an issue with the XXE attack prevention mechanism conflicting with other applications running in the same server.
  * Fixed an issue that prevented the SAML 1.X IdP to restart when the session is lost.
  * Fixed an issue that prevented classes using namespaces to be loaded automatically.
  * Fixed an issue that prevented certain metadata signatures to be verified (fixed upstream in `xmlseclibs`).
  * Other bug fixes and numerous documentation enhancements.

### API and user interface

  * Added a new and simple database class to serve as PDO interface for all the database needs.
  * Added the possibility to copy metadata and other elements by clicking a button in the web interface.
  * Removed the old, unused `pack` installer tool.
  * Improved usability by telling users the endpoints are not to be accessed directly.
  * Moved the hostname, port and protocol diagnostics tool to the admin directory.
  * Several classes and functions deprecated.
  * Changed the signature of several functions.
  * Deleted old and deprecated code, interfaces and endpoints.
  * Deleted old jQuery remnants.
  * Deleted the undocumented dynamic XML metadata storage handler.
  * Deleted the backwards-compatible authentication source.
  * Updated jQuery to the latest 1.8.X version.
  * Updated translations.

### `authcrypt`

  * Added whitehat101/apr1-md5 as a dependency for Apache htpasswd.

### `authX509`

  * Added an authentication processing filter to warn about certificate expiration.

### `ldap`

  * Added a new `port` configuration option.
  * Better error reporting.

### `metaedit`

  * Removed the `admins` configuration option.

### `metarefresh`

  * Added the possibility to specify which types of entities to load.
  * Added the possibility to verify metadata signatures by using the public key present in a certificate.
  * Fix `certificate` precedence over `fingerprint` in the configuration options when verifying metadata signatures.

### `smartnameattribute`

  * This module was deprecated long time ago and has now been removed. Use the `smartattributes` module instead.

## Version 1.13.2

Released 2014-11-04

  * Solved performance issues when processing large metadata sets.
  * Fix an issue in the web interface when only one language is enabled.

## Version 1.13.1

Released 2014-10-27

  * Solved an issue with empty fields in metadata to cause SimpleSAMLphp to fail with a translation error. Issues #97 and #114.
  * Added Basque language to the list of known languages. Issue #117.
  * Optimized the execution of redirections by removing an additional, unnecessary function call.
  * Solved an issue that caused SimpleSAMLphp to fail when the RelayState parameter was empty or missing on an IdP-initiated authentication. Issues #99 and # 104.
  * Fixed a certificate check for SubjectConfirmations with Holder of Key methods.

## Version 1.13

Released 2014-09-25.

  * Added the 'remember me' option to the default login page.
  * Improved error reporting.
  * Added a new 'logging.format' option to control the formatting of the logs.
  * Added support for the 'objectguid' binary attribute in LDAP modules.
  * Added support for custom search and private attributes read credentials in all LDAP modules.
  * Added support for the WantAuthnRequestsSigned option in generated SAML metadata.
  * Tracking identifiers are no longer generated based on MD5.
  * Several functions, classes and interfaces marked as deprecated.
  * Bug fixes and documentation enhancements.
  * Updated translations.
  * New language: Basque.

### `adfs`

  * Honour the 'wreply' parameter when redirecting.

### `aggregator`

  * Fixed an issue when regenerating metadata from certain metadata sources.

### `discopower`

  * Bug fix.

### `expirycheck`

  * Translations are now possible for this module.

### `metarefresh`

  * Use cached metadata if something goes wrong when refreshing feeds.

### `openidProvider`

  * Fix for compatibility with versions of PHP greater or equal to 5.4.

### `saml`

  * Make it possible to add friendly names to attributes in SP metadata.
  * The RSA_1.5 (RSA with PKCS#1 v1.5 padding) encryption algorithm is now blacklisted by default for security reasons.
  * Stop checking the 'IDPList' parameter in IdPs.
  * Solved an issue that allowed bypassing authentication status checks when presenting an 'IDPList' parameter.
  * The 'Destination' attribute is now always sent in logout responses issued by an SP.

### `sqlauth`

  * Updated documentation to remove bad practice with regard to password storage.

## Version 1.12

Released 2014-03-24.

  * Removed example authproc filters from configuration template.
  * Stopped using the 'target-densitydpi' option removed from WebKit.
  * The SimpleSAML_Utilities::generateRandomBytesMTrand() function is now deprecated.
  * Removed code for compatibility with PHP versions older than 5.3.
  * Removed the old interface of SimpleSAML_Session.
  * Fixed a memory leak in SimpleSAML_Session regarding serialization and unserialization.
  * Support for RegistrationInfo (MDRPI) elements in the metadata of identity and service providers.
  * Renamed SimpleSAML_Utilities::parseSAML2Time() function to xsDateTimeToTimestamp().
  * New SimpleSAML_Utilities::redirectTrustedURL() and redirectUntrustedURL() functions.
  * Deprecated the SimpleSAML_Utilities::redirect() function.
  * Improved Russian translation.
  * Added Czech translation.
  * New 'errorreporting' option to enable or disable error reporting feature.
  * Example certificate removed.
  * New SimpleSAML_Configuration::getEndpointPrioritizedByBinding() function.
  * PHP 5.3 or newer required.
  * Started using Composer as dependency manager.
  * Detached the basic SAML2 library and moved to a standalone library in github.
  * Added support for exporting shibmd:Scope metadata with regular expressions.
  * Remember me option in the IdP.
  * New SimpleSAML_Utilities::setCookie wrapper.
  * Custom HTTP codes on error.
  * Added Romanian translation.
  * Bug fixes and documentation enhancements.

### `adfs`

  * Support for exporting metadata.

### `aggregator`

  * Support for RegistrationInfo (MDRPI) elements in the metadata.
  * Fix for HTTP header injection vulnerability.
  * Fix for directory traversal vulnerability.

### `aggregator2`

  * Support for RegistrationInfo (MDRPI) elements in the metadata.

### `aselect`

  * License changed to LGPL 2.1.

### `authfacebook`

  * Updated extlibinc to 3.2.2.

### `authtwitter`

  * Added 'force_login' configuration option.

### `cdc`

  * Bugfix related to request validation.

### `core`

  * The AttributeAlter filter no longer throws an exception if the attribute was not found.
  * Support for removal of values in the AttributeAlter filter, with '%remove' flag.
  * Support for empty strings and NULL values as a replacement in the AttributeAlter filter.
  * Bugfixes in the AttributeAlter filter.
  * Support for NULL attribute values.
  * Support for limiting values and not only attributes in the AttributeLimit filter.
  * Log a message when a user authenticates successfully.
  * Added %duplicate flag to AttributeMap, to leave original names in place when using map file.
  * Fix infinite loop when overwriting attributes with AttributeMap.

### `discopower`

  * Bugfix for incorrect handling of the 'idpdisco.extDiscoveryStorage' option.

### `ldap`

  * Support for configuring the duplicate attribute handling policy in AttributeAddFromLDAP, 'attribute.policy' option.
  * Support for binary attributes in the AttributeAddFromLDAP filter.
  * Support for multiple attributes in the AttributeAddFromLDAP filter.

### `metarefresh`

  * Support for specifying permissions of the resulting files.

### `negotiate`

  * Added support for "attributes"-parameter.

### `oauth`

  * Bugfix related to authorize URL building.

### `openidProvider`

  * Support for SReg and AX requests.

### `saml`

  * Send 'isPassive' in passive discovery requests.
  * Support for generating NameIDFormat in service providers with NameIDPolicy set.
  * Support for AttributeConsumingService and AssertionConsumingServiceIndex.
  * Support for the HTTP-POST binding in WebSSO profile.
  * Fix for entity ID validation problems when using the IDPList configuration option.

### `smartattributes`

  * New 'add_candidate' option to allow the user to decide whether to prepend or not the candidate attribute name to the resulting value.

### `statistics`

  * Bugfix in statistics aggregator.

## Version 1.11

Released 2013-06-05.

  * Support for RSA_SHA256, RSA_SHA384 and RSA_SHA512 in HTTP Redirect binding.
  * Support for RegistrationInfo element in SAML 2.0 metadata.
  * Support for AuthnRequestsSigned and WantAssertionsSigned when generating metadata.
  * Third party OpenID library updated with a bugfix.
  * Added the Name attribute to EntitiesDescriptor.
  * Removed deprecated option 'session.requestcache' from config-template.
  * Workaround for SSL SNI extension not being correctly set.
  * New language cookie and parameter config options.
  * Add 'module.enable' configuration option for enabling/disabling modules.
  * Check for existence of memcache extension. 
  * Initial support for limiting redirects to trusted hosts.
  * Demo example now shows both friendly and canonical name of the attributes.
  * Other minor fixes for bugs and typos.
  * Several translations updated.
  * Added Latvian translation.

### `authorize`

  * Added a logout link to the 403 error page.

### `authtwitter`

  * Updated API endpoint for version 1.1.
  * Fix for oauth_verifier parameter.

### `authX509`

  * ldapusercert validation made optional.

### `consent`

  * Added support for SQLite databases.

### `core`

  * Fix error propagation in UserPass(Org)Base authentication sources.
  * MCrypt module marked as required.

### `discopower`

  * Get the name of an IdP from mdui:DisplayName.

### `expirycheck`

  * PHP 5.4 compatibility fixes.

### `InfoCard`

  * PHP 5.4 compatibility fixes.

### `ldap`

  * Added an option to disable following referrals.

### `metarefresh`

  * Improved help message.

### `oauth`

  * PHP 5.4 compatibility fixes.

### `saml`

  * Verify that the issuer of an AuthnResponse is the same entity ID we sent a request to.
  * Added separate option to enable Holder of Key support on SP.
  * Fix for HoK profile metadata.
  * New filter for storing persistent NameID in eduPersonTargetedID attribute.
  * Support for UIInfo elements.
  * Bugfix for SAML SP metadata signing.
  * Ignore default technical contact.
  * Support for MDUI elements in SP metadata.
  * Support for more contact types in SP metadata.
  * New information in statistics with the time it took for a login to happen.

### `sanitycheck`

  * Configuration file made optional.

### `smartattributes`

  * New filter: smartattributes:SmartID.
  * New filter: smartattributes:SmartName.

### `smartnameattribute`

  * Deprecated.

### `wsfed`

  * Support for SLO in WS-Fed.

## Version 1.10

Released 2012-09-25.

  * Add support for storing data without expiration timestamp in memcache.
  * Fix for reauthentication in old shib13 authentication handler.
  * Clean up executable-permissions on files.
  * Change encryption to use the rsa-oaep-mgf1p key padding instead of PKCS 1.5.
  * Update translations.
  * Added Serbian translation.

### `core`

  * `core:UserPass(Org)Base`: Add "remember username" option.

### `papi`

  * New authentication module supporting PAPI protocol.

### `radius`

  * New feature to configure multiple radius servers.

### `riak`

  * New module for storing sessions in a Riak database.

### `saml`

  * Add support for overriding SAML 2.0 SP authentication request generation.
  * Add support for blacklisting encryption algorithms.

## Version 1.9.2

Released 2012-08-29

  * Fix related to the security issue addressed in version 1.9.1.

## Version 1.9.1

Released 2012-08-02.

  * Fix for a new attack against PKCS 1.5 in XML encryption.

## Version 1.9

Released 2012-06-13.

  * Restructure error templates to share a common base template.
  * Warnings about URL length limits from Suhosin PHP extension.
  * New base class for errors from authentication sources.
  * Support for overriding URL generation when behind a reverse proxy.
  * New languages: Russian, Estonian, Hebrew, Chinese, Indonesian
  * Add getAuthSource()-function to SimpleSAML_Auth_Simple.
  * Add reauthenticate()-function to SimpleSAML_Auth_Source. (Is called when the IdP receives a new authentication request.)
  * iframe logout: Make it possible to skip the "question-page" for code on the IdP.
  * RTL text support.
  * Make SimpleSAMLAuthToken cookie name configurable.
  * Block writing secure cookies when we are on http.
  * Fix state information being unavailable to UserPassOrgBase authentication templates.
  * Make it possible to send POST-messages to http-endpoints without triggering a warning when the IdP supports both http and https.
  * Add IPv6-support to the SimpleSAML_Utilities::ipCIDRcheck()-function.
  * Do not allow users to switch to a language that is not enabled.
  * iframe logout: Add a per-SP timeout option.
  * SimpleSAML_Auth_LDAP: Better logging of the cause of exceptions.
  * SimpleSAML_Auth_State: Add $allowMissing-parameter to loadState().
  * module.php: More strict URL parsing.
  * Add support for hashed admin passwords.
  * Use openssl_random_pseudo_bytes() for better cross-platform random number generation.
  * Add the current hostname to the error reports.
  * Make the lifetime of SimpleSAML_Auth_State "state-arrays" configurable (via the `session.state.timeout`-option).
  * SimpleSAML_Auth_State: Add cloneState()-function.
  * Fix log levels used on Windows.
  * SimpleSAML_Auth_LDAP: Clean up some unused code.
  * core:UserPassOrgBase: Add selected organization to the authentication data that is stored in the session.
  * Do not warn about missing Radius and LDAP PHP extensions unless those modules are enabled.
  * Support for overriding the logic to determine the language.
  * Avoid crashes due to deprecation-warnings issued by PHP.
  * Use case-insensitive matching of language codes.
  * Add X-Frame-Options to prevent other sites from loading the SSP-pages in an iframe.
  * Add SimpleSAML_Utilities::isWindowsOS()-helper function.
  * chmod() generated files to only be accessible to the owner of the files.
  * Fix "re-posting" of POST data containing a key named "submit".
  * Do not attempt to read new sessions from the session handler.
  * Fix some pass-by-reference uses. (Support removed in PHP 5.4.)
  * Warn the user if the secretsalt-option isn't set.
  * A prototype for a new statistics logging core. Provides more structured logging of events, and support for multiple storage backends.
  * Support for arbitrary namespace-prefixed attributes in md:EndpointType-elements.
  * Fix invalid HTML for login pages where username is set.
  * Remove unecessary check for PHP version >= 5.2 when setting cookies.
  * Better error message when a module is missing a default-enable or default-disable file.
  * Support for validating RSA-SHA256 signatures.
  * Fixes for session exipration handling.

### `aselect`

  * New module that replaces the previous module.
  * Better error handling.
  * Support for request signing.
  * Loses support for A-Select Cross.

### `authcrypt`

  * `authcrypt:Hash`: New authentication source for checking username & password against a list of usernames and hashed passwords.
  * `authcrypt:Htpasswd`: New authentication source for checking username & password against a `.htpasswd`-file.

### `authfacebook`

  * Update to latest Facebook PHP SDK.

### `authorize`

  * `authorize:Authorize`: Add flag to change the behaviour from default-deny to default-allow.
  * `authorize:Authorize`: Add flag to do simple string matching instead of regex-matching.

### `authtwitter`

  * Update to use the correct API endpoint.
  * Propagate "user aborted" errors back to the caller.
  * Changes to error handling, throw more relevant exceptions.
  * Store state information directly in the state array, instead of the session.

### `authYubiKey`

  * Remove deprecated uses of split().

### `cas`

  * Make it possible for subclasses to override finalState().

### `core`

  * `core:AttributeCopy`: New filter to copy attributes.

### `consent`

  * Add a timeout option for the database connection.
  * Fix disabling of consent when the data store is down.
  * Simpler configuration for disabling consent for one SP or one IdP.
  * Do not connect to the database when consent is disabled for the current SP/IdP.

### `consentAdmin`

  * Fix for bridged IdP setup with `userid.attribute` set in `saml20-idp-hosted` metadata.

### `cron`

  * Set the From-address to be the technical contact email address.

### `expirycheck`

  * `expirycheck:ExpiryDate`: New module to check account expiration.

### `ldap`

  * Add a base class for authentication processing filters which fetch data from LDAP.
  * `ldap:AttributeAddUsersGroups`: Authentication processing filter that adds group information from LDAP.

### `metarefresh`

  * Support for blacklisting and whitelisting entities.
  * Support for conditional GET of metadata files.
  * Reuse old metadata when fetching metadata fails.

### `multiauth`

  * Add `multiauth:preselect`-parameter, to skip the page to select authentication source.
  * Make it possible to configure the names of the authentication sources.
  * Remember the last selected authentication source.

### `negotiate`

  * New module implementing "negotiate" authentication, which can be used for Kerberos authentication (including Windows SSO).

### `oauth`

  * Update to latest version of the OAuth library.
  * Remove support for older versions of OAuth than OAuth Rev A.

### `openid`

  * Separate linkback URL from page displaying OpenID URL field.
  * Throw more relevant exceptions.
  * Update to latest version of the OpenID library.
  * Support for sending authentication requests via GET requests (with the prefer_http_redirect option).
  * Prevent deprecation warnings from the OpenID library from causing deadlocks in the class loader.

### `openidProvider`

  * Prevent deprecation warnings from the OpenID library from causing deadlocks in the class loader.

### `radius`

  * Support for setting the "NAS-Identifier" attribute.

### `saml`

  * Preserve ID-attributes on elements during signing. (Makes it possible to change the binding for some messages.)
  * Allow SAML artifacts to be received through a POST request.
  * Log more debug information when we are unable to determine the binding a message was sent with.
  * Require HTTP-POST messages to be sent as POST data and HTTP-Redirect messages to be sent as query parameters.
  * Link to download certificates from metadata pages.
  * Fix canonicalization of &lt;md:EntityDescriptor> and &lt;md:EntitiesDescriptor>.
  * Support for receiving and sending extension in authentication request messages.
  * Reuse SimpleSAML_Utilities::postRedirect() to send HTTP-POST messages.
  * Allow ISO8601 durations with subsecond precision.
  * Add support for parsing and serializing the &lt;mdrpi:PublicationInfo> metadata extension.
  * Ignore cacheDuration when validating metadata.
  * Add support for the Holder-of-Key profile, on both the [SP](./simplesamlphp-hok-sp) and [IdP](./simplesamlphp-hok-idp).
  * Better error handling when receiving a SAML 2.0 artifact from an unknown entity.
  * Fix parsing of &lt;md:AssertionIDRequestService> metadata elements.
  * IdP: Do not always trigger reauthentication when the authentication request contains a IdPList-element.
  * IdP: Add `saml:AllowCreate` to the state array. This makes it possible to access this parameter from authentication processing filters.
  * IdP: Sign the artifact response message.
  * IdP: Allow the "host" metadata option to include more than one path element.
  * IdP: Support for generating metadata with MDUI extension elements.
  * SP: Use the discojuice-module as a discovery service if it is enabled.
  * SP: Add `saml:idp`-parameter to trigger login to a specific IdP to as_login.php.
  * SP: Do not display error on duplicate response when we have a valid session.
  * SP: Fix for logout after IdP initiated authentication.
  * SP: Fix handling of authentication response without a saml:Issuer element.
  * SP: Support for specifying required attributes in metadata.
  * SP: Support for limiting the AssertionConsumerService endpoints listed in metadata.
  * SP: Fix session expiration when the IdP limits the session lifetime.
  * `saml:PersistentNameID`: Fail when the user has more than one value in the user ID attribute.
  * `saml:SQLPersistentNameID`: Persistent NameID stored in a SQL database.
  * `saml:AuthnContextClassRef`: New filter to set the AuthnContextClassRef in responses.
  * `saml:ExpectedAuthnContextClassRef`: New filter to verify that the SP received the correct authentication class from the IdP.

## Version 1.8.2

Released 2012-01-10.

  * Fix for user-assisted cross site scripting on a couple of pages.

## Version 1.8.1

Released 2011-10-27.

  * Fix for key oracle attack against XML encryption on SP.
  * Fix for IdP initiated logout with IdP-initiated SSO.
  * Fix a PHP notice if we are unable to open /dev/urandom.
  * Fix a PHP notice during SAML 1.1 authentication.

## Version 1.8

  * New authentication modules:
      * [`authmyspace`](./authmyspace:oauthmyspace)
      * [`authlinkedin`](./authlinkedin:oauthlinkedin)
      * [`authwindowslive`](./authwindowslive:windowsliveid)
  * Support for custom error handler, replacing the default display function.
  * Allow error codes to be defined in modules.
  * Better control of logout what we do after logout request.
      * This makes it possible for the SP to display a warning when receiving a PartialLogout response from the IdP.
  * New `cdc` module, for setting and reading common domain cookies.

### `consent`

  * Support for disabling consent for some attributes.

### `ldap`

  * `ldap:AttributeAddFromLDAP`: Extract values from multiple matching entries.

### `oauth`

  * Added support for:
      * RSASHA1 signatures
      * consent
      * callbackurl
      * verifier code
      * request parameters

### `openid`

  * Support for sending custom extension arguments (e.g. UI extensions).

### `saml`

  * Extract Extensions from AuthnRequest for use by custom modules when authenticating.
  * Allow signing of SP metadata.
  * Better control over NameIDPolicy when sending AuthnRequest.
  * Support encrypting/decrypting NameID in LogoutRequest.
  * Option to disable client certificate in SOAP client.
  * Better selection of AssertionConsumerService endpoint based on parameters in AuthnRequest.
  * Set NotOnOrAfter in IdP LogoutRequest.
  * Only return PartialLogout from the IdP.


## Version 1.7

  * New authentication modules:
      * `aselect`
      * `authX509`
  * Unified cookie configuration settings.
  * Added protection against session fixation attacks.
  * Error logging when failing to initialize the Session class.
  * New session storage framework.
      * Add and use generic key/value store.
      * Support for storing sessions in SQL databases (MySQL, PostgreSQL & SQLite).
      * Support for implementing custom session storage handlers.
      * Allow loading of multiple sessions simultaneously.
  * Set headers allowing caching of static files.
  * More descriptive error pages:
      * Unable to load $state array because the session was lost.
      * Unable to find metadata for the given entityID.
  * Support for multiple keys in metadata.
      * Allow verification with any of the public keys in metadata.
      * Allow key rollower by defining new and old certificate in configuration.
      * Verify with signing keys, encrypt with encryption keys.
  * Change `debug`-option to log messages instead of displaying them in the browser.
      * Also logs data before encryption and after decryption.
  * Support for custom attribute dictionaries.
  * Add support for several authentication sessions within a single session.
      * Allows several SPs on a single host.
      * Allows for combining an SP and an IdP on a single host.
  * HTTP proxy support.

### Internal API changes & features removed

  * The `saml2` module has been removed.
      * The `saml2:SP` authsource has been removed.
      * The `sspmod_saml2_Error` class has been renamed to `sspmod_saml_Error`.
      * The `sspmod_saml2_Message` class has been renamed to `sspmod_saml_Message`.
  * Moved IdP functions from `sspmod_saml_Message` to `sspmod_saml_IdP_SAML2`.
  * Removed several functions and classes that are unused:
      * `SimpleSAML_Utilities::strleft`
      * `SimpleSAML_Utilities::array_values_equal`
      * `SimpleSAML_Utilities::getRequestURI`
      * `SimpleSAML_Utilities::getScriptName`
      * `SimpleSAML_Utilities::getSelfProtocol`
      * `SimpleSAML_Utilities::cert_fingerprint`
      * `SimpleSAML_Utilities::generateTrackID`
      * `SimpleSAML_Utilities::buildBacktrace`
      * `SimpleSAML_Utilities::formatBacktrace`
      * `SimpleSAML_Metadata_MetaDataStorageHandlerSAML2Meta`
      * `SimpleSAML_ModifiedInfo`
  * Moved function from Utilities-class to more appropriate locations.
      * `getAuthority` to `SimpleSAML_IdP`
      * `generateUserId` to `sspmod_saml_IdP_SAML2`.
  * Replaced calls to  with throwing an `SimpleSAML_Error_Error` exception.
  * Removed metadata send functionality from old SP code.
  * Removed bin/test.php and www/admin/test.php.
  * Removed metashare.
  * Removed www/auth/login-auto.php.
  * Removed www/auth/login-feide.php.
  * Removed optional parameters from `SimpleSAML_XHTML_Template::getLanguage()`.
  * Removed functions from `SAML2_Assertion`: `get/setDestination`, `get/setInResponseTo`.
    Replaced with `setSubjectConfirmation`.
  * Removed several unused files & templates.

### SAML 2 IdP

  * Support for generation of NameID values via [processing filters](./saml:nameid)
  * Obey the NameIDPolicy Format in authentication request.
  * Allow AuthnContextClassRef to be set by processing filters.
  * Rework iframe logout page to not rely on cookies.

### SAML 2 SP

  * Support SOAP logout.
  * Various fixes to adhere more closely to the specification.
      * Allow multiple SessionIndex-elements in LogoutRequest.
      * Handle multiple Assertion-elements in Response.
      * Reject duplicate assertions.
      * Support for encrypted NameID in LogoutRequest.
      * Verify Destination-attribute in LogoutRequest messages.
  * Add specific options for signing and verifying authentication request and logout messages.
  * `saml:NameIDAttribute` filter for extracting NameID from authentication response.

### SAML 1 IdP

  * Add `urn:mace:shibboleth:1.0` as supported protocol in generated metadata.

### SAML 1 SP

  * Support for IdP initiated authentication.

### `aggregator`

  * Allow metadata generation from command line.

### `authfacebook`

  * Change attribute names.

### `casserver`

  * Support for proxying.
  * Add ttl for tickets.

### `core`

  * `core:AttributeLimit`: Make it possible to specify a default set of attributes.
  * Make the SP metadata available on the login pages.

### `discoPower`

  * Sort IdPs without a name (where we only have an entityID) last in the list.
  * CDC cookie support.

### `exampleAuth`

  * Add example of integration with external authentication page.

### `ldap`

  * Add `ldap:AttributeAddFromLDAP` filter for adding attributes from a LDAP directory.

### `metarefresh`

  * Don't stop updates on the first exception.

### `openid`

  * Don't require access to the PHP session.
  * Remove OpenID test page. (May as well use the normal test pages.)
  * Support for attribute exchange.
  * Add `target` option, for directing authentication to a specific OpenID provider.
  * Add `realm` option, for specifying the realm we should send to the OpenID provider.

### `portal`

  * Make it possible to register pages from modules, and not only from configuration.

### `statistics`

  * New y-axis scaling algorithm

### `twitter`

  * Change attribute names returned from twitter.


## Version 1.6.3

Released 2010-12-17.

  * Fix for cross site scripting in redirect page.

## Version 1.6.2

Released 2010-07-29.

  * Various security fixes.

## Version 1.6.1

Released 2010-06-25.

  * saml:SP: Fix SingleLogoutService endpoint in SSP-format metadata array.
  * Shib13:IdP: Add urn:mace:shibboleth:1.0 to supported protocols.
  * Fix SAMLParser::parseElement().
  * SAML2:IdP: Fix persistent NameID generation.
  * Fix scoping on IdP discovery page.
  * metaedit: Fix endpoints parsed from XML.
  * Dictionary update.
  * Documentation fixes.

## Version 1.6

Released 2010-05-31.

[Upgrade notes](./simplesamlphp-upgrade-notes-1.6)

  * Detection of cookies disabled on the IdP.
  * New IdP core, which makes it simpler to share code between different IdPs, e.g. between SAML 1.1 and SAML 2.0.
  * Dictionaries moved to JSON format.
  * New authentication module: [`cas:CAS`](./cas:cas).
  * All images that doesn't permit non-commercial use have been replaced.
  * Better support for OrganizationName, OrganizationDisplayName and OrganizationURL in metadata.
  * Cookie secure flag no longer automatically set.
  * Cross-protocol logout between ADFS and SAML 2.
  * New experimental module for aggregating metadata: [`aggregator2`](./aggregator2:aggregator2)
  * Metadata support for multiple endpoints with [multiple bindings](./simplesamlphp-metadata-endpoints).
  * The metadata generation is using a new set of classes.
    As a result, all generated metadata elements now have a `md:`-prefix.
  * The deprecated functions `init(...)` and `setAuthenticated(...) in the `SimpleSAML_Session` class have been removed.
  * Configuration check and metadata check was removed, as they were often wrong.

### SAML 2 SP

  * SAML 2.0 HTTP-Artifact support on the [SP](./simplesamlphp-artifact-sp).

### SAML 2 IdP

  * SAML 2.0 HTTP-Artifact support on the [IdP](./simplesamlphp-artifact-idp).
  * Support for sending PartialLogout status code in logout response.
  * Set AuthnInstant to the timestamp for authentication.
  * Combine normal and iframe versions of the logout handlers into a single endpoint.
  * The SessionIndex is now unique per SP.
  * Statistics for logout failures.
  * Better generation of persistent NameID when `nameid.attribute` isn't specified.

### The SP API

  * Support for handling errors from the IdP.
  * Support for passing parameters to the authentication module.
    This can be used to specify SAML 2 parameters, such as isPassive and ForceAuthn.

### `adfs`

  * Move to new IdP core.

### `casserver`

  * Collect all endpoints in a single file.
  * Fix prefix on the tickets.

### `consent`

  * Support for deactivating consent for specific services.

### `consentAdmin`

  * Support for the SAML SP module.

### `core`

  * New filter: [`core:PHP`](./core:authproc_php), which allows processing of attributes with arbitrary PHP code.
  * Support for multiple target attributes in [`core:AttributeMap`](./core:authproc_attributemap).
  * New filter: [`core:ScopeFromAttribute`](./core:authproc_scopefromattribute), which allows the creation an attribute based on the scope of another attribute.
  * Support for a target attribute in [`core:AttributeAlter`](./core:authproc_attributealter).

### `discoPower`

  * Support for new scoring algorithm.

### `ldap`

  * SASL support in LDAPMulti

### `ldapstatus`

  * This module was removed, as it was very specific for Feide.

### `multiauth`

  * Support for specifying the target authentication source through a request parameter.

### `oauth`

  * Configurable which authentication source should be used.

### `openidProvider`

  * OpenID 2.0 support.
  * XRDS generation support.

### `saml`

  * Support for specifying parameters for authentication request.
  * Add AttributeConsumingService to generated metadata.
  * The two SPSSODescriptor elements in the metadata has been merged.


## Version 1.5.1

Released 2010-01-08.

  * Fix security vulnerability due to insecure temp file creation:
    * statistics: The logcleaner script outputs to a file in /tmp.
    * InfoCard: Saves state directly in /tmp. Changed to the SimpleSAMLphp temp directory.
    * openidProvider: Default configuration saves state information in /tmp.
      Changed to '/var/lib/simplesamlphp-openid-provider'.
    * SAML 1 artifact support: Saves certificates temporarily in '/tmp/simplesaml', but directory creation was insecure.
  * statistics: Handle new year wraparound.
  * Dictionary updates.
  * Fix bridged logout.
  * Some documentation updates.
  * Fix all metadata to use assignments to arrays.
  * Fix $session->getIdP().
  * Support AuthnContextClassRef in saml-module.
  * Do not attempt to send logout request to an IdP that does not support logout.
  * LDAP: Disallow bind with empty password.
  * LDAP: Assume that LDAP_NO_SUCH_OBJECT is an error due to invalid username/password.
  * statistics: Fix configuration template.
  * Handle missing authority in idp-hosted metadata better.


## Version 1.5

Released 2009-11-05. Revision 1937.

  * New API for SP authentication.
  * Make use of the portal module on the frontpage.
  * SQL datastore.
  * Support for setting timezone in config (instead of php.ini).
  * Logging of PHP errors and notices to SimpleSAMLphp log file.
  * Improve handling of unhandled errors and exceptions.
  * Admin authentication through authentication sources.
  * Various bugfixes & cleanups.
  * Translation updates.
  * Set the dropdown list as default for built in disco service.

### New modules:

  * `adfs`
  * [`authorize`](./authorize:authorize)
  * `authtwitter`
  * [`autotest`](./autotest:test)
  * `exampleattributeserver`
  * `metaedit`
  * [`multiauth`](./multiauth:multiauth)
  * `oauth`
  * [`openidProvider`](./openidProvider:provider)
  * [`radius`](./radius:radius)
  * [`saml`](./saml:sp)

### `aggregator`:

  * Add ARP + ARP signing functionality to the aggregator.
  * Improvements to the aggregator module. Added documentation, and re-written more OO-oriented.
  * Add support for reconstructing XML where XML for an entity is already cached.
  * Add support for excluding tags in metadata aggregator.

### `AuthMemCookie`:

  * Delete the session cookie when deleting the session.
  * Support for authentication sources.
  * Set expiry time of session data when saving to memcache.
  * Support multiple memcache servers.

### `cas`:

  * Added support for attributes in <cas:serviceResponse>.

### `consent`:

  * Support for hiding some attribute values.

### `consentAdmin`:

  * Added config option to display description.

### `core`:

  * New WarnShortSSOInterval filter.

### `discopower`:

  * Live search in discopower-module.

### `ldap`:

  * Support for proxy authentication.
  * Add 'debug' and 'timeout' options.
  * Privilege separation for LDAP attribute retrieval.
  * Allow search.base to be an array.
  * (LDAPMulti) Add support for including the organization as part of the username.

### `ldapstatus`:

  * Do a connect-test to all ip-addresses for a hostname.
  * Check wheter hostname exists before attempting to connect.
  * hobbit output.
  * Check schema version.
  * Add command line tab to single LDAP status page for easier debugging.

### `logpeek`:

  * Blockwise reading of logfile for faster execution.

### `metarefresh`:

  * Adding support for generating Shibboleth ARP files.
  * Add 'serialize' metadata format.

### `preprodwarning`:

  * Don't show warning in passive request.
  * Focus on continue-button.

### SAML:

  * Support for multiple AssertionConsumerService endpoints.
  * SAML 1 artifact support on the SP side.
  * New SAML authentication module.
  * Deprecation of www/saml2/sp & www/shib13/sp.
  * Support for encrypted NameID.
  * NameIDPolicy replaces NameIDFormat.
  * Better support for IdP initiated SSO and bookmarked login pages.
  * Improvements to iframe logout page.
  * Scoping support.
  * New library for SAML 2 messages.
  * Support for transporting errors from the IdP to the SP.
  * Sign both the assertion and the response element by default.
  * Support for sending XML attribute values from the IdP.

### `statistics`:

  * Extended Google chart encoding... Add option of alternative compare plot in graph...
  * Added support for Ratio type reports in the statistics module..
  * Changed default rule to sso.
  * Added incremental aggregation, independent time resolution from rule def, combined coldefs and more.
  * Add DST support in date handler. Added summary columns per delimiter. Added pie chart. +++
  * Log first SSO to a service during a session.


## Version 1.4

Released 2009-03-12. Revision 1405.

Updates to `config.php`. Please check for updates in your local modified configuration.

  * Language updates
  * Documentation update. New authencation source API now default and documented.
  * New authentication source (new API):
    * LDAP
    * LDAPMulti  
	* YubiKey authentication source. (Separate module)  
	* Facebook authentication source. (Separate module)
  * New Authentication Processing Filter:
    * AttributeAlter
    * AttributeFilter
    * AttributeMap
    * Smartname. does it best to guess the full name of the user based on several attributes.
    * Language adaptor: allow adopting UI by preferredLanguage SAML 2.0 Attribute both on the IdP and the SP. And if the user selects a lanauge, this can be sent to the SP as an attribute.
  * New module: portal, allows you to created tabbed interface for custom pages within SimpleSAMLphp. In example user consent management and attribute viewer.
  * New module: ldapstatus. Used by Feide to monitor connections to a large list of LDAP connections. Contact Feide on details on how to use.
  * ldapstatus also got certificate check capabilities.
  * New module: MemcacheMonitor: Show statistics for memcache servers.
  * New module: DiscoPower. A tabbed discovery service module with alot of functionality.
  * New module: SAML 2.0 Debugginer. An improved version of the one found on rnd.feide.no earlier is not included in SimpleSAMLphp allowing you to run it locally.
  * New module: Simple Consent Amdin module that have one button to remove all consent for one user.
  * New module: Consent Administration. Contribution from Wayf.
  * We also have a consent adminstration module that we use in Feide that is not checked in to subversion.
  * New module: logpeek. Lets administrator lookup loglines matching a TRackID.
  * New module: PreprodWarning: Adding a warning to users that access a preprod system.
  * New module: CAS Server
  * New module: Aggregator: Aggregates metadata. Used in Kalmar Union.
  * New module: Metarefresh, download, parses and consumes metadata.
  * New module: SanityCheck. Checks if things looks good and reports bad configuration etc.
  * New module: Cron. Will perform tasks regularly. 
  * Module: SAML2.0. SAML 2.0 SP implemented as an module. Yet not documented how to use, but all SAML 2.0 SP functionality may be moved out to this module for better modularization.
  * New module: statistics. Parses STAT log files, and aggregates based on a generic rule system. Output is stored in aggregated text files, and a frontend is included to present statistics with tables and graphs. Used sanitycheck and cron.
  * Added support for IdP initiated SSO.
  * Added support for IdP-initiated SLO with iFrame type logout.
  * Major updates to iFrame AJAX SLO. Improved user experience.
  * iFrame AJAX SLO is not safe against simulanous update of the session.
  * Added support for bookmarking login pages. By adding enough information in the URL to be able to bootstrap a new IdP-initiated SSO and sending.
  * Major updates to the infocard module.
  * Added some handling of isPassive with authentication processing filters.
  * More localized UI.
  * New login as administrator link on frontpage.
  * Tabbed frontpage. Restructured.
  * Simplifications to the theming and updated documentation on theming SimpleSAMLphp.
  * Attribute presentation hook allows you to tweak attributes before presentation in the attribute viewers. Used by Feide to group orgUnit information in a hieararchy.
  * Verification of the Receipient attribute in the response. Will improve security if for some reason an IdP is not includeding sufficient Audience restrictions.
  * Added hook to let modules tell about themself moduleinfo hook.
  * Improved cron mails
  * Improved sanity check exception handling
  * Preserver line breaks in stack trace UI
  * Improvements to WS-Federation support: dynamic realms, logout etc.
  * Better handling of presentation of JPEG photos as attributes.
  * Support limiting size of attribute retrieved from LDAP.
  * Added notes about how to aggregate and consume metadata. Just a start.
  * Large improvements to Configuration class, and config helper functions.
  * STAT logging is moved into separate authenticaion processing filter.
  * Fix for NoPassive responses to Google Apps with alternative NameIDFormats.  
  * LDAP module allows to search multiple searchbases.
  * All documentation is converted from docbook to markdown format.
  * Added headers to not allow google to index pages.
  * Added check on frontpage for magic quotes
  * Added statistic loggging to Consent class.
  * Improvements to Exception handler in LDAP class, and better logging.
  * LDAP class supports turning on LDAP-debug logging.
  * Much improvements to SAML 2.0 Metadata generation and parsing.
  * Adding more recent jquery library.
  * Generic interface for including jquery dependencies in template headers.
  * Improved UI on default theme
  * Fix for session duration in the Conditions element in the Assertion (SAML 2.0).
  * Updated with new Feide IdP metadata in metadata-templates
  


## Version 1.3

Released 2008-11-04. Revision 973.

Configuration file `config.php` should not include significant changes, except one language added.

### New features

  * Documentation update
  * Added new language. Now there are two different portugese
    dialects.
  * Consent "module" modified. Now added support for preselecting the
    checkbox by a configuration parameter. Consent module supports
    including attributs values (possible to configure).
  * CSS and look changed. Removed transparency to fix problem for some
    browsers.
  * The login-admin authentication module does not ask for username any
    more.
  * Added support for persistent NameID Format. (Added by Hans
    ZAndbelt)
  * Added experimental SAML 2.0 SP AuthSource module.
  * More readable XML output formatting. In example metadata.
  * Better support for choosing whether or not to sign authnrequest.
    Possible to specify both at SP hosted and IdP remote.
  * Adding more example metadata in metadata-templates.
  * Improved e-mails sent from SimpleSAMLphp. Now both plain text and
    html.
  * Configuration class may return information about what version.
  * iFrame AJAX SLO improved. Now with non-javascript failback
    handling.

### Bug fixes

  * Fixed warning with XML validator.
  * Improved loading of private/public keys in XML/Signer.
  * Improvements to CAS module.
  * Fixed memcache stats.


## Version 1.2

Released 2008-09-26. Revision 899.

There are some changes in the configuration files from version 1.1 to 1.2. `/simplesaml/admin/config.php` should be used to check what options have changed.

When you upgrade from an previous version you should copy `authsources.php` from `config-templates` into `config` directory.

There are also some changes to the templates. If you have any custom templates, they should be updated to match the ones included. Of notable changes is that the `t(...)`-functtes, they should be updated to match the ones included. Of notable changes is that the `t(...)`-function has been simplified, and takes far fewer parameters. It is backwardscompatible, but will write a warning to the log until updated. The backwards compatibility will be removed in a future version.

### New features

  * Experimental support for modules. Currently modules can contain
    custom authentication sources, authentication processing filters
    and themes.
  * An generic SQL autentication module added for those who store their
    users in an SQL database.
  * Limited support for validating against a CA root certificate. The
    current implementation only supports cases where the certificate is
    directly signed by the CA.
  * Allow an IdP to have multiple valid certificate fingerprints, to
    allow for easier updating of certificates.
  * Shibboleth 1.3 authentication for Auth MemCookie.
  * Support for link to privacy policy on consent-pages.
  * Customizable initial focus on consent-page.
  * Almost all pages should be translateable.
  * Allow SAML 2.0 SP to handle error replies from IdP.
  * PostgreSQL support for consent storage.
  * Add support for encrypted private keys.
  * Proof-of-concept MetaShare service, for easy publishing and sharing
    of metadata.


### Bug fixes

  * Fixed generated SAML 2.0 metadata to be correct.
  * Fixed logout for Auth MemCookie.
  * Sign SAML 2.0 authentication response on failure (such as
    NoPassive).
  * Fixes for IsPassive in the SAML 2.0 IdP.
  * Fix default syslog configuration on Windows.
  * Fix order of signing and encryption of SAML 2.0 responses
  * Fix generated metadata for Shib 1.3
  * Fix order of elements in encrypted assertions to be schema
    compliant.
  * Fix session index sent to SAML 2.0 SPs.
  * Remember SAML 2.0 NameID sent to SPs, and include it in logout
    requests.


## Version 1.1

Released 2008-06-19. Revision 673.

When upgrading to version 1.1 from version 1.0, you should update the configuration files. Many options have been added, and some have moved or removed. The new configuration check page: `/simplesaml/admin/config.php` may be useful for determining what should be updated. Also note that the `language.available` option in `config.php` should be updated to reflect the new languages which have been added.

There are also several changes to the template files. If you have done any customizations to these, you should test them to make sure that they still work. Some changes, such as allowing the users to save the IdP choice they make in the discovery service, will not work without updating the templates.

New localizations in version 1.1: Sami, Svenska (swedish), Suomeksi (finnish), Nederlands, Luxembourgish, Slovenian, Hrvatski (Croatian), Magyar (Hungarian).

### New features

  * Add support for saving the users choice of IdP in the IdP discovery
    service.
  * Add a config option for whether the Response element or the
    Assertion element in the response should be signed.
  * Make it easier to add attribute alteration functions.
  * Added support for multiple languages in metadata name and
    description (for IdP discovery service).
  * Added configuration checker for checking if configuration files
    should be updated.
  * Add support for icons in IdP discovery service.
  * Add support for external IdP discovery services.
  * Support password encrypted private keys.
  * Added PHP autoloading as the preferred way of loading the
    SimpleSAMLphp library.
  * New error report script which will report errors to the
    `technicalcontact_email` address.
  * Support lookup of the DN of the user who is logging in by searching
    for an attribute when using the LDAP authentication module.
  * Add support for fetching name and description of entities from XML
    metadata files.
  * Support for setting custom AttributeNameFormats.
  * Support for signing generated metadata.
  * Support for signature validation of metadata.
  * Added consent support for Shib 1.3 logging.
  * Added errorlog logging handler for logging to the default Apache
    error log.
  * Added support for WS-Federation single signon.
  * Allow `session_save_path` to be overridden by setting the
    `session.phpsession.savepath` option in `config.php`.
  * Add support for overriding autogenerated metadata values, such as
    the `AssertionConsumerService` address.
  * Added IsPassive support in the SAML 2.0 IdP.
  * Add attribute filter for generating eduPersonTargetedID attribute.
  * Add support for validation of sent and received messages and
    metadata.
  * Add support for dynamic metadata loading with cache.
  * Add support for dynamic generation of entityid and metadata.
  * Added wayf.dk login module.
  * Add support for encrypting and decrypting assertions.
  * CAS authentication module: Add support for serviceValidate.
  * CAS authentication module: Add support for getting attributes from
    response by specifying XPath mappings.
  * Add support for specifying a certificate in the `saml20-idp-remote`
    metadata instead of a fingerprint.
  * Add an attribute alter function for dynamic group generation.
  * Add support for attribute processing in SAML 2 SP.
  * Added tlsclient authentication module.
  * Allow the templates to override the header and footer of pages.
  * Major improvements to the Feide authentication module.
  * Add support for ForceAuthn in the SAML 2.0 IdP.
  * Choose language based on the languages the user has selected in the
    web browser.
  * Added fallback to base language if translation isn't found.


### Bug fixes

  * Modified IdP discovery service to support Shibboleth 2.0 SP.
  * Fix setcookie warning for PHP version \< 5.2.
  * Fix logout not being performed for Auth MemCache sometimes.
  * Preserve case of attribute names during LDAP attribute retrival.
  * Fix IdP-initiated logout.
  * Ensure that changed sessions with changed SP associations are
    written to memcache.
  * Prevent infinite recursion during logging.
  * Don't send the relaystate from the SP which initiated the logout to
    other SPs during logout.
  * Prevent consent module from revealing DB password when an error
    occurs.
  * Fix logout with memcache session handler.
  * Allow new session to be created in login modules.
  * Removed the strict parameter from base64\_decode for PHP 5.1
    compatibility.


## Version 1.0

Released 2008-03-28. Revision 470.

## Version 0.5

Released 2007-10-15. Revision 28.

### Warning

Both `config.php` and metadata format are changed. Look at the
templates to understand the new format.

  * Documentation is updated!
  * Metadata files made tidier. Unused entries removed. Look at the new
    templates on how to change your existing metadata.
  * Support for sending metadata by mail to Feide. Automatically
    detecting whether you have configured Feide as the default IdP or
    not.
  * Improved SAML 2.0 Metadata generation
  * Added support for Shibboleth 1.3 IdP functionality (beta, contact
    me if any problems)
  * Added RADIUS authentication backend
  * Added support for HTTP-Redirect debugging when enable `debug=true`
  * SAML 2.0 SP example now contains a logout page.
  * Added new authentication backend with support for multiple LDAP
    based on which organization the user selects.
  * Added SAML 2.0 Discovery Service
  * Initial 'proof of concept' implementation of "User consent on
    attribute release"
  * Fixed some minor bugs.


## Version 0.4

Released 2007-09-14. Revision X.

  * Improved documentation
  * Authentication plugin API. Only LDAP authenticaiton plugin is
    included, but it is now easier to implement your own plugin.
  * Added support for SAML 2.0 IdP to work with Google Apps for
    Education. Tested.
  * Initial implementation of SAML 2.0 Single Log Out functionality
    both for SP and IdP. Seems to work, but not yet well-tested.
  * Added support for bridging SAML 2.0 to SAML 2.0.
  * Added some time skew offset to the NotBefore timestamp on the
    assertion, to allow some time skew between the SP and IdP.
  * Fixed Browser/POST page to automaticly submit, and have fall back
    functionality for user agents with no javascript support.
  * Fixed some bug with warning traversing Shibboleth 1.3 Assertions.
  * Fixed tabindex on the login page of the LDAP authentication module
    to allow you to tab from username, to password and then to submit.
  * Fixed bug on autodiscovering hostname in multihost environments.
  * Cleaned out some debug messages, and added a debug option in the
    configuration file. This debug option let's you turn on the
    possibility of showing all SAML messages to users in the web
    browser, and manually submit them.
  * Several minor bugfixes.
