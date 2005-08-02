<?PHP  //$Id$

// MySQL commands for upgrading this enrolment module

function authorize_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($oldversion == 0) {
        modify_database("$CFG->dirroot/enrol/authorize/db/mysql.sql");
    }

    if ($oldversion < 2005071600) {
        // Be sure, only last 4 digit is inserted.
        table_column('enrol_authorize', 'cclastfour', 'cclastfour', 'integer', '4', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'courseid', 'courseid', 'integer', '10', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'userid', 'userid', 'integer', '10', 'unsigned', '0', 'not null');
	// Add some indexes for speed.
        execute_sql(" ALTER TABLE `{$CFG->prefix}enrol_authorize` ADD INDEX courseid(courseid) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}enrol_authorize` ADD INDEX userid(userid) ");
    }

    return $result;

}

?>
