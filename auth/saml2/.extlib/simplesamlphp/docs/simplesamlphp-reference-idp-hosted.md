IdP hosted metadata reference
=============================

<!-- {{TOC}} -->

This is a reference for the metadata files
`metadata/saml20-idp-hosted.php` and `metadata/shib13-idp-hosted.php`.
Both files have the following format:

    <?php
    /* The index of the array is the entity ID of this IdP. */
    $metadata['entity-id-1'] = [
        'host' => 'idp.example.org',
        /* Configuration options for the first IdP. */
    ];
    $metadata['entity-id-2'] = [
        'host' => '__DEFAULT__',
        /* Configuration options for the default IdP. */
    ];
    /* ... */

The entity ID should be an URI. It can, also be on the form
`__DYNAMIC:1__`, `__DYNAMIC:2__`, `...`. In that case, the entity ID
will be generated automatically.

The `host` option is the hostname of the IdP, and will be used to
select the correct configuration. One entry in the metadata-list can
have the host `__DEFAULT__`. This entry will be used when no other
entry matches.


Common options
--------------

`auth`
:   Which authentication module should be used to authenticate users on
    this IdP.

`authproc`
:   Used to manipulate attributes, and limit access for each SP. See
    the [authentication processing filter manual](simplesamlphp-authproc).

`certificate`
:   Certificate file which should be used by this IdP, in PEM format.
    The filename is relative to the `cert/`-directory.

`contacts`
:	Specify contacts in addition to the technical contact configured through config/config.php.
	For example, specifying a support contact:

		'contacts' => [
		    [
		        'contactType'       => 'support',
		        'emailAddress'      => 'support@example.org',
		        'givenName'         => 'John',
		        'surName'           => 'Doe',
		        'telephoneNumber'   => '+31(0)12345678',
		        'company'           => 'Example Inc.',
		    ],
		],

:	If you have support for a trust framework that requires extra attributes on the contact person element in your IdP metadata (for example, SIRTFI), you can specify an array of attributes on a contact.

		'contacts' => [
		    [
		        'contactType'       => 'other',
		        'emailAddress'      => 'mailto:abuse@example.org',
		        'givenName'         => 'John',
		        'surName'           => 'Doe',
		        'telephoneNumber'   => '+31(0)12345678',
		        'company'           => 'Example Inc.',
		        'attributes'        => [
		            'xmlns:remd'        => 'http://refeds.org/metadata',
		            'remd:contactType'  => 'http://refeds.org/metadata/contactType/security',
		        ],
		    ],
		],

`host`
:   The hostname for this IdP. One IdP can also have the `host`-option
    set to `__DEFAULT__`, and that IdP will be used when no other
    entries in the metadata matches.

`logouttype`
:   The logout handler to use. Either `iframe` or `traditional`. `traditional` is the default.

`OrganizationName`
:   The name of the organization responsible for this IdP.
    This name does not need to be suitable for display to end users.

:   This option can be translated into multiple languages by specifying the value as an array of language-code to translated name:

        'OrganizationName' => [
            'en' => 'Example organization',
            'no' => 'Eksempel organisation',
        ],

:   *Note*: If you specify this option, you must also specify the `OrganizationURL` option.

`OrganizationDisplayName`
:   The name of the organization responsible for this IdP.
    This name must be suitable for display to end users.
    If this option isn't specified, `OrganizationName` will be used instead.

:   This option can be translated into multiple languages by specifying the value as an array of language-code to translated name.

:   *Note*: If you specify this option, you must also specify the `OrganizationName` option.

`OrganizationURL`
:   A URL the end user can access for more information about the organization.

:   This option can be translated into multiple languages by specifying the value as an array of language-code to translated URL.

:   *Note*: If you specify this option, you must also specify the `OrganizationName` option.

`privacypolicy`
:   This is an absolute URL for where an user can find a
    privacypolicy. If set, this will be shown on the consent page.
    `%SPENTITYID%` in the URL will be replaced with the entity id of
    the service the user is accessing.

:   Note that this option also exists in the SP-remote metadata, and
    any value in the SP-remote metadata overrides the one configured
    in the IdP metadata.

:   *Note*: **deprecated** Will be removed in a future release; use the MDUI-extension instead

`privatekey`
:   Name of private key file for this IdP, in PEM format. The filename
    is relative to the `cert/`-directory.

`privatekey_pass`
:   Passphrase for the private key. Leave this option out if the
    private key is unencrypted.

`scope`
:   An array with scopes for this IdP.
    The scopes will be added to the generated XML metadata.
    A scope can either be a domain name or a regular expression
    matching a number of domains.

`userid.attribute`
:   The attribute name of an attribute which uniquely identifies
    the user. This attribute is used if SimpleSAMLphp needs to generate
    a persistent unique identifier for the user. This option can be set
    in both the IdP-hosted and the SP-remote metadata. The value in the
    SP-remote metadata has the highest priority. The default value is
    `eduPersonPrincipalName`.


SAML 2.0 options
----------------

The following SAML 2.0 options are available:

`assertion.encryption`
:   Whether assertions sent from this IdP should be encrypted. The default
    value is `FALSE`.

