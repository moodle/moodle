`core:AttributeAlter`
==========

This filter can be used to substitute and replace different parts of the attribute values based on regular expressions.
It can also be used to create new attributes based on existing values, or even to remove blacklisted values from
attributes.

Parameters
----------

`class`
:   This is the name of the filter.
    It must be `'core:AttributeAlter'`.

`subject`
:   The attribute in which the search is performed.
    This parameter is REQUIRED and the filter will throw an exception if it is not set. The filter will
    stop quietly if the attribute specified here is empty or not found.
    
`pattern`
:   The pattern to look for inside the subject. Supports full Perl Compatible Regular Expressions (PCRE).
    This parameter is REQUIRED and the filter will throw an exception if it is not set.
    
`replacement`
:   The value used to replace the match. Back references are not supported.
    This parameter is REQUIRED, except when using the `%replace` or `%remove` options. If `%replace` is used and
    `replacement` is not set, then the match is used as a replacement.
    
`target`
:   The attribute where the replaced value will be placed.
    This parameter is OPTIONAL, and if not set, `subject` is used as `target`.

`%replace`
:   Indicates that the whole value of the attribute should be replaced if there is a match,
    instead of just the match. If there's no match, the value will not be changed. This parameter is OPTIONAL.

`%remove`
:   Indicates that the whole value of the attribute should be removed completely if there is a match.
    If no other values exist, the attribute will be removed completely.
    This parameter is OPTIONAL.
    
Examples
--------

Change the domain on the `mail` attribute (when both the new and old domain are known):

    10 => [
        'class' => 'core:AttributeAlter',
        'subject' => 'mail',
        'pattern' => '/olddomain.com/',
        'replacement' => 'newdomain.com',
    ],

Change the domain on the `mail` attribute (when new domain is known):

    10 => [
        'class' => 'core:AttributeAlter',
        'subject' => 'mail',
        'pattern' => '/(?:[A-Za-z0-9-]+\.)+[A-Za-z]{2,6}$/',
        'replacement' => 'newdomain.com',
    ],
    
Set the eduPersonPrimaryAffiliation based on users' distinguishedName:

    10 => [
        'class' => 'core:AttributeAlter',
        'subject' => 'dn',
        'pattern' => '/OU=Staff/',
        'replacement' => 'staff',
        'target' => 'eduPersonPrimaryAffiliation',
    ],
    
Normalize the eduPersonPrimaryAffiliation:

    10 => [
        'class' => 'core:AttributeAlter',
        'subject' => 'eduPersonPrimaryAffiliation',
        'pattern' => '/Student in school/',
        'replacement' => 'student',
        '%replace',
    ],
    
Get the domain of the emailaddress and put it in a separate attribute:

    10 => [
        'class' => 'core:AttributeAlter',
        'subject' => 'mail',
        'pattern' => '/(?:[A-Za-z0-9-]+\.)+[A-Za-z]{2,6}$/',
        'target' => 'domain',
        '%replace',
    ],

Defaulting an attribute to one value (add it with the default before altering)
unless another attribute meets a condition:

    10 => [
        'class' => 'core:AttributeAdd',
        'myAttribute' => 'default-value'
    ],
    11 => [
        'class' => 'core:AttributeAlter',
        'subject' => 'entitlement',
        'pattern' => '/faculty/',
        'target' => 'myAttribute',
        '%replace',
    ],
 
Remove internal, private values from eduPersonEntitlement:

    10 => [
        'class' => 'core:AttributeAlter',
        'subject' => 'eduPersonEntitlement',
        'pattern' => '/ldap-admin/',
        '%remove',
    ],

Set a value to be blank (which will be sent as an empty string):

    10 => [
        'class' => 'core:AttributeAlter',
        'subject' => 'cn',
        'pattern' => '/No name/',
        'replacement' => '',
        '%replace',
    ],

Set a value to be NULL (which will be sent as a NULL value):

    10 => [
        'class' => 'core:AttributeAlter',
        'subject' => 'telephone',
        'pattern' => '/NULL/',
        'replacement' => null,
        '%replace',
    ],
