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

define('FEEDBACK_MULTICHOICE_TYPE_SEP', '>>>>>');
define('FEEDBACK_MULTICHOICE_LINE_SEP', '|');
define('FEEDBACK_MULTICHOICE_ADJUST_SEP', '<<<<<');
define('FEEDBACK_MULTICHOICE_IGNOREEMPTY', 'i');
define('FEEDBACK_MULTICHOICE_HIDENOSELECT', 'h');

class feedback_item_multichoice extends feedback_item_base {
    protected $type = "multichoice";
    private $commonparams;
    private $item_form;
    private $item;

    public function init() {

    }

    public function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('multichoice_form.php');

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
        $info = $this->get_info($item);

        $item->ignoreempty = $this->ignoreempty($item);
        $item->hidenoselect = $this->hidenoselect($item);

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
                            'position' => $position,
                            'info' => $info);

        $this->item_form = new feedback_multichoice_form('edit_item.php', $customdata);
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

        $this->set_ignoreempty($item, $item->ignoreempty);
        $this->set_hidenoselect($item, $item->hidenoselect);

        $item->hasvalue = $this->get_hasvalue();
        if (!$item->id) {
            $item->id = $DB->insert_record('feedback_item', $item);
        } else {
            $DB->update_record('feedback_item', $item);
        }

        return $DB->get_record('feedback_item', array('id'=>$item->id));
    }


    //gets an array with three values(typ, name, XXX)
    //XXX is an object with answertext, answercount and quotient
    public function get_analysed($item, $groupid = false, $courseid = false) {
        $info = $this->get_info($item);

        $analysed_item = array();
        $analysed_item[] = $item->typ;
        $analysed_item[] = $item->name;

        //get the possible answers
        $answers = null;
        $answers = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);
        if (!is_array($answers)) {
            return null;
        }

        //get the values
        $values = feedback_get_group_values($item, $groupid, $courseid, $this->ignoreempty($item));
        if (!$values) {
            return null;
        }

        //get answertext, answercount and quotient for each answer
        $analysed_answer = array();
        if ($info->subtype == 'c') {
            $sizeofanswers = count($answers);
            for ($i = 1; $i <= $sizeofanswers; $i++) {
                $ans = new stdClass();
                $ans->answertext = $answers[$i-1];
                $ans->answercount = 0;
                foreach ($values as $value) {
                    //ist die Antwort gleich dem index der Antworten + 1?
                    $vallist = explode(FEEDBACK_MULTICHOICE_LINE_SEP, $value->value);
                    foreach ($vallist as $val) {
                        if ($val == $i) {
                            $ans->answercount++;
                        }
                    }
                }
                $ans->quotient = $ans->answercount / count($values);
                $analysed_answer[] = $ans;
            }
        } else {
            $sizeofanswers = count($answers);
            for ($i = 1; $i <= $sizeofanswers; $i++) {
                $ans = new stdClass();
                $ans->answertext = $answers[$i-1];
                $ans->answercount = 0;
                foreach ($values as $value) {
                    //ist die Antwort gleich dem index der Antworten + 1?
                    if ($value->value == $i) {
                        $ans->answercount++;
                    }
                }
                $ans->quotient = $ans->answercount / count($values);
                $analysed_answer[] = $ans;
            }
        }
        $analysed_item[] = $analysed_answer;
        return $analysed_item;
    }

    public function get_printval($item, $value) {
        $info = $this->get_info($item);

        $printval = '';

        if (!isset($value->value)) {
            return $printval;
        }

        $presentation = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);

        if ($info->subtype == 'c') {
            $vallist = array_values(explode (FEEDBACK_MULTICHOICE_LINE_SEP, $value->value));
            $sizeofvallist = count($vallist);
            $sizeofpresentation = count($presentation);
            for ($i = 0; $i < $sizeofvallist; $i++) {
                for ($k = 0; $k < $sizeofpresentation; $k++) {
                    if ($vallist[$i] == ($k + 1)) {//Die Werte beginnen bei 1, das Array aber mit 0
                        $printval .= trim($presentation[$k]) . chr(10);
                        break;
                    }
                }
            }
        } else {
            $index = 1;
            foreach ($presentation as $pres) {
                if ($value->value == $index) {
                    $printval = $pres;
                    break;
                }
                $index++;
            }
        }
        return $printval;
    }

    public function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {
        global $OUTPUT;
        $sep_dec = get_string('separator_decimal', 'feedback');
        if (substr($sep_dec, 0, 2) == '[[') {
            $sep_dec = FEEDBACK_DECIMAL;
        }

        $sep_thous = get_string('separator_thousand', 'feedback');
        if (substr($sep_thous, 0, 2) == '[[') {
            $sep_thous = FEEDBACK_THOUSAND;
        }

        $analysed_item = $this->get_analysed($item, $groupid, $courseid);
        if ($analysed_item) {
            $itemname = $analysed_item[1];
            echo '<tr><th colspan="2" align="left">';
            echo $itemnr.'&nbsp;('.$item->label.') '.$itemname;
            echo '</th></tr>';

            $analysed_vals = $analysed_item[2];
            $pixnr = 0;
            foreach ($analysed_vals as $val) {
                $intvalue = $pixnr % 10;
                $pix = $OUTPUT->pix_url('multichoice/' . $intvalue, 'feedback');
                $pixnr++;
                $pixwidth = intval($val->quotient * FEEDBACK_MAX_PIX_LENGTH);
                $quotient = number_format(($val->quotient * 100), 2, $sep_dec, $sep_thous);
                $str_quotient = '';
                if ($val->quotient > 0) {
                    $str_quotient = '&nbsp;('. $quotient . '&nbsp;%)';
                }
                echo '<tr>';
                echo '<td align="left" valign="top">
                            -&nbsp;&nbsp;'.trim($val->answertext).':
                      </td>
                      <td align="left" style="width:'.FEEDBACK_MAX_PIX_LENGTH.';">
                        <img class="feedback_bar_image" alt="'.$intvalue.'" src="'.$pix.'" height="5" width="'.$pixwidth.'" />
                        &nbsp;'.$val->answercount.$str_quotient.'
                      </td>';
                echo '</tr>';
            }
        }
    }

    public function excelprint_item(&$worksheet, $row_offset,
                             $xls_formats, $item,
                             $groupid, $courseid = false) {

        $analysed_item = $this->get_analysed($item, $groupid, $courseid);

        $data = $analysed_item[2];

        //frage schreiben
        $worksheet->write_string($row_offset, 0, $item->label, $xls_formats->head2);
        $worksheet->write_string($row_offset, 1, $analysed_item[1], $xls_formats->head2);
        if (is_array($data)) {
            $sizeofdata = count($data);
            for ($i = 0; $i < $sizeofdata; $i++) {
                $analysed_data = $data[$i];

                $worksheet->write_string($row_offset,
                                         $i + 2,
                                         trim($analysed_data->answertext),
                                         $xls_formats->head2);

                $worksheet->write_number($row_offset + 1,
                                         $i + 2,
                                         $analysed_data->answercount,
                                         $xls_formats->default);

                $worksheet->write_number($row_offset + 2,
                                         $i + 2,
                                         $analysed_data->quotient,
                                         $xls_formats->procent);
            }
        }
        $row_offset += 3;
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
        $info = $this->get_info($item);
        $align = right_to_left() ? 'right' : 'left';

        $presentation = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);
        $strrequiredmark = '<img class="req" title="'.get_string('requiredelement', 'form').'" alt="'.
            get_string('requiredelement', 'form').'" src="'.$OUTPUT->pix_url('req') .'" />';

        //test if required and no value is set so we have to mark this item
        //we have to differ check and the other subtypes
        $requiredmark = ($item->required == 1) ? $strrequiredmark : '';

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
        if ($info->subtype == 'd') {
            echo '<label for="'. $item->typ . '_' . $item->id .'">';
        }
        echo '('.$item->label.') ';
        echo format_text($item->name . $requiredmark, FORMAT_HTML, array('noclean' => true, 'para' => false));
        if ($item->dependitem) {
            if ($dependitem = $DB->get_record('feedback_item', array('id'=>$item->dependitem))) {
                echo ' <span class="feedback_depend">';
                echo '('.$dependitem->label.'-&gt;'.$item->dependvalue.')';
                echo '</span>';
            }
        }
        if ($info->subtype == 'd') {
            echo '</label>';
        }
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
        $index = 1;
        $checked = '';
        if ($info->subtype == 'r' || $info->subtype == 'c') {
            // if (r)adio buttons or (c)heckboxes
            echo '<fieldset>';
            echo '<ul>';
        }

        if ($info->horizontal) {
            $hv = 'h';
        } else {
            $hv = 'v';
        }

        if ($info->subtype == 'r' AND !$this->hidenoselect($item)) {
        //print the "not_selected" item on radiobuttons
        ?>
        <li class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
            <span class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                <?php
                    echo '<input type="radio" '.
                            'name="'.$item->typ.'_'.$item->id.'[]" '.
                            'id="'.$item->typ.'_'.$item->id.'_xxx" '.
                            'value="" checked="checked" />';
                ?>
            </span>
            <span class="feedback_item_radiolabel_<?php echo $hv.'_'.$align;?>">
                <label for="<?php echo $item->typ . '_' . $item->id.'_xxx';?>">
                    <?php print_string('not_selected', 'feedback');?>&nbsp;
                </label>
            </span>
        </li>
        <?php
        }

        switch($info->subtype) {
            case 'r':
                $this->print_item_radio($presentation, $item, false, $info, $align);
                break;
            case 'c':
                $this->print_item_check($presentation, $item, false, $info, $align);
                break;
            case 'd':
                $this->print_item_dropdown($presentation, $item, false, $info, $align);
                break;
        }
        if ($info->subtype == 'r' || $info->subtype == 'c') {
            // if (r)adio buttons or (c)heckboxes
            echo '</ul>';
            echo '</fieldset>';
        }
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
    public function print_item_complete($item, $value = null, $highlightrequire = false) {
        global $OUTPUT;
        $info = $this->get_info($item);
        $align = right_to_left() ? 'right' : 'left';

        if ($value == null) {
            $value = array();
        }
        $presentation = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);
        $strrequiredmark = '<img class="req" title="'.get_string('requiredelement', 'form').'" alt="'.
            get_string('requiredelement', 'form').'" src="'.$OUTPUT->pix_url('req') .'" />';

        //test if required and no value is set so we have to mark this item
        //we have to differ check and the other subtypes
        if (is_array($value)) {
            $values = $value;
        } else {
            $values = explode(FEEDBACK_MULTICHOICE_LINE_SEP, $value);
        }
        $requiredmark = ($item->required == 1) ? $strrequiredmark : '';

        //print the question and label
        $inputname = $item->typ . '_' . $item->id;
        echo '<div class="feedback_item_label_'.$align.'">';
        if ($info->subtype == 'd') {
            echo '<label for="'. $inputname .'">';
            echo format_text($item->name.$requiredmark, true, false, false);
            if ($highlightrequire AND $item->required AND (count($values) == 0 OR $values[0] == '' OR $values[0] == 0)) {
                echo '<br class="error"><span id="id_error_'.$inputname.'" class="error"> '.get_string('err_required', 'form').
                    '</span><br id="id_error_break_'.$inputname.'" class="error" >';
            }
            echo '</label>';
        } else {
            echo format_text($item->name . $requiredmark, FORMAT_HTML, array('noclean' => true, 'para' => false));
            if ($highlightrequire AND $item->required AND (count($values) == 0 OR $values[0] == '' OR $values[0] == 0)) {
                echo '<br class="error"><span id="id_error_'.$inputname.'" class="error"> '.get_string('err_required', 'form').
                    '</span><br id="id_error_break_'.$inputname.'" class="error" >';
            }
        }
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';

        if ($info->subtype == 'r' || $info->subtype == 'c') {
            // if (r)adio buttons or (c)heckboxes
            echo '<fieldset>';
            echo '<ul>';
        }
        if ($info->horizontal) {
            $hv = 'h';
        } else {
            $hv = 'v';
        }
        //print the "not_selected" item on radiobuttons
        if ($info->subtype == 'r' AND !$this->hidenoselect($item)) {
        ?>
            <li class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                <span class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                    <?php
                    $checked = '';
                    // if (!$value) {
                        // $checked = 'checked="checked"';
                    // }
                    if (count($values) == 0 OR $values[0] == '' OR $values[0] == 0) {
                        $checked = 'checked="checked"';
                    }
                    echo '<input type="radio" '.
                            'name="'.$item->typ.'_'.$item->id.'[]" '.
                            'id="'.$item->typ.'_'.$item->id.'_xxx" '.
                            'value="" '.$checked.' />';
                    ?>
                </span>
                <span class="feedback_item_radiolabel_<?php echo $hv.'_'.$align;?>">
                    <label for="<?php echo $item->typ.'_'.$item->id.'_xxx';?>">
                        <?php print_string('not_selected', 'feedback');?>&nbsp;
                    </label>
                </span>
            </li>
        <?php
        }

        switch($info->subtype) {
            case 'r':
                $this->print_item_radio($presentation, $item, $value, $info, $align);
                break;
            case 'c':
                $this->print_item_check($presentation, $item, $value, $info, $align);
                break;
            case 'd':
                $this->print_item_dropdown($presentation, $item, $value, $info, $align);
                break;
        }
        if ($info->subtype == 'r' || $info->subtype == 'c') {
            // if (r)adio buttons or (c)heckboxes
            echo '</ul>';
            echo '</fieldset>';
        }
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
    public function print_item_show_value($item, $value = null) {
        global $OUTPUT;
        $info = $this->get_info($item);
        $align = right_to_left() ? 'right' : 'left';

        if ($value == null) {
            $value = array();
        }

        $presentation = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);

        //test if required and no value is set so we have to mark this item
        //we have to differ check and the other subtypes
        if ($info->subtype == 'c') {
            if (is_array($value)) {
                $values = $value;
            } else {
                $values = explode(FEEDBACK_MULTICHOICE_LINE_SEP, $value);
            }
        }
        $requiredmark = '';
        if ($item->required == 1) {
            $requiredmark = '<img class="req" title="'.get_string('requiredelement', 'form').'" alt="'.
                get_string('requiredelement', 'form').'" src="'.$OUTPUT->pix_url('req') .'" />';
        }

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
        echo '('.$item->label.') ';
        echo format_text($item->name . $requiredmark, FORMAT_HTML, array('noclean' => true, 'para' => false));
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
        $index = 1;
        if ($info->subtype == 'c') {
            echo $OUTPUT->box_start('generalbox boxalign'.$align);
            foreach ($presentation as $pres) {
                foreach ($values as $val) {
                    if ($val == $index) {
                        echo '<div class="feedback_item_multianswer">';
                        echo format_text($pres, FORMAT_HTML, array('noclean' => true, 'para' => false));
                        echo '</div>';
                        break;
                    }
                }
                $index++;
            }
            echo $OUTPUT->box_end();
        } else {
            foreach ($presentation as $pres) {
                if ($value == $index) {
                    echo $OUTPUT->box_start('generalbox boxalign'.$align);
                    echo format_text($pres, FORMAT_HTML, array('noclean' => true, 'para' => false));
                    echo $OUTPUT->box_end();
                    break;
                }
                $index++;
            }
        }
        echo '</div>';
    }

    public function check_value($value, $item) {
        $info = $this->get_info($item);

        if ($item->required != 1) {
            return true;
        }

        if (!isset($value) OR !is_array($value) OR $value[0] == '' OR $value[0] == 0) {
            return false;
        }

        return true;
    }

    public function create_value($data) {
        $vallist = $data;
        if (is_array($vallist)) {
            $vallist = array_unique($vallist);
        }
        return trim($this->item_array_to_string($vallist));
    }

    //compares the dbvalue with the dependvalue
    //dbvalue is the number of one selection
    //dependvalue is the presentation of one selection
    public function compare_value($item, $dbvalue, $dependvalue) {

        if (is_array($dbvalue)) {
            $dbvalues = $dbvalue;
        } else {
            $dbvalues = explode(FEEDBACK_MULTICHOICE_LINE_SEP, $dbvalue);
        }

        $info = $this->get_info($item);
        $presentation = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);
        $index = 1;
        foreach ($presentation as $pres) {
            foreach ($dbvalues as $dbval) {
                if ($dbval == $index AND trim($pres) == $dependvalue) {
                    return true;
                }
            }
            $index++;
        }
        return false;
    }

    public function get_presentation($data) {
        $present = str_replace("\n", FEEDBACK_MULTICHOICE_LINE_SEP, trim($data->itemvalues));
        if (!isset($data->subtype)) {
            $subtype = 'r';
        } else {
            $subtype = substr($data->subtype, 0, 1);
        }
        if (isset($data->horizontal) AND $data->horizontal == 1 AND $subtype != 'd') {
            $present .= FEEDBACK_MULTICHOICE_ADJUST_SEP.'1';
        }
        return $subtype.FEEDBACK_MULTICHOICE_TYPE_SEP.$present;
    }

    public function get_hasvalue() {
        return 1;
    }

    public function get_info($item) {
        $presentation = empty($item->presentation) ? '' : $item->presentation;

        $info = new stdClass();
        //check the subtype of the multichoice
        //it can be check(c), radio(r) or dropdown(d)
        $info->subtype = '';
        $info->presentation = '';
        $info->horizontal = false;

        $parts = explode(FEEDBACK_MULTICHOICE_TYPE_SEP, $item->presentation);
        @list($info->subtype, $info->presentation) = $parts;
        if (!isset($info->subtype)) {
            $info->subtype = 'r';
        }

        if ($info->subtype != 'd') {
            $parts = explode(FEEDBACK_MULTICHOICE_ADJUST_SEP, $info->presentation);
            @list($info->presentation, $info->horizontal) = $parts;
            if (isset($info->horizontal) AND $info->horizontal == 1) {
                $info->horizontal = true;
            } else {
                $info->horizontal = false;
            }
        }
        return $info;
    }

    private function item_array_to_string($value) {
        if (!is_array($value)) {
            return $value;
        }
        $retval = '';
        $arrvals = array_values($value);
        $arrvals = clean_param_array($arrvals, PARAM_INT);  //prevent sql-injection
        $retval = $arrvals[0];
        $sizeofarrvals = count($arrvals);
        for ($i = 1; $i < $sizeofarrvals; $i++) {
            $retval .= FEEDBACK_MULTICHOICE_LINE_SEP.$arrvals[$i];
        }
        return $retval;
    }

    private function print_item_radio($presentation, $item, $value, $info, $align) {
        $index = 1;
        $checked = '';

        if (is_array($value)) {
            $values = $value;
        } else {
            $values = array($value);
        }

        if ($info->horizontal) {
            $hv = 'h';
        } else {
            $hv = 'v';
        }

        foreach ($presentation as $radio) {
            foreach ($values as $val) {
                if ($val == $index) {
                    $checked = 'checked="checked"';
                    break;
                } else {
                    $checked = '';
                }
            }
            $inputname = $item->typ . '_' . $item->id;
            $inputid = $inputname.'_'.$index;
        ?>
            <li class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                <span class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                    <?php
                        echo '<input type="radio" '.
                                'name="'.$inputname.'[]" '.
                                'id="'.$inputid.'" '.
                                'value="'.$index.'" '.$checked.' />';
                    ?>
                </span>
                <span class="feedback_item_radiolabel_<?php echo $hv.'_'.$align;?>">
                    <label for="<?php echo $inputid;?>">
                        <?php echo format_text($radio, FORMAT_HTML, array('noclean' => true, 'para' => false));?>&nbsp;
                    </label>
                </span>
            </li>
        <?php
            $index++;
        }
    }

    private function print_item_check($presentation, $item, $value, $info, $align) {

        if (is_array($value)) {
            $values = $value;
        } else {
            $values = explode(FEEDBACK_MULTICHOICE_LINE_SEP, $value);
        }

        if ($info->horizontal) {
            $hv = 'h';
        } else {
            $hv = 'v';
        }

        $index = 1;
        $checked = '';
        foreach ($presentation as $check) {
            foreach ($values as $val) {
                if ($val == $index) {
                    $checked = 'checked="checked"';
                    break;
                } else {
                    $checked = '';
                }
            }
            $inputname = $item->typ. '_' . $item->id;
            $inputid = $item->typ. '_' . $item->id.'_'.$index;
        ?>
            <li class="feedback_item_check_<?php echo $hv.'_'.$align;?>">
                <span class="feedback_item_check_<?php echo $hv.'_'.$align;?>">
                    <?php
                        echo '<input type="checkbox" '.
                              'name="'.$inputname.'[]" '.
                              'id="'.$inputid.'" '.
                              'value="'.$index.'" '.$checked.' />';
                    ?>
                </span>
                <span class="feedback_item_radiolabel_<?php echo $hv.'_'.$align;?>">
                    <label for="<?php echo $inputid;?>">
                        <?php echo format_text($check, FORMAT_HTML, array('noclean' => true, 'para' => false));?>&nbsp;
                    </label>
                </span>
            </li>
        <?php
            $index++;
        }
    }

    private function print_item_dropdown($presentation, $item, $value, $info, $align) {
        if (is_array($value)) {
            $values = $value;
        } else {
            $values = array($value);
        }

        if ($info->horizontal) {
            $hv = 'h';
        } else {
            $hv = 'v';
        }

        ?>
        <div class="feedback_item_select_<?php echo $hv.'_'.$align;?>">
            <select  id="<?php echo $item->typ .'_' . $item->id;?>" name="<?php echo $item->typ .'_' . $item->id;?>[]" size="1">
                <option value="0">&nbsp;</option>
                <?php
                $index = 1;
                $selected = '';
                foreach ($presentation as $dropdown) {
                    foreach ($values as $val) {
                        if ($val == $index) {
                            $selected = 'selected="selected"';
                            break;
                        } else {
                            $selected = '';
                        }
                    }
                ?>
                    <option value="<?php echo $index;?>" <?php echo $selected;?>>
                        <?php echo format_text($dropdown, FORMAT_HTML, array('noclean' => true, 'para' => false));?>
                    </option>
                <?php
                    $index++;
                }
                ?>
            </select>
        </div>
        <?php
    }

    public function set_ignoreempty($item, $ignoreempty=true) {
        $item->options = str_replace(FEEDBACK_MULTICHOICE_IGNOREEMPTY, '', $item->options);
        if ($ignoreempty) {
            $item->options .= FEEDBACK_MULTICHOICE_IGNOREEMPTY;
        }
    }

    public function ignoreempty($item) {
        if (strstr($item->options, FEEDBACK_MULTICHOICE_IGNOREEMPTY)) {
            return true;
        }
        return false;
    }

    public function set_hidenoselect($item, $hidenoselect=true) {
        $item->options = str_replace(FEEDBACK_MULTICHOICE_HIDENOSELECT, '', $item->options);
        if ($hidenoselect) {
            $item->options .= FEEDBACK_MULTICHOICE_HIDENOSELECT;
        }
    }

    public function hidenoselect($item) {
        if (strstr($item->options, FEEDBACK_MULTICHOICE_HIDENOSELECT)) {
            return true;
        }
        return false;
    }

    public function can_switch_require() {
        return true;
    }

    public function value_type() {
        return PARAM_INT;
    }

    public function value_is_array() {
        return true;
    }

    public function clean_input_value($value) {
        return clean_param_array($value, $this->value_type());
    }
}
