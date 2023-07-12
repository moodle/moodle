`core:CardinalitySingle`
========================

Ensure the correct cardinality of single-valued attributes. This filter is a special case
of the more generic [`core:Cardinality`](./core:authproc_cardinality) filter that allows for optional corrective measures
when multi-valued attributes are received where single-valued ones are expected.

Parameters
----------

This filter implements a number of optional parameters:

`singleValued`
:   array of attribute names that *must* be single-valued, or a 403 error is generated.

`firstValue`
:   array of attribute names where only the first value of a multi-valued assertion should be returned.

`flatten`
:   array of attribute names where a multi-valued assertion is flattened into a single delimited string.

`flattenWith`
:   the delimiter for `flatten`. Defaults to ";".

`ignoreEntities`
:   array of entity IDs that should be ignored for testing, etc purposes.

When the same attribute name appears in multiple stanzas, they are processed in the order above.

Examples
--------

Abort with an error if any attribute defined as single-valued in the eduPerson or SCHAC schemas exists and has more than one value:

    'authproc' => array(
        50 => array(
            'class' => 'core:CardinalitySingle',
            'singleValued' => array(
                /* from eduPerson (internet2-mace-dir-eduperson-201602) */
                'eduPersonOrgDN', 'eduPersonPrimaryAffiliation', 'eduPersonPrimaryOrgUnitDN',
                'eduPersonPrincipalName', 'eduPersonUniqueId',
                /* from inetOrgPerson (RFC2798), referenced by internet2-mace-dir-eduperson-201602 */
                'displayName', 'preferredLanguage',
                /* from SCHAC-IAD Version 1.3.0 */
                'schacMotherTongue', 'schacGender', 'schacDateOfBirth', 'schacPlaceOfBirth',
                'schacPersonalTitle', 'schacHomeOrganization', 'schacHomeOrganizationType',
                'schacExpiryDate',
            ),
        ),
    ),

Abort if multiple values are received for `eduPersonPrincipalName`, but take the first value for `eduPersonPrimaryAffiliation`:

    'authproc' => array(
        50 => array(
            'class' => 'core:CardinalitySingle',
            'singleValued' => array('eduPersonPrincipalName'),
            'firstValue' => array('eduPersonPrimaryAffiliation'),
            ),
        ),
    ),

Construct `eduPersonPrimaryAffiliation` using the first value in `eduPersonAffiliation`:

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeCopy',
            'eduPersonAffiliation' => 'eduPersonPrimaryAffiliation',
        ),
        51 => array(
            'class' => 'core:CardinalitySingle',
            'firstValue' => array('eduPersonPrimaryAffiliation'),
        ),
    ),

Construct a single, comma-separated value version of `eduPersonAffiliation`:

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeCopy',
            'eduPersonAffiliation' => 'eduPersonAffiliationWithCommas',
        ),
        51 => array(
            'class' => 'core:CardinalitySingle',
            'flatten' => array('eduPersonAffiliationWithCommas'),
			'flattenWith' => ',',
        ),
    ),
