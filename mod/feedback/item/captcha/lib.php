<?php
defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_captcha extends feedback_item_base {
    var $type = "captcha";
    var $commonparams;
    var $item_form;
    var $item;

    function init() {

    }

    function build_editform($item, $feedback, $cm) {
        global $DB, $CFG;
        require_once('captcha_form.php');

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
        
        $item->presentation = empty($item->presentation) ? 3 : $item->presentation;
        $item->required = 1;

        $commonparams = array('cmid'=>$cm->id,
                             'id'=>isset($item->id) ? $item->id : NULL,
                             'typ'=>$item->typ,
                             'feedback'=>$feedback->id);

        //build the form
        $this->item_form = new feedback_captcha_form('edit_item.php', array('item'=>$item, 'common'=>$commonparams, 'positionlist'=>$positionlist, 'position'=>$position));
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
        
        if(!$item->id) {
            $item->id = $DB->insert_record('feedback_item', $item);
        }else {
            $DB->update_record('feedback_item', $item);
        }
        
        return $DB->get_record('feedback_item', array('id'=>$item->id));
    }

    //liefert eine Struktur ->name, ->data = array(mit Antworten)
    function get_analysed($item, $groupid = false, $courseid = false) {
        return null;
    }

    function get_printval($item, $value) {
        return '';
    }

    function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {
        return $itemnr;
    }

    function excelprint_item(&$worksheet, $rowOffset, $item, $groupid, $courseid = false) {
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
        global $SESSION, $CFG, $DB, $OUTPUT;

        $align = right_to_left() ? 'right' : 'left';

        $presentation = $item->presentation;
        $SESSION->feedback->item->captcha->charcount = $presentation;

        $cmid = 0;
        $feedbackid = $item->feedback;
        if($feedbackid > 0) {
            $feedback = $DB->get_record('feedback', array('id'=>$feedbackid));
            if($cm = get_coursemodule_from_instance("feedback", $feedback->id, $feedback->course)) {
                $cmid = $cm->id;
            }
        }

        if(isset($SESSION->feedback->item->captcha->checked)) {
            $checked = $SESSION->feedback->item->captcha->checked == true;
            unset($SESSION->feedback->item->captcha->checked);
        }else {
            $checked = false;
        }

        $requiredmark = ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        ?>
        <td valign="top" align="<?php echo $align;?>">
        <?php
            echo '('.$item->label.') ';
            echo format_text($item->name . $requiredmark, true, false, false);
            $imglink = new moodle_url('/mod/feedback/item/captcha/print_captcha.php', array('id'=>$cmid));
        ?>
            <img alt="<?php echo $this->type;?>" src="<?php echo $imglink->out();?>" />
        </td>
        <td valign="top" align="<?php echo $align;?>">
        <?php
        ?>
            <input type="text" name="<?php echo $item->typ . '_' . $item->id;?>"
                                    size="<?php echo $presentation;?>"
                                    maxlength="<?php echo $presentation;?>"
                                    value="" />
        </td>
        <?php
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
        global $SESSION, $CFG, $DB, $OUTPUT;

        $align = right_to_left() ? 'right' : 'left';

        $presentation = $item->presentation;
        $SESSION->feedback->item->captcha->charcount = $presentation;

        $cmid = 0;
        $feedbackid = $item->feedback;
        if($feedbackid > 0) {
            $feedback = $DB->get_record('feedback', array('id'=>$feedbackid));
            if($cm = get_coursemodule_from_instance("feedback", $feedback->id, $feedback->course)) {
                $cmid = $cm->id;
            }
        }

        if(isset($SESSION->feedback->item->captcha->checked)) {
            $checked = $SESSION->feedback->item->captcha->checked == true;
            unset($SESSION->feedback->item->captcha->checked);
        }else {
            $checked = false;
        }

        //check if an false value even the value is not required
        if(!$item->required AND $value != '' AND $SESSION->feedback->item->captcha->checkchar != $value) {
            $falsevalue = true;
        }else {
            $falsevalue = false;
        }

        if(($highlightrequire AND $item->required AND !$checked) OR $falsevalue) {
            $highlight = 'bgcolor="#FFAAAA" class="missingrequire"';
        }else {
            $highlight = '';
        }
        $requiredmark = ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        ?>
        <td <?php echo $highlight;?> valign="top" align="<?php echo $align;?>">
        <?php
            echo format_text($item->name . $requiredmark, true, false, false);
            $imglink = new moodle_url('/mod/feedback/item/captcha/print_captcha.php', array('id'=>$cmid));
        ?>
            <img alt="<?php echo $this->type;?>" src="<?php echo $imglink->out();?>" />
        </td>
        <td valign="top" align="<?php echo $align;?>">
            <input type="text" name="<?php echo $item->typ . '_' . $item->id;?>"
                                    size="<?php echo $presentation;?>"
                                    maxlength="<?php echo $presentation;?>"
                                    value="" />
        </td>
        <?php
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
        global $SESSION, $CFG, $DB, $OUTPUT;

        $align = right_to_left() ? 'right' : 'left';

        $presentation = $item->presentation;
        $SESSION->feedback->item->captcha->charcount = $presentation;

        $cmid = 0;

        if(isset($SESSION->feedback->item->captcha->checked)) {
            $checked = $SESSION->feedback->item->captcha->checked == true;
            unset($SESSION->feedback->item->captcha->checked);
        }else {
            $checked = false;
        }

        $requiredmark = ($item->required == 1)?'<span class="feedback_required_mark">*</span>':'';
        ?>
        <td valign="top" align="<?php echo $align;?>">
        <?php
            echo '('.$item->label.') ';
            echo format_text($item->name . $requiredmark, true, false, false);
            $imglink = new moodle_url('/mod/feedback/item/captcha/print_captcha.php', array('id'=>$cmid));
        ?>
            <img alt="<?php echo $this->type;?>" src="<?php echo $imglink->out();?>" />
        </td>
        <td valign="top" align="<?php echo $align;?>">
        <?php
        echo $OUTPUT->box_start('generalbox boxalign'.$align);
        echo $value ? $value : '&nbsp;';
        echo $OUTPUT->box_end();
        ?>
        </td>
        <?php
    }


    function check_value($value, $item) {
        global $SESSION;
        //if the item is not required, so the check is true if no value is given
        if((!isset($value) OR $value == '') AND $item->required != 1) return true;
        if($SESSION->feedback->item->captcha->checkchar == $value) {
            $SESSION->feedback->item->captcha->checked = true;
            return true;
        }
        return false;
    }

    function create_value($data) {
        $data = clean_text($data);
        return $data;
    }

    function get_presentation($data) {
        return $data->count_of_nums;
    }

    function get_hasvalue() {
        return 1;
    }
}
?>
