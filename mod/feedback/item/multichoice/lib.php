<?php
defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

define('FEEDBACK_MULTICHOICE_TYPE_SEP', '>>>>>');
define('FEEDBACK_MULTICHOICE_LINE_SEP', '|');
define('FEEDBACK_MULTICHOICE_ADJUST_SEP', '<<<<<');

class feedback_item_multichoice extends feedback_item_base {
    var $type = "multichoice";
    function init() {

    }

    function show_edit($item, $commonparams, $positionlist, $position) {
        global $CFG;

        require_once('multichoice_form.php');

        $item_form = new feedback_multichoice_form('edit_item.php', array('item'=>$item, 'common'=>$commonparams, 'positionlist'=>$positionlist, 'position'=>$position));

        $item->presentation = empty($item->presentation) ? '' : $item->presentation;
        $item->name = empty($item->name) ? '' : $item->name;
        $item->label = empty($item->label) ? '' : $item->label;

        $info = $this->get_info($item);

        $item->required = isset($item->required) ? $item->required : 0;
        if($item->required) {
            $item_form->requiredcheck->setValue(true);
        }

        $item_form->itemname->setValue($item->name);
        $item_form->itemlabel->setValue($item->label);

        $item_form->selectadjust->setValue($info->horizontal);

        $item_form->selecttype->setValue($info->subtype);

        $itemvalues = str_replace(FEEDBACK_MULTICHOICE_LINE_SEP, "\n", $info->presentation);
        $itemvalues = str_replace("\n\n", "\n", $itemvalues);
        $item_form->values->setValue($itemvalues);
        return $item_form;
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
        $values = feedback_get_group_values($item, $groupid, $courseid);
        if(!$values) return null;
        //schleife ueber den Werten und ueber die Antwortmoeglichkeiten

        $analysedAnswer = array();
        if($info->subtype == 'c') {
            for($i = 1; $i <= sizeof($answers); $i++) {
                $ans = null;
                $ans->answertext = $answers[$i-1];
                $ans->answercount = 0;
                foreach($values as $value) {
                    //ist die Antwort gleich dem index der Antworten + 1?
                    $vallist = explode(FEEDBACK_MULTICHOICE_LINE_SEP, $value->value);
                    foreach($vallist as $val) {
                        if ($val == $i) {
                           $ans->answercount++;
                        }
                    }
                }
                $ans->quotient = $ans->answercount / sizeof($values);
                $analysedAnswer[] = $ans;
            }
        }else {
            for($i = 1; $i <= sizeof($answers); $i++) {
                $ans = null;
                $ans->answertext = $answers[$i-1];
                $ans->answercount = 0;
                foreach($values as $value) {
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

        if(!isset($value->value)) return $printval;

        // @list($presentation) = explode(FEEDBACK_RADIO_ADJUST_SEP, $item->presentation); //remove the adjustment-info

        $presentation = explode (FEEDBACK_MULTICHOICE_LINE_SEP, $info->presentation);

        if($info->subtype == 'c') {
            $vallist = array_values(explode (FEEDBACK_MULTICHOICE_LINE_SEP, $value->value));
            for($i = 0; $i < sizeof($vallist); $i++) {
                for($k = 0; $k < sizeof($presentation); $k++) {
                    if($vallist[$i] == ($k + 1)) {//Die Werte beginnen bei 1, das Array aber mit 0
                        $printval .= trim($presentation[$k]) . chr(10);
                        break;
                    }
                }
            }
        }else {
            $index = 1;
            foreach($presentation as $pres){
                if($value->value == $index){
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
                if( function_exists("bcmod")) {
                    $intvalue = bcmod($pixnr, 10);
                }else {
                    $intvalue = 0;
                }
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
        $worksheet->write_string($rowOffset, 0, $item->label, $xlsFormats->head2_green);
        $worksheet->write_string($rowOffset, 1, $analysed_item[1], $xlsFormats->head2_green);
        if(is_array($data)) {
            for($i = 0; $i < sizeof($data); $i++) {
                $aData = $data[$i];

                // $worksheet->setFormat("<l><f><ro2><vo><c:blue>");
                $worksheet->write_string($rowOffset, $i + 2, trim($aData->answertext), $xlsFormats->value_blue);

                // $worksheet->setFormat("<l><vo>");
                $worksheet->write_number($rowOffset + 1, $i + 2, $aData->answercount, $xlsFormats->default);
                // $worksheet->setFormat("<l><f><vo><pr>");
                $worksheet->write_number($rowOffset + 2, $i + 2, $aData->quotient, $xlsFormats->procent);
            }
        }
        $rowOffset +=3 ;
        return $rowOffset;
    }

    function print_item($item, $value = false, $readonly = false, $edit = false, $highlightrequire = false){
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
                $highlight = 'bgcolor="#FFAAAA" class="missingrequire"';
            }else {
                $highlight = '';
            }
            $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';

            echo '<td '.$highlight.' valign="top" align="'.$align.'">';
            if($edit OR $readonly) {
                echo '('.$item->label.') ';
            }
            echo format_text($item->name.$requiredmark, true, false, false).'</td>';
            echo '<td valign="top" align="'.$align.'">';
        }else {
            if($highlightrequire AND $item->required AND intval($value) <= 0) {
                $highlight = 'bgcolor="#FFAAAA" class="missingrequire"';
            }else {
                $highlight = '';
            }
            $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
            ?>
                <td <?php echo $highlight;?> valign="top" align="<?php echo $align;?>">
                <?php
                if($edit OR $readonly) {
                    echo '('.$item->label.') ';
                }
                echo format_text($item->name . $requiredmark, true, false, false);
                ?>
                </td>
                <td valign="top" align="<?php echo $align;?>">
            <?php
        }
        $index = 1;
        $checked = '';
        if($readonly){
            if($info->subtype == 'c') {
                echo $OUTPUT->box_start('generalbox boxalign'.$align);
                foreach($presentation as $pres){
                    foreach($values as $val) {
                        if($val == $index){
                            echo text_to_html($pres . '<br />', true, false, false);
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
        } else {
            //print the "not_selected" item on radiobuttons
            if($info->subtype == 'r') {
    ?>
                <table><tr>
                <td valign="top" align="<?php echo $align;?>"><input type="radio"
                        name="<?php echo $item->typ . '_' . $item->id ;?>"
                        id="<?php echo $item->typ . '_' . $item->id.'_xxx';?>"
                        value="" <?php echo $value ? '' : 'checked="checked"';?> />
                </td>
                <td align="<?php echo $align;?>">
                    <label for="<?php echo $item->typ . '_' . $item->id.'_xxx';?>"><?php print_string('not_selected', 'feedback');?>&nbsp;</label>
                </td>
                </tr></table>
    <?php
            }
            if($info->subtype != 'd') {
                if($info->horizontal) {
                    echo '<table><tr>';
                }
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

            if($info->subtype != 'd') {
                if($info->horizontal) {
                    echo '</tr></table>';
                }
            }
            /*
            if($item->required == 1) {
                echo '<input type="hidden" name="'.$item->typ . '_' . $item->id.'" value="1" />';
            }
            */
        }
    ?>
        </td>
    <?php
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

        $info = new object();
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
        if(!is_array($value)) {
            return $value;
        }
        $retval = '';
        $arrvals = array_values($value);
        $arrvals = clean_param($arrvals, PARAM_INT);  //prevent sql-injection
        $retval = $arrvals[0];
        for($i = 1; $i < sizeof($arrvals); $i++) {
            $retval .= FEEDBACK_MULTICHOICE_LINE_SEP.$arrvals[$i];
        }
        return $retval;
    }

    function print_item_radio($presentation, $item, $value, $info, $align) {
        $index = 1;
        $checked = '';
        foreach($presentation as $radio){
            if($value == $index){
                $checked = 'checked="checked"';
            }else{
                $checked = '';
            }
            $inputname = $item->typ . '_' . $item->id;
            $inputid = $inputname.'_'.$index;
            if($info->horizontal) {
        ?>
                <td valign="top" align="<?php echo $align;?>"><input type="radio"
                    name="<?php echo $inputname;?>"
                    id="<?php echo $inputid;?>"
                    value="<?php echo $index;?>" <?php echo $checked;?> />
                </td>
                <td align="<?php echo $align;?>">
                    <label for="<?php echo $inputid;?>"><?php echo text_to_html($radio, true, false, false);?>&nbsp;</label>
                </td>
        <?php
            }else {
        ?>
                <table><tr>
                <td valign="top" align="<?php echo $align;?>"><input type="radio"
                        name="<?php echo $inputname;?>"
                        id="<?php echo $inputid;?>"
                        value="<?php echo $index;?>" <?php echo $checked;?> />
                </td><td align="<?php echo $align;?>"><label for="<?php echo $inputid;?>"><?php echo text_to_html($radio, true, false, false);?>&nbsp;</label>
                </td></tr></table>
        <?php
            }
            $index++;
        }
    }

    function print_item_check($presentation, $item, $value, $info, $align) {

        if (is_array($value)) {
            $values = $value;
        }else {
            $values = explode(FEEDBACK_MULTICHOICE_LINE_SEP, $value);
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
            if($info->horizontal) {
        ?>
                <td valign="top" align="<?php echo $align;?>"><input type="checkbox"
                    name="<?php echo $inputname;?>[]"
                    id="<?php echo $inputid;?>"
                    value="<?php echo $index;?>" <?php echo $checked;?> />
                </td><td align="<?php echo $align;?>"><label for="<?php echo $inputid;?>"><?php echo text_to_html($check, true, false, false);?>&nbsp;</label>
                </td>
        <?php
            }else {
        ?>
                <table><tr>
                <td valign="top" align="<?php echo $align;?>"><input type="checkbox"
                    name="<?php echo $inputname;?>[]"
                    id="<?php echo $inputid;?>"
                    value="<?php echo $index;?>" <?php echo $checked;?> />
                </td><td align="<?php echo $align;?>"><label for="<?php echo $inputid;?>"><?php echo text_to_html($check, true, false, false);?>&nbsp;</label>
                </td></tr></table>
        <?php
            }
            $index++;
        }
    }

    function print_item_dropdown($presentation, $item, $value, $info, $align) {
        ?>
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
        <?php
    }

}

?>
