LDAP module
===========

The LDAP module provides a method for authenticating users against an
LDAP server. There are two separate authentication modules and two
authentication processing filters:


`ldap:LDAP`
: Authenticate the user against a single LDAP server.

`ldap:LDAPMulti`
: Allow the user to chose one LDAP server to authenticate against.

`ldap:AttributeAddFromLDAP`
: Adds an attribute value from LDAP to the request

`ldap:AttributeAddUsersGroups`
: Add an attribute to the request with all the user's group memberships

`ldap:LDAP`
-----------

This module is used when you have an organization with a single LDAP
server with all the users. To create an LDAP authentication source, open
`config/authsources.php` in a text editor, and add an entry for the
authentication source:

	'example-ldap' => array(
		'ldap:LDAP',

		/* The hostname of the LDAP server. */
		'hostname' => 'ldap.example.org',

		/* Whether SSL/TLS should be used when contacting the LDAP server. */
		'enable_tls' => FALSE,

		/*
		 * Which attributes should be retrieved from the LDAP server.
		 * This can be an array of attribute names, or NULL, in which case
		 * all attributes are fetched.
		 */
		'attributes' => NULL,

		/*
		 * Which attributes should be base64 encoded after retrieval from the LDAP server.
		 */
		'attributes.binary' => ['jpegPhoto', 'objectGUID', 'objectSid', 'mS-DS-ConsistencyGuid'],

		/*
		 * The pattern which should be used to create the user's DN given the username.
		 * %username% in this pattern will be replaced with the user's username.
		 *
		 * This option is not used if the search.enable option is set to TRUE.
		 */
		'dnpattern' => 'uid=%username%,ou=people,dc=example,dc=org',

		/*
		 * As an alternative to specifying a pattern for the users DN, it is possible to
		 * search for the username in a set of attributes. This is enabled by this option.
		 */
		'search.enable' => FALSE,

		/*
		 * The DN which will be used as a base for the search.
		 * This can be a single string, in which case only that DN is searched, or an
		 * array of strings, in which case they will be searched in the order given.
		 */
		'search.base' => 'ou=people,dc=example,dc=org',

                /*
                 * The scope of the search. Valid values are 'subtree' and 'onelevel' and 'base',
                 * first one being the default if no value is set.
                 */
                'search.scope' => 'subtree',

		/*
		 * The attribute(s) the username should match against.
		 *
		 * This is an array with one or more attribute names. Any of the attributes in
		 * the array may match the value the username.
		 */
		'search.attributes' => array('uid', 'mail'),

		/*
		 * Additional filters that must match for the entire LDAP search to be TRUE
		 *
		 * This should be a single string conforming to (RFC 1960, 2544)
		 * The string is appended to the search attributes
		 */
		'search.filter' => '(&(objectClass=Person)(|(sn=Doe)(cn=John *)))',

		/*
		 * The username & password where SimpleSAMLphp should bind to before searching. If
		 * this is left NULL, no bind will be performed before searching.
		 */
		'search.username' => NULL,
		'search.password' => NULL,
	),


You should update the name of this authentication source
(`example-ldap`) to have a name which makes sense to your organization.
You also need to update the `hostname` and `dnpattern` options. The
`hostname` should be the hostname of your LDAP server, and the
`dnpattern` should be a pattern which can be used to generate the `dn`
of a user with a given username.

All other options have default values, and are not required.

### Searching for a user ###

Sometimes you cannot generate the user's `dn` from the username, or you
may want to allow the user to authenticate with for example their email
address as the username. In this case, you can configure the LDAP
module to search for the users `dn` by searching for the username in
one or more attributes.

To enable searching, you must set the `search.enable` option to `TRUE`.
You must then configure the `search.base` and the `search.attributes`
options. The `search.base`-option must be the `dn` which should be used
as the base/root of the search. The `search.attributes`-option is an
array with attributes the username should be matched against.

You can also append the `search.filter` option to further limit your search.
The `search.filter` field is optional and need not be included in your
configuration file.

The `dnpattern` option will not be used if searching is enabled.

Some LDAP servers may require authentication before a search can be
performed. In this case, you should configure the `search.username`
and `search.password` options. The `search.username` option is a `dn`
which can be used to perform a search, and the `search.password` option
is the password for that `dn`.

### Configuring failover ###

You can configure multiple LDAP servers in the hostname option by separating the individual hosts with a space.
This enables the builtin LDAP failover in OpenLDAP.

