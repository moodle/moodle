`core:AttributeRealm`
=====================

*NOTE:* This filter has been deprecated and will be removed in a future release. Please use
`core:ScopeFromAttribute` instead.

This filter creates a new attribute with the realm of the user.

The new attribute is names `realm` by default, but can be controlled by the `attributename` option.
The realm is extracted from the attribute set as the user ID (eduPersonPrincipalName by default).
The user ID attribute can be changed with the `userid.attribute` option in the IdP metadata.

Examples
--------

Create the `realm` attribute.

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeRealm',
        ),
    ),

Set the `schacHomeOrganization` attribute.

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeRealm',
            'attributename' => 'schacHomeOrganization',
        ),
    ),

