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
 * File in which the user_report class is defined.
 * @package gradebook
 */

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

/**
 * Class providing an API for the user report building and displaying.
 * @uses grade_report
 * @package gradebook
 */
class grade_report_user extends grade_report {

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
     * Flat structure similar to grade tree
     */
    var $gseq;

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $courseid
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     * @param int $userid The id of the user
     */
    function grade_report_user($courseid, $gpr, $context, $userid) {
        global $CFG;
        parent::grade_report($courseid, $gpr, $context);

        $switch = grade_get_setting($this->courseid, 'aggregationposition', $CFG->grade_aggregationposition);

        // Grab the grade_tree for this course
        $this->gseq = new grade_seq($this->courseid, $switch);

        // get the user (for full name)
        $this->user = get_record('user', 'id', $userid);

        // base url for sorting by first/last name
        $this->baseurl = $CFG->wwwroot.'/grade/report?id='.$courseid.'&amp;userid='.$userid;
        $this->pbarurl = $this->baseurl;

        // always setup groups - no user preference here
        $this->setup_groups();

        $this->setup_table();
    }

    /**
     * Prepares the headers and attributes of the flexitable.
     */
    function setup_table() {
        /*
        * Table has 6 columns
        *| pic  | itemname/description | grade (grade_final) | percentage | rank | feedback |
        */

        // setting up table headers
        $tablecolumns = array('itemname', 'category', 'grade', 'percentage', 'rank', 'feedback');
        $tableheaders = array($this->get_lang_string('gradeitem', 'grades'), $this->get_lang_string('category'), $this->get_lang_string('grade'),
            $this->get_lang_string('percent', 'grades'), $this->get_lang_string('rank', 'grades'),
            $this->get_lang_string('feedback'));

        $this->table = new flexible_table('grade-report-user-'.$this->courseid);

        $this->table->define_columns($tablecolumns);
        $this->table->define_headers($tableheaders);
        $this->table->define_baseurl($this->baseurl);

        $this->table->set_attribute('cellspacing', '0');
        $this->table->set_attribute('id', 'user-grade');
        $this->table->set_attribute('class', 'boxaligncenter generaltable');

        // not sure tables should be sortable or not, because if we allow it then sorted resutls distort grade category structure and sortorder
        $this->table->set_control_variables(array(
                TABLE_VAR_SORT    => 'ssort',
                TABLE_VAR_HIDE    => 'shide',
                TABLE_VAR_SHOW    => 'sshow',
                TABLE_VAR_IFIRST  => 'sifirst',
                TABLE_VAR_ILAST   => 'silast',
                TABLE_VAR_PAGE    => 'spage'
                ));

        $this->table->setup();
    }

    function fill_table() {
        global $CFG;
        $numusers = $this->get_numusers(false); // total course users
        $items =& $this->gseq->items;
        $grades = array();

        $viewhidden = has_capability('moodle/grade:viewhidden', get_context_instance(CONTEXT_COURSE, $this->courseid));

        foreach ($items as $key=>$unused) {
            $grade_item =& $items[$key];
            $grade_grade = new grade_grade(array('itemid'=>$grade_item->id, 'userid'=>$this->user->id));
            $grades[$key] = $grade_grade;
            $grades[$key]->grade_item =& $grade_item;
        }

        $hiding_affected = grade_grade::get_hiding_affected($grades, $items);

        foreach ($items as $key=>$unused) {
            $grade_item  =& $items[$key];
            $grade_grade =& $grades[$key];

            $data = array();

            // TODO: indicate items that "needsupdate" - missing final calculation

            /// prints grade item name
            if ($grade_item->is_course_item() or $grade_item->is_category_item()) {
                $data[] = '<div class="catname">'.$grade_item->get_name().'</div>';
            } else {
                $data[] = '<div class="itemname">'.$this->get_module_link($grade_item->get_name(), $grade_item->itemmodule, $grade_item->iteminstance).'</div>';
            }

            /// prints category
            $cat = $grade_item->get_parent_category();
            $data[] = $cat->get_name();

            /// prints the grade
            if ($grade_grade->is_excluded()) {
                $excluded = get_string('excluded', 'grades').' ';
            } else {
                $excluded = '';
            }

            if (is_null($grade_grade->finalgrade)) {
                $data[] = $excluded . '-';

            } else if (($grade_grade->is_hidden() or in_array($grade_item->id, $hiding_affected)) and !$viewhidden) {
                // TODO: optinally do not show anything for hidden grades
                // $data[] = '-';
                if ($grade_grade->is_hidden()) {
                    $data[] = $excluded . '<div class="gradeddate">'.get_string('gradedon', 'grades', userdate($grade_grade->timemodified, get_string('strftimedatetimeshort'))).'</div>';
                } else {
                    $data[] = $excluded . '-';
                }

            } else {
                $data[] = $excluded . grade_format_gradevalue($grade_grade->finalgrade, $grade_item, true);
            }

            /// prints percentage

            if (is_null($grade_grade->finalgrade)) {
                $data[] = '-';

            } else if (($grade_grade->is_hidden() or in_array($grade_item->id, $hiding_affected)) and !$viewhidden) {
                $data[] = '-';

            } else {
                $data[] = grade_format_gradevalue($grade_grade->finalgrade, $grade_item, true, GRADE_DISPLAY_TYPE_PERCENTAGE);
            }

            /// prints rank
            if (is_null($grade_grade->finalgrade)) {
                // no grade, no rank
                $data[] = '-';

            } else if (($grade_grade->is_hidden() or in_array($grade_item->id, $hiding_affected)) and !$viewhidden) {
                $data[] = '-';

            } else {
                /// find the number of users with a higher grade
                $sql = "SELECT COUNT(DISTINCT(userid))
                          FROM {$CFG->prefix}grade_grades
                         WHERE finalgrade > {$grade_grade->finalgrade}
                               AND itemid = {$grade_item->id}";
                $rank = count_records_sql($sql) + 1;

                $data[] = "$rank/$numusers";
            }

            /// prints notes
            if (empty($grade_grade->feedback)) {
                $data[] = '&nbsp;';

            } else if (($grade_grade->is_hidden() or in_array($grade_item->id, $hiding_affected)) and !$viewhidden) {
                $data[] = '&nbsp;';

            } else {
                $data[] = format_text($grade_grade->feedback, $grade_grade->feedbackformat);
            }

            $this->table->add_data($data);
        }

        return true;
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
?>