:   Note that this option can be set for each SP in the SP-remote metadata.

`attributeencodings`
:   What encoding should be used for the different attributes. This is
    an array which maps attribute names to attribute encodings. There
    are three different encodings:

:   -   `string`: Will include the attribute as a normal string. This is
        the default.

:   -   `base64`: Store the attribute as a base64 encoded string. This
        is the default when the `base64attributes`-option is set to
        `TRUE`.

:   -   `raw`: Store the attribute without any modifications. This
        makes it possible to include raw XML in the response.

:   Note that this option also exists in the SP-remote metadata, and
    any value in the SP-remote metadata overrides the one configured
    in the IdP metadata.

`attributes.NameFormat`
:   What value will be set in the Format field of attribute
    statements. This parameter can be configured multiple places, and
    the actual value used is fetched from metadata by the following
    priority:

:   1.  SP Remote Metadata

    2.  IdP Hosted Metadata

:   The default value is:
    `urn:oasis:names:tc:SAML:2.0:attrname-format:basic`

:   Some examples of values specified in the SAML 2.0 Core
    Specification:

:   -   `urn:oasis:names:tc:SAML:2.0:attrname-format:unspecified`

    -   `urn:oasis:names:tc:SAML:2.0:attrname-format:uri` (The default
        in Shibboleth 2.0)

    -   `urn:oasis:names:tc:SAML:2.0:attrname-format:basic` (The
        default in Sun Access Manager)

:   You can also define your own value.

:   Note that this option also exists in the SP-remote metadata, and
    any value in the SP-remote metadata overrides the one configured
    in the IdP metadata.

:   (This option was previously named `AttributeNameFormat`.)

`encryption.blacklisted-algorithms`
:   Blacklisted encryption algorithms. This is an array containing the algorithm identifiers.

:   Note that this option can be set for each SP in the [SP-remote metadata](./simplesamlphp-reference-sp-remote).

:   The RSA encryption algorithm with PKCS#1 v1.5 padding is blacklisted by default for security reasons. Any assertions
    encrypted with this algorithm will therefore fail to decrypt. You can override this limitation by defining an empty
    array in this option (or blacklisting any other algorithms not including that one). However, it is strongly
    discouraged to do so. For your own safety, please include the string 'http://www.w3.org/2001/04/xmlenc#rsa-1_5' if
    you make use of this option.

`https.certificate`
:   The certificate used by the webserver when handling connections.
    This certificate will be added to the generated metadata of the IdP,
    which is required by some SPs when using the HTTP-Artifact binding.

`nameid.encryption`
:   Whether NameIDs sent from this IdP should be encrypted. The default
    value is `FALSE`.

:   Note that this option can be set for each SP in the [SP-remote metadata](./simplesamlphp-reference-sp-remote).

`NameIDFormat`
:   The format(s) of the NameID supported by this IdP, as either an array or a string. If an array is given, the first
    value is used as the default if the incoming request does not specify a preference. Defaults to the `transient`
    format if unspecified.

:   This parameter can be configured in multiple places, and the actual value used is fetched from metadata with
    the following priority:

:   1.  SP Remote Metadata

    2.  IdP Hosted Metadata

:   The three most commonly used values are:

:   1.  `urn:oasis:names:tc:SAML:2.0:nameid-format:transient`
    2.  `urn:oasis:names:tc:SAML:2.0:nameid-format:persistent`
    3.  `urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress`

:   The `transient` format will generate a new unique ID every time
    the user logs in.

:   To properly support the `persistent` and `emailAddress` formats,
    you should configure [NameID generation filters](./saml:nameid)
    on your IdP.

:   Note that the value(s) set here will be added to the metadata generated for this IdP,
    in the `NameIDFormat` element.

`RegistrationInfo`
:   Allows to specify information about the registrar of this SP. Please refer to the
    [MDRPI extension](./simplesamlphp-metadata-extensions-rpi) document for further information.

`saml20.ecp`
:   Set to `true` to enable the IdP to recieve authnrequests and send responses according the Enhanced Client or Proxy (ECP) Profile. Note: authentication filters that require interaction with the user will not work with ECP.
    Defaults to `false`.

`saml20.hok.assertion`
:   Set to `TRUE` to enable the IdP to send responses according the [Holder-of-Key Web Browser SSO Profile](./simplesamlphp-hok-idp).
    Defaults to `FALSE`.

`saml20.sendartifact`
:   Set to `TRUE` to enable the IdP to send responses with the HTTP-Artifact binding.
    Defaults to `FALSE`.

:   Note that this requires a configured memcache server.

`saml20.sign.assertion`
:   Whether `<saml:Assertion>` elements should be signed.
    Defaults to `TRUE`.

:   Note that this option also exists in the SP-remote metadata, and
    any value in the SP-remote metadata overrides the one configured
    in the IdP metadata.

`saml20.sign.response`
:   Whether `<samlp:Response>` messages should be signed.
    Defaults to `TRUE`.

:   Note that this option also exists in the SP-remote metadata, and
    any value in the SP-remote metadata overrides the one configured
    in the IdP metadata.

