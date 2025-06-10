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
 * Database upgrades.
 *
 * @package report
 * @subpackage lsusql
 * @copyright 2013 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_report_lsusql_install() {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Build the tables.
    $csqlctable = new xmldb_table('report_customsql_categories');
    $csqlqtable = new xmldb_table('report_customsql_queries');
    $lsqlctable = new xmldb_table('report_lsusql_categories');
    $lsqlqtable = new xmldb_table('report_lsusql_queries');

    // I need strings.
    $pre = $CFG->prefix;
    $csqlcstring = 'report_customsql_categories';
    $csqlqstring = 'report_customsql_queries';
    $lsqlcstring = 'report_lsusql_categories';
    $lsqlqstring = 'report_lsusql_queries';

    // Build the tables.
    $csqlctable = new xmldb_table($csqlcstring);
    $csqlqtable = new xmldb_table($csqlqstring);
    $lsqlctable = new xmldb_table($lsqlcstring);
    $lsqlqtable = new xmldb_table($lsqlqstring);

    // Define fields to be added to report_lsusql_queries.
    $ulfield = new xmldb_field('userlimit', XMLDB_TYPE_TEXT, null, null, null, null, null, 'capability');
    $defield = new xmldb_field('donotescape', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'runable');
    $umfield = new xmldb_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'customdir');
    $tcfield = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'usermodified');
    $tmfield = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timecreated');


    // If we're installing on top of AHDQ so drop the new ones.
    if ($dbman->table_exists($csqlctable) &&
        $dbman->table_exists($csqlqtable) &&
        $dbman->table_exists($lsqlctable) &&
        $dbman->table_exists($lsqlqtable)) {

            $sql = "DROP TABLE " . $pre . $lsqlcstring . ", " . $pre . $lsqlqstring;
            $DB->execute($sql);
            mtrace("Dropped <strong>$lsqlcstring</strong> and <strong>$lsqlqstring</strong> tables.<br><br>\n");

        // Sanity check if the lsusql tables exist copy them.
        if ($dbman->table_exists($csqlctable)) {
            // Create the table if the corresponding AHDQ table exists.
            $sql = "CREATE TABLE IF NOT EXISTS " . $pre . $lsqlcstring . " SELECT * FROM " . $pre . $csqlcstring;
            $DB->execute($sql);
            mtrace("Copied <strong>$csqlcstring</strong> table to <strong>$lsqlcstring</strong>.<br><br>\n");
            // Make sure the table is auto-incrementing.
            $sql = "ALTER TABLE " . $pre . $lsqlcstring . " CHANGE id id bigint(10) AUTO_INCREMENT PRIMARY KEY";
            $DB->execute($sql);
            mtrace("Converted <strong>$lsqlcstring</strong> to auto-incrementing table.<br><br>\n");
        }

        // Sanity check if the lsusql tables exist copy them.
        if ($dbman->table_exists($csqlqtable)) {
            // Create the table if the corresponding AHDQ table exists.
            $sql = "CREATE TABLE IF NOT EXISTS " . $pre . $lsqlqstring . " SELECT * FROM " . $pre . $csqlqstring;
            $DB->execute($sql);
            mtrace("Copied <strong>$csqlqstring</strong> table to <strong>$lsqlqstring</strong>.<br><br>\n");
            // Make sure the table is auto-incrementing.
            $sql = "ALTER TABLE " . $pre . $lsqlqstring . " CHANGE id id bigint(10) AUTO_INCREMENT PRIMARY KEY";
            $DB->execute($sql);
            mtrace("Converted <strong>$lsqlqstring</strong> to auto-incrementing table.<br><br>\n");
        }

        // Conditionally add the userlimit field.
        if (!$dbman->field_exists($lsqlqtable, $ulfield)) {
            $dbman->add_field($lsqlqtable, $ulfield);
            mtrace("Added the field <strong>userlimit</strong> to the <strong>$lsqlqstring</strong> table.<br><br>\n");
        }

        // Conditionally add the donotescape field.
        if (!$dbman->field_exists($lsqlqtable, $defield)) {
            $dbman->add_field($lsqlqtable, $defield);
            mtrace("Added the field <strong>donotescape</strong> to the <strong>$lsqlqstring</strong> table.<br><br>\n");
        }

        // Conditionally launch add field usermodified.
        if (!$dbman->field_exists($lsqlqtable, $umfield)) {
            $dbman->add_field($lsqlqtable, $umfield);
            mtrace("Added the field <strong>usermodified</strong> to the <strong>$lsqlqstring</strong> table.<br><br>\n");
        }

        // Conditionally launch add key usermodified.
        if (!$lsqlqtable->getKey('usermodified')) {
            $key = new xmldb_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
            $dbman->add_key($lsqlqtable, $key);
        }

        // Conditionally launch add field timecreated.
        if (!$dbman->field_exists($lsqlqtable, $tcfield)) {
            $dbman->add_field($lsqlqtable, $tcfield);
            mtrace("Added the field <strong>timecreated</strong> to the <strong>$lsqlqstring</strong> table.<br><br>\n");
        }

        // Conditionally launch add field timemodified.
        if (!$dbman->field_exists($lsqlqtable, $tmfield)) {
            $dbman->add_field($lsqlqtable, $tmfield);
            mtrace("Added the field <strong>timemodified</strong> to the <strong>$lsqlqstring</strong> table.<br><br>\n");

            // If we don't have this field, we can be confident emails are in username format.
            // Convert the contents of the emailto column from a list of usernames to a list of user ids.
            // Transfer data from old columns into details.
            // (There seem to be just a few thousand of these, so not too bad).
            $queries = $DB->get_records_select('report_lsusql_queries', 'emailto <> ?', [''], 'id', 'id, emailto');
            $total = count($queries);

            if ($total > 0) {
                // First get all the different usernames that appear.
                $usernames = [];
                foreach ($queries as $query) {
                    foreach (preg_split("/[\s,;]+/", $query->emailto) as $username) {
                        $usernames[$username] = 1;
                    }
                }

                // Then get the corresponding user ids.
                $userids = $DB->get_records_list('user', 'username', array_keys($usernames), '', 'username, id');

                // Now  do the update.
                $progressbar = new progress_bar('report_lsusql_emailto_upgrade', 500, true);
                $done = 0;
                foreach ($queries as $query) {
                    $progressbar->update($done, $total,
                            "Updating LSU DB API Query email recipients - {$done}/{$total} (id = {$query->id}).");

                    $queryuserids = [];
                    foreach (preg_split("/[\s,;]+/", $query->emailto) as $username) {
                        if (isset($userids[$username])) {
                            $queryuserids[] = $userids[$username]->id;
                        }
                    }
                    sort($queryuserids);

                    $DB->set_field('report_lsusql_queries', 'emailto', implode(',', $queryuserids), ['id' => $query->id]);
                    $done += 1;
                }

                $progressbar->update($done, $total, "Updating LSU DB API Query email recipients - {$done}/{$total}.");
            }
        }
    }

    // Create the default 'Miscellaneous' category if it is missing.
    $category = new stdClass();
    $category->name = get_string('defaultcategory', 'report_lsusql');
    if (!$DB->record_exists($lsqlcstring, array('name' => $category->name))) {
        $DB->insert_record($lsqlcstring, $category);
        mtrace("Inserted the default $category->name category to the $lsqlcstring table.<br><br>");
    }

    return true;
}