Note that OpenLDAP waits for a timeout from the first server before attempting to connect to the other.
To avoid a very long wait, it is recommended to change the timeouts.
This can be done in the system-wide ldap configuration file.

    NETWORK_TIMEOUT 10
    TIMELIMIT       15
    TIMEOUT         20

In this case, if we are unable to connect to the first LDAP server within 10 seconds, we will attempt the next.
(Note: the NETWORK_TIMEOUT option was introduced with OpenLDAP version 2.4.)

#### Example ####

    /* Configuration that uses two ldap servers. */
    'example-ldap' => array(
        'ldap:LDAP',
        /* The hostname of the LDAP server. */
        'hostname' => 'ldaps://ldap1.example.org ldaps://ldap2.example.org',
        'dnpattern' => 'uid=%username%,ou=people,dc=example,dc=org',
    ),


`ldap:LDAPMulti`
----------------

This module can be used if your organization has separate groups with
separate LDAP servers or separate LDAP configurations. To use this
authentication module, open `config/authsources.php` in a text editor,
and add an entry which uses this module:

	'example-ldapmulti' => array(
		'ldap:LDAPMulti',

		/*
		 * The way the organization as part of the username should be handled.
		 * Three possible values:
		 * - 'none':   No handling of the organization. Allows '@' to be part
		 *             of the username.
		 * - 'allow':  Will allow users to type 'username@organization'.
		 * - 'force':  Force users to type 'username@organization'. The dropdown
		 *             list will be hidden.
		 *
		 * The default is 'none'.
		 */
		'username_organization_method' => 'none',

		/*
		 * Whether the organization should be included as part of the username
		 * when authenticating. If this is set to TRUE, the username will be on
		 * the form <username>@<organization identifier>. If this is FALSE, the
		 * username will be used as the user enters it.
		 *
		 * The default is FALSE.
		 */
		'include_organization_in_username' => FALSE,

		/*
		 * A list of available LDAP servers.
		 *
		 * The index is an identifier for the organization/group. When
		 * 'username_organization_method' is set to something other than 'none',
		 * the organization-part of the username is matched against the index.
		 *
		 * The value of each element is an array in the same format as an LDAP
		 * authentication source.
		 */
		'employees' => array(
			/*
			 * A short name/description for this group. Will be shown in a dropdown list
			 * when the user logs on.
			 *
			 * This option can be a string or an array with language => text mappings.
			 */
			'description' => 'Employees',

			/*
			 * The rest of the options are the same as those available for
			 * the LDAP authentication source.
			 */
			'hostname' => 'ldap.employees.example.org',
			'dnpattern' => 'uid=%username%,ou=employees,dc=example,dc=org',
		),

		'students' => array(
			'description' => 'Students',

			'hostname' => 'ldap.students.example.org',
			'dnpattern' => 'uid=%username%,ou=students,dc=example,dc=org',
		),

	),

The name of the authentication source (`example-ldapmulti`) should be
changed to something that makes sense for your organization. Each entry
in the configuration represents the configuration for one group of
users. The `description`-option in each group is the name of the group,
and will be shown to the user in a dropdown list on the login page.

The `description`-option can also be an array with descriptions in
different languages:

	'description' => array(
		'en' => 'Employees',
		'no' => 'Ansatte',
	),

All options from the `ldap:LDAP` configuration can be used in each
group, and you should refer to the documentation for that module for
more information about available options.


`ldap:AttributeAddFromLDAP`
---------------------------

Filter to add attributes to the identity by executing a query against
an LDAP directory. In addition to all the configuration options available
in the ldap:AttributeAddUsersGroups filter (below), these are the filter
specific configuration options:


	50 = array(
		'class' => 'ldap:AttributeAddFromLDAP',

		/**
		 * The attributes to search for and their mappings. This must be an array,
		 * and keys can be skipped. If you skip a key, then the attribute will be
		 * exported with the same name as the LDAP attribute.
		 *
		 * Default: NULL
		 * Required: Yes
		 */
		'attributes' => array('mail', 'jpegPhoto' => 'jpegphoto'),

		/**
		 * The attribute policy that defines what to do with attributes that are
		 * already part of the attributes of the user. Can be one of:
		 *
		 * - add: blindly add the values. If the attribute already exists and has
		 * the same value, the result of the filter will be two equal values.
		 *
		 * - merge: carefully merge the values. If a value is already part of
		 * the attribute, do not add a duplicate.
		 *
		 * - replace: if the attribute is present before running the filter,
		 * replace its values with the ones obtained at this point.
		 *
		 * Default: merge
		 * Required: No
		 */
		'attribute.policy' => 'merge',

		/**
		 * The search filter to find the user in LDAP.
		 *
		 * Note: Variable substitution will be performed on this option.
		 *       Any attribute in the identity can be substituted by surrounding
		 *       it with percent symbols (%). For instance %cn% would be replaced
		 *       with the CN of the user.
		 *
		 * Default: NULL
		 * Required: Yes
		 */
		'search.filter' => '(uid=%uid%)',
	);


