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
 * @package    gradingform
 * @subpackage rubric
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Keeps track or rubric plugin upgrade path
 *
 * @todo get rid of this before merging into the master branch MDL-29798
 * @param int $oldversion the DB version of currently installed plugin
 * @return bool true
 */
function xmldb_gradingform_rubric_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    if ($oldversion < 2011101400) {
        // add key uq_instance_criterion (unique)
        $table = new xmldb_table('gradingform_rubric_fillings');
        $key = new xmldb_key('uq_instance_criterion', XMLDB_KEY_UNIQUE, array('forminstanceid', 'criterionid'));
        $dbman->add_key($table, $key);
        upgrade_plugin_savepoint(true, 2011101400, 'gradingform', 'rubric');
    }

    return true;
}
