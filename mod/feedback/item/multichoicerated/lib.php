<?php
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
    var $type = "multichoicerated";
    var $commonparams;
    var $item_form;
    var $item;

    function init() {

    }

    function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('multichoicerated_form.php');

        //get the lastposition number of the feedback_items
        $position = $item->position;
        $lastposition = $DB->count_records('feedback_item', array('feedback'=>$feedback->id));
        if($position == -1){
            $i_formselect_last = $lastposition + 1;
            $i_formselect_value = $lastposition + 1;
            $item->position = $lastposition + 1;
        }else {
            $i_formselect_last = $lastposition;
            $i_formselect_value = $item->position;
        }
        //the elements for position dropdownlist
        $positionlist = array_slice(range(0,$i_formselect_last),1,$i_formselect_last,true);

        $item->presentation = empty($item->presentation) ? '' : $item->presentation;
        $info = $this->get_info($item);

        $item->ignoreempty = $this->ignoreempty($item);
        $item->hidenoselect = $this->hidenoselect($item);

        //all items for dependitem
        $feedbackitems = feedback_get_depend_candidates_for_item($feedback, $item);
        $commonparams = array('cmid'=>$cm->id,
                             'id'=>isset($item->id) ? $item->id : NULL,
                             'typ'=>$item->typ,
                             'items'=>$feedbackitems,
                             'feedback'=>$feedback->id);

        //build the form
        $this->item_form = new feedback_multichoicerated_form('edit_item.php', array('item'=>$item, 'common'=>$commonparams, 'positionlist'=>$positionlist, 'position'=>$position, 'info'=>$info));
    }

    //this function only can used after the call of build_editform()
    function show_editform() {
        $this->item_form->display();
    }

    function is_cancelled() {
        return $this->item_form->is_cancelled();
    }

    function get_data() {
        if($this->item = $this->item_form->get_data()) {
            return true;
        }
        return false;
    }

    function save_item() {
        global $DB;

        if(!$item = $this->item_form->get_data()) {
            return false;
        }

        if(isset($item->clone_item) AND $item->clone_item) {
            $item->id = ''; //to clone this item
            $item->position++;
        }

        $this->set_ignoreempty($item, $item->ignoreempty);
        $this->set_hidenoselect($item, $item->hidenoselect);

        $item->hasvalue = $this->get_hasvalue();
        if(!$item->id) {
            $item->id = $DB->insert_record('feedback_item', $item);
        }else {
            $DB->update_record('feedback_item', $item);
        }

        return $DB->get_record('feedback_item', array('id'=>$item->id));
    }


    //liefert ein eindimensionales Array mit drei Werten(typ, name, XXX)
    //XXX ist ein eindimensionales Array (Mittelwert der Werte der Antworten bei Typ Radio_rated) Jedes Element ist eine Struktur (answertext, avg)
    function get_analysed($item, $groupid = false, $courseid = false) {
        $analysedItem = array();
        $analysedItem[] = $item->typ;
        $analysedItem[] = $item->name;

        //die moeglichen Antworten extrahieren
        $info = $this->get_info($item);
        $lines = null;
        $lines = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        if(!is_array($lines)) return null;

        //die Werte holen
        $values = feedback_get_group_values($item, $groupid, $courseid, $this->ignoreempty($item));
        if(!$values) return null;
        //schleife ueber den Werten und ueber die Antwortmoeglichkeiten

        $analysedAnswer = array();
        $sizeoflines = sizeof($lines);
        for($i = 1; $i <= $sizeoflines; $i++) {
            $item_values = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $lines[$i-1]);
            $ans = null;
            $ans->answertext = $item_values[1];
            $avg = 0.0;
            $anscount = 0;
            foreach($values as $value) {
                //ist die Antwort gleich dem index der Antworten + 1?
                if ($value->value == $i) {
                    $avg += $item_values[0]; //erst alle Werte aufsummieren
                    $anscount++;
                }
            }
            $ans->answercount = $anscount;
            $ans->avg = doubleval($avg) / doubleval(sizeof($values));
            $ans->value = $item_values[0];
            $ans->quotient = $ans->answercount / sizeof($values);
            $analysedAnswer[] = $ans;
        }
        $analysedItem[] = $analysedAnswer;
        return $analysedItem;
    }

    function get_printval($item, $value) {
        $printval = '';

        if(!isset($value->value)) return $printval;

        $info = $this->get_info($item);

        $presentation = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        $index = 1;
        foreach($presentation as $pres){
            if($value->value == $index){
                $item_label = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $pres);
                $printval = $item_label[1];
                break;
            }
            $index++;
        }
        return $printval;
    }

    function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {
        global $OUTPUT;
        $sep_dec = get_string('separator_decimal', 'feedback');
        if(substr($sep_dec, 0, 2) == '[['){
            $sep_dec = FEEDBACK_DECIMAL;
        }

        $sep_thous = get_string('separator_thousand', 'feedback');
        if(substr($sep_thous, 0, 2) == '[['){
            $sep_thous = FEEDBACK_THOUSAND;
        }

        $analysedItem = $this->get_analysed($item, $groupid, $courseid);
        if($analysedItem) {
            //echo '<table>';
            // $itemnr++;
            echo '<tr><th colspan="2" align="left">'. $itemnr . '&nbsp;('. $item->label .') ' . $analysedItem[1] .'</th></tr>';
            $analysedVals = $analysedItem[2];
            $pixnr = 0;
            $avg = 0.0;
            foreach($analysedVals as $val) {
                $intvalue = $pixnr % 10;
                $pix = $OUTPUT->pix_url('multichoice/' . $intvalue, 'feedback');
                $pixnr++;
                $pixwidth = intval($val->quotient * FEEDBACK_MAX_PIX_LENGTH);

                $avg += $val->avg;
                $quotient = number_format(($val->quotient * 100), 2, $sep_dec, $sep_thous);
                echo '<tr><td align="left" valign="top">-&nbsp;&nbsp;' . trim($val->answertext) . ' ('.$val->value.'):</td><td align="left" style="width: '.FEEDBACK_MAX_PIX_LENGTH.'"><img alt="'.$intvalue.'" src="'.$pix.'" height="5" width="'.$pixwidth.'" />' . $val->answercount. (($val->quotient > 0)?'&nbsp;('. $quotient . '&nbsp;%)':'') . '</td></tr>';
            }
            $avg = number_format(($avg), 2, $sep_dec, $sep_thous);
            echo '<tr><td align="left" colspan="2"><b>'.get_string('average', 'feedback').': '.$avg.'</b></td></tr>';
            //echo '</table>';
        }
        // return $itemnr;
    }

    function excelprint_item(&$worksheet, $rowOffset, $xlsFormats, $item, $groupid, $courseid = false) {
        $analysed_item = $this->get_analysed($item, $groupid, $courseid);

        $data = $analysed_item[2];

        // $worksheet->setFormat("<l><f><ro2><vo><c:green>");
        //frage schreiben
        $worksheet->write_string($rowOffset, 0, $item->label, $xlsFormats->head2);
        $worksheet->write_string($rowOffset, 1, $analysed_item[1], $xlsFormats->head2);
        if(is_array($data)) {
            $avg = 0.0;
            $sizeofdata = sizeof($data);
            for($i = 0; $i < $sizeofdata; $i++) {
                $aData = $data[$i];

                // $worksheet->setFormat("<l><f><ro2><vo><c:blue>");
                $worksheet->write_string($rowOffset, $i + 2, trim($aData->answertext).' ('.$aData->value.')', $xlsFormats->value_bold);

                // $worksheet->setFormat("<l><vo>");
                $worksheet->write_number($rowOffset + 1, $i + 2, $aData->answercount, $xlsFormats->default);
                //$worksheet->setFormat("<l><f><vo>");
                //$worksheet->write_number($rowOffset + 2, $i + 1, $aData->avg);
                $avg += $aData->avg;
            }
            //mittelwert anzeigen
            // $worksheet->setFormat("<l><f><ro2><vo><c:red>");
            $worksheet->write_string($rowOffset, sizeof($data) + 2, get_string('average', 'feedback'), $xlsFormats->value_bold);

            // $worksheet->setFormat("<l><f><vo>");
            $worksheet->write_number($rowOffset + 1, sizeof($data) + 2, $avg, $xlsFormats->value_bold);
        }
        $rowOffset +=2 ;
        return $rowOffset;
    }

    /**
     * print the item at the edit-page of feedback
     *
     * @global object
     * @param object $item
     * @return void
     */
    function print_item_preview($item) {
        global $OUTPUT, $DB;

        $align = right_to_left() ? 'right' : 'left';
        $info = $this->get_info($item);

        $lines = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
        echo '('.$item->label.') ';
        echo format_text($item->name.$requiredmark, true, false, false);
        if($item->dependitem) {
            if($dependitem = $DB->get_record('feedback_item', array('id'=>$item->dependitem))) {
                echo ' <span class="feedback_depend">('.$dependitem->label.'-&gt;'.$item->dependvalue.')</span>';
            }
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
    function print_item_complete($item, $value = '', $highlightrequire = false) {
        global $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';
        $info = $this->get_info($item);

        $lines = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        if($highlightrequire AND $item->required AND intval($value) <= 0) {
            $highlight = ' missingrequire';
        }else {
            $highlight = '';
        }

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.$highlight.'">';
            echo format_text($item->name.$requiredmark, true, false, false);
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
    function print_item_show_value($item, $value = '') {
        global $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';
        $info = $this->get_info($item);

        $lines = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
            echo '('.$item->label.') ';
            echo format_text($item->name . $requiredmark, true, false, false);
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
        $index = 1;
        foreach($lines as $line){
            if($value == $index){
                $item_value = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $line);
                echo $OUTPUT->box_start('generalbox boxalign'.$align);
                echo text_to_html($item_value[1], true, false, false);
                echo $OUTPUT->box_end();
                break;
            }
            $index++;
        }
        echo '</div>';
    }

    function check_value($value, $item) {
        if((!isset($value) OR $value == '' OR $value == 0) AND $item->required != 1) return true;
        if(intval($value) > 0)return true;
        return false;
    }

    function create_value($data) {
        $data = trim($data);
        return $data;
    }

    //compares the dbvalue with the dependvalue
    //dbvalue is the number of one selection
    //dependvalue is the presentation of one selection
    function compare_value($item, $dbvalue, $dependvalue) {

        if (is_array($dbvalue)) {
            $dbvalues = $dbvalue;
        }else {
            $dbvalues = explode(FEEDBACK_MULTICHOICERATED_LINE_SEP, $dbvalue);
        }

        $info = $this->get_info($item);
        $presentation = explode (FEEDBACK_MULTICHOICERATED_LINE_SEP, $info->presentation);
        $index = 1;
        foreach($presentation as $pres) {
            $presvalues = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $pres);

            foreach($dbvalues as $dbval) {
                if($dbval == $index AND trim($presvalues[1]) == $dependvalue) {
                    return true;
                }
            }
            $index++;
        }
        return false;
    }
    function get_presentation($data) {
        // $present = str_replace("\n", FEEDBACK_MULTICHOICERATED_LINE_SEP, trim($data->itemvalues));
        $present = $this->prepare_presentation_values_save(trim($data->itemvalues), FEEDBACK_MULTICHOICERATED_VALUE_SEP2, FEEDBACK_MULTICHOICERATED_VALUE_SEP);
        // $present = str_replace("\n", FEEDBACK_MULTICHOICERATED_LINE_SEP, trim($data->itemvalues));
        if(!isset($data->subtype)) {
            $subtype = 'r';
        }else {
            $subtype = substr($data->subtype, 0, 1);
        }
        if(isset($data->horizontal) AND $data->horizontal == 1 AND $subtype != 'd') {
            $present .= FEEDBACK_MULTICHOICERATED_ADJUST_SEP.'1';
        }
        return $subtype.FEEDBACK_MULTICHOICERATED_TYPE_SEP.$present;
    }

    function get_hasvalue() {
        return 1;
    }

    function get_info($item) {
        $presentation = empty($item->presentation) ? '' : $item->presentation;

        $info = new stdClass();
        //check the subtype of the multichoice
        //it can be check(c), radio(r) or dropdown(d)
        $info->subtype = '';
        $info->presentation = '';
        $info->horizontal = false;

        @list($info->subtype, $info->presentation) = explode(FEEDBACK_MULTICHOICERATED_TYPE_SEP, $item->presentation);

        if(!isset($info->subtype)) {
            $info->subtype = 'r';
        }


        if($info->subtype != 'd') {
            @list($info->presentation, $info->horizontal) = explode(FEEDBACK_MULTICHOICERATED_ADJUST_SEP, $info->presentation);

            if(isset($info->horizontal) AND $info->horizontal == 1) {
                $info->horizontal = true;
            }else {
                $info->horizontal = false;
            }
        }

        $info->values = $this->prepare_presentation_values_print($info->presentation, FEEDBACK_MULTICHOICERATED_VALUE_SEP, FEEDBACK_MULTICHOICERATED_VALUE_SEP2);
        return $info;
    }

    function print_item_radio($item, $value, $info, $align, $showrating, $lines) {
        $index = 1;
        $checked = '';

        if($info->horizontal) {
            $hv = 'h';
        }else {
            $hv = 'v';
        }
        echo '<ul>';
        if(!$this->hidenoselect($item)) {
            ?>
                <li class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                    <span class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                        <input type="radio" name="<?php echo $item->typ . '_' . $item->id ;?>" id="<?php echo $item->typ . '_' . $item->id.'_xxx';?>" value="" checked="checked" />
                    </span>
                    <span class="feedback_item_radiolabel_<?php echo $hv.'_'.$align;?>">
                        <label for="<?php echo $item->typ . '_' . $item->id.'_xxx';?>"><?php print_string('not_selected', 'feedback');?>&nbsp;</label>
                    </span>
                </li>
            <?php
        }
        foreach($lines as $line){
            if($value == $index){
                $checked = 'checked="checked"';
            }else{
                $checked = '';
            }
            $radio_value = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $line);
            $inputname = $item->typ . '_' . $item->id;
            $inputid = $inputname.'_'.$index;
        ?>
            <li class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                <span class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                    <input type="radio" name="<?php echo $inputname;?>" id="<?php echo $inputid;?>" value="<?php echo $index;?>" <?php echo $checked;?> />
                </span>
                <span class="feedback_item_radiolabel_<?php echo $hv.'_'.$align;?>">
                    <label for="<?php echo $inputid;?>">
                        <?php
                            if($showrating) {
                                echo text_to_html('('.$radio_value[0].') '.$radio_value[1], true, false, false);
                            }else {
                                echo text_to_html($radio_value[1], true, false, false);
                            }
                        ?>
                    </label>
                </span>
            </li>
        <?php
            $index++;
        }
        echo '</ul>';
    }

    function print_item_dropdown($item, $value, $info, $align, $showrating, $lines) {
        if($info->horizontal) {
            $hv = 'h';
        }else {
            $hv = 'v';
        }
        echo '<ul>';
        ?>
        <li class="feedback_item_select_<?php echo $hv.'_'.$align;?>">
            <select name="<?php echo $item->typ.'_'.$item->id;?>">
                <option value="0">&nbsp;</option>
                <?php
                $index = 1;
                $checked = '';
                foreach($lines as $line){
                    if($value == $index){
                        $selected = 'selected="selected"';
                    }else{
                        $selected = '';
                    }
                    $dropdown_value = explode(FEEDBACK_MULTICHOICERATED_VALUE_SEP, $line);
                    if($showrating) {
                        echo '<option value="'.$index.'" '.$selected.'>'.clean_text('('.$dropdown_value[0].') '.$dropdown_value[1]).'</option>';
                    }else {
                        echo '<option value="'.$index.'" '.$selected.'>'.clean_text($dropdown_value[1]).'</option>';
                    }
                    $index++;
                }
                ?>
            </select>
        </li>
        <?php
        echo '</ul>';
    }

    function prepare_presentation_values($linesep1, $linesep2, $valuestring, $valuesep1, $valuesep2) {
        $lines = explode($linesep1, $valuestring);
        $newlines = array();
        foreach($lines as $line) {
            $value = '';
            $text = '';
            if(strpos($line, $valuesep1) === false) {
                $value = 0;
                $text = $line;
            }else {
                @list($value, $text) = explode($valuesep1, $line, 2);
            }

            $value = intval($value);
            $newlines[] = $value.$valuesep2.$text;
        }
        $newlines = implode($linesep2, $newlines);
        return $newlines;
    }

    function prepare_presentation_values_print($valuestring, $valuesep1, $valuesep2) {
        return $this->prepare_presentation_values(FEEDBACK_MULTICHOICERATED_LINE_SEP, "\n", $valuestring, $valuesep1, $valuesep2);
    }

    function prepare_presentation_values_save($valuestring, $valuesep1, $valuesep2) {
        return $this->prepare_presentation_values("\n", FEEDBACK_MULTICHOICERATED_LINE_SEP, $valuestring, $valuesep1, $valuesep2);
    }

    function set_ignoreempty($item, $ignoreempty=true) {
        $item->options = str_replace(FEEDBACK_MULTICHOICERATED_IGNOREEMPTY, '', $item->options);
        if($ignoreempty) {
            $item->options .= FEEDBACK_MULTICHOICERATED_IGNOREEMPTY;
        }
    }

    function ignoreempty($item) {
        if(strstr($item->options, FEEDBACK_MULTICHOICERATED_IGNOREEMPTY)) {
            return true;
        }
        return false;
    }

    function set_hidenoselect($item, $hidenoselect=true) {
        $item->options = str_replace(FEEDBACK_MULTICHOICERATED_HIDENOSELECT, '', $item->options);
        if($hidenoselect) {
            $item->options .= FEEDBACK_MULTICHOICERATED_HIDENOSELECT;
        }
    }

    function hidenoselect($item) {
        if(strstr($item->options, FEEDBACK_MULTICHOICERATED_HIDENOSELECT)) {
            return true;
        }
        return false;
    }

    function can_switch_require() {
        return true;
    }

}
