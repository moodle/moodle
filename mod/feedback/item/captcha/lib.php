<?php
defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_captcha extends feedback_item_base {
    var $type = "captcha";
    function init() {

    }

/**
 * Build the editform for the item
 *
 * @global object
 * @param object $item the instance of the recordset feedback_item
 * @param array $commonparams all hidden values needed in the form
 * @param array $positionlist this array build the selection list for itemposition
 * @param int $position the current itemposition
 * @return object instance of the built form
 */
    function show_edit($item, $commonparams, $positionlist, $position) {
        global $CFG;

        require_once('captcha_form.php');

        $item_form = new feedback_captcha_form('edit_item.php', array('item'=>$item, 'common'=>$commonparams, 'positionlist'=>$positionlist, 'position'=>$position));

        $item->presentation = empty($item->presentation) ? 3 : $item->presentation;
        $item->name = empty($item->name) ? '' : $item->name;
        $item->label = empty($item->label) ? '' : $item->label;

        $item->required = isset($item->required) ? $item->required : 1;
        if($item->required) {
            $item_form->requiredcheck->setValue(true);
        }

        $item_form->itemname->setValue($item->name);
        $item_form->itemlabel->setValue($item->label);

        $item_form->select->setValue($item->presentation);
        return $item_form;
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

    function print_item($item, $value = false, $readonly = false, $edit = false, $highlightrequire = false){
        global $SESSION, $CFG, $DB, $OUTPUT;

        $align = get_string('thisdirection') == 'ltr' ? 'left' : 'right';

        $presentation = $item->presentation;
        $SESSION->feedback->item->captcha->charcount = $presentation;

        $cmid = 0;
        if(!$readonly) {
            $feedbackid = $item->feedback;
            if($feedbackid > 0) {
                $feedback = $DB->get_record('feedback', array('id'=>$feedbackid));
                if($cm = get_coursemodule_from_instance("feedback", $feedback->id, $feedback->course)) {
                    $cmid = $cm->id;
                }
            }
        }

        if(isset($SESSION->feedback->item->captcha->checked)) {
            $checked = $SESSION->feedback->item->captcha->checked == true;
            unset($SESSION->feedback->item->captcha->checked);
        }else {
            $checked = false;
        }

        //check if an false value even the value is not required
        if(!$readonly AND !$item->required AND $value != '' AND $SESSION->feedback->item->captcha->checkchar != $value) {
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
            if($edit OR $readonly) {
                echo '('.$item->label.') ';
            }
            echo format_text($item->name . $requiredmark, true, false, false);
            $imglink = new moodle_url('/mod/feedback/item/captcha/print_captcha.php', array('id'=>$cmid));
        ?>
            <img alt="<?php echo $this->type;?>" src="<?php echo $imglink->out();?>" />
        </td>
        <td valign="top" align="<?php echo $align;?>">
    <?php
        if($readonly){
            echo $OUTPUT->box_start('generalbox boxalign'.$align);
            echo $value ? $value : '&nbsp;';
            echo $OUTPUT->box_end();
        }else {
    ?>
            <input type="text" name="<?php echo $item->typ . '_' . $item->id;?>"
                                    size="<?php echo $presentation;?>"
                                    maxlength="<?php echo $presentation;?>"
                                    value="" />
    <?php
        }
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
