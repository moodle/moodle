`core:ScopeFromAttribute`
=========================

This filter creates a new attribute based on the scope from a different attribute.


Parameters
----------

This filter has two parameters, where both parameters are mandatory.

`sourceAttribute`
:   The attribute we should extract the scope from.

`targetAttribute`
:   The name of the new attribute.



Example
-------

Set the `scope` attribute to the scope from the `eduPersonPrincipalName` attribute:

    'authproc' => array(
        50 => array(
            'class' => 'core:ScopeFromAttribute',
            'sourceAttribute' => 'eduPersonPrincipalName',
            'targetAttribute' => 'scope',
        ),
    ),
