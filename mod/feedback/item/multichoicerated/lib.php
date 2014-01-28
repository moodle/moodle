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

define('FEEDBACK_RADIORATED_ADJUST_SEP', '<<<<<');

define('FEEDBACK_MULTICHOICERATED_MAXCOUNT', 10); //count of possible items
define('FEEDBACK_MULTICHOICERATED_VALUE_SEP', '####');
define('FEEDBACK_MULTICHOICERATED_VALUE_SEP2', '/');
define('FEEDBACK_MULTICHOICERATED_TYPE_SEP', '>>>>>');
define('FEEDBACK_MULTICHOICERATED_LINE_SEP', '|');
define('FEEDBACK_MULTICHOICERATED_ADJUST_SEP', '<<<<<');
define('FEEDBACK_MULTICHOICERATED_IGNOREEMPTY', 'i');
define('FEEDBACK_MULTICHOICERATED_HIDENOSELECT', 'h');

class feedback_item_multichoicerated extends feedback_item_base {
    protected $type = "multichoicerated";
    private $commonparams;
    private $item_form;
    private $item;

    public function init() {

    }

    public function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('multichoicerated_form.php');

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

        $this->item_form = new feedback_multichoicerated_form('edit_item.php', $customdata);
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
        $analysed_item = array();
        $analysed_item[] = $item->typ;
        $analysed_item[] = $item->name;

        //die moeglichen Antworten extrahieren
        $info = $this->get_info($item);
        $lines = null;
        $lines = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        if (!is_array($lines)) {
            return null;
        }

        //die Werte holen
        $values = feedback_get_group_values($item, $groupid, $courseid, $this->ignoreempty($item));
        if (!$values) {
            return null;
        }
        //schleife ueber den Werten und ueber die Antwortmoeglichkeiten

