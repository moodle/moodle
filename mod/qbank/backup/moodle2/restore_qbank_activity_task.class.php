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

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/mod/qbank/backup/moodle2/restore_qbank_stepslib.php');

/**
 * The task that provides a complete restore of mod_qbank.
 *
 * @package     mod_qbank
 * @copyright   2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author      Simon Adams <simon.adams@catalyst-eu.net>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qbank_activity_task extends restore_activity_task {

    /**
     * Defines particular settings that this activity can have.
     */
    protected function define_my_settings(): void {
        // No particular settings for this activity.
    }

    /**
     * Defines particular steps that this activity can have.
     *
     * @return void.
     */
    protected function define_my_steps(): void {
        // Qbank only has one structure step.
        $this->add_step(new restore_qbank_activity_structure_step('qbank_structure', 'qbank.xml'));
    }

    /**
     * Defines the contents in the activity that must be processed by the link decoder.
     *
     * @return array.
     */
    public static function define_decode_contents(): array {
        return [];
    }

    /**
     * Defines the decoding rules for links belonging to the activity to be executed by the link decoder.
     *
     * @return restore_decode_rule[].
     */
    public static function define_decode_rules(): array {
        $rules = [];

        $rules[] = new restore_decode_rule('QBANKVIEWBYID', '/mod/qbank/view.php?id=$1', 'course_module');

        return $rules;
    }

    /**
     * Defines the restore log rules that will be applied by the
     * {@see restore_logs_processor} when restoring mod_qbank logs. It
     * must return one array of {@see restore_log_rule} objects.
     *
     * @return restore_log_rule[].
     */
    public static function define_restore_log_rules(): array {
        return [];
    }
}
