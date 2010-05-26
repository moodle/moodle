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
 * File in which the grade_report_grader class is defined.
 * @package gradebook
 */

require_once($CFG->dirroot . '/grade/report/grader/lib.php');

/**
 * Class providing an API for the grader report building and displaying.
 * @uses grade_report
 * @package gradebook
 * @todo MDL-21562 Look at this class + its methods and try to work out what is still required
 */
class grade_report_grader_ajax extends grade_report_grader {

    /**
     * An array of feedbacks, indexed by userid_itemid, used for JS caching
     * @var array $feedbacks
     */
    var $feedbacks = array();

    /**
     * Length at which feedback will be truncated (to the nearest word) and an ellipsis be added.
     * TODO replace this by a report preference
     * @var int $feedback_trunc_length
     */
    var $feedback_trunc_length = 50;

    /**
     * Self-incrementing variable, tracking the tabindex. Depending on the tabindex option ("all values, then feedbacks" is default)
     * Increments by one between each user for the gradevalues, and by 1 + usercount for the gradefeedback
     * @var int $tabindex
     */
    //var $tabindex = 0;

    /**
     * Loads, stores and returns the array of scales used in this course.
     * @return array
     */
    /*function get_scales_array() {
        global $DB;

        if (empty($this->gtree->items)) {
            return false;
        }

        if (!empty($this->scales_array)) {
            return $this->scales_array;
        }

        $scales_list = array();
        $scales_array = array();

        foreach ($this->gtree->items as $item) {
            if (!empty($item->scaleid)) {
                $scales_list[] = $item->scaleid;
            }
        }

        if (!empty($scales_list)) {
            $scales_array = $DB->get_records_list('scale', 'id', $scales_list);
            $this->scales_array = $scales_array;
            return $scales_array;
        } else {
            return null;
        }
    }*/

    /**
     * Builds and return the HTML rows of the table (grades headed by student).
     * @todo MDL-21562 Is this still used anywhere
     * @return string HTML
     */
    /*function get_studentshtml() {
        if (empty($this->users)) {
            print_error('nousersloaded', 'grades');
        }

        $this->numusers = count($this->users);

        $studentshtml = '';

        foreach ($this->users as $userid => $user) {
            $this->tabindex++;
            $studentshtml .= $this->get_studentrowhtml($user);
        }

        return $studentshtml;
    }*/


    /**
     * Given a userid, and provided the gtree is correctly loaded, returns a complete HTML row for this user.
     *
     * @todo MDL-21562 Apparently not used anywhere please check
     * @todo MDL-21562 Calls to JavaScript function `set_row` will no longer work
     *          and need to be replaced
     * @param object $user
     * @return string
     */
    /*function get_studentrowhtml($user) {
        global $CFG, $USER, $OUTPUT;
        $showuserimage = $this->get_pref('showuserimage');
        $showuseridnumber = $this->get_pref('showuseridnumber');
        $fixedstudents = empty($USER->screenreader) && $this->get_pref('fixedstudents');
        $studentrowhtml = '';
        $row_classes = array(' even ', ' odd ');

        if ($this->canviewhidden) {
            $altered = array();
            $unknown = array();
        } else {
            $hiding_affected = grade_grade::get_hiding_affected($this->grades[$userid], $this->gtree->items);
            $altered = $hiding_affected['altered'];
            $unknown = $hiding_affected['unknown'];
            unset($hiding_affected);
        }

        $columncount = 0;

        if ($fixedstudents) {
            $studentrowhtml .= '<tr class="r'.$this->rowcount++ . $row_classes[$this->rowcount % 2] . '">';
        } else {
            // Student name and link
            $user_pic = null;
            if ($showuserimage) {
                $user_pic = '<div class="userpic">' . $OUTPUT->user_picture($user) . '</div>';
            }

            $studentrowhtml .= '<tr class="r'.$this->rowcount++ . $row_classes[$this->rowcount % 2] . '">'
                          .'<th class="header c'.$columncount++.' user" scope="row" onclick="set_row(this.parentNode.rowIndex);">'.$user_pic
                          .'<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.$this->course->id.'">'
                          .fullname($user).'</a></th>';

            if ($showuseridnumber) {
                $studentrowhtml .= '<th class="header c'.$columncount++.' useridnumber" onclick="set_row(this.parentNode.rowIndex);">'. $user->idnumber.'</th>';
            }
        }

        $columntabcount = 0;
        $feedback_tabindex_modifier = 1; // Used to offset the grade value at the beginning of each new column

        if ($this->get_pref('showquickfeedback')) {
            $feedback_tabindex_modifier = 2;
        }

        foreach ($this->gtree->items as $itemid=>$unused) {

            $nexttabindex = $this->tabindex + $columntabcount * $feedback_tabindex_modifier * $this->numusers;
            $studentrowhtml .= $this->get_gradecellhtml($user, $itemid, $columncount, $nexttabindex, $altered, $unknown);
            $columntabcount++;
        }

        $studentrowhtml .= '</tr>';
        return $studentrowhtml;

    }*/

