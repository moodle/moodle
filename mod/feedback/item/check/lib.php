<?PHP  // $Id$
defined('FEEDBACK_INCLUDE_TEST') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

define('FEEDBACK_CHECK_LINE_SEP', '|');
define('FEEDBACK_CHECK_ADJUST_SEP', '<<<<<');
class feedback_item_check extends feedback_item_base {
    var $type = "check";
    function init() {
    
    }
    
    function show_edit($item, $usehtmleditor = false) {

        $item->presentation = empty($item->presentation) ? '' : $item->presentation;
       
        //check, whether the buttons are vertical or horizontal
        $presentation = $horizontal = '';
        @list($presentation, $horizontal) = explode(FEEDBACK_CHECK_ADJUST_SEP, $item->presentation);
        if(isset($horizontal) AND $horizontal == 1) {
            $horizontal = true;
        }else {
            $horizontal = false;
        }
        
    ?>
        <table>
            <tr><th colspan="2">
                <?php print_string('check', 'feedback');?>
                    &nbsp;(<input type="checkbox" name="required" value="1" <?php 
                    $item->required=isset($item->required) ? $item->required : 0;
                    echo ($item->required == 1?'checked="checked"':'');
                    ?> />&nbsp;<?php print_string('required', 'feedback');?>)
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <?php print_string('adjustment', 'feedback');?>:
                    &nbsp;<?php print_string('vertical', 'feedback');?><input type="radio" name="horizontal" value="0" <?php echo $horizontal ? '' : 'checked="checked"';?> />
                    &nbsp;<?php print_string('horizontal', 'feedback');?><input type="radio" name="horizontal" value="1" <?php echo $horizontal ? 'checked="checked"' : '';?> />
                </td>
            </tr>
            <tr>
                <td><?php print_string('item_name', 'feedback');?></td>
                <td><input type="text" id="itemname" name="itemname" size="40" maxlength="255" value="<?php echo isset($item->name)?htmlspecialchars(stripslashes_safe($item->name)):'';?>" /></td>
            </tr>
            <tr>
                <td>
                    <?php print_string('check_values', 'feedback');?>
                    <?php print_string('use_one_line_for_each_value', 'feedback');?>
                </td>
                <td>
        <?php
                    $itemvalues = str_replace(FEEDBACK_CHECK_LINE_SEP, "\n", stripslashes_safe($presentation));
        ?>
                    <textarea name="itemvalues" cols="30" rows="5"><?php echo $itemvalues;?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }

    //liefert ein Array mit drei Werten(typ, name, XXX)
    //XXX ist ein Array (anzahl der Antworten bei Typ checkbox) Jedes Element ist eine Struktur (answertext, answercount)
    function get_analysed($item, $groupid = false, $courseid = false) {
        $analysedItem = array();
        $analysedItem[] = $item->typ;
        $analysedItem[] = $item->name;
        //die moeglichen Antworten extrahieren
        $answers = null;
        $presentation = '';
        @list($presentation) = explode(FEEDBACK_CHECK_ADJUST_SEP, $item->presentation); //remove the adjustment-info
       
        $answers = explode (FEEDBACK_CHECK_LINE_SEP, stripslashes_safe($presentation));
        if(!is_array($answers)) return null;

        //die Werte holen
        $values = feedback_get_group_values($item, $groupid, $courseid);
        if(!$values) return null;
        //schleife ueber den Werten und ueber die Antwortmoeglichkeiten
       
        $analysedAnswer = array();

        for($i = 1; $i <= sizeof($answers); $i++) {
            $ans = null;
            $ans->answertext = $answers[$i-1];
            $ans->answercount = 0;
            foreach($values as $value) {
                //ist die Antwort gleich dem index der Antworten + 1?
                $vallist = explode(FEEDBACK_CHECK_LINE_SEP, $value->value);
                foreach($vallist as $val) {
                    if ($val == $i) {
                       $ans->answercount++;
                    }
                }
            }
            $ans->quotient = $ans->answercount / sizeof($values);
            $analysedAnswer[] = $ans;
        }
        $analysedItem[] = $analysedAnswer;
        return $analysedItem;
    }

    function get_printval($item, $value) {
        $printval = '';
        
        if(!isset($value->value)) return $printval;

        @list($presentation) = explode(FEEDBACK_CHECK_ADJUST_SEP, $item->presentation); //remove the adjustment-info

        $presentation = array_values(explode (FEEDBACK_CHECK_LINE_SEP, stripslashes_safe($presentation)));
        $vallist = array_values(explode (FEEDBACK_CHECK_LINE_SEP, $value->value));
        for($i = 0; $i < sizeof($vallist); $i++) {
            for($k = 0; $k < sizeof($presentation); $k++) {
                if($vallist[$i] == ($k + 1)) {//Die Werte beginnen bei 1, das Array aber mit 0
                    $printval .= trim($presentation[$k]) . chr(10);
                    break;
                }
            }
        }
        return $printval;
    }

