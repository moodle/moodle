`core:AttributeMap`
===================

Filter to change attribute names.

This filter can either contain the name of a map file or a set of name => value pairs describing the transformation.
If configuration references a map file, the file must be located in the `attributemap/` directory in the root of
SimpleSAMLphp's installation. Attribute map files located in the `attributemap/` directory in the root of a module can
also be used by specifying the file with the `module:file` syntax.

It can also create multiple attributes from a single attribute by specifying multiple target attributes as an array.

Examples
--------

Attribute maps embedded as parameters:

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeMap',
            'mail' => 'email',
            'uid' => 'user'
            'cn' => array('name', 'displayName'),
        ),
    ),

Attribute map in separate file:

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeMap',
            'name2oid',
        ),
    ),

This filter will use the map file from `simplesamlphp/attributemap/name2oid.php`.

Attribute map in a file contained in a module:

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeMap',
            'module:src2dst'
        ),
    ),

This filter will use the map file from `simplesamlphp/modules/module/attributemap/src2dst.php`.

Duplicate attributes based on a map file:

    'authproc' => array(
        50 => array(
            'class' => 'core:AttributeMap',
            'name2urn', 'name2oid',
            '%duplicate',
        ),
    ),
