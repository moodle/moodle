<?PHP  // $Id$
defined('FEEDBACK_INCLUDE_TEST') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_dropdown extends feedback_item_base {
    var $type = "dropdown";
    function init() {
    
    }
    
    function show_edit($item, $usehtmleditor = false) {

        $item->presentation=empty($item->presentation)?'':$item->presentation;

    ?>
        <table>
            <tr>
                <th colspan="2"><?php print_string('dropdown', 'feedback');?>
                    &nbsp;(<input type="checkbox" name="required" value="1" <?php 
                $item->required=isset($item->required)?$item->required:0;
                echo ($item->required == 1?'checked="checked"':'');
                ?> />&nbsp;<?php print_string('required', 'feedback');?>)
                </th>
            </tr>
            <tr>
                <td><?php print_string('item_name', 'feedback');?></td>
                <td><input type="text" id="itemname" name="itemname" size="40" maxlength="255" value="<?php echo isset($item->name)?htmlspecialchars(stripslashes_safe($item->name)):'';?>" /></td>
            </tr>
            <tr>
                <td>
                    <?php print_string('dropdown_values', 'feedback');?>
                    <?php print_string('use_one_line_for_each_value', 'feedback');?>
                </td>
                <td>
    <?php
                    $itemvalues = str_replace('|', "\n", stripslashes_safe($item->presentation));
    ?>
                    <textarea name="itemvalues" cols="30" rows="5"><?php echo $itemvalues;?></textarea>
                </td>
            </tr>
        </table>
    <?php
    }

    //liefert ein eindimensionales Array mit drei Werten(typ, name, XXX)
    //XXX ist ein eindimensionales Array (anzahl der Antworten bei Typ DropDown) Jedes Element ist eine Struktur (answertext, answercount)
    function get_analysed($item, $groupid = false, $courseid = false) {
        $analysedItem = array();
        $analysedItem[] = $item->typ;
        $analysedItem[] = $item->name;
        //die moeglichen Antworten extrahieren
        $answers = null;
        $answers = explode ("|", stripslashes_safe($item->presentation));
        if(!is_array($answers)) return null;

        //die Werte holen
        //$values = get_records('feedback_value', 'item', $item->id);
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
                if ($value->value == $i) {
                    $ans->answercount++;
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

        $presentation = explode ("|", stripslashes_safe($item->presentation));
        $index = 1;
        foreach($presentation as $pres){
            if($value->value == $index){
                $printval = $pres;
                break;
            }
            $index++;
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
            //echo '<table>';
            $itemnr++;
            echo '<tr><th colspan="2" align="left">'. $itemnr . '.)&nbsp;' . stripslashes($analysedItem[1]) .'</th></tr>';
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
                echo '<tr><td valign="top" align="left">-&nbsp;&nbsp;' . trim($val->answertext) . ':</td><td align="left" style="width: '.FEEDBACK_MAX_PIX_LENGTH.'"><img alt="'.$intvalue.'" src="'.$pix.'" height="5" width="'.$pixwidth.'" />&nbsp;' . $val->answercount . (($val->quotient > 0)?'&nbsp;('. $quotient . '&nbsp;%)':'').'</td></tr>';
            }
            //echo '</table>';
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
        
        $presentation = explode ("|", stripslashes_safe($item->presentation));
        if($highlightrequire AND $item->required AND intval($value) <= 0) {
            $highlight = 'bgcolor="#FFAAAA" class="missingrequire"';
        }else {
            $highlight = '';
        }
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
    ?>
        <td <?php echo $highlight;?> valign="top" align="<?php echo $align;?>"><?php echo format_text(stripslashes_safe($item->name) . $requiredmark, true, false, false);?></td>
        <td valign="top" align="<?php echo $align;?>">
    <?php
        $index = 1;
        $selected = '';
        if($readonly){
            foreach($presentation as $dropdown){
                if($value == $index){
                    // print_simple_box_start($align);
                    print_box_start('generalbox boxalign'.$align);
                    echo text_to_html($dropdown, true, false, false);
                    // print_simple_box_end();
                    print_box_end();
                    break;
                }
                $index++;
            }
        } else {
    ?>
            <select name="<?php echo $item->typ .'_' . $item->id;?>" size="1">
                <option value="0">&nbsp;</option>
    <?php
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
    ?>
        </td>
    <?php
    }

    function check_value($value, $item) {
        //if the item is not required, so the check is true if no value is given
        if((!isset($value) OR $value == '' OR $value == 0) AND $item->required != 1) return true;
        if($value == 0)return false;
        return true;
    }

    function create_value($data) {
        $data = clean_param($data, PARAM_INT);
        return $data;
    }

    function get_presentation($data) {
        $present = str_replace("\n", '|', trim($data->itemvalues));
        return $present;
    }

    function get_hasvalue() {
        return 1;
    }
}
?>
