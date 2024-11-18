Adding HTTP-Artifact support to the IdP
=======================================

This document describes the necessary steps to enable support for the HTTP-Artifact binding on a SimpleSAMLphp IdP:

1.  Configure SimpleSAMLphp to use memcache to store the session.
2.  Enable support for sending artifacts in `saml20-idp-hosted`.
3.  Add the webserver certificate to the generated metadata.


Memcache
--------

To enable memcache, you must first install and configure memcache on the server hosting your IdP.
You need both a memcache server and a the PHP memcached client (extension).

How this is done depends on the distribution.
If you are running Debian or Ubuntu, you can install this by running:

    apt install memcached php-memcached

simpleSAMLphp also supports the legacy `php-memcache` (without `d`) variant.

*Note*: For security, you must make sure that the memcache server is inaccessible to other hosts.
The default configuration on Debian is for the memcache server to be accessible to only the local host.


Once the memcache server is configured, you can configure simplesamlphp to use it to store sessions.
You can do this by setting the `store.type` option in `config.php` to `memcache`.
If you are running memcache on a different server than the IdP, you must also change the `memcache_store.servers` option in `config.php`.


Enabling artifact on the IdP
----------------------------

To enable the IdP to send artifacts, you must add the `saml20.sendartifact` option to the `saml20-idp-hosted` metadata file:

    $metadata['__DYNAMIC:1__'] = [
        [....]
        'auth' => 'example-userpass',
        'saml20.sendartifact' => TRUE,
    ];


Add new metadata to SPs
-----------------------

After enabling the Artifact binding, your IdP metadata will change to add a ArtifactResolutionService endpoint.
You therefore need to update the metadata for your IdP at your SPs.
`saml20-idp-remote` metadata for SimpleSAMLphp SPs should contain something like:

    'ArtifactResolutionService' => [
        [
            'index' => 0,
            'Location' => 'https://idp.example.org/simplesaml/saml2/idp/ArtifactResolutionService.php',
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
        ],
    ],


SP metadata on the IdP
----------------------

An SP using the HTTP-Artifact binding must have an AssertionConsumerService endpoint supporting that binding.
This means that you must use the complex endpoint format in `saml20-sp-remote` metadata.
In general, that should look something like:

    'AssertionConsumerService' => array (
        [
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
            'Location' => 'https://sp.example.org/simplesaml/module.php/saml/sp/saml2-acs.php/default-sp',
            'index' => 0,
        ],
        [
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact',
            'Location' => 'https://sp.example.org/simplesaml/module.php/saml/sp/saml2-acs.php/default-sp',
            'index' => 2,
        ],
    ),

(The specific values of the various fields will vary depending on the SP.)


Certificate in metadata
-----------------------

Some SPs validates the SSL certificate on the ArtifactResolutionService using the certificates in the metadata.
You may therefore have to add the webserver certificate to the metadata that your IdP generates.
To do this, you need to set the `https.certificate` option in the `saml20-idp-hosted` metadata file.
That option should refer to a file containing the webserver certificate.

    $metadata['__DYNAMIC:1__'] = [
        [....]
        'auth' => 'example-userpass',
        'saml20.sendartifact' => TRUE,
        'https.certificate' => '/etc/apache2/webserver.crt',
    ];
