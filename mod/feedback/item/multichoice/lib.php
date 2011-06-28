<?php
defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

define('FEEDBACK_MULTICHOICE_TYPE_SEP', '>>>>>');
define('FEEDBACK_MULTICHOICE_LINE_SEP', '|');
define('FEEDBACK_MULTICHOICE_ADJUST_SEP', '<<<<<');
define('FEEDBACK_MULTICHOICE_IGNOREEMPTY', 'i');
define('FEEDBACK_MULTICHOICE_HIDENOSELECT', 'h');

class feedback_item_multichoice extends feedback_item_base {
    var $type = "multichoice";
    var $commonparams;
    var $item_form;
    var $item;

    function init() {

    }

    function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('multichoice_form.php');

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
        $this->item_form = new feedback_multichoice_form('edit_item.php', array('item'=>$item, 'common'=>$commonparams, 'positionlist'=>$positionlist, 'position'=>$position, 'info'=>$info));
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
    //XXX ist ein eindimensionales Array (anzahl der Antworten bei Typ Radio) Jedes Element ist eine Struktur (answertext, answercount)
    function get_analysed($item, $groupid = false, $courseid = false) {
        $info = $this->get_info($item);

        $analysedItem = array();
        $analysedItem[] = $item->typ;
        $analysedItem[] = $item->name;
        //die moeglichen Antworten extrahieren
        $answers = null;
        // $presentation = '';
        // @list($presentation) = explode(FEEDBACK_RADIO_ADJUST_SEP, $item->presentation); //remove the adjustment-info

        $answers = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);
        if(!is_array($answers)) return null;

        //die Werte holen
        $values = feedback_get_group_values($item, $groupid, $courseid, $this->ignoreempty($item));
        if(!$values) return null;
        //schleife ueber den Werten und ueber die Antwortmoeglichkeiten

