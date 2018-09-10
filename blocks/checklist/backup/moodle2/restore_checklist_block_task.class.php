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
 * Match checklist block to the newly restored checklist activity, after restoring from backup.
 *
 * @package   block_checklist
 * @copyright 2014 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class restore_checklist_block_task extends restore_block_task {
    protected function define_my_settings() {
        // No settings.
    }

    public function get_fileareas() {
        return array(); // No fileareas.
    }

    public function get_configdata_encoded_attributes() {
        return array(); // No special handleing of configdata.
    }

    /**
     * Define (add) particular steps that each block can have
     */
    protected function define_my_steps() {
        // No steps to take.
    }

    public function after_restore() {
        global $DB;

        // Get the blockid.
        $blockid = $this->get_blockid();

        // Extract block configdata and update it to point to the new checklist.
        if ($configdata = $DB->get_field('block_instances', 'configdata', array('id' => $blockid))) {
            $config = unserialize(base64_decode($configdata));
            if (!empty($config->checklistid)) {
                // Get checklist mapping and replace it in config.
                $checklistmap = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'checklist', $config->checklistid);
                if ($checklistmap) {
                    $config->checklistid = $checklistmap->newitemid;
                } else {
                    $config->checklistid = 0;
                }
                $configdata = base64_encode(serialize($config));
                $DB->set_field('block_instances', 'configdata', $configdata, array('id' => $blockid));
            }
        }
    }

    static public function define_decode_contents() {
        return array(); // Nothing to do.
    }

    static public function define_decode_rules() {
        return array(); // Nothing to do.
    }
}