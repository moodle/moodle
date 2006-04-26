<?PHP  //$Id$

// MySQL commands for upgrading this enrolment module

function authorize_upgrade($oldversion=0) {
    global $CFG, $THEME, $db;

    $result = true;

    if ($oldversion == 0) {
        $result = modify_database("$CFG->dirroot/enrol/authorize/db/mysql.sql");
        return $result; // SQL file contains all upgrades.
    } else if (!in_array($CFG->prefix . 'enrol_authorize', $db->MetaTables('TABLES'))) {
        $result = modify_database("$CFG->dirroot/enrol/authorize/db/mysql.sql");
        return $result; // SQL file contains all upgrades.
    }

    if ($oldversion < 2005071601) {
        // Be sure, only last 4 digit is inserted.
        table_column('enrol_authorize', 'cclastfour', 'cclastfour', 'integer', '4', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'courseid', 'courseid', 'integer', '10', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'userid', 'userid', 'integer', '10', 'unsigned', '0', 'not null');
        // Add some indexes for speed.
        execute_sql(" ALTER TABLE `{$CFG->prefix}enrol_authorize` ADD INDEX courseid(courseid) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}enrol_authorize` ADD INDEX userid(userid) ");
    }

    if ($oldversion && $oldversion < 2005071602) {
        notify("If you are using the authorize.net enrolment plugin for credit card 
                handling, please ensure that you have turned loginhttps ON in Admin >> Variables >> Security.");
    }

    return $result;
}

?>
