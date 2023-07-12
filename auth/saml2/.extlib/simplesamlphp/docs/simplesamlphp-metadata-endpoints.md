Metadata endpoints
==================

This document gives a short introduction to the various methods forms metadata endpoints can take in SimpleSAMLphp.

The endpoints we have are:

Endpoint                       | Indexed | Default binding
-------------------------------|---------|----------------
`ArtifactResolutionService`    | Y       | SOAP
`AssertionConsumerService`     | Y       | HTTP-POST
`SingleLogoutService`          | N       | HTTP-Redirect
`SingleSignOnService`          | N       | HTTP-Redirect


The various endpoints can be specified in three different ways:

  * A single string.
  * Array of strings.
  * Array of arrays.


A single string
---------------

    'AssertionConsumerService' => 'https://sp.example.org/ACS',

This is the simplest endpoint format.
It can be used when there is only a single endpoint that uses the default binding.


Array of strings
----------------

    'AssertionConsumerService' => [
        'https://site1.example.org/ACS',
        'https://site2.example.org/ACS',
    ],

This endpoint format can be used to represent multiple endpoints, all of which use the default binding.


Array of arrays
---------------

    'AssertionConsumerService' => [
        [
            'index' => 1,
            'isDefault' => TRUE,
            'Location' => 'https://sp.example.org/ACS',
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        ],
        [
            'index' => 2,
            'Location' => 'https://sp.example.org/ACS',
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact',
        ],
    ],

This endpoint format allows for specifying multiple endpoints with different bindings.
It can also be used to specify the ResponseLocation attribute on endpoints, e.g. on `SingleLogoutService`:

    'SingleLogoutService' => [
        [
            'Location' => 'https://sp.example.org/LogoutRequest',
            'ResponseLocation' => 'https://sp.example.org/LogoutResponse',
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ],
    ],