`signature.algorithm`
:   The algorithm to use when signing any message generated by this identity provider. Defaults to RSA-SHA256.
:   Possible values:

    * `http://www.w3.org/2000/09/xmldsig#rsa-sha1`
       *Note*: the use of SHA1 is **deprecated** and will be disallowed in the future.
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha256`
       The default.
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha384`
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha512`

`sign.logout`
:   Whether to sign logout messages sent from this IdP.

:   Note that this option also exists in the SP-remote metadata, and
    any value in the SP-remote metadata overrides the one configured
    in the IdP metadata.

`SingleSignOnService`
:   Override the default URL for the SingleSignOnService for this
    IdP. This is an absolute URL. The default value is
    `<SimpleSAMLphp-root>/saml2/idp/SSOService.php`

:   Note that this only changes the values in the generated
    metadata and in the messages sent to others. You must also
    configure your webserver to deliver this URL to the correct PHP
    page.

`SingleSignOnServiceBinding`
:	List of SingleSignOnService bindings that the IdP will claim support for.
:	Possible values:

	* `urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect`
	* `urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST`

:	Defaults to HTTP-Redirect binding. Please note that the order
	specified will be kept in the metadata, making the first binding
	the default one.

`SingleLogoutService`
:   Override the default URL for the SingleLogoutService for this
    IdP. This is an absolute URL. The default value is
    `<SimpleSAMLphp-root>/saml2/idp/SingleLogoutService.php`

:   Note that this only changes the values in the generated
    metadata and in the messages sent to others. You must also
    configure your webserver to deliver this URL to the correct PHP
    page.

`SingleLogoutServiceBinding`
:   List of SingleLogoutService bindings the IdP will claim support for.
:	Possible values:

	* `urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect`
	* `urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST`

:	Defaults to HTTP-Redirect binding. Please note that the order
	specified will be kept in the metadata, making the first binding
	the default one.

`validate.authnrequest`
:   Whether we require signatures on authentication requests sent to this IdP.

:   Note that this option also exists in the SP-remote metadata, and
    any value in the SP-remote metadata overrides the one configured
    in the IdP metadata.

`validate.logout`
:   Whether we require signatures on logout messages sent to this IdP.

:   Note that this option also exists in the SP-remote metadata, and
    any value in the SP-remote metadata overrides the one configured
    in the IdP metadata.


### Fields for signing and validating messages

SimpleSAMLphp only signs authentication responses by default.
Signing of logout requests and logout responses can be enabled by
setting the `redirect.sign` option. Validation of received messages
can be enabled by the `redirect.validate` option.

These options set the default for this IdP, but options for each SP
can be set in `saml20-sp-remote`. Note that you need to add a
certificate for each SP to be able to validate signatures on
messages from that SP.

`redirect.sign`
:   Whether logout requests and logout responses sent from this IdP
    should be signed. The default is `FALSE`.

`redirect.validate`
:   Whether authentication requests, logout requests and logout
    responses received sent from this IdP should be validated. The
    default is `FALSE`


**Example: Configuration for signed messages**

     'redirect.sign' => true,


Shibboleth 1.3 options
----------------------

Note that Shibboleth 1.3 support is deprecated and will be removed in the next major release of SimpleSAMLphp.

The following options for Shibboleth 1.3 IdP's are avaiblable:

`scopedattributes`
:   Array with names of attributes which should be scoped. Scoped
    attributes will receive a `Scope`-attribute on the
    `AttributeValue`-element. The value of the Scope-attribute will
    be taken from the attribute value:

:   `<AttributeValue>someuser@example.org</AttributeValue>`

:   will be transformed into

:   `<AttributeValue Scope="example.org">someuser</AttributeValue>`

:   By default, no attributes are scoped. Note that this option also
    exists in the SP-remote metadata, and any value in the SP-remote
    metadata overrides the one configured in the IdP metadata.


Metadata extensions
-------------------

SimpleSAMLphp supports generating metadata with the MDUI, MDRPI and EntityAttributes metadata extensions.
See the documentation for those extensions for more details:

  * [MDUI extension](./simplesamlphp-metadata-extensions-ui)
  * [MDRPI extension](./simplesamlphp-metadata-extensions-rpi)
  * [EntityAttributes](./simplesamlphp-metadata-extensions-attributes)


Examples
--------

These are some examples of IdP metadata

### Minimal SAML 2.0 / Shibboleth 1.3 IdP ###

    <?php
    /*
     * We use the '__DYNAMIC:1__' entity ID so that the entity ID
     * will be autogenerated.
     */
    $metadata['__DYNAMIC:1__'] = [
        /*
         * We use '__DEFAULT__' as the hostname so we won't have to
         * enter a hostname.
         */
        'host' => '__DEFAULT__',

        /* The private key and certificate used by this IdP. */
        'certificate' => 'example.org.crt',
        'privatekey' => 'example.org.pem',

        /*
         * The authentication source for this IdP. Must be one
         * from config/authsources.php.
         */
        'auth' => 'example-userpass',
    ];
