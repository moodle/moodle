`core:AttributeValueMap`
===================

Filter that creates a target attribute based on one or more value(s) in source attribute.

Besides the mapping of source values to target values, the filter has the following options:
* `%replace` can be used to replace all existing values in target with new ones (any existing values will be lost)
* `%keep` can be used to keep the source attribute, otherwise it will be removed.

Examples
--------

### Add student affiliation based on LDAP groupmembership
Will add eduPersonAffiliation containing value "`student`" if the `memberOf` attribute contains
either '`cn=student,o=some,o=organization,dc=org`' or '`cn=student,o=other,o=organization,dc=org`'.
The '`memberOf`' attribute will be removed (use `%keep`, to keep it) and existing values in
'`eduPersonAffiliation`' will be merged (use `%replace` to replace them).

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeValueMap',
            'sourceattribute' => 'memberOf',
            'targetattribute' => 'eduPersonAffiliation',
            'values' => array(
                'student' => array(
                    'cn=student,o=some,o=organization,dc=org',
                    'cn=student,o=other,o=organization,dc=org',
                ),
            ),
        ),
    )

### Multiple assignments
Add `student`, `employee` and `both` affiliation based on LDAP groupmembership in the `memberOf` attribute.

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeValueMap',
            'sourceattribute' => 'memberOf',
            'targetattribute' => 'eduPersonAffiliation',
            'values' => array(
                'student' => array(
                    'cn=student,o=some,o=organization,dc=org',
                    'cn=student,o=other,o=organization,dc=org',
                ),
                'employee' => array(
                    'cn=employees,o=some,o=organization,dc=org',
                    'cn=employee,o=other,o=organization,dc=org',
                    'cn=workers,o=any,o=organization,dc=org',
                ),
                'both' => array(
                    'cn=student,o=some,o=organization,dc=org',
                    'cn=student,o=other,o=organization,dc=org',
                    'cn=employees,o=some,o=organization,dc=org',
                    'cn=employee,o=other,o=organization,dc=org',
                    'cn=workers,o=any,o=organization,dc=org',
                ),
            ),
        ),
    )

### Replace and Keep
Replace any existing '`affiliation`' attribute values and keep the '`groups`' attribute.
    
    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeValueMap',
            'sourceattribute' => 'groups',
            'targetattribute' => 'affiliation',
            '%replace',
            '%keep',
            'values' => array(
                'student' => array(
                    'cn=student,o=some,o=organization,dc=org',
                    'cn=student,o=other,o=organization,dc=org',
                ),
                'employee' => array(
                    'cn=employees,o=some,o=organization,dc=org',
                    'cn=employee,o=other,o=organization,dc=org',
                    'cn=workers,o=any,o=organization,dc=org',
                ),
            ),
        ),
    )