    function print_analysed($item, $itemnr = 0, $groupid = false, $courseid = false) {
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
            $itemnr++;
            $itemname = stripslashes($analysedItem[1]);
            echo '<tr><th colspan="2" align="left">'. $itemnr . '.)&nbsp;' . $itemname .'</th></tr>';
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
        return $itemnr;
    }

    function excelprint_item(&$worksheet, $rowOffset, $item, $groupid, $courseid = false) {
        $analysed_item = $this->get_analysed($item, $groupid, $courseid);


        $data = $analysed_item[2];

        $worksheet->setFormat("<l><f><ro2><vo><c:green>");
        //frage schreiben
        $worksheet->write_string($rowOffset, 0, stripslashes($analysed_item[1]));
        if(is_array($data)) {
            for($i = 0; $i < sizeof($data); $i++) {
                $aData = $data[$i];
             
                $worksheet->setFormat("<l><f><ro2><vo><c:blue>");
                $worksheet->write_string($rowOffset, $i + 1, trim($aData->answertext));
             
                $worksheet->setFormat("<l><vo>");
                $worksheet->write_number($rowOffset + 1, $i + 1, $aData->answercount);
                $worksheet->setFormat("<l><f><vo><pr>");
                $worksheet->write_number($rowOffset + 2, $i + 1, $aData->quotient);
            }
        }
        $rowOffset +=3 ;
        return $rowOffset;
    }

    function print_item($item, $value = false, $readonly = false, $edit = false, $highlightrequire = false){
       
        $align = get_string('thisdirection') == 'ltr' ? 'left' : 'right';
        
        //extract the adjustment-info
        $presentation = $horizontal = '';
        @list($presentation, $horizontal) = explode(FEEDBACK_CHECK_ADJUST_SEP, $item->presentation);
        if(isset($horizontal) AND $horizontal == 1) {
            $horizontal = true;
        }else {
            $horizontal = false;
        }
       
        $presentation = explode (FEEDBACK_CHECK_LINE_SEP, stripslashes_safe($presentation));
        if (is_array($value)) {
            $values = $value;
        }else {
            $values = explode(FEEDBACK_CHECK_LINE_SEP, $value);
        }
        if($highlightrequire AND $item->required AND $values[0] == '') {
            $highlight = 'bgcolor="#FFAAAA" class="missingrequire"';
        }else {
            $highlight = '';
        }
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        
        echo '<td '.$highlight.' valign="top" align="'.$align.'">'.format_text(stripslashes_safe($item->name).$requiredmark, true, false, false).'</td>';
        echo '<td valign="top" align="'.$align.'">';

        $index = 1;
        $checked = '';
        if($readonly){
            // print_simple_box_start($align);
            print_box_start('generalbox boxalign'.$align);
            foreach($presentation as $check){
                foreach($values as $val) {
                    if($val == $index){
                        echo text_to_html($check . '<br />', true, false, false);
                        break;
                    }
                }
                $index++;
            }
            // print_simple_box_end();
            print_box_end();
        } else {
            if($horizontal) {
                echo '<table><tr>';
            }
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
                if($horizontal) {
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
            if($horizontal) {
                echo '</tr></table>';
            }
        }
    ?>
            <input type="hidden" name="<?php echo $item->typ. '_' . $item->id?>[]" value="" />
        </td>
    <?php
    }

    function check_value($value, $item) {
       //if the item is not required, so the check is true if no value is given
        if((!isset($value) OR !is_array($value) OR $value[0] == '' OR $value[0] == 0) AND $item->required != 1) return true;
        if($value[0] == "")return false;
        return true;
    }

    function create_value($data) {
        $vallist = $data;
        return trim($this->item_arrayToString($vallist));
    }

    function get_presentation($data) {
        $present = str_replace("\n", FEEDBACK_CHECK_LINE_SEP, trim($data->itemvalues));
        if($data->horizontal == 1) {
            $present .= FEEDBACK_CHECK_ADJUST_SEP.'1';
        }
        return $present;
    }

    function get_hasvalue() {
        return 1;
    }

    function item_arrayToString($arr) {
        if(!is_array($arr)) {
            return '';
        }
        $retval = '';
        $arrvals = array_values($arr);
        $arrvals = clean_param($arrvals, PARAM_INT);  //prevent sql-injection
        $retval = $arrvals[0];
        for($i = 1; $i < sizeof($arrvals) - 1; $i++) {
            $retval .= FEEDBACK_CHECK_LINE_SEP.$arrvals[$i];
        }
        return $retval;
    }
}
?>
