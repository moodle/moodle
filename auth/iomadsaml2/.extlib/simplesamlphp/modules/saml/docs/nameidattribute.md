`saml:NameIDAttribute`
======================

Filter that extracts the NameID we received in the authentication response and adds it as an attribute.

Parameters
----------

`attribute`
:   The name of the attribute we should create.
    The default is `nameid`.

`format`
:   The format string for the attribute.
    The default is `%I!%S!%V`.

:   The format string accepts the following replacements:

    * `%I`: The IdP that issued the NameID.
            This will be the `NameQualifier` element of the NameID if it is present, or the entity ID of the IdP we received the response from if not.
    * `%S`: The SP the NameID was issued to.
            This will be the `SPNameQualifier` element of the NameID if it is present, or the entity ID of this SP otherwise.
    * `%V`: The value of the NameID.
    * `%F`: The format of the NameID.
    * `%%`: Will be replaced with a single `%`.

Examples
--------

Minimal configuration:

    'default-sp' => array(
        'saml:SP',
        'authproc' => array(
            20 => 'saml:NameIDAttribute',
        ),
    ),

Custom attribute name:

    'default-sp' => array(
        'saml:SP',
        'authproc' => array(
            20 => array(
                'class' => 'saml:NameIDAttribute',
                'attribute' => 'someattributename',
            ),
        ),
    ),

Only extract the value of the NameID.

    'default-sp' => array(
        'saml:SP',
        'authproc' => array(
            20 => array(
                'class' => 'saml:NameIDAttribute',
                'format' => '%V',
            ),
        ),
    ),

See also
--------

 * [The description of the `saml:SP` authentication source.](./saml:sp)
 * [How to generate various NameIDs on the IdP.](./saml:nameid)
