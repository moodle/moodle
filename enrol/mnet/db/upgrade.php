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
 * Keeps track of upgrades to the enrol_mnet plugin
 *
 * @package    enrol
 * @subpackage mnet
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_mnet_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    // when core upgraded all legacy enrol mnet plugins, it created instances of the plugin
    // here we set customint1 to 0 which means 'open for all hosts' (legacy behaviour)
    if ($oldversion < 2010071701) {
        $DB->set_field('enrol', 'customint1', 0, array('enrol' => 'mnet', 'customint1' => null));
        upgrade_plugin_savepoint(true, 2010071701, 'enrol', 'mnet');
    }

    // Moodle v2.1.0 release upgrade line
    // Put any upgrade step following this

    return true;
}
