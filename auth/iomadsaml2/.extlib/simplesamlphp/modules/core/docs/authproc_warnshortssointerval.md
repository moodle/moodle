`core:WarnShortSSOInterval`
===========================

Give a warning to the user when authenticating twice in a short time.
This is mainly intended to prevent redirect loops between the IdP and the SP.


Example
-------

    'authproc' => array(
        50 => array(
            'class' => 'core:WarnShortSSOInterval',
        ),
    ),

