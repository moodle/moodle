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
 * Essay question type upgrade code.
 *
 * @package    qtype
 * @subpackage essay
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the essay question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_essay_upgrade($oldversion) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/feedback/db/upgradelib.php');
    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Add reponse limit functionality.
    if ($oldversion < 2017110200) {
        $table = new xmldb_table('qtype_essay_options');
        $field = new xmldb_field('responselimitpolicy', XMLDB_TYPE_INTEGER, '4', null, true, false, 0, 'responsetemplateformat');
        $dbman->add_field($table, $field);
        $field = new xmldb_field('wordlimit', XMLDB_TYPE_INTEGER, '4', null, false, false, null, 'responselimitpolicy');
        $dbman->add_field($table, $field);
        $field = new xmldb_field('charlimit', XMLDB_TYPE_INTEGER, '4', null, false, false, null, 'wordlimit');
        $dbman->add_field($table, $field);

        upgrade_plugin_savepoint(true, 2017110200, 'qtype', 'essay');
    }

    return true;
}
