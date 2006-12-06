<?PHP  //$Id$

// MySQL commands for upgrading this enrolment module

function enrol_authorize_upgrade($oldversion=0) {
    global $CFG, $THEME, $db;
    require_once("$CFG->dirroot/enrol/authorize/const.php");

    $result = true;

    if ($oldversion < 2005071600) {
        // Be sure, only last 4 digit is inserted.
        table_column('enrol_authorize', 'cclastfour', 'cclastfour', 'integer', '4', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'courseid', 'courseid', 'integer', '10', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'userid', 'userid', 'integer', '10', 'unsigned', '0', 'not null');
        // Add some indexes for speed.
        execute_sql("ALTER TABLE `{$CFG->prefix}enrol_authorize` ADD INDEX courseid(courseid)", false);
        execute_sql("ALTER TABLE `{$CFG->prefix}enrol_authorize` ADD INDEX userid(userid)", false);
    }

    if ($oldversion && $oldversion < 2005071602) {
        notify("If you are using the authorize.net enrolment plugin for credit card
                handling, please ensure that you have turned loginhttps ON in Admin >> Variables >> Security.");
    }

    if ($oldversion < 2005112100) {
        table_column('enrol_authorize', '', 'authcode', 'varchar', '6', '', '', '', 'avscode'); // CAPTURE_ONLY
        table_column('enrol_authorize', '', 'status', 'integer', '10', 'unsigned', '0', 'not null', 'transid');
        table_column('enrol_authorize', '', 'timecreated', 'integer', '10', 'unsigned', '0', 'not null', 'status');
        table_column('enrol_authorize', '', 'timeupdated', 'integer', '10', 'unsigned', '0', 'not null', 'timecreated');
        // status index for speed.
        execute_sql("ALTER TABLE `{$CFG->prefix}enrol_authorize` ADD INDEX status(status)");
        // defaults.
        $status = AN_STATUS_AUTH | AN_STATUS_CAPTURE;
        execute_sql("UPDATE {$CFG->prefix}enrol_authorize SET status='$status' WHERE transid<>'0'", false);
        $timenow = time();
        execute_sql("UPDATE {$CFG->prefix}enrol_authorize SET timecreated='$timenow', timeupdated='$timenow'", false);
    }

    if ($oldversion < 2005121200) {
        // new fields for refund and sales reports.
        $defaultcurrency = empty($CFG->enrol_currency) ? 'USD' : $CFG->enrol_currency;
        table_column('enrol_authorize', '', 'amount', 'varchar', '10', '', '0', 'not null', 'timeupdated');
        table_column('enrol_authorize', '', 'currency', 'varchar', '3', '', $defaultcurrency, 'not null', 'amount');
        modify_database("","CREATE TABLE prefix_enrol_authorize_refunds (
          `id` int(10) unsigned NOT NULL auto_increment,
          `orderid` int(10) unsigned NOT NULL default 0,
          `refundtype` int(1) unsigned NOT NULL default 0,
          `amount` varchar(10) NOT NULL default '',
          `transid` int(10) unsigned NULL default 0,
          PRIMARY KEY (`id`),
          KEY `orderid` (`orderid`));");
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
        table_column('enrol_authorize_refunds', 'refundtype', 'status', 'integer', '1', 'unsigned', '0', 'not null');
        table_column('enrol_authorize_refunds', '', 'settletime', 'integer', '10', 'unsigned', '0', 'not null', 'transid');
        table_column('enrol_authorize', 'timeupdated', 'settletime', 'integer', '10', 'unsigned', '0', 'not null');
        $status = AN_STATUS_AUTH | AN_STATUS_CAPTURE;
        if ($settlements = get_records_select('enrol_authorize', "status='$status'", '', 'id, settletime')) {
            include_once("$CFG->dirroot/enrol/authorize/authorizenetlib.php");
            foreach ($settlements as $settlement) {
                execute_sql("UPDATE {$CFG->prefix}enrol_authorize SET settletime = '" .
                authorize_getsettletime($settlement->settletime) . "' WHERE id = '$settlement->id'", false);
            }
        }
    }

    if ($oldversion < 2005122800) { // no need anymore some fields.
        execute_sql("ALTER TABLE {$CFG->prefix}enrol_authorize DROP ccexp", false);
        execute_sql("ALTER TABLE {$CFG->prefix}enrol_authorize DROP cvv", false);
        execute_sql("ALTER TABLE {$CFG->prefix}enrol_authorize DROP avscode", false);
        execute_sql("ALTER TABLE {$CFG->prefix}enrol_authorize DROP authcode", false);
    }

    if ($oldversion < 2006010200) { // rename an_review_day
        if (isset($CFG->an_review_day)) {
            set_config('an_capture_day', $CFG->an_review_day);
            delete_records('config', 'name', 'an_review_day');
        }
    }

    if ($oldversion < 2006020100) { // rename an_cutoff_hour and an_cutoff_min to an_cutoff
        if (isset($CFG->an_cutoff_hour) && isset($CFG->an_cutoff_min)) {
            $an_cutoff_hour = intval($CFG->an_cutoff_hour);
            $an_cutoff_min = intval($CFG->an_cutoff_min);
            $an_cutoff = ($an_cutoff_hour * 60) + $an_cutoff_min;
            if (set_config('an_cutoff', $an_cutoff)) {
                delete_records('config', 'name', 'an_cutoff_hour');
                delete_records('config', 'name', 'an_cutoff_min');
            }
        }
    }

    if ($oldversion < 2006021500) { // transid is int
        table_column('enrol_authorize', 'transid', 'transid', 'integer', '10', 'unsigned', '0', 'not null');
    }

    if ($oldversion < 2006021501) { // delete an_nextmail record from config_plugins table
        delete_records('config_plugins', 'name', 'an_nextmail');
    }

    if ($oldversion < 2006021531) { // Don't show authorize.net login info at config page
        if (isset($CFG->an_login)) {
            if (!empty($CFG->an_login)) {
                set_config('an_login', rc4encrypt($CFG->an_login), 'enrol/authorize');
            }
            delete_records('config', 'name', 'an_login');
        }
        if (isset($CFG->an_tran_key)) {
            if (!empty($CFG->an_tran_key)) {
                set_config('an_tran_key', rc4encrypt($CFG->an_tran_key), 'enrol/authorize');
            }
            delete_records('config', 'name', 'an_tran_key');
        }
        if (isset($CFG->an_password)) {
            if (!empty($CFG->an_password)) {
                set_config('an_password', rc4encrypt($CFG->an_password), 'enrol/authorize');
            }
            delete_records('config', 'name', 'an_password');
        }
    }

    return $result;
}

?>
