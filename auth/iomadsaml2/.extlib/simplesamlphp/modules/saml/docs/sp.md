`saml:SP`
=========

This authentication source is used to authenticate against SAML 1 and SAML 2 IdPs.


Metadata
--------

The metadata for your SP will be available from the federation page on your SimpleSAMLphp installation.

SimpleSAMLphp supports generating metadata with the MDUI and MDRPI metadata extensions
and with entity attributes. See the documentation for those extensions for more details:

* [MDUI extension](../simplesamlphp-metadata-extensions-ui)
* [MDRPI extension](../simplesamlphp-metadata-extensions-rpi)
* [Attributes extension](../simplesamlphp-metadata-extensions-attributes)


Parameters
----------

These are parameters that can be used at runtime to control the authentication.
All these parameters override the equivalent option from the configuration.

`saml:AuthnContextClassRef`
:   The AuthnContextClassRef that will be sent in the login request.

:   *Note*: SAML 2 specific.

`saml:AuthnContextComparison`
:   The Comparison attribute of the AuthnContext that will be sent in the login request.
    This parameter won't be used unless `saml:AuthnContextClassRef` is set and contains one or more values.
    Possible values:
    
    * `SAML2\Constants::COMPARISON_EXACT` (default)
    * `SAML2\Constants::COMPARISON_BETTER`
    * `SAML2\Constants::COMPARISON_MINIMUM`
    * `SAML2\Constants::COMPARISON_MAXIMUM`
    
:   *Note*: SAML 2 specific.

`ForceAuthn`
:   Force authentication allows you to force re-authentication of users even if the user has a SSO session at the IdP.

:   *Note*: SAML 2 specific.

`saml:idp`
:   The entity ID of the IdP we should send an authentication request to.

`isPassive`
:   Send a passive authentication request.

:   *Note*: SAML 2 specific.

`saml:Extensions`
:   The samlp:Extensions that will be sent in the login request.

:   *Note*: SAML 2 specific.

`saml:NameID`
:   Add a Subject element with a NameID to the SAML AuthnRequest for the IdP.
    This must be a \SAML2\XML\saml\NameID object.

:   *Note*: SAML 2 specific.

`saml:NameIDPolicy`
:   The format of the NameID we request from the IdP: an array in the form of
    `[ 'Format' => the format, 'allowcreate' => true or false ]`.
    Set to `false` instead of an array to omit sending any specific NameIDPolicy
    in the AuthnRequest.

:   For compatibility purposes, `null` is equivalent to transient and a format
    can be defined as a string instead of an array. These variants are deprecated.

:   *Note*: SAML 2 specific.

`saml:Audience`
:   Add a Conditions element to the SAML AuthnRequest containing an
    AudienceRestriction with one or more audiences.

:   *Note*: SAML 2 specific.


Authentication data
-------------------

Some SAML-specific attributes are available to the application after authentication.
To retrieve these attributes, the application can use the `getAuthData()`-function from the [SP API](./simplesamlphp-sp-api).
The following attributes are available:

`saml:sp:IdP`
:   The entityID of the IdP the user is authenticated against.

`saml:sp:NameID`
:   The NameID the user was issued by the IdP.
    This is a \SAML2\XML\saml\NameID object with the various fields from the NameID.

`saml:sp:SessionIndex`
:   The SessionIndex we received from the IdP.


Options
-------

`acs.Bindings`
: List of bindings the SP should support. If it is unset, all will be added.
: Possible values:

    * `urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST`
    * `urn:oasis:names:tc:SAML:1.0:profiles:browser-post`
    * `urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact`
    * `urn:oasis:names:tc:SAML:1.0:profiles:artifact-01`
    * `urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser`

`assertion.encryption`
:   Whether assertions received by this SP must be encrypted. The default value is `FALSE`.
    If this option is set to `TRUE`, unencrypted assertions will be rejected.

:   Note that this option can be overridden for a specific IdP in saml20-idp-remote.

:   *Note*: SAML 2 specific.

`AssertionConsumerService`
:   List of Assertion Consumer Services in the generated metadata. Specified in the array of
    arrays format as seen in the [Metadata endpoints](./simplesamlphp-metadata-endpoints)
    documentation. Note: you must ensure that you set the `acs.Bindings` option to the set
    of bindings that you use here if it is different from the default.

`AssertionConsumerServiceIndex`
:   The Assertion Consumer Service Index to be used in the AuthnRequest in place of the Assertion
    Service Consumer URL.

`attributes`
:   List of attributes this SP requests from the IdP.
    This list will be added to the generated metadata.

:   The attributes will be added without a `NameFormat` by default.
    Use the `attributes.NameFormat` option to specify the `NameFormat` for the attributes.

