<?PHP  //$Id$

// This file keeps track of upgrades to Moodle.
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the functions defined in lib/ddllib.php


function xmldb_main_upgrade($oldversion=0) {

    global $CFG, $THEME, $USER, $SITE, $db;

    $result = true;

    if ($result && $oldversion < 2006100401) {
        /// Only for those tracking Moodle 1.7 dev, others will have these dropped in moodle_install_roles()
        if (!empty($CFG->rolesactive)) {
            drop_table(new XMLDBTable('user_students'));
            drop_table(new XMLDBTable('user_teachers'));
            drop_table(new XMLDBTable('user_coursecreators'));
            drop_table(new XMLDBTable('user_admins'));
        }

        upgrade_main_savepoint($result, 2006100401);
    }

    if ($result && $oldversion < 2006100601) {         /// Disable the exercise module because it's unmaintained
        if ($module = get_record('modules', 'name', 'exercise')) {
            if ($module->visible) {
                // Hide/disable the module entry
                set_field('modules', 'visible', '0', 'id', $module->id);
                // Save existing visible state for all activities
                set_field('course_modules', 'visibleold', '1', 'visible' ,'1', 'module', $module->id);
                set_field('course_modules', 'visibleold', '0', 'visible' ,'0', 'module', $module->id);
                // Hide all activities
                set_field('course_modules', 'visible', '0', 'module', $module->id);

                //require_once($CFG->dirroot.'/course/lib.php');
                //rebuild_course_cache();  // Rebuld cache for all modules because they might have changed
            }
        }

        upgrade_main_savepoint($result, 2006100601);
    }

    if ($result && $oldversion < 2006101001) {         /// Disable the LAMS module by default (if it is installed)
        if (count_records('modules', 'name', 'lams') && !count_records('lams')) {
            set_field('modules', 'visible', 0, 'name', 'lams');  // Disable it by default
        }

        upgrade_main_savepoint($result, 2006101001);
    }

    if ($result && $oldversion < 2006102600) {

        /// Define fields to be added to user_info_field
        $table  = new XMLDBTable('user_info_field');
        $field = new XMLDBField('description');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'categoryid');
        $field1 = new XMLDBField('param1');
        $field1->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'defaultdata');
        $field2 = new XMLDBField('param2');
        $field2->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'param1');
        $field3 = new XMLDBField('param3');
        $field3->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'param2');
        $field4 = new XMLDBField('param4');
        $field4->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'param3');
        $field5 = new XMLDBField('param5');
        $field5->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'param4');

        /// Launch add fields
        $result = $result && add_field($table, $field);
        $result = $result && add_field($table, $field1);
        $result = $result && add_field($table, $field2);
        $result = $result && add_field($table, $field3);
        $result = $result && add_field($table, $field4);
        $result = $result && add_field($table, $field5);

        upgrade_main_savepoint($result, 2006102600);
    }

    if ($result && $oldversion < 2006112000) {

    /// Define field attachment to be added to post
        $table = new XMLDBTable('post');
        $field = new XMLDBField('attachment');
        $field->setAttributes(XMLDB_TYPE_CHAR, '100', null, null, null, null, null, null, 'format');

    /// Launch add field attachment
        $result = $result && add_field($table, $field);

        upgrade_main_savepoint($result, 2006112000);
    }

    if ($result && $oldversion < 2006112200) {

    /// Define field imagealt to be added to user
        $table = new XMLDBTable('user');
        $field = new XMLDBField('imagealt');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null, 'trustbitmask');

    /// Launch add field imagealt
        $result = $result && add_field($table, $field);

        $table = new XMLDBTable('user');
        $field = new XMLDBField('screenreader');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, null, '0', 'imagealt');

    /// Launch add field screenreader
        $result = $result && add_field($table, $field);

        upgrade_main_savepoint($result, 2006112200);
    }

    if ($result && $oldversion < 2006120300) {    /// Delete guest course section settings
        // following code can be executed repeatedly, such as when upgrading from 1.7.x - it is ok
        if ($guest = get_record('user', 'username', 'guest')) {
            execute_sql("DELETE FROM {$CFG->prefix}course_display where userid=$guest->id", true);
        }

        upgrade_main_savepoint($result, 2006120300);
    }

    if ($result && $oldversion < 2006120400) {    /// Remove secureforms config setting
        execute_sql("DELETE FROM {$CFG->prefix}config where name='secureforms'", true);

        upgrade_main_savepoint($result, 2006120400);
    }

    if (!empty($CFG->rolesactive) && $oldversion < 2006120700) { // add moodle/user:viewdetails to all roles!
        // note: use of assign_capability() is discouraged in upgrade script!
        if ($roles = get_records('role')) {
            $context = get_context_instance(CONTEXT_SYSTEM);
            foreach ($roles as $roleid=>$role) {
                assign_capability('moodle/user:viewdetails', CAP_ALLOW, $roleid, $context->id);
            }
        }

        upgrade_main_savepoint($result, 2006120700);
    }

    // Move the auth plugin settings into the config_plugin table
    if ($result && $oldversion < 2007010300) {
        if ($CFG->auth == 'email') {
            set_config('registerauth', 'email');
        } else {
            set_config('registerauth', '');
        }
        $authplugins = get_list_of_plugins('auth');
        foreach ($CFG as $k => $v) {
            if (strpos($k, 'ldap_') === 0) {
                //upgrade nonstandard ldap settings
                $setting = substr($k, 5);
                if (set_config($setting, $v, "auth/ldap")) {
                    delete_records('config', 'name', $k);
                    unset($CFG->{$k});
                }
                continue;
            }
            if (strpos($k, 'auth_') !== 0) {
                continue;
            }
            $authsetting = substr($k, 5);
            foreach ($authplugins as $auth) {
                if (strpos($authsetting, $auth) !== 0) {
                    continue;
                }
                $setting = substr($authsetting, strlen($auth));
                if (set_config($setting, $v, "auth/$auth")) {
                    delete_records('config', 'name', $k);
                    unset($CFG->{$k});
                }
                break; // don't check the rest of the auth plugin names
            }
        }

        upgrade_main_savepoint($result, 2007010300);
    }

    if ($result && $oldversion < 2007010301) {
        //
        // Core MNET tables
        //
        $table = new XMLDBTable('mnet_host');
        $table->comment = 'Information about the local and remote hosts for RPC';
        // fields
        $f = $table->addFieldInfo('id',                 XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $f->comment = 'Unique Host ID';
        $f = $table->addFieldInfo('deleted',            XMLDB_TYPE_INTEGER,  '1', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
        $f = $table->addFieldInfo('wwwroot',            XMLDB_TYPE_CHAR,   '255', null,
                                  XMLDB_NOTNULL, null, null, null, null);
        $f = $table->addFieldInfo('ip_address',         XMLDB_TYPE_CHAR,    '39', null,
                                  XMLDB_NOTNULL, null, null, null, null);
        $f = $table->addFieldInfo('name',               XMLDB_TYPE_CHAR,    '80', null,
                                  XMLDB_NOTNULL, null, null, null, null);
        $f = $table->addFieldInfo('public_key',         XMLDB_TYPE_TEXT, 'medium', null,
                                  XMLDB_NOTNULL, null, null, null, null);
        $f = $table->addFieldInfo('public_key_expires', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
        $f = $table->addFieldInfo('transport',          XMLDB_TYPE_INTEGER,  '2', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
        $f = $table->addFieldInfo('portno',             XMLDB_TYPE_INTEGER,  '2', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
        $f = $table->addFieldInfo('last_connect_time',  XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
        $f = $table->addFieldInfo('last_log_id',  XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, null, null, null, 0);
        // PK and indexes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        // Create the table
        $result = $result && create_table($table);

        $table = new XMLDBTable('mnet_host2service');
        $table->comment = 'Information about the services for a given host';
        // fields
        $f = $table->addFieldInfo('id',        XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $f = $table->addFieldInfo('hostid',    XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('serviceid', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('publish', XMLDB_TYPE_INTEGER,  '1', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('subscribe', XMLDB_TYPE_INTEGER,  '1', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        // PK and indexes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('hostid_serviceid', XMLDB_INDEX_UNIQUE, array('hostid', 'serviceid'));
        // Create the table
        $result = $result && create_table($table);

        $table = new XMLDBTable('mnet_log');
        $table->comment = 'Store session data from users migrating to other sites';
        // fields
        $f = $table->addFieldInfo('id',        XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $f = $table->addFieldInfo('hostid',    XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('remoteid',    XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('time',    XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('userid',    XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('ip',    XMLDB_TYPE_CHAR,  '15', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('course',    XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('coursename',    XMLDB_TYPE_CHAR,  '40', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('module',    XMLDB_TYPE_CHAR,  '20', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('cmid',    XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('action',    XMLDB_TYPE_CHAR,  '40', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('url',    XMLDB_TYPE_CHAR,  '100', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('info',    XMLDB_TYPE_CHAR,  '255', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        // PK and indexes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('host_user_course', XMLDB_INDEX_NOTUNIQUE, array('hostid', 'userid', 'course'));
        // Create the table
        $result = $result && create_table($table);


        $table = new XMLDBTable('mnet_rpc');
        $table->comment = 'Functions or methods that we may publish or subscribe to';
        // fields
        $f = $table->addFieldInfo('id',        XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $f = $table->addFieldInfo('function_name',    XMLDB_TYPE_CHAR,  '40', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('xmlrpc_path',    XMLDB_TYPE_CHAR,  '80', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('parent_type',    XMLDB_TYPE_CHAR,  '6', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('parent',    XMLDB_TYPE_CHAR,  '20', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('enabled', XMLDB_TYPE_INTEGER,  '1', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('help',    XMLDB_TYPE_TEXT,  'medium', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('profile',    XMLDB_TYPE_TEXT,  'medium', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        // PK and indexes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('enabled_xpath', XMLDB_INDEX_NOTUNIQUE, array('enabled', 'xmlrpc_path'));
        // Create the table
        $result = $result && create_table($table);

        $table = new XMLDBTable('mnet_service');
        $table->comment = 'A service is a group of functions';
        // fields
        $f = $table->addFieldInfo('id',        XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $f = $table->addFieldInfo('name',    XMLDB_TYPE_CHAR,  '40', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('description',    XMLDB_TYPE_CHAR,  '40', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('apiversion',    XMLDB_TYPE_CHAR,  '10', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('offer',    XMLDB_TYPE_INTEGER,  '1', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        // PK and indexes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        // Create the table
        $result = $result && create_table($table);

        $table = new XMLDBTable('mnet_service2rpc');
        $table->comment = 'Group functions or methods under a service';
        // fields
        $f = $table->addFieldInfo('id',        XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $f = $table->addFieldInfo('serviceid', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('rpcid',    XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        // PK and indexes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('unique', XMLDB_INDEX_UNIQUE, array('rpcid', 'serviceid'));
        // Create the table
        $result = $result && create_table($table);

        //
        // Prime MNET configuration entries -- will be needed later by auth/mnet
        //
        include_once $CFG->dirroot . '/mnet/lib.php';
        $env = new mnet_environment();
        $env->init();
        unset($env);

        // add mnethostid to user-
        $table = new XMLDBTable('user');
        $field = new XMLDBField('mnethostid');
        $field->setType(XMLDB_TYPE_INTEGER);
        $field->setLength(10);
        $field->setNotNull(true);
        $field->setSequence(null);
        $field->setEnum(null);
        $field->setDefault('0');
        $field->setPrevious("deleted");
        $field->setNext("username");
        $result = $result && add_field($table, $field);

        // The default mnethostid is zero... we need to update this for all
        // users of the local IdP service.
        set_field('user',
                  'mnethostid', $CFG->mnet_localhost_id,
                  'mnethostid', '0');


        $index = new XMLDBIndex('username');
        $index->setUnique(true);
        $index->setFields(array('username'));
        drop_index($table, $index);
        $index->setFields(array('mnethostid', 'username'));
        if (!add_index($table, $index)) {
            notify(get_string('duplicate_usernames', 'mnet', 'http://docs.moodle.org/en/DuplicateUsernames'));
        }

        unset($table, $field, $index);

        /**
         ** auth/mnet tables
         **/
        $table = new XMLDBTable('mnet_session');
        $table->comment='Store session data from users migrating to other sites';
        // fields
        $f = $table->addFieldInfo('id',         XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL,XMLDB_SEQUENCE, null, null, null);
        $f = $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('username',   XMLDB_TYPE_CHAR,  '100', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('token',      XMLDB_TYPE_CHAR,  '40', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('mnethostid', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('useragent',  XMLDB_TYPE_CHAR,  '40', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('confirm_timeout', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('session_id',   XMLDB_TYPE_CHAR,  '40', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('expires', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        // PK and indexes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('token', XMLDB_INDEX_UNIQUE, array('token'));
        // Create the table
        $result = $result && create_table($table);


        $table = new XMLDBTable('mnet_sso_access_control');
        $table->comment = 'Users by host permitted (or not) to login from a remote provider';
        $f = $table->addFieldInfo('id',         XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL,XMLDB_SEQUENCE, null, null, null);
        $f = $table->addFieldInfo('username',   XMLDB_TYPE_CHAR,  '100', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('mnet_host_id', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('access',  XMLDB_TYPE_CHAR,  '20', null,
                                  XMLDB_NOTNULL, NULL, null, null, 'allow');
        // PK and indexes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('mnethostid_username', XMLDB_INDEX_UNIQUE, array('mnet_host_id', 'username'));
        // Create the table
        $result = $result && create_table($table);

        if (empty($USER->mnet_host_id)) {
            $USER->mnet_host_id = $CFG->mnet_localhost_id;    // Something for the current user to prevent warnings
        }

        /**
         ** enrol/mnet tables
         **/
        $table = new XMLDBTable('mnet_enrol_course');
        $table->comment = 'Information about courses on remote hosts';
        $f = $table->addFieldInfo('id',         XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL,XMLDB_SEQUENCE, null, null, null);
        $f = $table->addFieldInfo('hostid', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('remoteid', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                          XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('cat_id', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('cat_name',  XMLDB_TYPE_CHAR,  '255', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('cat_description',  XMLDB_TYPE_TEXT,  'medium', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER,  '4', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('fullname',  XMLDB_TYPE_CHAR,  '254', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('shortname',  XMLDB_TYPE_CHAR,  '15', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('idnumber',  XMLDB_TYPE_CHAR,  '100', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('summary',  XMLDB_TYPE_TEXT,  'medium', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('startdate', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('cost',  XMLDB_TYPE_CHAR,  '10', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('currency',  XMLDB_TYPE_CHAR,  '3', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('defaultroleid', XMLDB_TYPE_INTEGER,  '4', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('defaultrolename',  XMLDB_TYPE_CHAR,  '255', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        // PK and indexes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('hostid_remoteid', XMLDB_INDEX_UNIQUE, array('hostid', 'remoteid'));
        // Create the table
        $result = $result && create_table($table);


        $table = new XMLDBTable('mnet_enrol_assignments');

        $table->comment = 'Information about enrolments on courses on remote hosts';
        $f = $table->addFieldInfo('id',         XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL,XMLDB_SEQUENCE, null, null, null);
        $f = $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('hostid', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('rolename',  XMLDB_TYPE_CHAR,  '255', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('enroltime', XMLDB_TYPE_INTEGER,  '10', XMLDB_UNSIGNED,
                                  XMLDB_NOTNULL, NULL, null, null, 0);
        $f = $table->addFieldInfo('enroltype',  XMLDB_TYPE_CHAR,  '20', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);

        // PK and indexes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addIndexInfo('hostid_courseid', XMLDB_INDEX_NOTUNIQUE, array('hostid', 'courseid'));
        $table->addIndexInfo('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        // Create the table
        $result = $result && create_table($table);

        upgrade_main_savepoint($result, 2007010301);
    }

    if ($result && $oldversion < 2007010404) {

        /// Define field shortname to be added to user_info_field
        $table = new XMLDBTable('user_info_field');
        $field = new XMLDBField('shortname');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, 'shortname', 'id');

        /// Launch add field shortname
        $result = $result && add_field($table, $field);

        /// Changing type of field name on table user_info_field to text
        $table = new XMLDBTable('user_info_field');
        $field = new XMLDBField('name');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null, null, null, 'shortname');

        /// Launch change of type for field name
        $result = $result && change_field_type($table, $field);

        /// For existing fields use 'name' as the 'shortname' entry
        if ($fields = get_records_select('user_info_field', '', '', 'id, name')) {
            foreach ($fields as $field) {
                $field->shortname = clean_param($field->name, PARAM_ALPHANUM);
                $result && update_record('user_info_field', $field);
            }
        }

        upgrade_main_savepoint($result, 2007010404);
    }

    if ($result && $oldversion < 2007011501) {
        if (!empty($CFG->enablerecordcache) && empty($CFG->rcache) &&
            // Note: won't force-load these settings into CFG
            // we don't need or want cache during the upgrade itself
            empty($CFG->cachetype) && empty($CFG->intcachemax)) {
            set_config('cachetype',   'internal');
            set_config('rcache',      true);
            set_config('intcachemax', $CFG->enablerecordcache);
            unset_config('enablerecordcache');
            unset($CFG->enablerecordcache);
        }

        upgrade_main_savepoint($result, 2007011501);
    }

    if ($result && $oldversion < 2007012100) {
    /// Some old PG servers have user->firstname & user->lastname with 30cc. They must be 100cc.
    /// Fixing that conditionally. MDL-7110
        if ($CFG->dbfamily == 'postgres') {
        /// Get Metadata from user table
            $cols = array_change_key_case($db->MetaColumns($CFG->prefix . 'user'), CASE_LOWER);

        /// Process user->firstname if needed
            if ($col = $cols['firstname']) {
                if ($col->max_length < 100) {
                /// Changing precision of field firstname on table user to (100)
                    $table = new XMLDBTable('user');
                    $field = new XMLDBField('firstname');
                    $field->setAttributes(XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, null, null, 'idnumber');

                /// Launch change of precision for field firstname
                    $result = $result && change_field_precision($table, $field);
                }
            }

        /// Process user->lastname if needed
            if ($col = $cols['lastname']) {
                if ($col->max_length < 100) {
                /// Changing precision of field lastname on table user to (100)
                    $table = new XMLDBTable('user');
                    $field = new XMLDBField('lastname');
                    $field->setAttributes(XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, null, null, 'firstname');

                /// Launch change of precision for field lastname
                    $result = $result && change_field_precision($table, $field);
                }
            }
        }

        upgrade_main_savepoint($result, 2007012100);
    }

    if ($result && $oldversion < 2007012101) {

    /// Changing precision of field lang on table course to (30)
        $table = new XMLDBTable('course');
        $field = new XMLDBField('lang');
        $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null, 'groupmodeforce');

    /// Launch change of precision for field course->lang
        $result = $result && change_field_precision($table, $field);

    /// Changing precision of field lang on table user to (30)
        $table = new XMLDBTable('user');
        $field = new XMLDBField('lang');
        $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, 'en', 'country');

    /// Launch change of precision for field user->lang
        $result = $result && change_field_precision($table, $field);

        upgrade_main_savepoint($result, 2007012101);
    }

    if ($result && $oldversion < 2007012400) {

    /// Rename field access on table mnet_sso_access_control to accessctrl
        $table = new XMLDBTable('mnet_sso_access_control');
        $field = new XMLDBField('access');
        $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, 'allow', 'mnet_host_id');

    /// Launch rename field accessctrl
        $result = $result && rename_field($table, $field, 'accessctrl');

        upgrade_main_savepoint($result, 2007012400);
    }

    if ($result && $oldversion < 2007012500) {
        execute_sql("DELETE FROM {$CFG->prefix}user WHERE username='changeme'", true);

        upgrade_main_savepoint($result, 2007012500);
    }

    if ($result && $oldversion < 2007020400) {
    /// Only for MySQL and PG, declare the user->ajax field as not null. MDL-8421.
        if ($CFG->dbfamily == 'mysql' || $CFG->dbfamily == 'postgres') {
        /// Changing nullability of field ajax on table user to not null
            $table = new XMLDBTable('user');
            $field = new XMLDBField('ajax');
            $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '1', 'htmleditor');

        /// Launch change of nullability for field ajax
            $result = $result && change_field_notnull($table, $field);
        }

        upgrade_main_savepoint($result, 2007020400);
    }

    if (!empty($CFG->rolesactive) && $result && $oldversion < 2007021401) {
    /// create default logged in user role if not present - upgrade rom 1.7.x
        if (empty($CFG->defaultuserroleid) or empty($CFG->guestroleid) or $CFG->defaultuserroleid == $CFG->guestroleid) {
            if (!get_records('role', 'shortname', 'user')) {
                $userroleid = create_role(addslashes(get_string('authenticateduser')), 'user',
                                          addslashes(get_string('authenticateduserdescription')), 'moodle/legacy:user');
                if ($userroleid) {
                    reset_role_capabilities($userroleid);
                    set_config('defaultuserroleid', $userroleid);
                }
            }
        }

        upgrade_main_savepoint($result, 2007021401);
    }

    if ($result && $oldversion < 2007021501) {
    /// delete removed setting from config
        unset_config('tabselectedtofront');

        upgrade_main_savepoint($result, 2007021501);
    }


    if ($result && $oldversion < 2007032200) {

    /// Define table role_sortorder to be created
        $table = new XMLDBTable('role_sortorder');

    /// Adding fields to table role_sortorder
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('sortoder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table role_sortorder
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->addKeyInfo('roleid', XMLDB_KEY_FOREIGN, array('roleid'), 'role', array('id'));
        $table->addKeyInfo('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

    /// Adding indexes to table role_sortorder
        $table->addIndexInfo('userid-roleid-contextid', XMLDB_INDEX_UNIQUE, array('userid', 'roleid', 'contextid'));

    /// Launch create table for role_sortorder
        $result = $result && create_table($table);

        upgrade_main_savepoint($result, 2007032200);
    }


    /// code to change lenghen tag field to 255, MDL-9095
    if ($result && $oldversion < 2007040400) {

    /// Define index text (not unique) to be dropped form tags
        $table = new XMLDBTable('tags');
        $index = new XMLDBIndex('text');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('text'));

    /// Launch drop index text
        $result = $result && drop_index($table, $index);

        $field = new XMLDBField('text');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null, 'userid');

    /// Launch change of type for field text
        $result = $result && change_field_type($table, $field);

        $index = new XMLDBIndex('text');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('text'));

    /// Launch add index text
        $result = $result && add_index($table, $index);

        upgrade_main_savepoint($result, 2007040400);
    }

    if ($result && $oldversion < 2007041100) {

    /// Define field idnumber to be added to course_modules
        $table = new XMLDBTable('course_modules');
        $field = new XMLDBField('idnumber');
        $field->setAttributes(XMLDB_TYPE_CHAR, '100', null, null, null, null, null, null, 'section');

    /// Launch add field idnumber
        $result = $result && add_field($table, $field);

        upgrade_main_savepoint($result, 2007041100);
    }

    /* Changes to the custom profile menu type - store values rather than indices.
       We could do all this with one tricky SQL statement but it's a one-off so no
       harm in using PHP loops */
    if ($result && $oldversion < 2007041600) {

    /// Get the menu fields
        if ($fields = get_records('user_info_field', 'datatype', 'menu')) {
            foreach ($fields as $field) {

            /// Get user data for the menu field
                if ($data = get_records('user_info_data', 'fieldid', $field->id)) {

                /// Get the menu options
                    $options = explode("\n", $field->param1);
                    foreach ($data as $d) {
                        $key = array_search($d->data, $options);

                    /// If the data is an integer and is not one of the options,
                    /// set the respective option value
                        if (is_int($d->data) and (($key === NULL) or ($key === false)) and isset($options[$d->data])) {
                                $d->data = $options[$d->data];
                                $result = $result && update_record('user_info_data', $d);
                        }
                    }
                }
            }
        }

        upgrade_main_savepoint($result, 2007041600);
    }

    /// adding new gradebook tables
    if ($result && $oldversion < 2007041800) {

    /// Define table events_handlers to be created
        $table = new XMLDBTable('events_handlers');

    /// Adding fields to table events_handlers
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('eventname', XMLDB_TYPE_CHAR, '166', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('handlermodule', XMLDB_TYPE_CHAR, '166', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('handlerfile', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('handlerfunction', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);

    /// Adding keys to table events_handlers
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table events_handlers
        $table->addIndexInfo('eventname-handlermodule', XMLDB_INDEX_UNIQUE, array('eventname', 'handlermodule'));

    /// Launch create table for events_handlers
        $result = $result && create_table($table);

    /// Define table events_queue to be created
        $table = new XMLDBTable('events_queue');

    /// Adding fields to table events_queue
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('eventdata', XMLDB_TYPE_TEXT, 'big', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('schedule', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('stackdump', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table events_queue
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Launch create table for events_queue
        $result = $result && create_table($table);

    /// Define table events_queue_handlers to be created
        $table = new XMLDBTable('events_queue_handlers');

    /// Adding fields to table events_queue_handlers
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('queuedeventid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('handlerid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('status', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null);
        $table->addFieldInfo('errormessage', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table events_queue_handlers
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('queuedeventid', XMLDB_KEY_FOREIGN, array('queuedeventid'), 'events_queue', array('id'));
        $table->addKeyInfo('handlerid', XMLDB_KEY_FOREIGN, array('handlerid'), 'events_handlers', array('id'));

    /// Launch create table for events_queue_handlers
        $result = $result && create_table($table);

        upgrade_main_savepoint($result, 2007041800);
    }

    if ($result && $oldversion < 2007043001) {

    /// Define field schedule to be added to events_handlers
        $table = new XMLDBTable('events_handlers');
        $field = new XMLDBField('schedule');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null, 'handlerfunction');

    /// Launch add field schedule
        $result = $result && add_field($table, $field);

    /// Define field status to be added to events_handlers
        $table = new XMLDBTable('events_handlers');
        $field = new XMLDBField('status');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'schedule');

    /// Launch add field status
        $result = $result && add_field($table, $field);

        upgrade_main_savepoint($result, 2007043001);
    }

    if ($result && $oldversion < 2007050201) {

    /// Define field theme to be added to course_categories
        $table = new XMLDBTable('course_categories');
        $field = new XMLDBField('theme');
        $field->setAttributes(XMLDB_TYPE_CHAR, '50', null, null, null, null, null, null, 'path');

    /// Launch add field theme
        $result = $result && add_field($table, $field);

        upgrade_main_savepoint($result, 2007050201);
    }

    if ($result && $oldversion < 2007051100) {

    /// Define field forceunique to be added to user_info_field
        $table = new XMLDBTable('user_info_field');
        $field = new XMLDBField('forceunique');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'visible');

    /// Launch add field forceunique
        $result = $result && add_field($table, $field);

    /// Define field signup to be added to user_info_field
        $table = new XMLDBTable('user_info_field');
        $field = new XMLDBField('signup');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'forceunique');

    /// Launch add field signup
        $result = $result && add_field($table, $field);

        upgrade_main_savepoint($result, 2007051100);
    }

    if (!empty($CFG->rolesactive) && $result && $oldversion < 2007051801) {
        // Get the role id of the "Auth. User" role and check if the default role id is different
        // note: use of assign_capability() is discouraged in upgrade script!
        $userrole = get_record( 'role', 'shortname', 'user' );
        $defaultroleid = $CFG->defaultuserroleid;

        if( $defaultroleid != $userrole->id ) {
            //  Add in the new moodle/my:manageblocks capibility to the default user role
            $context = get_context_instance(CONTEXT_SYSTEM);
            assign_capability('moodle/my:manageblocks',CAP_ALLOW,$defaultroleid,$context->id);
        }

        upgrade_main_savepoint($result, 2007051801);
    }

    if ($result && $oldversion < 2007052200) {

    /// Define field schedule to be dropped from events_queue
        $table = new XMLDBTable('events_queue');
        $field = new XMLDBField('schedule');

    /// Launch drop field stackdump
        $result = $result && drop_field($table, $field);

        upgrade_main_savepoint($result, 2007052200);
    }

    if ($result && $oldversion < 2007052300) {
        require_once($CFG->dirroot . '/question/upgrade.php');
        $result = $result && question_remove_rqp_qtype();

        upgrade_main_savepoint($result, 2007052300);
    }

    if ($result && $oldversion < 2007060500) {

    /// Define field usermodified to be added to post
        $table = new XMLDBTable('post');
        $field = new XMLDBField('usermodified');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'created');

    /// Launch add field usermodified
        $result = $result && add_field($table, $field);

    /// Define key usermodified (foreign) to be added to post
        $table = new XMLDBTable('post');
        $key = new XMLDBKey('usermodified');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

    /// Launch add key usermodified
        $result = $result && add_key($table, $key);

        upgrade_main_savepoint($result, 2007060500);
    }

    if ($result && $oldversion < 2007070603) {
        // Small update of guest user to be 100% sure it has the correct mnethostid (MDL-10375)
        set_field('user', 'mnethostid', $CFG->mnet_localhost_id, 'username', 'guest');

        upgrade_main_savepoint($result, 2007070603);
    }

    if ($result && $oldversion < 2007071400) {
        /**
         ** mnet application table
         **/
        $table = new XMLDBTable('mnet_application');
        $table->comment = 'Information about applications on remote hosts';
        $f = $table->addFieldInfo('id',         XMLDB_TYPE_INTEGER,  '10', false,
                                  XMLDB_NOTNULL,XMLDB_SEQUENCE, null, null, null);
        $f = $table->addFieldInfo('name',  XMLDB_TYPE_CHAR,  '50', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('display_name',  XMLDB_TYPE_CHAR,  '50', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('xmlrpc_server_url',  XMLDB_TYPE_CHAR,  '255', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);
        $f = $table->addFieldInfo('sso_land_url',  XMLDB_TYPE_CHAR,  '255', null,
                                  XMLDB_NOTNULL, NULL, null, null, null);

        // PK and indexes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        // Create the table
        $result = $result && create_table($table);

        // Insert initial applications (moodle and mahara)
        $application = new stdClass();
        $application->name                = 'moodle';
        $application->display_name        = 'Moodle';
        $application->xmlrpc_server_url   = '/mnet/xmlrpc/server.php';
        $application->sso_land_url        = '/auth/mnet/land.php';
        if ($result) {
            $newid  = insert_record('mnet_application', $application, false);
        }

        $application = new stdClass();
        $application->name                = 'mahara';
        $application->display_name        = 'Mahara';
        $application->xmlrpc_server_url   = '/api/xmlrpc/server.php';
        $application->sso_land_url        = '/auth/xmlrpc/land.php';
        $result = $result && insert_record('mnet_application', $application, false);

        // New mnet_host->applicationid field
        $table = new XMLDBTable('mnet_host');
        $field = new XMLDBField('applicationid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, $newid , 'last_log_id');

        $result = $result && add_field($table, $field);

    /// Define key applicationid (foreign) to be added to mnet_host
        $table = new XMLDBTable('mnet_host');
        $key = new XMLDBKey('applicationid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('applicationid'), 'mnet_application', array('id'));

    /// Launch add key applicationid
        $result = $result && add_key($table, $key);

        upgrade_main_savepoint($result, 2007071400);
    }

    if ($result && $oldversion < 2007071607) {
        require_once($CFG->dirroot . '/question/upgrade.php');
        $result = $result && question_remove_rqp_qtype_config_string();

        upgrade_main_savepoint($result, 2007071607);
    }

    if ($result && $oldversion < 2007072200) {

/// Remove all grade tables used in development phases - we need new empty tables for final gradebook upgrade
        $tables = array('grade_categories',
                        'grade_items',
                        'grade_calculations',
                        'grade_grades',
                        'grade_grades_raw',
                        'grade_grades_final',
                        'grade_grades_text',
                        'grade_outcomes',
                        'grade_outcomes_courses',
                        'grade_history',
                        'grade_import_newitem',
                        'grade_import_values');

        foreach ($tables as $table) {
            $table = new XMLDBTable($table);
            if (table_exists($table)) {
                drop_table($table);
            }
        }

        $tables = array('grade_categories_history',
                        'grade_items_history',
                        'grade_grades_history',
                        'grade_grades_text_history',
                        'grade_scale_history',
                        'grade_outcomes_history');

        foreach ($tables as $table) {
            $table = new XMLDBTable($table);
            if (table_exists($table)) {
                drop_table($table);
            }
        }


    /// Define table grade_outcomes to be created
        $table = new XMLDBTable('grade_outcomes');

    /// Adding fields to table grade_outcomes
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table grade_outcomes
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->addKeyInfo('scaleid', XMLDB_KEY_FOREIGN, array('scaleid'), 'scale', array('id'));
        $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

    /// Launch create table for grade_outcomes
        $result = $result && create_table($table);


    /// Define table grade_categories to be created
        $table = new XMLDBTable('grade_categories');

    /// Adding fields to table grade_categories
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('parent', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('depth', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('path', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('aggregation', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('keephigh', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('droplow', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('aggregateonlygraded', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('aggregateoutcomes', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('aggregatesubcats', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table grade_categories
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->addKeyInfo('parent', XMLDB_KEY_FOREIGN, array('parent'), 'grade_categories', array('id'));

    /// Launch create table for grade_categories
        $result = $result && create_table($table);


    /// Define table grade_items to be created
        $table = new XMLDBTable('grade_items');

    /// Adding fields to table grade_items
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('categoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('itemname', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('itemtype', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('itemmodule', XMLDB_TYPE_CHAR, '30', null, null, null, null, null, null);
        $table->addFieldInfo('iteminstance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('itemnumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('iteminfo', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('calculation', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('gradetype', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '1');
        $table->addFieldInfo('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '100');
        $table->addFieldInfo('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('outcomeid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('gradepass', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('multfactor', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '1.0');
        $table->addFieldInfo('plusfactor', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('aggregationcoef', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('display', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('decimals', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('locktime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('needsupdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table grade_items
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->addKeyInfo('categoryid', XMLDB_KEY_FOREIGN, array('categoryid'), 'grade_categories', array('id'));
        $table->addKeyInfo('scaleid', XMLDB_KEY_FOREIGN, array('scaleid'), 'scale', array('id'));
        $table->addKeyInfo('outcomeid', XMLDB_KEY_FOREIGN, array('outcomeid'), 'grade_outcomes', array('id'));

    /// Adding indexes to table grade_grades
        $table->addIndexInfo('locked-locktime', XMLDB_INDEX_NOTUNIQUE, array('locked', 'locktime'));
        $table->addIndexInfo('itemtype-needsupdate', XMLDB_INDEX_NOTUNIQUE, array('itemtype', 'needsupdate'));
        $table->addIndexInfo('gradetype', XMLDB_INDEX_NOTUNIQUE, array('gradetype'));

    /// Launch create table for grade_items
        $result = $result && create_table($table);


    /// Define table grade_grades to be created
        $table = new XMLDBTable('grade_grades');

    /// Adding fields to table grade_grades
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('rawgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
        $table->addFieldInfo('rawgrademax', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '100');
        $table->addFieldInfo('rawgrademin', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('rawscaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('finalgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
        $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('locktime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('exported', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('overridden', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('excluded', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('feedback', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('feedbackformat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('information', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('informationformat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table grade_grades
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
        $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->addKeyInfo('rawscaleid', XMLDB_KEY_FOREIGN, array('rawscaleid'), 'scale', array('id'));
        $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

    /// Adding indexes to table grade_grades
        $table->addIndexInfo('locked-locktime', XMLDB_INDEX_NOTUNIQUE, array('locked', 'locktime'));

    /// Launch create table for grade_grades
        $result = $result && create_table($table);


    /// Define table grade_outcomes_history to be created
        $table = new XMLDBTable('grade_outcomes_history');

    /// Adding fields to table grade_outcomes_history
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('action', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('oldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('loggeduser', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);

    /// Adding keys to table grade_outcomes_history
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('oldid', XMLDB_KEY_FOREIGN, array('oldid'), 'grade_outcomes', array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->addKeyInfo('scaleid', XMLDB_KEY_FOREIGN, array('scaleid'), 'scale', array('id'));
        $table->addKeyInfo('loggeduser', XMLDB_KEY_FOREIGN, array('loggeduser'), 'user', array('id'));

    /// Adding indexes to table grade_outcomes_history
        $table->addIndexInfo('action', XMLDB_INDEX_NOTUNIQUE, array('action'));

    /// Launch create table for grade_outcomes_history
        $result = $result && create_table($table);


    /// Define table grade_categories_history to be created
        $table = new XMLDBTable('grade_categories_history');

    /// Adding fields to table grade_categories_history
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('action', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('oldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('loggeduser', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('parent', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('depth', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('path', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('aggregation', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('keephigh', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('droplow', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('aggregateonlygraded', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('aggregateoutcomes', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('aggregatesubcats', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

    /// Adding keys to table grade_categories_history
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('oldid', XMLDB_KEY_FOREIGN, array('oldid'), 'grade_categories', array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->addKeyInfo('parent', XMLDB_KEY_FOREIGN, array('parent'), 'grade_categories', array('id'));
        $table->addKeyInfo('loggeduser', XMLDB_KEY_FOREIGN, array('loggeduser'), 'user', array('id'));

    /// Adding indexes to table grade_categories_history
        $table->addIndexInfo('action', XMLDB_INDEX_NOTUNIQUE, array('action'));

    /// Launch create table for grade_categories_history
        $result = $result && create_table($table);


    /// Define table grade_items_history to be created
        $table = new XMLDBTable('grade_items_history');

    /// Adding fields to table grade_items_history
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('action', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('oldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('loggeduser', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('categoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('itemname', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('itemtype', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('itemmodule', XMLDB_TYPE_CHAR, '30', null, null, null, null, null, null);
        $table->addFieldInfo('iteminstance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('itemnumber', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('iteminfo', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('idnumber', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('calculation', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('gradetype', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '1');
        $table->addFieldInfo('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '100');
        $table->addFieldInfo('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('outcomeid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('gradepass', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('multfactor', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '1.0');
        $table->addFieldInfo('plusfactor', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('aggregationcoef', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('display', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('decimals', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('locktime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('needsupdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');

    /// Adding keys to table grade_items_history
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('oldid', XMLDB_KEY_FOREIGN, array('oldid'), 'grade_items', array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->addKeyInfo('categoryid', XMLDB_KEY_FOREIGN, array('categoryid'), 'grade_categories', array('id'));
        $table->addKeyInfo('scaleid', XMLDB_KEY_FOREIGN, array('scaleid'), 'scale', array('id'));
        $table->addKeyInfo('outcomeid', XMLDB_KEY_FOREIGN, array('outcomeid'), 'grade_outcomes', array('id'));
        $table->addKeyInfo('loggeduser', XMLDB_KEY_FOREIGN, array('loggeduser'), 'user', array('id'));

    /// Adding indexes to table grade_items_history
        $table->addIndexInfo('action', XMLDB_INDEX_NOTUNIQUE, array('action'));

    /// Launch create table for grade_items_history
        $result = $result && create_table($table);


    /// Define table grade_grades_history to be created
        $table = new XMLDBTable('grade_grades_history');

    /// Adding fields to table grade_grades_history
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('action', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('oldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('loggeduser', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('rawgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
        $table->addFieldInfo('rawgrademax', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '100');
        $table->addFieldInfo('rawgrademin', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('rawscaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('finalgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
        $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('locktime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('exported', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('overridden', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('excluded', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('feedback', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('feedbackformat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('information', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('informationformat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

    /// Adding keys to table grade_grades_history
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('oldid', XMLDB_KEY_FOREIGN, array('oldid'), 'grade_grades', array('id'));
        $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
        $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->addKeyInfo('rawscaleid', XMLDB_KEY_FOREIGN, array('rawscaleid'), 'scale', array('id'));
        $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));
        $table->addKeyInfo('loggeduser', XMLDB_KEY_FOREIGN, array('loggeduser'), 'user', array('id'));

    /// Adding indexes to table grade_grades_history
        $table->addIndexInfo('action', XMLDB_INDEX_NOTUNIQUE, array('action'));

    /// Launch create table for grade_grades_history
        $result = $result && create_table($table);


    /// Define table scale_history to be created
        $table = new XMLDBTable('scale_history');

    /// Adding fields to table scale_history
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('action', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('oldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('loggeduser', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('scale', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table scale_history
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('oldid', XMLDB_KEY_FOREIGN, array('oldid'), 'scale', array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->addKeyInfo('loggeduser', XMLDB_KEY_FOREIGN, array('loggeduser'), 'user', array('id'));

    /// Adding indexes to table scale_history
        $table->addIndexInfo('action', XMLDB_INDEX_NOTUNIQUE, array('action'));

    /// Launch create table for scale_history
        $result = $result && create_table($table);

    /// upgrade the old 1.8 gradebook - migrade data into new grade tables
        if ($result) {
            if ($rs = get_recordset('course')) {
                while ($course = rs_fetch_next_record($rs)) {
                    // this function uses SQL only, it must not be changed after 1.9 goes stable!!
                    if (!upgrade_18_gradebook($course->id)) {
                        $result = false;
                        break;
                    }
                }
                rs_close($rs);
            }
        }

        upgrade_main_savepoint($result, 2007072200);
    }

    if ($result && $oldversion < 2007072400) {
    /// Dropping one DEFAULT in a TEXT column. It's was only one remaining
    /// since Moodle 1.7, so new servers won't have those anymore.

    /// Changing the default of field sessdata on table sessions2 to drop it
        $table = new XMLDBTable('sessions2');
        $field = new XMLDBField('sessdata');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null, 'modified');

    /// Launch change of default for field sessdata
        $result = $result && change_field_default($table, $field);

        upgrade_main_savepoint($result, 2007072400);
    }


    if ($result && $oldversion < 2007073100) {
    /// Define table grade_outcomes_courses to be created
        $table = new XMLDBTable('grade_outcomes_courses');

    /// Adding fields to table grade_outcomes_courses
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('outcomeid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table grade_outcomes_courses
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->addKeyInfo('outcomeid', XMLDB_KEY_FOREIGN, array('outcomeid'), 'grade_outcomes', array('id'));
        $table->addKeyInfo('courseid-outcomeid', XMLDB_KEY_UNIQUE, array('courseid', 'outcomeid'));
    /// Launch create table for grade_outcomes_courses
        $result = $result && create_table($table);

        upgrade_main_savepoint($result, 2007073100);
    }


    if ($result && $oldversion < 2007073101) {    // Add new tag tables

    /// Define table tag to be created
        $table = new XMLDBTable('tag');

    /// Adding fields to table tag
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('tagtype', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);
        $table->addFieldInfo('descriptionformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('flag', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, null, null, '0');
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table tag
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table tag
        $table->addIndexInfo('name', XMLDB_INDEX_UNIQUE, array('name'));

    /// Launch create table for tag
        $result = $result && create_table($table);



    /// Define table tag_correlation to be created
        $table = new XMLDBTable('tag_correlation');

    /// Adding fields to table tag_correlation
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('tagid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('correlatedtags', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table tag_correlation
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table tag_correlation
        $table->addIndexInfo('tagid', XMLDB_INDEX_UNIQUE, array('tagid'));

    /// Launch create table for tag_correlation
        $result = $result && create_table($table);



    /// Define table tag_instance to be created
        $table = new XMLDBTable('tag_instance');

    /// Adding fields to table tag_instance
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('tagid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('itemtype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table tag_instance
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table tag_instance
        $table->addIndexInfo('tagiditem', XMLDB_INDEX_NOTUNIQUE, array('tagid', 'itemtype', 'itemid'));

    /// Launch create table for tag_instance
        $result = $result && create_table($table);

        upgrade_main_savepoint($result, 2007073101);
    }


    if ($result && $oldversion < 2007073103) {

    /// Define field rawname to be added to tag
        $table = new XMLDBTable('tag');
        $field = new XMLDBField('rawname');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null, 'name');

    /// Launch add field rawname
        $result = $result && add_field($table, $field);

        upgrade_main_savepoint($result, 2007073103);
    }

    if ($result && $oldversion < 2007073105) {

    /// Define field description to be added to grade_outcomes
        $table = new XMLDBTable('grade_outcomes');
        $field = new XMLDBField('description');
        if (!field_exists($table, $field)) {
            $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'scaleid');
        /// Launch add field description
            $result = $result && add_field($table, $field);
        }

        $table = new XMLDBTable('grade_outcomes_history');
        $field = new XMLDBField('description');
        if (!field_exists($table, $field)) {
            $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'scaleid');
        /// Launch add field description
            $result = $result && add_field($table, $field);
        }

        upgrade_main_savepoint($result, 2007073105);
    }

    // adding unique contraint on (courseid,shortname) of an outcome
    if ($result && $oldversion < 2007080100) {

    /// Define key courseid-shortname (unique) to be added to grade_outcomes
        $table = new XMLDBTable('grade_outcomes');
        $key = new XMLDBKey('courseid-shortname');
        $key->setAttributes(XMLDB_KEY_UNIQUE, array('courseid', 'shortname'));

    /// Launch add key courseid-shortname
        $result = $result && add_key($table, $key);

        upgrade_main_savepoint($result, 2007080100);
    }

    /// originally there was supportname and supportemail upgrade code - this is handled in upgradesettings.php instead

    if ($result && $oldversion < 2007080202) {

    /// Define index tagiditem (not unique) to be dropped form tag_instance
        $table = new XMLDBTable('tag_instance');
        $index = new XMLDBIndex('tagiditem');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('tagid', 'itemtype', 'itemid'));

    /// Launch drop index tagiditem
        drop_index($table, $index);

   /// Define index tagiditem (unique) to be added to tag_instance
        $table = new XMLDBTable('tag_instance');
        $index = new XMLDBIndex('tagiditem');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('tagid', 'itemtype', 'itemid'));

    /// Launch add index tagiditem
        $result = $result && add_index($table, $index);

        upgrade_main_savepoint($result, 2007080202);
    }

    if ($result && $oldversion < 2007080300) {

    /// Define field aggregateoutcomes to be added to grade_categories
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('aggregateoutcomes');
        if (!field_exists($table, $field)) {
            $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'droplow');

        /// Launch add field aggregateoutcomes
            $result = $result && add_field($table, $field);
        }

    /// Define field aggregateoutcomes to be added to grade_categories
        $table = new XMLDBTable('grade_categories_history');
        $field = new XMLDBField('aggregateoutcomes');
        if (!field_exists($table, $field)) {
            $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'droplow');

        /// Launch add field aggregateoutcomes
            $result = $result && add_field($table, $field);
        }

        upgrade_main_savepoint($result, 2007080300);
    }

    if ($result && $oldversion < 2007080800) { /// Normalize course->shortname MDL-10026

    /// Changing precision of field shortname on table course to (100)
        $table = new XMLDBTable('course');
        $field = new XMLDBField('shortname');
        $field->setAttributes(XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, null, null, 'fullname');

    /// Launch change of precision for field shortname
        $result = $result && change_field_precision($table, $field);

        upgrade_main_savepoint($result, 2007080800);
    }

    if ($result && $oldversion < 2007080900) {
    /// Add context.path & index
        $table = new XMLDBTable('context');
        $field = new XMLDBField('path');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null, 'instanceid');
        $result = $result && add_field($table, $field);
        $table = new XMLDBTable('context');
        $index = new XMLDBIndex('path');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('path'));
        $result = $result && add_index($table, $index);

    /// Add context.depth
        $table = new XMLDBTable('context');
        $field = new XMLDBField('depth');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'path');
        $result = $result && add_field($table, $field);

    /// make sure the system context has proper data
        get_system_context(false);

        upgrade_main_savepoint($result, 2007080900);
    }

    if ($result && $oldversion < 2007080903) {
    /// Define index
        $table = new XMLDBTable('grade_grades');
        $index = new XMLDBIndex('locked-locktime');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('locked', 'locktime'));

        if (!index_exists($table, $index)) {
        /// Launch add index
            $result = $result && add_index($table, $index);
        }

    /// Define index
        $table = new XMLDBTable('grade_items');
        $index = new XMLDBIndex('locked-locktime');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('locked', 'locktime'));

        if (!index_exists($table, $index)) {
        /// Launch add index
            $result = $result && add_index($table, $index);
        }

    /// Define index itemtype-needsupdate (not unique) to be added to grade_items
        $table = new XMLDBTable('grade_items');
        $index = new XMLDBIndex('itemtype-needsupdate');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('itemtype', 'needsupdate'));
        if (!index_exists($table, $index)) {
        /// Launch add index itemtype-needsupdate
            $result = $result && add_index($table, $index);
        }

    /// Define index
        $table = new XMLDBTable('grade_items');
        $index = new XMLDBIndex('gradetype');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('gradetype'));

        if (!index_exists($table, $index)) {
        /// Launch add index
            $result = $result && add_index($table, $index);
        }

        upgrade_main_savepoint($result, 2007080903);
    }

    if ($result && $oldversion < 2007081000) {
        require_once($CFG->dirroot . '/question/upgrade.php');
        $result = $result && question_upgrade_context_etc();

        upgrade_main_savepoint($result, 2007081000);
    }

    if ($result && $oldversion < 2007081302) {

        $table = new XMLDBTable('groups');
        $field = new XMLDBField('password');

        if (field_exists($table, $field)) { 
    /// 1.7.*/1.6.*/1.5.* - create 'groupings' and 'groupings_groups' + rename password to enrolmentkey
    /// or second run after fixing structure broken from 1.8.x
            $result = $result && upgrade_17_groups();

        } else if (table_exists(new XMLDBTable('groups_groupings'))) {
    /// ELSE 'groups_groupings' table exists, this is 1.8.* properly upgraded
            $result = $result && upgrade_18_groups();

        } else {
    /// broken groups, failed 1.8.x upgrade
            upgrade_18_broken_groups();
            notify('Warning: failed groups upgrade detected! Unfortunately this problem '.
                   'can not be fixed automatically. Mapping of groups to courses was lost, '.
                   'you can either revert to backup from 1.7.x and run ugprade again or '. 
                   'continue and fill in the missing course ids into groups table manually.');
            $result = false;
        }

        upgrade_main_savepoint($result, 2007081302);
    }

    if ($result && $oldversion < 2007081303) {
    /// Common groups upgrade for 1.8.* and 1.7.*/1.6.*..

        // delete not used fields
        $table = new XMLDBTable('groups');
        $field = new XMLDBField('theme');
        if (field_exists($table, $field)) {
            drop_field($table, $field);
        }
        $table = new XMLDBTable('groups');
        $field = new XMLDBField('lang');
        if (field_exists($table, $field)) {
            drop_field($table, $field);
        }

    /// Add groupingid field/f.key to 'course' table.
        $table = new XMLDBTable('course');
        $field = new XMLDBField('defaultgroupingid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', $prev='groupmodeforce');
        $result = $result && add_field($table, $field);


    /// Add grouping ID, grouponly field/f.key to 'course_modules' table.
        $table = new XMLDBTable('course_modules');
        $field = new XMLDBField('groupingid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', $prev='groupmode');
        $result = $result && add_field($table, $field);

        $table = new XMLDBTable('course_modules');
        $field = new XMLDBField('groupmembersonly');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', $prev='groupingid');
        $result = $result && add_field($table, $field);

        $table = new XMLDBTable('course_modules');
        $key = new XMLDBKey('groupingid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('groupingid'), 'groupings', array('id'));
        $result = $result && add_key($table, $key);

        upgrade_main_savepoint($result, 2007081303);
    }

    if ($result && $oldversion < 2007082300) {

    /// Define field ordering to be added to tag_instance table
        $table = new XMLDBTable('tag_instance');
        $field = new XMLDBField('ordering');

        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'itemid');

    /// Launch add field rawname
        $result = $result && add_field($table, $field);

        upgrade_main_savepoint($result, 2007082300);
    }

    if ($result && $oldversion < 2007082700) {

    /// Define field timemodified to be added to tag_instance
        $table = new XMLDBTable('tag_instance');
        $field = new XMLDBField('timemodified');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'ordering');

    /// Launch add field timemodified
        $result = $result && add_field($table, $field);

        upgrade_main_savepoint($result, 2007082700);
    }

    /// migrate all tags table to tag - this code MUST use SQL only,
    /// because if the db structure changes the library functions will fail in future
    if ($result && $oldversion < 2007082701) {
        $tagrefs = array(); // $tagrefs[$oldtagid] = $newtagid
        if ($rs = get_recordset('tags')) {
            $db->debug = false;
            while ($oldtag = rs_fetch_next_record($rs)) {
                $raw_normalized = clean_param($oldtag->text, PARAM_TAG);
                $normalized     = moodle_strtolower($raw_normalized);
                // if this tag does not exist in tag table yet
                if (!$newtag = get_record('tag', 'name', addslashes($normalized), '', '', '', '', 'id')) {
                    $itag = new object();
                    $itag->name         = $normalized;
                    $itag->rawname      = $raw_normalized;
                    $itag->userid       = $oldtag->userid;
                    $itag->timemodified = time();
                    $itag->descriptionformat = 0; // default format
                    if ($oldtag->type == 'official') {
                        $itag->tagtype  = 'official';
                    } else {
                        $itag->tagtype  = 'default';
                    }

                    if ($idx = insert_record('tag', addslashes_recursive($itag))) {
                        $tagrefs[$oldtag->id] = $idx;
                    }
                // if this tag is already used by tag table
                } else {
                    $tagrefs[$oldtag->id] = $newtag->id;
                }
            }
            $db->debug = true;
            rs_close($rs);
        }

        // fetch all the tag instances and migrate them as well
        if ($rs = get_recordset('blog_tag_instance')) {
            $db->debug = false;
            while ($blogtag = rs_fetch_next_record($rs)) {
                if (array_key_exists($blogtag->tagid, $tagrefs)) {
                    $tag_instance = new object();
                    $tag_instance->tagid        = $tagrefs[$blogtag->tagid];
                    $tag_instance->itemtype     = 'blog';
                    $tag_instance->itemid       = $blogtag->entryid;
                    $tag_instance->ordering     = 1; // does not matter much, because originally there was no ordering in blogs
                    $tag_instance->timemodified = time();
                    insert_record('tag_instance', $tag_instance);
                }
            }
            $db->debug = true;
            rs_close($rs);
        }

        unset($tagrefs); // release memory

        $table = new XMLDBTable('tags');
        drop_table($table);
        $table = new XMLDBTable('blog_tag_instance');
        drop_table($table);

        upgrade_main_savepoint($result, 2007082701);
    }

    /// MDL-11015, MDL-11016
    if ($result && $oldversion < 2007082800) {

    /// Changing type of field userid on table tag to int
        $table = new XMLDBTable('tag');
        $field = new XMLDBField('userid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null, 'id');

    /// Launch change of type for field userid
        $result = $result && change_field_type($table, $field);

    /// Changing type of field descriptionformat on table tag to int
        $table = new XMLDBTable('tag');
        $field = new XMLDBField('descriptionformat');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'description');

    /// Launch change of type for field descriptionformat
        $result = $result && change_field_type($table, $field);

    /// Define key userid (foreign) to be added to tag
        $table = new XMLDBTable('tag');
        $key = new XMLDBKey('userid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Launch add key userid
        $result = $result && add_key($table, $key);

    /// Define index tagiditem (unique) to be dropped form tag_instance
        $table = new XMLDBTable('tag_instance');
        $index = new XMLDBIndex('tagiditem');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('tagid', 'itemtype', 'itemid'));

    /// Launch drop index tagiditem
        $result = $result && drop_index($table, $index);

    /// Changing type of field tagid on table tag_instance to int
        $table = new XMLDBTable('tag_instance');
        $field = new XMLDBField('tagid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null, 'id');

    /// Launch change of type for field tagid
        $result = $result && change_field_type($table, $field);

    /// Define key tagid (foreign) to be added to tag_instance
        $table = new XMLDBTable('tag_instance');
        $key = new XMLDBKey('tagid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('tagid'), 'tag', array('id'));

     /// Launch add key tagid
        $result = $result && add_key($table, $key);

    /// Changing sign of field itemid on table tag_instance to unsigned
        $table = new XMLDBTable('tag_instance');
        $field = new XMLDBField('itemid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null, 'itemtype');

    /// Launch change of sign for field itemid
        $result = $result && change_field_unsigned($table, $field);

    /// Changing sign of field ordering on table tag_instance to unsigned
        $table = new XMLDBTable('tag_instance');
        $field = new XMLDBField('ordering');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'itemid');

    /// Launch change of sign for field ordering
        $result = $result && change_field_unsigned($table, $field);

    /// Define index itemtype-itemid-tagid (unique) to be added to tag_instance
        $table = new XMLDBTable('tag_instance');
        $index = new XMLDBIndex('itemtype-itemid-tagid');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('itemtype', 'itemid', 'tagid'));

    /// Launch add index itemtype-itemid-tagid
        $result = $result && add_index($table, $index);

    /// Define index tagid (unique) to be dropped form tag_correlation
        $table = new XMLDBTable('tag_correlation');
        $index = new XMLDBIndex('tagid');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('tagid'));

    /// Launch drop index tagid
        $result = $result && drop_index($table, $index);

    /// Changing type of field tagid on table tag_correlation to int
        $table = new XMLDBTable('tag_correlation');
        $field = new XMLDBField('tagid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null, 'id');

    /// Launch change of type for field tagid
        $result = $result && change_field_type($table, $field);


    /// Define key tagid (foreign) to be added to tag_correlation
        $table = new XMLDBTable('tag_correlation');
        $key = new XMLDBKey('tagid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('tagid'), 'tag', array('id'));

    /// Launch add key tagid
        $result = $result && add_key($table, $key);

        upgrade_main_savepoint($result, 2007082800);
    }


    if ($result && $oldversion < 2007082801) {

    /// Define table user_private_key to be created
        $table = new XMLDBTable('user_private_key');

    /// Adding fields to table user_private_key
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('script', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('value', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('iprestriction', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('validuntil', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table user_private_key
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table user_private_key
        $table->addIndexInfo('script-value', XMLDB_INDEX_NOTUNIQUE, array('script', 'value'));

    /// Launch create table for user_private_key
        $result = $result && create_table($table);

        upgrade_main_savepoint($result, 2007082801);
    }

/// Going to modify the applicationid from int(1) to int(10). Dropping and
/// re-creating the associated keys/indexes is mandatory to be cross-db. MDL-11042
    if ($result && $oldversion < 2007082803) {

    /// Define key applicationid (foreign) to be dropped form mnet_host
        $table = new XMLDBTable('mnet_host');
        $key = new XMLDBKey('applicationid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('applicationid'), 'mnet_application', array('id'));

    /// Launch drop key applicationid
        $result = $result && drop_key($table, $key);

    /// Changing type of field applicationid on table mnet_host to int
        $field = new XMLDBField('applicationid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '1', 'last_log_id');

    /// Launch change of type for field applicationid
        $result = $result && change_field_type($table, $field);

    /// Define key applicationid (foreign) to be added to mnet_host
        $key = new XMLDBKey('applicationid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('applicationid'), 'mnet_application', array('id'));

    /// Launch add key applicationid
        $result = $result && add_key($table, $key);

        upgrade_main_savepoint($result, 2007082803);
    }

    if ($result && $oldversion < 2007090503) {
    /// Define field aggregatesubcats to be added to grade_categories
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('aggregatesubcats');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'aggregateoutcomes');

        if (!field_exists($table, $field)) {
        /// Launch add field aggregateonlygraded
            $result = $result && add_field($table, $field);
        }

    /// Define field aggregateonlygraded to be added to grade_categories
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('aggregateonlygraded');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'droplow');

        if (!field_exists($table, $field)) {
        /// Launch add field aggregateonlygraded
            $result = $result && add_field($table, $field);
        }

    /// Define field aggregatesubcats to be added to grade_categories_history
        $table = new XMLDBTable('grade_categories_history');
        $field = new XMLDBField('aggregatesubcats');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'aggregateoutcomes');

        if (!field_exists($table, $field)) {
        /// Launch add field aggregateonlygraded
            $result = $result && add_field($table, $field);
        }

    /// Define field aggregateonlygraded to be added to grade_categories_history
        $table = new XMLDBTable('grade_categories_history');
        $field = new XMLDBField('aggregateonlygraded');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'droplow');

        if (!field_exists($table, $field)) {
        /// Launch add field aggregateonlygraded
            $result = $result && add_field($table, $field);
        }

    /// upgrade path in grade_categrories table - now using slash on both ends
        $concat = sql_concat('path', "'/'");
        $sql = "UPDATE {$CFG->prefix}grade_categories SET path = $concat WHERE path NOT LIKE '/%/'";
        execute_sql($sql, true);

    /// convert old aggregation constants if needed
        /*for ($i=0; $i<=12; $i=$i+2) {
            $j = $i+1;
            $sql = "UPDATE {$CFG->prefix}grade_categories SET aggregation = $i, aggregateonlygraded = 1 WHERE aggregation = $j";
            execute_sql($sql, true);
        }*/ // not needed anymore - breaks upgrade now

        upgrade_main_savepoint($result, 2007090503);
    }

/// To have UNIQUE indexes over NULLable columns isn't cross-db at all
/// so we create a non unique index and programatically enforce uniqueness
    if ($result && $oldversion < 2007090600) {

    /// Define index idnumber (unique) to be dropped form course_modules
        $table = new XMLDBTable('course_modules');
        $index = new XMLDBIndex('idnumber');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('idnumber'));

    /// Launch drop index idnumber
        $result = $result && drop_index($table, $index);

    /// Define index idnumber-course (not unique) to be added to course_modules
        $table = new XMLDBTable('course_modules');
        $index = new XMLDBIndex('idnumber-course');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('idnumber', 'course'));

    /// Launch add index idnumber-course
        $result = $result && add_index($table, $index);

    /// Define index idnumber-courseid (not unique) to be added to grade_items
        $table = new XMLDBTable('grade_items');
        $index = new XMLDBIndex('idnumber-courseid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('idnumber', 'courseid'));

    /// Launch add index idnumber-courseid
        $result = $result && add_index($table, $index);

        upgrade_main_savepoint($result, 2007090600);
    }

/// Create the permanent context_temp table to be used by build_context_path()
    if ($result && $oldversion < 2007092001) {

    /// Define table context_temp to be created
        $table = new XMLDBTable('context_temp');

    /// Adding fields to table context_temp
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('path', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('depth', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table context_temp
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Launch create table for context_temp
        $result = $result && create_table($table);

    /// make sure category depths, parents and paths are ok, categories from 1.5 may not be properly initialized (MDL-12585)
        upgrade_fix_category_depths();

    /// Recalculate depths, paths and so on
        if (!empty($CFG->rolesactive)) {
            cleanup_contexts();
            // make sure all course, category and user contexts exist - we need it for grade letter upgrade, etc.
            create_contexts(CONTEXT_COURSE, false, true);
            create_contexts(CONTEXT_USER, false, true);
            // we need all contexts path/depths filled properly
            build_context_path(true, true);
            load_all_capabilities();

        } else {
            // upgrade from 1.6 - build all contexts
            create_contexts(null, true, true);
        }

        upgrade_main_savepoint($result, 2007092001);
    }

    /**
     * Merging of grade_grades_text back into grade_grades
     */
    if ($result && $oldversion < 2007092002) {

    /// Define field feedback to be added to grade_grades
        $table = new XMLDBTable('grade_grades');
        $field = new XMLDBField('feedback');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null, 'excluded');

        if (!field_exists($table, $field)) {
        /// Launch add field feedback
            $result = $result && add_field($table, $field);
        }

    /// Define field feedbackformat to be added to grade_grades
        $table = new XMLDBTable('grade_grades');
        $field = new XMLDBField('feedbackformat');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'feedback');

        if (!field_exists($table, $field)) {
        /// Launch add field feedbackformat
            $result = $result && add_field($table, $field);
        }

    /// Define field information to be added to grade_grades
        $table = new XMLDBTable('grade_grades');
        $field = new XMLDBField('information');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null, 'feedbackformat');

        if (!field_exists($table, $field)) {
        /// Launch add field information
            $result = $result && add_field($table, $field);
        }

    /// Define field informationformat to be added to grade_grades
        $table = new XMLDBTable('grade_grades');
        $field = new XMLDBField('informationformat');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'information');

        if (!field_exists($table, $field)) {
        /// Launch add field informationformat
            $result = $result && add_field($table, $field);
        }

    /// Define field feedback to be added to grade_grades_history
        $table = new XMLDBTable('grade_grades_history');
        $field = new XMLDBField('feedback');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null, 'excluded');

        if (!field_exists($table, $field)) {
        /// Launch add field feedback
            $result = $result && add_field($table, $field);
        }

    /// Define field feedbackformat to be added to grade_grades_history
        $table = new XMLDBTable('grade_grades_history');
        $field = new XMLDBField('feedbackformat');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'feedback');

        if (!field_exists($table, $field)) {
        /// Launch add field feedbackformat
            $result = $result && add_field($table, $field);
        }

    /// Define field information to be added to grade_grades_history
        $table = new XMLDBTable('grade_grades_history');
        $field = new XMLDBField('information');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null, 'feedbackformat');

        if (!field_exists($table, $field)) {
        /// Launch add field information
            $result = $result && add_field($table, $field);
        }

    /// Define field informationformat to be added to grade_grades_history
        $table = new XMLDBTable('grade_grades_history');
        $field = new XMLDBField('informationformat');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'information');

        if (!field_exists($table, $field)) {
        /// Launch add field informationformat
            $result = $result && add_field($table, $field);
        }

        $table = new XMLDBTable('grade_grades_text');
        if ($result and table_exists($table)) {
            //migrade existing data into grade_grades table - this is slow but works for all dbs,
            //it will be executed on development sites only
            $fields = array('feedback', 'information');
            foreach ($fields as $field) {
                $sql = "UPDATE {$CFG->prefix}grade_grades
                           SET $field = (
                                SELECT $field
                                  FROM {$CFG->prefix}grade_grades_text ggt
                                 WHERE ggt.gradeid = {$CFG->prefix}grade_grades.id)";
                $result = execute_sql($sql) && $result;
            }
            $fields = array('feedbackformat', 'informationformat');
            foreach ($fields as $field) {
                $sql = "UPDATE {$CFG->prefix}grade_grades
                           SET $field = COALESCE((
                                SELECT $field
                                  FROM {$CFG->prefix}grade_grades_text ggt
                                 WHERE ggt.gradeid = {$CFG->prefix}grade_grades.id), 0)";
                $result = execute_sql($sql) && $result;
            }

            if ($result) {
                $tables = array('grade_grades_text', 'grade_grades_text_history');

                foreach ($tables as $table) {
                    $table = new XMLDBTable($table);
                    if (table_exists($table)) {
                        drop_table($table);
                    }
                }
            }
        }

        upgrade_main_savepoint($result, 2007092002);
    }

    if ($result && $oldversion < 2007092803) {

/// Remove obsoleted unit tests tables - they will be recreated automatically
        $tables = array('grade_categories',
                        'scale',
                        'grade_items',
                        'grade_calculations',
                        'grade_grades',
                        'grade_grades_raw',
                        'grade_grades_final',
                        'grade_grades_text',
                        'grade_outcomes',
                        'grade_outcomes_courses');

        foreach ($tables as $tablename) {
            $table = new XMLDBTable('unittest_'.$tablename);
            if (table_exists($table)) {
                drop_table($table);
            }
            $table = new XMLDBTable('unittest_'.$tablename.'_history');
            if (table_exists($table)) {
                drop_table($table);
            }
        }

    /// Define field display to be added to grade_items
        $table = new XMLDBTable('grade_items');
        $field = new XMLDBField('display');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0', 'sortorder');

    /// Launch add field display
        if (!field_exists($table, $field)) {
            $result = $result && add_field($table, $field);
        } else {
            $result = $result && change_field_default($table, $field);
        }

    /// Define field display to be added to grade_items_history
        $table = new XMLDBTable('grade_items_history');
        $field = new XMLDBField('display');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0', 'sortorder');

    /// Launch add field display
        if (!field_exists($table, $field)) {
            $result = $result && add_field($table, $field);
        }


    /// Define field decimals to be added to grade_items
        $table = new XMLDBTable('grade_items');
        $field = new XMLDBField('decimals');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null, 'display');

    /// Launch add field decimals
        if (!field_exists($table, $field)) {
            $result = $result && add_field($table, $field);
        } else {
            $result = $result && change_field_default($table, $field);
            $result = $result && change_field_notnull($table, $field);
        }

    /// Define field decimals to be added to grade_items_history
        $table = new XMLDBTable('grade_items_history');
        $field = new XMLDBField('decimals');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null, 'display');

    /// Launch add field decimals
        if (!field_exists($table, $field)) {
            $result = $result && add_field($table, $field);
        }


    /// fix incorrect -1 default for grade_item->display
        execute_sql("UPDATE {$CFG->prefix}grade_items SET display=0 WHERE display=-1");

        upgrade_main_savepoint($result, 2007092803);
    }

/// migrade grade letters - we can not do this in normal grades upgrade becuase we need all course contexts
    if ($result && $oldversion < 2007092806) {

        $result = upgrade_18_letters();

    /// Define index contextidlowerboundary (not unique) to be added to grade_letters
        $table = new XMLDBTable('grade_letters');
        $index = new XMLDBIndex('contextid-lowerboundary');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('contextid', 'lowerboundary'));

    /// Launch add index contextidlowerboundary
        if (!index_exists($table, $index)) {
            $result = $result && add_index($table, $index);
        }

        upgrade_main_savepoint($result, 2007092806);
    }

    if ($result && $oldversion < 2007100100) {

    /// Define table cache_flags to be created
        $table = new XMLDBTable('cache_flags');

    /// Adding fields to table cache_flags
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('flagtype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('value', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('expiry', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table cache_flags
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /*
     * Note: mysql can not create indexes on text fields larger than 333 chars! 
     */

    /// Adding indexes to table cache_flags
        $table->addIndexInfo('flagtype', XMLDB_INDEX_NOTUNIQUE, array('flagtype'));
        $table->addIndexInfo('name', XMLDB_INDEX_NOTUNIQUE, array('name'));

    /// Launch create table for cache_flags
        if (!table_exists($table)) {
            $result = $result && create_table($table);
        }

        upgrade_main_savepoint($result, 2007100100);
    }


    if ($result && $oldversion < 2007100300) {
    /// MNET stuff for roaming theme
    /// Define field force_theme to be added to mnet_host
        $table = new XMLDBTable('mnet_host');
        $field = new XMLDBField('force_theme');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'last_log_id');

    /// Launch add field force_theme
        $result = $result && add_field($table, $field);

    /// Define field theme to be added to mnet_host
        $table = new XMLDBTable('mnet_host');
        $field = new XMLDBField('theme');
        $field->setAttributes(XMLDB_TYPE_CHAR, '100', null, null, null, null, null, null, 'force_theme');

    /// Launch add field theme
        $result = $result && add_field($table, $field);

        upgrade_main_savepoint($result, 2007100300);
    }

    if ($result && $oldversion < 2007100301) {

    /// Define table cache_flags to be created
        $table = new XMLDBTable('cache_flags');
        $index = new XMLDBIndex('typename');
        if (index_exists($table, $index)) {
            $result = $result && drop_index($table, $index);
        }
        
        $table = new XMLDBTable('cache_flags');
        $index = new XMLDBIndex('flagtype');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('flagtype'));
        if (!index_exists($table, $index)) {
            $result = $result && add_index($table, $index);
        }

        $table = new XMLDBTable('cache_flags');
        $index = new XMLDBIndex('name');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('name'));
        if (!index_exists($table, $index)) {
            $result = $result && add_index($table, $index);
        }

        upgrade_main_savepoint($result, 2007100301);
    }

    if ($result && $oldversion < 2007100303) {

    /// Changing nullability of field summary on table course to null
        $table = new XMLDBTable('course');
        $field = new XMLDBField('summary');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'idnumber');

    /// Launch change of nullability for field summary
        $result = $result && change_field_notnull($table, $field);

        upgrade_main_savepoint($result, 2007100303);
    }

    if ($result && $oldversion < 2007100500) {
    /// for dev sites - it is ok to do this repeatedly

    /// Changing nullability of field path on table context to null
        $table = new XMLDBTable('context');
        $field = new XMLDBField('path');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null, 'instanceid');

    /// Launch change of nullability for field path
        $result = $result && change_field_notnull($table, $field);

        upgrade_main_savepoint($result, 2007100500);
    }

    if ($result && $oldversion < 2007100700) {

    /// first drop existing tables - we do not need any data from there
        $table = new XMLDBTable('grade_import_values');
        if (table_exists($table)) {
            drop_table($table);
        }

        $table = new XMLDBTable('grade_import_newitem');
        if (table_exists($table)) {
            drop_table($table);
        }

    /// Define table grade_import_newitem to be created
        $table = new XMLDBTable('grade_import_newitem');

    /// Adding fields to table grade_import_newitem
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('itemname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('importcode', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('importer', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table grade_import_newitem
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('importer', XMLDB_KEY_FOREIGN, array('importer'), 'user', array('id'));

    /// Launch create table for grade_import_newitem
        $result = $result && create_table($table);


    /// Define table grade_import_values to be created
        $table = new XMLDBTable('grade_import_values');

    /// Adding fields to table grade_import_values
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('newgradeitem', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('finalgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
        $table->addFieldInfo('feedback', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('importcode', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('importer', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table grade_import_values
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
        $table->addKeyInfo('newgradeitem', XMLDB_KEY_FOREIGN, array('newgradeitem'), 'grade_import_newitem', array('id'));
        $table->addKeyInfo('importer', XMLDB_KEY_FOREIGN, array('importer'), 'user', array('id'));

    /// Launch create table for grade_import_values
        $result = $result && create_table($table);

        upgrade_main_savepoint($result, 2007100700);
    }

/// dropping context_rel table - not used anymore
    if ($result && $oldversion < 2007100800) {

    /// Define table context_rel to be dropped
        $table = new XMLDBTable('context_rel');

    /// Launch drop table for context_rel
        if (table_exists($table)) {
            drop_table($table);
        }

        upgrade_main_savepoint($result, 2007100800);
    }

/// Truncate the text_cahe table and add new index
    if ($result && $oldversion < 2007100802) {

    /// Truncate the cache_text table
        execute_sql("TRUNCATE TABLE {$CFG->prefix}cache_text", true);

    /// Define index timemodified (not unique) to be added to cache_text
        $table = new XMLDBTable('cache_text');
        $index = new XMLDBIndex('timemodified');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('timemodified'));

    /// Launch add index timemodified
        $result = $result && add_index($table, $index);

        upgrade_main_savepoint($result, 2007100802);
    }

/// newtable for gradebook settings per course
    if ($result && $oldversion < 2007100803) {

    /// Define table grade_settings to be created
        $table = new XMLDBTable('grade_settings');

    /// Adding fields to table grade_settings
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('value', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);

    /// Adding keys to table grade_settings
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));

    /// Adding indexes to table grade_settings
        $table->addIndexInfo('courseid-name', XMLDB_INDEX_UNIQUE, array('courseid', 'name'));

    /// Launch create table for grade_settings
        $result = $result && create_table($table);

        upgrade_main_savepoint($result, 2007100803);
    }

/// cleanup in user_lastaccess
    if ($result && $oldversion < 2007100902) {
        $sql = "DELETE
                  FROM {$CFG->prefix}user_lastaccess
                 WHERE NOT EXISTS (SELECT 'x'
                                    FROM {$CFG->prefix}course c
                                   WHERE c.id = {$CFG->prefix}user_lastaccess.courseid)"; 
        execute_sql($sql);

        upgrade_main_savepoint($result, 2007100902);
    }

/// drop old gradebook tables
    if ($result && $oldversion < 2007100903) {
        $tables = array('grade_category',
                        'grade_item',
                        'grade_letter',
                        'grade_preferences',
                        'grade_exceptions');

        foreach ($tables as $table) {
            $table = new XMLDBTable($table);
            if (table_exists($table)) {
                drop_table($table);
            }
        }

        upgrade_main_savepoint($result, 2007100903);
    }
    
    if ($result && $oldversion < 2007101500 && !file_exists($CFG->dataroot . '/user')) {
        // Get list of users by browsing moodledata/user
        $oldusersdir = $CFG->dataroot . '/users';
        $folders = get_directory_list($oldusersdir, '', false, true, false);
        
        foreach ($folders as $userid) {
            $olddir = $oldusersdir . '/' . $userid;
            $files = get_directory_list($olddir);
            
            if (empty($files)) {
                continue;
            }

            // Create new user directory
            if (!$newdir = make_user_directory($userid)) {
                // some weird directory - do not stop the upgrade, just ignore it
                continue;
            }

            // Move contents of old directory to new one
            if (file_exists($olddir) && file_exists($newdir)) {
                foreach ($files as $file) {
                    copy($olddir . '/' . $file, $newdir . '/' . $file);
                }
            } else {
                notify("Could not move the contents of $olddir into $newdir!");
                $result = false;
                break;
            }
        }

        // Leave a README in old users directory
        $readmefilename = $oldusersdir . '/README.txt';
        if ($handle = fopen($readmefilename, 'w+b')) {
            if (!fwrite($handle, get_string('olduserdirectory'))) {
                // Could not write to the readme file. No cause for huge concern 
                notify("Could not write to the README.txt file in $readmefilename.");
            }
            fclose($handle);
        } else {
            // Could not create the readme file. No cause for huge concern
            notify("Could not create the README.txt file in $readmefilename.");
        }
    }    

    if ($result && $oldversion < 2007101502) {

    /// try to remove duplicate entries
    
        $SQL = "SELECT userid, itemid, COUNT(*)
               FROM {$CFG->prefix}grade_grades
               GROUP BY userid, itemid
               HAVING COUNT( * ) >1";
        // duplicates found
        
        if ($rs = get_recordset_sql($SQL)) {
            if ($rs && $rs->RecordCount() > 0) {
                while ($dup = rs_fetch_next_record($rs)) {
                    if ($thisdups = get_records_sql("SELECT id FROM {$CFG->prefix}grade_grades 
                                                    WHERE itemid = $dup->itemid AND userid = $dup->userid
                                                    ORDER BY timemodified DESC")) {

                        $processed = 0; // keep the first one
                        foreach ($thisdups as $thisdup) {
                            if ($processed) {
                                // remove the duplicates
                                delete_records('grade_grades', 'id', $thisdup->id);
                            }
                            $processed++;
                        }
                    }
                }
                rs_close($rs);
            }
        }

    /// Define key userid-itemid (unique) to be added to grade_grades
        $table = new XMLDBTable('grade_grades');
        $key = new XMLDBKey('userid-itemid');
        $key->setAttributes(XMLDB_KEY_UNIQUE, array('userid', 'itemid'));

    /// Launch add key userid-itemid
        $result = $result && add_key($table, $key);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101502);
    }

    if ($result && $oldversion < 2007101505) {

    /// Changing precision of field dst_time on table timezone to (6)
        $table = new XMLDBTable('timezone');
        $field = new XMLDBField('dst_time');
        $field->setAttributes(XMLDB_TYPE_CHAR, '6', null, XMLDB_NOTNULL, null, null, null, '00:00', 'dst_skipweeks');

    /// Launch change of precision for field dst_time
        $result = $result && change_field_precision($table, $field);

    /// Changing precision of field std_time on table timezone to (6)
        $table = new XMLDBTable('timezone');
        $field = new XMLDBField('std_time');
        $field->setAttributes(XMLDB_TYPE_CHAR, '6', null, XMLDB_NOTNULL, null, null, null, '00:00', 'std_skipweeks');

    /// Launch change of precision for field std_time
        $result = $result && change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101505);
    }

    if ($result && $oldversion < 2007101506) {

    /// CONTEXT_PERSONAL was never implemented - removing
        $sql = "DELETE
                  FROM {$CFG->prefix}context
                 WHERE contextlevel=20";
 
        execute_sql($sql);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101506);
    }

    if ($result && $oldversion < 2007101507) {
        $db->debug = false;
        require_once($CFG->dirroot.'/course/lib.php');
        notify('Started rebuilding of course cache...', 'notifysuccess');
        rebuild_course_cache();  // Rebuild course cache - new group related fields there
        notify('...finished rebuilding of course cache.', 'notifysuccess');
        $db->debug = true;
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101507);
    }

    if ($result && $oldversion < 2007101508) {
        $db->debug = false;
        notify('Updating country list according to recent official ISO listing...', 'notifysuccess');
        // re-assign users to valid countries
        set_field('user', 'country', 'CD', 'country', 'ZR'); // Zaire is now Congo Democratique
        set_field('user', 'country', 'TL', 'country', 'TP'); // Timor has changed
        set_field('user', 'country', 'FR', 'country', 'FX'); // France metropolitaine doesn't exist
        set_field('user', 'country', 'RS', 'country', 'KO'); // Kosovo is part of Serbia, "under the auspices of the United Nations, pursuant to UN Security Council Resolution 1244 of 10 June 1999."
        set_field('user', 'country', 'GB', 'country', 'WA'); // Wales is part of UK (ie Great Britain)
        set_field('user', 'country', 'RS', 'country', 'CS'); // Re-assign Serbia-Montenegro to Serbia.  This is arbitrary, but there is no way to make an automatic decision on this.
        notify('...update complete. Remember to update the language pack to get the most recent country names defitions and codes.  This is specialy important for sites with users from Congo (now CD), Timor (now TL), Kosovo (now RS), Wales (now GB), Serbia (RS) and Montenegro (ME).  Users based in Montenegro (ME) will need to manually update their profile.', 'notifysuccess');
        $db->debug = true;
        upgrade_main_savepoint($result, 2007101508);
    }

    if ($result && $oldversion < 2007101508.01) {
// add forgotten table
    /// Define table scale_history to be created
        $table = new XMLDBTable('scale_history');

    /// Adding fields to table scale_history
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('action', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('oldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('source', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('loggeduser', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('scale', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('description', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table scale_history
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('oldid', XMLDB_KEY_FOREIGN, array('oldid'), 'scale', array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->addKeyInfo('loggeduser', XMLDB_KEY_FOREIGN, array('loggeduser'), 'user', array('id'));

    /// Adding indexes to table scale_history
        $table->addIndexInfo('action', XMLDB_INDEX_NOTUNIQUE, array('action'));

        if ($result and !table_exists($table)) {
        /// Launch create table for scale_history
            $result = $result && create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101508.01);
    }


    if ($result && $oldversion < 2007101508.02) {
        // upgade totals, no big deal if it fails
        require_once($CFG->libdir.'/statslib.php');
        stats_upgrade_totals();

        if (isset($CFG->loglifetime) and $CFG->loglifetime == 30) {
            set_config('loglifetime', 35); // we need more than 31 days for monthly stats!
        }

        notify('Upgrading log table indexes, this may take a long time, please be patient.', 'notifysuccess');

    /// Define index time-course-module-action (not unique) to be dropped form log
        $table = new XMLDBTable('log');
        $index = new XMLDBIndex('time-course-module-action');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('time', 'course', 'module', 'action'));

    /// Launch drop index time-course-module-action
        if (index_exists($table, $index)) {
            $result = drop_index($table, $index) && $result;
        }

    /// Define index userid (not unique) to be dropped form log
        $table = new XMLDBTable('log');
        $index = new XMLDBIndex('userid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('userid'));

    /// Launch drop index userid
        if (index_exists($table, $index)) {
            $result = drop_index($table, $index) && $result;
        }

    /// Define index info (not unique) to be dropped form log
        $table = new XMLDBTable('log');
        $index = new XMLDBIndex('info');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('info'));

    /// Launch drop index info
        if (index_exists($table, $index)) {
            $result = drop_index($table, $index) && $result;
        }

    /// Define index time (not unique) to be added to log
        $table = new XMLDBTable('log');
        $index = new XMLDBIndex('time');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('time'));

    /// Launch add index time
        if (!index_exists($table, $index)) {
            $result = add_index($table, $index) && $result;
        }

    /// Define index action (not unique) to be added to log
        $table = new XMLDBTable('log');
        $index = new XMLDBIndex('action');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('action'));

    /// Launch add index action
        if (!index_exists($table, $index)) {
            $result = add_index($table, $index) && $result;
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101508.02);
    }

    if ($result && $oldversion < 2007101508.03) {

    /// Define index course-userid (not unique) to be dropped form log
        $table = new XMLDBTable('log');
        $index = new XMLDBIndex('course-userid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('course', 'userid'));

    /// Launch drop index course-userid
        if (index_exists($table, $index)) {
            $result = $result && drop_index($table, $index);
        }

    /// Define index userid-course (not unique) to be added to log
        $table = new XMLDBTable('log');
        $index = new XMLDBIndex('userid-course');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('userid', 'course'));

    /// Launch add index userid-course
        if (!index_exists($table, $index)) {
            $result = $result && add_index($table, $index);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101508.03);
    }

    if ($result && $oldversion < 2007101508.04) {
        set_field('tag_instance', 'itemtype', 'post', 'itemtype', 'blog'); 
        upgrade_main_savepoint($result, 2007101508.04);
    }

    if ($result && $oldversion < 2007101508.05) {

    /// Define index cmid (not unique) to be added to log
        $table = new XMLDBTable('log');
        $index = new XMLDBIndex('cmid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('cmid'));

    /// Launch add index cmid
        if (!index_exists($table, $index)) {
            $result = $result && add_index($table, $index);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101508.05);
    }

    if ($result && $oldversion < 2007101508.06) {

    /// Define index groupid-courseid-visible-userid (not unique) to be added to event
        $table = new XMLDBTable('event');
        $index = new XMLDBIndex('groupid-courseid-visible-userid');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('groupid', 'courseid', 'visible', 'userid'));

    /// Launch add index groupid-courseid-visible-userid
        if (!index_exists($table, $index)) {
            $result = $result && add_index($table, $index);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101508.06);
    }

    if ($result && $oldversion < 2007101508.07) {

    /// Define table webdav_locks to be created
        $table = new XMLDBTable('webdav_locks');

    /// Adding fields to table webdav_locks
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('token', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('path', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('expiry', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('recursive', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('exclusivelock', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('created', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('modified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('owner', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);

    /// Adding keys to table webdav_locks
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('token', XMLDB_KEY_UNIQUE, array('token'));

    /// Adding indexes to table webdav_locks
        $table->addIndexInfo('path', XMLDB_INDEX_NOTUNIQUE, array('path'));
        $table->addIndexInfo('expiry', XMLDB_INDEX_NOTUNIQUE, array('expiry'));

    /// Launch create table for webdav_locks
        $result = $result && create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101508.07);
    }

    if ($result && $oldversion < 2007101508.08) {    // MDL-13676

    /// Define field name to be added to role_names
        $table = new XMLDBTable('role_names');
        $field = new XMLDBField('name');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null, 'text');

    /// Launch add field name
        $result = $result && add_field($table, $field);
 
    /// Copy data from old field to new field
        $result = $result && execute_sql('UPDATE '.$CFG->prefix.'role_names SET name = text');

    /// Define field text to be dropped from role_names
        $table = new XMLDBTable('role_names');
        $field = new XMLDBField('text');

    /// Launch drop field text
        $result = $result && drop_field($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101508.08);
    }

    if ($result && $oldversion < 2007101509) {
        // force full regrading
        set_field('grade_items', 'needsupdate', 1, 'needsupdate', 0);
    }

    if ($result && $oldversion < 2007101510) {
    /// Fix minor problem caused by MDL-5482.
        require_once($CFG->dirroot . '/question/upgrade.php');
        $result = $result && question_fix_random_question_parents();
        upgrade_main_savepoint($result, 2007101510);
    }

    if ($result && $oldversion < 2007101511) {
        // if guest role used as default user role unset it and force admin to choose new setting
        if (!empty($CFG->defaultuserroleid)) {
            if ($role = get_record('role', 'id', $CFG->defaultuserroleid)) {
                if ($guestroles = get_roles_with_capability('moodle/legacy:guest', CAP_ALLOW)) {
                    if (isset($guestroles[$role->id])) {
                        set_config('defaultuserroleid', null);
                        notify('Guest role removed from "Default role for all users" setting, please select another role.', 'notifysuccess');
                    }
                }
            } else {
                set_config('defaultuserroleid', null);
            }
        }
    }

    if ($result && $oldversion < 2007101512) {
        notify('Increasing size of user idnumber field, this may take a while...', 'notifysuccess');

    /// Under MySQL and Postgres... detect old NULL contents and change them by correct empty string. MDL-14859
        if ($CFG->dbfamily == 'mysql' || $CFG->dbfamily == 'postgres') {
            execute_sql("UPDATE {$CFG->prefix}user SET idnumber = '' WHERE idnumber IS NULL", true);
        }

    /// Define index idnumber (not unique) to be dropped form user
        $table = new XMLDBTable('user');
        $index = new XMLDBIndex('idnumber');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('idnumber'));

    /// Launch drop index idnumber
        if (index_exists($table, $index)) {
            $result = $result && drop_index($table, $index);
        }

    /// Changing precision of field idnumber on table user to (255)
        $table = new XMLDBTable('user');
        $field = new XMLDBField('idnumber');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null, 'password');

    /// Launch change of precision for field idnumber
        $result = $result && change_field_precision($table, $field);

    /// Launch add index idnumber again
        $index = new XMLDBIndex('idnumber');
        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('idnumber'));
        $result = $result && add_index($table, $index);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101512);
    }

    if ($result && $oldversion < 2007101513) {
        $log_action = new stdClass();
        $log_action->module = 'course';
        $log_action->action = 'unenrol';
        $log_action->mtable = 'course';
        $log_action->field  = 'fullname';
        if (!record_exists("log_display", "action", "unenrol",
                    "module", "course")){
            $result  = $result && insert_record('log_display', $log_action);
        }
        upgrade_main_savepoint($result, 2007101513);
    }

    if ($result && $oldversion < 2007101514) {
        $table = new XMLDBTable('mnet_enrol_course');
        $field = new XMLDBField('sortorder');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', true, true, null, false, false, 0);
        $result = change_field_precision($table, $field);
        upgrade_main_savepoint($result, 2007101514);
    }

    if ($result && $oldversion < 2007101515) {
        $result = delete_records_select('role_names', sql_isempty('role_names', 'name', false, false));
        upgrade_main_savepoint($result, 2007101515);
    }

    if ($result && $oldversion < 2007101517) {
        if (isset($CFG->defaultuserroleid) and isset($CFG->guestroleid) and $CFG->defaultuserroleid == $CFG->guestroleid) {
            // guest can not be selected in defaultuserroleid!
            unset_config('defaultuserroleid');
        }
        upgrade_main_savepoint($result, 2007101517);
    }

    if ($result && $oldversion < 2007101526) {

    /// Changing the default of field lang on table user to en_utf8
        $table = new XMLDBTable('user');
        $field = new XMLDBField('lang');
        $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, 'en_utf8', 'country');

    /// Launch change of default for field lang
        $result = $result && change_field_default($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101526);
    }

    if ($result && $oldversion < 2007101527) {
        if (!get_config(NULL, 'statsruntimedays')) {
            set_config('statsruntimedays', '31');
        }
    }

    /// For MDL-17501. Ensure that any role that has moodle/course:update also
    /// has moodle/course:visibility.
    if ($result && $oldversion < 2007101532.10) {
        if (!empty($CFG->rolesactive)) { // In case we are upgrading from Moodle 1.6.
        /// Get the roles with 'moodle/course:update'.
            $systemcontext = get_context_instance(CONTEXT_SYSTEM);
            $roles = get_roles_with_capability('moodle/course:update', CAP_ALLOW, $systemcontext);

        /// Give those roles 'moodle/course:visibility'.
            foreach ($roles as $role) {
                assign_capability('moodle/course:visibility', CAP_ALLOW, $role->id, $systemcontext->id);
            }

        /// Force all sessions to refresh access data.
            mark_context_dirty($systemcontext->path);
        }

        /// Main savepoint reached
            upgrade_main_savepoint($result, 2007101532.10);
    }

    if ($result && $oldversion < 2007101542) {
        if (empty($CFG->hiddenuserfields)) {
            set_config('hiddenuserfields','firstaccess');
        } else {
            if (strpos($CFG->hiddenuserfields, 'firstaccess') === false) { //firstaccess should not already be listed but just in case
                set_config('hiddenuserfields',$CFG->hiddenuserfields.',firstaccess');
            }
        }
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101542);
    }

    if ($result && $oldversion < 2007101545.01) {
        require_once("$CFG->dirroot/filter/tex/lib.php");
        filter_tex_updatedcallback(null);
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101545.01);
    }

    if ($result && $oldversion < 2007101546.02) {
        if (empty($CFG->gradebook_latest195_upgrade)) {
            require_once($CFG->libdir.'/gradelib.php'); // we need constants only
            // reset current coef for simple mean items - it may contain some rubbish ;-)
            $sql = "UPDATE {$CFG->prefix}grade_items
                       SET aggregationcoef = 0
                     WHERE categoryid IN (SELECT gc.id
                                            FROM {$CFG->prefix}grade_categories gc
                                           WHERE gc.aggregation = ".GRADE_AGGREGATE_WEIGHTED_MEAN2.")";
            $result = execute_sql($sql);
        } else {
            // direct upgrade from 1.8.x - no need to reset coef, because it is already ok
            unset_config('gradebook_latest195_upgrade');
        }

        upgrade_main_savepoint($result, 2007101546.02);
    }

    if ($result && $oldversion < 2007101546.03) {
    /// Deleting orphaned messages from deleted users.
        require_once($CFG->dirroot.'/message/lib.php');
    /// Detect deleted users with messages sent(useridfrom) and not read
        if ($deletedusers = get_records_sql("SELECT DISTINCT u.id
                                           FROM {$CFG->prefix}user u
                                           JOIN {$CFG->prefix}message m ON m.useridfrom = u.id
                                          WHERE u.deleted = 1")) {
            foreach ($deletedusers as $deleteduser) {
                message_move_userfrom_unread2read($deleteduser->id); // move messages
            }
        }
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2007101546.03);
    }

    if ($result && $oldversion < 2007101546.05) {
        // force full regrading - the max grade for sum aggregation was not correct when scales involved,
        //                        extra credit grade is not dropped anymore in aggregations if drop low or keep high specified
        //                        sum aggragetion respects drop low and keep high when calculation max value
        set_field('grade_items', 'needsupdate', 1, 'needsupdate', 0);
    }

    if ($result && $oldversion < 2007101546.06) {
        unset_config('grade_report_showgroups');
        upgrade_main_savepoint($result, 2007101546.06);
    }

    if ($result && $oldversion < 2007101547) {
        // Let's check the status of mandatory mnet_host records, fixing them
        // and moving "orphan" users to default localhost record. MDL-16879
        notify('Fixing mnet records, this may take a while...', 'notifysuccess');
        $db->debug = false; // Can output too much. Disabling
        upgrade_fix_incorrect_mnethostids();
        $db->debug = true; // Restoring debug level
        upgrade_main_savepoint($result, 2007101547);
    }

    if ($result && $oldversion < 2007101551){
        //insert new record for log_display table
        //used to record tag update.
        if (!record_exists("log_display", "action", "update",
                    "module", "tag")){
            $log_action = new stdClass();
            $log_action->module = 'tag';
            $log_action->action = 'update';
            $log_action->mtable = 'tag';
            $log_action->field  = 'name';

            $result  = $result && insert_record('log_display', $log_action);
        }
        upgrade_main_savepoint($result, 2007101551);
    }

    if ($result && $oldversion < 2007101561.01) {
        // As part of security changes password policy will now be enabled by default.
        // If it has not already been enabled then we will enable it... Admins will still
        // be able to switch it off after this upgrade
        if (record_exists('config', 'name', 'passwordpolicy', 'value', 0)) {
            unset_config('passwordpolicy');
        }

        $message = get_string('upgrade197notice', 'admin');
        if (empty($CFG->passwordmainsalt)) {
            $docspath = $CFG->docroot.'/'.str_replace('_utf8', '', current_language()).'/report/security/report_security_check_passwordsaltmain';
            $message .= "\n".get_string('upgrade197salt', 'admin', $docspath);
        }
        notify($message, 'notifysuccess');

        unset($message);

        upgrade_main_savepoint($result, 2007101561.01);
    }

    if ($result && $oldversion < 2007101561.02) {
        $messagesubject = s($SITE->shortname).': '.get_string('upgrade197noticesubject', 'admin');
        $message  = '<p>'.s($SITE->fullname).' ('.s($CFG->wwwroot).'):</p>'.get_string('upgrade197notice', 'admin');
        if (empty($CFG->passwordmainsalt)) {
            $docspath = $CFG->docroot.'/'.str_replace('_utf8', '', current_language()).'/report/security/report_security_check_passwordsaltmain';
            $message .= "\n".get_string('upgrade197salt', 'admin', $docspath);
        }

        // Force administrators to change password on next login
        $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.picture, u.imagealt, u.email, u.password, u.mailformat
              FROM {$CFG->prefix}role_capabilities rc
              JOIN {$CFG->prefix}role_assignments ra ON (ra.contextid = rc.contextid AND ra.roleid = rc.roleid)
              JOIN {$CFG->prefix}user u ON u.id = ra.userid
             WHERE rc.capability = 'moodle/site:doanything'
                   AND rc.permission = ".CAP_ALLOW."
                   AND u.deleted = 0
                   AND rc.contextid = ".$systemcontext->id." AND (u.auth='manual' OR u.auth='email')";

        $adminusers = get_records_sql($sql);
        foreach ($adminusers as $adminuser) {
            if ($preference = get_record('user_preferences', 'userid', $adminuser->id, 'name', 'auth_forcepasswordchange')) {
                if ($preference->value == '1') {
                    continue;
                }
                set_field('user_preferences', 'value', '1', 'id', $preference->id);
            } else {
                $preference = new stdClass;
                $preference->userid = $adminuser->id;
                $preference->name   = 'auth_forcepasswordchange';
                $preference->value  = '1';
                insert_record('user_preferences', $preference);
            }
            $adminuser->maildisplay = 0; // do not use return email to self, it might actually help emails to get through and prevents notices
            // Message them with the notice about upgrading
            email_to_user($adminuser, $adminuser, $messagesubject, html_to_text($message), $message);
        }

        unset($adminusers);
        unset($preference);
        unset($message);
        unset($messagesubject);

        upgrade_main_savepoint($result, 2007101561.02);
    }

    if ($result && $oldversion < 2007101563.02) {
        // this block tries to undo incorrect forcing of new passwords for admins that have no
        // way to change passwords MDL-20933
        $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.picture, u.imagealt, u.email, u.password
                  FROM {$CFG->prefix}role_capabilities rc
                  JOIN {$CFG->prefix}role_assignments ra ON (ra.contextid = rc.contextid AND ra.roleid = rc.roleid)
                  JOIN {$CFG->prefix}user u ON u.id = ra.userid
                 WHERE rc.capability = 'moodle/site:doanything'
                       AND rc.permission = ".CAP_ALLOW."
                       AND u.deleted = 0
                       AND rc.contextid = ".$systemcontext->id." AND u.auth<>'manual' AND u.auth<>'email'";

        if ($adminusers = get_records_sql($sql)) {
            foreach ($adminusers as $adminuser) {
                delete_records('user_preferences', 'userid', $adminuser->id, 'name', 'auth_forcepasswordchange');
            }
        }
        unset($adminusers);

        upgrade_main_savepoint($result, 2007101563.02);
    }

    if ($result && $oldversion < 2007101563.03) {
        // NOTE: this is quite hacky, but anyway it should work fine in 1.9,
        //       in 2.0 we should always use plugin upgrade code for things like this

        $authsavailable = get_list_of_plugins('auth');
        foreach($authsavailable as $authname) {
            if (!$auth = get_auth_plugin($authname)) {
                continue;
            }
            if ($auth->prevent_local_passwords()) {
                execute_sql("UPDATE {$CFG->prefix}user SET password='not cached' WHERE auth='$authname'");
            }
        }

        upgrade_main_savepoint($result, 2007101563.03);
    }

    if ($result && $oldversion < 2007101571.01) {
        // MDL-21011 bring down course sort orders away from maximum values
        $sql = "SELECT id, category, sortorder from {$CFG->prefix}course
                ORDER BY sortorder ASC;";
        if ($courses = get_recordset_sql($sql)) {
            $i=1000;
            $old_category = 0;
            while ($course = rs_fetch_next_record($courses)) {
                if($course->category!=$old_category) {
                    //increase i to put a gap between courses in different categories
                    //don't think we need to but they had one before
                    $i += 1000;
                    $old_category = $course->category;
                }
                set_field('course', 'sortorder', $i++, 'id', $course->id);
            }
            rs_close($courses);
        }
        unset($courses);

        upgrade_main_savepoint($result, 2007101571.01);
    }

    if ($result && $oldversion < 2007101571.02) {
        upgrade_fix_incorrect_mnethostids();
        upgrade_main_savepoint($result, 2007101571.02);
    }

    /// MDL-17863. Increase the portno column length on mnet_host to handle any port number
    if ($result && $oldversion < 2007101571.03) {
        $table = new XMLDBTable('mnet_host');
        $field = new XMLDBField('portno');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '5', true, true, null, false, false, 0);
        $result = change_field_precision($table, $field);
        upgrade_main_savepoint($result, 2007101571.03);
    }

    // MDL-21407. Trim leading spaces from default tex latexpreamble causing problems under some confs
    if ($result && $oldversion < 2007101571.04) {
        if ($preamble = $CFG->filter_tex_latexpreamble) {
            $preamble = preg_replace('/^ +/m', '', $preamble);
            set_config('filter_tex_latexpreamble', $preamble);
        }
        upgrade_main_savepoint($result, 2007101571.04);
    }

    return $result;
}


?>