        $analysedAnswer = array();
        if($info->subtype == 'c') {
            $sizeofanswers = sizeof($answers);
            for ($i = 1; $i <= $sizeofanswers; $i++) {
                $ans = null;
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
                $ans->quotient = $ans->answercount / sizeof($values);
                $analysedAnswer[] = $ans;
            }
        }else {
            $sizeofanswers = sizeof($answers);
            for ($i = 1; $i <= $sizeofanswers; $i++) {
                $ans = null;
                $ans->answertext = $answers[$i-1];
                $ans->answercount = 0;
                foreach ($values as $value) {
                    //ist die Antwort gleich dem index der Antworten + 1?
                    if ($value->value == $i) {
                        $ans->answercount++;
                    }
                }
                $ans->quotient = $ans->answercount / sizeof($values);
                $analysedAnswer[] = $ans;
            }
        }
        $analysedItem[] = $analysedAnswer;
        return $analysedItem;
    }

    function get_printval($item, $value) {
        $info = $this->get_info($item);

        $printval = '';

        if (!isset($value->value)) {
            return $printval;
        }

        // @list($presentation) = explode(FEEDBACK_RADIO_ADJUST_SEP, $item->presentation); //remove the adjustment-info

        $presentation = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);

        if ($info->subtype == 'c') {
            $vallist = array_values(explode (FEEDBACK_MULTICHOICE_LINE_SEP, $value->value));
            $sizeofvallist = sizeof($vallist);
            $sizeofpresentation = sizeof($presentation);
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
            foreach($presentation as $pres){
                if ($value->value == $index){
                    $printval = $pres;
                    break;
                }
                $index++;
            }
        }
        return $printval;
    }

    function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {
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
            // $itemnr++;
            $itemname = $analysedItem[1];
            echo '<tr><th colspan="2" align="left">'. $itemnr . '&nbsp;('. $item->label .') ' . $itemname .'</th></tr>';
            $analysedVals = $analysedItem[2];
            $pixnr = 0;
            foreach($analysedVals as $val) {
                $intvalue = $pixnr % 10;
                $pix = "pics/$intvalue.gif";
                $pixnr++;
                $pixwidth = intval($val->quotient * FEEDBACK_MAX_PIX_LENGTH);
                $quotient = number_format(($val->quotient * 100), 2, $sep_dec, $sep_thous);
                echo '<tr><td align="left" valign="top">-&nbsp;&nbsp;' . trim($val->answertext) . ':</td><td align="left" style="width: '.FEEDBACK_MAX_PIX_LENGTH.'"><img alt="'.$intvalue.'" src="'.$pix.'" height="5" width="'.$pixwidth.'" />&nbsp;' . $val->answercount . (($val->quotient > 0)?'&nbsp;('. $quotient . '&nbsp;%)':'').'</td></tr>';
            }
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
        if (is_array($data)) {
            $sizeofdata = sizeof($data);
            for ($i = 0; $i < $sizeofdata; $i++) {
                $aData = $data[$i];

                // $worksheet->setFormat("<l><f><ro2><vo><c:blue>");
                $worksheet->write_string($rowOffset, $i + 2, trim($aData->answertext), $xlsFormats->head2);

                // $worksheet->setFormat("<l><vo>");
                $worksheet->write_number($rowOffset + 1, $i + 2, $aData->answercount, $xlsFormats->default);
                // $worksheet->setFormat("<l><f><vo><pr>");
                $worksheet->write_number($rowOffset + 2, $i + 2, $aData->quotient, $xlsFormats->procent);
            }
        }
        $rowOffset += 3;
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
        $info = $this->get_info($item);
        $align = right_to_left() ? 'right' : 'left';

        $presentation = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);


        //test if required and no value is set so we have to mark this item
        //we have to differ check and the other subtypes
        $requiredmark =  ($item->required == 1) ? '<span class="feedback_required_mark">*</span>' : '';

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
        $index = 1;
        $checked = '';
        echo '<ul>';
        if($info->horizontal) {
            $hv = 'h';
        }else {
            $hv = 'v';
        }

        if($info->subtype == 'r' AND !$this->hidenoselect($item)) {
        //print the "not_selected" item on radiobuttons
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
        echo '</ul>';
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
        $info = $this->get_info($item);
        $align = right_to_left() ? 'right' : 'left';

        $presentation = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);


        //test if required and no value is set so we have to mark this item
        //we have to differ check and the other subtypes
        if($info->subtype == 'c') {
            if (is_array($value)) {
                $values = $value;
            }else {
                $values = explode(FEEDBACK_MULTICHOICE_LINE_SEP, $value);
            }
            if($highlightrequire AND $item->required AND $values[0] == '') {
                $highlight = ' missingrequire';
            }else {
                $highlight = '';
            }
            $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        }else {
            if($highlightrequire AND $item->required AND intval($value) <= 0) {
                $highlight = ' missingrequire';
            }else {
                $highlight = '';
            }
            $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        }

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.$highlight.'">';
            echo format_text($item->name.$requiredmark, true, false, false);
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.$highlight.'">';

        echo '<ul>';
        if($info->horizontal) {
            $hv = 'h';
        }else {
            $hv = 'v';
        }
        //print the "not_selected" item on radiobuttons
        if($info->subtype == 'r' AND !$this->hidenoselect($item)) {
        ?>
            <li class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                <span class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                    <input type="radio" name="<?php echo $item->typ.'_'.$item->id ;?>" id="<?php echo $item->typ . '_' . $item->id.'_xxx';?>" value="" <?php echo $value ? '' : 'checked="checked"';?> />
                </span>
                <span class="feedback_item_radiolabel_<?php echo $hv.'_'.$align;?>">
                    <label for="<?php echo $item->typ.'_'.$item->id.'_xxx';?>"><?php print_string('not_selected', 'feedback');?>&nbsp;</label>
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
        echo '</ul>';
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
        $info = $this->get_info($item);
        $align = right_to_left() ? 'right' : 'left';

        $presentation = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);


        //test if required and no value is set so we have to mark this item
        //we have to differ check and the other subtypes
        if($info->subtype == 'c') {
            if (is_array($value)) {
                $values = $value;
            }else {
                $values = explode(FEEDBACK_MULTICHOICE_LINE_SEP, $value);
            }
            $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        }else {
            $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        }

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
        echo '('.$item->label.') ';
        echo format_text($item->name . $requiredmark, true, false, false);
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
        $index = 1;
        if($info->subtype == 'c') {
            echo $OUTPUT->box_start('generalbox boxalign'.$align);
            foreach($presentation as $pres){
                foreach($values as $val) {
                    if($val == $index){
                        echo '<div class="feedback_item_multianswer">';
                        echo text_to_html($pres, true, false, false);
                        echo '</div>';
                        break;
                    }
                }
                $index++;
            }
            echo $OUTPUT->box_end();
        }else {
            foreach($presentation as $pres){
                if($value == $index){
                    echo $OUTPUT->box_start('generalbox boxalign'.$align);
                    echo text_to_html($pres, true, false, false);
                    echo $OUTPUT->box_end();
                    break;
                }
                $index++;
            }
        }
        echo '</div>';
    }

    function check_value($value, $item) {
        $info = $this->get_info($item);

        if($info->subtype == 'c') {
            if((!isset($value) OR !is_array($value) OR $value[0] == '' OR $value[0] == 0) AND $item->required != 1){
                return true;
            }
            if($value[0] == ""){
                return false;
            }
            return true;
        }else {
            //if the item is not required, so the check is true if no value is given
            if((!isset($value) OR $value == '' OR $value == 0) AND $item->required != 1) return true;
            if(intval($value) > 0)return true;
        }
        return false;
    }

    function create_value($data) {
        $vallist = $data;
        return trim($this->item_arrayToString($vallist));
    }

    //compares the dbvalue with the dependvalue
    //dbvalue is the number of one selection
    //dependvalue is the presentation of one selection
    function compare_value($item, $dbvalue, $dependvalue) {

        if (is_array($dbvalue)) {
            $dbvalues = $dbvalue;
        }else {
            $dbvalues = explode(FEEDBACK_MULTICHOICE_LINE_SEP, $dbvalue);
        }

        $info = $this->get_info($item);
        $presentation = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);
        $index = 1;
        foreach($presentation as $pres) {
            foreach($dbvalues as $dbval) {
                if($dbval == $index AND trim($pres) == $dependvalue) {
                    return true;
                }
            }
            $index++;
        }
        return false;
    }

    function get_presentation($data) {
        $present = str_replace("\n", FEEDBACK_MULTICHOICE_LINE_SEP, trim($data->itemvalues));
        if(!isset($data->subtype)) {
            $subtype = 'r';
        }else {
            $subtype = substr($data->subtype, 0, 1);
        }
        if(isset($data->horizontal) AND $data->horizontal == 1 AND $subtype != 'd') {
            $present .= FEEDBACK_MULTICHOICE_ADJUST_SEP.'1';
        }
        return $subtype.FEEDBACK_MULTICHOICE_TYPE_SEP.$present;
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

        @list($info->subtype, $info->presentation) = explode(FEEDBACK_MULTICHOICE_TYPE_SEP, $item->presentation);
        if(!isset($info->subtype)) {
            $info->subtype = 'r';
        }

        if($info->subtype != 'd') {
            @list($info->presentation, $info->horizontal) = explode(FEEDBACK_MULTICHOICE_ADJUST_SEP, $info->presentation);
            if(isset($info->horizontal) AND $info->horizontal == 1) {
                $info->horizontal = true;
            }else {
                $info->horizontal = false;
            }
        }
        return $info;
    }

    function item_arrayToString($value) {
        if (!is_array($value)) {
            return $value;
        }
        $retval = '';
        $arrvals = array_values($value);
        $arrvals = clean_param($arrvals, PARAM_INT);  //prevent sql-injection
        $retval = $arrvals[0];
        $sizeofarrvals = sizeof($arrvals);
        for ($i = 1; $i < $sizeofarrvals; $i++) {
            $retval .= FEEDBACK_MULTICHOICE_LINE_SEP.$arrvals[$i];
        }
        return $retval;
    }

    function print_item_radio($presentation, $item, $value, $info, $align) {
        $index = 1;
        $checked = '';

        if($info->horizontal) {
            $hv = 'h';
        }else {
            $hv = 'v';
        }

        foreach($presentation as $radio){
            if($value == $index){
                $checked = 'checked="checked"';
            }else{
                $checked = '';
            }
            $inputname = $item->typ . '_' . $item->id;
            $inputid = $inputname.'_'.$index;
        ?>
            <li class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                <span class="feedback_item_radio_<?php echo $hv.'_'.$align;?>">
                    <input type="radio" name="<?php echo $inputname;?>" id="<?php echo $inputid;?>" value="<?php echo $index;?>" <?php echo $checked;?> />
                </span>
                <span class="feedback_item_radiolabel_<?php echo $hv.'_'.$align;?>">
                    <label for="<?php echo $inputid;?>"><?php echo text_to_html($radio, true, false, false);?>&nbsp;</label>
                </span>
            </li>
        <?php
            $index++;
        }
    }

    function print_item_check($presentation, $item, $value, $info, $align) {

        if (is_array($value)) {
            $values = $value;
        }else {
            $values = explode(FEEDBACK_MULTICHOICE_LINE_SEP, $value);
        }

        if($info->horizontal) {
            $hv = 'h';
        }else {
            $hv = 'v';
        }

        $index = 1;
        $checked = '';
        foreach($presentation as $check){
            foreach($values as $val) {
                if($val == $index){
                    $checked = 'checked="checked"';
                    break;
                }else{
                    $checked = '';
                }
            }
            $inputname = $item->typ. '_' . $item->id;
            $inputid = $item->typ. '_' . $item->id.'_'.$index;
        ?>
            <li class="feedback_item_check_<?php echo $hv.'_'.$align;?>">
                <span class="feedback_item_check_<?php echo $hv.'_'.$align;?>">
                    <input type="checkbox" name="<?php echo $inputname;?>[]" id="<?php echo $inputid;?>" value="<?php echo $index;?>" <?php echo $checked;?> />
                </span>
                <span class="feedback_item_radiolabel_<?php echo $hv.'_'.$align;?>">
                    <label for="<?php echo $inputid;?>"><?php echo text_to_html($check, true, false, false);?>&nbsp;</label>
                </span>
            </li>
        <?php
            $index++;
        }
    }

    function print_item_dropdown($presentation, $item, $value, $info, $align) {
        if($info->horizontal) {
            $hv = 'h';
        }else {
            $hv = 'v';
        }

        ?>
        <li class="feedback_item_select_<?php echo $hv.'_'.$align;?>">
            <select name="<?php echo $item->typ .'_' . $item->id;?>" size="1">
                <option value="0">&nbsp;</option>
                <?php
                $index = 1;
                $checked = '';
                foreach($presentation as $dropdown){
                    if($value == $index){
                        $selected = 'selected="selected"';
                    }else{
                        $selected = '';
                    }
                ?>
                    <option value="<?php echo $index;?>" <?php echo $selected;?>><?php echo text_to_html($dropdown, true, false, false);?></option>
                <?php
                    $index++;
                }
                ?>
            </select>
        </li>
        <?php
    }

    function set_ignoreempty($item, $ignoreempty=true) {
        $item->options = str_replace(FEEDBACK_MULTICHOICE_IGNOREEMPTY, '', $item->options);
        if($ignoreempty) {
            $item->options .= FEEDBACK_MULTICHOICE_IGNOREEMPTY;
        }
    }

    function ignoreempty($item) {
        if(strstr($item->options, FEEDBACK_MULTICHOICE_IGNOREEMPTY)) {
            return true;
        }
        return false;
    }

    function set_hidenoselect($item, $hidenoselect=true) {
        $item->options = str_replace(FEEDBACK_MULTICHOICE_HIDENOSELECT, '', $item->options);
        if($hidenoselect) {
            $item->options .= FEEDBACK_MULTICHOICE_HIDENOSELECT;
        }
    }

    function hidenoselect($item) {
        if(strstr($item->options, FEEDBACK_MULTICHOICE_HIDENOSELECT)) {
            return true;
        }
        return false;
    }


    function can_switch_require() {
        return true;
    }
}
