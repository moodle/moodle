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

class feedback_item_textarea extends feedback_item_base {
    protected $type = "textarea";
    private $commonparams;
    private $item_form;
    private $item;

    public function init() {

    }

    public function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('textarea_form.php');

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

        $width_and_height = explode('|', $item->presentation);

        if (isset($width_and_height[0]) AND $width_and_height[0] >= 5) {
            $itemwidth = $width_and_height[0];
        } else {
            $itemwidth = 30;
        }

        if (isset($width_and_height[1])) {
            $itemheight = $width_and_height[1];
        } else {
            $itemheight = 5;
        }
        $item->itemwidth = $itemwidth;
        $item->itemheight = $itemheight;

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

        $this->item_form = new feedback_textarea_form('edit_item.php', $customdata);
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

        $analysed_val = new stdClass();
        $analysed_val->data = array();
        $analysed_val->name = $item->name;

        $values = feedback_get_group_values($item, $groupid, $courseid);
        if ($values) {
            $data = array();
            foreach ($values as $value) {
                $data[] = str_replace("\n", '<br />', $value->value);
            }
            $analysed_val->data = $data;
        }
        return $analysed_val;
    }

    public function get_printval($item, $value) {

        if (!isset($value->value)) {
            return '';
        }

        return $value->value;
    }

    public function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {
        $values = feedback_get_group_values($item, $groupid, $courseid);
        if ($values) {
            echo '<tr><th colspan="2" align="left">';
            echo $itemnr.'&nbsp;('.$item->label.') '.$item->name;
            echo '</th></tr>';
            foreach ($values as $value) {
                echo '<tr>';
                echo '<td valign="top" align="left">';
                echo '-&nbsp;&nbsp;';
                echo '</td>';
                echo '<td align="left" valign="top">';
                echo str_replace("\n", '<br />', $value->value);
                echo '</td>';
                echo '</tr>';
            }
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
            if (isset($data[0])) {
                $worksheet->write_string($row_offset, 2, htmlspecialchars_decode($data[0], ENT_QUOTES), $xls_formats->value_bold);
            }
            $row_offset++;
            $sizeofdata = count($data);
            for ($i = 1; $i < $sizeofdata; $i++) {
                $worksheet->write_string($row_offset, 2, htmlspecialchars_decode($data[$i], ENT_QUOTES), $xls_formats->default);
                $row_offset++;
            }
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
        $strrequiredmark = '<img class="req" title="'.get_string('requiredelement', 'form').'" alt="'.
            get_string('requiredelement', 'form').'" src="'.$OUTPUT->pix_url('req') .'" />';

        $presentation = explode ("|", $item->presentation);
        $requiredmark = ($item->required == 1) ? $strrequiredmark : '';
        //print the question and label
        $inputname = $item->typ . '_' . $item->id;
        echo '<div class="feedback_item_label_'.$align.'">';
        echo '<label for="'. $inputname .'">';
        echo '('.$item->label.') ';
        echo format_text($item->name.$requiredmark, true, false, false);
        if ($item->dependitem) {
            if ($dependitem = $DB->get_record('feedback_item', array('id'=>$item->dependitem))) {
                echo ' <span class="feedback_depend">';
                echo '('.$dependitem->label.'-&gt;'.$item->dependvalue.')';
                echo '</span>';
            }
        }
        echo '</label>';
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
        echo '<span class="feedback_item_textarea">';
        echo '<textarea id="'.$inputname.'" '.
                       'name="'.$inputname.'" '.
                       'cols="'.$presentation[0].'" '.
                       'rows="'.$presentation[1].'">';
        echo '</textarea>';
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
        $strrequiredmark = '<img class="req" title="'.get_string('requiredelement', 'form').'" alt="'.
            get_string('requiredelement', 'form').'" src="'.$OUTPUT->pix_url('req') .'" />';

        $presentation = explode ("|", $item->presentation);
        $requiredmark = ($item->required == 1) ? $strrequiredmark :'';

        //print the question and label
        $inputname = $item->typ . '_' . $item->id;
        echo '<div class="feedback_item_label_'.$align.'">';
        echo '<label for="'. $inputname .'">';
            echo format_text($item->name . $requiredmark, true, false, false);
        if ($highlightrequire AND $item->required AND strval($value) == '') {
            echo '<br class="error"><span id="id_error_'.$inputname.'" class="error"> '.get_string('err_required', 'form').
                '</span><br id="id_error_break_'.$inputname.'" class="error" >';
        }
        echo '</label>';
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
        echo '<span class="feedback_item_textarea">';
        echo '<textarea id="'.$inputname.'" '.
                       'name="'.$inputname.'" '.
                       'cols="'.$presentation[0].'" '.
                       'rows="'.$presentation[1].'">';
        echo $value;
        echo '</textarea>';
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
        $strrequiredmark = '<img class="req" title="'.get_string('requiredelement', 'form').'" alt="'.
            get_string('requiredelement', 'form').'" src="'.$OUTPUT->pix_url('req') .'" />';

        $presentation = explode ("|", $item->presentation);
        $requiredmark = ($item->required == 1) ? $strrequiredmark : '';

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
            echo '('.$item->label.') ';
            echo format_text($item->name . $requiredmark, true, false, false);
        echo '</div>';

        //print the presentation
        echo $OUTPUT->box_start('generalbox boxalign'.$align);
        echo $value ? str_replace("\n", '<br />', $value) : '&nbsp;';
        echo $OUTPUT->box_end();
    }

    public function check_value($value, $item) {
        //if the item is not required, so the check is true if no value is given
        if ((!isset($value) OR $value == '') AND $item->required != 1) {
            return true;
        }
        if ($value == "") {
            return false;
        }
        return true;
    }

    public function create_value($data) {
        $data = s($data);
        return $data;
    }

    //compares the dbvalue with the dependvalue
    //dbvalue is the value put in by the user
    //dependvalue is the value that is compared
    public function compare_value($item, $dbvalue, $dependvalue) {
        if ($dbvalue == $dependvalue) {
            return true;
        }
        return false;
    }

    public function get_presentation($data) {
        return $data->itemwidth.'|'.$data->itemheight;
    }

    public function get_hasvalue() {
        return 1;
    }

    public function can_switch_require() {
        return true;
    }

    public function value_type() {
        return PARAM_RAW;
    }

    public function clean_input_value($value) {
        return s($value);
    }
}
