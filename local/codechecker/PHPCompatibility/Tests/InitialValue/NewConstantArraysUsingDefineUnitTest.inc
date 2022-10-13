<?php

define('ANIMALS', [
    'dog',
    'cat',
    'bird'
]);

define('ANIMALS', array(
    'dog',
    'cat',
    'bird'
));

define('ANIMALS', 'dog');

// Test correct function detection.
myClass::define('ANIMALS', 'dog');
$object->define('ANIMALS', 'dog');

class myClass {
	const define = true;
	function define() {}
}

notDefine('ANIMALS', 'dog');

define('ANIMALS');

// Array within a function call.
define('WPDIRAUTH_LDAP_RETURN_KEYS',serialize(array('sn', 'givenname', 'mail')));
define('WPDIRAUTH_LDAP_RETURN_KEYS',serialize(['sn', 'givenname', 'mail']));
