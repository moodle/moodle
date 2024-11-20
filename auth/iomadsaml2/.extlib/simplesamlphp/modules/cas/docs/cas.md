Using the CAS authentication source with SimpleSAMLphp
==========================================================

This is completely based on the original cas authentication, 
the only diffrence is this is authentication module and not a script.

Setting up the CAS authentication module
----------------------------------

Adding a authentication source

example authsource.php
----------------------------------

	'example-cas' => array(
		'cas:CAS',
		'cas' => array(
			'login' => 'https://cas.example.com/login',
			'validate' => 'https://cas.example.com/validate',
			'logout' => 'https://cas.example.com/logout'
		),
		'ldap' => array(
			'servers' => 'ldaps://ldaps.example.be:636/',
			'enable_tls' => true,
			'searchbase' => 'ou=people,dc=org,dc=com',
			'searchattributes' => 'uid',
			'attributes' => array('uid','cn'),
			'priv_user_dn' => 'cn=simplesamlphp,ou=applications,dc=org,dc=com',
			'priv_user_pw' => 'password',

		),
	),
