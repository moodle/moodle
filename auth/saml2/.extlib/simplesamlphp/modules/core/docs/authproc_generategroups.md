`core:GenerateGroups`
=====================

This filter creates a `group` attribute based on the contents of the other attributes of the user.

By default this filter will generate groups from the following set of attributes:

* `eduPersonAffiliation`
* `eduPersonOrgUnitDN`
* `eduPersonEntitlement`

This can be overridden by specifying the names of the attributes in the configuration.

It will attempt to determine a realm the user belongs to based on the `eduPersonPrincipalName`
attribute, if it is present.

The groups this filter generates are on the form `<attribute name>-<attributevalue>` and `<attributename>-<realm>-<attributevalue>`.
For example, if the user has the following attributes:

* `eduPersonPrincipalName`: `user@example.org`
* `eduPersonAffiliation`: `student`, `member`

The following groups will be created:

* `eduPersonAffiliation-student`
* `eduPersonAffiliation-member`
* `eduPersonAffiliation-example.org-student`
* `eduPersonAffiliation-example.org-member`


Examples
--------

Default attributes:

    'authproc' => array(
        50 => array(
            'class' => 'core:GenerateGroups',
        ),
    ),

Custom attributes:

    'authproc' => array(
        50 => array(
            'class' => 'core:GenerateGroups',
            'someAttribute',
            'someOtherAttribute',
        ),
    ),

