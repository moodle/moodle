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

class local_online_plugin {

    /**
     * Master method for kicking off student data reprocessing
     *
     * @param  boolean $run_as_adhoc  whether or not the task has been run "ad-hoc" or "scheduled" (default)
     * @return boolean
     */
    public function run_online_student_reprocess($run_as_adhoc = false) {
        global $CFG;
        require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
        ues::require_libs();
        require_once($CFG->dirroot . '/local/online/provider.php');

        $provider = new online_enrollment_provider();
        $semesters = ues_semester::in_session(time());
        $ues = enrol_get_plugin('ues');

        // Begin using the provider to pull data and manifest enrollment and note start time for reporting.
        return $provider->postprocess($ues);
    }

    /**
     * Fetches the moodle "scheduled task" object
     *
     * @return \core\task\scheduled_task
     */
    private function get_scheduled_task() {
        $task = \core\task\manager::get_scheduled_task('\local_online\task\reprocess_online_student_data');

        return $task;
    }
}