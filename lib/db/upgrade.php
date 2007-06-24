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

    global $CFG, $THEME, $USER, $db;

    $result = true;

    if ($oldversion < 2006100401) {
        /// Only for those tracking Moodle 1.7 dev, others will have these dropped in moodle_install_roles()
        if (!empty($CFG->rolesactive)) {
            drop_table(new XMLDBTable('user_students'));
            drop_table(new XMLDBTable('user_teachers'));
            drop_table(new XMLDBTable('user_coursecreators'));
            drop_table(new XMLDBTable('user_admins'));
        }
    }

    if ($oldversion < 2006100601) {         /// Disable the exercise module because it's unmaintained
        if ($module = get_record('modules', 'name', 'exercise')) {
            if ($module->visible) {
                // Hide/disable the module entry
                set_field('modules', 'visible', '0', 'id', $module->id); 
                // Save existing visible state for all activities
                set_field('course_modules', 'visibleold', '1', 'visible' ,'1', 'module', $module->id);
                set_field('course_modules', 'visibleold', '0', 'visible' ,'0', 'module', $module->id);
                // Hide all activities
                set_field('course_modules', 'visible', '0', 'module', $module->id);
    
                require_once($CFG->dirroot.'/course/lib.php');
                rebuild_course_cache();  // Rebuld cache for all modules because they might have changed
            }
        }
    }

    if ($oldversion < 2006101001) {         /// Disable the LAMS module by default (if it is installed)
        if (count_records('modules', 'name', 'lams') && !count_records('lams')) {
            set_field('modules', 'visible', 0, 'name', 'lams');  // Disable it by default
        }
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
    }
    
    if ($result && $oldversion < 2006112000) {

    /// Define field attachment to be added to post
        $table = new XMLDBTable('post');
        $field = new XMLDBField('attachment');
        $field->setAttributes(XMLDB_TYPE_CHAR, '100', null, null, null, null, null, null, 'format');

    /// Launch add field attachment
        $result = $result && add_field($table, $field);
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
    }

    if ($oldversion < 2006120300) {    /// Delete guest course section settings
        // following code can be executed repeatedly, such as when upgrading from 1.7.x - it is ok
        if ($guest = get_record('user', 'username', 'guest')) {
            execute_sql("DELETE FROM {$CFG->prefix}course_display where userid=$guest->id", true);
        }
    }

    if ($oldversion < 2006120400) {    /// Remove secureforms config setting
        execute_sql("DELETE FROM {$CFG->prefix}config where name='secureforms'", true);
    }
    
    if ($oldversion < 2006120700) { // add moodle/user:viewdetails to all roles!
        if ($roles = get_records('role')) {
            $context = get_context_instance(CONTEXT_SYSTEM);
            foreach ($roles as $roleid=>$role) {
                assign_capability('moodle/user:viewdetails', CAP_ALLOW, $roleid, $context->id);
            }
        }
    }

    // Move the auth plugin settings into the config_plugin table
    if ($oldversion < 2007010300) {
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
    }

    if ($oldversion < 2007010301) {
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
    }

    if ($result && $oldversion < 2007011200) {

    /// Define table context_rel to be created
        $table = new XMLDBTable('context_rel');

    /// Adding fields to table context_rel
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('c1', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null);
        $table->addFieldInfo('c2', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null);

    /// Adding keys to table context_rel
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('c1', XMLDB_KEY_FOREIGN, array('c1'), 'context', array('id'));
        $table->addKeyInfo('c2', XMLDB_KEY_FOREIGN, array('c2'), 'context', array('id'));
        $table->addKeyInfo('c1c2', XMLDB_KEY_UNIQUE, array('c1', 'c2'));

    /// Launch create table for context_rel
        $result = $result && create_table($table);
        
        /// code here to fill the context_rel table
        /// use get record set to iterate slower
        build_context_rel();
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
    }

    if ($result && $oldversion < 2007012400) {

    /// Rename field access on table mnet_sso_access_control to accessctrl
        $table = new XMLDBTable('mnet_sso_access_control');
        $field = new XMLDBField('access');
        $field->setAttributes(XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, 'allow', 'mnet_host_id');

    /// Launch rename field accessctrl
        $result = $result && rename_field($table, $field, 'accessctrl');
    }

    if ($result && $oldversion < 2007012500) {
        execute_sql("DELETE FROM {$CFG->prefix}user WHERE username='changeme'", true);
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
    }

    if ($result && $oldversion < 2007021501) {
    /// delete removed setting from config
        unset_config('tabselectedtofront');
    }


    if ($result && $oldversion < 2007032200) {

    /// Define table role_sortorder to be created
        $table = new XMLDBTable('role_sortorder');

    /// Adding fields to table role_sortorder
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
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
    }
    
    if ($result && $oldversion < 2007041100) {

    /// Define field idnumber to be added to course_modules
        $table = new XMLDBTable('course_modules');
        $field = new XMLDBField('idnumber');
        $field->setAttributes(XMLDB_TYPE_CHAR, '100', null, null, null, null, null, null, 'section');
    
    /// Launch add field idnumber
        $result = $result && add_field($table, $field);
    
    /// Define index idnumber (unique) to be added to course_modules
        $table = new XMLDBTable('course_modules');
        $index = new XMLDBIndex('idnumber');
        $index->setAttributes(XMLDB_INDEX_UNIQUE, array('idnumber'));

    /// Launch add index idnumber
        $result = $result && add_index($table, $index);

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
                    $options = explode("\n", $this->field->param1);
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
        
    }
    
    /// adding new gradebook tables
    if ($result && $oldversion < 2007041800) {

    /// Define table events_handlers to be created
        $table = new XMLDBTable('events_handlers');

    /// Adding fields to table events_handlers
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
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
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
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
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
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

    }
    
    if ($result && $oldversion < 2007042400) {

    /// Define table grade_items to be created
        $table = new XMLDBTable('grade_items');

    /// Adding fields to table grade_items
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
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
        $table->addFieldInfo('gradetype', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '100');
        $table->addFieldInfo('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('outcomeid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null);
        $table->addFieldInfo('gradepass', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('multfactor', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '1.0');
        $table->addFieldInfo('plusfactor', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('locked', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('needsupdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table grade_items
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->addKeyInfo('categoryid', XMLDB_KEY_FOREIGN, array('categoryid'), 'grade_categories', array('id'));
        $table->addKeyInfo('scaleid', XMLDB_KEY_FOREIGN, array('scaleid'), 'scale', array('id'));
        $table->addKeyInfo('outcomeid', XMLDB_KEY_FOREIGN, array('outcomeid'), 'grade_outcomes', array('id'));

    /// Launch create table for grade_items
        $result = $result && create_table($table);
        
    /// Define table grade_categories to be created
        $table = new XMLDBTable('grade_categories');

    /// Adding fields to table grade_categories
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('categoryid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('aggregation', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('keephigh', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('droplow', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('hidden', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');

    /// Adding keys to table grade_categories
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('courseid', XMLDB_KEY_FOREIGN, array('courseid'), 'course', array('id'));
        $table->addKeyInfo('categoryid', XMLDB_KEY_FOREIGN, array('categoryid'), 'grade_categories', array('id'));

    /// Launch create table for grade_categories
        $result = $result && create_table($table);
        

    /// Define table grade_grades to be created
        $table = new XMLDBTable('grade_grades');

    /// Adding fields to table grade_grades
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
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
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table grade_grades
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
        $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->addKeyInfo('rawscaleid', XMLDB_KEY_FOREIGN, array('rawscaleid'), 'scale', array('id'));
        $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

    /// Launch create table for grade_grades
        $result = $result && create_table($table);
        

    /// Define table grade_grades_text to be created
        $table = new XMLDBTable('grade_grades_text');

    /// Adding fields to table grade_grades_text
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('information', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('informationformat', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('feedback', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
        $table->addFieldInfo('feedbackformat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null);
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null);

    /// Adding keys to table grade_grades_text
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));
        $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_item', array('id'));
        $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Launch create table for grade_grades_text
        $result = $result && create_table($table);

        
   /// Define table grade_outcomes to be created
        $table = new XMLDBTable('grade_outcomes');

    /// Adding fields to table grade_outcomes
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('shortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('fullname', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
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
        
    /// Define table grade_history to be created
        $table = new XMLDBTable('grade_history');

    /// Adding fields to table grade_history
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('oldgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
        $table->addFieldInfo('newgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
        $table->addFieldInfo('note', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);
        $table->addFieldInfo('howmodified', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, 'manual');
        $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table grade_history
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
        $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

    /// Launch create table for grade_history
        $result = $result && create_table($table);        
    }

    if ($result && $oldversion < 2007042600) {

        /// Define field timecreated to be added to grade_categories
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('timecreated');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null, 'hidden');

        /// Launch add field timecreated
        $result = $result && add_field($table, $field);

        /// Define field timemodified to be added to grade_categories
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('timemodified');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null, 'timecreated');

        /// Launch add field timemodified
        $result = $result && add_field($table, $field);
    }

    if ($result && $oldversion < 2007042701) {

    /// Define key categoryid (foreign) to be dropped form grade_categories
        $table = new XMLDBTable('grade_categories');
        $key = new XMLDBKey('categoryid');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('categoryid'), 'grade_categories', array('id'));

    /// Launch drop key categoryid
        $result = $result && drop_key($table, $key);   

    /// Rename field categoryid on table grade_categories to parent
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('categoryid');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'courseid');

    /// Launch rename field categoryid
        $result = $result && rename_field($table, $field, 'parent');

    /// Define key parent (foreign) to be added to grade_categories
        $table = new XMLDBTable('grade_categories');
        $key = new XMLDBKey('parent');
        $key->setAttributes(XMLDB_KEY_FOREIGN, array('parent'), 'grade_categories', array('id'));

    /// Launch add key parent
        $result = $result && add_key($table, $key);
    
    /// Define field depth to be added to grade_categories
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('depth');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'parent');

    /// Launch add field depth
        $result = $result && add_field($table, $field);
        
    /// Define field path to be added to grade_categories
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('path');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null, 'depth');

    /// Launch add field path
        $result = $result && add_field($table, $field);
     
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
    }

    if ($result && $oldversion < 2007050201) {

    /// Define field theme to be added to course_categories
        $table = new XMLDBTable('course_categories');
        $field = new XMLDBField('theme');
        $field->setAttributes(XMLDB_TYPE_CHAR, '50', null, null, null, null, null, null, 'path');

    /// Launch add field theme
        $result = $result && add_field($table, $field);
    }
    if ($result && $oldversion < 2007050300) {

    // Define field childrentype to be added to grade_categories
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('childrentype');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'parent');
    
    // Launch add field childrentype
        $result = $result && add_field($table, $field);
    }
    if ($result && $oldversion < 2007050301) {

    /// Define field parent to be dropped from grade_categories
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('childrentype');
    
    /// Launch drop field parent
        $result = $result && drop_field($table, $field);
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
    }

    if ($result && $oldversion < 2007051101) {
        if (empty($CFG->enablegroupings)) {
            // delete all groupings - they do not work yet :-(
            // while keeping all existing groups
            require_once("$CFG->dirroot/group/db/upgrade.php");
            undo_groupings();
        }
    }

    if ($result && $oldversion < 2007051801) {
        //  Get the role id of the "Auth. User" role and check if the default role id is different
        $userrole = get_record( 'role', 'shortname', 'user' );
        $defaultroleid = $CFG->defaultuserroleid;

        if( $defaultroleid != $userrole->id ) {
            //  Add in the new moodle/my:manageblocks capibility to the default user role
            $context = get_context_instance(CONTEXT_SYSTEM, SITEID);
            assign_capability('moodle/my:manageblocks',CAP_ALLOW,$defaultroleid,$context->id);
        }
    }

    if ($result && $oldversion < 2007052200) {

    /// Define field schedule to be dropped from events_queue
        $table = new XMLDBTable('events_queue');
        $field = new XMLDBField('schedule');

    /// Launch drop field stackdump
        $result = $result && drop_field($table, $field);
    }

    if ($result && $oldversion < 2007052300) {
        require_once($CFG->dirroot . '/question/upgrade.php');
        $result = $result && question_remove_rqp_qtype();
    }

    if ($result && $oldversion < 2007060100) {

        /// Define field hidden to be dropped from grade_categories
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('hidden');
        
        // Launch drop field hidden
        $result = $result && drop_field($table, $field);

        // Define field deleted to be added to grade_items
        $table = new XMLDBTable('grade_items');
        $field = new XMLDBField('deleted');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'locked');

        // Launch add field deleted
        $result = $result && add_field($table, $field); 
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
    }
    
    if ($result && $oldversion < 2007060501) {

        /// Changing the default of field gradetype on table grade_items to 1
        $table = new XMLDBTable('grade_items');
        $field = new XMLDBField('gradetype');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, null, '1', 'idnumber');
        
        /// Launch change of default for field gradetype
        $result = $result && change_field_default($table, $field);
    }
    