        $analysed_answer = array();
        $sizeoflines = count($lines);
        for ($i = 1; $i <= $sizeoflines; $i++) {
            $item_values = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $lines[$i-1]);
            $ans = new stdClass();
            $ans->answertext = $item_values[1];
            $avg = 0.0;
            $anscount = 0;
            foreach ($values as $value) {
                //ist die Antwort gleich dem index der Antworten + 1?
                if ($value->value == $i) {
                    $avg += $item_values[0]; //erst alle Werte aufsummieren
                    $anscount++;
                }
            }
            $ans->answercount = $anscount;
            $ans->avg = doubleval($avg) / doubleval(count($values));
            $ans->value = $item_values[0];
            $ans->quotient = $ans->answercount / count($values);
            $analysed_answer[] = $ans;
        }
        $analysed_item[] = $analysed_answer;
        return $analysed_item;
    }

    public function get_printval($item, $value) {
        $printval = '';

        if (!isset($value->value)) {
            return $printval;
        }

        $info = $this->get_info($item);

        $presentation = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        $index = 1;
        foreach ($presentation as $pres) {
            if ($value->value == $index) {
                $item_label = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $pres);
                $printval = $item_label[1];
                break;
            }
            $index++;
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
            echo '<tr><th colspan="2" align="left">';
            echo $itemnr.'&nbsp;('.$item->label.') '.$analysed_item[1];
            echo '</th></tr>';
            $analysed_vals = $analysed_item[2];
            $pixnr = 0;
            $avg = 0.0;
            foreach ($analysed_vals as $val) {
                $intvalue = $pixnr % 10;
                $pix = $OUTPUT->pix_url('multichoice/' . $intvalue, 'feedback');
                $pixnr++;
                $pixwidth = intval($val->quotient * FEEDBACK_MAX_PIX_LENGTH);

                $avg += $val->avg;
                $quotient = number_format(($val->quotient * 100), 2, $sep_dec, $sep_thous);
                echo '<tr>';
                echo '<td align="left" valign="top">';
                echo '-&nbsp;&nbsp;'.trim($val->answertext).' ('.$val->value.'):</td>';
                echo '<td align="left" style="width: '.FEEDBACK_MAX_PIX_LENGTH.'">';
                echo '<img class="feedback_bar_image" alt="'.$intvalue.'" src="'.$pix.'" height="5" width="'.$pixwidth.'" />';
                echo $val->answercount;
                if ($val->quotient > 0) {
                    echo '&nbsp;('.$quotient.'&nbsp;%)';
                } else {
                    echo '';
                }
                echo '</td></tr>';
            }
            $avg = number_format(($avg), 2, $sep_dec, $sep_thous);
            echo '<tr><td align="left" colspan="2"><b>';
            echo get_string('average', 'feedback').': '.$avg.'</b>';
            echo '</td></tr>';
        }
    }

    public function excelprint_item(&$worksheet, $row_offset,
                             $xls_formats, $item,
                             $groupid, $courseid = false) {

        $analysed_item = $this->get_analysed($item, $groupid, $courseid);

        $data = $analysed_item[2];

        //write the item
        $worksheet->write_string($row_offset, 0, $item->label, $xls_formats->head2);
        $worksheet->write_string($row_offset, 1, $analysed_item[1], $xls_formats->head2);
        if (is_array($data)) {
            $avg = 0.0;
            $sizeofdata = count($data);
            for ($i = 0; $i < $sizeofdata; $i++) {
                $analysed_data = $data[$i];

                $worksheet->write_string($row_offset,
                                $i + 2,
                                trim($analysed_data->answertext).' ('.$analysed_data->value.')',
                                $xls_formats->value_bold);

                $worksheet->write_number($row_offset + 1,
                                $i + 2,
                                $analysed_data->answercount,
                                $xls_formats->default);

                $avg += $analysed_data->avg;
            }
            //mittelwert anzeigen
            $worksheet->write_string($row_offset,
                                count($data) + 2,
                                get_string('average', 'feedback'),
                                $xls_formats->value_bold);

            $worksheet->write_number($row_offset + 1,
                                count($data) + 2,
                                $avg,
                                $xls_formats->value_bold);
        }
        $row_offset +=2;
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
        $info = $this->get_info($item);
        $str_required_mark = '<span class="feedback_required_mark">*</span>';

        $lines = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        $requiredmark =  ($item->required == 1) ? $str_required_mark : '';
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
        switch($info->subtype) {
            case 'r':
                $this->print_item_radio($item, false, $info, $align, true, $lines);
                break;
            case 'd':
                $this->print_item_dropdown($item, false, $info, $align, true, $lines);
                break;
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
    public function print_item_complete($item, $value = '', $highlightrequire = false) {
        global $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';
        $info = $this->get_info($item);
        $str_required_mark = '<span class="feedback_required_mark">*</span>';

        $lines = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        $requiredmark =  ($item->required == 1) ? $str_required_mark : '';
        if ($highlightrequire AND $item->required AND intval($value) <= 0) {
            $highlight = ' missingrequire';
        } else {
            $highlight = '';
        }

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.$highlight.'">';
        if ($info->subtype == 'd') {
            echo '<label for="'. $item->typ . '_' . $item->id .'">';
            echo format_text($item->name . $requiredmark, FORMAT_HTML, array('noclean' => true, 'para' => false));
            echo '</label>';
        } else {
            echo format_text($item->name . $requiredmark, FORMAT_HTML, array('noclean' => true, 'para' => false));
        }
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.$highlight.'">';
        switch($info->subtype) {
            case 'r':
                $this->print_item_radio($item, $value, $info, $align, false, $lines);
                break;
            case 'd':
                $this->print_item_dropdown($item, $value, $info, $align, false, $lines);
                break;
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
    public function print_item_show_value($item, $value = '') {
        global $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';
        $info = $this->get_info($item);

        $lines = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        $requiredmark = ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
            echo '('.$item->label.') ';
            echo format_text($item->name . $requiredmark, FORMAT_HTML, array('noclean' => true, 'para' => false));
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
        $index = 1;
        foreach ($lines as $line) {
            if ($value == $index) {
                $item_value = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $line);
                echo $OUTPUT->box_start('generalbox boxalign'.$align);
                echo format_text($item_value[1], FORMAT_HTML, array('noclean' => true, 'para' => false));
                echo $OUTPUT->box_end();
                break;
            }
            $index++;
        }
        echo '</div>';
    }

    public function check_value($value, $item) {
        if ((!isset($value) OR $value == '' OR $value == 0) AND $item->required != 1) {
            return true;
        }
        if (intval($value) > 0) {
            return true;
        }
        return false;
    }

    public function create_value($data) {
        $data = trim($data);
        return $data;
    }

    //compares the dbvalue with the dependvalue
    //dbvalue is the number of one selection
    //dependvalue is the presentation of one selection
    public function compare_value($item, $dbvalue, $dependvalue) {

        if (is_array($dbvalue)) {
            $dbvalues = $dbvalue;
        } else {
            $dbvalues = explode(FEEDBACK_MULTICHOICERATED_LINE_SEP, $dbvalue);
        }

        $info = $this->get_info($item);
        $presentation = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        $index = 1;
        foreach ($presentation as $pres) {
            $presvalues = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $pres);

            foreach ($dbvalues as $dbval) {
                if ($dbval == $index AND trim($presvalues[1]) == $dependvalue) {
                    return true;
                }
            }
            $index++;
        }
        return false;
    }

    public function get_presentation($data) {
        $present = $this->prepare_presentation_values_save(trim($data->itemvalues),
                                            FEEDBACK_MULTICHOICERATED_VALUE_SEP2,
                                            FEEDBACK_MULTICHOICERATED_VALUE_SEP);
        if (!isset($data->subtype)) {
            $subtype = 'r';
        } else {
            $subtype = substr($data->subtype, 0, 1);
        }
        if (isset($data->horizontal) AND $data->horizontal == 1 AND $subtype != 'd') {
            $present .= FEEDBACK_MULTICHOICERATED_ADJUST_SEP.'1';
        }
        return $subtype.FEEDBACK_MULTICHOICERATED_TYPE_SEP.$present;
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

        $parts = explode(FEEDBACK_MULTICHOICERATED_TYPE_SEP, $item->presentation);
        @list($info->subtype, $info->presentation) = $parts;

        if (!isset($info->subtype)) {
            $info->subtype = 'r';
        }

        if ($info->subtype != 'd') {
            $parts = explode(FEEDBACK_MULTICHOICERATED_ADJUST_SEP, $info->presentation);
            @list($info->presentation, $info->horizontal) = $parts;

            if (isset($info->horizontal) AND $info->horizontal == 1) {
                $info->horizontal = true;
            } else {
                $info->horizontal = false;
            }
        }

        $info->values = $this->prepare_presentation_values_print($info->presentation,
                                                    FEEDBACK_MULTICHOICERATED_VALUE_SEP,
                                                    FEEDBACK_MULTICHOICERATED_VALUE_SEP2);
        return $info;
    }

    private function print_item_radio($item, $value, $info, $align, $showrating, $lines) {
        $index = 1;
        $checked = '';

        if ($info->horizontal) {
            $hv = 'h';
        } else {
            $hv = 'v';
        }
        echo '<fieldset>';
        echo '<ul>';
        if (!$this->hidenoselect($item)) {
            ?>
                <li class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                    <span class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                        <?php
                        echo '<input type="radio" '.
                                    'name="'.$item->typ.'_'.$item->id.'" '.
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
        foreach ($lines as $line) {
            if ($value == $index) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            $radio_value = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $line);
            $inputname = $item->typ . '_' . $item->id;
            $inputid = $inputname.'_'.$index;
        ?>
            <li class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                <span class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                <?php
                echo '<input type="radio" '.
                            'name="'.$inputname.'" '.
                            'id="'.$inputid.'" '.
                            'value="'.$index.'" '.$checked.' />';
                ?>
                </span>
                <span class="feedback_item_radiolabel_<?php echo $hv.'_'.$align;?>">
                    <label for="<?php echo $inputid;?>">
                        <?php
                            if ($showrating) {
                                $str_rating_value = '('.$radio_value[0].') '.$radio_value[1];
                                echo format_text($str_rating_value, FORMAT_HTML, array('noclean' => true, 'para' => false));
                            } else {
                                echo format_text($radio_value[1], FORMAT_HTML, array('noclean' => true, 'para' => false));
                            }
                        ?>
                    </label>
                </span>
            </li>
        <?php
            $index++;
        }
        echo '</ul>';
        echo '</fieldset>';
    }

    private function print_item_dropdown($item, $value, $info, $align, $showrating, $lines) {
        if ($info->horizontal) {
            $hv = 'h';
        } else {
            $hv = 'v';
        }
        ?>
        <div class="feedback_item_select_<?php echo $hv.'_'.$align;?>">
            <select id="<?php echo $item->typ.'_'.$item->id;?>" name="<?php echo $item->typ.'_'.$item->id;?>">
                <option value="0">&nbsp;</option>
                <?php
                $index = 1;
                $checked = '';
                foreach ($lines as $line) {
                    if ($value == $index) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    $dropdown_value = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $line);
                    if ($showrating) {
                        echo '<option value="'.$index.'" '.$selected.'>';
                        echo format_text('(' . $dropdown_value[0] . ') ' . $dropdown_value[1], FORMAT_HTML, array('para' => false));
                        echo '</option>';
                    } else {
                        echo '<option value="'.$index.'" '.$selected.'>';
                        echo format_text($dropdown_value[1], FORMAT_HTML, array('para' => false));
                        echo '</option>';
                    }
                    $index++;
                }
                ?>
            </select>
        </div>
        <?php
    }

    public function prepare_presentation_values($linesep1,
                                         $linesep2,
                                         $valuestring,
                                         $valuesep1,
                                         $valuesep2) {

        $lines = explode($linesep1, $valuestring);
        $newlines = array();
        foreach ($lines as $line) {
            $value = '';
            $text = '';
            if (strpos($line, $valuesep1) === false) {
                $value = 0;
                $text = $line;
            } else {
                @list($value, $text) = explode($valuesep1, $line, 2);
            }

            $value = intval($value);
            $newlines[] = $value.$valuesep2.$text;
        }
        $newlines = implode($linesep2, $newlines);
        return $newlines;
    }

    public function prepare_presentation_values_print($valuestring, $valuesep1, $valuesep2) {
        return $this->prepare_presentation_values(FEEDBACK_MULTICHOICERATED_LINE_SEP,
                                                  "\n",
                                                  $valuestring,
                                                  $valuesep1,
                                                  $valuesep2);
    }

    public function prepare_presentation_values_save($valuestring, $valuesep1, $valuesep2) {
        return $this->prepare_presentation_values("\n",
                        FEEDBACK_MULTICHOICERATED_LINE_SEP,
                        $valuestring,
                        $valuesep1,
                        $valuesep2);
    }

    public function set_ignoreempty($item, $ignoreempty=true) {
        $item->options = str_replace(FEEDBACK_MULTICHOICERATED_IGNOREEMPTY, '', $item->options);
        if ($ignoreempty) {
            $item->options .= FEEDBACK_MULTICHOICERATED_IGNOREEMPTY;
        }
    }

    public function ignoreempty($item) {
        if (strstr($item->options, FEEDBACK_MULTICHOICERATED_IGNOREEMPTY)) {
            return true;
        }
        return false;
    }

    public function set_hidenoselect($item, $hidenoselect=true) {
        $item->options = str_replace(FEEDBACK_MULTICHOICERATED_HIDENOSELECT, '', $item->options);
        if ($hidenoselect) {
            $item->options .= FEEDBACK_MULTICHOICERATED_HIDENOSELECT;
        }
    }

    public function hidenoselect($item) {
        if (strstr($item->options, FEEDBACK_MULTICHOICERATED_HIDENOSELECT)) {
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

    public function clean_input_value($value) {
        return clean_param($value, $this->value_type());
    }
}
