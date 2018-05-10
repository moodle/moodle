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
abstract class grade_export {

    public $plugin; // plgin name - must be filled in subclasses!

    public $grade_items; // list of all course grade items
    public $groupid;     // groupid, 0 means all groups
    public $course;      // course object
    public $columns;     // array of grade_items selected for export

    public $export_letters;  // export letters
    public $export_feedback; // export feedback
    public $userkey;         // export using private user key

    public $updatedgradesonly; // only export updated grades

    /**
     *  Grade display type (real, percentages or letter).
     *
     *  This attribute is an integer for XML file export. Otherwise is an array for all other formats (ODS, XLS and TXT).
     *
     *  @var $displaytype Grade display type constant (1, 2 or 3) or an array of display types where the key is the name
     *                    and the value is the grade display type constant or 0 for unchecked display types.
     * @access public.
     */
    public $displaytype;
    public $decimalpoints; // number of decimal points for exports
    public $onlyactive; // only include users with an active enrolment
    public $usercustomfields; // include users custom fields

    /**
     * @deprecated since Moodle 2.8
     * @var $previewrows Number of rows in preview.
     */
    public $previewrows;

    /**
     * Constructor should set up all the private variables ready to be pulled.
     *
     * This constructor used to accept the individual parameters as separate arguments, in
     * 2.8 this was simplified to just accept the data from the moodle form.
     *
     * @access public
     * @param object $course
     * @param int $groupid
     * @param stdClass|null $formdata
     * @note Exporting as letters will lead to data loss if that exported set it re-imported.
     */
    public function __construct($course, $groupid, $formdata) {
        if (func_num_args() != 3 || ($formdata != null && get_class($formdata) != "stdClass")) {
            $args = func_get_args();
            return call_user_func_array(array($this, "deprecated_constructor"), $args);
        }
        $this->course = $course;
        $this->groupid = $groupid;

        $this->grade_items = grade_item::fetch_all(array('courseid'=>$this->course->id));

        $this->process_form($formdata);
    }

