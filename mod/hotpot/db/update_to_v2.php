<?PHP
if (file_exists("$CFG->dirroot/lib/ddllib.php")) {
    // Moodle 1.8+
    include_once "$CFG->dirroot/lib/ddllib.php";
}

function hotpot_update_to_v2_2() {
    global $CFG;
    $ok = true;

    // remove the index on hotpot_questions.name
    $table = 'hotpot_questions';
    $field = 'name';
    if (strtolower($CFG->dbfamily)=='postgres') {
        $index = "{$CFG->prefix}{$table}_{$field}_idx";
    } else {
        $index = "{$table}_{$field}_idx";
	}
    hotpot_db_delete_index("{$CFG->prefix}$table", $index);

    // add new hotpot_questions.md5key field (and index)
    $table = 'hotpot_questions';
    $field = 'md5key';
    $ok = $ok && hotpot_db_update_field_type($table, '', $field, 'VARCHAR', 32, '', 'NOT NULL', '');
    $ok = $ok && hotpot_db_add_index($table, $field);

    // add new values hotpot_questions.md5key
    $table = 'hotpot_questions';
    if ($records = get_records($table)) {
        foreach ($records as $record) {
            $ok = $ok && set_field($table, 'md5key', md5($record->name), 'id', $record->id);
        }
    }

    // remove the index on hotpot_strings.string
    $table = 'hotpot_strings';
    $field = 'string';
    if (strtolower($CFG->dbfamily)=='postgres') {
        $index = "{$CFG->prefix}{$table}_{$field}_idx";
    } else {
        $index = "{$table}_{$field}_idx";
	}
    hotpot_db_delete_index("{$CFG->prefix}$table", $index);

    // add new hotpot_strings.md5key field (and index)
    $table = 'hotpot_strings';
    $field = 'md5key';
    $ok = $ok && hotpot_db_update_field_type($table, '', $field, 'VARCHAR', 32, '', 'NOT NULL', '');
    $ok = $ok && hotpot_db_add_index($table, $field);

    // add new values hotpot_strings.md5key
    $table = 'hotpot_strings';
    if ($records = get_records($table)) {
        foreach ($records as $record) {
            $ok = $ok && set_field($table, 'md5key', md5($record->string), 'id', $record->id);
        }
    }

    return $ok;
}
function hotpot_update_to_v2_1_21() {
    global $CFG;
    $ok = true;

    if (strtolower($CFG->dbfamily)=='postgres') {
        // ensure setting of default values on certain fields
        // this was originally done in postgres7.php, but was found to be incompatible with PG7 :-(
        $table="hotpot";
        execute_sql("UPDATE {$CFG->prefix}$table SET studentfeedbackurl = '' WHERE studentfeedbackurl IS NULL");
        $ok = $ok && hotpot_db_update_field_type($table, '', 'studentfeedbackurl', 'VARCHAR', 255, '',         'NOT NULL', '');
        $ok = $ok && hotpot_db_update_field_type($table, '', 'studentfeedback',    'INTEGER', 4,   'UNSIGNED', 'NOT NULL', 0);
        $ok = $ok && hotpot_db_update_field_type($table, '', 'clickreporting',     'INTEGER', 4,   'UNSIGNED', 'NOT NULL', 0);

        $table="hotpot_attempts";
        $ok = $ok && hotpot_db_update_field_type($table, '', 'score',     'INTEGER', 4, 'UNSIGNED', 'NOT NULL', 0);
        $ok = $ok && hotpot_db_update_field_type($table, '', 'penalties', 'INTEGER', 4, 'UNSIGNED', 'NOT NULL', 0);
        $ok = $ok && hotpot_db_update_field_type($table, '', 'status',    'INTEGER', 4, 'UNSIGNED', 'NOT NULL', 1);

        $table="hotpot_questions";
        $ok = $ok && hotpot_db_update_field_type($table, '', 'type',      'INTEGER', 4, 'UNSIGNED', 'NOT NULL', 0);

        $table="hotpot_responses";
        $ok = $ok && hotpot_db_update_field_type($table, '', 'score',     'INTEGER', 4,   'UNSIGNED', 'NOT NULL', 0);
        $ok = $ok && hotpot_db_update_field_type($table, '', 'weighting', 'INTEGER', 4,   'UNSIGNED', 'NOT NULL', 0);
        $ok = $ok && hotpot_db_update_field_type($table, '', 'correct',   'VARCHAR', 255, '',         'NOT NULL', '');
        execute_sql("UPDATE {$CFG->prefix}$table SET wrong = '' WHERE wrong IS NULL");
        $ok = $ok && hotpot_db_update_field_type($table, '', 'wrong',     'VARCHAR', 255, '',         'NOT NULL', '');
        execute_sql("UPDATE {$CFG->prefix}$table SET ignored = '' WHERE ignored IS NULL");
        $ok = $ok && hotpot_db_update_field_type($table, '', 'ignored',   'VARCHAR', 255, '',         'NOT NULL', '');
        $ok = $ok && hotpot_db_update_field_type($table, '', 'hints',     'INTEGER', 4,   'UNSIGNED', 'NOT NULL', 0);
        $ok = $ok && hotpot_db_update_field_type($table, '', 'clues',     'INTEGER', 4,   'UNSIGNED', 'NOT NULL', 0);
        $ok = $ok && hotpot_db_update_field_type($table, '', 'checks',    'INTEGER', 4,   'UNSIGNED', 'NOT NULL', 0);

        $table="hotpot_strings";
        $ok = $ok && hotpot_db_update_field_type($table, '', 'string', 'TEXT', '', '', 'NOT NULL', '');
    }

    return $ok;
}
function hotpot_update_to_v2_1_18() {
    $ok = true;

    // remove all orphan records (there shouldn't be any, but if there are they can mess up the utfdbmigrate)

    $ok = $ok && hotpot_remove_orphans('hotpot_attempts', 'hotpot', 'hotpot');
    $ok = $ok && hotpot_remove_orphans('hotpot_questions', 'hotpot', 'hotpot');
    $ok = $ok && hotpot_remove_orphans('hotpot_responses', 'attempt', 'hotpot_attempts');
    $ok = $ok && hotpot_remove_orphans('hotpot_responses', 'question', 'hotpot_questions');
    $ok = $ok && hotpot_remove_orphans('hotpot_details', 'attempt', 'hotpot_attempts');

    // allow negative weighting and scores

    $ok = $ok && hotpot_denull_int_field('hotpot_responses', 'weighting', '6', false);
    $ok = $ok && hotpot_denull_int_field('hotpot_responses', 'score', '6', false);

    return $ok;
}
function hotpot_remove_orphans($secondarytable, $secondarykeyfield, $primarytable, $primarykeyfield='id') {
    global $CFG,$db;
    $ok = true;

    // save and switch off SQL message echo
    $debug = $db->debug;
    $db->debug = false;

    $records = get_records_sql("
        SELECT
            t2.$secondarykeyfield, t2.$secondarykeyfield
        FROM
            {$CFG->prefix}$secondarytable t2 LEFT JOIN {$CFG->prefix}$primarytable t1
            ON (t2.$secondarykeyfield = t1.id)
        WHERE
            t1.$primarykeyfield IS NULL
    ");

    // restore SQL message echo setting
    $db->debug = $debug;

    if ($records) {
        $keys = implode(',', array_keys($records));
        print "removing orphan record(s) from {$CFG->prefix}$secondarytable<br/>";
        $ok = $ok && execute_sql("DELETE FROM {$CFG->prefix}$secondarytable WHERE $secondarykeyfield IN ($keys)");
    }

    return $ok;
}
function hotpot_update_to_v2_1_17() {
    global $CFG;
    $ok = true;

    // convert and disable null values on certain numeric fields

    $ok = $ok && hotpot_denull_int_field('hotpot_attempts', 'starttime', '10');
    $ok = $ok && hotpot_denull_int_field('hotpot_attempts', 'endtime', '10');
    $ok = $ok && hotpot_denull_int_field('hotpot_attempts', 'score', '6');
    $ok = $ok && hotpot_denull_int_field('hotpot_attempts', 'penalties',  '6');
    $ok = $ok && hotpot_denull_int_field('hotpot_attempts', 'timestart', '10');
    $ok = $ok && hotpot_denull_int_field('hotpot_attempts', 'timefinish', '10');
    $ok = $ok && hotpot_denull_int_field('hotpot_attempts', 'clickreportid', '10');

    $ok = $ok && hotpot_denull_int_field('hotpot_questions', 'type', '4');
    $ok = $ok && hotpot_denull_int_field('hotpot_questions', 'text', '10');

    $ok = $ok && hotpot_denull_int_field('hotpot_responses', 'weighting', '6', false);
    $ok = $ok && hotpot_denull_int_field('hotpot_responses', 'score', '6', false);
    $ok = $ok && hotpot_denull_int_field('hotpot_responses', 'hints', '6');
    $ok = $ok && hotpot_denull_int_field('hotpot_responses', 'clues', '6');
    $ok = $ok && hotpot_denull_int_field('hotpot_responses', 'checks', '6');
    return $ok;
}
function hotpot_denull_int_field($table, $field, $size, $unsigned=true) {
    global $CFG;
    $ok = true;

    $ok = $ok && execute_sql("UPDATE {$CFG->prefix}$table SET $field=0 WHERE $field IS NULL", false);
    if ($unsigned) {
        $ok = $ok && execute_sql("UPDATE {$CFG->prefix}$table SET $field=0 WHERE $field<0", false);
    }
    $ok = $ok && hotpot_db_update_field_type($table, $field, $field, 'INTEGER', $size, $unsigned, 'NOT NULL', 0);

    return $ok;
}
function hotpot_update_to_v2_1_16() {
    global $CFG;
    $ok = true;

    // remove the questions name index
    hotpot_db_delete_index("{$CFG->prefix}hotpot_questions", "hotpot_questions_name_idx");
    hotpot_db_delete_index("{$CFG->prefix}hotpot_questions", "{$CFG->prefix}hotpot_questions_name_idx");

    // make sure type of 'name' is a text field (not varchar 255)
    $ok = $ok && hotpot_db_update_field_type('hotpot_questions', 'name', 'name', 'TEXT',   '',  '', 'NOT NULL', '');

    if (strtolower($CFG->dbfamily)=='mysql') {

        // set default values on certain VARCHAR(255) fields
        $fields = array(
            'hotpot' => 'studentfeedbackurl',
            'hotpot_responses' => 'correct',
            'hotpot_responses' => 'wrong',
            'hotpot_responses' => 'ignored'
        );
        foreach ($fields as $table=>$field) {
            execute_sql("UPDATE {$CFG->prefix}$table SET $field='' WHERE $field IS NULL");
            $ok = $ok && hotpot_db_update_field_type($table, $field, $field, 'VARCHAR', 255, '', 'NOT NULL', '');
        }

        // remove $CFG->prefix from all index names
        $ok = $ok && hotpot_index_remove_prefix('hotpot_attempts', 'hotpot');
        $ok = $ok && hotpot_index_remove_prefix('hotpot_attempts', 'userid');
        $ok = $ok && hotpot_index_remove_prefix('hotpot_details', 'attempt');
        $ok = $ok && hotpot_index_remove_prefix('hotpot_questions', 'hotpot');
        $ok = $ok && hotpot_index_remove_prefix('hotpot_responses', 'attempt');
        $ok = $ok && hotpot_index_remove_prefix('hotpot_responses', 'question');
    }
    return $ok;
}
function hotpot_index_remove_prefix($table, $field) {
    global $CFG;
    hotpot_db_delete_index("{$CFG->prefix}$table", "{$CFG->prefix}{$table}_{$field}_idx");
    hotpot_db_delete_index("{$CFG->prefix}$table", "{$table}_{$field}_idx");
    return hotpot_db_add_index($table, $field);
}

function hotpot_update_to_v2_1_8() {
    global $CFG;
    $ok = true;
    if (strtolower($CFG->dbfamily)=='postgres') {
        // add, delete and rename certain fields and indexes
        // that were not correctly setup by postgres7.sql

        // hotpot
        $table = 'hotpot';
        if (hotpot_db_field_exists($table, 'microreporting')) {
            $ok = $ok && hotpot_db_update_field_type($table, 'microreporting', 'clickreporting', 'INTEGER', 4, 'UNSIGNED', 'NOT NULL', '0');
        }
    }
    return $ok;
}
function hotpot_update_to_v2_1_6() {
    global $CFG;
    $ok = true;

    if (strtolower($CFG->dbfamily)=='postgres') {
        // add, delete and rename certain fields and indexes
        // that were not correctly setup by postgres7.sql

        // hotpot
        $table = 'hotpot';
        if (hotpot_db_field_exists($table, 'studentfeedback') && !hotpot_db_field_exists($table, 'studentfeedbackurl')) {
            $ok = $ok && hotpot_db_update_field_type($table, 'studentfeedback', 'studentfeedbackurl', 'VARCHAR', 255, '', 'NULL');
            $ok = $ok && hotpot_db_update_field_type($table, '', 'studentfeedback', 'INTEGER', 4, 'UNSIGNED', 'NOT NULL', '0');
        }

        // hotpot_attempts
        $table = 'hotpot_attempts';
        $ok = $ok && hotpot_db_remove_field($table, 'groupid');
        if (hotpot_db_field_exists($table, 'microreportid') && !hotpot_db_field_exists($table, 'clickreportid')) {
            $ok = $ok && hotpot_db_update_field_type($table, 'microreportid', 'clickreportid', 'INTEGER', 10, 'UNSIGNED', 'NULL');
        }
    }

    return $ok;
}
function hotpot_update_to_v2_1_2() {
    global $CFG, $db;
    $ok = true;

    // save and switch off SQL message echo
    $debug = $db->debug;
    $db->debug = false;

    // extract info about attempts by each user on each hotpot (cases where
    // the user has only one attempt, or no "in progess" attempt are ignored)
    $rs = $db->Execute("
        SELECT userid, hotpot, COUNT(*), MIN(status)
        FROM {$CFG->prefix}hotpot_attempts
        GROUP BY userid, hotpot
        HAVING COUNT(*)>1 AND MIN(status)=1
    ");
    if ($rs && $rs->RecordCount()) {
        $records = $rs->GetArray();

        // start message to browser
        print "adjusting status of ".count($records)." &quot;in progress&quot; attempts ... ";

        // loop through records
        foreach ($records as $record) {

            // get all attempts by this user at this hotpot
            $attempts = get_records_sql("
                SELECT id, userid, hotpot, score, timestart, timefinish, status
                FROM {$CFG->prefix}hotpot_attempts
                WHERE userid = ".$record['userid']." AND hotpot=".$record['hotpot']."
                ORDER BY timestart DESC, id DESC
            ");

            unset($previous_timestart);

            foreach ($attempts as $attempt) {
                // if this attempt has a status of "in progress" and is not
                // the most recent one in the group, set the status to "abandoned"
                if ($attempt->status==1 && isset($previous_timestart)) {
                    $values = 'status=3';
                    if (empty($attempt->score)) {
                        $values .= ',score=0';
                    }
                    if (empty($attempt->timefinish)) {
                        $values .= ",timefinish=$previous_timestart";
                    }
                    execute_sql("UPDATE {$CFG->prefix}hotpot_attempts SET $values WHERE id=$attempt->id", false);
                    print ".";
                    hotpot_flush(300);
                }
                $previous_timestart = $attempt->timestart;
            } // end foreach $attempts
        } // end foreach $records

        // finish message to browser
        print $ok ? get_string('success') : 'failed';
        print "<br />\n";
    }

    // restore SQL message echo setting
    $db->debug = $debug;

    return $ok;
}
function hotpot_update_to_v2_1() {
    global $CFG, $db;
    $ok = true;
    // hotpot_questions: reduce size of "type" field to "4"
    $ok = $ok && hotpot_db_update_field_type('hotpot_questions', 'type', 'type', 'INTEGER', 4,  'UNSIGNED', 'NULL');
    // hotpot_questions: change type of "name" field to "text"
    $ok = $ok && hotpot_db_update_field_type('hotpot_questions', 'name', 'name', 'TEXT',   '',  '', 'NOT NULL', '');
    // hotpot_questions: nullify empty and non-numeric (shouldn't be any) values in "text" field
    switch (strtolower($CFG->dbfamily)) {
        case 'mysql' :
            $NOT_REGEXP = 'NOT REGEXP';
        break;
        case 'postgres' :
            $NOT_REGEXP = '!~';
        break;
        default:
            $NOT_REGEXP = '';
        break;
    }
    if ($NOT_REGEXP) {
        $ok = $ok && execute_sql("UPDATE {$CFG->prefix}hotpot_questions SET text=NULL WHERE text $NOT_REGEXP '^[0-9]+$'");
    }
    // hotpot_questions: change type of "text" field to "INT(10)"
    $ok = $ok && hotpot_db_update_field_type('hotpot_questions', 'text', 'text', 'INTEGER', 10, 'UNSIGNED', 'NULL');
    // hotpot_attempts
    // hotpot_attempts: move "details" to separate table
    $table = 'hotpot_details';
    if (hotpot_db_table_exists($table)) {
        // do nothing
    } else {
        $ok = $ok && hotpot_create_table($table);
        switch (strtolower($CFG->dbfamily)) {
            case 'mysql' :
            case 'postgres' :
                $sql = "
                    INSERT INTO {$CFG->prefix}$table (attempt, details)
                    SELECT a.id AS attempt, a.details AS details
                        FROM {$CFG->prefix}hotpot_attempts a
                        WHERE
                            a.details IS NOT NULL AND a.details <> ''
                            AND a.details LIKE '<?xml%' AND a.details LIKE '%</hpjsresult>'
                ";
            break;
            default:
                $sql = '';
            break;
        }
        if ($sql) {
            $ok = $ok && execute_sql($sql);
        }
    }
    // hotpot_attempts: remove the "details" field
    $ok = $ok && hotpot_db_remove_field('hotpot_attempts', 'details');
    // hotpot_attempts: create and set status field (1=in-progress, 2=timed-out, 3=abandoned, 4=completed)
    $ok = $ok && hotpot_db_update_field_type('hotpot_attempts', '', 'status', 'INTEGER', 4, 'UNSIGNED', 'NOT NULL', 1);
    $ok = $ok && execute_sql("UPDATE {$CFG->prefix}hotpot_attempts SET status=1 WHERE timefinish=0 AND SCORE IS NULL");
    $ok = $ok && execute_sql("UPDATE {$CFG->prefix}hotpot_attempts SET status=3 WHERE timefinish>0 AND SCORE IS NULL");
    $ok = $ok && execute_sql("UPDATE {$CFG->prefix}hotpot_attempts SET status=4 WHERE timefinish>0 AND SCORE IS NOT NULL");
    // hotpot_attempts: create and set clickreport fields
    $ok = $ok && hotpot_db_update_field_type('hotpot', '', 'clickreporting', 'INTEGER', 4, 'UNSIGNED', 'NOT NULL', 0);
    $ok = $ok && hotpot_db_update_field_type('hotpot_attempts', '', 'clickreportid', 'INTEGER', 10, 'UNSIGNED', 'NULL');
    $ok = $ok && execute_sql("UPDATE {$CFG->prefix}hotpot_attempts SET clickreportid=id WHERE clickreportid IS NULL");
    // hotpot_attempts: create and set studentfeedback field (0=none, 1=formmail, 2=moodleforum, 3=moodlemessaging)
    $ok = $ok && hotpot_db_update_field_type('hotpot', '', 'studentfeedback', 'INTEGER', 4, 'UNSIGNED', 'NOT NULL', '0');
    $ok = $ok && hotpot_db_update_field_type('hotpot', '', 'studentfeedbackurl', 'VARCHAR', 255, '', 'NULL');
    // add indexes
    $ok = $ok && hotpot_db_add_index('hotpot_attempts', 'hotpot');
    $ok = $ok && hotpot_db_add_index('hotpot_attempts', 'userid');
    $ok = $ok && hotpot_db_add_index('hotpot_details', 'attempt');
    $ok = $ok && hotpot_db_add_index('hotpot_questions', 'hotpot');
    $ok = $ok && hotpot_db_add_index('hotpot_responses', 'attempt');
    $ok = $ok && hotpot_db_add_index('hotpot_responses', 'question');
    // hotpot_string: correct double-encoded HTML entities
    $ok = $ok && execute_sql("
        UPDATE {$CFG->prefix}hotpot_strings
        SET string = REPLACE(string, '&amp;','&')
        WHERE string LIKE '%&amp;#%'
        AND (string LIKE '<' OR string LIKE '>')
    ");
    // hotpot_question: remove questions which refer to deleted hotpots
    if ($ok) {
        // try and get all hotpot records
        if ($records = get_records('hotpot')) {
            $ids = implode(',', array_keys($records));
            $sql = "DELETE FROM {$CFG->prefix}hotpot_questions WHERE hotpot NOT IN ($ids)";
        } else {
            // remove all question records (because there are no valid hotpot ids)
            $sql = "TRUNCATE {$CFG->prefix}hotpot_questions";
        }
        print "Removing unused question records ...";
        execute_sql($sql);
    }
    if ($ok) {
        // remove old 'v6' templates folder (replaced by 'template' folder)
        $ds = DIRECTORY_SEPARATOR;
        $dir = "mod{$ds}hotpot{$ds}v6";
        print "removing old templates ($dir) ... ";
        if (hotpot_rm("$CFG->dirroot{$ds}$dir", false)) {
            print get_string('success');
        } else {
            print "failed<br/>Please remove '$CFG->dirroot{$ds}$dir' manually";
        }
        print "<br />\n";
    }
    return $ok;
}
function hotpot_update_to_v2_from_v1() {
    global $CFG;
    $ok = true;
    // remove, alter and add fields in database
    $table = 'hotpot';
    if (hotpot_db_table_exists($table)) {
        $ok = $ok && hotpot_update_fields($table);
    } else {
        $ok = $ok && hotpot_create_table($table);
    }
    $table = 'hotpot_attempts';
    $oldtable = 'hotpot_events';
    if (hotpot_db_table_exists($oldtable)) {
        $ok = $ok && hotpot_update_fields($oldtable);
        $ok = $ok && hotpot_db_append_table($oldtable, $table);
    } else {
        $ok = $ok && hotpot_create_table($table);
    }
    // create new tables (from mysql.sql)
    $ok = $ok && hotpot_create_table('hotpot_questions');
    $ok = $ok && hotpot_create_table('hotpot_responses');
    $ok = $ok && hotpot_create_table('hotpot_strings');
    // remove redundant scripts
    $files = array('coursefiles.php', 'details.php', 'dummy.html', 'hotpot.php', 'hotpot2db.php');
    foreach ($files as $file) {
        $filepath = "$CFG->dirroot/mod/hotpot/$file";
        if (file_exists($filepath)) {
            @unlink($filepath); // don't worry about errors
        }
    }
    return $ok;
}
function hotpot_update_to_v2_from_hotpotatoes() {
    global $CFG;
    $ok = true; // hope for the best!
    // check we have the minimum required hotpot module
    $minimum = 2005031400;
    $module = get_record("modules", "name", "hotpot");
    if (empty($module) || $module->version<$minimum) {
        if ($module) {
            print ("<p>The update to the HotPotatoes module requires at least version $minimum of the HotPot module.</p>");
            print ("<p>The current version of the HotPot module on this site is $module->version.</p>");
        }
        print ("<p>Please install the latest version of the HotPot module and then try the update again.</p>");
        $ok = false;
    } else {
        // arrays to map foreign keys
        $new = array();
        $new['hotpot'] = array();
        $new['attempt'] = array();
        $new['question'] = array();
        $new['string'] = array();
        // save and switch off SQL message echo
        global $db;
        $debug = $db->debug;
        $db->debug = false;
        // import hotpotatoes (and save old ids)
        $ok = $ok && hotpot_update_fields('hotpotatoes');
        $ok = $ok && hotpot_transfer_records('hotpotatoes', 'hotpot', array(), 'hotpot', $new);
        // update course modules and logs
        $ok = $ok && hotpot_update_course_modules('hotpotatoes', 'hotpot', $new);
        // import hotpotatoes_strings (and save old ids)
        $ok = $ok && hotpot_transfer_records('hotpotatoes_strings', 'hotpot_strings', array(), 'string', $new);
        // import hotpotatoes_attempts (and save old ids)
        $ok = $ok && hotpot_transfer_records('hotpotatoes_attempts', 'hotpot_attempts', array('hotpotatoes'=>'hotpot'), 'attempt', $new);
        // import hotpotatoes_questions (and save old ids)
        $ok = $ok && hotpot_transfer_records('hotpotatoes_questions', 'hotpot_questions', array('hotpotatoes'=>'hotpot'), 'question', $new);
        // import hotpotatoes_responses
        $ok = $ok && hotpot_transfer_records('hotpotatoes_responses', 'hotpot_responses', array('attempt'=>'attempt', 'question'=>'question'), 'response', $new);
        // restore SQL message echo setting
        $db->debug = $debug;
        // remove the hotpotatoes tables, if the update went ok
        if ($ok) {
        //  hotpot_db_remove_table('hotpotatoes');
        //  hotpot_db_remove_table('hotpotatoes_attempts');
        //  hotpot_db_remove_table('hotpotatoes_questions');
        //  hotpot_db_remove_table('hotpotatoes_responses');
        //  hotpot_db_remove_table('hotpotatoes_strings');
        }
        // hide the hotpotatoes module (see admin/modules.php))
        if ($ok && ($module = get_record("modules", "name", "hotpotatoes"))) {
            set_field("modules", "visible", "0", "id", $module->id);
            print '<p>All HotPotatoes activities have been imported to the HotPot module.<br />'."\n";
            print 'The HotPotatoes module has been hidden and can safely be deleted from this Moodle site.<br />'."\n";
            print ' &nbsp; &nbsp; <a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/modules.php">Configuration -> Modules</A>, then click &quot;Delete&quot; for &quot;Hot Potatoes XML Quiz&quot;</p>'."\n";
        }
    }
    if ($ok) {
        print '<p align="center">Thank you for using the HotPotatoes module.<br />';
        print 'The HotPotatoes module has been replaced by<br />version 2 of the HotPot module. Enjoy!</p>';
    }
    return $ok;
}
function hotpot_create_table($table) {
    global $CFG;

    static $sql;
    static $xmldb_file;

    // check table does not already exist
    if (hotpot_db_table_exists($table)) {
        return true;
    }

    if (! isset($xmldb_file)) { // first time only
        if (class_exists('XMLDBFile')) {
            $xmldb_file = new XMLDBFile("$CFG->dirroot/mod/hotpot/db/install.xml");
            if (! $xmldb_file->fileExists() || !$xmldb_file->loadXMLStructure() || !$xmldb_file->isLoaded()) {
                unset($xmldb_file);
            }
        }
        if (empty($xmldb_file)) {
            $xmldb_file = false;
        }
    }

    if ($xmldb_file) {
        // Moodle 1.8 (and later)
        $ok = false;
        foreach ($xmldb_file->xmldb_structure->tables as $xmldb_table) {
            if ($xmldb_table->name==$table) {
                $ok = create_table($xmldb_table);
                break;
            }
        }
        return $ok;
    }

    // Moodle 1.7 (and earlier)

    if (! isset($sql)) { // first time only
        $sqlfilepath = "$CFG->dirroot/mod/hotpot/db/$CFG->dbtype.sql";
        if (file_exists($sqlfilepath)) {
            if (function_exists('file_get_contents')) {
                $sql = file_get_contents($sqlfilepath);
            } else { // PHP < 4.3
                $sql = file($sqlfilepath);
                if (is_array($sql)) {
                     $sql = implode('', $sql);
                }
            }
        }
        if (empty($sql)) {
            $sql = '';
        }
    }

    // extract and execute all CREATE statements relating to this table
    if (preg_match_all("/CREATE (TABLE|INDEX)(\s[^;]*)? prefix_{$table}(\s[^;]*)?;/s", $sql, $strings)) {
        $ok = true;
        foreach ($strings[0] as $string) {
            $ok = $ok && modify_database('', $string);
        }
        return $ok;
    }

    // table could not be created
    return false;
}
function hotpot_transfer_records($oldtable, $table, $foreignkeys, $primarykey, &$new) {
    global $db;
    $ok = true;
    // get the records, if any
    if (hotpot_db_table_exists($oldtable) && ($records = get_records($oldtable))) {
        // start progress report
        $i = 0;
        $count = count($records);
        hotpot_update_print("Transferring $count records from &quot;$oldtable&quot; to &quot;$table&quot; ... ");
        // transfer all $records
        foreach ($records as $record) {
            switch ($table) {
                case 'hotpot' :
                    $record->summary = addslashes($record->summary);
                    break;
                case 'hotpot_attempts' :
                    $record->details = addslashes($record->details);
                    break;
                case 'hotpot_questions' :
                    $record->name = addslashes($record->name);
                    hotpot_update_string_id_list($table, $record, 'TEXT', $new);
                    break;
                case 'hotpot_responses' :
                    hotpot_update_string_id_list($table, $record, 'correct', $new);
                    hotpot_update_string_id_list($table, $record, 'ignored', $new);
                    hotpot_update_string_id_list($table, $record, 'wrong', $new);
                    break;
                case 'hotpot_strings' :
                    $record->string = addslashes($record->string);
                    break;
            }
            // update foreign keys, if any
            foreach ($foreignkeys as $oldkey=>$key) {
                // transfer (and update) key
                $value = $record->$oldkey;
                if (isset($new[$key][$value])) {
                    $record->$key = $new[$key][$value];
                } else {
                    // foreign key could not be updated
                    $ok = hotpot_update_print_warning($key, $value, $oldtable, $record->id) && $ok;
                    unset($record->id);
                }
            }
            if ($ok && isset($record->id)) {
                // store and remove old primary key
                $id = $record->id;
                unset($record->id);
                // add the updated record and store the new id
                $new[$primarykey][$id] = insert_record($table, $record, true);
                // check id is numeric
                if (!is_numeric($new[$primarykey][$id])) {
                    hotpot_update_print("<li>Record could not added to $table table ($oldtable id=$id)</li>\n");
                    //$ok = false;
                }
            }
            $i++;
            hotpot_update_print_progress($i);
        }
        // finish progress report
        hotpot_update_print_ok($ok);
    }
    return $ok;
}
function hotpot_update_course_modules($oldmodulename, $modulename, &$new) {
    $ok = true;
    $oldmoduleid = get_field('modules', 'id', 'name', $oldmodulename);
    $moduleid = get_field('modules', 'id', 'name', $modulename);
    if (is_numeric($oldmoduleid) && is_numeric($moduleid)) {
        // get module records
        if ($records = get_records('course_modules', 'module', $oldmoduleid)) {
            // start progress report
            $count = count($records);
            hotpot_update_print("Updating $count course modules from &quot;$oldmodulename&quot; to &quot;$modulename&quot; ... ");
            // update foreign keys in all $records
            foreach ($records as $record) {
                // update instance
                $instance = $record->instance;
                if (isset($new[$modulename][$instance])) {
                    $record->instance = $new[$modulename][$instance];
                } else if ($record->deleted) {
                    unset($record->id);
                } else {
                    // could not find new id of course module
                    $ok = hotpot_update_print_warning("$modulename instance", $instance, 'course_modules', $record->id) && $ok;
                    unset($record->id);
                }
                // update module id
                if ($ok && isset($record->id)) {
                    $record->module = $moduleid;
                    $ok = update_record('course_modules', $record);
                }
            }
            // finish progress report
            hotpot_update_print_ok($ok);
        }
        // update logs
        $ok = $ok && hotpot_update_logs($oldmodulename, $modulename, $moduleid, $new);
    }
    return $ok;
}
function hotpot_update_logs($oldmodulename, $modulename, $moduleid, &$new) {
    $table = 'log';
    $ok = true;
    // get log records for the oldmodule
    if ($records = get_records($table, 'module', $oldmodulename)) {
        // start progress report
        $i = 0;
        $count = count($records);
        hotpot_update_print("Updating $count log records ... ");
        // update foreign keys in all $records
        foreach ($records as $record) {
            // update course module name
            $record->module = $modulename;
            // check if module id was given (usually it is)
            if ($record->cmid) {
                // update course module id, if necessary
                if (isset($new[$modulename][$record->cmid])) {
                    $record->cmid = $new[$modulename][$record->cmid];
                } else {
                    // could not update course module id
                    $ok = hotpot_update_print_warning('cmid', $record->cmid, 'log', $record->id) && $ok;
                    unset($record->id);
                }
                // update url and info
                switch ($record->action) {
                    case "add":
                    case "update":
                    case "view":
                        $record->url = "view.php?id=".$record->cmid;
                        $record->info = $moduleid;
                        break;
                    case "view all":
                        // do nothing
                        break;
                    case "report":
                        $record->url = "report.php?id=".$record->cmid;
                        $record->info = $moduleid;
                        break;
                    case "attempt":
                    case "submit":
                    case "review":
                        $id = substr(strrchr($record->url,"="),1);
                        if (isset($new->attempt[$id])) {
                            $id = $new->attempt[$id];
                        }
                        $record->url = "review.php?id=".$record->cmid."&attempt=$id";
                        $record->info = $moduleid;
                        break;
                    default:
                        // unknown log action
                        $ok = hotpot_update_print_warning('action', $record->action, 'log', $record->id) && $ok;
                        unset($record->id);
                } // end switch
            }
            if (isset($record->id)) {
                $ok = $ok && update_record($table, $record);
            }
            $i++;
            hotpot_update_print_progress($i);
        } // end foreach
        // finish progress report
        hotpot_update_print_ok($ok);
    }
    return $ok;
}
function hotpot_update_fields($table, $feedback=false) {
    global $CFG, $db;
    $ok = true;
    // check the table exists
    if (hotpot_db_table_exists($table)) {
        switch ($table) {
            case 'hotpot' :
                // == ADD ==
                hotpot_db_update_field_type($table, '', 'location',     'INTEGER', 4, 'UNSIGNED', 'NOT NULL', 0);
                hotpot_db_update_field_type($table, '', 'navigation',   'INTEGER', 4, 'UNSIGNED', 'NOT NULL', 1);
                hotpot_db_update_field_type($table, '', 'outputformat', 'INTEGER', 4, 'UNSIGNED', 'NOT NULL', 1);
                hotpot_db_update_field_type($table, '', 'shownextquiz', 'INTEGER', 4, 'UNSIGNED', 'NOT NULL', 0);
                hotpot_db_update_field_type($table, '', 'forceplugins', 'INTEGER', 4, 'UNSIGNED', 'NOT NULL', 0);
                hotpot_db_update_field_type($table, '', 'password',     'VARCHAR', 255, '',       'NOT NULL', '');
                hotpot_db_update_field_type($table, '', 'subnet',       'VARCHAR', 255, '',       'NOT NULL', '');
                // == ALTER ==
                hotpot_db_update_field_type($table, 'summary',   'summary',   'TEXT',    '',  '', 'NOT NULL', '');
                hotpot_db_update_field_type($table, 'reference', 'reference', 'VARCHAR', 255, '', 'NOT NULL', '');
                // == REMOVE ==
                hotpot_db_remove_field($table, 'intro');
                hotpot_db_remove_field($table, 'attemptonlast');
                hotpot_db_remove_field($table, 'sumgrades');
                hotpot_db_set_table_comment($table, 'details about Hot Potatoes quizzes');
            break;
            case 'hotpot_events' :
                // == ADD ==
                hotpot_db_update_field_type($table, '', 'hotpot',     'INTEGER', 10, 'UNSIGNED', 'NOT NULL');
                hotpot_db_update_field_type($table, '', 'attempt',    'INTEGER', 6,  'UNSIGNED', 'NOT NULL');
                hotpot_db_update_field_type($table, '', 'details',    'TEXT',    '', '', '', '');
                hotpot_db_update_field_type($table, '', 'timestart',  'INTEGER', 10, 'UNSIGNED', 'NOT NULL', 0);
                hotpot_db_update_field_type($table, '', 'timefinish', 'INTEGER', 10, 'UNSIGNED', 'NOT NULL', 0);
                // == ALTER ==
                hotpot_db_update_field_type($table, 'score',     'score',      'INTEGER', 6,  'UNSIGNED', 'NULL');
                hotpot_db_update_field_type($table, 'wrong',     'penalties',  'INTEGER', 6,  'UNSIGNED', 'NULL');
                hotpot_db_update_field_type($table, 'starttime', 'starttime',  'INTEGER', 10, 'UNSIGNED', 'NULL');
                hotpot_db_update_field_type($table, 'endtime',   'endtime',    'INTEGER', 10, 'UNSIGNED', 'NULL');
                // save and switch off SQL message echo
                $debug = $db->debug;
                $db->debug = $feedback;
                // get array mapping course module ids to hotpot ids
                $hotpotmoduleid = get_field('modules', 'id', 'name', 'hotpot');
                $coursemodules = get_records('course_modules', 'module', $hotpotmoduleid, 'id', 'id, instance');
                // get all event records
                if (hotpot_db_field_exists($table, 'hotpotid')) {
                    $records = get_records($table, '', '', 'userid,hotpotid,time');
                } else {
                    $records = false; // table has already been updated
                }
                if ($records) {
                    $count = count($records);
                    hotpot_update_print("Updating $count records in $table ... ");
                    $ids = array_keys($records);
                    foreach ($ids as $i=>$id) {
                        // reference to current record
                        $record = &$records[$id];
                        // set timestart and timefinish (the times recorded by Moodle)
                        if (empty($record->timestart) && $record->time) {
                            $record->timestart = $record->time;
                        }
                        if (empty($record->timefinish) && $record->timestart) {
                            if ($record->starttime && $record->endtime) {
                                $duration = ($record->endtime - $record->starttime);
                            } else {
                                if (($i+1)>=$count) {
                                    $nextrecord = NULL;
                                } else {
                                    $nextrecord = &$records[$ids[$i+1]];
                                }
                                if (isset($nextrecord) && $nextrecord->userid==$record->userid && $nextrecord->hotpotid==$record->hotpotid) {
                                    $duration = $nextrecord->time - $record->time;
                                } else {
                                    $duration = NULL;
                                }
                            }
                            if (isset($duration)) {
                                $record->timefinish = $record->timestart + $duration;
                            }
                        }
                        // unset score and penalties, if quiz was abandoned
                        if (empty($record->endtime) || (empty($record->penalties) && empty($record->score))) {
                            unset($record->score);
                            unset($record->penalties);
                        }
                        // get last (=previous) record
                        if ($i==0) {
                            $lastrecord = NULL;
                        } else {
                            $lastrecord = &$records[$ids[$i-1]];
                        }
                        // increment or reset $attempt number
                        if (isset($lastrecord) && $lastrecord->userid==$record->userid && $lastrecord->hotpotid==$record->hotpotid) {
                            $attempt++;
                        } else {
                            $attempt = 1;
                        }
                        // set $record->$attempt, if necessary
                        if (empty($record->attempt) || $record->attempt<$attempt) {
                            $record->attempt = $attempt;
                        } else {
                            $attempt = $record->attempt;
                        }
                        // set hotpot id and update record
                        if (isset($record->hotpotid) && isset($record->id)) {
                            if (isset($coursemodules[$record->hotpotid])) {
                                $record->hotpot = $coursemodules[$record->hotpotid]->instance;
                                hotpot_db_update_record($table, $record, true);
                            } else {
                                // hotpotid is invalid (shouldn't happen)
                                $ok = hotpot_update_print_warning('hotpotid', $record->hotpotid, $table, $record->id) && $ok;
                                delete_records($table, 'id', $record->id);
                            }
                        } else {
                                // empty record (shouldn't happen)
                        }
                        hotpot_update_print_progress($i);
                    }
                    // finish progress report
                    hotpot_update_print_ok($ok);
                }
                // restore SQL message echo setting
                $db->debug = $debug;
                // == REMOVE ==
                hotpot_db_remove_field($table, 'hotpotid');
                hotpot_db_remove_field($table, 'course');
                hotpot_db_remove_field($table, 'time');
                hotpot_db_remove_field($table, 'event');
                hotpot_db_set_table_comment($table, 'details about Hot Potatoes quiz attempts');
            break;
            case 'hotpotatoes' :
                // == ALTER ==
                hotpot_db_update_field_type($table, 'intro', 'summary', 'TEXT', '', '', '', 'NULL');
            break;
        }
    }
    return $ok;
}
function hotpot_update_string_id_list($table, &$record, $field, &$new) {
    $ok = true;
    if (isset($record->$field)) {
        $oldids = explode(',', $record->$field);
        $newids = array();
        foreach ($oldids as $id) {
            if (isset($new['string'][$id])) {
                $newids[] = $new['string'][$id];
            } else if (is_numeric($id)) {
                // string id could not be updated
                $ok = hotpot_update_print_warning("string id in $field", $id, $table, $record->id) && $ok;
            } else {
                // ignore non-numeric ids (e.g. blanks)
            }
        }
        if ($ok) {
            $record->$field = implode(',', $newids);
        }
    }
    return $ok;
}
///////////////////////////
//     print functions
///////////////////////////
function hotpot_update_print($msg=false, $n=300) {
    // this function prints $msg and flush output buffer
    if ($msg) {
        if (is_string($msg)) {
            print $msg;
        } else {
            print strftime("%X", time());
        }
    }
    // fill output buffer
    if ($n) {
        print str_repeat(" ", $n);
    }
    // some browser's require newline to flush
    print "\n";
    // flush PHP's output buffer
    flush();
}
function hotpot_update_print_progress($i) {
    if ($i%10==0) {
        $msg = '.';
        hotpot_update_print($msg);
    }
}
function hotpot_update_print_ok($ok) {
    if ($ok) {
        hotpot_update_print('<font color="green">'.get_string('success')."</font><br />\n");
    } else {
        hotpot_update_print('<font color="red">'.get_string('error')."</font><br />\n");
    }
}
function hotpot_update_print_warning($field, $value, $table, $id) {
    hotpot_update_print("<li><b>Warning:</b> invalid $field field (value=$value) in $table (id=$id)</li>\n");
    return true;
}
///////////////////////////
//     database functions
///////////////////////////
function hotpot_db_index_exists($table, $index, $feedback=false) {
    global $CFG, $db;
    $exists = false;
    // save and switch off SQL message echo
    $debug = $db->debug;
    $db->debug = $feedback;
    switch (strtolower($CFG->dbfamily)) {
        case 'mysql' :
            $rs = $db->Execute("SHOW INDEX FROM `$table`");
            if ($rs && $rs->RecordCount()>0) {
                $records = $rs->GetArray();
                foreach ($records as $record) {
                    if (isset($record['Key_name']) && $record['Key_name']==$index) {
                        $exists = true;
                        break;
                    }
                }
            }
        break;
        case 'postgres' :
            $rs = $db->Execute("SELECT relname FROM pg_class WHERE relname = '$index' AND relkind='i'");
            if ($rs && $rs->RecordCount()>0) {
                $exists = true;
            }
        break;
    }
    // restore SQL message echo
    $db->debug = $debug;
    return $exists;
}
function hotpot_db_delete_index($table, $index, $feedback=false) {
    global $CFG, $db;
    $ok = true;
    // check index exists
    if (hotpot_db_index_exists($table, $index)) {
        switch (strtolower($CFG->dbfamily)) {
            case 'mysql' :
                $sql = "ALTER TABLE `$table` DROP INDEX `$index`";
            break;
            case 'postgres' :
                $sql = "DROP INDEX $index";
            break;
            default: // unknown database type
                $sql = '';
            break;
        }
        if ($sql) {
            // save and switch off SQL message echo
            $debug = $db->debug;
            $db->debug = $feedback;
            $ok = $db->Execute($sql) ? true : false;
            // restore SQL message echo
            $db->debug = $debug;
        } else { // unknown database type
            $ok = false;
        }
    }
    return $ok;
}
function hotpot_db_add_index($table, $field, $length='') {
    global $CFG, $db;

    if (strtolower($CFG->dbfamily)=='postgres') {
        $index = "{$CFG->prefix}{$table}_{$field}_idx";
    } else {
        // mysql (and others)
        $index = "{$table}_{$field}_idx";
    }
    $table = "{$CFG->prefix}$table";

    // delete $index if it already exists
    $ok = hotpot_db_delete_index($table, $index);

    switch (strtolower($CFG->dbfamily)) {
        case 'mysql' :
            $ok = $ok && $db->Execute("ALTER TABLE `$table` ADD INDEX `$index` (`$field`)");
        break;
        case 'postgres' :
            $ok = $ok && $db->Execute("CREATE INDEX $index ON $table (\"$field\")");
        break;
        default: // unknown database type
            $ok = false;
        break;
    }
    return $ok;
}
function hotpot_db_table_exists($table, $feedback=false) {
    return hotpot_db_object_exists($table, '', $feedback);
}
function hotpot_db_field_exists($table, $field, $feedback=false) {
    return
        hotpot_db_object_exists($table, '', $feedback) &&
        hotpot_db_object_exists($table, $field, $feedback)
    ;
}
function hotpot_db_object_exists($table, $field='', $feedback=false) {
    global $CFG,$db;
    // expand table name
    $table = "{$CFG->prefix}$table";
    // set $sql
    switch (strtolower($CFG->dbfamily)) {
        case 'mysql' :
            if (empty($field)) {
                $sql = "SHOW TABLES LIKE '$table'";
            } else {
                $sql = "SHOW COLUMNS FROM `$table` LIKE '$field'";
            }
        break;
        case 'postgres' :
            if (empty($field)) {
                $sql = "SELECT relname FROM pg_class WHERE relname = '$table' AND relkind='r'";
            } else {
                $sql = "
                    SELECT attname FROM pg_attribute WHERE attname = '$field'
                    AND attrelid = (SELECT oid FROM pg_class WHERE relname = '$table')
                ";
            }
        break;
    }
    // save and switch off SQL message echo
    $debug = $db->debug;
    $db->debug = $feedback;
    // execute sql
    $rs = $db->Execute($sql);
    // restore SQL message echo setting
    $db->debug = $debug;
    // report error if required
    if (empty($rs) && debugging()) {
        notify($db->ErrorMsg()."<br /><br />$sql");
    }
    return ($rs && $rs->RecordCount()>0);
}
function hotpot_db_remove_table($table, $feedback=true) {
    global $CFG;
    if (hotpot_db_table_exists($table)) {
        $ok = execute_sql("DROP TABLE {$CFG->prefix}$table", $feedback);
    } else {
        $ok = true;
    }
    return $ok;
}
function hotpot_db_rename_table($oldtable, $table, $feedback=true) {
    global $CFG;
    if (hotpot_db_table_exists($oldtable)) {
        $ok = execute_sql("ALTER TABLE {$CFG->prefix}$oldtable RENAME TO {$CFG->prefix}$table", $feedback);
    } else {
        $ok = true;
    }
    return $ok;
}
function hotpot_db_append_table($oldtable, $table, $feedback=true) {
    global $CFG, $db;
    if (hotpot_db_table_exists($oldtable)) {
        if (hotpot_db_table_exists($table)) {
            // expand table names
            $table = "{$CFG->prefix}$table";
            $oldtable = "{$CFG->prefix}$oldtable";
            // get field info
            $fields = $db->MetaColumns($table);
            $oldfields = $db->MetaColumns($oldtable);
            $fieldnames = array();
            if (!empty($fields) || !empty($oldfields)) {
                foreach ($fields as $field) {
                    if ($field->name!='id' && isset($oldfields[strtoupper($field->name)])) {
                        $fieldnames[] = $field->name;
                    }
                }
            }
            $fieldnames = implode(',', $fieldnames);
            if (empty($fieldnames)) {
                $ok = false;
            } else {
                switch (strtolower($CFG->dbfamily)) {
                    case 'mysql':
                        $ok = execute_sql("INSERT INTO `$table` ($fieldnames) SELECT $fieldnames FROM `$oldtable` WHERE 1");
                        break;
                    case 'postgres':
                        $ok = execute_sql("INSERT INTO $table ($fieldnames) SELECT $fieldnames FROM $oldtable");
                        break;
                    default:
                        $ok = false;
                        break;
                }
            }
        } else { // $table does not exist
            $ok = hotpot_db_rename_table($oldtable, $table, $feedback);
        }
    } else { // $oldtable does not exist
        $ok = hotpot_db_table_exists($table, $feedback);
    }
    return $ok;
}
function hotpot_db_set_table_comment($table, $comment, $feedback=true) {
    global $CFG;
    $ok = true;
    switch (strtolower($CFG->dbfamily)) {
        case 'mysql' :
            $ok = execute_sql("ALTER TABLE {$CFG->prefix}$table COMMENT='$comment'");
            break;
        case 'postgres' :
            $ok = execute_sql("COMMENT ON TABLE {$CFG->prefix}$table IS '$comment'");
            break;
    }
    return $ok;
}
function hotpot_db_remove_field($table, $field, $feedback=true) {
    global $CFG;
    if (hotpot_db_field_exists($table, $field)) {
        $ok = execute_sql("ALTER TABLE {$CFG->prefix}$table DROP COLUMN $field", $feedback);
    } else {
        $ok = true;
    }
    return $ok;
}
function hotpot_db_update_field_type($table, $oldfield, $field, $type, $size, $unsigned, $notnull, $default=NULL, $after=NULL) {
    $ok = true;
    global $CFG,$db;
    // check validity of arguments, and adjust if necessary
    if ($oldfield && !hotpot_db_field_exists($table, $oldfield)) {
        $oldfield = '';
    }
    if (empty($oldfield) && hotpot_db_field_exists($table, $field)) {
        $oldfield = $field;
    }
    if (is_string($unsigned)) {
        $unsigned = (strtoupper($unsigned)=='UNSIGNED');
    }
    if (is_string($notnull)) {
        $notnull = (strtoupper($notnull)=='NOT NULL');
    }
    if (isset($default)) {
        if (!is_numeric($default) && strtoupper($default)!='NULL' && !preg_match("|^'.*'$|", $default)) {
            $default = "'$default'";
        }
    }
    // set full table name
    $table = "{$CFG->prefix}$table";
    // update the field in the database
    switch (strtolower($CFG->dbfamily)) {
        case 'mysql':
            // optimize integer types
            switch (strtoupper($type)) {
                case 'TEXT':
                    $size = '';
                    $unsigned = false;
                break;
                case 'INTEGER' :
                    if (!is_numeric($size)) {
                        $size = '';
                    } else if ($size <= 4) {
                        $type = "TINYINT";   // 1 byte
                    } else if ($size <= 6) {
                        $type = "SMALLINT";  // 2 bytes
                    } else if ($size <= 8) {
                        $type = "MEDIUMINT"; // 3 bytes
                    } else if ($size <= 10) {
                        $type = "INTEGER";   // 4 bytes (=INT)
                    } else if ($size > 10) {
                        $type = "BIGINT";    // 8 bytes
                    }
                break;
                case 'VARCHAR':
                    $unsigned = false;
                break;
            }
            // set action
            if (empty($oldfield)) {
                $action = "ADD";
            } else {
                $action = "CHANGE `$oldfield`";
            }
            // set fieldtype
            $fieldtype = $type;
            if ($size) {
                $fieldtype .= "($size)";
            }
            if ($unsigned) {
                $fieldtype .= ' UNSIGNED';
            }
            if ($notnull) {
                $fieldtype .= ' NOT NULL';
            }
            if (isset($default)) {
                $fieldtype .= " DEFAULT $default";
            }
            if (!empty($after)) {
                $fieldtype .= " AFTER `$after`";
            }
            $ok = $ok && execute_sql("ALTER TABLE `$table` $action `$field` $fieldtype");
        break;
        case 'postgres':
            // get db version
            //    N.B. $db->ServerInfo() usually returns blank
            //    (except lib/adodb/drivers/adodb-postgre64-inc.php)
            $dbversion = '';
            $rs = $db->Execute("SELECT version()");
            if ($rs && $rs->RecordCount()>0) {
                $records = $rs->GetArray();
                if (preg_match('/\d+\.\d+/', $records[0][0], $matches)) {
                    $dbversion = $matches[0];
                }
            }
            $tmpfield = 'temporary_'.$field.'_'.time();
            switch (strtoupper($type)) {
                case "INTEGER":
                    if (!is_numeric($size)) {
                        $fieldtype = "INTEGER";
                    } else if ($size <= 4) {
                        $fieldtype = "INT2"; // 2 bytes
                    } else if ($size <= 10) {
                        $fieldtype = "INT4"; // 4 bytes (=INTEGER)
                    } else if ($size > 10) {
                        $fieldtype = "INT8"; // 8 bytes
                    }
                break;
                case "VARCHAR":
                    $fieldtype = "VARCHAR($size)";
                break;
                default:
                    $fieldtype = $type;
            }
            // start transaction
            execute_sql('BEGIN');
            // create temporary field
            execute_sql('ALTER TABLE '.$table.' ADD COLUMN "'.$tmpfield.'" '.$fieldtype);
            // set default
            if (isset($default)) {
                execute_sql('UPDATE '.$table.' SET "'.$tmpfield.'" = '.$default);
                execute_sql('ALTER TABLE '.$table.' ALTER COLUMN "'.$tmpfield.'" SET DEFAULT '.$default);
            } else {
                execute_sql('ALTER TABLE '.$table.' ALTER COLUMN "'.$tmpfield.'" DROP DEFAULT');
            }
            // set not null
            if ($dbversion=='' || $dbversion >= "7.3") {
                $notnull = ($notnull ? 'SET NOT NULL' : 'DROP NOT NULL');
                execute_sql('ALTER TABLE '.$table.' ALTER COLUMN "'.$tmpfield.'" '.$notnull);
            } else {
                execute_sql("
                    UPDATE pg_attribute SET attnotnull=".($notnull ? 'TRUE' : 'FALSE')."
                    WHERE attname = '$tmpfield'
                    AND attrelid = (SELECT oid FROM pg_class WHERE relname = '$table')
                ");
            }
            // transfer $oldfield values, if necessary
            if ( $oldfield != '' ) {
                execute_sql('UPDATE '.$table.' SET "'.$tmpfield.'" = CAST ("'.$oldfield.'" AS '.$fieldtype.')');
                execute_sql('ALTER TABLE '.$table.' DROP COLUMN "'.$oldfield.'"');
            }
            // rename $tmpfield to $field
            execute_sql('ALTER TABLE '.$table.' RENAME COLUMN "'.$tmpfield.'" TO "'.$field.'"');
            // do the transaction
            execute_sql('COMMIT');
            // reclaim disk space (must be done outside transaction)
            if ($oldfield != '' && $dbversion >= "7.3") {
                execute_sql('UPDATE '.$table.' SET "'.$field.'" = "'.$field.'"');
                execute_sql('VACUUM FULL '.$table);
            }
        break;
    } // end switch $CGF->dbfamily
    return $ok;
}
function hotpot_db_update_record($table, $record, $forcenull=false) {
    global $CFG, $db;
    $ok = true;
    // set full table name
    $table = "{$CFG->prefix}$table";
    // get field names
    $fields = $db->MetaColumns($table);
    if (empty($fields)) {
        $ok = false;
    } else {
        // get values
        $values = array();
        foreach ($fields as $field) {
            $fieldname = $field->name;
            if ($fieldname!='id' && ($forcenull || isset($record->$fieldname))) {
                $value = isset($record->$fieldname) ? "'".$record->$fieldname."'" : 'NULL';
                $values[] = "$fieldname = $value";
            }
        }
        $values = implode(',', $values);
        // update values (if there are any)
        if ($values) {
            $sql = "UPDATE $table SET $values WHERE id='$record->id'";
            $rs = $db->Execute($sql);
            if (empty($rs)) {
                $ok = false;
                debugging($db->ErrorMsg()."<br /><br />$sql");
            }
        }
    }
    return $ok;
}
function hotpot_rm($target, $output=true) {
    $ok = true;
    if (!empty($target)) {
        if (is_file($target)) {
            if ($output) {
                print "removing file: $target ... ";
            }
            $ok = @unlink($target);
        } else if (is_dir($target)) {
            $dir = dir($target);
            while(false !== ($entry = $dir->read())) {
                if ($entry!='.' && $entry!='..') {
                    $ok = $ok && hotpot_rm($target.DIRECTORY_SEPARATOR.$entry, $output);
                }
            }
            $dir->close();
            if ($output) {
                print "removing folder: $target ... ";
            }
            $ok = $ok && @rmdir($target);
        } else { // not a file or directory (probably doesn't exist)
            $output = false;
        }
        if ($output) {
            if ($ok) {
                print '<font color="green">OK</font><br />';
            } else {
                print '<font color="red">Failed</font><br />';
            }
        }
    }
    return $ok;
}
function hotpot_flush($n=0, $time=false) {
    if ($time) {
        $t = strftime("%X",time());
    } else {
        $t = "";
    }
    echo str_repeat(" ", $n) . $t . "\n";
    flush();
}
?>
