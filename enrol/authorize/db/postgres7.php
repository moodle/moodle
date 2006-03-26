<?PHP  //$Id$

// PostgreSQL commands for upgrading this enrolment module

function authorize_upgrade($oldversion=0) {
    global $CFG, $THEME, $db;

    $result = true;

    if (!$tables = $db->MetaColumns($CFG->prefix . 'enrol_authorize')) {
        $installfirst = true;
    }

    if ($oldversion == 0 || !empty($installfirst)) {
        modify_database("$CFG->dirroot/enrol/authorize/db/postgres7.sql");
    }

    if ($oldversion < 2005071601) {
        // Be sure, only last 4 digit is inserted.
        table_column('enrol_authorize', 'cclastfour', 'cclastfour', 'integer', '4', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'courseid', 'courseid', 'integer', '10', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'userid', 'userid', 'integer', '10', 'unsigned', '0', 'not null');
        // Add some indexes for speed.
        modify_database('',"CREATE INDEX prefix_enrol_authorize_courseid_idx ON prefix_enrol_authorize (courseid);");
        modify_database('',"CREATE INDEX prefix_enrol_authorize_userid_idx ON prefix_enrol_authorize (userid);");
    }

    if ($oldversion < 2005071602) {
        notify("If you are using the authorize.net enrolment plugin for credit card 
                handling, please ensure that you have turned loginhttps ON in Admin >> Variables >> Security.");
    }
    
    return $result;
}
