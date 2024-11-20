SP remote metadata reference
============================

<!-- {{TOC}} -->

This is a reference for metadata options available for
`metadata/saml20-sp-remote.php` and `metadata/shib13-sp-remote.php`.
Both files have the following format:

    <?php
    /* The index of the array is the entity ID of this SP. */
    $metadata['entity-id-1'] = [
        /* Configuration options for the first SP. */
    ];
    $metadata['entity-id-2'] = [
        /* Configuration options for the second SP. */
    ];
    /* ... */


Common options
--------------

The following options are common between both the SAML 2.0 protocol
and Shibboleth 1.3 protocol:

`attributes`
:   This should indicate which attributes an SP should receive. It is
    used by for example the `consent:Consent` module to tell the user
    which attributes the SP will receive, and the `core:AttributeLimit`
    module to limit which attributes are sent to the SP.

`authproc`
:   Used to manipulate attributes, and limit access for each SP. See
    the [authentication processing filter manual](simplesamlphp-authproc).

`base64attributes`
:   Whether attributes sent to this SP should be base64 encoded. The
    default is `FALSE`.

`description`
:   A description of this SP. Will be used by various modules when they
    need to show a description of the SP to the user.

:   This option can be translated into multiple languages in the same
    way as the `name`-option.

`name`
:   The name of this SP. Will be used by various modules when they need
    to show a name of the SP to the user.

:   If this option is unset, the organization name will be used instead (if it is available).

:   This option can be translated into multiple languages by specifying
    the value as an array of language-code to translated name:

        'name' => [
            'en' => 'A service',
            'no' => 'En tjeneste',
        ],

`OrganizationName`
:   The name of the organization responsible for this SPP.
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
:   This is an absolute URL for where an user can find a privacypolicy
    for this SP. If set, this will be shown on the consent page.
    `%SPENTITYID%` in the URL will be replaced with the entity id of
    this service provider.

:   Note that this option also exists in the IdP-hosted metadata. This
    entry in the SP-remote metadata overrides the option in the
    IdP-hosted metadata.

:   *Note*: **deprecated** Will be removed in a future release; use the MDUI-extension instead

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

`AssertionConsumerService`
:   The URL of the AssertionConsumerService endpoint for this SP.
    This option is required - without it you will not be able to send
    responses back to the SP.

:   The value of this option is specified in one of several [endpoint formats](./simplesamlphp-metadata-endpoints).

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

:   Note that this option also exists in the IdP-hosted metadata. This
    entry in the SP-remote metadata overrides the option in the
    IdP-hosted metadata.

:   (This option was previously named `AttributeNameFormat`.)

`audience`
:   An array of additional entities to be added to the AudienceRestriction. By default the only audience is the SP's entityID. 

`certData`
:   The base64 encoded certificate for this SP. This is an alternative to storing the certificate in a file on disk and specifying the filename in the `certificate`-option.

`certificate`
:   Name of certificate file for this SP. The certificate is used to
    verify the signature of messages received from the SP (if
    `redirect.validate`is set to `TRUE`), and to encrypting assertions
    (if `assertion.encryption` is set to TRUE and `sharedkey` is
    unset.)

`encryption.blacklisted-algorithms`
:   Blacklisted encryption algorithms. This is an array containing the algorithm identifiers.

:   Note that this option also exists in the IdP-hosted metadata. This
    entry in the SP-remote metadata overrides the option in the
    [IdP-hosted metadata](./simplesamlphp-reference-idp-hosted).

:   The RSA encryption algorithm with PKCS#1 v1.5 padding is blacklisted by default for security reasons. Any assertions
    encrypted with this algorithm will therefore fail to decrypt. You can override this limitation by defining an empty
    array in this option (or blacklisting any other algorithms not including that one). However, it is strongly
    discouraged to do so. For your own safety, please include the string 'http://www.w3.org/2001/04/xmlenc#rsa-1_5' if
    you make use of this option.

`ForceAuthn`
:   Set this `TRUE` to force the user to reauthenticate when the IdP
    receives authentication requests from this SP. The default is
    `FALSE`.

`NameIDFormat`
:   The `NameIDFormat` this SP should receive. This may be specified as either a string or an array.

:   The three most commonly used values are:

:   1.  `urn:oasis:names:tc:SAML:2.0:nameid-format:transient`
    2.  `urn:oasis:names:tc:SAML:2.0:nameid-format:persistent`
    3.  `urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress`

