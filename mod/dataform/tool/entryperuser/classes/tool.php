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
 * @package dataformtool_entryperuser
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

class dataformtool_entryperuser_tool {
    /**
     *
     */
    public static function execute($df) {
        global $DB;

        // Get gradebook users.
        if (!$users = $df->grade_manager->get_gradebook_users()) {
            return;
        }

        // Construct entries data.
        $data = (object) array('eids' => array());
        $entryid = -1;
        foreach ($users as $userid => $unused) {
            $data->eids[$entryid] = $entryid;
            $data->{"entry_{$entryid}_userid"} = $userid;
            $entryid--;
        }
        // Add entries.
        $em = new mod_dataform_entry_manager($df->id);
        $processed = $em->process_entries('update', $data->eids, $data, true);

        if (is_array($processed)) {
            list($strnotify, $processedeids) = $processed;
            if ($entriesprocessed = ($processedeids ? count($processedeids) : 0)) {
                return array('success', $strnotify);
            }
        }
        return array('problem', get_string('entriesupdated', 'dataform', get_string('no')));
    }
}