:   An associative array can be used, mixing both elements with and without keys. When a key is
    specified for an element of the array, it will be used as the friendly name of the attribute
    in the generated metadata.

:   *Note*: This list will only be added to the metadata if the `name`-option is also specified.

`attributes.NameFormat`
:   The `NameFormat` for the requested attributes.

`attributes.index`
:   The `index` attribute that is set in the md:AttributeConsumingService element. Integer value that defaults to `0`.

`attributes.isDefault`
:   If present, sets the `isDefault` attribute in the md:AttributeConsumingService element. Boolean value, when
    unset, the attribute will be omitted.

`attributes.required`
: If you have attributes added you can here specify which should be marked as required.
: The attributes should still be present in `attributes`.

`AuthnContextClassRef`
:   The SP can request authentication with a specific authentication context class.
    One example of usage could be if the IdP supports both username/password authentication as well as software-PKI.

:   *Note*: SAML 2 specific.

`AuthnContextComparison`
:   The Comparison attribute of the AuthnContext that will be sent in the login request.
    This parameter won't be used unless `saml:AuthnContextClassRef` is set and contains one or more values.
    Possible values:
    
    * `SAML2\Constants::COMPARISON_EXACT` (default)
    * `SAML2\Constants::COMPARISON_BETTER`
    * `SAML2\Constants::COMPARISON_MINIMUM`
    * `SAML2\Constants::COMPARISON_MAXIMUM`
    
:   *Note*: SAML 2 specific.

`authproc`
:   Processing filters that should be run after SP authentication.
    See the [authentication processing filter manual](simplesamlphp-authproc).

`certData`
:   Base64 encoded certificate data. Can be used instead of the `certificate` option.

`certificate`
:   File name of certificate for this SP. This certificate will be included in generated metadata.

`contacts`
:   Specify contacts in addition to the `technical` contact configured through `config/config.php`.

:   For example, specifying a support contact:

        'contacts' => array(
            array(
                'contactType'       => 'support',
                'emailAddress'      => 'support@example.org',
                'givenName'         => 'John',
                'surName'           => 'Doe',
                'telephoneNumber'   => '+31(0)12345678',
                'company'           => 'Example Inc.',
            )
        ),

:   Valid values for `contactType` are: `technical`, `support`, `administrative`, `billing` and `other`. All
    fields, except `contactType` are OPTIONAL.

`description`
:   A description of this SP.
    Will be added to the generated metadata, in an AttributeConsumingService element.

:   This option can be translated into multiple languages by specifying the value as an array of language-code to translated description:

        'description' => array(
            'en' => 'A service',
            'no' => 'En tjeneste',
        ),

:   *Note*: For this to be added to the metadata, you must also specify the `attributes` and `name` options.

