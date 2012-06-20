<?php
defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_numeric extends feedback_item_base {
    var $type = "numeric";
    var $sep_dec, $sep_thous;
    var $commonparams;
    var $item_form;
    var $item;

    function init() {
        $this->sep_dec = get_string('separator_decimal', 'feedback');
        if(substr($this->sep_dec, 0, 2) == '[['){
            $this->sep_dec = FEEDBACK_DECIMAL;
        }

        $this->sep_thous = get_string('separator_thousand', 'feedback');
        if(substr($this->sep_thous, 0, 2) == '[['){
            $this->sep_thous = FEEDBACK_THOUSAND;
        }
    }

    function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('numeric_form.php');

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
        
        $range_from_to = explode('|',$item->presentation);
        $range_from = (isset($range_from_to[0]) AND is_numeric($range_from_to[0])) ? str_replace(FEEDBACK_DECIMAL, $this->sep_dec, floatval($range_from_to[0])) : '-';
        $range_to = (isset($range_from_to[1]) AND is_numeric($range_from_to[1])) ? str_replace(FEEDBACK_DECIMAL, $this->sep_dec, floatval($range_from_to[1])) : '-';
        $item->rangefrom = $range_from;
        $item->rangeto = $range_to;

        //all items for dependitem
        $feedbackitems = feedback_get_depend_candidates_for_item($feedback, $item);
        $commonparams = array('cmid'=>$cm->id,
                             'id'=>isset($item->id) ? $item->id : NULL,
                             'typ'=>$item->typ,
                             'items'=>$feedbackitems,
                             'feedback'=>$feedback->id);

        //build the form
        $this->item_form = new feedback_numeric_form('edit_item.php', array('item'=>$item, 'common'=>$commonparams, 'positionlist'=>$positionlist, 'position'=>$position));
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
        
        $item->hasvalue = $this->get_hasvalue();
        if(!$item->id) {
            $item->id = $DB->insert_record('feedback_item', $item);
        }else {
            $DB->update_record('feedback_item', $item);
        }
        
        return $DB->get_record('feedback_item', array('id'=>$item->id));
    }


    //liefert eine Struktur ->name, ->data = array(mit Antworten)
    function get_analysed($item, $groupid = false, $courseid = false) {
        global $DB;

        $analysed = null;
        $analysed->data = array();
        $analysed->name = $item->name;
        //$values = $DB->get_records('feedback_value', array('item'=>$item->id));
        $values = feedback_get_group_values($item, $groupid, $courseid);

        $avg = 0.0;
        $counter = 0;
        if($values) {
            $data = array();
            foreach($values as $value) {
                if(is_numeric($value->value)) {
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

    function get_printval($item, $value) {
        if(!isset($value->value)) return '';

        return $value->value;
    }

    function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {

        // $values = feedback_get_group_values($item, $groupid, $courseid);
        $values = $this->get_analysed($item, $groupid, $courseid);

        if(isset($values->data) AND is_array($values->data)) {
            //echo '<table>';2
            // $itemnr++;
            echo '<tr><th colspan="2" align="left">'. $itemnr . '&nbsp;('. $item->label .') ' . $item->name .'</th></tr>';
            foreach($values->data as $value) {
                echo '<tr><td colspan="2" valign="top" align="left">-&nbsp;&nbsp;' . number_format($value, 2, $this->sep_dec, $this->sep_thous) . '</td></tr>';
            }
            //echo '</table>';
            if(isset($values->avg)) {
                $avg = number_format($values->avg, 2, $this->sep_dec, $this->sep_thous);
            } else {
                $avg = number_format(0, 2, $this->sep_dec, $this->sep_thous);
            }
            echo '<tr><td align="left" colspan="2"><b>'.get_string('average', 'feedback').': '.$avg.'</b></td></tr>';
        }
        // return $itemnr;
    }

    function excelprint_item(&$worksheet, $rowOffset, $xlsFormats, $item, $groupid, $courseid = false) {
        $analysed_item = $this->get_analysed($item, $groupid, $courseid);

        // $worksheet->setFormat("<l><f><ro2><vo><c:green>");
        $worksheet->write_string($rowOffset, 0, $item->label, $xlsFormats->head2);
        $worksheet->write_string($rowOffset, 1, $item->name, $xlsFormats->head2);
        $data = $analysed_item->data;
        if(is_array($data)) {
            // $worksheet->setFormat("<l><ro2><vo>");
            // $worksheet->write_number($rowOffset, 1, $data[0]);
            // $rowOffset++;
            // for($i = 1; $i < sizeof($data); $i++) {
                // $worksheet->setFormat("<l><vo>");
                // $worksheet->write_number($rowOffset, 1, $data[$i]);
                // $rowOffset++;
            // }

            //mittelwert anzeigen
            // $worksheet->setFormat("<l><f><ro2><vo><c:red>");
            $worksheet->write_string($rowOffset, 2, get_string('average', 'feedback'), $xlsFormats->value_bold);

            // $worksheet->setFormat("<l><f><vo>");
            $worksheet->write_number($rowOffset + 1, 2, $analysed_item->avg, $xlsFormats->value_bold);
            $rowOffset++;
        }
        $rowOffset++;
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

        //get the range
        $range_from_to = explode('|',$item->presentation);
        //get the min-value
        $range_from = (isset($range_from_to[0]) AND is_numeric($range_from_to[0])) ? floatval($range_from_to[0]) : 0;
        //get the max-value
        $range_to = (isset($range_from_to[1]) AND is_numeric($range_from_to[1])) ? floatval($range_from_to[1]) : 0;
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
            echo '('.$item->label.') ';
            echo format_text($item->name . $requiredmark, true, false, false);
            if($item->dependitem) {
                if($dependitem = $DB->get_record('feedback_item', array('id'=>$item->dependitem))) {
                    echo ' <span class="feedback_depend">('.$dependitem->label.'-&gt;'.$item->dependvalue.')</span>';
                }
            }
            echo '<span class="feedback_item_numinfo">';
            switch(true) {
                case ($range_from === '-' AND is_numeric($range_to)):
                    echo ' ('.get_string('maximal', 'feedback').': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                    break;
                case (is_numeric($range_from) AND $range_to === '-'):
                    echo ' ('.get_string('minimal', 'feedback').': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).')';
                    break;
                case ($range_from === '-' AND $range_to === '-'):
                    break;
                default:
                    echo ' ('.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).' - '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                    break;
            }
            echo '</span>';
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
        echo '<span class="feedback_item_textfield">';
        echo '<input type="text" name="'.$item->typ.'_'.$item->id.'" size="10" maxlength="10" value="" />';
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
    function print_item_complete($item, $value = '', $highlightrequire = false) {
        global $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';

        //get the range
        $range_from_to = explode('|',$item->presentation);
        //get the min-value
        $range_from = (isset($range_from_to[0]) AND is_numeric($range_from_to[0])) ? floatval($range_from_to[0]) : 0;
        //get the max-value
        $range_to = (isset($range_from_to[1]) AND is_numeric($range_from_to[1])) ? floatval($range_from_to[1]) : 0;
        if($highlightrequire AND (!$this->check_value($value, $item))) {
            $highlight = ' missingrequire';
        }else {
            $highlight = '';
        }
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        
        //print the question and label
        echo '<div class="feedback_item_label_'.$align.$highlight.'">';
            echo format_text($item->name . $requiredmark, true, false, false);
            echo '<span class="feedback_item_numinfo">';
            switch(true) {
                case ($range_from === '-' AND is_numeric($range_to)):
                    echo ' ('.get_string('maximal', 'feedback').': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                    break;
                case (is_numeric($range_from) AND $range_to === '-'):
                    echo ' ('.get_string('minimal', 'feedback').': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).')';
                    break;
                case ($range_from === '-' AND $range_to === '-'):
                    break;
                default:
                    echo ' ('.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).' - '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                    break;
            }
            echo '</span>';
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.$highlight.'">';
        echo '<span class="feedback_item_textfield">';
        echo '<input type="text" name="'.$item->typ.'_'.$item->id.'" size="10" maxlength="10" value="'.$value.'" />';
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
    function print_item_show_value($item, $value = '') {
        global $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';

        //get the range
        $range_from_to = explode('|',$item->presentation);
        //get the min-value
        $range_from = (isset($range_from_to[0]) AND is_numeric($range_from_to[0])) ? floatval($range_from_to[0]) : 0;
        //get the max-value
        $range_to = (isset($range_from_to[1]) AND is_numeric($range_from_to[1])) ? floatval($range_from_to[1]) : 0;
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        
        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
            echo '('.$item->label.') ';
            echo format_text($item->name . $requiredmark, true, false, false);
            switch(true) {
                case ($range_from === '-' AND is_numeric($range_to)):
                    echo ' ('.get_string('maximal', 'feedback').': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                    break;
                case (is_numeric($range_from) AND $range_to === '-'):
                    echo ' ('.get_string('minimal', 'feedback').': '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).')';
                    break;
                case ($range_from === '-' AND $range_to === '-'):
                    break;
                default:
                    echo ' ('.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_from).' - '.str_replace(FEEDBACK_DECIMAL, $this->sep_dec, $range_to).')';
                    break;
            }
        echo '</div>';
        
        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
        echo $OUTPUT->box_start('generalbox boxalign'.$align);
        echo (is_numeric($value)) ? number_format($value, 2, $this->sep_dec, $this->sep_thous) : '&nbsp;';
        echo $OUTPUT->box_end();
        echo '</div>';
    }

    function check_value($value, $item) {
        $value = str_replace($this->sep_dec, FEEDBACK_DECIMAL, $value);
        //if the item is not required, so the check is true if no value is given
        if((!isset($value) OR $value == '') AND $item->required != 1) return true;
        if(!is_numeric($value))return false;

        $range_from_to = explode('|',$item->presentation);
        $range_from = (isset($range_from_to[0]) AND is_numeric($range_from_to[0])) ? floatval($range_from_to[0]) : '-';
        $range_to = (isset($range_from_to[1]) AND is_numeric($range_from_to[1])) ? floatval($range_from_to[1]) : '-';

        switch(true) {
            case ($range_from === '-' AND is_numeric($range_to)):
                if(floatval($value) <= $range_to) return true;
                break;
            case (is_numeric($range_from) AND $range_to === '-'):
                if(floatval($value) >= $range_from) return true;
                break;
            case ($range_from === '-' AND $range_to === '-'):
                return true;
                break;
            default:
                if(floatval($value) >= $range_from AND floatval($value) <= $range_to) return true;
                break;
        }

        return false;
    }

    function create_value($data) {
        $data = str_replace($this->sep_dec, FEEDBACK_DECIMAL, $data);

        if(is_numeric($data)) {
            $data = floatval($data);
        }else {
            $data = '';
        }
        return $data;
    }

    //compares the dbvalue with the dependvalue
    //dbvalue is the number put in by the user
    //dependvalue is the value that is compared
    function compare_value($item, $dbvalue, $dependvalue) {
        if($dbvalue == $dependvalue) {
            return true;
        }
        return false;
    }
    
    function get_presentation($data) {
        $num1 = str_replace($this->sep_dec, FEEDBACK_DECIMAL, $data->numericrangefrom);
        if(is_numeric($num1)) {
            $num1 = floatval($num1);
        }else {
            $num1 = '-';
        }

        $num2 = str_replace($this->sep_dec, FEEDBACK_DECIMAL, $data->numericrangeto);
        if(is_numeric($num2)) {
            $num2 = floatval($num2);
        }else {
            $num2 = '-';
        }

        if($num1 === '-' OR $num2 === '-') {
            return $num1 . '|'. $num2;
        }

        if($num1 > $num2) {
            return $num2 . '|'. $num1;
        }else {
            return $num1 . '|'. $num2;
        }
    }

    function get_hasvalue() {
        return 1;
    }

    function can_switch_require() {
        return true;
    }

    function clean_input_value($value) {
        if (!is_numeric($value)) {
            return null;
        }
        return clean_param($value, PARAM_FLOAT);
    }
}
