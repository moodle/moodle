Scoped Attributes Filtering
===========================

This document describes the **FilterScopes** attribute filter in the saml module.

This filter allows a Service Provider to make sure the scopes included in the values
of certain attributes correspond to what the Identity Provider declares in its
metadata. If the IdP includes a list of scopes in the metadata, only those scopes will
be allowed. On the other hand, if no scopes are declared or the scope is not included
in the list of declared scopes, it will be matched against the domain used by the
SAML `SingleSignOnService` endpoint. This means the `example.com` scope will be
allowed in attributes received from an IdP whose `SingleSignOnService` endpoint
is located on the `example.com` top domain or any subdomain of that. Such scope will
be rejected though if the match with the IdP's endpoint does not happen at the top
level, like for example with `example.com.domain.net`.

If you are configuring the metadata of an IdP manually, remember to add an array
to it with the key `scope`, containing the list of scopes expected from that entity.

Configuration
-------------

This filter can be configured in the `config/authsources.php` file, inside the
`authproc` array of the corresponding SAML authentication source in use.

Note that this filter **can only be used with SAML authentication sources**.

Here are the options available for the filter:

`attributes`
:   An array containing a list of attributes that are scoped and therefore should be evaluated.
    Defaults to _eduPersonPrincipalName_ and _eduPersonScopedAffiliation_.


Examples
--------

Basic configuration:
```
    'authproc' => [
        90 => [
            'class' => 'saml:FilterScopes',
        ],
    ],
```

Specify `mail` and `eduPersonPrincipalName` as scoped attributes:
```
    'authproc' => [
        90 => [
            'class' => 'saml:FilterScopes',
            'attributes' => [
                'mail',
                'eduPersonPrincipalName',
            ],
        ],
    ],
```

Specify the same attributes in OID format:
```
    'authproc' => [
        90 => [
            'class' => 'saml:FilterScopes',
            'attributes' => [
                'urn:oid:0.9.2342.19200300.100.1.3',
                'urn:oid:1.3.6.1.4.1.5923.1.1.1.6',
            ],
        ],
    ],
```
