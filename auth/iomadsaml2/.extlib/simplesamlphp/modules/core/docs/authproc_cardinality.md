`core:Cardinality`
==================

Ensure the number of attribute values is within the specified multiplicity.

This filter should contain a set of attribute name => rule pairs describing the multiplicity rules for an attribute.

The special parameter `%ignoreEntities` can be used to give an array of entity IDs that should be ignored for testing, etc purposes.

A separate [`core:CardinalitySingle`](./core:authproc_cardinalitysingle) authproc filter provides additional functionality for the special case where attributes are single valued.

Specifying Rules
----------------

Multiplicity rules are specified as an associative array containing one or more of the following parameters:

`min`
:   The minimum number of values (participation) this attribute should have. Defaults to `zero`.

`max`
:   The maximum number of values (cardinality) this attribute should have. Defaults to no upper bound.

`warn`
:   Log a warning rather than generating an error. Defaults to `false`.

For convenience, minimum and maximum values can also be specified using a shorthand list notation.

Examples
--------

Require at least one `givenName`, no more than two email addresses, and between two and four values for `eduPersonScopedAffiliation`.

    'authproc' => array(
        50 => array(
            'class' => 'core:Cardinality',
            'givenName' => array('min' => 1),
            'mail' => array('max' => 2),
            'eduPersonScopedAffiliation' => array('min' => 2, 'max' => 4),
        ),
    ),

Use the shorthand notation for min, max:

    'authproc' => array(
        50 => array(
            'class' => 'core:Cardinality',
            'mail' => array(0, 2),
        ),
    ),
