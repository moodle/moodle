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
        $empty_on_duplicate = add_index($table, $index);
        if(empty($fail_on_duplicate)) {
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

    return $result;

}

?>