### Backwards Compatibility ###

Previous versions of this filter allowed just one attribute to be fetched from the
LDAP at a time. The options 'attribute.new' and 'search.attribute' were used instead
of the new option 'attributes'. Fortunately, the filter is backwards compatible, so
your old configuration will still work, but keep in mind that the old configuration
style is deprecated now and will be removed in 2.0.


### Example ###

This is the most basic configuration possible. It will look at the
authsource for all LDAP connection information and queries LDAP for
the specific attributes requested.

	50 => array(
		'class' => 'ldap:AttributeAddFromLDAP',
		'authsource' => 'example-ldap',
		'attributes' => array('displayName' => 'cn', 'jpegPhoto'),
		'search.filter' => '(uid=%uid%)',
	)

If no authsource is available then you can specify the connection info
using the filter configuration. Note: Not all of the options below are
required, see the config options for ldap:AttributeAddFromLDAP above.

	50 => array(
		'class' => 'ldap:AttributeAddFromLDAP',
		'ldap.hostname' => 'ldap.example.org',
		'ldap.username' => 'CN=LDAP User,CN=Users,DC=example,DC=org',
		'ldap.password' => 'Abc123',
		'ldap.basedn' => 'DC=example,DC=org',
		'attributes' => array('displayName' => 'cn', 'jpegPhoto'),
		'search.filter' => '(uid=%uid%)',
	)




`ldap:AttributeAddUsersGroups`
------------------------------

