Using HTTP-Artifact from a SimpleSAMLphp SP
===========================================

This document describes how to use the HTTP-Artifact binding to receive authentication responses from the IdP.

Which binding the IdP should use when sending authentication responses is controlled by the `ProtocolBinding` in the SP configuration.
To make your Service Provider (SP) request that the response from the IdP is sent using the HTTP-Artifact binding, this option must be set to the HTTP-Artifact binding.

In addition to selecting the binding, you must also add a private key and certificate to your SP.
This is used for SSL client authentication when contacting the IdP.

To generate a private key and certificate, you may use the `openssl` commandline utility:

    openssl req -newkey rsa:3072 -new -x509 -days 3652 -nodes -out sp.example.org.crt -keyout sp.example.org.pem

You can then add the private key and certificate to the SP configuration.
When this is done, you can add the metadata of your SP to the IdP, and test the authentication.

Example configuration
---------------------

    'artifact-sp' => [
        'saml:SP',
        'ProtocolBinding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact',
        'privatekey' => 'sp.example.org.pem',
        'certificate' => 'sp.example.org.crt',
    ],

See the [SP configuration reference](./saml:sp) for a description of the options.
