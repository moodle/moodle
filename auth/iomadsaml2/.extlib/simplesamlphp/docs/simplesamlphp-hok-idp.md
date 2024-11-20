Adding Holder-of-Key Web Browser SSO Profile support to the IdP
===============================================================

This document describes the necessary steps to enable support for the [SAML V2.0 Holder-of-Key (HoK) Web Browser SSO Profile](http://docs.oasis-open.org/security/saml/Post2.0/sstc-saml-holder-of-key-browser-sso.pdf)
on a SimpleSAMLphp Identity Provider (IdP).

The SAML V2.0 HoK Web Browser SSO Profile is an alternate version of the standard SAML Web Browser SSO Profile. Its primary benefit is the enhanced security of the SSO process
while preserving maximum compatibility with existing deployments on client and server side.

When using this profile the communication between the user and the IdP is required to be protected by the TLS protocol. Additionally, the user needs a TLS client certificate.
This certificate is usually selfsigned and stored in the certificate store of the browser or the underlying operating system.

Configuring Apache
------------------

The IdP requests a client certificate from the user agent during the TLS handshake. This behaviour is enabled with the following Apache webserver configuration:

    SSLEngine on
    SSLCertificateFile /etc/openssl/certs/server.crt
    SSLCertificateKeyFile /etc/openssl/private/server.key
    SSLVerifyClient optional_no_ca
    SSLOptions +ExportCertData

If the user agent can successfully prove possession of the private key associated to the public key from the certificate, the received certificate is stored in the
environment variable `SSL_CLIENT_CERT` of the webserver. The IdP embeds the client certificate into the created HoK assertion.

Enabling HoK SSO Profile on the IdP
-----------------------------------

To enable the IdP to send HoK assertions you must add the `saml20.hok.assertion` option to the `saml20-idp-hosted` metadata file:

    $metadata['__DYNAMIC:1__'] = [
        [....]
        'auth' => 'example-userpass',
        'saml20.hok.assertion' => TRUE,
    ];

Add new metadata to SPs
-----------------------

After enabling the Holder-of-Key Web Browser SSO Profile your IdP metadata will change. An additional HoK `SingleSignOnService` endpoint is added.
You therefore need to update the metadata for your IdP at your SPs.
The `saml20-idp-remote` metadata for SimpleSAMLphp SPs should contain something like the following code:

	'SingleSignOnService' => array (
		array (
			'hoksso:ProtocolBinding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
			'Binding' => 'urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser',
			'Location' => 'https://idp.example.org/simplesaml/saml2/idp/SSOService.php',
		),
		array (
			'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
			'Location' => 'https://idp.example.org/simplesaml/saml2/idp/SSOService.php',
		),
	),

SP metadata on the IdP
----------------------

A SP using the HoK Web Browser SSO Profile must have an `AssertionConsumerService` endpoint supporting that profile.
This means that you have to use the complex endpoint format in `saml20-sp-remote` metadata.
In general, this should look like the following code:

	'AssertionConsumerService' => array (
		[
			'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
			'Location' => 'https://sp.example.org/simplesaml/module.php/saml/sp/saml2-acs.php/default-sp',
			'index' => 0,
		],
		[
			'Binding' => 'urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser',
			'Location' => 'https://sp.example.org/simplesaml/module.php/saml/sp/saml2-acs.php/default-sp',
			'index' => 4,
		],
	),

(The specific values of the various fields will vary depending on the SP.)