`disable_scoping`
:    Whether sending of samlp:Scoping elements in authentication requests should be suppressed. The default value is `FALSE`.
     When set to `TRUE`, no scoping elements will be sent. This does not comply with the SAML2 specification, but allows
     interoperability with ADFS which [does not support Scoping elements](https://docs.microsoft.com/en-za/azure/active-directory/develop/active-directory-single-sign-on-protocol-reference#scoping).

:   Note that this option also exists in the IdP remote configuration. An entry
    in the IdP-remote metadata overrides this the option in the SP
    configuration.

`discoURL`
:   Set which IdP discovery service this SP should use.
    If this is unset, the IdP discovery service specified in the global option `idpdisco.url.{saml20|shib13}` in `config/config.php` will be used.
    If that one is also unset, the builtin default discovery service will be used.

`encryption.blacklisted-algorithms`
:   Blacklisted encryption algorithms. This is an array containing the algorithm identifiers.

:   Note that this option can be set for each IdP in the [IdP-remote metadata](./simplesamlphp-reference-idp-remote).

:   *Note*: SAML 2 specific.

`entityID`
:   The entity ID this SP should use.

:   If this option is unset, a default entity ID will be generated.
    The generated entity ID will be a URL where the metadata of this SP can be downloaded.

`ForceAuthn`
:   Force authentication allows you to force re-authentication of users even if the user has a SSO session at the IdP.

:   *Note*: SAML 2 specific.

`idp`
:   The entity ID this SP should connect to.

:   If this option is unset, an IdP discovery service page will be shown.

`IsPassive`
:   IsPassive allows you to enable passive authentication by default for this SP.

:   *Note*: SAML 2 specific.

`name`
:   The name of this SP.
    Will be added to the generated metadata, in an AttributeConsumingService element.

:   This option can be translated into multiple languages by specifying the value as an array of language-code to translated name:

        'name' => array(
            'en' => 'A service',
            'no' => 'En tjeneste',
        ),

:   *Note*: You must also specify at least one attribute in the `attributes` option for this element to be added to the metadata.

`nameid.encryption`
:   Whether NameIDs sent from this SP should be encrypted. The default
    value is `FALSE`.

:   Note that this option can be set for each IdP in the [IdP-remote metadata](./simplesamlphp-reference-idp-remote).

:   *Note*: SAML 2 specific.

`NameIDPolicy`
:   The format of the NameID we request from the idp: an array in the form of
    `[ 'Format' => the format, 'AllowCreate' => true or false ]`.
    Set to `false` instead of an array to omit sending any specific NameIDPolicy
    in the AuthnRequest.

:   For compatibility purposes, `null` is equivalent to transient and a format
    can be defined as a string instead of an array. These variants are deprecated.

:   *Note*: SAML 2 specific.

`OrganizationName`
:   The name of the organization responsible for this SP.
    This name does not need to be suitable for display to end users.

:   This option can be translated into multiple languages by specifying the value as an array of language-code to translated name:

        'OrganizationName' => array(
            'en' => 'Example organization',
            'no' => 'Eksempel organisation',
        ),

:   *Note*: If you specify this option, you must also specify the `OrganizationURL` option.

`OrganizationDisplayName`
:   The name of the organization responsible for this SP.
    This name must be suitable for display to end users.
    If this option isn't specified, `OrganizationName` will be used instead.

:   This option can be translated into multiple languages by specifying the value as an array of language-code to translated name.

:   *Note*: If you specify this option, you must also specify the `OrganizationName` option.

`OrganizationURL`
:   A URL the end user can access for more information about the organization.

:   This option can be translated into multiple languages by specifying the value as an array of language-code to translated URL.

:   *Note*: If you specify this option, you must also specify the `OrganizationName` option.

`privatekey`
:   File name of private key to be used for signing messages and decrypting messages from the IdP. This option is only required if you use encrypted assertions or if you enable signing of messages.

:   *Note*: SAML 2 specific.

`privatekey_pass`
:   The passphrase for the private key, if it is encrypted. If the private key is unencrypted, this can be left out.

:   *Note*: SAML 2 specific.

`ProviderName`
:   Human readable name of the local SP sent with the authentication request.

:   *Note*: SAML 2 specific.

`ProtocolBinding`
:   The binding that should be used for SAML2 authentication responses.
    This option controls the binding that is requested through the AuthnRequest message to the IdP.
    By default the HTTP-Post binding is used.

:   *Note*: SAML 2 specific.

`redirect.sign`
:   Whether authentication requests, logout requests and logout responses sent from this SP should be signed. The default is `FALSE`.
    If set, the `AuthnRequestsSigned` attribute of the `SPSSODescriptor` element in SAML 2.0 metadata will contain its value. This
    option takes precedence over the `sign.authnrequest` option in any metadata generated for this SP.

:   *Note*: SAML 2 specific.

`redirect.validate`
:   Whether logout requests and logout responses received by this SP should be validated. The default is `FALSE`.

:   *Note*: SAML 2 specific.

`RegistrationInfo`
:   Allows to specify information about the registrar of this SP. Please refer to the
    [MDRPI extension](./simplesamlphp-metadata-extensions-rpi) document for further information.

`RelayState`
:   The page the user should be redirected to after an IdP initiated SSO.

:   *Note*: SAML 2 specific.
    For SAML 1.1 SPs, you must specify the `TARGET` parameter in the authentication response.
    How to set that parameter is depends on the IdP.
    For SimpleSAMLphp, see the documentation for [IdP-first flow](./simplesamlphp-idp-more#section_4_1).

`saml.SOAPClient.certificate`
:   A file with a certificate _and_ private key that should be used when issuing SOAP requests from this SP.
    If this option isn't specified, the SP private key and certificate will be used.

:   This option can also be set to `FALSE`, in which case no client certificate will be used.

`saml.SOAPClient.privatekey_pass`
:   The passphrase of the privatekey in `saml.SOAPClient.certificate`.

`saml1.useartifact`
:   Request that the IdP returns the result to the artifact binding.
    The default is to use the POST binding, set this option to TRUE to use the artifact binding instead.

:   This option can also be set in the `shib13-idp-remote` metadata, in which case the setting in `shib13-idp-remote` takes precedence.

:   *Note*: SAML 1 specific.

`saml20.hok.assertion`
:   Enable support for the SAML 2.0 Holder-of-Key SSO profile.
    See the documentation for the [Holder-of-Key profile](./simplesamlphp-hok-sp).

`sign.authnrequest`
:   Whether to sign authentication requests sent from this SP. If set, the `AuthnRequestsSigned` attribute of the
    `SPSSODescriptor` element in SAML 2.0 metadata will contain its value.

:   Note that this option also exists in the IdP-remote metadata, and
    any value in the IdP-remote metadata overrides the one configured
    in the SP configuration.

:   *Note*: SAML 2 specific.

`sign.logout`
:   Whether to sign logout messages sent from this SP.

:   Note that this option also exists in the IdP-remote metadata, and
    any value in the IdP-remote metadata overrides the one configured
    in the SP configuration.

:   *Note*: SAML 2 specific.

`signature.algorithm`
:   The algorithm to use when signing any message generated by this service provider. Defaults to RSA-SHA256.
:   Possible values:

    * `http://www.w3.org/2000/09/xmldsig#rsa-sha1`
       *Note*: the use of SHA1 is **deprecated** and will be disallowed in the future.
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha256`
      The default.
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha384`
    * `http://www.w3.org/2001/04/xmldsig-more#rsa-sha512`

`SingleLogoutServiceBinding`
:	List of SingleLogoutService bindings the SP will claim support for (can be empty).
:	Possible values:

    * `urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect`
    * `urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST`
    * `urn:oasis:names:tc:SAML:2.0:bindings:SOAP`

`SingleLogoutServiceLocation`
:   The Single Logout Service URL published in the generated metadata.

`url`
:   A URL to your service provider. Will be added as an OrganizationURL-element in the metadata.

:   This option can be translated into multiple languages by specifying the value as an array of language-code to language-specific URL:

        'url' => array(
            'en' => 'http://sp.example.net/en/info.html',
            'no' => 'http://sp.example.net/no/info.html',
        ),

`validate.logout`
:   Whether we require signatures on logout messages sent to this SP.

:   Note that this option also exists in the IdP-remote metadata, and
    any value in the IdP-remote metadata overrides the one configured
    in the IdP metadata.

:   *Note*: SAML 2 specific.

`WantAssertionsSigned`
:   Whether assertions received by this SP must be signed. The default value is `FALSE`.
    The value set for this option will be used to set the `WantAssertionsSigned` attribute of the `SPSSODescriptor` element in
    the exported SAML 2.0 metadata.


Examples
--------

Here we will list some examples for this authentication source.

### Minimal

    'example-minimal' => array(
        'saml:SP',
    ),

### Connecting to a specific IdP

    'example' => array(
        'saml:SP',
        'idp' => 'https://idp.example.net/',
    ),

### Using a specific entity ID

    'example' => array(
        'saml:SP',
        'entityID' => 'https://sp.example.net',
    ),

### Encryption and signing

    This SP will accept encrypted assertions, and will sign and validate all messages.

    'example-enc' => array(
        'saml:SP',

        'certificate' => 'example.crt',
        'privatekey' => 'example.key',
        'privatekey_pass' => 'secretpassword',
        'redirect.sign' => TRUE,
        'redirect.validate' => TRUE,
    ),


### Specifying attributes and required attributes

    An SP that wants eduPersonPrincipalName and mail, where eduPersonPrincipalName should be listed as required:

    'example-attributes => array(
        'saml:SP',
        'name' => array( // Name required for AttributeConsumingService-element.
            'en' => 'Example service',
            'no' => 'Eksempeltjeneste',
        ),
        'attributes' => array(
            'eduPersonPrincipalName',
            'mail',
            // Specify friendly names for these attributes:
            'sn' => 'urn:oid:2.5.4.4',
            'givenName' => 'urn:oid:2.5.4.42',
        ),
        'attributes.required' => array (
            'eduPersonPrincipalName',
        ),
        'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:basic',
    ),


### Limiting supported AssertionConsumerService endpoint bindings

    'example-acs-limit' => array(
        'saml:SP',
        'acs.Bindings' => array(
            'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
            'urn:oasis:names:tc:SAML:1.0:profiles:browser-post',
        ),
    ),


### Requesting a specific authentication method.

    $auth = new \SimpleSAML\Auth\Simple('default-sp');
    $auth->login(array(
        'saml:AuthnContextClassRef' => 'urn:oasis:names:tc:SAML:2.0:ac:classes:Password',
    ));

### Using samlp:Extensions

    $dom = \SAML2\DOMDocumentFactory::create();
    $ce = $dom->createElementNS('http://www.example.com/XFoo', 'xfoo:test', 'Test data!');
    $ext[] = new \SAML2\XML\Chunk($ce);

    $auth = new \SimpleSAML\Auth\Simple('default-sp');
    $auth->login(array(
        'saml:Extensions' => $ext,
    ));