    /**
     * Old deprecated constructor.
     *
     * This deprecated constructor accepts the individual parameters as separate arguments, in
     * 2.8 this was simplified to just accept the data from the moodle form.
     *
     * @deprecated since 2.8 MDL-46548. Instead call the shortened constructor which accepts the data
     * directly from the grade_export_form.
     */
    protected function deprecated_constructor($course,
                                              $groupid=0,
                                              $itemlist='',
                                              $export_feedback=false,
                                              $updatedgradesonly = false,
                                              $displaytype = GRADE_DISPLAY_TYPE_REAL,
                                              $decimalpoints = 2,
                                              $onlyactive = false,
                                              $usercustomfields = false) {

        debugging('Many argument constructor for class "grade_export" is deprecated. Call the 3 argument version instead.', DEBUG_DEVELOPER);

        $this->course = $course;
        $this->groupid = $groupid;

        $this->grade_items = grade_item::fetch_all(array('courseid'=>$this->course->id));
        //Populating the columns here is required by /grade/export/(whatever)/export.php
        //however index.php, when the form is submitted, will construct the collection here
        //with an empty $itemlist then reconstruct it in process_form() using $formdata
        $this->columns = array();
        if (!empty($itemlist)) {
            if ($itemlist=='-1') {
                //user deselected all items
            } else {
                $itemids = explode(',', $itemlist);
                // remove items that are not requested
                foreach ($itemids as $itemid) {
                    if (array_key_exists($itemid, $this->grade_items)) {
                        $this->columns[$itemid] =& $this->grade_items[$itemid];
                    }
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
        $this->onlyactive = $onlyactive;
        $this->usercustomfields = $usercustomfields;
    }

    /**
     * Init object based using data from form
     * @param object $formdata
     */
    function process_form($formdata) {
        global $USER;

        $this->columns = array();
        if (!empty($formdata->itemids)) {
            if ($formdata->itemids=='-1') {
                //user deselected all items
            } else {
                foreach ($formdata->itemids as $itemid=>$selected) {
                    if ($selected and array_key_exists($itemid, $this->grade_items)) {
                        $this->columns[$itemid] =& $this->grade_items[$itemid];
                    }
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

        if (isset($formdata->decimals)) {
            $this->decimalpoints = $formdata->decimals;
        }

        if (isset($formdata->export_letters)) {
            $this->export_letters = $formdata->export_letters;
        }

        if (isset($formdata->export_feedback)) {
            $this->export_feedback = $formdata->export_feedback;
        }

        if (isset($formdata->export_onlyactive)) {
            $this->onlyactive = $formdata->export_onlyactive;
        }

        if (isset($formdata->previewrows)) {
            $this->previewrows = $formdata->previewrows;
        }

        if (isset($formdata->display)) {
            $this->displaytype = $formdata->display;

            // Used by grade exports which accept multiple display types.
            // If the checkbox value is 0 (unchecked) then remove it.
            if (is_array($formdata->display)) {
                $this->displaytype = array_filter($formdata->display);
            }
        }

        if (isset($formdata->updatedgradesonly)) {
            $this->updatedgradesonly = $formdata->updatedgradesonly;
        }
    }

    /**
     * Update exported field in grade_grades table
     * @return boolean
     */
    public function track_exports() {
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
     * @param object $grade instance of grade_grade class
     * @param integer $gradedisplayconst grade display type constant.
     * @return string
     */
    public function format_grade($grade, $gradedisplayconst = null) {
        $displaytype = $this->displaytype;
        if (is_array($this->displaytype) && !is_null($gradedisplayconst)) {
            $displaytype = $gradedisplayconst;
        }

        $gradeitem = $this->grade_items[$grade->itemid];

        // We are going to store the min and max so that we can "reset" the grade_item for later.
        $grademax = $gradeitem->grademax;
        $grademin = $gradeitem->grademin;

        // Updating grade_item with this grade_grades min and max.
        $gradeitem->grademax = $grade->get_grade_max();
        $gradeitem->grademin = $grade->get_grade_min();

        $formattedgrade = grade_format_gradevalue($grade->finalgrade, $gradeitem, false, $displaytype, $this->decimalpoints);

        // Resetting the grade item in case it is reused.
        $gradeitem->grademax = $grademax;
        $gradeitem->grademin = $grademin;

        return $formattedgrade;
    }

    /**
     * Returns the name of column in export
     * @param object $grade_item
     * @param boolean $feedback feedback colum
     * @param string $gradedisplayname grade display name.
     * @return string
     */
    public function format_column_name($grade_item, $feedback=false, $gradedisplayname = null) {
        $column = new stdClass();

        if ($grade_item->itemtype == 'mod') {
            $column->name = get_string('modulename', $grade_item->itemmodule).get_string('labelsep', 'langconfig').$grade_item->get_name();
        } else {
            $column->name = $grade_item->get_name(true);
        }

        // We can't have feedback and display type at the same time.
        $column->extra = ($feedback) ? get_string('feedback') : get_string($gradedisplayname, 'grades');

        return html_to_text(get_string('gradeexportcolumntype', 'grades', $column), 0, false);
    }

    /**
     * Returns formatted grade feedback
     * @param object $feedback object with properties feedback and feedbackformat
     * @return string
     */
    public function format_feedback($feedback) {
        return strip_tags(format_text($feedback->feedback, $feedback->feedbackformat));
    }

    /**
     * Implemented by child class
     */
    public abstract function print_grades();

    /**
     * Prints preview of exported grades on screen as a feedback mechanism
     * @param bool $require_user_idnumber true means skip users without idnumber
     * @deprecated since 2.8 MDL-46548. Previews are not useful on export.
     */
    public function display_preview($require_user_idnumber=false) {
        global $OUTPUT;

        debugging('function grade_export::display_preview is deprecated.', DEBUG_DEVELOPER);

        $userprofilefields = grade_helper::get_user_profile_fields($this->course->id, $this->usercustomfields);
        $formatoptions = new stdClass();
        $formatoptions->para = false;

        echo $OUTPUT->heading(get_string('previewrows', 'grades'));

        echo '<table>';
        echo '<tr>';
        foreach ($userprofilefields as $field) {
            echo '<th>' . $field->fullname . '</th>';
        }
        if (!$this->onlyactive) {
            echo '<th>'.get_string("suspended")."</th>";
        }
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
        $gui->require_active_enrolment($this->onlyactive);
        $gui->allow_user_custom_fields($this->usercustomfields);
        $gui->init();
        while ($userdata = $gui->next_user()) {
            // number of preview rows
            if ($this->previewrows and $this->previewrows <= $i) {
                break;
            }
            $user = $userdata->user;
            if ($require_user_idnumber and empty($user->idnumber)) {
                // some exports require user idnumber so we can match up students when importing the data
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
            foreach ($userprofilefields as $field) {
                $fieldvalue = grade_helper::get_user_field_value($user, $field);
                // @see profile_field_base::display_data().
                echo '<td>' . format_text($fieldvalue, FORMAT_MOODLE, $formatoptions) . '</td>';
            }
            if (!$this->onlyactive) {
                $issuspended = ($user->suspendedenrolment) ? get_string('yes') : '';
                echo "<td>$issuspended</td>";
            }
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
    public function get_export_params() {
        $itemids = array_keys($this->columns);
        $itemidsparam = implode(',', $itemids);
        if (empty($itemidsparam)) {
            $itemidsparam = '-1';
        }

        // We have a single grade display type constant.
        if (!is_array($this->displaytype)) {
            $displaytypes = $this->displaytype;
        } else {
            // Implode the grade display types array as moodle_url function doesn't accept arrays.
            $displaytypes = implode(',', $this->displaytype);
        }

        if (!empty($this->updatedgradesonly)) {
            $updatedgradesonly = $this->updatedgradesonly;
        } else {
            $updatedgradesonly = 0;
        }
        $params = array('id'                => $this->course->id,
                        'groupid'           => $this->groupid,
                        'itemids'           => $itemidsparam,
                        'export_letters'    => $this->export_letters,
                        'export_feedback'   => $this->export_feedback,
                        'updatedgradesonly' => $updatedgradesonly,
                        'decimalpoints'     => $this->decimalpoints,
                        'export_onlyactive' => $this->onlyactive,
                        'usercustomfields'  => $this->usercustomfields,
                        'displaytype'       => $displaytypes,
                        'key'               => $this->userkey);

        return $params;
    }

    /**
     * Either prints a "Export" box, which will redirect the user to the download page,
     * or prints the URL for the published data.
     *
     * @deprecated since 2.8 MDL-46548. Call get_export_url and set the
     *             action of the grade_export_form instead.
     * @return void
     */
    public function print_continue() {
        global $CFG, $OUTPUT;

        debugging('function grade_export::print_continue is deprecated.', DEBUG_DEVELOPER);
        $params = $this->get_export_params();

        echo $OUTPUT->heading(get_string('export', 'grades'));

        echo $OUTPUT->container_start('gradeexportlink');

        if (!$this->userkey) {
            // This button should trigger a download prompt.
            $url = new moodle_url('/grade/export/'.$this->plugin.'/export.php', $params);
            echo $OUTPUT->single_button($url, get_string('download', 'admin'));

        } else {
            $paramstr = '';
            $sep = '?';
            foreach($params as $name=>$value) {
                $paramstr .= $sep.$name.'='.$value;
                $sep = '&';
            }

            $link = $CFG->wwwroot.'/grade/export/'.$this->plugin.'/dump.php'.$paramstr.'&key='.$this->userkey;

            echo get_string('download', 'admin').': ' . html_writer::link($link, $link);
        }
        echo $OUTPUT->container_end();

        return;
    }

    /**
     * Generate the export url.
     *
     * Get submitted form data and create the url to be used on the grade publish feature.
     *
     * @return moodle_url the url of grade publishing export.
     */
    public function get_export_url() {
        return new moodle_url('/grade/export/'.$this->plugin.'/dump.php', $this->get_export_params());
    }

    /**
     * Convert the grade display types parameter into the required array to grade exporting class.
     *
     * In order to export, the array key must be the display type name and the value must be the grade display type
     * constant.
     *
     * Note: Added support for combined display types constants like the (GRADE_DISPLAY_TYPE_PERCENTAGE_REAL) as
     *       the $CFG->grade_export_displaytype config is still used on 2.7 in case of missing displaytype url param.
     *       In these cases, the file will be exported with a column for each display type.
     *
     * @param string $displaytypes can be a single or multiple display type constants comma separated.
     * @return array $types
     */
    public static function convert_flat_displaytypes_to_array($displaytypes) {
        $types = array();

        // We have a single grade display type constant.
        if (is_int($displaytypes)) {
            $displaytype = clean_param($displaytypes, PARAM_INT);

            // Let's set a default value, will be replaced below by the grade display type constant.
            $display[$displaytype] = 1;
        } else {
            // Multiple grade display types constants.
            $display = array_flip(explode(',', $displaytypes));
        }

        // Now, create the array in the required format by grade exporting class.
        foreach ($display as $type => $value) {
            $type = clean_param($type, PARAM_INT);
            if ($type == GRADE_DISPLAY_TYPE_LETTER) {
                $types['letter'] = GRADE_DISPLAY_TYPE_LETTER;
            } else if ($type == GRADE_DISPLAY_TYPE_PERCENTAGE) {
                $types['percentage'] = GRADE_DISPLAY_TYPE_PERCENTAGE;
            } else if ($type == GRADE_DISPLAY_TYPE_REAL) {
                $types['real'] = GRADE_DISPLAY_TYPE_REAL;
            } else if ($type == GRADE_DISPLAY_TYPE_REAL_PERCENTAGE) {
                $types['real'] = GRADE_DISPLAY_TYPE_REAL;
                $types['percentage'] = GRADE_DISPLAY_TYPE_PERCENTAGE;
            } else if ($type == GRADE_DISPLAY_TYPE_REAL_LETTER) {
                $types['real'] = GRADE_DISPLAY_TYPE_REAL;
                $types['letter'] = GRADE_DISPLAY_TYPE_LETTER;
            } else if ($type == GRADE_DISPLAY_TYPE_LETTER_REAL) {
                $types['letter'] = GRADE_DISPLAY_TYPE_LETTER;
                $types['real'] = GRADE_DISPLAY_TYPE_REAL;
            } else if ($type == GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE) {
                $types['letter'] = GRADE_DISPLAY_TYPE_LETTER;
                $types['percentage'] = GRADE_DISPLAY_TYPE_PERCENTAGE;
            } else if ($type == GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER) {
                $types['percentage'] = GRADE_DISPLAY_TYPE_PERCENTAGE;
                $types['letter'] = GRADE_DISPLAY_TYPE_LETTER;
            } else if ($type == GRADE_DISPLAY_TYPE_PERCENTAGE_REAL) {
                $types['percentage'] = GRADE_DISPLAY_TYPE_PERCENTAGE;
                $types['real'] = GRADE_DISPLAY_TYPE_REAL;
            }
        }
        return $types;
    }

    /**
     * Convert the item ids parameter into the required array to grade exporting class.
     *
     * In order to export, the array key must be the grade item id and all values must be one.
     *
     * @param string $itemids can be a single item id or many item ids comma separated.
     * @return array $items correctly formatted array.
     */
    public static function convert_flat_itemids_to_array($itemids) {
        $items = array();

        // We just have one single item id.
        if (is_int($itemids)) {
            $itemid = clean_param($itemids, PARAM_INT);
            $items[$itemid] = 1;
        } else {
            // Few grade items.
            $items = array_flip(explode(',', $itemids));
            foreach ($items as $itemid => $value) {
                $itemid = clean_param($itemid, PARAM_INT);
                $items[$itemid] = 1;
            }
        }
        return $items;
    }

    /**
     * Create the html code of the grade publishing feature.
     *
     * @return string $output html code of the grade publishing.
     */
    public function get_grade_publishing_url() {
        $url = $this->get_export_url();
        $output =  html_writer::start_div();
        $output .= html_writer::tag('p', get_string('gradepublishinglink', 'grades', html_writer::link($url, $url)));
        $output .=  html_writer::end_div();
        return $output;
    }

    /**
     * Create a stdClass object from URL parameters to be used by grade_export class.
     *
     * @param int $id course id.
     * @param string $itemids grade items comma separated.
     * @param bool $exportfeedback export feedback option.
     * @param bool $onlyactive only enrolled active students.
     * @param string $displaytype grade display type constants comma separated.
     * @param int $decimalpoints grade decimal points.
     * @param null $updatedgradesonly recently updated grades only (Used by XML exporting only).
     * @param null $separator separator character: tab, comma, colon and semicolon (Used by TXT exporting only).
     *
     * @return stdClass $formdata
     */
    public static function export_bulk_export_data($id, $itemids, $exportfeedback, $onlyactive, $displaytype,
                                                   $decimalpoints, $updatedgradesonly = null, $separator = null) {

        $formdata = new \stdClass();
        $formdata->id = $id;
        $formdata->itemids = self::convert_flat_itemids_to_array($itemids);
        $formdata->exportfeedback = $exportfeedback;
        $formdata->export_onlyactive = $onlyactive;
        $formdata->display = self::convert_flat_displaytypes_to_array($displaytype);
        $formdata->decimals = $decimalpoints;

        if (!empty($updatedgradesonly)) {
            $formdata->updatedgradesonly = $updatedgradesonly;
        }

        if (!empty($separator)) {
            $formdata->separator = $separator;
        }

        return $formdata;
    }
}

/**
 * This class is used to update the exported field in grade_grades.
 * It does internal buffering to speedup the db operations.
 */
class grade_export_update_buffer {
    public $update_list;
    public $export_time;

    /**
     * Constructor - creates the buffer and initialises the time stamp
     */
    public function __construct() {
        $this->update_list = array();
        $this->export_time = time();
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function grade_export_update_buffer() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    public function flush($buffersize) {
        global $CFG, $DB;

        if (count($this->update_list) > $buffersize) {
            list($usql, $params) = $DB->get_in_or_equal($this->update_list);
            $params = array_merge(array($this->export_time), $params);

            $sql = "UPDATE {grade_grades} SET exported = ? WHERE id $usql";
            $DB->execute($sql, $params);
            $this->update_list = array();
        }
    }

    /**
     * Track grade export status
     * @param object $grade_grade
     * @return string $status (unknow, new, regrade, nochange)
     */
    public function track($grade_grade) {

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
    public function close() {
        $this->flush(0);
    }
}

/**
 * Verify that there is a valid set of grades to export.
 * @param $courseid int The course being exported
 */
function export_verify_grades($courseid) {
    if (grade_needs_regrade_final_grades($courseid)) {
        throw new moodle_exception('gradesneedregrading', 'grades');
    }
}
