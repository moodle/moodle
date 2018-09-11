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
 * This file keeps track of upgrades to the local_mobile plugin.
 *
 * @package    local_mobile
 * @copyright  2014 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_local_mobile_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2014060300) {
        // Define table local_mobile_user_devices to be dropped.
        $oldtable = new xmldb_table('local_mobile_user_devices');
        $newtable = new xmldb_table('user_devices');
        $airnotifier = new xmldb_table('message_airnotifier_devices');

        // We must be sure that the new table exists.
        if ($dbman->table_exists($newtable) and $dbman->table_exists($oldtable)) {
            // Copy the old records to the new table, we cant use and INSERT INTO SELECT FROM for avoid unique keys problems.
            if ($devices = $DB->get_records('local_mobile_user_devices')) {
                $mappings = array();

                foreach ($devices as $d) {
                    $oldid = $d->id;
                    unset($d->id);
                    try {
                        $newid = $DB->insert_record('user_devices', $d, true, true);
                        // Map oldid to newid.
                        $mappings[$oldid] = $newid;
                    } catch (dml_exception $e) {
                        // Unique keys problems, for non upgrades 2.6 or 2.7 versions may
                        // happen due to incorrect unique key definitions. Continue then.
                    }
                }
                if (!empty($mappings) and $dbman->table_exists($airnotifier)) {
                    // Update the ids in the message_airnotifier table if exists.
                    if ($airdevices = $DB->get_records('message_airnotifier_devices')) {
                        foreach ($airdevices as $d) {
                            if (empty($mappings[$d->userdeviceid])) {
                                // Delete non-existent devices.
                                $DB->delete_records('message_airnotifier_devices', array('id' => $d->id));
                            } else {
                                // Set the userdeviceid to the new one (in the nes table).
                                $DB->set_field('message_airnotifier_devices', 'userdeviceid', $mappings[$d->userdeviceid],
                                            array('id' => $d->id));
                            }
                        }
                    }
                }
            }
            $dbman->drop_table($oldtable);
        }

        upgrade_plugin_savepoint(true, 2014060300, 'local', 'mobile');
    }

    if ($oldversion < 2016102600) {
        // Update configs moved to core.
        $typeoflogin = get_config('local_mobile', 'typeoflogin');
        $forcedurlscheme = get_config('local_mobile', 'urlscheme');

        if (!empty($typeoflogin)) {
            set_config('typeoflogin', $typeoflogin, 'tool_mobile');
        }

        if (!empty($forcedurlscheme)) {
            set_config('forcedurlscheme', $forcedurlscheme, 'tool_mobile');
        }

        upgrade_plugin_savepoint(true, 2016102600, 'local', 'mobile');
    }

    if ($oldversion < 2017050401) {
        // Update configs moved to core.
        $forcelogout = get_config('local_mobile', 'forcelogout');
        $disabledfeatures = get_config('local_mobile', 'disabledfeatures');
        $custommenuitems = get_config('local_mobile', 'custommenuitems');
        $customlangstrings = get_config('local_mobile', 'customlangstrings');

        if (!empty($forcelogout)) {
            set_config('forcelogout', $forcelogout, 'tool_mobile');
        }
        if (!empty($disabledfeatures)) {
            set_config('disabledfeatures', $disabledfeatures, 'tool_mobile');
        }
        if (!empty($custommenuitems)) {
            set_config('custommenuitems', $custommenuitems, 'tool_mobile');
        }
        if (!empty($customlangstrings)) {
            set_config('customlangstrings', $customlangstrings, 'tool_mobile');
        }

        upgrade_plugin_savepoint(true, 2017050401, 'local', 'mobile');
    }

    return true;
}