`core:PHP`
==========

This is a filter which makes it possible to run arbitrary PHP code to modify the attributes or state of an user.

Parameters
----------

`class`
:   This is the name of the filter.
    It must be `'core:PHP'`.

`code`
:   The PHP code that should be run. This code will have two variables available: 

* `$attributes`.
    This is an associative array of attributes, and can be modified to add or remove attributes.
    
* `$state`.
    This is an associative array of request state. It can be modified to adjust data related to the authentication
    such as desired NameId, requested Attributes, authnContextRef and many more.

Examples
--------

Add the `mail` attribute based on the user's `uid` attribute:

    10 => array(
        'class' => 'core:PHP',
        'code' => '
            if (empty($attributes["uid"])) {
                throw new Exception("Missing uid attribute.");
            }

            $uid = $attributes["uid"][0];
            $mail = $uid . "@example.net";
            $attributes["mail"] = array($mail);
        ',
    ),


Create a random number variable:

    10 => array(
        'class' => 'core:PHP',
        'code' => '
            $attributes["random"] = array(
                (string)rand(),
            );
        ',
    ),

Force a specific NameIdFormat. Useful if an SP misbehaves and requests (or publishes) an incorrect NameId

    90 => array(
         'class' => 'core:PHP',
         'code' => '$state["saml:NameIDFormat"] = ["Format" => "urn:oasis:names:tc:SAML:2.0:nameid-format:transient", "AllowCreate" => true];'
    ),
