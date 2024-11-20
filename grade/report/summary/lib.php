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

defined('MOODLE_INTERNAL') || die;

/**
 * Definition of the summary report class
 *
 * @package   gradereport_summary
 * @copyright 2022 Ilya Tregubov <ilya@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/grade/report/lib.php');

/**
 * Class providing an API for the summary report building.
 *
 * @package   gradereport_summary
 * @uses      grade_report
 * @copyright 2022 Ilya Tregubov <ilya@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_report_summary extends grade_report {

    /**
     * Capability check caching
     *
     * @var boolean $canviewhidden
     */
    public $canviewhidden;

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     *
     * @param int $courseid
     * @param object $gpr grade plugin return tracking object
     * @param context_course $context
     */
    public function __construct($courseid, $gpr, $context) {
        parent::__construct($courseid, $gpr, $context);

        $this->canviewhidden = has_capability('moodle/grade:viewhidden', $context);
        $this->setup_groups();
    }

    /**
     * Processes a single action against a category, grade_item or grade. Not used in summary report.
     *
     * @param string $target eid ({type}{id}, e.g. c4 for category4)
     * @param string $action Which action to take (edit, delete etc...)
     */
    public function process_action($target, $action) {
    }

    /**
     * Handles form data sent by this report for this report. Not used in summary report.
     *
     * @param array $data
     */
    public function process_data($data) {
    }

    /**
     * To check if we only need to include active enrolments.
     *
     * @return bool
     */
    public function show_only_active(): bool {

        // Limit to users with an active enrolment.
        $defaultgradeshowactiveenrol = !empty($CFG->grade_report_showonlyactiveenrol);
        $showonlyactiveenrol = get_user_preferences('grade_report_showonlyactiveenrol', $defaultgradeshowactiveenrol);
        return $showonlyactiveenrol ||
            !has_capability('moodle/course:viewsuspendedusers', $this->context);
    }
}