This filter will add the logged in user's LDAP group memberships to
a specified request attribute. Although most LDAP products have a
memberOf attribute which only lists the direct membership relations,
this filter checks those relation for "sub" groups, recursively
checking the hierarchy for all groups the user would technically be
a member of. This can be helpful for other filters to know. Below is
a listing of all configuration options and their details.


    50 => [
        'class' => 'ldap:AttributeAddUsersGroups',


        /**
         * LDAP connection settings can be retrieved from an ldap:LDAP
         * authsource. Specify the authsource name here to pull that
         * data from the authsources.php file in the config folder.
         *
         * Note: ldap:LDAPMulti is not supported as the SimpleSAMLphp
         *       framework does not pass any information about which
         *       LDAP source the user selected.
         *
         * Default: NULL
         * Require: No
         */
        'authsource' => null,
        'authsource' => 'example-ldap',


        /**
         * This is the attribute name which the users groups will be
         * added to. If the attribute exists in the request then the
         * filter will attempt to add them.
         *
         * Default: 'groups'
         * Required: No
         */
        'attribute.groups' => 'groups',


        /**
         * The base DN used to search LDAP. May not be needed if searching
         * LDAP using the standard method, meaning that no Product is specified.
         * Can be listed as a single string for one base, else an array of
         * strings for multiple bases.
         *
         * Default: ''
         * Required: No
         * AuthSource: search.base
         */
        'ldap.basedn' => '',
        'ldap.basedn' => 'DC=example,DC=org',
        'ldap.basedn' => [
            'OU=Staff,DC=example,DC=org',
            'OU=Students,DC=example,DC=org'
        ],


        /**
         * Set to TRUE to enable LDAP debug level. Passed to
         * the LDAP connection class.
         *
         * Default: FALSE
         * Required: No
         * AuthSource: debug
         */
        'ldap.debug' => false,
        'ldap.debug' => true,


        /**
         * Set to TRUE to force the LDAP connection to use TLS.
         *
         * Note: If ldaps:// is specified in the hostname then it
         *       will automatically use TLS.
         *
         * Default: FALSE
         * Required: No
         * AuthSource: enable_tls
         */
        'ldap.enable_tls' => false,
        'ldap.enable_tls' => true,


        /**
         * This is the hostname string of LDAP server(s) to try
         * and connect to. It should be the same format as the
         * LDAP authsource hostname as it is passed to that class.
         *
         * Note: Multiple servers are separated by a space.
         *
         * Default: NULL
         * Required: Yes, unless authsource is used
         * AuthSource: hostname
         */
        'ldap.hostname' => 'ldap.example.org',
        'ldap.hostname' => 'ad1.example.org ad2.example.org',


        /**
         * This is the port where the LDAP server(s) listen for
         * connections.
         *
         * Default: 389
         * Required: No
         * AuthSource: port
         */
        'ldap.port' => 389,


        /**
         * This is the password used to bind to LDAP.
         *
         * Default: NULL
         * Required: No, only if required for binding.
         * AuthSource: search.password OR priv.password
         */
        'ldap.password' => 'Abc123',


        /**
         * By specifying the directory service product name, the number
         * of LDAP queries can be dramatically reduced. The reason is
         * that most products have a special query to recursively search
         * group membership.
         *
         * Note: Only ActiveDirectory is currently supported 
         * (OpenLDAP is implemented but not supported, see example below).
         *
         * Default: ''
         * Required: No
         */
        'ldap.product' => '',
        'ldap.product' => 'ActiveDirectory',
        'ldap.product' => 'OpenLDAP',


        /**
         * The LDAP timeout value passed to the LDAP connection class.
         *
         * Default: 0
         * Required: No
         * AuthSource: timeout
         */
        'ldap.timeout' => 0,
        'ldap.timeout' => 30,


        /**
         * This is the username used to bind to LDAP with.
         * More than likely will need to be in the DN of
         * user binding to LDAP.
         *
         * Default: NULL
         * Required: No, only if required for binding.
         * AuthSource: search.username OR priv.username
         */
        'ldap.username' => 'CN=LDAP User,CN=Users,DC=example,DC=org',


        /**
         * The following attribute.* and type.* configuration options
         * define the LDAP schema and should only be defined/modified
         * if the schema has been modified or the LDAP product used
         * uses other attribute names. By default, the schema is setup
         * for ActiveDirectory.
         *
         * Defaults: Listed Below
         * Required: No
         */
        'attribute.dn' => 'distinguishedName',
        'attribute.groups' => 'groups', // Also noted above
        'attribute.member' => 'member',
        'attribute.memberof' => 'memberOf',
        'attribute.groupname' => 'name',
        'attribute.return' => 'distinguishedName',
        'attribute.type' => 'objectClass',
        'attribute.username' => 'sAMAccountName',


        /**
         * As mentioned above, these can be changed if the LDAP schema
         * has been modified. These list the Object/Entry Type for a given
         * DN, in relation to the 'attribute.type' config option above.
         * These are used to determine the type of entry.
         *
         * Defaults: Listed Below
         * Required: No
         */
        'type.group' => 'group',
        'type.user' => 'user',


        /**
         * LDAP search filters to be added to the base filters for this authproc-filter.
         * It's an array of key => value pairs that will be translated to (key=value) in the ldap query.
         *
         */
        'additional_filters' => [],


        /**
         * A flag whether to escape the additional filter values or not. Defaults to TRUE
         */
        'escape' => true
    ]


### Example ###

This is the most basic configuration possible. It will look at the
authsource for all LDAP connection information and manually search
the hierarchy for the users group memberships.

	50 => array(
		'class' => 'ldap:AttributeAddUsersGroups',
		'authsource' => 'example-ldap'
	)

By making one small change we can optimize the filter to use better
group search methods and eliminate un-needed LDAP queries.

	50 => array(
		'class' => 'ldap:AttributeAddUsersGroups',
		'authsource' => 'example-ldap',
		'ldap.product' => 'ActiveDirectory'
	)

If no authsource is available then you can specify the connection info
using the filter configuration. Note: Not all of the options below are
required, see the config info above for details.

	50 => array(
		'class' => 'ldap:AttributeAddUsersGroups',
		'ldap.hostname' => 'ldap.example.org',
		'ldap.username' => 'CN=LDAP User,CN=Users,DC=example,DC=org',
		'ldap.password' => 'Abc123',
		'ldap.basedn' => 'DC=example,DC=org'
	)

Example for unsupported OpenLDAP usage. 
Intention is to filter in `ou=groups,dc=example,dc=com` for
`(memberUid = <UID>)` and take only the attribute `cn` (=name of the group).

    50 => array(
        'class' => 'ldap:AttributeAddUsersGroups',
        'ldap.product' => 'OpenLDAP',
        'ldap.basedn' => 'ou=groups,dc=example,dc=org',
        'attribute.username' => 'uid',
        'attribute.member' => 'cn',
        'attribute.memberof' => 'memberUid',
    ),
