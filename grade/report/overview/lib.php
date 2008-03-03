<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////
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
        if ($courses = get_my_courses($this->user->id, 'c.sortorder ASC', 'id, shortname')) {
            $numusers = $this->get_numusers(false);

            foreach ($courses as $course) {
                $courselink = '<a href="'.$CFG->wwwroot.'/grade/report/user/index.php?id='.$course->id.'">'.$course->shortname.'</a>';

                // Get course grade_item
                $grade_item = grade_item::fetch_course_item($course->id);

                // Get the grade
                $grade = new grade_grade(array('itemid'=>$grade_item->id, 'userid'=>$this->user->id));
                $grade->grade_item =& $grade_item;
                $finalgrade = $grade->finalgrade;

                // TODO: this DOES NOT work properly if there are any hidden grades,
                //       rank might be wrong & totals might be different from user report!!!
                if ($grade->is_hidden() and !has_capability('moodle/grade:viewhidden', get_context_instance(CONTEXT_COURSE, $course->id))) {
                    $finalgrade = null;
                }

                $data = array($courselink, grade_format_gradevalue($finalgrade, $grade_item, true));

                if (!$this->showrank) {
                    //nothing to do

                } else if (!is_null($finalgrade)) {
                    /// find the number of users with a higher grade
                    $sql = "SELECT COUNT(DISTINCT(userid))
                              FROM {$CFG->prefix}grade_grades
                             WHERE finalgrade IS NOT NULL AND finalgrade > $finalgrade
                                   AND itemid = {$grade_item->id}";
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
