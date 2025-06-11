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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/quickmail/backup/moodle2/restore_quickmail_stepslib.php');

class restore_quickmail_block_task extends restore_block_task {
    public function history_exists() {
        // Weird... the folder doesn't exist.
        $fullpath = $this->get_taskbasepath();
        if (empty($fullpath)) {
            return false;
        }

        // Issue #45: trying to restore from a non-existent logfile.
        $fullpath = rtrim($fullpath, '/') . '/emaillogs_and_block_configuration.xml';
        if (!file_exists($fullpath)) {
            return false;
        }

        return true;
    }

    protected function define_my_settings() {
        // Nothing to do.
        if (!$this->history_exists()) {
            return;
        }

        $rootsettings = $this->get_info()->root_settings;

        $defaultvalue = false;
        $changeable = false;

        $isblocks = isset($rootsettings['blocks']) && $rootsettings['blocks'];
        $isusers = isset($rootsettings['users']) && $rootsettings['users'];

        if ($isblocks && $isusers) {
            $defaultvalue = true;
            $changeable = true;
        }

        $restorehistory = new restore_generic_setting('restore_quickmail_history',
            base_setting::IS_BOOLEAN, $defaultvalue);
        $restorehistory->set_ui(new backup_setting_ui_select(
            $restorehistory, get_string('restore_history', 'block_quickmail'),
            array(1 => get_string('yes'), 0 => get_string('no'))
        ));

        if (!$changeable) {
            $restorehistory->set_value($defaultvalue);
            $restorehistory->set_status(backup_setting::LOCKED_BY_CONFIG);
            $restorehistory->set_visibility(backup_setting::HIDDEN);
        }

        $this->add_setting($restorehistory);
        $this->get_setting('users')->add_dependency($restorehistory);
        $this->get_setting('blocks')->add_dependency($restorehistory);

        $overwritehistory = new restore_course_generic_setting('overwrite_quickmail_history', base_setting::IS_BOOLEAN, false);
        $overwritehistory->set_ui(new backup_setting_ui_select(
            $overwritehistory,
            get_string('overwrite_history', 'block_quickmail'),
            array(1 => get_string('yes'), 0 => get_string('no'))
        ));

        if ($this->get_target() != backup::TARGET_CURRENT_DELETING && $this->get_target() != backup::TARGET_EXISTING_DELETING) {

            $overwritehistory->set_value(false);
            $overwritehistory->set_status(backup_setting::LOCKED_BY_CONFIG);
        }

        $this->add_setting($overwritehistory);
        $restorehistory->add_dependency($overwritehistory);
    }

    protected function define_my_steps() {
        if ($this->history_exists()) {
            $this->add_step(new restore_quickmail_log_structure_step(
                'quickmail_structure', 'emaillogs_and_block_configuration.xml'
            ));
        }
    }

    public function get_fileareas() {
        return array();
    }

    public function get_configdata_encoded_attributes() {
        return array();
    }

    public static function define_decode_contents() {
        // TODO: Perhaps we'll need this when moving away from email zip attachments.
        return array();
    }

    public static function define_decode_rules() {
        return array();
    }
}
