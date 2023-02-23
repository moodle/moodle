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

defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_numeric extends feedback_item_base {
    protected $type = "numeric";

    public function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('numeric_form.php');

        //get the lastposition number of the feedback_items
        $position = $item->position;
        $lastposition = $DB->count_records('feedback_item', array('feedback'=>$feedback->id));
        if ($position == -1) {
            $i_formselect_last = $lastposition + 1;
            $i_formselect_value = $lastposition + 1;
            $item->position = $lastposition + 1;
        } else {
            $i_formselect_last = $lastposition;
            $i_formselect_value = $item->position;
        }
        //the elements for position dropdownlist
        $positionlist = array_slice(range(0, $i_formselect_last), 1, $i_formselect_last, true);

        $item->presentation = empty($item->presentation) ? '' : $item->presentation;

        $range_from_to = explode('|', $item->presentation);
        if (isset($range_from_to[0]) AND is_numeric($range_from_to[0])) {
            $range_from = $this->format_float($range_from_to[0]);
        } else {
            $range_from = '-';
        }

        if (isset($range_from_to[1]) AND is_numeric($range_from_to[1])) {
            $range_to = $this->format_float($range_from_to[1]);
        } else {
            $range_to = '-';
        }

        $item->rangefrom = $range_from;
        $item->rangeto = $range_to;

        //all items for dependitem
        $feedbackitems = feedback_get_depend_candidates_for_item($feedback, $item);
        $commonparams = array('cmid'=>$cm->id,
                             'id'=>isset($item->id) ? $item->id : null,
                             'typ'=>$item->typ,
                             'items'=>$feedbackitems,
                             'feedback'=>$feedback->id);

        //build the form
        $customdata = array('item' => $item,
                            'common' => $commonparams,
                            'positionlist' => $positionlist,
                            'position' => $position);

        $this->item_form = new feedback_numeric_form('edit_item.php', $customdata);
    }

    public function save_item() {
        global $DB;

        if (!$this->get_data()) {
            return false;
        }
        $item = $this->item;

        if (isset($item->clone_item) AND $item->clone_item) {
            $item->id = ''; //to clone this item
            $item->position++;
        }

        $item->hasvalue = $this->get_hasvalue();
        if (!$item->id) {
            $item->id = $DB->insert_record('feedback_item', $item);
        } else {
            $DB->update_record('feedback_item', $item);
        }

        return $DB->get_record('feedback_item', array('id'=>$item->id));
    }

    /**
     * Helper function for collected data, both for analysis page and export to excel
     *
     * @param stdClass $item the db-object from feedback_item
     * @param int $groupid
     * @param int $courseid
     * @return stdClass
     */
    protected function get_analysed($item, $groupid = false, $courseid = false) {
        global $DB;

        $analysed = new stdClass();
        $analysed->data = array();
        $analysed->name = $item->name;
        $values = feedback_get_group_values($item, $groupid, $courseid);

        $avg = 0.0;
        $counter = 0;
        if ($values) {
            $data = array();
            foreach ($values as $value) {
                if (is_numeric($value->value)) {
                    $data[] = $value->value;
                    $avg += $value->value;
                    $counter++;
                }
            }
            $avg = $counter > 0 ? $avg / $counter : null;
            $analysed->data = $data;
            $analysed->avg = $avg;
        }
        return $analysed;
    }

    public function get_printval($item, $value) {
        if (!isset($value->value)) {
            return '';
        }

        return $value->value;
    }

    public function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {

        $values = $this->get_analysed($item, $groupid, $courseid);

        if (isset($values->data) AND is_array($values->data)) {
            echo "<table class=\"analysis itemtype_{$item->typ}\">";
            echo '<tr><th class="text-left">';
            echo $itemnr . ' ';
            if (strval($item->label) !== '') {
                echo '('. format_string($item->label).') ';
            }
            echo format_text($item->name, FORMAT_HTML, array('noclean' => true, 'para' => false));
            echo '</th></tr>';

            foreach ($values->data as $value) {
                echo '<tr><td class="singlevalue">';
                echo $this->format_float($value);
                echo '</td></tr>';
            }

            if (isset($values->avg)) {
                $avg = format_float($values->avg, 2);
            } else {
                $avg = '-';
            }
            echo '<tr><td><b>';
            echo get_string('average', 'feedback').': '.$avg;
            echo '</b></td></tr>';
            echo '</table>';
        }
    }

    public function excelprint_item(&$worksheet, $row_offset,
                             $xls_formats, $item,
                             $groupid, $courseid = false) {

        $analysed_item = $this->get_analysed($item, $groupid, $courseid);

        $worksheet->write_string($row_offset, 0, $item->label, $xls_formats->head2);
        $worksheet->write_string($row_offset, 1, $item->name, $xls_formats->head2);
        $data = $analysed_item->data;
        if (is_array($data)) {

            // Export average.
            $worksheet->write_string($row_offset,
                                     2,
                                     get_string('average', 'feedback'),
                                     $xls_formats->value_bold);

            if (isset($analysed_item->avg)) {
                $worksheet->write_number($row_offset + 1,
                                         2,
                                         $analysed_item->avg,
                                         $xls_formats->value_bold);
            } else {
                $worksheet->write_string($row_offset + 1,
                                         2,
                                         '',
                                         $xls_formats->value_bold);
            }
            $row_offset++;
        }
        $row_offset++;
        return $row_offset;
    }

    /**
     * Prints the float nicely in the localized format
     *
     * Similar to format_float() but automatically calculates the number of decimal places
     *
     * @param float $value The float to print
     * @return string
     */
    protected function format_float($value) {
        if (!is_numeric($value)) {
            return null;
        }
        $decimal = is_int($value) ? 0 : strlen(substr(strrchr($value, '.'), 1));
        return format_float($value, $decimal);
    }

    /**
     * Returns human-readable boundaries (min - max)
     * @param stdClass $item
     * @return string
     */
    protected function get_boundaries_for_display($item) {
        list($rangefrom, $rangeto) = explode('|', $item->presentation);
        if (!isset($rangefrom) || !is_numeric($rangefrom)) {
            $rangefrom = null;
        }
        if (!isset($rangeto) || !is_numeric($rangeto)) {
            $rangeto = null;
        }

        if (is_null($rangefrom) && is_numeric($rangeto)) {
            return ' (' . get_string('maximal', 'feedback') .
                        ': ' . $this->format_float($rangeto) . ')';
        }
        if (is_numeric($rangefrom) && is_null($rangeto)) {
            return ' (' . get_string('minimal', 'feedback') .
                        ': ' . $this->format_float($rangefrom) . ')';
        }
        if (is_null($rangefrom) && is_null($rangeto)) {
            return '';
        }
        return ' (' . $this->format_float($rangefrom) .
                ' - ' . $this->format_float($rangeto) . ')';
    }

    /**
     * Returns the postfix to be appended to the display name that is based on other settings
     *
     * @param stdClass $item
     * @return string
     */
    public function get_display_name_postfix($item) {
        return html_writer::span($this->get_boundaries_for_display($item), 'boundaries');
    }

    /**
     * Adds an input element to the complete form
     *
     * @param stdClass $item
     * @param mod_feedback_complete_form $form
     */
    public function complete_form_element($item, $form) {
        $name = $this->get_display_name($item);
        $inputname = $item->typ . '_' . $item->id;
        $form->add_form_element($item,
                ['text', $inputname, $name],
                true,
                false
                );
        $form->set_element_type($inputname, PARAM_NOTAGS);
        $tmpvalue = $this->format_float($form->get_item_value($item));
        $form->set_element_default($inputname, $tmpvalue);

        // Add form validation rule to check for boundaries.
        $form->add_validation_rule(function($values, $files) use ($item) {
            $inputname = $item->typ . '_' . $item->id;
            list($rangefrom, $rangeto) = explode('|', $item->presentation);
            if (!isset($values[$inputname]) || trim($values[$inputname]) === '') {
                return $item->required ? array($inputname => get_string('required')) : true;
            }
            $value = unformat_float($values[$inputname], true);
            if ($value === false) {
                return array($inputname => get_string('invalidnum', 'error'));
            }
            if ((is_numeric($rangefrom) && $value < floatval($rangefrom)) ||
                    (is_numeric($rangeto) && $value > floatval($rangeto))) {
                return array($inputname => get_string('numberoutofrange', 'feedback'));
            }
            return true;
        });
    }

    public function create_value($data) {
        $data = unformat_float($data, true);

        if (is_numeric($data)) {
            $data = floatval($data);
        } else {
            $data = '';
        }
        return $data;
    }

    /**
     * Return the analysis data ready for external functions.
     *
     * @param stdClass $item     the item (question) information
     * @param int      $groupid  the group id to filter data (optional)
     * @param int      $courseid the course id (optional)
     * @return array an array of data with non scalar types json encoded
     * @since  Moodle 3.3
     */
    public function get_analysed_for_external($item, $groupid = false, $courseid = false) {

        $externaldata = array();
        $data = $this->get_analysed($item, $groupid, $courseid);

        if (is_array($data->data)) {
            return $data->data; // No need to json, scalar type.
        }
        return $externaldata;
    }
}
