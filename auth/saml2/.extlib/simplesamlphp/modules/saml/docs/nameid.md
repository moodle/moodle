NameID generation filters
=========================

This document describes the NameID generation filters in the saml module.


Common options
--------------

`NameQualifier`
:   The NameQualifier attribute for the generated NameID.
    This can be a string that is used as the value directly.
    It can also be `TRUE`, in which case we use the IdP entity ID as the NameQualifier.
    If it is `FALSE`, no NameQualifier will be included.

:   The default is `FALSE`, which means that we will not include a NameQualifier by default.

`SPNameQualifier`
:   The SPNameQualifier attribute for the generated NameID.
    This can be a string that is used as the value directly.
    It can also be `TRUE`, in which case we use the SP entity ID as the SPNameQualifier.
    If it is `FALSE`, no SPNameQualifier will be included.

:   The default is `TRUE`, which means that we will use the SP entity ID.


`saml:AttributeNameID`
----------------------

Uses the value of an attribute to generate a NameID.

### Options

`attribute`
:   The name of the attribute we should use as the unique user ID.

`Format`
:   The `Format` attribute of the generated NameID.



`saml:PersistentNameID`
-----------------------

Generates a persistent NameID with the format `urn:oasis:names:tc:SAML:2.0:nameid-format:persistent`.
The filter will take the user ID from the attribute described in the `attribute` option, and hash it with the `secretsalt` from `config.php`, and the SP and IdP entity ID.
The resulting hash is sent as the persistent NameID.

### Options

`attribute`
:   The name of the attribute we should use as the unique user ID.


`saml:TransientNameID`
----------------------

Generates a transient NameID with the format `urn:oasis:names:tc:SAML:2.0:nameid-format:transient`.

No extra options are available for this filter.


`saml:SQLPersistentNameID`
--------------------------

Generates and stores persistent NameIDs in a SQL database.

This filter generates and stores a persistent NameID in a SQL database.
To use this filter, either specify the `store` option and a database,
or configure SimpleSAMLphp to use a SQL datastore.
See the `store.type` configuration option in `config.php`.

### Options

`attribute`
:   The name of the attribute we should use as the unique user ID.

`allowUnspecified`
:   Whether a persistent NameID should be created if the SP does not specify any NameID format in the request.
    The default is `FALSE`.

`allowDifferent`
:   Whether a persistent NameID should be created if there are only other NameID formats specified in the request or the SP's metadata.
    The default is `FALSE`.

`alwaysCreate`
:   Whether to ignore an explicit `AllowCreate="false"` in the authentication request's NameIDPolicy.
    The default is `FALSE`, which will only create new NameIDs when the SP specifies `AllowCreate="true"` in the authentication request.

`store`
:   An array of database options passed to `\SimpleSAML\Database`, keys prefixed with `database.`.
    The default is `[]`, which uses the global SQL datastore.

Setting both `allowUnspecified` and `alwaysCreate` to `TRUE` causes `saml:SQLPersistentNameID` to behave like `saml:PersistentNameID` (and other NameID generation filters), at the expense of creating unnecessary entries in the SQL datastore.


`saml:PersistentNameID2TargetedID`
----------------------------------

Stores a persistent NameID in the `eduPersonTargetedID`-attribute.

This filter is not actually a NameID generation filter.
Instead, it takes a persistent NameID and adds it as an attribute in the assertion.
This can be used to set the `eduPersonTargetedID`-attribute to the same value as the persistent NameID.

### Options

`attribute`
:   The name of the attribute we should store the result in.
    The default is `eduPersonTargetedID`.

`nameId`
:   Whether the generated attribute should be an saml:NameID element.
    The default is `TRUE`.



Example
-------

This example makes three NameIDs available:

    'authproc' => array(
        1 => array(
            'class' => 'saml:TransientNameID',
        ),
        2 => array(
            'class' => 'saml:PersistentNameID',
            'attribute' => 'eduPersonPrincipalName',
        ),
        3 => array(
            'class' => 'saml:AttributeNameID',
            'attribute' => 'mail',
            'Format' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
        ),
    ),

Storing persistent NameIDs in a SQL database:

    'authproc' => array(
        1 => array(
            'class' => 'saml:TransientNameID',
        ),
        2 => array(
            'class' => 'saml:SQLPersistentNameID',
            'attribute' => 'eduPersonPrincipalName',
        ),
    ),

Generating Persistent NameID and eduPersonTargetedID.

    'authproc' => array(
        // Generate the persistent NameID.
        2 => array(
            'class' => 'saml:PersistentNameID',
            'attribute' => 'eduPersonPrincipalName',
        ),
        // Add the persistent to the eduPersonTargetedID attribute
        60 => array(
            'class' => 'saml:PersistentNameID2TargetedID',
            'attribute' => 'eduPersonTargetedID', // The default
            'nameId' => TRUE, // The default
        ),
        // Use OID attribute names.
        90 => array(
            'class' => 'core:AttributeMap',
            'name2oid',
        ),
    ),
    // The URN attribute NameFormat for OID attributes.
    'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri',
    'attributeencodings' => array(
        'urn:oid:1.3.6.1.4.1.5923.1.1.1.10' => 'raw', /* eduPersonTargetedID with oid NameFormat is a raw XML value */
    ),