/// merge raw and final grade tables
    if ($result && $oldversion < 2007062007) {
        // it should be ok to frop following tables so early in development cycle ;-)
        // the grades can be fetched again from modules anyway

        $table = new XMLDBTable('grade_grades_final');
        if (table_exists($table)) {
            drop_table($table);
        }

        $table = new XMLDBTable('grade_grades_raw');
        if (table_exists($table)) {
            drop_table($table);
        }

        $table = new XMLDBTable('grade_grades_text');
        $field = new XMLDBField('gradesid');

        if (field_exists($table, $field)) {
            drop_table($table);

        /// Adding fields to table grade_grades_text
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->addFieldInfo('information', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
            $table->addFieldInfo('informationformat', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('feedback', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null);
            $table->addFieldInfo('feedbackformat', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null);
            $table->addFieldInfo('usermodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, null, null);

        /// Adding keys to table grade_grades_text
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));
            $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_item', array('id'));
            $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

        /// Launch create table for grade_grades_text
            $result = $result && create_table($table);
        }

        $table = new XMLDBTable('grade_grades');
        if (!table_exists($table)) {
        /// Adding fields to table grade_grades
            $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
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
            $table->addFieldInfo('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->addFieldInfo('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

        /// Adding keys to table grade_grades
            $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
            $table->addKeyInfo('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
            $table->addKeyInfo('rawscaleid', XMLDB_KEY_FOREIGN, array('rawscaleid'), 'scale', array('id'));
            $table->addKeyInfo('usermodified', XMLDB_KEY_FOREIGN, array('usermodified'), 'user', array('id'));

        /// Launch create table for grade_grades
            $result = $result && create_table($table);
        }

    /// Define table grade_import_values to be created
        $table = new XMLDBTable('grade_import_values');
        if (table_exists($table)) {
            drop_table($table);
        }

    /// Adding fields to table grade_import_values
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('newgradeitem', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->addFieldInfo('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('rawgrade', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null, null, '0.0');
        $table->addFieldInfo('import_code', XMLDB_TYPE_INTEGER, '12', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table grade_import_values
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->addKeyInfo('itemid', XMLDB_KEY_FOREIGN, array('itemid'), 'grade_items', array('id'));
        $table->addKeyInfo('newgradeitem', XMLDB_KEY_FOREIGN, array('newgradeitem'), 'grade_import_newitem', array('id'));

    /// Launch create table for grade_import_values
        $result = $result && create_table($table);

    /// Define table grade_import_newitem to be created
        $table = new XMLDBTable('grade_import_newitem');
        if (table_exists($table)) {
            drop_table($table);
        }

    /// Adding fields to table grade_import_newitem
        $table->addFieldInfo('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->addFieldInfo('itemname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->addFieldInfo('import_code', XMLDB_TYPE_INTEGER, '12', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table grade_import_newitem
        $table->addKeyInfo('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Launch create table for grade_import_newitem
        $result = $result && create_table($table);

    }


    /// add new locktime field if needed
    if ($result && $oldversion < 2007062008) {

        $table  = new XMLDBTable('grade_items');
        $field = new XMLDBField('locktime');

        if (!field_exists($table, $field)) {
            $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'locked');
        /// Launch add field locktime
            $result = $result && add_field($table, $field);
        }
    }


/// merge calculation formula into grade_item
    if ($result && $oldversion < 2007062301) {

    /// Delete obsoleted calculations table - we did not need the data yet
        $table = new XMLDBTable('grade_calculations');
        if (table_exists($table)) {
            drop_table($table);
        }

    /// Define field calculation to be added to grade_items
        $table = new XMLDBTable('grade_items');
        $field = new XMLDBField('calculation');

        if (!field_exists($table, $field)) {
            $field->setAttributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null, 'idnumber');
        /// Launch add field calculation
            $result = $result && add_field($table, $field);
        }
    }

    if ($result && $oldversion < 2007062401) {

    /// Changing nullability of field itemname on table grade_items to null
        $table = new XMLDBTable('grade_items');
        $field = new XMLDBField('itemname');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null, 'categoryid');

    /// Launch change of nullability for field itemname
        $result = $result && change_field_notnull($table, $field);

        $field = new XMLDBField('itemmodule');
        $field->setAttributes(XMLDB_TYPE_CHAR, '30', null, null, null, null, null, null, 'itemtype');

    /// Launch change of nullability for field itemname
        $result = $result && change_field_notnull($table, $field);

        $field = new XMLDBField('iteminfo');
        $field->setAttributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null, 'itemnumber');

    /// Launch change of nullability for field itemname
        $result = $result && change_field_notnull($table, $field);


    /// Changing nullability of field path on table grade_categories to null
        $table = new XMLDBTable('grade_categories');
        $field = new XMLDBField('path');
        $field->setAttributes(XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null, 'depth');

    /// Launch change of nullability for field path
        $result = $result && change_field_notnull($table, $field);


        /// Remove the obsoleted unitttests tables - they will be recreated automatically
        $tables = array('grade_categories',
                        'scale',
                        'grade_items',
                        'grade_calculations',
                        'grade_grades_raw',
                        'grade_grades_final',
                        'grade_grades_text',
                        'grade_outcomes',
                        'grade_history');

        foreach ($tables as $table) {
            $table = new XMLDBTable('unittest_'.$table);
            if (table_exists($table)) {
                drop_table($table);
            }
        }

    }

    return $result;
}

?>
