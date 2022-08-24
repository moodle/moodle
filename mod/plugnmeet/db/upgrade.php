<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin upgrade steps are defined here.
 *
 * @package     mod_plugnmeet
 * @category    upgrade
 * @copyright   2022 mynaparrot
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/upgradelib.php');

/**
 * Execute mod_plugnmeet upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_plugnmeet_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // For further information please read {@link https://docs.moodle.org/dev/Upgrade_API}.
    //
    // You will also have to create the db/install.xml file by using the XMLDB Editor.
    // Documentation for the XMLDB Editor can be found at {@link https://docs.moodle.org/dev/XMLDB_editor}.

    $table = new xmldb_table('plugnmeet');
    $available = new xmldb_field('available', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
    $deadline = new xmldb_field('deadline', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);

    // Conditionally launch add field newfield.
    if (!$dbman->field_exists($table, $available)) {
        $dbman->add_field($table, $available);
    }
    if (!$dbman->field_exists($table, $deadline)) {
        $dbman->add_field($table, $deadline);
    }

    return true;
}
