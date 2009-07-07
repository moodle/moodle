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
 * File in which the overview_report class is defined.
 * @package gradebook
 */

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

/**
 * Class providing an API for the overview report building and displaying.
 * @uses grade_report
 * @package gradebook
 */
class grade_report_overview extends grade_report {

    /**
     * The user.
     * @var object $user
     */
    var $user;

    /**
     * A flexitable to hold the data.
     * @var object $table
     */
    var $table;

    /**
     * show student ranks
     */
    var $showrank;

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $userid
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     */
    function grade_report_overview($userid, $gpr, $context) {
        global $CFG, $COURSE;
        parent::grade_report($COURSE->id, $gpr, $context);

        $this->showrank = grade_get_setting($this->courseid, 'report_overview_showrank', !empty($CFG->grade_report_overview_showrank));

        // get the user (for full name)
        $this->user = get_record('user', 'id', $userid);

        // base url for sorting by first/last name
        $this->baseurl = $CFG->wwwroot.'/grade/overview/index.php?id='.$userid;
        $this->pbarurl = $this->baseurl;

        $this->setup_table();
    }

    /**
     * Prepares the headers and attributes of the flexitable.
     */
    function setup_table() {
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

    function fill_table() {
        global $CFG;

        // MDL-11679, only show 'mycourses' instead of all courses
        if ($courses = get_my_courses($this->user->id, 'c.sortorder ASC', 'id, shortname, showgrades')) {
            $numusers = $this->get_numusers(false);

            foreach ($courses as $course) {
                if (!$course->showgrades) {
                    continue;
                }
                $courselink = '<a href="'.$CFG->wwwroot.'/grade/report/user/index.php?id='.$course->id.'">'.$course->shortname.'</a>';
                $canviewhidden = has_capability('moodle/grade:viewhidden', get_context_instance(CONTEXT_COURSE, $course->id));

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
                        // This is a really ugly hack, it will be fixed in 2.0
                        $items = grade_item::fetch_all(array('courseid'=>$course->id));
                        $grades = array();
                        $sql = "SELECT g.*
                                  FROM {$CFG->prefix}grade_grades g
                                  JOIN {$CFG->prefix}grade_items gi ON gi.id = g.itemid
                                 WHERE g.userid = {$this->user->id} AND gi.courseid = {$course->id}";
                        if ($gradesrecords = get_records_sql($sql)) {
                            foreach ($gradesrecords as $grade) {
                                $grades[$grade->itemid] = new grade_grade($grade, false);
                            }
                            unset($gradesrecords);
                        }
                        foreach ($items as $itemid=>$unused) {
                            if (!isset($grades[$itemid])) {
                                $grade_grade = new grade_grade();
                                $grade_grade->userid = $this->user->id;
                                $grade_grade->itemid = $items[$itemid]->id;
                                $grades[$itemid] = $grade_grade;
                            }
                            $grades[$itemid]->grade_item =& $items[$itemid];
                        }
                        $hiding_affected = grade_grade::get_hiding_affected($grades, $items);
                        if (array_key_exists($course_item->id, $hiding_affected['altered'])) {
                            $finalgrade = $hiding_affected['altered'][$course_item->id];

                        } else if (!empty($hiding_affected['unknown'][$course_item->id])) {
                            $finalgrade = null;
                        }

                        unset($hiding_affected);
                        unset($grades);
                        unset($items);
                    }
                }

                $data = array($courselink, grade_format_gradevalue($finalgrade, $course_item, true));

                if (!$this->showrank) {
                    //nothing to do

                } else if (!is_null($finalgrade)) {
                    /// find the number of users with a higher grade
                    /// please note this can not work if hidden grades involved :-( to be fixed in 2.0
                    $sql = "SELECT COUNT(DISTINCT(userid))
                              FROM {$CFG->prefix}grade_grades
                             WHERE finalgrade IS NOT NULL AND finalgrade > $finalgrade
                                   AND itemid = {$course_item->id}";
                    $rank = count_records_sql($sql) + 1;

                    $data[] = "$rank/$numusers";

                } else {
                    // no grade, no rank
                    $data[] = '-';
                }

                $this->table->add_data($data);
            }
            return true;

        } else {
            notify(get_string('nocourses', 'grades'));
            return false;
        }
    }

    /**
     * Prints or returns the HTML from the flexitable.
     * @param bool $return Whether or not to return the data instead of printing it directly.
     * @return string
     */
    function print_table($return=false) {
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
}

function grade_report_overview_settings_definition(&$mform) {
    global $CFG;

    $options = array(-1 => get_string('default', 'grades'),
                      0 => get_string('hide'),
                      1 => get_string('show'));

    if (empty($CFG->grade_overviewreport_showrank)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_overview_showrank', get_string('showrank', 'grades'), $options);
    $mform->setHelpButton('report_overview_showrank', array('showrank', get_string('showrank', 'grades'), 'grade'));
}

?>