:   The `transient` format will generate a new unique ID every time
    the SP logs in.

:   To properly support the `persistent` and `emailAddress` formats,
    you should configure [NameID generation filters](./saml:nameid)
    on your IdP.

`nameid.encryption`
:   Whether NameIDs sent to this SP should be encrypted. The default
    value is `FALSE`.

:   Note that this option also exists in the IdP-hosted metadata. This
    entry in the SP-remote metadata overrides the option in the
    [IdP-hosted metadata](./simplesamlphp-reference-idp-hosted).

`saml20.sign.response`
:   Whether `<samlp:Response>` messages should be signed.
    Defaults to `TRUE`.

:   Note that this option also exists in the IdP-hosted metadata.
    The value in the SP-remote metadata overrides the value in the IdP-hosted metadata.

`saml20.sign.assertion`
:   Whether `<saml:Assertion>` elements should be signed.
    Defaults to `TRUE`.

:   Note that this option also exists in the IdP-hosted metadata.
    The value in the SP-remote metadata overrides the value in the IdP-hosted metadata.

`signature.algorithm`
:   The algorithm to use when signing any message sent to this specific service provider. Defaults to RSA-SHA256.
:   Note that this option also exists in the IdP-hosted metadata.
    The value in the SP-remote metadata overrides the value in the IdP-hosted metadata.
