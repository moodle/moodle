`core:AttributeCopy`
===================

Filter that copies attributes.


Examples
--------

Copy a single attribute (user's `uid` will be copied to the user's `username`):

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeCopy',
            'uid' => 'username',
        ),
    ),

Copy a single attribute to more then one attribute (user's `uid` will be copied to the user's `username` and to `urn:mace:dir:attribute-def:uid`)

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeCopy',
            'uid' => array('username', 'urn:mace:dir:attribute-def:uid'),
        ),
    ),
