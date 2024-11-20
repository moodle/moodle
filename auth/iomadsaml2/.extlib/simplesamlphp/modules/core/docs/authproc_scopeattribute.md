`core:ScopeAttribute`
=====================

A filter which combines two attributes into a scoped attribute.
That is, the value will be `something@scope`, usually to make it globally unique.

Parameters
----------

`scopeAttribute`
:   The attribute that contains the scope.

:   If the attribute contains a '@', we will take the scope from the part following the '@'.
    Otherwise, we will use the entire value.

:   If the attribute is multi-valued, we will add all the scopes to the target.


`sourceAttribute`
:   The attribute that contains the values we shall add the scope to.

:   This attribute can be multi-valued, in which case we will add all the values.

`targetAttribute`
:   The attribute we shall add the scoped attributes to.

:   If the attribute already exists, the new values will be merged into the existing attribute.

`onlyIfEmpty`
:	Only replace the targetAttribute if it is empty to begin with.

:	If `true`, then the targetAttribute will only be created if it didn't already contain values. Defaults to `false`.

:	This is useful if, for instance, you want to create eduPersonScopedAffiliation from eduPersonAffiliation _only_ if eduPersonScopedAffiliation was not returned by the authenticaton source.

Example
-------

Add eduPersonScopedAffiliation based on eduPersonAffiliation and eduPersonPrincipalName.

    10 => array(
        'class' => 'core:ScopeAttribute',
        'scopeAttribute' => 'eduPersonPrincipalName',
        'sourceAttribute' => 'eduPersonAffiliation',
        'targetAttribute' => 'eduPersonScopedAffiliation',
    ),

With values being `eduPersonPrincipalName`: `jdoe@example.edu` and
`eduPersonAffiliation`: `faculty`, this will result in the attribute
`eduPersonScopedAffiliation` with value `faculty@example.edu`.
