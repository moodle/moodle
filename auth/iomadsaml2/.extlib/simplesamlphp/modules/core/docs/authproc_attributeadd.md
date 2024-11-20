`core:AttributeAdd`
===================

Filter that adds attributes to the user.

If the attribute already exists, the values added will be merged into a multi-valued attribute.
If you instead want to replace the existing attribute, you may add the `'%replace'` option.


Examples
--------

Add a single-valued attributes:

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeAdd',
            'source' => array('myidp'),
        ),
    ),

Add a multi-valued attribute:

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeAdd',
            'groups' => array('users', 'members'),
        ),
    ),

Add multiple attributes:

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeAdd',
	    'eduPersonPrimaryAffiliation' => 'student',
            'eduPersonAffiliation' => array('student', 'employee', 'members'),
        ),
    ),

Replace an existing attributes:

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeAdd',
            '%replace',
            'uid' => array('guest'),
        ),
    ),