:   Possible values:

    * `http://www.w3.org/2000/09/xmldsig#rsa-sha1`
       *Note*: the use of SHA1 is **deprecated** and will be disallowed in the future.
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha256`
      The default.
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha384`
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha512`

`signature.privatekey`
:   Name of private key file for this IdP, in PEM format. The filename is relative to the cert/-directory.
:   Note that this option also exists in the IdP-hosted metadata. This entry in the SP-remote metadata overrides the option `privatekey` in the IdP-hosted metadata.

`signature.privatekey_pass`
:   Passphrase for the private key. Leave this option out if the private key is unencrypted.
:   Note that this option only is used if `signature.privatekey` is present.

`signature.certificate`
:   Certificate file included by IdP for KeyInfo within the signature for the SP, in PEM format. The filename is relative to the cert/-directory.
:   If `signature.privatekey` is present and `signature.certificate` is left blank, X509Certificate will not be included with the signature.

`sign.logout`
:   Whether to sign logout messages sent to this SP.

:   Note that this option also exists in the IdP-hosted metadata.
    The value in the SP-remote metadata overrides the value in the IdP-hosted metadata.

`simplesaml.nameidattribute`
:   When the value of the `NameIDFormat`-option is set to either
    `email` or `persistent`, this is the name of the attribute which
    should be used as the value of the `NameID`. The attribute must
    be in the set of attributes exported to the SP (that is, be in
    the `attributes` array). For more advanced control over `NameID`,
    including the ability to specify any attribute regardless of
    the set sent to the SP, see the [NameID processing filters](./saml:nameid).
    Note that the value of the attribute is collected **after** authproc-filters have run.

:   Typical values can be `mail` for when using the `email` format,
    and `eduPersonTargetedID` when using the `persistent` format.

`simplesaml.attributes`
:   Whether the SP should receive any attributes from the IdP. The
    default value is `TRUE`.

`SingleLogoutService`
:   The URL of the SingleLogoutService endpoint for this SP.
    This option is required if you want to implement single logout for
    this SP. If the option isn't specified, this SP will not be logged
    out automatically when a single logout operation is initialized.

:   The value of this option is specified in one of several [endpoint formats](./simplesamlphp-metadata-endpoints).

`SingleLogoutServiceResponse`
:   The URL logout responses to this SP should be sent. If this option
    is unspecified, the `SingleLogoutService` endpoint will be used as
    the recipient of logout responses.

`SPNameQualifier`
:   SP NameQualifier for this SP. If not set, the IdP will set the
    SPNameQualifier to be the SP entity ID.

`validate.authnrequest`
:   Whether we require signatures on authentication requests sent from this SP.

:   Note that this option also exists in the IdP-hosted metadata.
    The value in the SP-remote metadata overrides the value in the IdP-hosted metadata.

`validate.logout`
:   Whether we require signatures on logout messages sent from this SP.

:   Note that this option also exists in the IdP-hosted metadata.
    The value in the SP-remote metadata overrides the value in the IdP-hosted metadata.


### Encrypting assertions

It is possible to encrypt the assertions sent to a SP. Currently the
only algorithm supported is `AES128_CBC` or `RIJNDAEL_128`.

There are two modes of encryption supported by SimpleSAMLphp. One is
symmetric encryption, in which case both the SP and the IdP needs to
share a key. The other mode is the use of public key encryption. In
that mode, the public key of the SP is extracted from the certificate
of the SP.

`assertion.encryption`
:   Whether assertions sent to this SP should be encrypted. The default
    value is `FALSE`.

:   Note that this option also exists in the IdP-hosted metadata. This
    entry in the SP-remote metadata overrides the option in the
    IdP-hosted metadata.

`sharedkey`
:   Symmetric key which should be used for encryption. This should be a
    128-bit, 192-bit or 256-bit key based on the algorithm used.
    If this option is not specified, public key encryption will be used instead.

`sharedkey_algorithm`
:   Algorithm which should be used for encryption. Possible values are:

    * http://www.w3.org/2001/04/xmlenc#aes128-cbc
    * http://www.w3.org/2001/04/xmlenc#aes192-cbc
    * http://www.w3.org/2001/04/xmlenc#aes256-cbc
    * http://www.w3.org/2009/xmlenc11#aes128-gcm
    * http://www.w3.org/2009/xmlenc11#aes192-gcm
    * http://www.w3.org/2009/xmlenc11#aes256-gcm

### Fields for signing and validating messages

SimpleSAMLphp only signs authentication responses by default.
Signing of logout requests and logout responses can be enabled by
setting the `redirect.sign` option. Validation of received messages
can be enabled by the `redirect.validate` option.

These options overrides the options set in `saml20-idp-hosted`.

`redirect.sign`
:   Whether logout requests and logout responses sent to this SP should
    be signed. The default is `FALSE`.

`redirect.validate`
:   Whether authentication requests, logout requests and logout
    responses received from this SP should be validated. The default is
    `FALSE`

**Example: Configuration for validating messages**

    'redirect.validate' => TRUE,
    'certificate' => 'example.org.crt',

### Fields for scoping

Only relevant if you are a proxy/bridge and wants to limit the idps this
sp can use.

`IDPList`
: The list of scoped idps ie. the list of entityids for idps that are
relevant for this sp. The final list is the concatenation of the list
given as parameter to InitSSO (at the sp), the list configured at the
sp and the list configured at the ipd (here) for this sp. The intersection
of the final list and the idps configured at the at this idp will be
presented to the user at the discovery service if neccessary. If only one
idp is in the intersection the discoveryservice will go directly to the idp.

**Example: Configuration for scoping**


     'IDPList' => ['https://idp1.wayf.dk', 'https://idp2.wayf.dk'],
     

Shibboleth 1.3 options
----------------------

Note that Shibboleth 1.3 support is deprecated and will be removed in the next major release of SimpleSAMLphp.

The following options for Shibboleth 1.3 SP's are avaiblable:

`audience`
:   The value which should be given in the `<Audience>`-element in the
    `<AudienceRestrictionCondition>`-element in the response. The
    default value is the entity ID of the SP.

`AssertionConsumerService`
:   The URL of the AssertionConsumerService endpoint for this SP.
    This endpoint must accept the SAML responses encoded with the
    `urn:oasis:names:tc:SAML:1.0:profiles:browser-post` encoding.
    This option is required - without it you will not be able to send
    responses back to the SP.

:   The value of this option is specified in one of several [endpoint formats](./simplesamlphp-metadata-endpoints).

`NameQualifier`
:   What the value of the `NameQualifier`-attribute of the
    `<NameIdentifier>`-element should be. The default value is the
    entity ID of the SP.

`scopedattributes`
:   Array with names of attributes which should be scoped. Scoped
    attributes will receive a `Scope`-attribute on the
    `AttributeValue`-element. The value of the Scope-attribute will
    be taken from the attribute value:

:   `<AttributeValue>someuser@example.org</AttributeValue>`

:   will be transformed into

:   `<AttributeValue Scope="example.org">someuser</AttributeValue>`

:   By default, no attributes are scoped. This option overrides the
    option with the same name in the `shib13-idp-hosted.php` metadata
    file.
