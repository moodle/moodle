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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

/**
 * Class plugin_usercompletion
 *
 * @package   block_configurable_reports
 * @author    Juan leyva <http://www.twitter.com/jleyvadelgado>
 */
class plugin_usercompletion extends plugin_base {

    /**
     * Init
     *
     * @return void
     */
    public function init(): void {
        $this->fullname = get_string('usercompletion', 'block_configurable_reports');
        $this->type = 'undefined';
        $this->form = false;
        $this->reporttypes = ['users'];
    }

    /**
     * Summary
     *
     * @param object $data
     * @return string
     */
    public function summary(object $data): string {
        return get_string('usercompletionsummary', 'block_configurable_reports');
    }

    /**
     * Execute
     *
     * @param array $data
     * @param object $row
     * @param object $user
     * @param int $courseid
     * @param int $starttime
     * @param int $endtime
     * @return string
     */
    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0) {
        global $DB, $CFG;

        // Data -> Plugin configuration data.
        // Row -> Complet user row c->id, c->fullname, etc...
        require_once("{$CFG->libdir}/completionlib.php");

        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

        $info = new completion_info($course);

        // Is course complete?
        $coursecomplete = $info->is_course_complete($row->id);

        // Load course completion.
        $params = [
            'userid' => $row->id,
            'course' => $course->id,
        ];
        $ccompletion = new completion_completion($params);

        // Has this user completed any criteria?
        $criteriacomplete = $info->count_course_user_data($row->id);

        $content = "";
        if ($coursecomplete) {
            $content .= get_string('complete');
        } else if (!$criteriacomplete && !$ccompletion->timestarted) {
            $content .= html_writer::tag('i', get_string('notyetstarted', 'completion'));
        } else {
            $content .= html_writer::tag('i', get_string('inprogress', 'completion'));
        }

        return $content;
    }

}
