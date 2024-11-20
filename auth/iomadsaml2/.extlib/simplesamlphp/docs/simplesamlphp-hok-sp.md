Using Holder-of-Key Web Browser SSO Profile on a SimpleSAMLphp SP
=================================================================

This document describes how to enable the [SAML V2.0 Holder-of-Key (HoK) Web Browser SSO Profile](http://docs.oasis-open.org/security/saml/Post2.0/sstc-saml-holder-of-key-browser-sso.pdf)
on a SimpleSAMLphp Service Provider (SP).

The SAML V2.0 HoK Web Browser SSO Profile is an alternate version of the standard SAML Web Browser SSO Profile. Its primary benefit is the enhanced security of the SSO process
while preserving maximum compatibility with existing deployments on client and server side.

When using this profile the communication between the user and the SP is required to be protected by the TLS protocol. Additionally, the user needs a TLS client certificate.
This certificate is usually selfsigned and stored in the certificate store of the browser or the underlying operating system.

Configuring Apache
------------------

The SP requests a client certificate from the user agent during the TLS handshake. This behaviour is enabled with the following Apache webserver configuration:

    SSLEngine on
    SSLCertificateFile /etc/openssl/certs/server.crt
    SSLCertificateKeyFile /etc/openssl/private/server.key
    SSLVerifyClient optional_no_ca
    SSLOptions +ExportCertData

If the user agent can successfully prove possession of the private key associated to the public key from the certificate, the received certificate is stored in the
environment variable `SSL_CLIENT_CERT` of the webserver.

Enable HoK on SP
----------------

To enable support for the HoK SSO Profile in the SP, the `saml20.hok.assertion` option must be set to TRUE in the SP configuration.
This option can also be enabled in the `saml20-idp-remote` metadata file, but in that case the endpoint will not be added to the SP metadata.
You must also send authentication requests specifying the Holder-of-Key profile to the IdP. This is controlled by the `ProtocolBinding` option in the SP configuration.

    'hok-sp' => [
        'saml:SP',
        'saml20.hok.assertion' => TRUE,
        'ProtocolBinding' => 'urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser',
    ],

When this is done, you can add the metadata of your SP to the IdP and test the authentication.
