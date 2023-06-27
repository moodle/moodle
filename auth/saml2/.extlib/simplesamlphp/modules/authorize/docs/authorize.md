authorize Module
================

<!--
	This file is written in Markdown syntax.
	For more information about how to use the Markdown syntax, read here:
	http://daringfireball.net/projects/markdown/syntax
-->

  * Author: Ernesto Revilla <erny@yaco.es>, Yaco Sistemas, Ryan Panning
  * Package: SimpleSAMLphp

This module provides a user authorization filter based on attribute matching for those applications that do not cleanly separate authentication from authorization and set some default permissions for authenticated users.


`authorize:Authorize`
: Authorize certain users based on attribute matching


`authorize:Authorize`
---------------------

There are three configuration options that can be defined: `deny`, `regex`, and `reject_msg`. All other filter configuration options are considered attribute matching rules.

Unauthorized users will be shown a 403 Forbidden page.

### `deny` ###
The default action of the filter is to authorize only if an attribute match is found (default allow). When set to TRUE, this option reverses that rule and authorizes the user unless an attribute match is found (default deny), causing an unauthorized action.

Note: This option needs to be boolean (TRUE/FALSE) else it will be considered an attribute matching rule.

### `regex` ###
Turn regex pattern matching on or off for the attribute values defined. For backwards compatibility, this option defaults to TRUE, but can be turned off by setting it to FALSE.

Note: This option needs to be boolean (TRUE/FALSE) else it will be considered an attribute matching rule.

### `reject_msg` ###
This option can be used to provide a localised, custom message to an unauthorised user. For example: tailored instructions on how to fix the authorisation issue, specific contact details, etc.

It should be an array of key/value pairs, with the keys as the language code. You can use HTML in the message. See below for an example.


### Attribute Rules ###
Each additional filter configuration option is considered an attribute matching rule. For each attribute, you can specify a string or array of strings to match. If one of those attributes match one of the rules (OR operator), the user is authorized/unauthorized (depending on the deny config option).

Note: If regex is enabled, you must use the preg_match format, i.e. you have to enclose it with a delimiter that does not appear inside the regex (e.g. slash (/), at sign (@), number sign (#) or underscore (`_`)).

### Problems ###
 * Once you get the forbidden page, you can't logout at the IdP directly,
   (as far as I know), you have to close the browser.

### Examples ###
To use this filter configure it in `config/config.php`.
For unstructured attributes use `^` and `$` to anchor your regex as necessary:

```php
'authproc.sp' => [
    60 => [
        'class' => 'authorize:Authorize',
        'uid'   =>  [
            '/^.*@example.com$/',
            // Use anchors to prevent matching 'wronguser1@example.edu.attacker.com'
            '/^(user1|user2|user3)@example.edu$/',
        ],
        'schacUserStatus' => '@urn:mace:terena.org:userStatus:' .
        'example.org:service:active.*@',
    ]
]
```


An alternate way of using this filter is to deny certain users. Or even use multiple filters to create a simple ACL, by first allowing a group of users but then denying a "black list" of users.

```php
'authproc.sp' => [
    60 => array[
        'class' => 'authorize:Authorize',
        'deny'  => true,
        'uid'   =>  [
            '/.*@students.example.edu$/',
            '/^(stu1|stu2|stu3)@example.edu$/',
        ]
    ]
]
```

The regex pattern matching can be turned off, allowing for exact attribute matching rules. This can be helpful in cases where you know what the value should be. An example of this is with the memberOf attribute or using the ldap:AttributeAddUsersGroups filter with the group attribute.

Additionally, some helpful instructions are shown.

```php
'authproc.sp' => [
    60 => [
        'class' => 'authorize:Authorize',
        'regex' => false,
        'group' => [
            'CN=SimpleSAML Students,CN=Users,DC=example,DC=edu',
            'CN=All Teachers,OU=Staff,DC=example,DC=edu',
        ],
        'reject_msg' => [
            'en' => 'This service is only available to students and teachers.' .
                'Please contact <a href="mailto:support@example.edu">support</a>.',
            'nl' => 'Deze dienst is alleen beschikbaar voor studenten en docenten.' .
                'Neem contact op met <a href="mailto:support@example.edu">support</a>.',
        ]
    ]
]
```