    /**
     * Retuns the HTML table cell for a user's grade for a grade_item
     *
     * @param object $user
     * @param int    $itemid
     * @param int    $columncount
     * @param int    $nexttabindex
     * @param array  $altered
     * @param array  $unknown
     *
     * @return string
     */
    /*function get_gradecellhtml($user, $itemid, $columncount, $nexttabindex, $altered=array(), $unknown=array()) {
        global $CFG, $USER;

        $strfeedback  = $this->get_lang_string("feedback");
        $strgrade     = $this->get_lang_string('grade');

        // Preload scale objects for items with a scaleid
        $scales_array = $this->get_scales_array();

        $userid = $user->id;
        $item =& $this->gtree->items[$itemid];
        $grade = $this->grades[$userid][$item->id];

        // Get the decimal points preference for this item
        $decimalpoints = $item->get_decimals();

        if (in_array($itemid, $unknown)) {
            $gradeval = null;
        } else if (array_key_exists($itemid, $altered)) {
            $gradeval = $altered[$itemid];
        } else {
            $gradeval = $grade->finalgrade;
        }

        $gradecellhtml = '';

        // MDL-11274
        // Hide grades in the grader report if the current grader doesn't have 'moodle/grade:viewhidden'
        if (!$this->canviewhidden and $grade->is_hidden()) {
            if (!empty($CFG->grade_hiddenasdate) and $grade->get_datesubmitted() and !$item->is_category_item() and !$item->is_course_item()) {
                // the problem here is that we do not have the time when grade value was modified, 'timemodified' is general modification date for grade_grades records
                $gradecellhtml .= '<td class="cell c'.$columncount++.'"><span class="datesubmitted">'.userdate($grade->get_datesubmitted(),get_string('strftimedatetimeshort')).'</span></td>';
            } else {
                $gradecellhtml .= '<td class="cell c'.$columncount++.'">-</td>';
            }
            continue;
        }

        // emulate grade element
        $eid = $this->gtree->get_grade_eid($grade);
        $element = array('eid'=>$eid, 'object'=>$grade, 'type'=>'grade');

        $cellclasses = 'grade ajax cell c'.$columncount++;
        if ($item->is_category_item()) {
            $cellclasses .= ' cat';
        }
        if ($item->is_course_item()) {
            $cellclasses .= ' course';
        }
        if ($grade->is_overridden()) {
            $cellclasses .= ' overridden';
        }

        if ($grade->is_excluded()) {
            $cellclasses .= ' excluded';
        }

        $grade_title = '&lt;div class=&quot;fullname&quot;&gt;'.fullname($user).'&lt;/div&gt;';
        $grade_title .= '&lt;div class=&quot;itemname&quot;&gt;'.$item->get_name(true).'&lt;/div&gt;';

        if (!empty($grade->feedback) && !$USER->gradeediting[$this->courseid]) {
            $grade_title .= '&lt;div class=&quot;feedback&quot;&gt;'
                         .wordwrap(trim(format_string($grade->feedback, $grade->feedbackformat)), 34, '&lt;br/ &gt;') . '&lt;/div&gt;';
        }

        $gradecellhtml .= "<td id=\"gradecell_u$userid-i$itemid\" class=\"$cellclasses\" title=\"$grade_title\">";

        if ($grade->is_excluded()) {
            $gradecellhtml .= get_string('excluded', 'grades') . ' ';
        }

        // Do not show any icons if no grade (no record in DB to match)
        if (!$item->needsupdate and $USER->gradeediting[$this->courseid]) {
            $gradecellhtml .= $this->get_icons($element);
            // Add a class to the icon so that it floats left
            $gradecellhtml = str_replace('class="iconsmall"', 'class="iconsmall ajax"', $gradecellhtml);
        }

        $hidden = '';
        if ($grade->is_hidden()) {
            $hidden = ' hidden ';
        }

        $gradepass = ' gradefail ';
        if ($grade->is_passed($item)) {
            $gradepass = ' gradepass ';
        } elseif (is_null($grade->is_passed($item))) {
            $gradepass = '';
        }

        // if in editting mode, we need to print either a text box
        // or a drop down (for scales)
        // grades in item of type grade category or course are not directly editable
        if ($item->needsupdate) {
            $gradecellhtml .= '<span class="gradingerror'.$hidden.'">'.get_string('error').'</span>';

        } else if ($USER->gradeediting[$this->courseid]) {
            $anchor_id = "gradevalue_$userid-i$itemid";

            if ($item->scaleid && !empty($scales_array[$item->scaleid])) {
                $scale = $scales_array[$item->scaleid];
                $gradeval = (int)$gradeval; // scales use only integers
                $scales = explode(",", $scale->scale);
                // reindex because scale is off 1

                // MDL-12104 some previous scales might have taken up part of the array
                // so this needs to be reset
                $scaleopt = array();
                $i = 0;
                foreach ($scales as $scaleoption) {
                    $i++;
                    $scaleopt[$i] = $scaleoption;
                }

                if ($this->get_pref('quickgrading') and $grade->is_editable()) {
                    $oldval = empty($gradeval) ? -1 : $gradeval;
                    if (empty($item->outcomeid)) {
                        $nogradestr = $this->get_lang_string('nograde');
                    } else {
                        $nogradestr = $this->get_lang_string('nooutcome', 'grades');
                    }

                    $gradecellhtml .= '<select name="grade_'.$userid.'_'.$item->id.'" class="gradescale editable" '
                                    . 'id="gradescale_'.$userid.'-i'.$item->id.'" tabindex="'.$nexttabindex.'">' . "\n";
                    $gradecellhtml .= '<option value="-1">' . $nogradestr . "</option>\n";

                    foreach ($scaleopt as $val => $label) {
                        $selected = '';

                        if ($val == $oldval) {
                            $selected = 'selected="selected"';
                        }

                        $gradecellhtml .= "<option value=\"$val\" $selected>$label</option>\n";
                    }

                    $gradecellhtml .= "</select>\n";

                } elseif(!empty($scale)) {
                    $scales = explode(",", $scale->scale);

                    // invalid grade if gradeval < 1
                    if ($gradeval < 1) {
                        $gradecellhtml .= '<a tabindex="'.$nexttabindex .'" id="' . $anchor_id
                                       . '"  class="gradevalue'.$hidden.$gradepass.'">-</a>';
                    } else {
                        //just in case somebody changes scale
                        $gradeval = (int)bounded_number($grade->grade_item->grademin, $gradeval, $grade->grade_item->grademax);
                        $gradecellhtml .= '<a tabindex="'.$nexttabindex .'" id="' . $anchor_id
                                       . '"  class="gradevalue'.$hidden.$gradepass.'">'.$scales[$gradeval-1].'</a>';
                    }
                } else {
                    // no such scale, throw error?
                }

            } else if ($item->gradetype != GRADE_TYPE_TEXT) { // Value type
                $value = $gradeval;
                if ($this->get_pref('quickgrading') and $grade->is_editable()) {
                    $gradecellhtml .= '<a tabindex="'.$nexttabindex .'" id="' . $anchor_id
                                   . '"  class="gradevalue'.$hidden.$gradepass.' editable">' .$value.'</a>';
                } else {
                    $gradecellhtml .= '<a tabindex="'.$nexttabindex .'" id="' . $anchor_id . '"  class="gradevalue'
                                   .$hidden.$gradepass.'">'.$value.'</a>';
                }
            }


            // If quickfeedback is on, print an input element
            if ($this->get_pref('showquickfeedback') and $grade->is_editable()) {
                if ($this->get_pref('quickgrading')) {
                    $gradecellhtml .= '<br />';
                }
                $feedback = s($grade->feedback);
                $anchor_id = "gradefeedback_$userid-i$itemid";

                $gradecellhtml .= '<a ';
                if (empty($feedback)) {
                    $feedback = get_string('addfeedback', 'grades');
                }

                $feedback_tabindex = $nexttabindex + $this->numusers;

                $short_feedback = shorten_text($feedback, $this->feedback_trunc_length);
                $gradecellhtml .= ' tabindex="'.$feedback_tabindex .'" id="'
                               . $anchor_id . '"  class="gradefeedback editable">' . $short_feedback . '</a>';
                $this->feedbacks[$userid][$item->id] = $feedback;
            }

        } else { // Not editing
            $gradedisplaytype = $item->get_displaytype();

            if ($item->needsupdate) {
                $gradecellhtml .= '<span class="gradingerror'.$hidden.$gradepass.'">'.get_string('error').'</span>';

            } else {
                $gradecellhtml .= '<span class="gradevalue'.$hidden.$gradepass.'">'.grade_format_gradevalue($gradeval, $item, true, $gradedisplaytype, null).'</span>';
            }

            // Close feedback span
            if (!empty($grade->feedback)) {
                $gradecellhtml .= '</span>';
            }
        }

        if (!empty($this->gradeserror[$item->id][$userid])) {
            $gradecellhtml .= $this->gradeserror[$item->id][$userid];
        }

        $gradecellhtml .=  '</td>' . "\n";
        return $gradecellhtml;
    }*/

    /**
     * Returns a valid JSON object with feedbacks indexed by userid and itemid.
     * Paging is taken into account: this needs to be reloaded at each new page (not page load, just page of data);
     */
    /*function getFeedbackJsArray() {
        if (!empty($this->feedbacks)) {
            return json_encode($this->feedbacks);
        } else {
            return null;
        }
    }*/

    /**
     * Returns a json_encoded hash of itemid => decimalpoints preferences
     */
    /*function getItemsDecimalPoints() {
        $decimals = array();
        foreach ($this->gtree->items as $itemid=>$item) {
            $decimals[$itemid] = $item->get_decimals();
        }
        return json_encode($decimals);
    }*/
}

