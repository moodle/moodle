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
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

function xmldb_main_upgrade($oldversion) {
    global $CFG, $THEME, $USER, $DB;

    $result = true;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    ////////////////////////////////////////
    ///upgrade supported only from 1.9.x ///
    ////////////////////////////////////////

    if ($result && $oldversion < 2008030700) {
        upgrade_set_timeout(60*20); // this may take a while

    /// Define index contextid-lowerboundary (not unique) to be dropped form grade_letters
        $table = new xmldb_table('grade_letters');
        $index = new xmldb_index('contextid-lowerboundary', XMLDB_INDEX_NOTUNIQUE, array('contextid', 'lowerboundary'));

    /// Launch drop index contextid-lowerboundary
        $dbman->drop_index($table, $index);

    /// Define index contextid-lowerboundary-letter (unique) to be added to grade_letters
        $table = new xmldb_table('grade_letters');
        $index = new xmldb_index('contextid-lowerboundary-letter', XMLDB_INDEX_UNIQUE, array('contextid', 'lowerboundary', 'letter'));

    /// Launch add index contextid-lowerboundary-letter
        $dbman->add_index($table, $index);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008030700);
    }

    if ($result && $oldversion < 2008050100) {
        // Update courses that used weekscss to weeks
        $result = $DB->set_field('course', 'format', 'weeks', array('format' => 'weekscss'));
        upgrade_main_savepoint($result, 2008050100);
    }

    if ($result && $oldversion < 2008050200) {
        // remove unused config options
        unset_config('statsrolesupgraded');
        upgrade_main_savepoint($result, 2008050200);
    }

    if ($result && $oldversion < 2008050700) {
        upgrade_set_timeout(60*20); // this may take a while

    /// Fix minor problem caused by MDL-5482.
        require_once($CFG->dirroot . '/question/upgrade.php');
        $result = $result && question_fix_random_question_parents();
        upgrade_main_savepoint($result, 2008050700);
    }

    if ($result && $oldversion < 2008051200) {
        // if guest role used as default user role unset it and force admin to choose new setting
        if (!empty($CFG->defaultuserroleid)) {
            if ($role = $DB->get_record('role', array('id'=>$CFG->defaultuserroleid))) {
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

    if ($result && $oldversion < 2008051201) {
        notify('Increasing size of user idnumber field, this may take a while...', 'notifysuccess');
        upgrade_set_timeout(60*20); // this may take a while

    /// Under MySQL and Postgres... detect old NULL contents and change them by correct empty string. MDL-14859
        $dbfamily = $DB->get_dbfamily();
        if ($dbfamily === 'mysql' || $dbfamily === 'postgres') {
            $DB->execute("UPDATE {user} SET idnumber = '' WHERE idnumber IS NULL");
        }

    /// Define index idnumber (not unique) to be dropped form user
        $table = new xmldb_table('user');
        $index = new xmldb_index('idnumber', XMLDB_INDEX_NOTUNIQUE, array('idnumber'));

    /// Launch drop index idnumber
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

    /// Changing precision of field idnumber on table user to (255)
        $table = new xmldb_table('user');
        $field = new xmldb_field('idnumber', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null, 'password');

    /// Launch change of precision for field idnumber
        $dbman->change_field_precision($table, $field);

    /// Launch add index idnumber again
        $index = new xmldb_index('idnumber', XMLDB_INDEX_NOTUNIQUE, array('idnumber'));
        $dbman->add_index($table, $index);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008051201);
    }

    if ($result && $oldversion < 2008051202) {
        $log_action = new object();
        $log_action->module = 'course';
        $log_action->action = 'unenrol';
        $log_action->mtable = 'course';
        $log_action->field  = 'fullname';
        if (!$DB->record_exists('log_display', array('action'=>'unenrol', 'module'=>'course'))) {
            $result = $result && $DB->insert_record('log_display', $log_action);
        }
        upgrade_main_savepoint($result, 2008051202);
    }

    if ($result && $oldversion < 2008051203) {
        $table = new xmldb_table('mnet_enrol_course');
        $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, '10', true, true, null, false, false, 0);
        $dbman->change_field_precision($table, $field);
        upgrade_main_savepoint($result, 2008051203);
    }

    if ($result && $oldversion < 2008063001) {
        upgrade_set_timeout(60*20); // this may take a while

        // table to be modified
        $table = new xmldb_table('tag_instance');
        // add field
        $field = new xmldb_field('tiuserid');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 0, 'itemid');
            $dbman->add_field($table, $field);
        }
        // modify index
        $index = new xmldb_index('itemtype-itemid-tagid');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('itemtype', 'itemid', 'tagid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
        $index = new xmldb_index('itemtype-itemid-tagid-tiuserid');
        $index->set_attributes(XMLDB_INDEX_UNIQUE, array('itemtype', 'itemid', 'tagid', 'tiuserid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        /// Main savepoint reached
        upgrade_main_savepoint($result, 2008063001);
    }

    if ($result && $oldversion < 2008070300) {
        $result = $DB->delete_records_select('role_names', $DB->sql_isempty('role_names', 'name', false, false));
        upgrade_main_savepoint($result, 2008070300);
    }

    if ($result && $oldversion < 2008070700) {
        if (isset($CFG->defaultuserroleid) and isset($CFG->guestroleid) and $CFG->defaultuserroleid == $CFG->guestroleid) {
            // guest can not be selected in defaultuserroleid!
            unset_config('defaultuserroleid');
        }
        upgrade_main_savepoint($result, 2008070700);
    }

    if ($result && $oldversion < 2008070701) {

    /// Define table portfolio_instance to be created
        $table = new xmldb_table('portfolio_instance');

    /// Adding fields to table portfolio_instance
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('plugin', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '1');

    /// Adding keys to table portfolio_instance
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for portfolio_instance
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
  /// Define table portfolio_instance_config to be created
        $table = new xmldb_table('portfolio_instance_config');

    /// Adding fields to table portfolio_instance_config
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null);

    /// Adding keys to table portfolio_instance_config
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('instance', XMLDB_KEY_FOREIGN, array('instance'), 'portfolio_instance', array('id'));

    /// Adding indexes to table portfolio_instance_config
        $table->add_index('name', XMLDB_INDEX_NOTUNIQUE, array('name'));

    /// Conditionally launch create table for portfolio_instance_config
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

   /// Define table portfolio_instance_user to be created
        $table = new xmldb_table('portfolio_instance_user');

    /// Adding fields to table portfolio_instance_user
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null);

    /// Adding keys to table portfolio_instance_user
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('instancefk', XMLDB_KEY_FOREIGN, array('instance'), 'portfolio_instance', array('id'));
        $table->add_key('userfk', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Conditionally launch create table for portfolio_instance_user
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008070701);
    }

    if ($result && $oldversion < 2008072400) {
    /// Create the database tables for message_processors
        $table = new xmldb_table('message_processors');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '166', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

    /// delete old and create new fields
        $table = new xmldb_table('message');
        $field = new xmldb_field('messagetype');
        $dbman->drop_field($table, $field);

    /// fields to rename
        $field = new xmldb_field('message');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, null);
        $dbman->rename_field($table, $field, 'fullmessage');
        $field = new xmldb_field('format');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, null, null, '0', null);
        $dbman->rename_field($table, $field, 'fullmessageformat');

    /// new message fields
        $field = new xmldb_field('subject');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('fullmessagehtml');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('smallmessage');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, null);
        $dbman->add_field($table, $field);


        $table = new xmldb_table('message_read');
        $field = new xmldb_field('messagetype');
        $dbman->drop_field($table, $field);
        $field = new xmldb_field('mailed');
        $dbman->drop_field($table, $field);

    /// fields to rename
        $field = new xmldb_field('message');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, null);
        $dbman->rename_field($table, $field, 'fullmessage');
        $field = new xmldb_field('format');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, null, null, '0', null);
        $dbman->rename_field($table, $field, 'fullmessageformat');


    /// new message fields
        $field = new xmldb_field('subject');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('fullmessagehtml');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('smallmessage');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, null);
        $dbman->add_field($table, $field);

    /// new table
        $table = new xmldb_table('message_working');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('unreadmessageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('processorid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);


        upgrade_main_savepoint($result, 2008072400);
    }

    if ($result && $oldversion < 2008072800) {

    /// Define field enablecompletion to be added to course
        $table = new xmldb_table('course');
        $field = new xmldb_field('enablecompletion');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'defaultrole');

    /// Launch add field enablecompletion
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completion to be added to course_modules
        $table = new xmldb_table('course_modules');
        $field = new xmldb_field('completion');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'groupmembersonly');

    /// Launch add field completion
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completiongradeitemnumber to be added to course_modules
        $field = new xmldb_field('completiongradeitemnumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'completion');

    /// Launch add field completiongradeitemnumber
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completionview to be added to course_modules
        $field = new xmldb_field('completionview');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'completiongradeitemnumber');

    /// Launch add field completionview
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completionexpected to be added to course_modules
        $field = new xmldb_field('completionexpected');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'completionview');

    /// Launch add field completionexpected
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

   /// Define table course_modules_completion to be created
        $table = new xmldb_table('course_modules_completion');
        if (!$dbman->table_exists($table)) {

        /// Adding fields to table course_modules_completion
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->add_field('coursemoduleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->add_field('completionstate', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
            $table->add_field('viewed', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

        /// Adding keys to table course_modules_completion
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        /// Adding indexes to table course_modules_completion
            $table->add_index('coursemoduleid', XMLDB_INDEX_NOTUNIQUE, array('coursemoduleid'));
            $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        /// Launch create table for course_modules_completion
            $dbman->create_table($table);
        }

        /// Main savepoint reached
        upgrade_main_savepoint($result, 2008072800);
    }

    if ($result && $oldversion < 2008073000) {

    /// Define table portfolio_log to be created
        $table = new xmldb_table('portfolio_log');

    /// Adding fields to table portfolio_log
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('time', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('portfolio', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('caller_class', XMLDB_TYPE_CHAR, '150', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('caller_file', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('caller_sha1', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table portfolio_log
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userfk', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('portfoliofk', XMLDB_KEY_FOREIGN, array('portfolio'), 'portfolio_instance', array('id'));

    /// Conditionally launch create table for portfolio_log
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008073000);
    }

    if ($result && $oldversion < 2008073104) {
    /// Drop old table that might exist for some people
        $table = new xmldb_table('message_providers');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Define table message_providers to be created
        $table = new xmldb_table('message_providers');

    /// Adding fields to table message_providers
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('capability', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);

    /// Adding keys to table message_providers
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table message_providers
        $table->add_index('componentname', XMLDB_INDEX_UNIQUE, array('component', 'name'));

    /// Create table for message_providers
        $dbman->create_table($table);

        upgrade_main_savepoint($result, 2008073104);
    }

    if ($result && $oldversion < 2008073111) {
    /// Define table files to be created
        $table = new xmldb_table('files');

    /// Adding fields to table files
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('contenthash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('pathnamehash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('filearea', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('filepath', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('filename', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->add_field('filesize', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('mimetype', XMLDB_TYPE_CHAR, '100', null, null, null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table files
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table files
        $table->add_index('filearea-contextid-itemid', XMLDB_INDEX_NOTUNIQUE, array('filearea', 'contextid', 'itemid'));
        $table->add_index('contenthash', XMLDB_INDEX_NOTUNIQUE, array('contenthash'));
        $table->add_index('pathnamehash', XMLDB_INDEX_UNIQUE, array('pathnamehash'));

    /// Conditionally launch create table for files
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008073111);
    }

    if ($result && $oldversion < 2008073112) {
    /// Define table files_cleanup to be created
        $table = new xmldb_table('files_cleanup');

    /// Adding fields to table files_cleanup
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('contenthash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table files_cleanup
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table files_cleanup
        $table->add_index('contenthash', XMLDB_INDEX_UNIQUE, array('contenthash'));

    /// Conditionally launch create table for files_cleanup
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008073112);
    }

    if ($result && $oldversion < 2008073113) {
    /// move all course, backup and other files to new filepool based storage
        upgrade_migrate_files_courses();
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008073113);
    }

    if ($result && $oldversion < 2008073114) {
    /// move all course, backup and other files to new filepool based storage
        upgrade_migrate_files_blog();
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008073114);
    }

    if ($result && $oldversion < 2008080400) {
        // Add field ssl_jump_url to mnet application, and populate existing default applications
        $table = new xmldb_table('mnet_application');
        $field = new xmldb_field('sso_jump_url');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
            $dbman->add_field($table, $field);
            $result = $DB->set_field('mnet_application', 'sso_jump_url', '/auth/mnet/jump.php', array('name' => 'moodle'));
            $result = $result && $DB->set_field('mnet_application', 'sso_jump_url', '/auth/xmlrpc/jump.php', array('name' => 'mahara'));
        }

        /// Main savepoint reached
        upgrade_main_savepoint($result, 2008080400);
    }

    if ($result && $oldversion < 2008080500) {

   /// Define table portfolio_tempdata to be created
        $table = new xmldb_table('portfolio_tempdata');

    /// Adding fields to table portfolio_tempdata
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null);

    /// Adding keys to table portfolio_tempdata
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for portfolio_tempdata
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008080500);
    }

    if ($result && $oldversion < 2008080600) {

        $DB->delete_records('portfolio_tempdata'); // there shouldnt' be any, and it will cause problems with this upgrade.
    /// Define field expirytime to be added to portfolio_tempdata
        $table = new xmldb_table('portfolio_tempdata');
        $field = new xmldb_field('expirytime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null, 'data');

    /// Conditionally launch add field expirytime
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008080600);
    }

/// Changing the type of all the columns that the question bank uses to store grades to be NUMBER(12, 7).
    if ($result && $oldversion < 2008081500) {
        $table = new xmldb_table('question');
        $field = new xmldb_field('defaultgrade', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, null, null, 'generalfeedback');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081500);
    }

    if ($result && $oldversion < 2008081501) {
        $table = new xmldb_table('question');
        $field = new xmldb_field('penalty', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, null, null, 'defaultgrade');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081501);
    }

    if ($result && $oldversion < 2008081502) {
        $table = new xmldb_table('question_answers');
        $field = new xmldb_field('fraction', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, null, null, 'answer');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081502);
    }

    if ($result && $oldversion < 2008081503) {
        $table = new xmldb_table('question_sessions');
        $field = new xmldb_field('sumpenalty', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, null, null, 'newgraded');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081503);
    }

    if ($result && $oldversion < 2008081504) {
        $table = new xmldb_table('question_states');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, null, null, 'event');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081504);
    }

    if ($result && $oldversion < 2008081505) {
        $table = new xmldb_table('question_states');
        $field = new xmldb_field('raw_grade', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, null, null, 'grade');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081505);
    }

    if ($result && $oldversion < 2008081506) {
        $table = new xmldb_table('question_states');
        $field = new xmldb_field('penalty', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, null, null, 'raw_grade');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081506);
    }

    if ($result && $oldversion < 2008081600) {

    /// all 1.9 sites and fresh installs must already be unicode, not needed anymore
        unset_config('unicodedb');

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008081600);
    }

    if ($result && $oldversion < 2008081900) {
    /// Define field userid to be added to portfolio_tempdata
        $table = new xmldb_table('portfolio_tempdata');
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null, 'expirytime');

    /// Conditionally launch add field userid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $DB->set_field('portfolio_tempdata', 'userid', 0);
    /// now change it to be notnull

    /// Changing nullability of field userid on table portfolio_tempdata to not null
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null, 'expirytime');

    /// Launch change of nullability for field userid
        $dbman->change_field_notnull($table, $field);

    /// Define key userfk (foreign) to be added to portfolio_tempdata
        $table = new xmldb_table('portfolio_tempdata');
        $key = new xmldb_key('userfk', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Launch add key userfk
        $dbman->add_key($table, $key);

        upgrade_main_savepoint($result, 2008081900);
    }
    if ($result && $oldversion < 2008082602) {

    /// Define table repository to be dropped
        $table = new xmldb_table('repository');

    /// Conditionally launch drop table for repository
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Define table repository to be created
        $table = new xmldb_table('repository');

    /// Adding fields to table repository
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, '1');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');

    /// Adding keys to table repository
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for repository
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
    /// Define table repository_instances to be created
        $table = new xmldb_table('repository_instances');

    /// Adding fields to table repository_instances
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('typeid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('username', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->add_field('password', XMLDB_TYPE_CHAR, '255', null, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);

    /// Adding keys to table repository_instances
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for repository_instances
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Define table repository_instance_config to be created
        $table = new xmldb_table('repository_instance_config');

    /// Adding fields to table repository_instance_config
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null);

    /// Adding keys to table repository_instance_config
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for repository_instance_config
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008082602);
    }

    if ($result && $oldversion < 2008082700) {
    /// Add a new column to the question sessions table to record whether a
    /// question has been flagged.

    /// Define field flagged to be added to question_sessions
        $table = new xmldb_table('question_sessions');
        $field = new xmldb_field('flagged', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null, null, '0', 'manualcomment');

    /// Conditionally launch add field flagged
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008082700);
    }

    if ($result && $oldversion < 2008082900) {

    /// Changing precision of field parent_type on table mnet_rpc to (20)
        $table = new xmldb_table('mnet_rpc');
        $field = new xmldb_field('parent_type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, null, null, 'xmlrpc_path');

    /// Launch change of precision for field parent_type
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008082900);
    }

    if ($result && $oldversion < 2008090108) {
        $repo = new object();
        $repo->type      = 'upload';
        $repo->visible   = 1;
        $repo->sortorder = 1;
        if (!$DB->record_exists('repository', array('type'=>'upload'))) {
            $typeid = $DB->insert_record('repository', $repo);
        }else{
            $record = $DB->get_record('repository', array('type'=>'upload'));
            $typeid = $record->id;
        }
        if (!$DB->record_exists('repository_instances', array('typeid'=>$typeid))) {
            $instance = new object();
            $instance->name      = get_string('repositoryname', 'repository_upload');
            $instance->typeid    = $typeid;
            $instance->userid    = 0;
            $instance->contextid = SITEID;
            $instance->timecreated  = time();
            $instance->timemodified = time();
            $result = $result && $DB->insert_record('repository_instances', $instance);
        }
        $repo->type      = 'local';
        $repo->visible   = 1;
        $repo->sortorder = 1;
        if (!$DB->record_exists('repository', array('type'=>'local'))) {
            $typeid = $DB->insert_record('repository', $repo);
        }else{
            $record = $DB->get_record('repository', array('type'=>'local'));
            $typeid = $record->id;
        }
        if (!$DB->record_exists('repository_instances', array('typeid'=>$typeid))) {
            $instance = new object();
            $instance->name      = get_string('repositoryname', 'repository_local');
            $instance->typeid    = $typeid;
            $instance->userid    = 0;
            $instance->contextid = SITEID;
            $instance->timecreated  = time();
            $instance->timemodified = time();
            $result = $result && $DB->insert_record('repository_instances', $instance);
        }

        upgrade_main_savepoint($result, 2008090108);
    }

    // MDL-16411 Move all plugintype_pluginname_version values from config to config_plugins.
    if ($result && $oldversion < 2008091000) {
        foreach (get_object_vars($CFG) as $name => $value) {
            if (substr($name, strlen($name) - 8) !== '_version') {
                continue;
            }
            $pluginname = substr($name, 0, strlen($name) - 8);
            if (!strpos($pluginname, '_')) {
                // Skip things like backup_version that don't contain an extra _
                continue;
            }
            if ($pluginname == 'enrol_ldap_version') {
                // Special case - this is something different from a plugin version number.
                continue;
            }
            if (!preg_match('/^\d{10}$/', $value)) {
                // Extra safety check, skip anything that does not look like a Moodle
                // version number (10 digits).
                continue;
            }
            $result = $result && set_config('version', $value, $pluginname);
            $result = $result && unset_config($name);
        }
        upgrade_main_savepoint($result, 2008091000);
    }

    //Add a readonly field to the repository_instances table
    //in order to support instance created automatically by a repository plugin
     if ($result && $oldversion < 2008091611) {

    /// Define field readonly to be added to repository_instances
        $table = new xmldb_table('repository_instances');
        $field = new xmldb_field('readonly', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'timemodified');

    /// Conditionally launch add field readonly
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008091611);
    }

    if ($result && $oldversion < 2008092300) {
        unset_config('editorspelling');
        unset_config('editordictionary');
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008092300);
    }

    if ($result && $oldversion < 2008101000) {

    /// Changing the default of field lang on table user to en_utf8
        $table = new xmldb_table('user');
        $field = new xmldb_field('lang', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, null, null, 'en_utf8', 'country');

    /// Launch change of default for field lang
        $dbman->change_field_default($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008101000);
    }

    if ($result && $oldversion < 2008101300) {

        if (!get_config(NULL, 'statsruntimedays')) {
            set_config('statsruntimedays', '31');
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008101300);
    }

    /// New table for storing which roles can be assigned in which contexts.
    if ($result && $oldversion < 2008110601) {

    /// Define table role_context_levels to be created
        $table = new xmldb_table('role_context_levels');

    /// Adding fields to table role_context_levels
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('contextlevel', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);

    /// Adding keys to table role_context_levels
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextlevel-roleid', XMLDB_KEY_UNIQUE, array('contextlevel', 'roleid'));
        $table->add_key('roleid', XMLDB_KEY_FOREIGN, array('roleid'), 'role', array('id'));

    /// Conditionally launch create table for role_context_levels
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008110601);
    }

    /// Now populate the role_context_levels table with the defaults that match
    /// moodle_install_roles, and any other combinations that exist in this system.
    if ($result && $oldversion < 2008110602) {
        $roleids = $DB->get_records_menu('role', array(), '', 'shortname,id');

    /// Defaults, should match moodle_install_roles.
        $rolecontextlevels = array();
        if (isset($roleids['admin'])) {
            $rolecontextlevels[$roleids['admin']] = get_default_contextlevels('admin');
        }
        if (isset($roleids['coursecreator'])) {
            $rolecontextlevels[$roleids['coursecreator']] = get_default_contextlevels('coursecreator');
        }
        if (isset($roleids['editingteacher'])) {
            $rolecontextlevels[$roleids['editingteacher']] = get_default_contextlevels('editingteacher');
        }
        if (isset($roleids['teacher'])) {
            $rolecontextlevels[$roleids['teacher']] = get_default_contextlevels('teacher');
        }
        if (isset($roleids['student'])) {
            $rolecontextlevels[$roleids['student']] = get_default_contextlevels('student');
        }
        if (isset($roleids['guest'])) {
            $rolecontextlevels[$roleids['guest']] = get_default_contextlevels('guest');
        }
        if (isset($roleids['user'])) {
            $rolecontextlevels[$roleids['user']] = get_default_contextlevels('user');
        }

    /// See what other role assignments are in this database, extend the allowed
    /// lists to allow them too.
        $existingrolecontextlevels = $DB->get_recordset_sql('SELECT DISTINCT ra.roleid, con.contextlevel FROM
                {role_assignments} ra JOIN {context} con ON ra.contextid = con.id');
        foreach ($existingrolecontextlevels as $rcl) {
            if (!isset($rolecontextlevels[$rcl->roleid])) {
                $rolecontextlevels[$rcl->roleid] = array($rcl->contextlevel);
            } else if (!in_array($rcl->contextlevel, $rolecontextlevels[$rcl->roleid])) {
                $rolecontextlevels[$rcl->roleid][] = $rcl->contextlevel;
            }
        }

    /// Put the data into the database.
        foreach ($rolecontextlevels as $roleid => $contextlevels) {
            set_role_contextlevels($roleid, $contextlevels);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008110602);
    }

    /// Remove any role overrides for moodle/site:doanything, or any permissions
    /// for it in a role without legacy:admin.
    if ($result && $oldversion < 2008110603) {
        $systemcontext = get_context_instance(CONTEXT_SYSTEM);

        // Remove all overrides.
        $DB->delete_records_select('role_capabilities', 'capability = ? AND contextid <> ?', array('moodle/site:doanything', $systemcontext->id));

        // Get the ids of all the roles that are moodle/legacy:admin.
        $adminroleids = $DB->get_records_menu('role_capabilities',
                array('capability' => 'moodle/legacy:admin', 'permission' => 1, 'contextid' => $systemcontext->id),
                '', 'id, roleid');

        // Remove moodle/site:doanything from all other roles.
        list($notroletest, $params) = $DB->get_in_or_equal($adminroleids, SQL_PARAMS_QM, '', false);
        $DB->delete_records_select('role_capabilities', "roleid $notroletest AND capability = ? AND contextid = ?",
                array_merge($params, array('moodle/site:doanything', $systemcontext->id)));

        // Ensure that for all admin-y roles, the permission for moodle/site:doanything is 1
        list($isroletest, $params) = $DB->get_in_or_equal($adminroleids);
        $DB->set_field_select('role_capabilities', 'permission', 1,
                "roleid $isroletest AND capability = ? AND contextid = ?",
                array_merge($params, array('moodle/site:doanything', $systemcontext->id)));

        // And for any admin-y roles where moodle/site:doanything is not set, set it.
        $doanythingroleids = $DB->get_records_menu('role_capabilities',
                array('capability' => 'moodle/site:doanything', 'permission' => 1, 'contextid' => $systemcontext->id),
                '', 'id, roleid');
        foreach ($adminroleids as $roleid) {
            if (!in_array($roleid, $doanythingroleids)) {
                $rc = new stdClass;
                $rc->contextid = $systemcontext->id;
                $rc->roleid = $roleid;
                $rc->capability = 'moodle/site:doanything';
                $rc->permission = 1;
                $rc->timemodified = time();
                $DB->insert_record('role_capabilities', $rc);
            }
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008110603);
    }

    /// Drop the deprecated teacher, teachers, student and students columns from the course table.
    if ($result && $oldversion < 2008111200) {
        $table = new xmldb_table('course');

    /// Conditionally launch drop field teacher
        $field = new xmldb_field('teacher');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Conditionally launch drop field teacher
        $field = new xmldb_field('teachers');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Conditionally launch drop field teacher
        $field = new xmldb_field('student');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Conditionally launch drop field teacher
        $field = new xmldb_field('students');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008111200);
    }

