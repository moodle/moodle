<?php
defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_info extends feedback_item_base {
    var $type = "info";
    var $commonparams;
    var $item_form;
    var $item;

    function init() {

    }

    function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('info_form.php');

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

        $item->presentation = empty($item->presentation) ? 1 : $item->presentation;
        $item->required = 0;

        //all items for dependitem
        $feedbackitems = feedback_get_depend_candidates_for_item($feedback, $item);
        $commonparams = array('cmid'=>$cm->id,
                             'id'=>isset($item->id) ? $item->id : NULL,
                             'typ'=>$item->typ,
                             'items'=>$feedbackitems,
                             'feedback'=>$feedback->id);

        //build the form
        $this->item_form = new feedback_info_form('edit_item.php', array('item'=>$item, 'common'=>$commonparams, 'positionlist'=>$positionlist, 'position'=>$position));
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

        $presentation = $item->presentation;
        $aVal = null;
        $aVal->data = null;
        $aVal->name = $item->name;
        //$values = get_records('feedback_value', 'item', $item->id);
        $values = feedback_get_group_values($item, $groupid, $courseid);
        if($values) {
            $data = array();
            $datavalue = new stdClass();
            foreach($values as $value) {

                switch($presentation) {
                    case 1:
                        $datavalue->value = $value->value;
                        $datavalue->show = UserDate($datavalue->value);
                        break;
                    case 2:
                        $datavalue->value = $value->value;
                        $datavalue->show = $datavalue->value;
                        break;
                    case 3:
                        $datavalue->value = $value->value;
                        $datavalue->show = $datavalue->value;
                        break;
                }

                $data[] = $datavalue;
            }
            $aVal->data = $data;
        }
        return $aVal;
    }

    function get_printval($item, $value) {

        if(!isset($value->value)) return '';
        return UserDate($value->value);
    }

    function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {
        $analysed_item = $this->get_analysed($item, $groupid, $courseid);
        $data = $analysed_item->data;
        if (is_array($data)) {
            echo '<tr><th colspan="2" align="left">'. $itemnr . '&nbsp;('. $item->label .') ' . $item->name .'</th></tr>';
            $sizeofdata = sizeof($data);
            for ($i = 0; $i < $sizeofdata; $i++) {
                echo '<tr><td colspan="2" valign="top" align="left">-&nbsp;&nbsp;' . str_replace("\n", '<br />', $data[$i]->show) . '</td></tr>';
            }
        }
        // return $itemnr;
    }

    function excelprint_item(&$worksheet, $rowOffset, $xlsFormats, $item, $groupid, $courseid = false) {
        $analysed_item = $this->get_analysed($item, $groupid, $courseid);

        // $worksheet->setFormat("<l><f><ro2><vo><c:green>");
        $worksheet->write_string($rowOffset, 0, $item->label, $xlsFormats->head2);
        $worksheet->write_string($rowOffset, 1, $item->name, $xlsFormats->head2);
        $data = $analysed_item->data;
        if (is_array($data)) {
            // $worksheet->setFormat("<l><ro2><vo>");
            $worksheet->write_string($rowOffset, 2, $data[0]->show, $xlsFormats->value_bold);
            $rowOffset++;
            $sizeofdata = sizeof($data);
            for ($i = 1; $i < $sizeofdata; $i++) {
                // $worksheet->setFormat("<l><vo>");
                $worksheet->write_string($rowOffset, 2, $data[$i]->show, $xlsFormats->default);
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
        global $USER, $DB, $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';

        $presentation = $item->presentation;
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';

        $feedback = $DB->get_record('feedback', array('id'=>$item->feedback));
        $course = $DB->get_record('course', array('id'=>$feedback->course));
        $coursecategory = $DB->get_record('course_categories', array('id'=>$course->category));
        switch($presentation) {
            case 1:
                $itemvalue = time();
                $itemshowvalue = UserDate($itemvalue);
                break;
            case 2:
                $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
                $itemvalue = format_string($course->shortname, true, array('context' => $coursecontext));
                $itemshowvalue = $itemvalue;
                break;
            case 3:
                $itemvalue = format_string($coursecategory->name, true, array('context' => get_context_instance(CONTEXT_COURSECAT, $coursecategory->id)));
                $itemshowvalue = $itemvalue;
                break;
        }

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
            echo '<input type="hidden" name="'.$item->typ.'_'.$item->id.'" value="'.$itemvalue.'" />';
            echo '<span class="feedback_item_info">'.$itemshowvalue.'</span>';
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
        global $USER, $DB, $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';

        $presentation = $item->presentation;
        if($highlightrequire AND $item->required AND strval($value) == '') {
            $highlight = ' missingrequire';
        }else {
            $highlight = '';
        }
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';

        $feedback = $DB->get_record('feedback', array('id'=>$item->feedback));
        $course = $DB->get_record('course', array('id'=>$feedback->course));
        $coursecategory = $DB->get_record('course_categories', array('id'=>$course->category));
        switch($presentation) {
            case 1:
                $itemvalue = time();
                $itemshowvalue = UserDate($itemvalue);
                break;
            case 2:
                $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
                $itemvalue = format_string($course->shortname, true, array('context' => $coursecontext));
                $itemshowvalue = $itemvalue;
                break;
            case 3:
                $itemvalue = format_string($coursecategory->name, true, array('context' => get_context_instance(CONTEXT_COURSECAT, $coursecategory->id)));
                $itemshowvalue = $itemvalue;
                break;
        }

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.$highlight.'">';
            echo format_text($item->name.$requiredmark, true, false, false);
        echo '</div>';

        //print the presentation
        echo '<div class="feedback_item_presentation_'.$align.'">';
            echo '<input type="hidden" name="'.$item->typ.'_'.$item->id.'" value="'.$itemvalue.'" />';
            echo '<span class="feedback_item_info">'.$itemshowvalue.'</span>';
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
        global $USER, $DB, $OUTPUT;
        $align = right_to_left() ? 'right' : 'left';

        $presentation = $item->presentation;
        $requiredmark =  ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';

        if($presentation == 1) {
            $value = $value ? UserDate($value) : '&nbsp;';
        }

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
            echo '('.$item->label.') ';
            echo format_text($item->name . $requiredmark, true, false, false);
        echo '</div>';

        //print the presentation
        echo $OUTPUT->box_start('generalbox boxalign'.$align);
        echo $value;
        echo $OUTPUT->box_end();
    }

    function check_value($value, $item) {
        return true;
    }

    function create_value($data) {
        $data = clean_text($data);
        return $data;
    }

    //compares the dbvalue with the dependvalue
    //the values can be the shortname of a course or the category name
    //the date is not compareable :(.
    function compare_value($item, $dbvalue, $dependvalue) {
        if($dbvalue == $dependvalue) {
            return true;
        }
        return false;
    }

    function get_presentation($data) {
        return $data->infotype;
    }

    function get_hasvalue() {
        return 1;
    }

    function can_switch_require() {
        return false;
    }
}
