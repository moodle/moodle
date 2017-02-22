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

// This file keeps track of upgrades to
// the feedback module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die();

function xmldb_feedback_upgrade($oldversion) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/feedback/db/upgradelib.php');

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2016031600) {
        // Remove labels from all 'captcha' and 'label' items.
        $DB->execute('UPDATE {feedback_item} SET label = ? WHERE typ = ? OR typ = ?',
                array('', 'captcha', 'label'));

        // Data savepoint reached.
        upgrade_mod_savepoint(true, 2016031600, 'feedback');
    }

    if ($oldversion < 2016040100) {

        // In order to keep the previous "Analysis" results unchanged,
        // set all multiple-answer multiplechoice questions as "Do not analyse empty submits"="Yes"
        // because prior to this date this setting did not work.

        $sql = "UPDATE {feedback_item} SET options = " . $DB->sql_concat('?', 'options') .
                " WHERE typ = ? AND presentation LIKE ? AND options NOT LIKE ?";
        $params = array('i', 'multichoice', 'c%', '%i%');
        $DB->execute($sql, $params);

        // Feedback savepoint reached.
        upgrade_mod_savepoint(true, 2016040100, 'feedback');
    }

    if ($oldversion < 2016051103) {

        // Define index completed_item (unique) to be added to feedback_value.
        $table = new xmldb_table('feedback_value');
        $index = new xmldb_index('completed_item', XMLDB_INDEX_UNIQUE, array('completed', 'item', 'course_id'));

        // Conditionally launch add index completed_item.
        if (!$dbman->index_exists($table, $index)) {
            mod_feedback_upgrade_delete_duplicate_values();
            $dbman->add_index($table, $index);
        }

        // Feedback savepoint reached.
        upgrade_mod_savepoint(true, 2016051103, 'feedback');
    }

    if ($oldversion < 2016051104) {

        // Define index completed_item (unique) to be added to feedback_valuetmp.
        $table = new xmldb_table('feedback_valuetmp');
        $index = new xmldb_index('completed_item', XMLDB_INDEX_UNIQUE, array('completed', 'item', 'course_id'));

        // Conditionally launch add index completed_item.
        if (!$dbman->index_exists($table, $index)) {
            mod_feedback_upgrade_delete_duplicate_values(true);
            $dbman->add_index($table, $index);
        }

        // Feedback savepoint reached.
        upgrade_mod_savepoint(true, 2016051104, 'feedback');
    }

    if ($oldversion < 2016051105) {

        // Define field courseid to be added to feedback_completed.
        $table = new xmldb_table('feedback_completed');
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'anonymous_response');

        // Conditionally launch add field courseid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            // Run upgrade script to fill the new field courseid with the data from feedback_value table.
            mod_feedback_upgrade_courseid(false);
        }

        // Define field courseid to be added to feedback_completedtmp.
        $table = new xmldb_table('feedback_completedtmp');
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'anonymous_response');

        // Conditionally launch add field courseid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            // Run upgrade script to fill the new field courseid with the data from feedback_valuetmp table.
            mod_feedback_upgrade_courseid(true);
        }

        // Define table feedback_tracking to be dropped.
        $table = new xmldb_table('feedback_tracking');

        // Conditionally launch drop table for feedback_tracking.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Feedback savepoint reached.
        upgrade_mod_savepoint(true, 2016051105, 'feedback');
    }

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
