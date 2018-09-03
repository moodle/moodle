<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * mod/hotpot/db/upgrade.php
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * xmldb_hotpot_upgrade
 *
 * @param xxx $oldversion
 * @return xxx
 */
function xmldb_hotpot_upgrade($oldversion) {
    global $CFG, $DB;

    // this flag will be set to true if any upgrade needs to empty the HotPot cache
    $empty_cache = false;

    $dbman = $DB->get_manager();

    if (defined('STDIN') && defined('CLI_SCRIPT')) {
        $interactive = false;
    } else {
        $interactive = true;
    }

    //===== 1.9.0 upgrade line ======//

    // update hotpot grades from sites earlier than Moodle 1.9, 27th March 2008
    $newversion = 2007101511;
    if ($oldversion < $newversion) {
        // ensure "hotpot_upgrade_grades" function is available
        require_once $CFG->dirroot.'/mod/hotpot/lib.php';
        hotpot_upgrade_grades();
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2008011200;
    if ($oldversion < $newversion) {
        // remove unused config setting
        unset_config('hotpot_initialdisable');
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080301;
    if ($oldversion < $newversion) {

        // remove unused config settings
        unset_config('hotpot_showtimes');
        unset_config('hotpot_excelencodings');

        // modify table: hotpot
        $table = new xmldb_table('hotpot');

        // expected structure of hotpot table when we start this upgrade:
        // (i.e. this is how things were at the end of Moodle 1.9)
        //   id, course, name, summary, timeopen, timeclose, location, reference,
        //   outputformat, navigation, studentfeedback, studentfeedbackurl,
        //   forceplugins, shownextquiz, review, grade, grademethod, attempts,
        //   password, subnet, clickreporting, timecreated, timemodified

        // convert, move and rename fields ($newname => $oldfield)
        $fields = array(
            // same name
            'outputformat'   => new xmldb_field('outputformat', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL), // (int -> varchar)
            'timeopen'       => new xmldb_field('timeopen', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'studentfeedbackurl'),
            'timeclose'      => new xmldb_field('timeclose', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'timeopen'),
            'grademethod'    => new xmldb_field('grademethod', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'grade'),
            // new name
            'sourcefile'     => new xmldb_field('reference', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'name'),
            'sourcelocation' => new xmldb_field('location', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'sourcefile'),
            'entrytext'      => new xmldb_field('summary', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'sourcelocation'),
            'reviewoptions'  => new xmldb_field('review', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0'),
            'attemptlimit'   => new xmldb_field('attempts', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'reviewoptions'),
            'gradeweighting' => new xmldb_field('grade', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'attemptlimit'),
        );

        foreach ($fields as $newname => $field) {
            if ($dbman->field_exists($table, $field)) {
                xmldb_hotpot_fix_previous_field($dbman, $table, $field);
                $dbman->change_field_type($table, $field);
                if ($field->getName() != $newname) {
                    $dbman->rename_field($table, $field, $newname);
                }
            }
        }

        // add fields
        $fields = array(
            new xmldb_field('sourcefile', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'name'),
            new xmldb_field('sourcetype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'sourcefile'),
            new xmldb_field('sourceitemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'sourcetype'),
            new xmldb_field('sourcelocation', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'sourceitemid'),

            new xmldb_field('configfile', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'sourcelocation'),
            new xmldb_field('configitemid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'configfile'),
            new xmldb_field('configlocation', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'configitemid'),

            new xmldb_field('entrycm', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'configlocation'),
            new xmldb_field('entrygrade', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '100', 'entrycm'),
            new xmldb_field('entrypage', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'entrygrade'),
            new xmldb_field('entrytext', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'entrypage'),
            new xmldb_field('entryformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'entrytext'),
            new xmldb_field('entryoptions', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'entryformat'),

            new xmldb_field('exitpage', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'entryoptions'),
            new xmldb_field('exittext', XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'exitpage'),
            new xmldb_field('exitformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'exittext'),
            new xmldb_field('exitoptions', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'exitformat'),
            new xmldb_field('exitcm', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'exitoptions'),

            new xmldb_field('title', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '3', 'navigation'),
            new xmldb_field('stopbutton', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'title'),
            new xmldb_field('stoptext', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'stopbutton'),
            new xmldb_field('usefilters', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'stoptext'),
            new xmldb_field('useglossary', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'usefilters'),
            new xmldb_field('usemediafilter', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'useglossary'),

            new xmldb_field('timelimit', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timeclose'),
            new xmldb_field('delay1', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'timelimit'),
            new xmldb_field('delay2', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'delay1'),
            new xmldb_field('delay3', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '2', 'delay2'),
            new xmldb_field('discarddetails', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'clickreporting')
        );

        foreach ($fields as $field) {
            if (! $dbman->field_exists($table, $field)) {
                xmldb_hotpot_fix_previous_field($dbman, $table, $field);
                $dbman->add_field($table, $field);
            }
        }

        // remove field: forceplugins (replaced by "usemediafilter")
        $field = new xmldb_field('forceplugins', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        if ($dbman->field_exists($table, $field)) {
            $DB->execute('UPDATE {hotpot} SET '."usemediafilter='moodle'".' WHERE forceplugins=1');
            $dbman->drop_field($table, $field);
        }

        // remove field: shownextquiz (replaced by "exitcm")
        $field = new xmldb_field('shownextquiz', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        if ($dbman->field_exists($table, $field)) {
            // set exitcm to show next HotPot: -4 = hotpot::ACTIVITY_SECTION_HOTPOT
            $DB->execute('UPDATE {hotpot} SET exitcm=-4 WHERE shownextquiz=1');
            $dbman->drop_field($table, $field);
        }

        // append "id" to fields that are foreign keys in other hotpot tables
        $fields = array(
            // $tablename => $fieldnames array
            'hotpot_attempts'  => array('hotpot'),
            'hotpot_details'   => array('attempt'),
            'hotpot_questions' => array('hotpot'),
            'hotpot_responses' => array('attempt', 'question'),
        );
        foreach ($fields as $tablename => $fieldnames) {
            $table = new xmldb_table($tablename);
            foreach ($fieldnames as  $fieldname) {
                $field = new xmldb_field($fieldname, XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
                if ($dbman->field_exists($table, $field)) {
                    // maybe we should remove all indexes and keys
                    // using this $fieldname before we rename the field
                    $dbman->rename_field($table, $field, $fieldname.'id');
                }
            }
        }

        // create new table: hotpot_cache
        $table = new xmldb_table('hotpot_cache');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('hotpotid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('slasharguments', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL);
            $table->add_field('hotpot_enableobfuscate', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL);
            $table->add_field('hotpot_enableswf', XMLDB_TYPE_CHAR, '1', null, XMLDB_NOTNULL);
            $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
            $table->add_field('sourcefile', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
            $table->add_field('sourcetype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
            $table->add_field('sourcelocation', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL);
            $table->add_field('sourcelastmodified', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
            $table->add_field('sourceetag', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
            $table->add_field('configfile', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
            $table->add_field('configlocation', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('configlastmodified', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
            $table->add_field('configetag', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
            $table->add_field('navigation', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('title', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('stopbutton', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, null, null, '0');
            $table->add_field('stoptext', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
            $table->add_field('usefilters', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0');
            $table->add_field('useglossary', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, null, null, '0');
            $table->add_field('usemediafilter', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('studentfeedback', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('studentfeedbackurl', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL);
            $table->add_field('timelimit', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $table->add_field('delay3', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '-1');
            $table->add_field('clickreporting', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('content', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL);
            $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL);
            $table->add_field('md5key', XMLDB_TYPE_CHAR, '32', null, XMLDB_NOTNULL);

            // Add keys to table hotpot_cache
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('hotpotid', XMLDB_KEY_FOREIGN, array('hotpotid'), 'hotpot', array('id'));

            // Add indexes to table hotpot_cache
            $table->add_index('hotpotid-md5key', XMLDB_INDEX_NOTUNIQUE, array('hotpotid', 'md5key'));

            $dbman->create_table($table);
        }

        // add new logging actions
        log_update_descriptions('mod/hotpot');

        // hotpot savepoint reached
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080302;
    if ($oldversion < $newversion) {
        // navigation setting of "none" is now "0" (was "6")
        $DB->execute('UPDATE {hotpot} SET navigation=0 WHERE navigation=6');

        // navigation's "give up" button, is replaced by the "stopbutton" field
        $DB->execute('UPDATE {hotpot} SET stopbutton=0 WHERE navigation=5');
        $DB->execute('UPDATE {hotpot} SET navigation=0 WHERE navigation=5');
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080303;
    if ($oldversion < $newversion) {
        // modify table: hotpot_attempts
        $table = new xmldb_table('hotpot_attempts');

        // add field: timemodified
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        if (! $dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            $DB->execute('UPDATE {hotpot_attempts} SET timemodified = timefinish WHERE timemodified=0');
            $DB->execute('UPDATE {hotpot_attempts} SET timemodified = timestart  WHERE timemodified=0');
        }

        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080305;
    if ($oldversion < $newversion) {
        // modify table: hotpot
        $table = new xmldb_table('hotpot');

        // change fields
        //  - entrycm         (-> signed)
        //  - outputformat    (-> varchar)
        //  - timelimit       (-> signed)
        //  - delay3          (-> signed)
        //  - attemptlimit    (-> unsigned)
        //  - gradeweighting  (-> unsigned)
        //  - grademethod     (-> unsigned)
        $fields = array(
            new xmldb_field('entrycm', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0'),
            new xmldb_field('outputformat', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL),
            new xmldb_field('timelimit', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0'),
            new xmldb_field('delay3', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '2'),
            new xmldb_field('attemptlimit', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0'),
            new xmldb_field('gradeweighting', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0'),
            new xmldb_field('grademethod', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0')
        );

        foreach ($fields as $field) {
            if ($dbman->field_exists($table, $field)) {
                xmldb_hotpot_fix_previous_field($dbman, $table, $field);
                $dbman->change_field_type($table, $field);
            }
        }

        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080306;
    if ($oldversion < $newversion) {
        // modify table: hotpot
        $table = new xmldb_table('hotpot');

        // rename field: gradelimit -> gradeweighting
        $field = new xmldb_field('gradelimit', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'gradeweighting');
        }

        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080308;
    if ($oldversion < $newversion) {

        // add display fields to hotpot
        // (these fields were missing from access.xml so won't be on new sites)
        $tables = array(
            'hotpot' => array(
                new xmldb_field('title', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '3', 'navigation'),
                new xmldb_field('stopbutton', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'title'),
                new xmldb_field('stoptext', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'stopbutton'),
                new xmldb_field('usefilters', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'stoptext'),
                new xmldb_field('useglossary', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'usefilters'),
                new xmldb_field('usemediafilter', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'useglossary'),
            )
        );
        foreach ($tables as $tablename => $fields) {
            $table = new xmldb_table($tablename);

            foreach ($fields as $field) {
                xmldb_hotpot_fix_previous_field($dbman, $table, $field);
                if ($dbman->field_exists($table, $field)) {
                    $dbman->change_field_type($table, $field);
                } else {
                    $dbman->add_field($table, $field);
                }
            }
        }

        $table = new xmldb_table('hotpot');
        $field = new xmldb_field('forceplugins', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        if ($dbman->field_exists($table, $field)) {
            $DB->execute('UPDATE {hotpot} SET '."usemediafilter='moodle'".' WHERE forceplugins=1');
            $dbman->drop_field($table, $field);
        }

        // force certain fields to be not null
        $tables = array(
            'hotpot' => array(
                new xmldb_field('entrygrade', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '100')
            ),
            'hotpot_cache' => array(
                new xmldb_field('stopbutton', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0'),
                new xmldb_field('usefilters', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0'),
                new xmldb_field('useglossary', XMLDB_TYPE_INTEGER, '2', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0'),
                new xmldb_field('studentfeedback', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0'),
            )
        );

        foreach ($tables as $tablename => $fields) {
            $table = new xmldb_table($tablename);
            foreach ($fields as $field) {
                if ($dbman->field_exists($table, $field)) {
                    $dbman->change_field_type($table, $field);
                }
            }
        }

        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080309;
    if ($oldversion < $newversion) {

        // force certain text fields to be not null
        $tables = array(
            'hotpot' => array(
                new xmldb_field('sourcefile', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL),
                new xmldb_field('entrytext', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL),
                new xmldb_field('exittext', XMLDB_TYPE_TEXT, 'small', null, XMLDB_NOTNULL),
                new xmldb_field('stoptext', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL)
            )
        );

        foreach ($tables as $tablename => $fields) {
            $table = new xmldb_table($tablename);
            foreach ($fields as $field) {
                if ($dbman->field_exists($table, $field)) {
                    $fieldname = $field->getName();
                    $DB->set_field_select($tablename, $fieldname, '', "$fieldname IS NULL");
                    $dbman->change_field_type($table, $field);
                }
            }
        }

        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080311;
    if ($oldversion < $newversion) {

        require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

        /////////////////////////////////////
        /// new file storage migrate code ///
        /////////////////////////////////////

        // set up sql strings to select HotPots with Moodle 1.x file paths (i.e. no leading slash)
        $strupdating = get_string('migratingfiles', 'mod_hotpot');
        $select = 'h.*, cm.id AS cmid';
        $from   = '{hotpot} h, {course_modules} cm, {modules} m';
        $where  = 'm.name=? AND m.id=cm.module AND cm.instance=h.id AND h.sourcefile<>?'.
                  ' AND '.$DB->sql_like('h.sourcefile', '?', false, false, true); // NOT LIKE
        $params = array('hotpot', '', '/%', 0);
        $orderby = 'h.course, h.id';

        // get HotPot records that need to be updated
        if ($count = $DB->count_records_sql("SELECT COUNT('x') FROM $from WHERE $where", $params)) {
            $rs = $DB->get_recordset_sql("SELECT $select FROM $from WHERE $where ORDER BY $orderby", $params);
        } else {
            $rs = false;
        }
        if ($rs) {
            if ($interactive) {
                $i = 0;
                $bar = new progress_bar('hotpotmigratefiles', 500, true);
            }

            // get file storage object
            $fs = get_file_storage();

            if (class_exists('context_course')) {
                $sitecontext = context_course::instance(SITEID);
            } else {
                $sitecontext = get_context_instance(CONTEXT_COURSE, SITEID);
            }

            $coursecontext = null;
            $modulecontext = null;
            foreach ($rs as $hotpot) {

                // apply for more script execution time (3 mins)
                upgrade_set_timeout();

                // get course context for this $hotpot
                if ($coursecontext===null || $coursecontext->instanceid != $hotpot->course) {
                    if (class_exists('context_course')) {
                        $coursecontext = context_course::instance($hotpot->course);
                    } else {
                        $coursecontext = get_context_instance(CONTEXT_COURSE, $hotpot->course);
                    }
                }

                // get module context for this $hotpot/$task
                if ($modulecontext===null || $modulecontext->instanceid != $hotpot->cmid) {
                    if (class_exists('context_module')) {
                        $modulecontext = context_module::instance($hotpot->cmid);
                    } else {
                        $modulecontext = get_context_instance(CONTEXT_MODULE, $hotpot->cmid);
                    }
                }

                // actually there shouldn't be any urls in HotPot activities,
                // but this code will also be used to convert QuizPort to TaskChain
                if (preg_match('/^https?:\/\//i', $hotpot->sourcefile)) {
                    $url = $hotpot->sourcefile;
                    $path = parse_url($url, PHP_URL_PATH);
                } else {
                    $url = '';
                    $path = $hotpot->sourcefile;
                }
                $path = clean_param($path, PARAM_PATH);

                // this information should be enough to access the file
                // if it has been migrated into Moodle 2.0 file system
                $old_filename = basename($path);
                $old_filepath = dirname($path);
                if ($old_filepath=='.' || $old_filepath=='') {
                    $old_filepath = '/';
                } else {
                    $old_filepath = '/'.ltrim($old_filepath, '/'); // require leading slash
                    $old_filepath = rtrim($old_filepath, '/').'/'; // require trailing slash
                }

                // update $hotpot->sourcefile, if necessary
                if ($hotpot->sourcefile != $old_filepath.$old_filename) {
                    $hotpot->sourcefile = $old_filepath.$old_filename;
                    $DB->set_field('hotpot', 'sourcefile', $hotpot->sourcefile, array('id' => $hotpot->id));
                }

                // set $courseid and $contextid from $task->$location
                // of where we expect to find the $file
                //   0 : HOTPOT_LOCATION_COURSEFILES
                //   1 : HOTPOT_LOCATION_SITEFILES
                //   2 : HOTPOT_LOCATION_WWW (not used)
                if ($hotpot->sourcelocation) {
                    $courseid = SITEID;
                    $contextid = $sitecontext->id;
                } else {
                    $courseid = $hotpot->course;
                    $contextid = $coursecontext->id;
                }

                // we expect to need the $filehash to get a file that has been migrated
                $filehash = sha1('/'.$contextid.'/course/legacy/0'.$old_filepath.$old_filename);

                // we might also need the old file path, if the file has not been migrated
                $oldfilepath = $CFG->dataroot.'/'.$courseid.$old_filepath.$old_filename;

                // set parameters used to add file to filearea
                // (sortorder=1 siginifies the "mainfile" in this filearea)
                $file_record = array(
                    'contextid'=>$modulecontext->id, 'component'=>'mod_hotpot', 'filearea'=>'sourcefile',
                    'sortorder'=>1, 'itemid'=>0, 'filepath'=>$old_filepath, 'filename'=>$old_filename
                );

                // initialize sourcefile settings
                $hotpot->sourcefile = $old_filepath.$old_filename;
                $hotpot->sourcetype = '';
                $hotpot->sourceitemid = 0;

                if ($file = $fs->get_file($modulecontext->id, 'mod_hotpot', 'sourcefile', 0, $old_filepath, $old_filename)) {
                    // file already exists for this context - shouldn't happen !!
                    // maybe an earlier upgrade failed for some reason ?
                    // anyway we must do this check, so that create_file_from_xxx() does not abort
                } else if ($url) {
                    // file is on an external url - unusual ?!
                    $file = $fs->create_file_from_url($file_record, $url);
                } else if ($file = xmldb_hotpot_locate_externalfile($modulecontext->id, 'mod_hotpot', 'sourcefile', 0, $old_filepath, $old_filename)) {
                    // file exists in external repository - great !
                } else if ($file = $fs->get_file_by_hash($filehash)) {
                    // $file has already been migrated to Moodle's file system
                    // this is the route we expect most people to come :-)
                    $file = $fs->create_file_from_storedfile($file_record, $file);
                } else if (file_exists($oldfilepath)) {
                    // $file still exists on server's filesystem - unusual ?!
                    $file = $fs->create_file_from_pathname($file_record, $oldfilepath);
                } else {
                    // file was not migrated and is not on server's filesystem
                    $file = false;
                }

                // if source file did not exist, notify user of the problem
                if (empty($file)) {
                    if ($url) {
                        $msg = "course_modules.id=$hotpot->cmid, url=$url";
                    } else {
                        $msg = "course_modules.id=$hotpot->cmid, path=$path";
                    }
                    $params = array('update'=>$hotpot->cmid, 'onclick'=>'this.target="_blank"');
                    $msg = html_writer::link(new moodle_url('/course/modedit.php', $params), $msg);
                    $msg = get_string('sourcefilenotfound', 'mod_hotpot', $msg);
                    echo html_writer::tag('div', $msg, array('class'=>'notifyproblem'));
                }

                // set $hotpot->sourcetype
                if ($pos = strrpos($hotpot->sourcefile, '.')) {
                    $filetype = substr($hotpot->sourcefile, $pos+1);
                    switch ($filetype) {
                        case 'jcl': $hotpot->sourcetype = 'hp_6_jcloze_xml'; break;
                        case 'jcw': $hotpot->sourcetype = 'hp_6_jcross_xml'; break;
                        case 'jmt': $hotpot->sourcetype = 'hp_6_jmatch_xml'; break;
                        case 'jmx': $hotpot->sourcetype = 'hp_6_jmix_xml'; break;
                        case 'jqz': $hotpot->sourcetype = 'hp_6_jquiz_xml'; break;
                        case 'rhb': $hotpot->sourcetype = 'hp_6_rhubarb_xml'; break;
                        case 'sqt': $hotpot->sourcetype = 'hp_6_sequitur_xml'; break;
                        case 'htm':
                        case 'html':
                        default:
                            if ($file) {
                                $pathnamehash = $fs->get_pathname_hash($modulecontext->id, 'mod_hotpot', 'sourcefile', 0, $old_filepath, $old_filename);
                                if ($contenthash = $DB->get_field('files', 'contenthash', array('pathnamehash'=>$pathnamehash))) {
                                    $l1 = $contenthash[0].$contenthash[1];
                                    $l2 = $contenthash[2].$contenthash[3];
                                    if (file_exists("$CFG->dataroot/filedir/$l1/$l2/$contenthash")) {
                                        $hotpot->sourcetype = hotpot::get_sourcetype($file);
                                    } else {
                                        $msg = html_writer::link(
                                            new moodle_url('/course/modedit.php', array('update'=>$hotpot->cmid)),
                                            "course_modules.id=$hotpot->cmid, path=$path"
                                        );
                                        $msg .= html_writer::empty_tag('br');
                                        $msg .= "filedir path=$l1/$l2/$contenthash";
                                        $msg = get_string('sourcefilenotfound', 'mod_hotpot', $msg);
                                        echo html_writer::tag('div', $msg, array('class'=>'notifyproblem'));
                                    }
                                }
                            }
                    }
                }

                // JMatch has 2 output formats
                //     14 : v6  : drop down menus : hp_6_jmatch_xml_v6
                //     15 : v6+ : drag-and-drop   : hp_6_jmatch_xml_v6_plus
                // JMix has 2 output formats
                //     14 : v6  : links           : hp_6_jmix_xml_v6
                //     15 : v6+ : drag-and-drop   : hp_6_jmix_xml_v6_plus
                // since drag-and-drop is the "best" outputformat for both types of quiz,
                // we only need to worry about HotPots whose outputformat was 14 (="v6")

                // set $hotpot->outputformat
                if ($hotpot->outputformat==14 && ($hotpot->sourcetype=='hp_6_jmatch_xml' || $hotpot->sourcetype=='hp_6_jmix_xml')) {
                    $hotpot->outputformat = $hotpot->sourcetype.'_v6';
                } else {
                    $hotpot->outputformat = ''; //  = "best" output format
                }

                $DB->update_record('hotpot', $hotpot);

                // update progress bar
                if ($interactive) {
                    $i++;
                    $bar->update($i, $count, $strupdating.": ($i/$count)");
                }
            }
            $rs->close();
        }

        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080316;
    if ($oldversion < $newversion) {

        // because the HotPot activities were probably hidden until now
        // we need to reset the course caches (using "course/lib.php")
        require_once($CFG->dirroot.'/course/lib.php');

        $courseids = array();
        if ($hotpots = $DB->get_records('hotpot', null, '', 'id,course')) {
            foreach ($hotpots as $hotpot) {
                $courseids[$hotpot->course] = true;
            }
            $courseids = array_keys($courseids);
        }
        unset($hotpots, $hotpot);

        foreach ($courseids as $courseid) {
            rebuild_course_cache($courseid, true);
        }
        unset($courseids, $courseid);

        // reset theme cache to force inclusion of new hotpot css
        theme_reset_all_caches();

        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080325;
    if ($oldversion < $newversion) {
        $table = new xmldb_table('hotpot');
        $fieldnames = array('sourceitemid', 'configitemid');
        foreach ($fieldnames as $fieldname) {
            $field = new xmldb_field($fieldname);
            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }
        }
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080330;
    if ($oldversion < $newversion) {
        require_once($CFG->dirroot.'/mod/hotpot/lib.php');
        hotpot_refresh_events();
    }

    $newversion = 2010080333;
    if ($oldversion < $newversion) {
        update_capabilities('mod/hotpot');
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080339;
    if ($oldversion < $newversion) {
        $table = new xmldb_table('hotpot');
        $field = new xmldb_field('exitgrade', XMLDB_TYPE_INTEGER, '6', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'exitcm');
        xmldb_hotpot_fix_previous_field($dbman, $table, $field);
        if (! $dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080340;
    if ($oldversion < $newversion) {

        // force all text fields to be long text, the default for Moodle 2.3 and later
        $tables = array(
            'hotpot' => array(
                new xmldb_field('entrytext', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL),
                new xmldb_field('exittext', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL)
            ),
            'hotpot_cache' => array(
                new xmldb_field('content', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL)
            ),
            'hotpot_details' => array(
                new xmldb_field('details', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL)
            ),
            'hotpot_questions' => array(
                new xmldb_field('name', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL)
            ),
            'hotpot_strings' => array(
                new xmldb_field('string', XMLDB_TYPE_TEXT, 'long', null, XMLDB_NOTNULL)
            )
        );

        foreach ($tables as $tablename => $fields) {
            $table = new xmldb_table($tablename);
            foreach ($fields as $field) {
                if ($dbman->field_exists($table, $field)) {
                    $fieldname = $field->getName();
                    $DB->set_field_select($tablename, $fieldname, '', "$fieldname IS NULL");
                    $dbman->change_field_type($table, $field);
                }
            }
        }
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080342;
    if ($oldversion < $newversion) {
        // force all MySQL integer fields to be signed, the default for Moodle 2.3 and later
        if ($DB->get_dbfamily() == 'mysql') {
            $prefix = $DB->get_prefix();
            $tables = $DB->get_tables();
            foreach ($tables as $table) {
                if (substr($table, 0, 6)=='hotpot') {
                    $rs = $DB->get_recordset_sql("SHOW COLUMNS FROM {$CFG->prefix}$table WHERE type LIKE '%unsigned%'");
                    foreach ($rs as $column) {
                        // copied from as "lib/db/upgradelib.php"
                        $type = preg_replace('/\s*unsigned/i', 'signed', $column->type);
                        $notnull = ($column->null === 'NO') ? 'NOT NULL' : 'NULL';
                        $default = (is_null($column->default) || $column->default === '') ? '' : "DEFAULT '$column->default'";
                        $autoinc = (stripos($column->extra, 'auto_increment') === false)  ? '' : 'AUTO_INCREMENT';
                        $sql = "ALTER TABLE `{$prefix}$table` MODIFY COLUMN `$column->field` $type $notnull $default $autoinc";
                        $DB->change_database_structure($sql);
                    }
                }
            }
        }
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080353;
    if ($oldversion < $newversion) {

        // remove any unwanted "course_files" folders that may have been created
        // when restoring Moodle 1.9 HotPot activities to a Moodle 2.x site

        // select all HotPot activities which have a "course_files" folder
        // but whose "sourcefile" path does not require such a folder

        $select = 'f.*,'.
                  'h.id AS hotpotid,'.
                  'h.sourcefile AS sourcefile';

        $from   = '{hotpot} h,'.
                  '{course_modules} cm,'.
                  '{context} c,'.
                  '{files} f';

        $where  = $DB->sql_like('h.sourcefile', '?', false, false, true). // NOT LIKE
                  ' AND h.id=cm.instance'.
                  ' AND cm.id=c.instanceid'.
                  ' AND c.id=f.contextid'.
                  ' AND f.component=?'.
                  ' AND f.filearea=?'.
                  ' AND f.filepath=?'.
                  ' AND f.filename=?';

        $params = array('/course_files/%', 'mod_hotpot', 'sourcefile', '/course_files/', '.');
        if ($filerecords = $DB->get_records_sql("SELECT $select FROM $from WHERE $where", $params)) {

            $fs = get_file_storage();
            foreach ($filerecords as $filerecord) {
                $file = $fs->get_file_instance($filerecord);
                xmldb_hotpot_move_file($file, '/');
            }
        }

        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080366;
    if ($oldversion < $newversion) {
        if ($hotpots = $DB->get_records_select('hotpot', $DB->sql_like('sourcefile', '?'), array('%http://localhost/19/99/%'))) {
            foreach ($hotpots as $hotpot) {
                $sourcefile = str_replace('http://localhost/19/99/', '', $hotpot->sourcefile);
                $DB->set_field('hotpot', 'sourcefile', $sourcefile, array('id' => $hotpot->id));
            }
        }
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2010080370;
    if ($oldversion < $newversion) {
        require_once($CFG->dirroot.'/mod/hotpot/locallib.php');

        $reviewoptions = 0;
        list($times, $items) = hotpot::reviewoptions_times_items();
        foreach ($times as $timename => $timevalue) {
            foreach ($items as $itemname => $itemvalue) {
                $reviewoptions += ($timevalue & $itemvalue);
            }
        }
        // $reviewoptions should now be set to 62415
        $DB->set_field('hotpot', 'reviewoptions', $reviewoptions);

        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2013111685;
    if ($oldversion < $newversion) {
        $tables = array(
            'hotpot' => array(
                new xmldb_field('allowpaste', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'stoptext')
            ),
            'hotpot_cache' => array(
                new xmldb_field('hotpot_bodystyles',  XMLDB_TYPE_CHAR,    '8',  null, XMLDB_NOTNULL, null, null, 'slasharguments'),
                new xmldb_field('sourcerepositoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0',  'sourcelocation'),
                new xmldb_field('configrepositoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0',  'configlocation'),
                new xmldb_field('allowpaste',         XMLDB_TYPE_INTEGER, '2',  null, XMLDB_NOTNULL, null, '0',  'stoptext')
            ),
        );
        foreach ($tables as $table => $fields) {
            $table = new xmldb_table($table);
            foreach ($fields as $field) {
                xmldb_hotpot_fix_previous_field($dbman, $table, $field);
                if ($dbman->field_exists($table, $field)) {
                    $dbman->change_field_type($table, $field);
                } else {
                    $dbman->add_field($table, $field);
                }
            }
        }
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2014011694;
    if ($oldversion < $newversion) {
        require_once($CFG->dirroot.'/mod/hotpot/lib.php');
        hotpot_update_grades();
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2014111133;
    if ($oldversion < $newversion) {
        // fix all hotpots with view completion
        if (defined('COMPLETION_VIEW_REQUIRED')) {
            $moduleid = $DB->get_field('modules', 'id', array('name' => 'hotpot'));
            $params = array('module' => $moduleid, 'completionview' => COMPLETION_VIEW_REQUIRED);
            if ($cms = $DB->get_records('course_modules', $params, 'course,section')) {
                $time = time();
                $course = null;
                foreach ($cms as $cm) {
                    $params = array('coursemoduleid'  => $cm->id,
                                    'viewed'          => COMPLETION_VIEWED,
                                    'completionstate' => COMPLETION_INCOMPLETE);
                    if ($userids = $DB->get_records_menu('course_modules_completion', $params, '', 'id,userid')) {
                        if ($course===null || $course->id != $cm->course) {
                            $params = array('id' => $cm->course);
                            $course = $DB->get_record('course', $params);
                            $completion = new completion_info($course);
                        }
                        $userids = array_values($userids);
                        $userids = array_unique($userids);
                        foreach ($userids as $userid) {
                            // mimic "set_module_viewed($cm, $userid)"
                            // but without the warnings about headers
                            $data = $completion->get_data($cm, false, $userid);
                            $data->viewed = COMPLETION_VIEWED;
                            $completion->internal_set_data($cm, $data);
                            $completion->update_state($cm, COMPLETION_COMPLETE, $userid);
                        }
                    }
                }
            }
        }
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2014112837;
    if ($oldversion < $newversion) {
        require_once($CFG->dirroot.'/mod/hotpot/lib.php');

        if (function_exists('get_log_manager')) {

            if ($loglegacy = get_config('loglegacy', 'logstore_legacy')) {
                set_config('loglegacy', 0, 'logstore_legacy');
            }

            $legacy_log_tablename = 'log';
            $legacy_log_table = new xmldb_table($legacy_log_tablename);

            $standard_log_tablename = 'logstore_standard_log';
            $standard_log_table = new xmldb_table($standard_log_tablename);

            if ($dbman->table_exists($legacy_log_table) && $dbman->table_exists($standard_log_table)) {

                $select = 'module = ?';
                $params = array('hotpot');

                if ($time = $DB->get_field($standard_log_tablename, 'MAX(timecreated)', array('component' => 'hotpot'))) {
                    $select .= ' AND time > ?';
                    $params[] = $time;
                } else if ($time = $DB->get_field($standard_log_tablename, 'MIN(timecreated)', array())) {
                    $select .= ' AND time > ?';
                    $params[] = $time;
                }

                if ($count = $DB->count_records_select($legacy_log_tablename, $select, $params)) {
                    $rs = $DB->get_recordset_select($legacy_log_tablename, $select, $params);
                } else {
                    $rs = false;
                }

                if ($rs) {
                    if ($interactive) {
                        $i = 0;
                        $bar = new progress_bar('hotpotmigratelogs', 500, true);
                    }
                    $strupdating = get_string('migratinglogs', 'mod_hotpot');
                    foreach ($rs as $log) {
                        upgrade_set_timeout(); // 3 mins
                        hotpot_add_to_log($log->course,
                                          $log->module,
                                          $log->action,
                                          $log->url,
                                          $log->info,
                                          $log->cmid,
                                          $log->userid);
                        if ($interactive) {
                            $i++;
                            $bar->update($i, $count, $strupdating.": ($i/$count)");
                        }
                    }
                    $rs->close();
                }
            }

            // reset loglegacy config setting
            if ($loglegacy) {
                set_config('loglegacy', $loglegacy, 'logstore_legacy');
            }
        }
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2014121044;
    if ($oldversion < $newversion) {
        if (function_exists('get_log_manager')) {
            if ($dbman->table_exists('log')) {
                $select = 'module = ? AND '.$DB->sql_like('action', '?');
                $DB->set_field_select('log', 'action', 'attempt', $select, array('hotpot', '%attempt_started'));
                $DB->set_field_select('log', 'action', 'report',  $select, array('hotpot', '%report_viewed'));
                $DB->set_field_select('log', 'action', 'review',  $select, array('hotpot', '%attempt_reviewed'));
                $DB->set_field_select('log', 'action', 'submit',  $select, array('hotpot', '%attempt_submitted'));
            }
        }
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2015102678;
    if ($oldversion < $newversion) {
        // add custom completion fields for TaskChain module
        $table = new xmldb_table('hotpot');
        $fields = array(
            new xmldb_field('completionmingrade',  XMLDB_TYPE_FLOAT, '6,2', null, XMLDB_NOTNULL, null, 0.00, 'timemodified'),
            new xmldb_field('completionpass',      XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0,    'completionmingrade'),
            new xmldb_field('completioncompleted', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, 0,    'completionpass')
        );
        foreach ($fields as $field) {
            xmldb_hotpot_fix_previous_field($dbman, $table, $field);
            if ($dbman->field_exists($table, $field)) {
                $dbman->change_field_type($table, $field);
            } else {
                $dbman->add_field($table, $field);
            }
        }
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2015110382;
    if ($oldversion < $newversion) {
        $select = 'cm.*, m.name AS modname';
        $from   = '{course_modules} cm '.
                  'JOIN {modules} m ON cm.module = m.id '.
                  'JOIN {hotpot} h ON cm.instance = h.id';
        $where  = 'm.name = ? AND (h.completionmingrade > ? OR h.completionpass = ? OR h.completioncompleted = ?)';
        $order  = 'cm.course';
        $params = array('hotpot', 0.00, 1, 1);
        if ($cms = $DB->get_records_sql("SELECT $select FROM $from WHERE $where ORDER BY $order", $params)) {
            $course = null;
            $completion = null;
            foreach ($cms as $cm) {
                if ($course && $course->id==$cm->course) {
                    // same course as previous $cm
                } else {
                    if ($course = $DB->get_record('course', array('id' => $cm->course))) {
                        $completion = new completion_info($course);
                    } else {
                        $completion = null; // shouldn't happen !!
                    }
                }
                if ($completion) {
                    $completion->reset_all_state($cm);
                }
            }
        }
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    $newversion = 2016100600;
    if ($oldversion < $newversion) {
        $empty_cache = true;
        upgrade_mod_savepoint(true, "$newversion", 'hotpot');
    }

    if ($empty_cache) {
        $DB->delete_records('hotpot_cache');
    }

    return true;
}

function xmldb_hotpot_locate_externalfile($contextid, $component, $filearea, $itemid, $filepath, $filename) {
    global $CFG, $DB;

    if (! class_exists('repository')) {
        return false; // Moodle <= 2.2 has no repositories
    }

    static $repositories = null;
    if ($repositories===null) {
        $exclude_types = array('recent', 'upload', 'user', 'areafiles');
        $repositories = repository::get_instances();
        foreach (array_keys($repositories) as $id) {
            if (method_exists($repositories[$id], 'get_typename')) {
                $type = $repositories[$id]->get_typename();
            } else {
                $type = $repositories[$id]->options['type'];
            }
            if (in_array($type, $exclude_types)) {
                unset($repositories[$id]);
            }
        }
        // ensure upgraderunning is set
        if (empty($CFG->upgraderunning)) {
            $CFG->upgraderunning = null;
        }
    }

    // get file storage
    $fs = get_file_storage();

    // the following types repository use encoded params
    $encoded_types = array('user', 'areafiles', 'coursefiles');

    foreach ($repositories as $id => $repository) {

        // "filesystem" path is in plain text, others are encoded
        if (method_exists($repositories[$id], 'get_typename')) {
            $type = $repositories[$id]->get_typename();
        } else {
            $type = $repositories[$id]->options['type'];
        }
        $encodepath = in_array($type, $encoded_types);

        // save $root_path, because it may get messed up by
        // $repository->get_listing($path), if $path is non-existant
        if (method_exists($repository, 'get_rootpath')) {
            $root_path = $repository->get_rootpath();
        } else if (isset($repository->root_path)) {
            $root_path = $repository->root_path;
        } else {
            $root_path = false;
        }

        // get repository type
        switch (true) {
            case isset($repository->options['type']):
                $type = $repository->options['type'];
                break;
            case isset($repository->instance->typeid):
                $type = repository::get_type_by_id($repository->instance->typeid);
                $type = $type->get_typename();
                break;
            default:
                $type = ''; // shouldn't happen !!
        }

        $path = $filepath;
        $source = trim($filepath.$filename, '/');

        // setup $params for path encoding, if necessary
        $params = array();
        if ($encodepath) {
            $listing = $repository->get_listing();
            switch (true) {
                case isset($listing['list'][0]['source']): $param = 'source'; break; // file
                case isset($listing['list'][0]['path']):   $param = 'path';   break; // dir
                default: return false; // shouldn't happen !!
            }
            $params = file_storage::unpack_reference($listing['list'][0][$param], true);

            $params['filepath'] = '/'.$path.($path=='' ? '' : '/');
            $params['filename'] = '.'; // "." signifies a directory
            $path = file_storage::pack_reference($params);
        }

        // set $nodepathmode for filesystem repository on Moodle >= 3.1
        $nodepathmode = '';
        if ($type=='filesystem') {
            if (method_exists($repository, 'build_node_path')) {
                $nodepathmode = 'browse';
                // the following code mimics the protected method
                // $repository->build_node_path($nodepathmode, $path)
                $path = $nodepathmode.':'.base64_encode($path).':';
            }
        }

        // reset $repository->root_path (filesystem repository only)
        if ($root_path) {
            $repository->root_path = $root_path;
        }

        // unset upgraderunning because it can cause get_listing() to fail
        $upgraderunning = $CFG->upgraderunning;
        $CFG->upgraderunning = null;

        // Note: we use "@" to suppress warnings in case $path does not exist
        $listing = @$repository->get_listing($path);

        // restore upgraderunning flag
        $CFG->upgraderunning = $upgraderunning;

        // check each file to see if it is the one we want
        foreach ($listing['list'] as $file) {

            switch (true) {
                case isset($file['source']): $param = 'source'; break; // file
                case isset($file['path']):   $param = 'path';   break; // dir
                default: continue; // shouldn't happen !!
            }

            if ($encodepath) {
                $file[$param] = file_storage::unpack_reference($file[$param]);
                $file[$param] = trim($file[$param]['filepath'], '/').'/'.$file[$param]['filename'];
            }

            if ($file[$param]==$source) {

                if ($encodepath) {
                    $params['filename'] = $filename;
                    $source = file_storage::pack_reference($params);
                }

                $file_record = array(
                    'contextid' => $contextid, 'component' => $component, 'filearea' => $filearea,
                    'sortorder' => 0, 'itemid' => 0, 'filepath' => $filepath, 'filename' => $filename
                );

                if ($file = $fs->create_file_from_reference($file_record, $id, $source)) {
                    return $file;
                }

                break; // try another repository
            }
        }
    }

    // external file not found (or found but not created)
    return false;
}

/**
 * xmldb_hotpot_move_file
 *
 * move a file or folder (within the same context)
 * if $file is a directory, then all subfolders and files will also be moved
 * if the destination file/folder already exists, then $file will be deleted
 *
 * @param stored_file $file
 * @param string $new_filepath
 * @param string $new_filename (optional, default='')
 * @return void, but may update filearea
 */
function xmldb_hotpot_move_file($file, $new_filepath, $new_filename='') {

    $fs = get_file_storage();

    $contextid = $file->get_contextid();
    $component = $file->get_component();
    $filearea  = $file->get_filearea();
    $itemid    = $file->get_itemid();

    $old_filepath = $file->get_filepath();
    $old_filename = $file->get_filename();

    if ($file->is_directory()) {
        $children = $fs->get_directory_files($contextid, $component, $filearea, $itemid, $old_filepath);
        $old_filepath = '/^'.preg_quote($old_filepath, '/').'/';
        foreach ($children as $child) {
            xmldb_hotpot_move_file($child, preg_replace($old_filepath, $new_filepath, $child->get_filepath(), 1));
        }
    }

    if ($new_filename=='') {
        $new_filename = $old_filename;
    }

    if ($fs->file_exists($contextid, $component, $filearea, $itemid, $new_filepath, $new_filename)) {
        $file->delete(); // new file already exists
    } else {
        $file->rename($new_filepath, $new_filename);
    }
}

/**
 * xmldb_hotpot_fix_previous_fields
 *
 * @param xxx $dbman
 * @param xmldb_table $table
 * @param array of xmldb_field $fields (passed by reference)
 * @return void, but may update some items in $fields array
 */
function xmldb_hotpot_fix_previous_fields($dbman, $table, &$fields) {
    foreach ($fields as $i => $field) {
        xmldb_hotpot_fix_previous_field($dbman, $table, $fields[$i]);
    }
}

/**
 * xmldb_hotpot_fix_previous_field
 *
 * @param xxx $dbman
 * @param xmldb_table $table
 * @param xmldb_field $field (passed by reference)
 * @return void, but may update $field->previous
 */
function xmldb_hotpot_fix_previous_field($dbman, $table, &$field) {
    $previous = $field->getPrevious();
    if (empty($previous) || $dbman->field_exists($table, $previous)) {
        // $previous field exists - do nothing
    } else {
        // $previous field does not exist, so remove it
        $field->setPrevious(null);
    }
}
