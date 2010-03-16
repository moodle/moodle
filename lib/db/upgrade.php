<?PHP

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
    global $CFG, $USER, $DB, $OUTPUT;

    require_once($CFG->libdir.'/db/upgradelib.php'); // Core Upgrade-related functions

    $result = true;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    ////////////////////////////////////////
    ///upgrade supported only from 1.9.x ///
    ////////////////////////////////////////

    if ($result && $oldversion < 2008030600) {
        //NOTE: this table was added much later, that is why this step is repeated later in this file

    /// Define table upgrade_log to be created
        $table = new xmldb_table('upgrade_log');

    /// Adding fields to table upgrade_log
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('plugin', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('version', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('info', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('details', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('backtrace', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table upgrade_log
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table upgrade_log
        $table->add_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));
        $table->add_index('type-timemodified', XMLDB_INDEX_NOTUNIQUE, array('type', 'timemodified'));

    /// Create table for upgrade_log
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008030600);
    }

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
                        echo $OUTPUT->notification('Guest role removed from "Default role for all users" setting, please select another role.', 'notifysuccess');
                    }
                }
            } else {
                set_config('defaultuserroleid', null);
            }
        }
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008051200);
    }

    if ($result && $oldversion < 2008051201) {
        echo $OUTPUT->notification('Increasing size of user idnumber field, this may take a while...', 'notifysuccess');
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
        $field = new xmldb_field('idnumber', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'password');

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
        $field = new xmldb_field('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);
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
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'itemid');
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
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('plugin', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1');

    /// Adding keys to table portfolio_instance
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for portfolio_instance
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
  /// Define table portfolio_instance_config to be created
        $table = new xmldb_table('portfolio_instance_config');

    /// Adding fields to table portfolio_instance_config
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

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
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field('instance', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

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
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '166', null, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);

    /// delete old and create new fields
        $table = new xmldb_table('message');
        $field = new xmldb_field('messagetype');
        $dbman->drop_field($table, $field);

    /// fields to rename
        $field = new xmldb_field('message');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->rename_field($table, $field, 'fullmessage');
        $field = new xmldb_field('format');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0', null);
        $dbman->rename_field($table, $field, 'fullmessageformat');

    /// new message fields
        $field = new xmldb_field('subject');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('fullmessagehtml');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('smallmessage');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->add_field($table, $field);


        $table = new xmldb_table('message_read');
        $field = new xmldb_field('messagetype');
        $dbman->drop_field($table, $field);
        $field = new xmldb_field('mailed');
        $dbman->drop_field($table, $field);

    /// fields to rename
        $field = new xmldb_field('message');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->rename_field($table, $field, 'fullmessage');
        $field = new xmldb_field('format');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0', null);
        $dbman->rename_field($table, $field, 'fullmessageformat');


    /// new message fields
        $field = new xmldb_field('subject');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('fullmessagehtml');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null);
        $dbman->add_field($table, $field);
        $field = new xmldb_field('smallmessage');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, null);
        $dbman->add_field($table, $field);

    /// new table
        $table = new xmldb_table('message_working');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('unreadmessageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('processorid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $dbman->create_table($table);


        upgrade_main_savepoint($result, 2008072400);
    }

    if ($result && $oldversion < 2008072800) {

    /// Define field enablecompletion to be added to course
        $table = new xmldb_table('course');
        $field = new xmldb_field('enablecompletion');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'defaultrole');

    /// Launch add field enablecompletion
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completion to be added to course_modules
        $table = new xmldb_table('course_modules');
        $field = new xmldb_field('completion');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'groupmembersonly');

    /// Launch add field completion
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completiongradeitemnumber to be added to course_modules
        $field = new xmldb_field('completiongradeitemnumber');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'completion');

    /// Launch add field completiongradeitemnumber
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completionview to be added to course_modules
        $field = new xmldb_field('completionview');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'completiongradeitemnumber');

    /// Launch add field completionview
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completionexpected to be added to course_modules
        $field = new xmldb_field('completionexpected');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'completionview');

    /// Launch add field completionexpected
        if (!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

   /// Define table course_modules_completion to be created
        $table = new xmldb_table('course_modules_completion');
        if (!$dbman->table_exists($table)) {

        /// Adding fields to table course_modules_completion
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('coursemoduleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('completionstate', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
            $table->add_field('viewed', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

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
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('time', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('portfolio', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('caller_class', XMLDB_TYPE_CHAR, '150', null, XMLDB_NOTNULL, null, null);
        $table->add_field('caller_file', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('caller_sha1', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

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
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
        $table->add_field('capability', XMLDB_TYPE_CHAR, '255', null, null, null, null);

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
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contenthash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('pathnamehash', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('filearea', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('filepath', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('filename', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('filesize', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('mimetype', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

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
            $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
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
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

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
        $field = new xmldb_field('expirytime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'data');

    /// Conditionally launch add field expirytime
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008080600);
    }

    if ($result && $oldversion < 2008081500) {
    /// Changing the type of all the columns that the question bank uses to store grades to be NUMBER(12, 7).
        $table = new xmldb_table('question');
        $field = new xmldb_field('defaultgrade', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'generalfeedback');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081500);
    }

    if ($result && $oldversion < 2008081501) {
        $table = new xmldb_table('question');
        $field = new xmldb_field('penalty', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'defaultgrade');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081501);
    }

    if ($result && $oldversion < 2008081502) {
        $table = new xmldb_table('question_answers');
        $field = new xmldb_field('fraction', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'answer');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081502);
    }

    if ($result && $oldversion < 2008081503) {
        $table = new xmldb_table('question_sessions');
        $field = new xmldb_field('sumpenalty', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'newgraded');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081503);
    }

    if ($result && $oldversion < 2008081504) {
        $table = new xmldb_table('question_states');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'event');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081504);
    }

    if ($result && $oldversion < 2008081505) {
        $table = new xmldb_table('question_states');
        $field = new xmldb_field('raw_grade', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'grade');
        $dbman->change_field_type($table, $field);
        upgrade_main_savepoint($result, 2008081505);
    }

    if ($result && $oldversion < 2008081506) {
        $table = new xmldb_table('question_states');
        $field = new xmldb_field('penalty', XMLDB_TYPE_NUMBER, '12, 7', null, null, null, null, 'raw_grade');
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
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'expirytime');

    /// Conditionally launch add field userid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $DB->set_field('portfolio_tempdata', 'userid', 0);
    /// now change it to be notnull

    /// Changing nullability of field userid on table portfolio_tempdata to not null
        $field = new xmldb_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'expirytime');

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
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, '1');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table repository
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for repository
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
    /// Define table repository_instances to be created
        $table = new xmldb_table('repository_instances');

    /// Adding fields to table repository_instances
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('typeid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('username', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('password', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table repository_instances
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for repository_instances
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Define table repository_instance_config to be created
        $table = new xmldb_table('repository_instance_config');

    /// Adding fields to table repository_instance_config
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

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
        $field = new xmldb_field('flagged', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'manualcomment');

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
        $field = new xmldb_field('parent_type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'xmlrpc_path');

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
        $field = new xmldb_field('readonly', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'timemodified');

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
        $field = new xmldb_field('lang', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'en_utf8', 'country');

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
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('contextlevel', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

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
        $field = new xmldb_field('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'fullname');

    /// Before changing the field, drop dependent indexes
    /// Define index shortname (not unique) to be dropped form course_request
        $index = new xmldb_index('shortname', XMLDB_INDEX_NOTUNIQUE, array('shortname'));
    /// Conditionally launch drop index shortname
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

    /// Launch change of precision for field shortname
        $dbman->change_field_precision($table, $field);

    /// After changing the field, recreate dependent indexes
    /// Define index shortname (not unique) to be added to course_request
        $index = new xmldb_index('shortname', XMLDB_INDEX_NOTUNIQUE, array('shortname'));
    /// Conditionally launch add index shortname
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

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
        $field = new xmldb_field('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'fullname');

    /// Launch change of precision for field shortname
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2008120801);
    }

    if ($result && $oldversion < 2008121701) {

    /// Define field availablefrom to be added to course_modules
        $table = new xmldb_table('course_modules');
        $field = new xmldb_field('availablefrom', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'completionexpected');

    /// Conditionally launch add field availablefrom
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field availableuntil to be added to course_modules
        $field = new xmldb_field('availableuntil', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'availablefrom');

    /// Conditionally launch add field availableuntil
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field showavailability to be added to course_modules
        $field = new xmldb_field('showavailability', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'availableuntil');

    /// Conditionally launch add field showavailability
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define table course_modules_availability to be created
        $table = new xmldb_table('course_modules_availability');

    /// Adding fields to table course_modules_availability
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('coursemoduleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('sourcecmid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('requiredcompletion', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('gradeitemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);
        $table->add_field('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, null);

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
        require_once($CFG->dirroot . '/course/lib.php');
        rebuild_course_cache(0, true);

    /// For developer upgrades, turn on the conditional activities and completion
    /// features automatically (to gain more testing)
//TODO: remove before 2.0 final!
        if (debugging('', DEBUG_DEVELOPER)) {
            set_config('enableavailability', 1);
            set_config('enablecompletion', 1);
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
        $field = new xmldb_field('ip', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, 'userid');

    /// Launch change of precision for field ip
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010601);
    }

    if ($result && $oldversion < 2009010602) {

    /// Changing precision of field lastip on table user to (45)
        $table = new xmldb_table('user');
        $field = new xmldb_field('lastip', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, 'currentlogin');

    /// Launch change of precision for field lastip
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010602);
    }

    if ($result && $oldversion < 2009010603) {

    /// Changing precision of field ip_address on table mnet_host to (45)
        $table = new xmldb_table('mnet_host');
        $field = new xmldb_field('ip_address', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, 'wwwroot');

    /// Launch change of precision for field ip_address
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010603);
    }

    if ($result && $oldversion < 2009010604) {

    /// Changing precision of field ip on table mnet_log to (45)
        $table = new xmldb_table('mnet_log');
        $field = new xmldb_field('ip', XMLDB_TYPE_CHAR, '45', null, XMLDB_NOTNULL, null, null, 'userid');

    /// Launch change of precision for field ip
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009010604);
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
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'visible');

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

    if ($result && $oldversion < 2009011101) {
    /// Migrate backup settings to core plugin config table
        $configs = $DB->get_records('backup_config');
        foreach ($configs as $config) {
            set_config($config->name, $config->value, 'backup');
        }

    /// Define table to be dropped
        $table = new xmldb_table('backup_config');

    /// Launch drop table for old backup config
        $dbman->drop_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009011101);
    }

    if ($result && $oldversion < 2009011303) {

    /// Define table config_log to be created
        $table = new xmldb_table('config_log');

    /// Adding fields to table config_log
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('plugin', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('oldvalue', XMLDB_TYPE_TEXT, 'small', null, null, null, null);

    /// Adding keys to table config_log
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table config_log
        $table->add_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));

    /// Launch create table for config_log
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009011303);
    }

    if ($result && $oldversion < 2009011900) {

    /// Define table sessions2 to be dropped
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

    /// Define table sessions to be created
        $table = new xmldb_table('sessions');

    /// Adding fields to table sessions
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('state', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('sid', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('sessdata', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('firstip', XMLDB_TYPE_CHAR, '45', null, null, null, null);
        $table->add_field('lastip', XMLDB_TYPE_CHAR, '45', null, null, null, null);

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
        upgrade_main_savepoint($result, 2009011900);
    }

    if ($result && $oldversion < 2009012901) {
        // NOTE: this table may already exist, see beginning of this file ;-)

    /// Define table upgrade_log to be created
        $table = new xmldb_table('upgrade_log');

    /// Adding fields to table upgrade_log
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('plugin', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('version', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('info', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('details', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('backtrace', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table upgrade_log
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table upgrade_log
        $table->add_index('timemodified', XMLDB_INDEX_NOTUNIQUE, array('timemodified'));
        $table->add_index('type-timemodified', XMLDB_INDEX_NOTUNIQUE, array('type', 'timemodified'));

    /// Conditionally launch create table for upgrade_log
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009012901);
    }

    if ($result && $oldversion < 2009021800) {
        // Converting format of grade conditions, if any exist, to percentages.
        $DB->execute("
UPDATE {course_modules_availability} SET grademin=(
    SELECT 100.0*({course_modules_availability}.grademin-gi.grademin)
        /(gi.grademax-gi.grademin)
    FROM {grade_items} gi
    WHERE gi.id={course_modules_availability}.gradeitemid)
WHERE gradeitemid IS NOT NULL AND grademin IS NOT NULL");
        $DB->execute("
UPDATE {course_modules_availability} SET grademax=(
    SELECT 100.0*({course_modules_availability}.grademax-gi.grademin)
        /(gi.grademax-gi.grademin)
    FROM {grade_items} gi
    WHERE gi.id={course_modules_availability}.gradeitemid)
WHERE gradeitemid IS NOT NULL AND grademax IS NOT NULL");

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009021800);
    }
    if ($result && $oldversion < 2009021801) {
    /// Define field backuptype to be added to backup_log
        $table = new xmldb_table('backup_log');
        $field = new xmldb_field('backuptype', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null, 'info');
    /// Conditionally Launch add field backuptype and set all old records as 'scheduledbackup' records.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $DB->execute("UPDATE {backup_log} SET backuptype='scheduledbackup'");
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009021801);
    }
    /// Add default sort order for question types.
    if ($result && $oldversion < 2009030300) {
        set_config('multichoice_sortorder', 1, 'question');
        set_config('truefalse_sortorder', 2, 'question');
        set_config('shortanswer_sortorder', 3, 'question');
        set_config('numerical_sortorder', 4, 'question');
        set_config('calculated_sortorder', 5, 'question');
        set_config('essay_sortorder', 6, 'question');
        set_config('match_sortorder', 7, 'question');
        set_config('randomsamatch_sortorder', 8, 'question');
        set_config('multianswer_sortorder', 9, 'question');
        set_config('description_sortorder', 10, 'question');
        set_config('random_sortorder', 11, 'question');
        set_config('missingtype_sortorder', 12, 'question');

        upgrade_main_savepoint($result, 2009030300);
    }
    if ($result && $oldversion < 2009030501) {
    /// setup default repository plugins
        require_once($CFG->dirroot . '/repository/lib.php');
        repository_setup_default_plugins();
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009030501);
    }

    /// MDL-18132 replace the use a new Role allow switch settings page, instead of
    /// $CFG->allowuserswitchrolestheycantassign
    if ($result && $oldversion < 2009032000) {
    /// First create the new table.
            $table = new xmldb_table('role_allow_switch');

    /// Adding fields to table role_allow_switch
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('allowswitch', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table role_allow_switch
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('roleid', XMLDB_KEY_FOREIGN, array('roleid'), 'role', array('id'));
        $table->add_key('allowswitch', XMLDB_KEY_FOREIGN, array('allowswitch'), 'role', array('id'));

    /// Adding indexes to table role_allow_switch
        $table->add_index('roleid-allowoverride', XMLDB_INDEX_UNIQUE, array('roleid', 'allowswitch'));

    /// Conditionally launch create table for role_allow_switch
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009032000);
    }

    if ($result && $oldversion < 2009032001) {
    /// Copy from role_allow_assign into the new table.
        $DB->execute('INSERT INTO {role_allow_switch} (roleid, allowswitch)
                SELECT roleid, allowassign FROM {role_allow_assign}');

    /// Unset the config variable used in 1.9.
        unset_config('allowuserswitchrolestheycantassign');

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009032001);
    }

    if ($result && $oldversion < 2009033100) {
        require_once("$CFG->dirroot/filter/tex/lib.php");
        filter_tex_updatedcallback(null);
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009033100);
    }

    if ($result && $oldversion < 2009040300) {

    /// Define table filter_active to be created
        $table = new xmldb_table('filter_active');

    /// Adding fields to table filter_active
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('filter', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('active', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table filter_active
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

    /// Adding indexes to table filter_active
        $table->add_index('contextid-filter', XMLDB_INDEX_UNIQUE, array('contextid', 'filter'));

    /// Conditionally launch create table for filter_active
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009040300);
    }

    if ($result && $oldversion < 2009040301) {

    /// Define table filter_config to be created
        $table = new xmldb_table('filter_config');

    /// Adding fields to table filter_config
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('filter', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_TEXT, 'small', null, null, null, null);

    /// Adding keys to table filter_config
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

    /// Adding indexes to table filter_config
        $table->add_index('contextid-filter-name', XMLDB_INDEX_UNIQUE, array('contextid', 'filter', 'name'));

    /// Conditionally launch create table for filter_config
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009040301);
    }

    if ($result && $oldversion < 2009040302) {
    /// Transfer current settings from $CFG->textfilters
        $disabledfilters = filter_get_all_installed();
        if (empty($CFG->textfilters)) {
            $activefilters = array();
        } else {
            $activefilters = explode(',', $CFG->textfilters);
        }
        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        $sortorder = 1;
        foreach ($activefilters as $filter) {
            filter_set_global_state($filter, TEXTFILTER_ON, $sortorder);
            $sortorder += 1;
            unset($disabledfilters[$filter]);
        }
        foreach ($disabledfilters as $filter => $notused) {
            filter_set_global_state($filter, TEXTFILTER_DISABLED, $sortorder);
            $sortorder += 1;
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009040302);
    }

    if ($result && $oldversion < 2009040600) {
    /// Ensure that $CFG->stringfilters is set.
        if (empty($CFG->stringfilters)) {
            if (!empty($CFG->filterall)) {
                set_config('stringfilters', $CFG->textfilters);
            } else {
                set_config('stringfilters', '');
            }
        }

        set_config('filterall', !empty($CFG->stringfilters));
        unset_config('textfilters');

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009040600);
    }

    if ($result && $oldversion < 2009041700) {
    /// To ensure the UI remains consistent with no behaviour change, any
    /// 'until' date in an activity condition should have 1 second subtracted
    /// (to go from 0:00 on the following day to 23:59 on the previous one).
        $DB->execute('UPDATE {course_modules} SET availableuntil = availableuntil - 1 WHERE availableuntil <> 0');
        require_once($CFG->dirroot . '/course/lib.php');
        rebuild_course_cache(0, true);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009041700);
    }

    if ($result && $oldversion < 2009042600) {
    /// Deleting orphaned messages from deleted users.
        require_once($CFG->dirroot.'/message/lib.php');
    /// Detect deleted users with messages sent(useridfrom) and not read
        if ($deletedusers = $DB->get_records_sql('SELECT DISTINCT u.id
                                                    FROM {user} u
                                                    JOIN {message} m ON m.useridfrom = u.id
                                                   WHERE u.deleted = ?', array(1))) {
            foreach ($deletedusers as $deleteduser) {
                message_move_userfrom_unread2read($deleteduser->id); // move messages
            }
        }
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009042600);
    }

    /// Dropping all enums/check contraints from core. MDL-18577
    if ($result && $oldversion < 2009042700) {

    /// Changing list of values (enum) of field stattype on table stats_daily to none
        $table = new xmldb_table('stats_daily');
        $field = new xmldb_field('stattype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'activity', 'roleid');

    /// Launch change of list of values for field stattype
        $dbman->drop_enum_from_field($table, $field);

    /// Changing list of values (enum) of field stattype on table stats_weekly to none
        $table = new xmldb_table('stats_weekly');
        $field = new xmldb_field('stattype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'activity', 'roleid');

    /// Launch change of list of values for field stattype
        $dbman->drop_enum_from_field($table, $field);

    /// Changing list of values (enum) of field stattype on table stats_monthly to none
        $table = new xmldb_table('stats_monthly');
        $field = new xmldb_field('stattype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'activity', 'roleid');

    /// Launch change of list of values for field stattype
        $dbman->drop_enum_from_field($table, $field);

    /// Changing list of values (enum) of field publishstate on table post to none
        $table = new xmldb_table('post');
        $field = new xmldb_field('publishstate', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'draft', 'attachment');

    /// Launch change of list of values for field publishstate
        $dbman->drop_enum_from_field($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009042700);
    }

    if ($result && $oldversion < 2009043000) {
        unset_config('grade_report_showgroups');
        upgrade_main_savepoint($result, 2009043000);
    }

    if ($result && $oldversion < 2009050600) {
    /// Site front page blocks need to be moved due to page name change.
        $DB->set_field('block_instance', 'pagetype', 'site-index', array('pagetype' => 'course-view', 'pageid' => SITEID));

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050600);
    }

    if ($result && $oldversion < 2009050601) {

    /// Define table block_instance to be renamed to block_instances
        $table = new xmldb_table('block_instance');

    /// Launch rename table for block_instance
        $dbman->rename_table($table, 'block_instances');

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050601);
    }

    if ($result && $oldversion < 2009050602) {

    /// Define table block_instance to be renamed to block_instance_old
        $table = new xmldb_table('block_pinned');

    /// Launch rename table for block_instance
        $dbman->rename_table($table, 'block_pinned_old');

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050602);
    }

    if ($result && $oldversion < 2009050603) {

    /// Define table block_instance_old to be created
        $table = new xmldb_table('block_instance_old');

    /// Adding fields to table block_instance_old
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('oldid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('blockid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('pageid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('pagetype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('position', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('weight', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('configdata', XMLDB_TYPE_TEXT, 'small', null, null, null, null);

    /// Adding keys to table block_instance_old
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('blockid', XMLDB_KEY_FOREIGN, array('blockid'), 'block', array('id'));

    /// Adding indexes to table block_instance_old
        $table->add_index('pageid', XMLDB_INDEX_NOTUNIQUE, array('pageid'));
        $table->add_index('pagetype', XMLDB_INDEX_NOTUNIQUE, array('pagetype'));

    /// Conditionally launch create table for block_instance_old
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050603);
    }

    if ($result && $oldversion < 2009050604) {
    /// Copy current blocks data from block_instances to block_instance_old
        $DB->execute('INSERT INTO {block_instance_old} (oldid, blockid, pageid, pagetype, position, weight, visible, configdata)
            SELECT id, blockid, pageid, pagetype, position, weight, visible, configdata FROM {block_instances} ORDER BY id');

        upgrade_main_savepoint($result, 2009050604);
    }

    if ($result && $oldversion < 2009050605) {

    /// Define field multiple to be dropped from block
        $table = new xmldb_table('block');
        $field = new xmldb_field('multiple');

    /// Conditionally launch drop field multiple
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050605);
    }

    if ($result && $oldversion < 2009050606) {
        $table = new xmldb_table('block_instances');

    /// Rename field weight on table block_instances to defaultweight
        $field = new xmldb_field('weight', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '0', 'position');
        $dbman->rename_field($table, $field, 'defaultweight');

    /// Rename field position on table block_instances to defaultregion
        $field = new xmldb_field('position', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, 'pagetype');
        $dbman->rename_field($table, $field, 'defaultregion');

        /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050606);
    }

    if ($result && $oldversion < 2009050607) {
    /// Changing precision of field defaultregion on table block_instances to (16)
        $table = new xmldb_table('block_instances');
        $field = new xmldb_field('defaultregion', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, null, 'pagetype');

    /// Launch change of precision for field defaultregion
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050607);
    }

    if ($result && $oldversion < 2009050608) {
    /// Change regions to the new notation
        $DB->set_field('block_instances', 'defaultregion', 'side-pre', array('defaultregion' => 'l'));
        $DB->set_field('block_instances', 'defaultregion', 'side-post', array('defaultregion' => 'r'));
        $DB->set_field('block_instances', 'defaultregion', 'course-view-top', array('defaultregion' => 'c'));
        // This third one is a custom value from contrib/patches/center_blocks_position_patch and the
        // flex page course format. Hopefully this new value is an adequate alternative.

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050608);
    }

    if ($result && $oldversion < 2009050609) {

    /// Define key blockname (unique) to be added to block
        $table = new xmldb_table('block');
        $key = new xmldb_key('blockname', XMLDB_KEY_UNIQUE, array('name'));

    /// Launch add key blockname
        $dbman->add_key($table, $key);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050609);
    }

    if ($result && $oldversion < 2009050610) {
        $table = new xmldb_table('block_instances');

    /// Define field blockname to be added to block_instances
        $field = new xmldb_field('blockname', XMLDB_TYPE_CHAR, '40', null, null, null, null, 'blockid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field contextid to be added to block_instances
        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'blockname');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field showinsubcontexts to be added to block_instances
        $field = new xmldb_field('showinsubcontexts', XMLDB_TYPE_INTEGER, '4', null, null, null, null, 'contextid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field subpagepattern to be added to block_instances
        $field = new xmldb_field('subpagepattern', XMLDB_TYPE_CHAR, '16', null, null, null, null, 'pagetype');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050610);
    }

    if ($result && $oldversion < 2009050611) {
        $table = new xmldb_table('block_instances');

    /// Fill in blockname from blockid
        $DB->execute("UPDATE {block_instances} SET blockname = (SELECT name FROM {block} WHERE id = blockid)");

    /// Set showinsubcontexts = 0 for all rows.
        $DB->execute("UPDATE {block_instances} SET showinsubcontexts = 0");

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050611);
    }

    if ($result && $oldversion < 2009050612) {

    /// Rename field pagetype on table block_instances to pagetypepattern
        $table = new xmldb_table('block_instances');
        $field = new xmldb_field('pagetype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'pageid');

    /// Launch rename field pagetype
        $dbman->rename_field($table, $field, 'pagetypepattern');

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050612);
    }

    if ($result && $oldversion < 2009050613) {
    /// fill in contextid and subpage, and update pagetypepattern from pagetype and pageid

    /// site-index
        $frontpagecontext = get_context_instance(CONTEXT_COURSE, SITEID);
        $DB->execute("UPDATE {block_instances} SET contextid = " . $frontpagecontext->id . ",
                                                   pagetypepattern = 'site-index',
                                                   subpagepattern = NULL
                      WHERE pagetypepattern = 'site-index'");

    /// course-view
        $DB->execute("UPDATE {block_instances} SET
                        contextid = (
                            SELECT {context}.id
                            FROM {context}
                            JOIN {course} ON instanceid = {course}.id AND contextlevel = " . CONTEXT_COURSE . "
                            WHERE {course}.id = pageid
                        ),
                       pagetypepattern = 'course-view-*',
                       subpagepattern = NULL
                      WHERE pagetypepattern = 'course-view'");

    /// admin
        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        $DB->execute("UPDATE {block_instances} SET
                        contextid = " . $syscontext->id . ",
                        pagetypepattern = 'admin-*',
                        subpagepattern = NULL
                      WHERE pagetypepattern = 'admin'");

    /// my-index
        $DB->execute("UPDATE {block_instances} SET
                        contextid = (
                            SELECT {context}.id
                            FROM {context}
                            JOIN {user} ON instanceid = {user}.id AND contextlevel = " . CONTEXT_USER . "
                            WHERE {user}.id = pageid
                        ),
                        pagetypepattern = 'my-index',
                        subpagepattern = NULL
                      WHERE pagetypepattern = 'my-index'");

    /// tag-index
        $DB->execute("UPDATE {block_instances} SET
                        contextid = " . $syscontext->id . ",
                        pagetypepattern = 'tag-index',
                        subpagepattern = pageid
                      WHERE pagetypepattern = 'tag-index'");

    /// blog-view
        $DB->execute("UPDATE {block_instances} SET
                        contextid = (
                            SELECT {context}.id
                            FROM {context}
                            JOIN {user} ON instanceid = {user}.id AND contextlevel = " . CONTEXT_USER . "
                            WHERE {user}.id = pageid
                        ),
                        pagetypepattern = 'blog-index',
                        subpagepattern = NULL
                      WHERE pagetypepattern = 'blog-view'");

    /// mod-xxx-view
        $moduleswithblocks = array('chat', 'data', 'lesson', 'quiz', 'dimdim', 'game', 'wiki', 'oublog');
        foreach ($moduleswithblocks as $modname) {
            if (!$dbman->table_exists($modname)) {
                continue;
            }
            $DB->execute("UPDATE {block_instances} SET
                            contextid = (
                                SELECT {context}.id
                                FROM {context}
                                JOIN {course_modules} ON instanceid = {course_modules}.id AND contextlevel = " . CONTEXT_MODULE . "
                                JOIN {modules} ON {modules}.id = {course_modules}.module AND {modules}.name = '$modname'
                                JOIN {{$modname}} ON {course_modules}.instance = {{$modname}}.id
                                WHERE {{$modname}}.id = pageid
                            ),
                            pagetypepattern = 'blog-index',
                            subpagepattern = NULL
                          WHERE pagetypepattern = 'blog-view'");
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050613);
    }

    if ($result && $oldversion < 2009050614) {
    /// fill in any missing contextids with a dummy value, so we can add the not-null constraint.
        $DB->execute("UPDATE {block_instances} SET contextid = 0 WHERE contextid IS NULL");

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050614);
    }

    if ($result && $oldversion < 2009050615) {
        $table = new xmldb_table('block_instances');

    /// Changing nullability of field blockname on table block_instances to not null
        $field = new xmldb_field('blockname', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null, 'id');
        $dbman->change_field_notnull($table, $field);

    /// Changing nullability of field contextid on table block_instances to not null
        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'blockname');
        $dbman->change_field_notnull($table, $field);

    /// Changing nullability of field showinsubcontexts on table block_instances to not null
        $field = new xmldb_field('showinsubcontexts', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null, 'contextid');
        $dbman->change_field_notnull($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050615);
    }

    if ($result && $oldversion < 2009050616) {
    /// Add exiting sticky blocks.
        $blocks = $DB->get_records('block');
        $syscontext = get_context_instance(CONTEXT_SYSTEM);
        $newregions = array(
            'l' => 'side-pre',
            'r' => 'side-post',
            'c' => 'course-view-top',
        );
        $stickyblocks = $DB->get_recordset('block_pinned_old');
        foreach ($stickyblocks as $stickyblock) {
            $newblock = new object();
            $newblock->blockname = $blocks[$stickyblock->blockid]->name;
            $newblock->contextid = $syscontext->id;
            $newblock->showinsubcontexts = 1;
            switch ($stickyblock->pagetype) {
                case 'course-view':
                    $newblock->pagetypepattern = 'course-view-*';
                    break;
                default:
                    $newblock->pagetypepattern = $stickyblock->pagetype;
            }
            $newblock->defaultregion = $newregions[$stickyblock->position];
            $newblock->defaultweight = $stickyblock->weight;
            $newblock->configdata = $stickyblock->configdata;
            $newblock->visible = 1;
            $DB->insert_record('block_instances', $newblock);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050616);
    }

    if ($result && $oldversion < 2009050617) {

    /// Define table block_positions to be created
        $table = new xmldb_table('block_positions');

    /// Adding fields to table block_positions
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('blockinstanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('pagetype', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subpage', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, null);
        $table->add_field('visible', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('region', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, null);
        $table->add_field('weight', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

    /// Adding keys to table block_positions
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('blockinstanceid', XMLDB_KEY_FOREIGN, array('blockinstanceid'), 'block_instances', array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

    /// Adding indexes to table block_positions
        $table->add_index('blockinstanceid-contextid-pagetype-subpage', XMLDB_INDEX_UNIQUE, array('blockinstanceid', 'contextid', 'pagetype', 'subpage'));

    /// Conditionally launch create table for block_positions
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050617);
    }

    if ($result && $oldversion < 2009050618) {
    /// And block instances with visible = 0, copy that information to block_positions
        $DB->execute("INSERT INTO {block_positions} (blockinstanceid, contextid, pagetype, subpage, visible, region, weight)
                SELECT id, contextid,
                CASE WHEN pagetypepattern = 'course-view-*' THEN
                        (SELECT " . $DB->sql_concat("'course-view-'", 'format') . "
                        FROM {course}
                        JOIN {context} ON {course}.id = {context}.instanceid
                        WHERE {context}.id = contextid)
                    ELSE pagetypepattern END,
                CASE WHEN subpagepattern IS NULL THEN ''
                    ELSE subpagepattern END,
                0, defaultregion, defaultweight
                FROM {block_instances} WHERE visible = 0 AND pagetypepattern <> 'admin-*'");

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050618);
    }

    if ($result && $oldversion < 2009050619) {
        $table = new xmldb_table('block_instances');

    /// Define field blockid to be dropped from block_instances
        $field = new xmldb_field('blockid');
        if ($dbman->field_exists($table, $field)) {
        /// Before dropping the field, drop dependent indexes
            $index = new xmldb_index('blockid', XMLDB_INDEX_NOTUNIQUE, array('blockid'));
            if ($dbman->index_exists($table, $index)) {
            /// Launch drop index blockid
                $dbman->drop_index($table, $index);
            }
            $dbman->drop_field($table, $field);
        }

    /// Define field pageid to be dropped from block_instances
        $field = new xmldb_field('pageid');
        if ($dbman->field_exists($table, $field)) {
        /// Before dropping the field, drop dependent indexes
            $index = new xmldb_index('pageid', XMLDB_INDEX_NOTUNIQUE, array('pageid'));
            if ($dbman->index_exists($table, $index)) {
            /// Launch drop index pageid
                $dbman->drop_index($table, $index);
            }
            $dbman->drop_field($table, $field);
        }

    /// Define field visible to be dropped from block_instances
        $field = new xmldb_field('visible');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009050619);
    }

    if ($result && $oldversion < 2009051200) {
    /// Let's check the status of mandatory mnet_host records, fixing them
    /// and moving "orphan" users to default localhost record. MDL-16879
        echo $OUTPUT->notification('Fixing mnet records, this may take a while...', 'notifysuccess');
        upgrade_fix_incorrect_mnethostids();

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009051200);
    }


    if ($result && $oldversion < 2009051700) {
    /// migrate editor settings
        if (empty($CFG->htmleditor)) {
            set_config('texteditors', 'textarea');
        } else {
            set_config('texteditors', 'tinymce,textarea');
        }

        unset_config('htmleditor');
        unset_config('defaulthtmleditor');

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009051700);
    }

    if ($result && $oldversion < 2009060200) {
    /// Define table files_cleanup to be dropped - not needed
        $table = new xmldb_table('files_cleanup');

    /// Conditionally launch drop table for files_cleanup
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009060200);
    }

    if ($result && $oldversion < 2009061300) {
        //TODO: copy this to the very beginning of this upgrade script so that we may log upgrade queries

    /// Define table log_queries to be created
        $table = new xmldb_table('log_queries');

    /// Adding fields to table log_queries
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('qtype', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('sqltext', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sqlparams', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('error', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('info', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('backtrace', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('exectime', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timelogged', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table log_queries
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for log_queries
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009061300);
    }

    /// Repeat 2009050607 upgrade step, which Petr commented out becuase of XMLDB
    /// stupidity, so lots of peopel will have missed.
    if ($result && $oldversion < 2009061600) {
    /// Changing precision of field defaultregion on table block_instances to (16)
        $table = new xmldb_table('block_instances');
        $field = new xmldb_field('defaultregion', XMLDB_TYPE_CHAR, '16', null, XMLDB_NOTNULL, null, null, 'configdata');

    /// Launch change of precision for field defaultregion
        $dbman->change_field_precision($table, $field);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009061600);
    }

    if ($result && $oldversion < 2009061702) {
        // standardizing plugin names
        if ($configs = $DB->get_records_select('config_plugins', "plugin LIKE 'quizreport_%'")) {
            foreach ($configs as $config) {
                $result = $result && unset_config($config->name, $config->plugin); /// unset old config
                $config->plugin = str_replace('quizreport_', 'quiz_', $config->plugin);
                $result = $result && set_config($config->name, $config->value, $config->plugin); /// set new config
            }
        }
        unset($configs);
        upgrade_main_savepoint($result, 2009061702);
    }

    if ($result && $oldversion < 2009061703) {
        // standardizing plugin names
        if ($configs = $DB->get_records_select('config_plugins', "plugin LIKE 'assignment_type_%'")) {
            foreach ($configs as $config) {
                $result = $result && unset_config($config->name, $config->plugin); /// unset old config
                $config->plugin = str_replace('assignment_type_', 'assignment_', $config->plugin);
                $result = $result && set_config($config->name, $config->value, $config->plugin); /// set new config
            }
        }
        unset($configs);
        upgrade_main_savepoint($result, 2009061703);
    }

    if ($result && $oldversion < 2009061704) {
        // change component string in capability records to new "_" format
        if ($caps = $DB->get_records('capabilities')) {
            foreach ($caps as $cap) {
                $cap->component = str_replace('/', '_', $cap->component);
                $DB->update_record('capabilities', $cap);
            }
        }
        unset($caps);
        upgrade_main_savepoint($result, 2009061704);
    }

    if ($result && $oldversion < 2009061705) {
        // change component string in events_handlers records to new "_" format
        if ($handlers = $DB->get_records('events_handlers')) {
            foreach ($handlers as $handler) {
                $handler->handlermodule = str_replace('/', '_', $handler->handlermodule);
                $DB->update_record('events_handlers', $handler);
            }
        }
        unset($handlers);
        upgrade_main_savepoint($result, 2009061705);
    }

    if ($result && $oldversion < 2009061706) {
        // change component string in message_providers records to new "_" format
        if ($mps = $DB->get_records('message_providers')) {
            foreach ($mps as $mp) {
                $mp->component = str_replace('/', '_', $mp->component);
                $DB->update_record('message_providers', $cap);
            }
        }
        unset($caps);
        upgrade_main_savepoint($result, 2009061706);
    }

    if ($result && $oldversion < 2009063000) {
        // upgrade format of _with_advanced settings - quiz only
        // note: this can be removed later, not needed for upgrades from 1.9.x
        if ($quiz = get_config('quiz')) {
            foreach ($quiz as $name=>$value) {
                if (strpos($name, 'fix_') !== 0) {
                    continue;
                }
                $newname = substr($name,4).'_adv';
                set_config($newname, $value, 'quiz');
                unset_config($name, 'quiz');
            }
        }
        upgrade_main_savepoint($result, 2009063000);
    }

    if ($result && $oldversion < 2009071000) {

    /// Rename field contextid on table block_instances to parentcontextid
        $table = new xmldb_table('block_instances');
        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'blockname');

    /// Launch rename field parentcontextid
        $dbman->rename_field($table, $field, 'parentcontextid');

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009071000);
    }

    if ($result && $oldversion < 2009071300) {

    /// Create contexts for every block. In the past, only non-sticky course block had contexts.
    /// This is a copy of the code in create_contexts.
        $sql = "INSERT INTO {context} (contextlevel, instanceid)
                SELECT " . CONTEXT_BLOCK . ", bi.id
                  FROM {block_instances} bi
                 WHERE NOT EXISTS (SELECT 'x'
                                     FROM {context} ctx
                                    WHERE bi.id = ctx.instanceid AND ctx.contextlevel=" . CONTEXT_BLOCK . ")";
        $DB->execute($sql);

    /// TODO MDL-19776 We should not really use API funcitons in upgrade.
    /// If MDL-19776 is done, we can remove this whole upgrade block.
        build_context_path();

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009071300);
    }

    if ($result && $oldversion < 2009071600) {

    /// Define field summaryformat to be added to post
        $table = new xmldb_table('post');
        $field = new xmldb_field('summaryformat', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'format');

    /// Conditionally launch add field summaryformat
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009071600);
    }

    if ($result && $oldversion < 2009072400) {

    /// Define table comments to be created
        $table = new xmldb_table('comments');

    /// Adding fields to table comments
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('commentarea', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('format', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table comments
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for comments
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009072400);
    }

    /**
     * This upgrade is to set up the new navigation blocks that have been developed
     * as part of Moodle 2.0
     * Now I [Sam Hemelryk] hit a conundrum while exploring how to go about this
     * as not only do we want to install the new blocks but we also want to set up
     * default instances of them, and at the same time remove instances of the blocks
     * that were/will-be outmoded by the two new navigation blocks.
     * After talking it through with Tim Hunt {@link http://moodle.org/mod/cvsadmin/view.php?conversationid=3112}
     * we decided that the best way to go about this was to put the bulk of the
     * upgrade operation into core upgrade `here` but to let the plugins block
     * still install the blocks.
     * This leaves one hairy end in that we will create block_instances within the
     * DB before the blocks themselves are created within the DB
     */
    if ($result && $oldversion < 2009082800) {

        echo $OUTPUT->notification(get_string('navigationupgrade', 'admin'));

        // Get the system context so we can set the block instances to it
        $syscontext = get_context_instance(CONTEXT_SYSTEM);

        // An array to contain the new block instances we will create
        $newblockinstances = array('globalnavigation'=>new stdClass,'settingsnavigation'=>new stdClass);
        // The new global navigation block instance as a stdClass
        $newblockinstances['globalnavigation']->blockname = 'global_navigation_tree';
        $newblockinstances['globalnavigation']->parentcontextid = $syscontext->id; // System context
        $newblockinstances['globalnavigation']->showinsubcontexts = true; // Show absolutly everywhere
        $newblockinstances['globalnavigation']->pagetypepattern = '*'; // Thats right everywhere
        $newblockinstances['globalnavigation']->subpagetypepattern = null;
        $newblockinstances['globalnavigation']->defaultregion = BLOCK_POS_LEFT;
        $newblockinstances['globalnavigation']->defaultweight = -10; // Try make this first
        $newblockinstances['globalnavigation']->configdata = '';
        // The new settings navigation block instance as a stdClass
        $newblockinstances['settingsnavigation']->blockname = 'settings_navigation_tree';
        $newblockinstances['settingsnavigation']->parentcontextid = $syscontext->id;
        $newblockinstances['settingsnavigation']->showinsubcontexts = true;
        $newblockinstances['settingsnavigation']->pagetypepattern = '*';
        $newblockinstances['settingsnavigation']->subpagetypepattern = null;
        $newblockinstances['settingsnavigation']->defaultregion = BLOCK_POS_LEFT;
        $newblockinstances['settingsnavigation']->defaultweight = -9; // Try make this second
        $newblockinstances['settingsnavigation']->configdata = '';

        // Blocks that are outmoded and for whom the bells will toll... by which I
        // mean we will delete all instances of
        $outmodedblocks = array('participants','admin_tree','activity_modules','admin','course_list');
        $outmodedblocksstring = '\''.join('\',\'',$outmodedblocks).'\'';
        unset($outmodedblocks);
        // Retrieve the block instance id's and parent contexts, so we can join them an GREATLY
        // cut down the number of delete queries we will need to run
        $allblockinstances = $DB->get_recordset_select('block_instances', 'blockname IN ('.$outmodedblocksstring.')', array(), '', 'id, parentcontextid');

        $contextids = array();
        $instanceids = array();
        // Iterate through all block instances
        foreach ($allblockinstances as $blockinstance) {
            if (!in_array($blockinstance->parentcontextid, $contextids)) {
                $contextids[] = $blockinstance->parentcontextid;

                // If we have over 1000 contexts clean them up and reset the array
                // this ensures we don't hit any nasty memory limits or such
                if (count($contextids) > 1000) {
                    $result = $result && upgrade_cleanup_unwanted_block_contexts($contextids);
                    $contextids = array();
                }
            }
            if (!in_array($blockinstance->id, $instanceids)) {
                $instanceids[] = $blockinstance->id;
                // If we have more than 1000 block instances now remove all block positions
                // and empty the array
                if (count($contextids) > 1000) {
                    $instanceidstring = join(',',$instanceids);
                    $result = $result && $DB->delete_records_select('block_positions', 'blockinstanceid IN ('.$instanceidstring.')');
                    $instanceids = array();
                }
            }
        }

        $result = $result && upgrade_cleanup_unwanted_block_contexts($contextids);

        $instanceidstring = join(',',$instanceids);
        $outcome1 = $result && $DB->delete_records_select('block_positions', 'blockinstanceid IN ('.$instanceidstring.')');

        unset($allblockinstances);
        unset($contextids);
        unset($instanceids);
        unset($instanceidstring);

        // Now remove the actual block instance
        $result = $result && $DB->delete_records_select('block_instances', 'blockname IN ('.$outmodedblocksstring.')');
        unset($outmodedblocksstring);

        // Insert the new block instances. Remember they have not been installed yet
        // however this should not be a problem
        foreach ($newblockinstances as $blockinstance) {
            $blockinstance->id= $DB->insert_record('block_instances', $blockinstance);
            // Ensure the block context is created.
            get_context_instance(CONTEXT_BLOCK, $blockinstance->id);
        }
        unset($newblockinstances);

        upgrade_main_savepoint($result, 2009082800);
        // The end of the navigation upgrade
    }

    if ($result && $oldversion < 2009090800){
        //insert new record for log_display table
        //used to record tag update.
        if (!$DB->record_exists('log_display', array('action'=>'update', 'module'=>'tag'))) {
            $log_action = new object();
            $log_action->module = 'tag';
            $log_action->action = 'update';
            $log_action->mtable = 'tag';
            $log_action->field  = 'name';

            $result  = $result && $DB->insert_record('log_display', $log_action);
        }
        upgrade_main_savepoint($result, 2009090800);
    }

    if ($result && $oldversion < 2009100601) {
        // drop all previous tables defined during the dev phase
        $dropold = array('external_services_users', 'external_services_functions', 'external_services', 'external_functions');
        foreach ($dropold as $tablename) {
            $table = new xmldb_table($tablename);
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }
        upgrade_main_savepoint($result, 2009100601);
    }

    if ($result && $oldversion < 2009100602) {
    /// Define table external_functions to be created
        $table = new xmldb_table('external_functions');

    /// Adding fields to table external_functions
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
        $table->add_field('classname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('methodname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('classpath', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);

    /// Adding keys to table external_functions
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table external_functions
        $table->add_index('name', XMLDB_INDEX_UNIQUE, array('name'));

    /// Launch create table for external_functions
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009100602);
    }

    if ($result && $oldversion < 2009100603) {
    /// Define table external_services to be created
        $table = new xmldb_table('external_services');

    /// Adding fields to table external_services
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('requiredcapability', XMLDB_TYPE_CHAR, '150', null, null, null, null);
        $table->add_field('restrictedusers', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('component', XMLDB_TYPE_CHAR, '100', null, null, null, null);

    /// Adding keys to table external_services
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table external_services
        $table->add_index('name', XMLDB_INDEX_UNIQUE, array('name'));

    /// Launch create table for external_services
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009100603);
    }

    if ($result && $oldversion < 2009100604) {
    /// Define table external_services_functions to be created
        $table = new xmldb_table('external_services_functions');

    /// Adding fields to table external_services_functions
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('externalserviceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('functionname', XMLDB_TYPE_CHAR, '200', null, XMLDB_NOTNULL, null, null);

    /// Adding keys to table external_services_functions
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('externalserviceid', XMLDB_KEY_FOREIGN, array('externalserviceid'), 'external_services', array('id'));

    /// Launch create table for external_services_functions
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009100604);
    }

    if ($result && $oldversion < 2009100605) {
    /// Define table external_services_users to be created
        $table = new xmldb_table('external_services_users');

    /// Adding fields to table external_services_users
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('externalserviceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('iprestriction', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('validuntil', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table external_services_users
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('externalserviceid', XMLDB_KEY_FOREIGN, array('externalserviceid'), 'external_services', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Launch create table for external_services_users
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009100605);
    }

    if ($result && $oldversion < 2009102600) {

    /// Define table external_tokens to be created
        $table = new xmldb_table('external_tokens');

    /// Adding fields to table external_tokens
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('token', XMLDB_TYPE_CHAR, '128', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tokentype', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('externalserviceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('sid', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('iprestriction', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('validuntil', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('lastaccess', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);

    /// Adding keys to table external_tokens
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));
        $table->add_key('externalserviceid', XMLDB_KEY_FOREIGN, array('externalserviceid'), 'external_services', array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));

    /// Launch create table for external_tokens
        $dbman->create_table($table);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009102600);
    }

   if ($result && $oldversion < 2009103000) {

    /// Define table blog_association to be created
        $table = new xmldb_table('blog_association');

    /// Adding fields to table blog_association
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('blogid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table blog_association
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));
        $table->add_key('blogid', XMLDB_KEY_FOREIGN, array('blogid'), 'post', array('id'));

    /// Conditionally launch create table for blog_association
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

/// Define table blog_external to be created
        $table = new xmldb_table('blog_external');

    /// Adding fields to table blog_external
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('url', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('filtertags', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('failedlastsync', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null);
        $table->add_field('timefetched', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table blog_external
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Conditionally launch create table for blog_external
        if ($dbman->table_exists($table)) {
            // Delete the existing one first (comes from early dev version)
            $dbman->drop_table($table);
        }
        $dbman->create_table($table);

        // Print notice about need to upgrade bloglevel
        if (($CFG->bloglevel == BLOG_COURSE_LEVEL || $CFG->bloglevel == BLOG_GROUP_LEVEL) && empty($CFG->bloglevel_upgrade_complete)) {
            echo $OUTPUT->notification(get_string('bloglevelupgradenotice', 'admin'));

            // email admins about the need to upgrade their system using the admin/bloglevelupgrade.php script
            $admins = get_admins();
            $site = get_site();

            $a = new StdClass;
            $a->sitename = $site->fullname;
            $a->fixurl   = "$CFG->wwwroot/$CFG->admin/bloglevelupgrade.php";

            $subject = get_string('bloglevelupgrade', 'admin');
            $description = get_string('bloglevelupgradedescription', 'admin', $a);

            // can not use messaging here because it is not configured yet!
            upgrade_log(UPGRADE_LOG_NOTICE, null, $subject, $description);
        }
    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009103000);
    }

    if ($result && $oldversion < 2009110400) {

        // An array used to store the table name and keys of summary and trust fields
        // to be added
        $extendtables = array();
        $extendtables['course'] = array('summaryformat');
        $extendtables['course_categories'] = array('descriptionformat');
        $extendtables['course_request'] = array('summaryformat');
        $extendtables['grade_outcomes'] = array('descriptionformat');
        $extendtables['groups'] = array('descriptionformat');
        $extendtables['groupings'] = array('descriptionformat');
        $extendtables['scale'] = array('descriptionformat');
        $extendtables['user'] = array('descriptionformat');
        $extendtables['user_info_field'] = array('descriptionformat', 'defaultdataformat');
        $extendtables['user_info_data'] = array('dataformat');

        foreach ($extendtables as $tablestr=>$newfields) {
            $table = new xmldb_table($tablestr);
            foreach ($newfields as $fieldstr) {
                $field = new xmldb_field($fieldstr, XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
                // Check that the field doesn't already exists
                if (!$dbman->field_exists($table, $field)) {
                    // Add the new field
                    $dbman->add_field($table, $field);
                    // Update the field if the text contains the default FORMAT_MOODLE to FORMAT_HTML
                    if (($pos = strpos($fieldstr, 'format'))>0) {
                        upgrade_set_timeout(60*20); // this may take a little while
                        $params = array(FORMAT_HTML, '<p%', '%<br />%', FORMAT_MOODLE);
                        $textfield = substr($fieldstr, 0, $pos);
                        $DB->execute('UPDATE {'.$tablestr.'} SET '.$fieldstr.'=? WHERE ('.$textfield.' LIKE ? OR '.$textfield.' LIKE ?) AND '.$fieldstr.'=?', $params);
                    }
                }
            }
        }

        unset($extendtables);

        upgrade_main_savepoint($result, 2009110400);
    }

    if ($result && $oldversion < 2009110605) {

    /// Define field timecreated to be added to external_services
        $table = new xmldb_table('external_services');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'component');

    /// Conditionally launch add field timecreated
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field timemodified to be added to external_services
        $table = new xmldb_table('external_services');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, null, null, 'timecreated');

    /// Conditionally launch add field timemodified
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009110605);
    }

    if ($result && $oldversion < 2009111600) {

    /// Define field instance to be added to portfolio_tempdata
        $table = new xmldb_table('portfolio_tempdata');
        $field = new xmldb_field('instance', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'userid');

    /// Conditionally launch add field instance
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

       $key = new xmldb_key('instancefk', XMLDB_KEY_FOREIGN, array('instance'), 'portfolio_instance', array('id'));

    /// Launch add key instancefk
        $dbman->add_key($table, $key);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009111600);
    }

    if ($result && $oldversion < 2009111700) {

    /// Define field tempdataid to be added to portfolio_log
        $table = new xmldb_table('portfolio_log');
        $field = new xmldb_field('tempdataid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'caller_sha1');

    /// Conditionally launch add field tempdataid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009111700);
    }

    if ($result && $oldversion < 2009111701) {

    /// Define field returnurl to be added to portfolio_log
        $table = new xmldb_table('portfolio_log');
        $field = new xmldb_field('returnurl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'tempdataid');

    /// Conditionally launch add field returnurl
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009111701);
    }

    if ($result && $oldversion < 2009111702) {

    /// Define field continueurl to be added to portfolio_log
        $table = new xmldb_table('portfolio_log');
        $field = new xmldb_field('continueurl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'returnurl');

    /// Conditionally launch add field continueurl
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2009111702);
    }

    if ($result && $oldversion < 2009112400) {
        if (empty($CFG->passwordsaltmain)) {
            $subject = get_string('check_passwordsaltmain_name', 'report_security');
            $description = get_string('check_passwordsaltmain_warning', 'report_security');;
            upgrade_log(UPGRADE_LOG_NOTICE, null, $subject, $description);
        }
        upgrade_main_savepoint($result, 2009112400);
    }

    if ($result && $oldversion < 2010010601) {

    /// Define field creatorid to be added to external_tokens
        $table = new xmldb_table('external_tokens');
        $field = new xmldb_field('creatorid', XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'contextid');

    /// Conditionally launch add field creatorid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define key creatorid (foreign) to be added to external_tokens
        $table = new xmldb_table('external_tokens');
        $key = new xmldb_key('creatorid', XMLDB_KEY_FOREIGN, array('creatorid'), 'user', array('id'));

    /// Launch add key creatorid
        $dbman->add_key($table, $key);

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2010010601);
    }

    if ($result && $oldversion < 2010011200) {
        $table = new xmldb_table('grade_categories');
        $field = new xmldb_field('hidden', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_main_savepoint($result, 2010011200);
    }


    if ($result && $oldversion < 2010012500) {
        upgrade_fix_incorrect_mnethostids();
        upgrade_main_savepoint($result, 2010012500);
    }

    if ($result && $oldversion < 2010012600) {
        // do stuff to the mnet table
        $table = new xmldb_table('mnet_rpc');

        $field = new xmldb_field('parent_type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'xmlrpc_path');
        $dbman->rename_field($table, $field, 'plugintype');

        $field = new xmldb_field('parent', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'xmlrpc_path');
        $dbman->rename_field($table, $field, 'pluginname');

        $field = new xmldb_field('filename', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'profile');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('classname', XMLDB_TYPE_CHAR, '150', null, null, null, null, 'filename');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('static', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'classname');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Main savepoint reached
        upgrade_main_savepoint($result, 2010012600);
    }

    if ($result && $oldversion < 2010012900) {

    /// Define table mnet_remote_rpc to be created
        $table = new xmldb_table('mnet_remote_rpc');

    /// Adding fields to table mnet_remote_rpc
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('functionname', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null);
        $table->add_field('xmlrpcpath', XMLDB_TYPE_CHAR, '80', null, XMLDB_NOTNULL, null, null);

    /// Adding keys to table mnet_remote_rpc
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Conditionally launch create table for mnet_remote_rpc
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


    /// Define table mnet_remote_service2rpc to be created
        $table = new xmldb_table('mnet_remote_service2rpc');

    /// Adding fields to table mnet_remote_service2rpc
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('serviceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('rpcid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

    /// Adding keys to table mnet_remote_service2rpc
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    /// Adding indexes to table mnet_remote_service2rpc
        $table->add_index('rpcid_serviceid', XMLDB_INDEX_UNIQUE, array('rpcid', 'serviceid'));

    /// Conditionally launch create table for mnet_remote_service2rpc
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


    /// Rename field function_name on table mnet_rpc to functionname
        $table = new xmldb_table('mnet_rpc');
        $field = new xmldb_field('function_name', XMLDB_TYPE_CHAR, '40', null, XMLDB_NOTNULL, null, null, 'id');

    /// Launch rename field function_name
        $dbman->rename_field($table, $field, 'functionname');


    /// Rename field xmlrpc_path on table mnet_rpc to xmlrpcpath
        $table = new xmldb_table('mnet_rpc');
        $field = new xmldb_field('xmlrpc_path', XMLDB_TYPE_CHAR, '80', null, XMLDB_NOTNULL, null, null, 'function_name');

    /// Launch rename field xmlrpc_path
        $dbman->rename_field($table, $field, 'xmlrpcpath');


    /// Main savepoint reached
        upgrade_main_savepoint($result, 2010012900);
    }

    if ($result && $oldversion < 2010012901) {

        /// Define field plugintype to be added to mnet_remote_rpc
        $table = new xmldb_table('mnet_remote_rpc');
        $field = new xmldb_field('plugintype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'xmlrpcpath');

        /// Conditionally launch add field plugintype
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field pluginname to be added to mnet_remote_rpc
        $field = new xmldb_field('pluginname', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null, 'plugintype');

    /// Conditionally launch add field pluginname
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        /// Main savepoint reached
        upgrade_main_savepoint($result, 2010012901);
    }

    if ($result && $oldversion < 2010012902) {

    /// Define field enabled to be added to mnet_remote_rpc
        $table = new xmldb_table('mnet_remote_rpc');
        $field = new xmldb_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null, 'pluginname');

    /// Conditionally launch add field enabled
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        /// Main savepoint reached
        upgrade_main_savepoint($result, 2010012902);
    }

    /// MDL-17863. Increase the portno column length on mnet_host to handle any port number
    if ($result && $oldversion < 2010020100) {
    /// Changing precision of field portno on table mnet_host to (5)
        $table = new xmldb_table('mnet_host');
        $field = new xmldb_field('portno', XMLDB_TYPE_INTEGER, '5', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'transport');

    /// Launch change of precision for field portno
        $dbman->change_field_precision($table, $field);

        upgrade_main_savepoint($result, 2010020100);
    }

    if ($result && $oldversion < 2010020300) {

    /// Define field timecreated to be added to user
        $table = new xmldb_table('user');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'trackforums');

        if (!$dbman->field_exists($table, $field)) {
        /// Launch add field timecreated
            $dbman->add_field($table, $field);

            $DB->execute("UPDATE {user} SET timecreated = firstaccess");

            $sql = "UPDATE {user} SET timecreated = " . time() ." where timecreated = 0";
            $DB->execute($sql);
        }
        upgrade_main_savepoint($result, 2010020300);
    }

    // MDL-21407. Trim leading spaces from default tex latexpreamble causing problems under some confs
    if ($result && $oldversion < 2010020301) {
        if ($preamble = $CFG->filter_tex_latexpreamble) {
            $preamble = preg_replace('/^ +/m', '', $preamble);
            set_config('filter_tex_latexpreamble', $preamble);
        }
        upgrade_main_savepoint($result, 2010020301);
    }

    if ($result && $oldversion < 2010021400) {
    /// Changes to modinfo mean we need to rebuild course cache
        require_once($CFG->dirroot . '/course/lib.php');
        rebuild_course_cache(0, true);
        upgrade_main_savepoint($result, 2010021400);
    }

    if ($result && $oldversion < 2010021800) {
        $DB->set_field('mnet_application', 'sso_jump_url', '/auth/mnet/jump.php', array('name' => 'moodle'));
        upgrade_main_savepoint($result, 2010021800);
    }

    if ($result && $oldversion < 2010031600) {
        //create the ratings table (replaces module specific ratings implementations)
        $table = new xmldb_table('ratings');

    /// Adding fields to table ratings
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('scaleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('rating', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

    /// Adding keys to table ratings
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('contextid', XMLDB_KEY_FOREIGN, array('contextid'), 'context', array('id'));
        $table->add_key('userid', XMLDB_KEY_FOREIGN, array('userid'), 'user', array('id'));

    /// Adding indexes to table ratings
        $table->add_index('itemid', XMLDB_INDEX_NOTUNIQUE, array('itemid'));

    /// Create table for ratings
        $dbman->create_table($table);

        //migrate ratings out of the modules into the central ratings table

        //migrate forumratings
        //forum ratings only have a single time column so use it for both time created and modified
        $ratingssql = 'select r.id as rid, r.post as itemid, r.rating, r.userid, r.time as timecreated, r.time as timemodified, f.scale, f.id as mid from {forum_ratings} r
inner join {forum_posts} p on p.id=r.post
inner join {forum_discussions} d on d.id=p.discussion
inner join {forum} f on f.id=d.forum';
        echo "migrating forum ratings<br>";
        $result = $result && upgrade_module_ratings($ratingssql,'forum');
        
        //migrate glossary_ratings
        //glossary ratings only have a single time column so use it for both time created and modified
        $ratingssql = 'select r.id as rid, r.entryid as itemid, r.rating, r.userid, r.time as timecreated, r.time as timemodified, g.id as mid, g.scale
from {glossary_ratings} r inner join {glossary_entries} ge on ge.id=r.entryid
inner join {glossary} g on g.id=ge.glossaryid';
        echo "migrating glossary ratings<br>";
        $result = $result && upgrade_module_ratings($ratingssql,'glossary');
        
        //migrate data_ratings
        //data ratings didnt store time created and modified so Im using the times from the record the rating was attached to
        $ratingssql = 'select r.id as rid, r.recordid as itemid, r.rating, r.userid, re.timecreated, re.timemodified, d.scale, d.id as mid
from {data_ratings} r inner join {data_records} re on r.recordid=re.id
inner join {data} d on d.id=re.dataid';
        echo "migrating data ratings<br>";
        $result = $result && upgrade_module_ratings($ratingssql,'data');

        //add assesstimestart and assesstimefinish columns to data
        $table = new xmldb_table('data');
        $field = new xmldb_field('assesstimestart');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'assessed');
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('assesstimefinish');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'assesstimestart');
            $dbman->add_field($table, $field);
        }

        //todo set permissions based on current value of glossary.assessed
        
        //todo drop forum_ratings, data_ratings and glossary_ratings

        upgrade_main_savepoint($result, 2010031600);
    }

    return $result;
}

//TODO: Before 2.0 release
// 1/ remove the automatic enabling of completion lib if debug enabled ( in 2008121701 block)
// 2/ move 2009061300 block to the top of the file so that we may log upgrade queries
// 3/ force admin password change if salt not set, to be done after planned role changes
