SAML V2.0 Metadata Attribute Extensions
=======================================

<!--
	This file is written in Markdown syntax.
	For more information about how to use the Markdown syntax, read here:
	http://daringfireball.net/projects/markdown/syntax
-->

<!-- {{TOC}} -->

This is a reference for the SimpleSAMLphp implementation of the [SAML
V2.0 Attribute Extensions](http://docs.oasis-open.org/security/saml/Post2.0/sstc-saml-attribute-ext.pdf)
defined by OASIS. A common use case is adding entity attributes
to the generated metadata.

For an IdP `metadata/saml20-idp-hosted.php` entries are used to define the
metadata extension items; for an SP they can be added to `config/authsources.php`.
An example of this is:

    <?php
    $metadata['entity-id-1'] = [
        /* ... */
		'EntityAttributes' => [
			'urn:simplesamlphp:v1:simplesamlphp' => ['is', 'really', 'cool'],
			'{urn:simplesamlphp:v1}foo'          => ['bar'],
		],
        /* ... */
    ];

The OASIS specification primarily defines how to include arbitrary
`Attribute` and `Assertion` elements within the metadata for an entity.

*Note*: SimpleSAMLphp does not support `Assertion` elements within the
metadata at this time.

Defining Attributes
-------------------

The `EntityAttributes` key is used to define the attributes in the
metadata. Each item in the `EntityAttributes` array defines a new
`<Attribute>` item in the metadata. The value for each key must be an
array. Each item in this array produces a separte `<AttributeValue>`
element within the `<Attribute>` element.

		'EntityAttributes' => [
			'urn:simplesamlphp:v1:simplesamlphp' => ['is', 'really', 'cool'],
		],

This generates:

      <saml:Attribute xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" Name="urn:simplesamlphp:v1:simplesamlphp" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:uri">
        <saml:AttributeValue xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" xsi:type="xs:string">is</saml:AttributeValue>
        <saml:AttributeValue xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" xsi:type="xs:string">really</saml:AttributeValue>
        <saml:AttributeValue xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" xsi:type="xs:string">cool</saml:AttributeValue>
      </saml:Attribute>

Each `<Attribute>` element requires a `NameFormat` attribute. This is
specified using curly braces at the beginning of the key name:

		'EntityAttributes' => [
			'{urn:simplesamlphp:v1}foo' => ['bar'],
		],

This generates:

      <saml:Attribute xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" Name="foo" NameFormat="urn:simplesamlphp:v1">
        <saml:AttributeValue xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" xsi:type="xs:string">bar</saml:AttributeValue>
      </saml:Attribute>

When the curly braces are omitted, the NameFormat is automatically set
to "urn:oasis:names:tc:SAML:2.0:attrname-format:uri".

Examples
--------

If given the following configuration...

    $metadata['https://www.example.com/saml/saml2/idp/metadata.php'] = [
        'host' => 'www.example.com',
        'certificate' => 'example.com.crt',
        'privatekey' => 'example.com.pem',
        'auth' => 'example-userpass',

		'EntityAttributes' => [
			'urn:simplesamlphp:v1:simplesamlphp' => ['is', 'really', 'cool'],
			'{urn:simplesamlphp:v1}foo'          => ['bar'],
		],
	];

... will generate the following XML metadata:

	<?xml version="1.0"?>
	<md:EntityDescriptor xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata" xmlns:mdattr="urn:oasis:names:tc:SAML:metadata:attribute" xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:mdui="urn:oasis:names:tc:SAML:metadata:ui" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" entityID="https://www.example.com/saml/saml2/idp/metadata.php">
	  <md:Extensions>
		<mdattr:EntityAttributes xmlns:mdattr="urn:oasis:names:tc:SAML:metadata:attribute">
		  <saml:Attribute xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" Name="urn:simplesamlphp:v1:simplesamlphp" NameFormat="urn:oasis:names:tc:SAML:2.0:attrname-format:uri">
			<saml:AttributeValue xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" xsi:type="xs:string">is</saml:AttributeValue>
			<saml:AttributeValue xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" xsi:type="xs:string">really</saml:AttributeValue>
			<saml:AttributeValue xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" xsi:type="xs:string">cool</saml:AttributeValue>
		  </saml:Attribute>
		  <saml:Attribute xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" Name="foo" NameFormat="urn:simplesamlphp:v1">
			<saml:AttributeValue xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" xsi:type="xs:string">bar</saml:AttributeValue>
		  </saml:Attribute>
		</mdattr:EntityAttributes>
	  </md:Extensions>
	  <md:IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol">
		<md:KeyDescriptor use="signing">
		  <ds:KeyInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
			<ds:X509Data>
            ...


An example configuration to declare Géant Data Protection Code of Conduct
entity category support for a service provider in `authsources.php`:

    'saml:SP' => [
        ...
        'EntityAttributes' => [
            'http://macedir.org/entity-category' => [
                'http://www.geant.net/uri/dataprotection-code-of-conduct/v1'
            ]
        ],
        'UIInfo' =>[
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
        ]
    ],
