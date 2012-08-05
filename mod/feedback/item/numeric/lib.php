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
    public $sep_dec, $sep_thous;
    private $commonparams;
    private $item_form;
    private $item;

    public function init() {
        $this->sep_dec = get_string('separator_decimal', 'feedback');
        if (substr($this->sep_dec, 0, 2) == '[[') {
            $this->sep_dec = FEEDBACK_DECIMAL;
        }

        $this->sep_thous = get_string('separator_thousand', 'feedback');
        if (substr($this->sep_thous, 0, 2) == '[[') {
            $this->sep_thous = FEEDBACK_THOUSAND;
        }
    }

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
            $range_from = str_replace(FEEDBACK_DECIMAL,
                                $this->sep_dec,
                                floatval($range_from_to[0]));
        } else {
            $range_from = '-';
        }

        if (isset($range_from_to[1]) AND is_numeric($range_from_to[1])) {
            $range_to = str_replace(FEEDBACK_DECIMAL,
                                $this->sep_dec,
                                floatval($range_from_to[1]));
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

    //this function only can used after the call of build_editform()
    public function show_editform() {
        $this->item_form->display();
    }

    public function is_cancelled() {
        return $this->item_form->is_cancelled();
    }

    public function get_data() {
        if ($this->item = $this->item_form->get_data()) {
            return true;
        }
        return false;
    }

    public function save_item() {
        global $DB;

        if (!$item = $this->item_form->get_data()) {
            return false;
        }

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


    //liefert eine Struktur ->name, ->data = array(mit Antworten)
    public function get_analysed($item, $groupid = false, $courseid = false) {
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
            $avg = $counter > 0 ? $avg / $counter : 0;
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
            echo '<tr><th colspan="2" align="left">';
            echo $itemnr.'&nbsp;('.$item->label.') '.$item->name;
            echo '</th></tr>';

            foreach ($values->data as $value) {
                echo '<tr><td colspan="2" valign="top" align="left">';
                echo '-&nbsp;&nbsp;'.number_format($value, 2, $this->sep_dec, $this->sep_thous);
                echo '</td></tr>';
            }

            if (isset($values->avg)) {
                $avg = number_format($values->avg, 2, $this->sep_dec, $this->sep_thous);
            } else {
                $avg = number_format(0, 2, $this->sep_dec, $this->sep_thous);
            }
            echo '<tr><td align="left" colspan="2"><b>';
            echo get_string('average', 'feedback').': '.$avg;
            echo '</b></td></tr>';
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

            //mittelwert anzeigen
            $worksheet->write_string($row_offset,
                                     2,
                                     get_string('average', 'feedback'),
                                     $xls_formats->value_bold);

            $worksheet->write_number($row_offset + 1,
                                     2,
                                     $analysed_item->avg,
                                     $xls_formats->value_bold);
            $row_offset++;
        }
        $row_offset++;
        return $row_offset;
    }

    /**
     * print the item at the edit-page of feedback
     *
     * @global object
     * @param object $item
     * @return void
     */
    public function print_item_preview($item) {
        global $OUTPUT, $DB;

        $align = right_to_left() ? 'right' : 'left';
        $str_required_mark = '<span class="feedback_required_mark">*</span>';

        //get the range
        $range_from_to = explode('|', $item->presentation);

        //get the min-value
        if (isset($range_from_to[0]) AND is_numeric($range_from_to[0])) {
            $range_from = floatval($range_from_to[0]);
        } else {
            $range_from = 0;
        }

        //get the max-value
        if (isset($range_from_to[1]) AND is_numeric($range_from_to[1])) {
            $range_to = floatval($range_from_to[1]);
        } else {
            $range_to = 0;
        }

        $requiredmark =  ($item->required == 1) ? $str_required_mark : '';
        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
        echo '('.$item->label.') ';
        echo format_text($item->name . $requiredmark, true, false, false);
        if ($item->dependitem) {
            $params = array('id'=>$item->dependitem);
            if ($dependitem = $DB->get_record('feedback_item', $params)) {
                echo ' <span class="feedback_depend">';
                echo '('.$dependitem->label.'-&gt;'.$item->dependvalue.')';
                echo '</span>';
            }
        }
        echo '<span class="feedback_item_numinfo">';
        switch(true) {
            case ($range_from === '-' AND is_numeric($range_to)):
                echo ' ('.get_string('maximal', 'feedback').
                        ': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                break;
            case (is_numeric($range_from) AND $range_to === '-'):
                echo ' ('.get_string('minimal', 'feedback').
                        ': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).')';
                break;
            case ($range_from === '-' AND $range_to === '-'):
                break;
            default:
                echo ' ('.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).
                        ' - '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                break;
        }
        echo '</span>';
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
        echo '<span class="feedback_item_textfield">';
        echo '<input type="text" '.
                    'name="'.$item->typ.'_'.$item->id.'" '.
                    'size="10" '.
                    'maxlength="10" '.
                    'value="" />';

        echo '</span>';
        echo '</div>';
    }

    /**
     * print the item at the complete-page of feedback
     *
     * @global object
     * @param object $item
     * @param string $value
     * @param bool $highlightrequire
     * @return void
     */
    public function print_item_complete($item, $value = '', $highlightrequire = false) {
        global $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';
        $str_required_mark = '<span class="feedback_required_mark">*</span>';

        //get the range
        $range_from_to = explode('|', $item->presentation);

        //get the min-value
        if (isset($range_from_to[0]) AND is_numeric($range_from_to[0])) {
            $range_from = floatval($range_from_to[0]);
        } else {
            $range_from = 0;
        }

        //get the max-value
        if (isset($range_from_to[1]) AND is_numeric($range_from_to[1])) {
            $range_to = floatval($range_from_to[1]);
        } else {
            $range_to = 0;
        }

        if ($highlightrequire AND (!$this->check_value($value, $item))) {
            $highlight = ' missingrequire';
        } else {
            $highlight = '';
        }
        $requiredmark = ($item->required == 1) ? $str_required_mark : '';

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.$highlight.'">';
        echo format_text($item->name . $requiredmark, true, false, false);
        echo '<span class="feedback_item_numinfo">';
        switch(true) {
            case ($range_from === '-' AND is_numeric($range_to)):
                echo ' ('.get_string('maximal', 'feedback').
                        ': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                break;
            case (is_numeric($range_from) AND $range_to === '-'):
                echo ' ('.get_string('minimal', 'feedback').
                        ': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).')';
                break;
            case ($range_from === '-' AND $range_to === '-'):
                break;
            default:
                echo ' ('.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).
                        ' - '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                break;
        }
        echo '</span>';
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.$highlight.'">';
        echo '<span class="feedback_item_textfield">';
        echo '<input type="text" '.
                     'name="'.$item->typ.'_'.$item->id.'" '.
                     'size="10" '.
                     'maxlength="10" '.
                     'value="'.$value.'" />';

        echo '</span>';
        echo '</div>';
    }

    /**
     * print the item at the complete-page of feedback
     *
     * @global object
     * @param object $item
     * @param string $value
     * @return void
     */
    public function print_item_show_value($item, $value = '') {
        global $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';
        $str_required_mark = '<span class="feedback_required_mark">*</span>';

        //get the range
        $range_from_to = explode('|', $item->presentation);
        //get the min-value
        if (isset($range_from_to[0]) AND is_numeric($range_from_to[0])) {
            $range_from = floatval($range_from_to[0]);
        } else {
            $range_from = 0;
        }
        //get the max-value
        if (isset($range_from_to[1]) AND is_numeric($range_from_to[1])) {
            $range_to = floatval($range_from_to[1]);
        } else {
            $range_to = 0;
        }
        $requiredmark = ($item->required == 1) ? $str_required_mark : '';

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
        echo '('.$item->label.') ';
        echo format_text($item->name . $requiredmark, true, false, false);
        switch(true) {
            case ($range_from === '-' AND is_numeric($range_to)):
                echo ' ('.get_string('maximal', 'feedback').
                    ': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                break;
            case (is_numeric($range_from) AND $range_to === '-'):
                echo ' ('.get_string('minimal', 'feedback').
                    ': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).')';
                break;
            case ($range_from === '-' AND $range_to === '-'):
                break;
            default:
                echo ' ('.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).
                    ' - '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                break;
        }
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
        echo $OUTPUT->box_start('generalbox boxalign'.$align);
        if (is_numeric($value)) {
            $str_num_value = number_format($value, 2, $this->sep_dec, $this->sep_thous);
        } else {
            $str_num_value = '&nbsp;';
        }
        echo $str_num_value;
        echo $OUTPUT->box_end();
        echo '</div>';
    }

    public function check_value($value, $item) {
        $value = str_replace($this->sep_dec, FEEDBACK_DECIMAL, $value);
        //if the item is not required, so the check is true if no value is given
        if ((!isset($value) OR $value == '') AND $item->required != 1) {
            return true;
        }
        if (!is_numeric($value)) {
            return false;
        }

        $range_from_to = explode('|', $item->presentation);
        if (isset($range_from_to[0]) AND is_numeric($range_from_to[0])) {
            $range_from = floatval($range_from_to[0]);
        } else {
            $range_from = '-';
        }
        if (isset($range_from_to[1]) AND is_numeric($range_from_to[1])) {
            $range_to = floatval($range_from_to[1]);
        } else {
            $range_to = '-';
        }

        switch(true) {
            case ($range_from === '-' AND is_numeric($range_to)):
                if (floatval($value) <= $range_to) {
                    return true;
                }
                break;
            case (is_numeric($range_from) AND $range_to === '-'):
                if (floatval($value) >= $range_from) {
                    return true;
                }
                break;
            case ($range_from === '-' AND $range_to === '-'):
                return true;
                break;
            default:
                if (floatval($value) >= $range_from AND floatval($value) <= $range_to) {
                    return true;
                }
                break;
        }

        return false;
    }

    public function create_value($data) {
        $data = str_replace($this->sep_dec, FEEDBACK_DECIMAL, $data);

        if (is_numeric($data)) {
            $data = floatval($data);
        } else {
            $data = '';
        }
        return $data;
    }

    //compares the dbvalue with the dependvalue
    //dbvalue is the number put in by the user
    //dependvalue is the value that is compared
    public function compare_value($item, $dbvalue, $dependvalue) {
        if ($dbvalue == $dependvalue) {
            return true;
        }
        return false;
    }

    public function get_presentation($data) {
        $num1 = str_replace($this->sep_dec, FEEDBACK_DECIMAL, $data->numericrangefrom);
        if (is_numeric($num1)) {
            $num1 = floatval($num1);
        } else {
            $num1 = '-';
        }

        $num2 = str_replace($this->sep_dec, FEEDBACK_DECIMAL, $data->numericrangeto);
        if (is_numeric($num2)) {
            $num2 = floatval($num2);
        } else {
            $num2 = '-';
        }

        if ($num1 === '-' OR $num2 === '-') {
            return $num1 . '|'. $num2;
        }

        if ($num1 > $num2) {
            return $num2 . '|'. $num1;
        } else {
            return $num1 . '|'. $num2;
        }
    }

    public function get_hasvalue() {
        return 1;
    }

    public function can_switch_require() {
        return true;
    }

    public function value_type() {
        return PARAM_FLOAT;
    }

    public function clean_input_value($value) {
        $value = str_replace($this->sep_dec, FEEDBACK_DECIMAL, $value);
        if (!is_numeric($value)) {
            if ($value == '') {
                return null; //an empty string should be null
            } else {
                return clean_param($value, PARAM_TEXT); //we have to know the value if it is wrong
            }
        }
        return clean_param($value, $this->value_type());
    }
}
