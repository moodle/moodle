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
 * Definition of the grade_overview_report class
 *
 * @package gradereport_overview
 * @copyright 2007 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

/**
 * Class providing an API for the overview report building and displaying.
 * @uses grade_report
 * @package gradereport_overview
 */
class grade_report_overview extends grade_report {

    /**
     * The user.
     * @var object $user
     */
    public $user;

    /**
     * A flexitable to hold the data.
     * @var object $table
     */
    public $table;

    /**
     * show student ranks
     */
    public $showrank;

    /**
     * show course/category totals if they contain hidden items
     */
    var $showtotalsifcontainhidden;

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $userid
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     */
    public function __construct($userid, $gpr, $context) {
        global $CFG, $COURSE, $DB;
        parent::__construct($COURSE->id, $gpr, $context);

        $this->showrank = grade_get_setting($this->courseid, 'report_overview_showrank', !empty($CFG->grade_report_overview_showrank));
        $this->showtotalsifcontainhidden = grade_get_setting($this->courseid, 'report_overview_showtotalsifcontainhidden', $CFG->grade_report_overview_showtotalsifcontainhidden);

        // get the user (for full name)
        $this->user = $DB->get_record('user', array('id' => $userid));

        // base url for sorting by first/last name
        $this->baseurl = $CFG->wwwroot.'/grade/overview/index.php?id='.$userid;
        $this->pbarurl = $this->baseurl;

        $this->setup_table();
    }

    /**
     * Prepares the headers and attributes of the flexitable.
     */
    public function setup_table() {
        /*
         * Table has 3 columns
         *| course  | final grade | rank (optional) |
         */

        // setting up table headers
        if ($this->showrank) {
            $tablecolumns = array('coursename', 'grade', 'rank');
            $tableheaders = array($this->get_lang_string('coursename', 'grades'),
                                  $this->get_lang_string('grade'),
                                  $this->get_lang_string('rank', 'grades'));
        } else {
            $tablecolumns = array('coursename', 'grade');
            $tableheaders = array($this->get_lang_string('coursename', 'grades'),
                                  $this->get_lang_string('grade'));
        }
        $this->table = new flexible_table('grade-report-overview-'.$this->user->id);

        $this->table->define_columns($tablecolumns);
        $this->table->define_headers($tableheaders);
        $this->table->define_baseurl($this->baseurl);

        $this->table->set_attribute('cellspacing', '0');
        $this->table->set_attribute('id', 'overview-grade');
        $this->table->set_attribute('class', 'boxaligncenter generaltable');

        $this->table->setup();
    }

    public function fill_table() {
        global $CFG, $DB, $OUTPUT;

        // MDL-11679, only show user's courses instead of all courses
        if ($courses = enrol_get_users_courses($this->user->id, false, 'id, shortname, showgrades')) {
            $numusers = $this->get_numusers(false);

            foreach ($courses as $course) {
                if (!$course->showgrades) {
                    continue;
                }

                $coursecontext = context_course::instance($course->id);

                if (!$course->visible && !has_capability('moodle/course:viewhiddencourses', $coursecontext)) {
                    // The course is hidden and the user isn't allowed to see it
                    continue;
                }

                $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));
                $courselink = html_writer::link(new moodle_url('/grade/report/user/index.php', array('id' => $course->id, 'userid' => $this->user->id)), $courseshortname);
                $canviewhidden = has_capability('moodle/grade:viewhidden', $coursecontext);

                // Get course grade_item
                $course_item = grade_item::fetch_course_item($course->id);

                // Get the stored grade
                $course_grade = new grade_grade(array('itemid'=>$course_item->id, 'userid'=>$this->user->id));
                $course_grade->grade_item =& $course_item;
                $finalgrade = $course_grade->finalgrade;

                if (!$canviewhidden and !is_null($finalgrade)) {
                    if ($course_grade->is_hidden()) {
                        $finalgrade = null;
                    } else {
                        $finalgrade = $this->blank_hidden_total($course->id, $course_item, $finalgrade);
                    }
                }

                $data = array($courselink, grade_format_gradevalue($finalgrade, $course_item, true));

                if (!$this->showrank) {
                    //nothing to do

                } else if (!is_null($finalgrade)) {
                    /// find the number of users with a higher grade
                    /// please note this can not work if hidden grades involved :-( to be fixed in 2.0
                    $params = array($finalgrade, $course_item->id);
                    $sql = "SELECT COUNT(DISTINCT(userid))
                              FROM {grade_grades}
                             WHERE finalgrade IS NOT NULL AND finalgrade > ?
                                   AND itemid = ?";
                    $rank = $DB->count_records_sql($sql, $params) + 1;

                    $data[] = "$rank/$numusers";

                } else {
                    // no grade, no rank
                    $data[] = '-';
                }

                $this->table->add_data($data);
            }
            return true;

        } else {
            echo $OUTPUT->notification(get_string('nocourses', 'grades'));
            return false;
        }
    }

    /**
     * Prints or returns the HTML from the flexitable.
     * @param bool $return Whether or not to return the data instead of printing it directly.
     * @return string
     */
    public function print_table($return=false) {
        ob_start();
        $this->table->print_html();
        $html = ob_get_clean();
        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }

    /**
     * Processes the data sent by the form (grades and feedbacks).
     * @var array $data
     * @return bool Success or Failure (array of errors).
     */
    function process_data($data) {
    }
    function process_action($target, $action) {
    }
}

function grade_report_overview_settings_definition(&$mform) {
    global $CFG;

    //show rank
    $options = array(-1 => get_string('default', 'grades'),
                      0 => get_string('hide'),
                      1 => get_string('show'));

    if (empty($CFG->grade_overviewreport_showrank)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_overview_showrank', get_string('showrank', 'grades'), $options);
    $mform->addHelpButton('report_overview_showrank', 'showrank', 'grades');

    //showtotalsifcontainhidden
    $options = array(-1 => get_string('default', 'grades'),
                      GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN => get_string('hide'),
                      GRADE_REPORT_SHOW_TOTAL_IF_CONTAINS_HIDDEN => get_string('hidetotalshowexhiddenitems', 'grades'),
                      GRADE_REPORT_SHOW_REAL_TOTAL_IF_CONTAINS_HIDDEN => get_string('hidetotalshowinchiddenitems', 'grades') );

    if (empty($CFG->grade_report_overview_showtotalsifcontainhidden)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_overview_showtotalsifcontainhidden', get_string('hidetotalifhiddenitems', 'grades'), $options);
    $mform->addHelpButton('report_overview_showtotalsifcontainhidden', 'hidetotalifhiddenitems', 'grades');
}


