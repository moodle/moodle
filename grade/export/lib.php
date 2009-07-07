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


require_once($CFG->dirroot.'/lib/gradelib.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/export/grade_export_form.php');

/**
 * Base export class
 */
class grade_export {

    var $plugin; // plgin name - must be filled in subclasses!

    var $grade_items; // list of all course grade items
    var $groupid;     // groupid, 0 means all groups
    var $course;      // course object
    var $columns;     // array of grade_items selected for export

    var $previewrows;     // number of rows in preview
    var $export_letters;  // export letters
    var $export_feedback; // export feedback
    var $userkey;         // export using private user key

    var $updatedgradesonly; // only export updated grades
    var $displaytype; // display type (e.g. real, percentages, letter) for exports
    var $decimalpoints; // number of decimal points for exports
    /**
     * Constructor should set up all the private variables ready to be pulled
     * @param object $course
     * @param int $groupid id of selected group, 0 means all
     * @param string $itemlist comma separated list of item ids, empty means all
     * @param boolean $export_feedback
     * @param boolean $export_letters
     * @note Exporting as letters will lead to data loss if that exported set it re-imported.
     */
    function grade_export($course, $groupid=0, $itemlist='', $export_feedback=false, $updatedgradesonly = false, $displaytype = GRADE_DISPLAY_TYPE_REAL, $decimalpoints = 2) {
        $this->course = $course;
        $this->groupid = $groupid;
        $this->grade_items = grade_item::fetch_all(array('courseid'=>$this->course->id));

        $this->columns = array();
        if (!empty($itemlist)) {
            $itemids = explode(',', $itemlist);
            // remove items that are not requested
            foreach ($itemids as $itemid) {
                if (array_key_exists($itemid, $this->grade_items)) {
                    $this->columns[$itemid] =& $this->grade_items[$itemid];
                }
            }
        } else {
            foreach ($this->grade_items as $itemid=>$unused) {
                $this->columns[$itemid] =& $this->grade_items[$itemid];
            }
        }

        $this->export_feedback = $export_feedback;
        $this->userkey         = '';
        $this->previewrows     = false;
        $this->updatedgradesonly = $updatedgradesonly;
        
        $this->displaytype = $displaytype;
        $this->decimalpoints = $decimalpoints;
    }

    /**
     * Init object based using data from form
     * @param object $formdata
     */
    function process_form($formdata) {
        global $USER;

        $this->columns = array();
        if (!empty($formdata->itemids)) {
            foreach ($formdata->itemids as $itemid=>$selected) {
                if ($selected and array_key_exists($itemid, $this->grade_items)) {
                    $this->columns[$itemid] =& $this->grade_items[$itemid];
                }
            }
        } else {
            foreach ($this->grade_items as $itemid=>$unused) {
                $this->columns[$itemid] =& $this->grade_items[$itemid];
            }
        }

        if (isset($formdata->key)) {
            if ($formdata->key == 1 && isset($formdata->iprestriction) && isset($formdata->validuntil)) {
                // Create a new key
                $formdata->key = create_user_key('grade/export', $USER->id, $this->course->id, $formdata->iprestriction, $formdata->validuntil);
            }
            $this->userkey = $formdata->key;
        }

        if (isset($formdata->export_letters)) {
            $this->export_letters = $formdata->export_letters;
        }

        if (isset($formdata->export_feedback)) {
            $this->export_feedback = $formdata->export_feedback;
        }

        if (isset($formdata->previewrows)) {
            $this->previewrows = $formdata->previewrows;
        }

    }

    /**
     * Update exported field in grade_grades table
     * @return boolean
     */
    function track_exports() {
        global $CFG;

        /// Whether this plugin is entitled to update export time
        if ($expplugins = explode(",", $CFG->gradeexport)) {
            if (in_array($this->plugin, $expplugins)) {
                return true;
            } else {
                return false;
          }
        } else {
            return false;
        }
    }

    /**
     * Returns string representation of final grade
     * @param $object $grade instance of grade_grade class
     * @return string
     */
    function format_grade($grade) {
        return grade_format_gradevalue($grade->finalgrade, $this->grade_items[$grade->itemid], false, $this->displaytype, $this->decimalpoints);
    }

    /**
     * Returns the name of column in export
     * @param object $grade_item
     * @param boolena $feedback feedback colum
     * &return string
     */
    function format_column_name($grade_item, $feedback=false) {
        if ($grade_item->itemtype == 'mod') {
            $name = get_string('modulename', $grade_item->itemmodule).': '.$grade_item->get_name();
        } else {
            $name = $grade_item->get_name();
        }

        if ($feedback) {
            $name .= ' ('.get_string('feedback').')';
        }

        return strip_tags($name);
    }

    /**
     * Returns formatted grade feedback
     * @param object $feedback object with properties feedback and feedbackformat
     * @return string
     */
    function format_feedback($feedback) {
        return strip_tags(format_text($feedback->feedback, $feedback->feedbackformat));
    }

    /**
     * Implemented by child class
     */
    function print_grades() { }

