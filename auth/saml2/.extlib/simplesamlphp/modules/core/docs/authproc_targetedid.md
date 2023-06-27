`core:TargetedID`
=================

This filter generates the `eduPersonTargetedID` attribute for the user.

By default, this filter will use the contents of the attribute set by the `userid.attribute` metadata option as the unique user ID.
You can also use a different attribute by setting the `attributename` option,

Parameters
----------

`attributename`
:   The name of the attribute we should use for the unique user identifier.
    Optional, will use the attribute set by the `userid.attribute` metadata option by default.
    *deprecated:* Please use `identifyingAttribute` instead.

`identifyingAttribute`
:   The name of the attribute we should use for the unique user identifier.
    Optional, will use the attribute set by the `userid.attribute` metadata option by default.

`nameId`
:   Set this option to `TRUE` to generate the attribute as in SAML 2 NameID format.
    This can be used to generate an Internet2 compatible `eduPersonTargetedID` attribute.
    Optional, defaults to `FALSE`.


Examples
--------

Using the attribute from `userid.attribute`:

    'authproc' => array(
        50 => array(
            'class' => 'core:TargetedID',
        ),
    ),

A custom attribute:

    'authproc' => array(
        50 => array(
            'class' => 'core:TargetedID',
            'identifyingAttribute' => 'eduPersonPrincipalName'
        ),
    ),

Internet2 compatible `eduPersontargetedID`:

    /* In saml20-idp-hosted.php. */
    $metadata['__DYNAMIC:1__'] = array(
        'host' => '__DEFAULT__',
        'auth' => 'example-static',

        'authproc' => array(
            60 => array(
                'class' => 'core:TargetedID',
                'nameId' => TRUE,
            ),
            90 => array(
                'class' => 'core:AttributeMap',
                'name2oid',
            ),
        ),
        'attributes.NameFormat' => 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri',
        'attributeencodings' => array(
            'urn:oid:1.3.6.1.4.1.5923.1.1.1.10' => 'raw', /* eduPersonTargetedID with oid NameFormat. */
        ),
    );
