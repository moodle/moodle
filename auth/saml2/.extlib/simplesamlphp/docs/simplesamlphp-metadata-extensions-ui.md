SAML V2.0 Metadata Extensions for Login and Discovery User Interface
=============================

<!--
    This file is written in Markdown syntax.
    For more information about how to use the Markdown syntax, read here:
    http://daringfireball.net/projects/markdown/syntax
-->

  * Author: Timothy Ace [tace@synacor.com](mailto:tace@synacor.com)

<!-- {{TOC}} -->

This is a reference for the SimpleSAMLphp implementation of the [SAML
V2.0 Metadata Extensions for Login and Discovery User Interface](http://docs.oasis-open.org/security/saml/Post2.0/sstc-saml-metadata-ui/v1.0/sstc-saml-metadata-ui-v1.0.pdf)
defined by OASIS.

The metadata extensions are available to both IdP and SP usage of
SimpleSAMLphp. For an IdP, the entries are placed in
`metadata/saml20-idp-hosted.php`, for an SP, they are put inside
the relevant entry in `authsources.php`.

An example for an IdP:

    <?php
    $metadata['entity-id-1'] = [
        /* ... */
        'UIInfo' => [
            'DisplayName' => [
                'en' => 'English name',
                'es' => 'Nombre en Español',
            ],
            'Description' => [
                'en' => 'English description',
                'es' => 'Descripción en Español',
            ],
            'InformationURL' => [
                'en' => 'http://example.com/info/en',
                'es' => 'http://example.com/info/es',
            ],
            'PrivacyStatementURL' => [
                'en' => 'http://example.com/privacy/en',
                'es' => 'http://example.com/privacy/es',
            ],
            'Keywords' => [
                'en' => ['communication', 'federated session'],
                'es' => ['comunicación', 'sesión federated'],
            ],
            'Logo' => [
                [
                    'url'    => 'http://example.com/logo1.png',
                    'height' => 200,
                    'width'  => 400,
                    'lang'   => 'en',
                ],
                [
                    'url'    => 'http://example.com/logo2.png',
                    'height' => 201,
                    'width'  => 401,
                ],
            ],
        ],
        'DiscoHints' => [
            'IPHint'          => ['130.59.0.0/16', '2001:620::0/96'],
            'DomainHint'      => ['example.com', 'www.example.com'],
            'GeolocationHint' => ['geo:47.37328,8.531126', 'geo:19.34343,12.342514'],
        ],
        /* ... */
    ];

And for an SP it could look like this:

    <?php
    $config = [

        'default-sp' => [
            'saml:SP',

            'UIInfo' => [
                'DisplayName' => [
                    'en' => 'English name',
                    'es' => 'Nombre en Español'
                ],
                'Description' => [
                    'en' => 'English description',
                    'es' => 'Descripción en Español'
                ],
            ],
            /* ... */
        ],
    ];

The OASIS specification primarily defines how an entity can communicate
metadata related to IdP or service discovery and identification. There
are two different types of
extensions defined. There are the `<mdui:UIInfo>`elements that define
how an IdP or SP should be displayed and there are the `<mdui:DiscoHints>`
elements that define when an IdP should be chosen/displayed.

UIInfo Items
--------------

These elements are used for IdP and SP discovery to determine what to display
about an IdP or SP. These properties are all children of the `UIInfo` key.

*Note*: Most elements are localized strings that specify the language
using the array key as the language-code:

            'DisplayName' => [
                'en' => 'English name',
                'es' => 'Nombre en Español',
            ],

`DisplayName`
:   The localized list of names for this entity

            'DisplayName' => [
                'en' => 'English name',
                'es' => 'Nombre en Español',
            ],

`Description`
:   The localized list of statements used to describe this entity

            'Description' => [
                'en' => 'English description',
                'es' => 'Descripción en Español',
            ],

`InformationURL`
:   A localized list of URLs where more information about the entity is
    located.

            'InformationURL' => [
                'en' => 'http://example.com/info/en',
                'es' => 'http://example.com/info/es',
            ],

`PrivacyStatementURL`
:   A localized list of URLs where the entity's privacy statement is
    located.

            'PrivacyStatementURL' => [
                'en' => 'http://example.com/privacy/en',
                'es' => 'http://example.com/privacy/es',
            ],

`Keywords`
:   A localized list of keywords used to describe the entity

            'Keywords' => [
                'en' => ['communication', 'federated session'],
                'es' => ['comunicación', 'sesión federated'],
            ],

:   *Note*: The `+` (plus) character is forbidden by specification from
    being part of a Keyword.

`Logo`
:   The logos used to represent the entity

            'Logo' => [
                [
                    'url'    => 'http://example.com/logo1.png',
                    'height' => 200,
                    'width'  => 400,
                    'lang'   => 'en',
                ],
                [
                    'url'    => 'http://example.com/logo2.png',
                    'height' => 201,
                    'width'  => 401,
                ],
            ],

:   An optional `lang` key containing a language-code is supported for
    localized logos.

DiscoHints Items
--------------

These elements are only relevant when operating in the IdP role; they
assist IdP discovery to determine when to choose or
present an IdP. These properties are all children of the `DiscoHints`
key.

`IPHint`
:   This is a list of both IPv4 and IPv6 addresses in CIDR notation
    services by or associated with this entity.

            'IPHint' => ['130.59.0.0/16', '2001:620::0/96'],

`DomainHint`
:   This specifies a list of domain names serviced by or associated with
    this entity.

            'DomainHint' => ['example.com', 'www.example.com'],

`GeolocationHint`
:   This specifies a list of geographic coordinates associated with, or
    serviced by, the entity. Coordinates are given in URI form using the
    geo URI scheme [RFC5870](http://www.ietf.org/rfc/rfc5870.txt).

            'GeolocationHint' => ['geo:47.37328,8.531126', 'geo:19.34343,12.342514'],


Generated XML Metadata Examples
----------------

If given the following configuration...

    $metadata['https://www.example.com/saml/saml2/idp/metadata.php'] = [
        'host' => 'www.example.com',
        'certificate' => 'example.com.crt',
        'privatekey' => 'example.com.pem',
        'auth' => 'example-userpass',

        'UIInfo' => [
            'DisplayName' => [
                'en' => 'English name',
                'es' => 'Nombre en Español',
            ],
            'Description' => [
                'en' => 'English description',
                'es' => 'Descripción en Español',
            ],
            'InformationURL' => [
                'en' => 'http://example.com/info/en',
                'es' => 'http://example.com/info/es',
            ],
            'PrivacyStatementURL' => [
                'en' => 'http://example.com/privacy/en',
                'es' => 'http://example.com/privacy/es',
            ],
            'Keywords' => [
                'en' => ['communication', 'federated session'],
                'es' => ['comunicación', 'sesión federated'],
            ],
            'Logo' => [
                [
                    'url'    => 'http://example.com/logo1.png',
                    'height' => 200,
                    'width'  => 400,
                ],
                [
                    'url'    => 'http://example.com/logo2.png',
                    'height' => 201,
                    'width'  => 401,
                ],
            ],
        ],
        'DiscoHints' => [
            'IPHint'          => ['130.59.0.0/16', '2001:620::0/96'],
            'DomainHint'      => ['example.com', 'www.example.com'],
            'GeolocationHint' => ['geo:47.37328,8.531126', 'geo:19.34343,12.342514'],
        ],
    ];

... will generate the following XML metadata:

    <?xml version="1.0"?>
    <md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" xmlns:mdattr="urn:oasis:names:tc:SAML:metadata:attribute" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:mdui="urn:oasis:names:tc:SAML:metadata:ui" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" entityID="https://www.example.com/saml/saml2/idp/metadata.php">
      <md:IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
        <md:Extensions>
          <mdui:UIInfo xmlns:mdui="urn:oasis:names:tc:SAML:metadata:ui">
            <mdui:DisplayName xml:lang="en">English name</mdui:DisplayName>
            <mdui:DisplayName xml:lang="es">Nombre en Espa&#xF1;ol</mdui:DisplayName>
            <mdui:Description xml:lang="en">English description</mdui:Description>
            <mdui:Description xml:lang="es">Descripci&#xF3;n en Espa&#xF1;ol</mdui:Description>
            <mdui:InformationURL xml:lang="en">http://example.com/info/en</mdui:InformationURL>
            <mdui:InformationURL xml:lang="es">http://example.com/info/es</mdui:InformationURL>
            <mdui:PrivacyStatementURL xml:lang="en">http://example.com/privacy/en</mdui:PrivacyStatementURL>
            <mdui:PrivacyStatementURL xml:lang="es">http://example.com/privacy/es</mdui:PrivacyStatementURL>
            <mdui:Keywords xml:lang="en">communication federated+session</mdui:Keywords>
            <mdui:Keywords xml:lang="es">comunicaci&#xF3;n sesi&#xF3;n+federated</mdui:Keywords>
            <mdui:Logo width="400" height="200" xml:lang="en">http://example.com/logo1.png</mdui:Logo>
            <mdui:Logo width="401" height="201">http://example.com/logo2.png</mdui:Logo>
          </mdui:UIInfo>
          <mdui:DiscoHints xmlns:mdui="urn:oasis:names:tc:SAML:metadata:ui">
            <mdui:IPHint>130.59.0.0/16</mdui:IPHint>
            <mdui:IPHint>2001:620::0/96</mdui:IPHint>
            <mdui:DomainHint>example.com</mdui:DomainHint>
            <mdui:DomainHint>www.example.com</mdui:DomainHint>
            <mdui:GeolocationHint>geo:47.37328,8.531126</mdui:GeolocationHint>
            <mdui:GeolocationHint>geo:19.34343,12.342514</mdui:GeolocationHint>
          </mdui:DiscoHints>
        </md:Extensions>
        <md:KeyDescriptor use="signing">
          <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
            <ds:X509Data>
            ...

