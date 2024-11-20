`core:StatisticsWithAttribute`
==============================

This filter logs a statistics entry that can be parsed by the statistics module.

Parameters
----------

`attributename`
:   The name of an attribute that should be included in the statistics entry.

`type`
:   The type of the statistics entry.

`skipPassive`
:   A boolean indicating whether passive requests should be skipped. Defaults to `FALSE`, in which case the type tag is prefixed with 'passive-'.


Example
-------

Log the realm of the user:

    45 => array(
        'class' => 'core:StatisticsWithAttribute',
        'attributename' => 'realm',
        'type' => 'saml20-idp-SSO',
    ),

