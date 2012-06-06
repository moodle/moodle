<?php
defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_textfield extends feedback_item_base {
    var $type = "textfield";
    var $commonparams;
    var $item_form;
    var $item;

    function init() {

    }

    function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('textfield_form.php');

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
        
        $sizeAndLength = explode('|',$item->presentation);
        $itemsize = (isset($sizeAndLength[0]) AND $sizeAndLength[0] >= 5) ? $sizeAndLength[0] : 30;
        $itemlength = isset($sizeAndLength[1]) ? $sizeAndLength[1] : 5;
        $item->itemsize = $itemsize;
        $item->itemmaxlength = $itemlength;

        //all items for dependitem
        $feedbackitems = feedback_get_depend_candidates_for_item($feedback, $item);
        $commonparams = array('cmid'=>$cm->id,
                             'id'=>isset($item->id) ? $item->id : NULL,
                             'typ'=>$item->typ,
                             'items'=>$feedbackitems,
                             'feedback'=>$feedback->id);

        //build the form
        $this->item_form = new feedback_textfield_form('edit_item.php', array('item'=>$item, 'common'=>$commonparams, 'positionlist'=>$positionlist, 'position'=>$position));
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

        $aVal = null;
        $aVal->data = null;
        $aVal->name = $item->name;
        //$values = $DB->get_records('feedback_value', array('item'=>$item->id));
        $values = feedback_get_group_values($item, $groupid, $courseid);
        if($values) {
            $data = array();
            foreach($values as $value) {
                $data[] = str_replace("\n", '<br />', $value->value);
            }
            $aVal->data = $data;
        }
        return $aVal;
    }

    function get_printval($item, $value) {

        if(!isset($value->value)) return '';
        return $value->value;
    }

    function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {
        $values = feedback_get_group_values($item, $groupid, $courseid);
        if($values) {
            //echo '<table>';2
            // $itemnr++;
            echo '<tr><th colspan="2" align="left">'. $itemnr . '&nbsp;('. $item->label .') ' . $item->name .'</th></tr>';
            foreach($values as $value) {
                echo '<tr><td colspan="2" valign="top" align="left">-&nbsp;&nbsp;' . str_replace("\n", '<br />', $value->value) . '</td></tr>';
            }
            //echo '</table>';
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
            $worksheet->write_string($rowOffset, 2, $data[0], $xlsFormats->value_bold);
            $rowOffset++;
            $sizeofdata = sizeof($data);
            for($i = 1; $i < $sizeofdata; $i++) {
                // $worksheet->setFormat("<l><vo>");
                $worksheet->write_string($rowOffset, 2, $data[$i], $xlsFormats->default);
                $rowOffset++;
            }
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

        $presentation = explode ("|", $item->presentation);
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
        echo '<span class="feedback_item_textfield">';
        echo '<input type="text" name="'.$item->typ . '_' . $item->id.'" size="'.$presentation[0].'" maxlength="'.$presentation[1].'" value="" />';
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

        $presentation = explode ("|", $item->presentation);
        if($highlightrequire AND $item->required AND strval($value) == '') {
            $highlight = ' missingrequire';
        }else {
            $highlight = '';
        }
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        
        //print the question and label
        echo '<div class="feedback_item_label_'.$align.$highlight.'">';
            echo format_text($item->name.$requiredmark, true, false, false);
        echo '</div>';
        
        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.$highlight.'">';
        echo '<span class="feedback_item_textfield">';
        echo '<input type="text" name="'.$item->typ.'_'.$item->id.'" size="'.$presentation[0].'" maxlength="'.$presentation[1].'" value="'.$value.'" />';
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

        $presentation = explode ("|", $item->presentation);
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        
        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
            echo '('.$item->label.') ';
            echo format_text($item->name . $requiredmark, true, false, false);
        echo '</div>';
        echo $OUTPUT->box_start('generalbox boxalign'.$align);
        echo $value ? $value : '&nbsp;';
        echo $OUTPUT->box_end();
    }

    function check_value($value, $item) {
        //if the item is not required, so the check is true if no value is given
        if((!isset($value) OR $value == '') AND $item->required != 1) return true;
        if($value == "")return false;
        return true;
    }

    function create_value($data) {
        $data = s($data);
        return $data;
    }

    //compares the dbvalue with the dependvalue
    //dbvalue is the value put in by the user
    //dependvalue is the value that is compared
    function compare_value($item, $dbvalue, $dependvalue) {
        if($dbvalue == $dependvalue) {
            return true;
        }
        return false;
    }
    
    function get_presentation($data) {
        return $data->itemsize . '|'. $data->itemmaxlength;
    }

    function get_hasvalue() {
        return 1;
    }

    function can_switch_require() {
        return true;
    }

    function clean_input_value($value) {
        return s($value);
    }
}
