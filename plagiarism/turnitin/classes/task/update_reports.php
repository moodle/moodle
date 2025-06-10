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
 * Update report Scores from Turnitin.
 *
 * @package    plagiarism_turnitin
 * @author     John McGettrick http://www.turnitin.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace plagiarism_turnitin\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Update report Scores from Turnitin.
 */
class update_reports extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('updatereportscores', 'plagiarism_turnitin');
    }

    public function execute() {
        global $CFG;

        require_once($CFG->dirroot.'/plagiarism/turnitin/lib.php');
        $plagiarismturnitin = new \plagiarism_plugin_turnitin();
        if (!$plagiarismturnitin->is_plugin_configured()) {
            return;
        }
        plagiarism_turnitin_update_reports();
    }
}