    /**
     * Prints preview of exported grades on screen as a feedback mechanism
     * @param bool $require_user_idnumber true means skip users without idnumber
     */
    function display_preview($require_user_idnumber=false) {

        print_heading(get_string('previewrows', 'grades'));

        echo '<table>';
        echo '<tr>';
        echo '<th>'.get_string("firstname")."</th>".
             '<th>'.get_string("lastname")."</th>".
             '<th>'.get_string("idnumber")."</th>".
             '<th>'.get_string("institution")."</th>".
             '<th>'.get_string("department")."</th>".
             '<th>'.get_string("email")."</th>";
        foreach ($this->columns as $grade_item) {
            echo '<th>'.$this->format_column_name($grade_item).'</th>';

            /// add a column_feedback column
            if ($this->export_feedback) {
                echo '<th>'.$this->format_column_name($grade_item, true).'</th>';
            }
        }
        echo '</tr>';
        /// Print all the lines of data.

        $i = 0;
        $gui = new graded_users_iterator($this->course, $this->columns, $this->groupid);
        $gui->init();
        while ($userdata = $gui->next_user()) {
            // number of preview rows
            if ($this->previewrows and $this->previewrows <= $i) {
                break;
            }
            $user = $userdata->user;
            if ($require_user_idnumber and empty($user->idnumber)) {
                // some exports require user idnumber
                continue;
            }

            $gradeupdated = false; // if no grade is update at all for this user, do not display this row
            $rowstr = '';
            foreach ($this->columns as $itemid=>$unused) {
                $gradetxt = $this->format_grade($userdata->grades[$itemid]);
                
                // get the status of this grade, and put it through track to get the status
                $g = new grade_export_update_buffer();
                $grade_grade = new grade_grade(array('itemid'=>$itemid, 'userid'=>$user->id));
                $status = $g->track($grade_grade);

                if ($this->updatedgradesonly && ($status == 'nochange' || $status == 'unknown')) {
                    $rowstr .= '<td>'.get_string('unchangedgrade', 'grades').'</td>';
                } else {
                    $rowstr .= "<td>$gradetxt</td>";
                    $gradeupdated = true;
                }
                
                if ($this->export_feedback) {
                    $rowstr .=  '<td>'.$this->format_feedback($userdata->feedbacks[$itemid]).'</td>';
                }
            }

            // if we are requesting updated grades only, we are not interested in this user at all            
            if (!$gradeupdated && $this->updatedgradesonly) {
                continue; 
            }

            echo '<tr>';
            echo "<td>$user->firstname</td><td>$user->lastname</td><td>$user->idnumber</td><td>$user->institution</td><td>$user->department</td><td>$user->email</td>";           
            echo $rowstr;
            echo "</tr>";
            
            $i++; // increment the counter
        }
        echo '</table>';
        $gui->close();
    }

    /**
     * Returns array of parameters used by dump.php and export.php.
     * @return array
     */
    function get_export_params() {
        $itemids = array_keys($this->columns);

        $params = array('id'                =>$this->course->id,
                        'groupid'           =>$this->groupid,
                        'itemids'           =>implode(',', $itemids),
                        'export_letters'    =>$this->export_letters,
                        'export_feedback'   =>$this->export_feedback,
                        'updatedgradesonly' =>$this->updatedgradesonly,
                        'displaytype'       =>$this->displaytype,
                        'decimalpoints'     =>$this->decimalpoints);

        return $params;
    }

    /**
     * Either prints a "Export" box, which will redirect the user to the download page,
     * or prints the URL for the published data.
     * @return void
     */
    function print_continue() {
        global $CFG;

        $params = $this->get_export_params();


        print_heading(get_string('export', 'grades'));

        echo '<div class="gradeexportlink">';
        if (!$this->userkey) {      // this button should trigger a download prompt
            print_single_button($CFG->wwwroot.'/grade/export/'.$this->plugin.'/export.php',
                                $params, get_string('download', 'admin'));

        } else {
            $paramstr = '';
            $sep = '?';
            foreach($params as $name=>$value) {
                $paramstr .= $sep.$name.'='.$value;
                $sep = '&amp;';
            }

            $link = $CFG->wwwroot.'/grade/export/'.$this->plugin.'/dump.php'.$paramstr.'&amp;key='.$this->userkey;

            echo get_string('download', 'admin').': <a href="'.$link.'">'.$link.'</a>';
        }
        echo '</div>';
    }
}

/**
 * This class is used to update the exported field in grade_grades.
 * It does internal buffering to speedup the db operations.
 */
class grade_export_update_buffer {
    var $update_list;
    var $export_time;

    /**
     * Constructor - creates the buffer and initialises the time stamp
     */
    function grade_export_update_buffer() {
        $this->update_list = array();
        $this->export_time = time();
    }

    function flush($buffersize) {
        global $CFG;

        if (count($this->update_list) > $buffersize) {
            $list = implode(',', $this->update_list);
            $sql = "UPDATE {$CFG->prefix}grade_grades SET exported = {$this->export_time} WHERE id IN ($list)";
            execute_sql($sql, false);
            $this->update_list = array();
        }
    }

    /**
     * Track grade export status
     * @param object $grade_grade
     * @return string $status (unknow, new, regrade, nochange)
     */
    function track($grade_grade) {

        if (empty($grade_grade->exported) or empty($grade_grade->timemodified)) {
            if (is_null($grade_grade->finalgrade)) {
                // grade does not exist yet
                $status = 'unknown';
            } else {
                $status = 'new';
                $this->update_list[] = $grade_grade->id;
            }

        } else if ($grade_grade->exported < $grade_grade->timemodified) {
            $status = 'regrade';
            $this->update_list[] = $grade_grade->id;

        } else if ($grade_grade->exported >= $grade_grade->timemodified) {
            $status = 'nochange';

        } else {
            // something is wrong?
            $status = 'unknown';
        }

        $this->flush(100);

        return $status;
    }

    /**
     * Flush and close the buffer.
     */
    function close() {
        $this->flush(0);
    }
}
?>
