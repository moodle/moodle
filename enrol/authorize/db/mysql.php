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

    if ($oldversion < 2005112100) {
        include_once("$CFG->dirroot/enrol/authorize/enrol.php");

        table_column('enrol_authorize', '', 'authcode', 'varchar', '6', '', '', '', 'avscode'); // CAPTURE_ONLY
        table_column('enrol_authorize', '', 'status', 'integer', '10', 'unsigned', '0', 'not null', 'transid');
        table_column('enrol_authorize', '', 'timecreated', 'integer', '10', 'unsigned', '0', 'not null', 'status');
        table_column('enrol_authorize', '', 'timeupdated', 'integer', '10', 'unsigned', '0', 'not null', 'timecreated');
        // status index for speed.
        execute_sql(" ALTER TABLE `{$CFG->prefix}enrol_authorize` ADD INDEX status(status) ");
        // defaults.
        $timenow = time();
        $status = AN_STATUS_AUTH | AN_STATUS_CAPTURE;
        execute_sql(" UPDATE {$CFG->prefix}enrol_authorize SET timecreated='$timenow', timeupdated='$timenow', status='$status' ", false);
    }

    if ($oldversion < 2005121200) {
        // new fields for refund and sales reports.
        $defaultcurrency = empty($CFG->enrol_currency) ? 'USD' : $CFG->enrol_currency;
        table_column('enrol_authorize', '', 'amount', 'varchar', '10', '', '0', 'not null', 'timeupdated');
        table_column('enrol_authorize', '', 'currency', 'char', '3', '', $defaultcurrency, 'not null', 'amount');
        modify_database("","CREATE TABLE prefix_enrol_authorize_refunds (
          `id` int(10) unsigned NOT NULL auto_increment,
          `orderid` int(10) unsigned NOT NULL default 0,
          `refundtype` int(1) unsigned NOT NULL default 0,
          `amount` varchar(10) NOT NULL default '',
          `transid` int(10) unsigned NULL default 0,
          PRIMARY KEY (`id`),
          KEY `orderid` (`orderid`)
          );");
        // defaults.
        if ($courses = get_records_select('course', '', '', 'id, cost, currency')) {
            foreach ($courses as $course) {
                execute_sql("UPDATE {$CFG->prefix}enrol_authorize
                             SET amount = '$course->cost', currency = '$course->currency'
                             WHERE courseid = '$course->id'", false);
            }
        }
    }

    if ($oldversion < 2005122200) { // settletime
        include_once("$CFG->dirroot/enrol/authorize/enrol.php");

        table_column('enrol_authorize_refunds', 'refundtype', 'status', 'integer', '1', 'unsigned', '0', 'not null');
        table_column('enrol_authorize_refunds', '', 'settletime', 'integer', '10', 'unsigned', '0', 'not null', 'transid');

        table_column('enrol_authorize', 'timeupdated', 'settletime', 'integer', '10', 'unsigned', '0', 'not null');
        $status = AN_STATUS_AUTH | AN_STATUS_CAPTURE;
        if ($settlements = get_records_select('enrol_authorize', "status='$status'", '', 'id, settletime')) {
            include_once("$CFG->dirroot/enrol/authorize/action.php");
            foreach ($settlements as $settlement) {
                execute_sql("UPDATE {$CFG->prefix}enrol_authorize SET settletime = '" .
                getsettletime($settlement->settletime) . "' WHERE id = '$settlement->id'", false);
            }
        }
    }

    return $result;
}

?>
