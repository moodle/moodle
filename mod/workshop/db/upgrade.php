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
 * Keeps track of upgrades to the workshop module
 *
 * @package    mod_workshop
 * @category   upgrade
 * @copyright  2009 David Mudrak <david.mudrak@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Performs upgrade of the database structure and data
 *
 * Workshop supports upgrades from version 1.9.0 and higher only. During 1.9 > 2.0 upgrade,
 * there are significant database changes.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_workshop_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    // Moodle v2.2.0 release upgrade line

    if ($oldversion < 2012033100) {
        // add the field 'phaseswitchassessment' to the 'workshop' table
        $table = new xmldb_table('workshop');
        $field = new xmldb_field('phaseswitchassessment', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'assessmentend');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2012033100, 'workshop');
    }

    /**
     * Remove all workshop calendar events
     */
    if ($oldversion < 2012041700) {
        require_once($CFG->dirroot . '/calendar/lib.php');
        $events = $DB->get_records('event', array('modulename' => 'workshop'));
        foreach ($events as $event) {
            $event = calendar_event::load($event);
            $event->delete();
        }
        upgrade_mod_savepoint(true, 2012041700, 'workshop');
    }

    /**
     * Recreate all workshop calendar events
     */
    if ($oldversion < 2012041701) {
        require_once(dirname(dirname(__FILE__)) . '/lib.php');

        $sql = "SELECT w.id, w.course, w.name, w.intro, w.introformat, w.submissionstart,
                       w.submissionend, w.assessmentstart, w.assessmentend,
                       cm.id AS cmid
                  FROM {workshop} w
                  JOIN {modules} m ON m.name = 'workshop'
                  JOIN {course_modules} cm ON (cm.module = m.id AND cm.course = w.course AND cm.instance = w.id)";

        $rs = $DB->get_recordset_sql($sql);

        foreach ($rs as $workshop) {
            $cmid = $workshop->cmid;
            unset($workshop->cmid);
            workshop_calendar_update($workshop, $cmid);
        }
        $rs->close();
        upgrade_mod_savepoint(true, 2012041701, 'workshop');
    }

    // Moodle v2.3.0 release upgrade line

    /**
     * Add new fields conclusion and conclusionformat
     */
    if ($oldversion < 2012102400) {
        $table = new xmldb_table('workshop');

        $field = new xmldb_field('conclusion', XMLDB_TYPE_TEXT, null, null, null, null, null, 'phaseswitchassessment');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('conclusionformat', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '1', 'conclusion');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2012102400, 'workshop');
    }


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this

    /**
     * Add overall feedback related fields into the workshop table.
     */
    if ($oldversion < 2013032500) {
        $table = new xmldb_table('workshop');

        $field = new xmldb_field('overallfeedbackmode', XMLDB_TYPE_INTEGER, '3', null, null, null, '1', 'conclusionformat');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('overallfeedbackfiles', XMLDB_TYPE_INTEGER, '3', null, null, null, '0', 'overallfeedbackmode');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('overallfeedbackmaxbytes', XMLDB_TYPE_INTEGER, '10', null, null, null, '100000', 'overallfeedbackfiles');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2013032500, 'workshop');
    }

    /**
     * Add feedbackauthorattachment field into the workshop_assessments table.
     */
    if ($oldversion < 2013032501) {
        $table = new xmldb_table('workshop_assessments');
        $field = new xmldb_field('feedbackauthorattachment', XMLDB_TYPE_INTEGER, '3', null, null, null, '0', 'feedbackauthorformat');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2013032501, 'workshop');
    }


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
