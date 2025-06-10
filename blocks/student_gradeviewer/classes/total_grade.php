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
 * @package    block_student_gradeviewer
 * @copyright  2008 Onwards - Louisiana State University
 * @copyright  2008 Onwards - Adam Zapletal, Philip Cali, Jason Peak, Chad Mazilly, Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/grade_grade.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/grade/report/lib.php');

// Class to get grade report functions and variables from parent class in grade/report/lib.php.
// This insanity was copied from LSU's Quick Edit.

class grade_report_student_gradeviewer extends grade_report {

    /**
     * The user.
     * @var object $user
     */
    public $user;

    /**
     * The user's courses.
     * @var array $courses
     */
    public $courses;

    /**
     * Show course/category totals if they contain hidden items
     * @var bool $showtotalsifcontainhidden
     */
    public $showtotalsifcontainhidden;

    /**
     * An array of course ids that the user is a student in.
     * @var array $studentcourseids
     */
    public $studentcourseids;

    /**
     * An array of courses that the user is a teacher in.
     * @var array $teachercourses
     */
    public $teachercourses;

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * Run for each course the user is enrolled in.
     * @param int $userid
     * @param string $context
     */
    public function __construct($userid, $courseid, $context) {
        global $CFG, $DB;
        parent::__construct($courseid, null, $context);

        // Get the user (for later use in grade/report/lib.php).
        $user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);
        $this->user = $user;

        // Create an array (for later use in grade/report/lib.php).
        $this->showtotalsifcontainhidden = array();

        // Sanity check.
        if ($courseid) {
            // Populate this (for later use in grade/report/lib.php).
            $this->showtotalsifcontainhidden[$courseid] = grade_get_setting(
                $courseid, 'report_overview_showtotalsifcontainhidden', $CFG->grade_report_overview_showtotalsifcontainhidden
            );
        }
    }

    public function process_action($target, $action) {
    }

    public function process_data($data) {
        return $this->screen->process($data);
    }

    public function get_blank_hidden_total_and_adjust_bounds($courseid, $coursetotalitem, $finalgrade) {
        return($this->blank_hidden_total_and_adjust_bounds($courseid, $coursetotalitem, $finalgrade));
    }
}


/*
 * If a course has no course grade item (no grades at all) the system returns '-'.
 * If a user has no course grade, the system returns '-'.
 * If a user has grades and the instructor allows those grades to be viewed,
 * the system returns the final grade as stored in the database.
 * If a user has grades and the instructor has hidden the course grade item,
 * the system returns the string 'hidden'.
 * If a user has grades and the instructor has hidden some of the users grades
 * AND those hidden items impact the course grade based on the instructor's settings,
 * the system recalculates the course grade appropriately.

 * @return string - The formatted course total item value give a userid and a course id.
 */

function sg_get_grade_for_course($courseid, $userid) {
    $coursetotalitem = grade_item::fetch_course_item($courseid);
    $coursecontext = context_course::instance($courseid);
    $canviewhidden = has_capability('moodle/grade:viewhidden', $coursecontext, $userid);
    $report = new grade_report_student_gradeviewer($userid, $courseid, null, $coursecontext);
    if (!$coursetotalitem) {
        $totalgrade = '-';
    }

    $gradegradeparams = array(
        'itemid' => $coursetotalitem->id,
        'userid' => $userid
    );

    $usergradegrade = new grade_grade($gradegradeparams);
    $usergradegrade->grade_item =& $coursetotalitem;

    $finalgrade = $usergradegrade->finalgrade;

    if (!$canviewhidden and !is_null($finalgrade)) {
        $adjustedgrade = $report->get_blank_hidden_total_and_adjust_bounds($courseid,
                                                                           $coursetotalitem,
                                                                           $finalgrade);

        // We temporarily adjust the view of this grade item - because the min and
        // max are affected by the hidden values in the aggregation.
        $coursetotalitem->grademax = $adjustedgrade['grademax'];
        $coursetotalitem->grademin = $adjustedgrade['grademin'];
    } else if (!is_null($finalgrade)) {
        // Because the purpose of this block is to show MY grades as calculated for output
        // we make sure we adhere to how hiding grades impacts the total grade regardless
        // of if the user can view or not view hidden grades.
        // Example: User may be a site admin, faculty assistant, or some other
        // priveleged person and taking courses.
        // In any case, it's best to calculate grades how the instructor specifies.
        $adjustedgrade = $report->get_blank_hidden_total_and_adjust_bounds($courseid,
                                                                           $coursetotalitem,
                                                                           $finalgrade);
        // We must use the specific max/min because it can be different for
        // each grade_grade when items are excluded from sum of grades.
        $coursetotalitem->grademin = $usergradegrade->get_grade_min();
        $coursetotalitem->grademax = $usergradegrade->get_grade_max();
    }
    if (isset($adjustedgrade)) {
        $totalgrade = grade_format_gradevalue($adjustedgrade['grade'], $coursetotalitem, true);
        $sggrademax = grade_format_gradevalue($adjustedgrade['grademax'], $coursetotalitem, true);
    } else {
        $totalgrade = '-';
        $sggrademax = '-';
    }
    if ($coursetotalitem->hidden) {
        $totalgrade = get_string('hidden', 'block_grades_at_a_glance');
    }
    return array($totalgrade, $sggrademax);
}