/// Add a unique index to the role.name column.
    if ($result && $oldversion < 2008111800) {

    /// Define index name (unique) to be added to role
        $table = new xmldb_table('role');
        $index = new xmldb_index('name', XMLDB_INDEX_UNIQUE, array('name'));

    /// Conditionally launch add index name
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008111800);
    }

/// Add a unique index to the role.shortname column.
    if ($result && $oldversion < 2008111801) {

    /// Define index shortname (unique) to be added to role
        $table = new xmldb_table('role');
        $index = new xmldb_index('shortname', XMLDB_INDEX_UNIQUE, array('shortname'));

    /// Conditionally launch add index shortname
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008111801);
    }

    if ($result && $oldversion < 2008120700) {

    /// Changing precision of field shortname on table course_request to (100)
        $table = new xmldb_table('course_request');
        $field = new xmldb_field('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, null, null, 'fullname');

    /// Launch change of precision for field shortname
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008120700);
    }

    /// For MDL-17501. Ensure that any role that has moodle/course:update also
    /// has moodle/course:visibility.
    if ($result && $oldversion < 2008120800) {
    /// Get the roles with 'moodle/course:update'.
        $systemcontext = get_context_instance(CONTEXT_SYSTEM);
        $roles = get_roles_with_capability('moodle/course:update', CAP_ALLOW, $systemcontext);

    /// Give those roles 'moodle/course:visibility'.
        foreach ($roles as $role) {
            assign_capability('moodle/course:visibility', CAP_ALLOW, $role->id, $systemcontext->id);
        }

    /// Force all sessions to refresh access data.
        mark_context_dirty($systemcontext->path);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008120800);
    }

    if ($result && $oldversion < 2008120801) {

    /// Changing precision of field shortname on table mnet_enrol_course to (100)
        $table = new xmldb_table('mnet_enrol_course');
        $field = new xmldb_field('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, null, null, 'fullname');

    /// Launch change of precision for field shortname
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008120801);
    }

    if ($result && $oldversion < 2008121701) {

    /// Define field availablefrom to be added to course_modules
        $table = new xmldb_table('course_modules');
        $field = new xmldb_field('availablefrom', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'completionexpected');

    /// Conditionally launch add field availablefrom
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field availableuntil to be added to course_modules
        $field = new xmldb_field('availableuntil', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'availablefrom');

    /// Conditionally launch add field availableuntil
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field showavailability to be added to course_modules
        $field = new xmldb_field('showavailability', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'availableuntil');

    /// Conditionally launch add field showavailability
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define table course_modules_availability to be created
        $table = new xmldb_table('course_modules_availability');

    /// Adding fields to table course_modules_availability
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('coursemoduleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('sourcecmid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->add_field('requiredcompletion', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->add_field('gradeitemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->add_field('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);
        $table->add_field('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null, null, null);

    /// Adding keys to table course_modules_availability
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('coursemoduleid', XMLDB_KEY_FOREIGN, array('coursemoduleid'), 'course_modules', array('id'));
        $table->add_key('sourcecmid', XMLDB_KEY_FOREIGN, array('sourcecmid'), 'course_modules', array('id'));
        $table->add_key('gradeitemid', XMLDB_KEY_FOREIGN, array('gradeitemid'), 'grade_items', array('id'));

    /// Conditionally launch create table for course_modules_availability
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Changes to modinfo mean we need to rebuild course cache
        rebuild_course_cache(0,true);

    /// For developer upgrades, turn on the conditional activities and completion
    /// features automatically (to gain more testing)
        if(debugging('',DEBUG_DEVELOPER)) {
            set_config('enableavailability',1);
            set_config('enablecompletion',1);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008121701);
    }

    if ($result && $oldversion < 2009010500) {
    /// clean up config table a bit
        unset_config('session_error_counter');

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010500);
    }

    if ($result && $oldversion < 2009010600) {

    /// Define field originalquestion to be dropped from question_states
        $table = new xmldb_table('question_states');
        $field = new xmldb_field('originalquestion');

    /// Conditionally launch drop field originalquestion
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010600);
    }

    if ($result && $oldversion < 2009010601) {

    /// Changing precision of field ip on table log to (45)
        $table = new xmldb_table('log');
        $field = new xmldb_field('ip', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, null, null, 'userid');

    /// Launch change of precision for field ip
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010601);
    }

    if ($result && $oldversion < 2009010602) {

    /// Changing precision of field lastip on table user to (45)
        $table = new xmldb_table('user');
        $field = new xmldb_field('lastip', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, null, null, 'currentlogin');

    /// Launch change of precision for field lastip
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010602);
    }

    if ($result && $oldversion < 2009010603) {

    /// Changing precision of field ip_address on table mnet_host to (45)
        $table = new xmldb_table('mnet_host');
        $field = new xmldb_field('ip_address', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, null, null, 'wwwroot');

    /// Launch change of precision for field ip_address
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010603);
    }

    if ($result && $oldversion < 2009010604) {

    /// Changing precision of field ip on table mnet_log to (45)
        $table = new xmldb_table('mnet_log');
        $field = new xmldb_field('ip', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, null, null, 'userid');

    /// Launch change of precision for field ip
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010604);
    }

    if ($result && $oldversion < 2009010605) {

    /// Define table sessions to be dropped
        $table = new xmldb_table('sessions2');

    /// Conditionally launch drop table for sessions
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Define table sessions to be dropped
        $table = new xmldb_table('sessions');

    /// Conditionally launch drop table for sessions
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010605);
    }

    if ($result && $oldversion < 2009010606) {

    /// Define table sessions to be created
        $table = new xmldb_table('sessions');

    /// Adding fields to table sessions
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('state', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('sid', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('sessdata', XMLDB_TYPE_TEXT, 'big', null, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('firstip', XMLDB_TYPE_CHAR, '45', null, null, null, null, null, null);
        $table->add_field('lastip', XMLDB_TYPE_CHAR, '45', null, null, null, null, null, null);

    /// Adding keys to table sessions
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table sessions
        $table->add_index('state', XMLDB_INDEX_NOTUNIQUE, array('state'));
        $table->add_index('sid', XMLDB_INDEX_UNIQUE, array('sid'));
        $table->add_index('timecreated', XMLDB_INDEX_NOTUNIQUE, array('timecreated'));
        $table->add_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));

    /// Launch create table for sessions
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010606);
    }

    if ($result && $oldversion < 2009010800) {
    /// Update the notifyloginfailures setting.
        if ($CFG->notifyloginfailures == 'mainadmin') {
            set_config('notifyloginfailures', get_admin()->username);
        } else if ($CFG->notifyloginfailures == 'alladmins') {
            set_config('notifyloginfailures', '$@ALL@$');
        } else {
            set_config('notifyloginfailures', '');
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010800);
    }

    if ($result && $oldversion < 2009011000) {

    /// Changing nullability of field configdata on table block_instance to null
        $table = new xmldb_table('block_instance');
        $field = new xmldb_field('configdata');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null, 'visible');

    /// Launch change of nullability for field configdata
        $dbman->change_field_notnull($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009011000);
    }

    if ($result && $oldversion < 2009011100) {
    /// Remove unused settings
        unset_config('zip');
        unset_config('unzip');
        unset_config('adminblocks_initialised');

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009011100);
    }


    return $result;
}


?>
