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

namespace block_quickmail\services\grade_calculator;

defined('MOODLE_INTERNAL') || die();

class course_grade_report extends \grade_report {

    public $user;

    /**
     * show course/category totals if they contain hidden items
     */
    public $showtotalsifcontainhidden;

    public function __construct($courseid, $coursecontext, $userid) {
        parent::__construct($courseid, null, $coursecontext);

        global $DB, $CFG;

        // Get the user to be graded.
        $this->user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

        // Necessary for parent report.
        $this->showtotalsifcontainhidden[$courseid] = grade_get_setting(
                                                          $courseid,
                                                          'report_overview_showtotalsifcontainhidden',
                                                          $CFG->grade_report_overview_showtotalsifcontainhidden);
    }

    // Necessary for implementation of \grade_report abstract.
    public function process_action($target, $action) {
    }

    // Necessary for implementation of \grade_report abstract.
    public function process_data($data) {
        return $this->screen->process($data);
    }

    // Getter for the \grade_report's method.
    public function get_blank_hidden_total_and_adjust_bounds($courseid, $coursetotalitem, $finalgrade) {
        return $this->blank_hidden_total_and_adjust_bounds($courseid, $coursetotalitem, $finalgrade);
    }

}
