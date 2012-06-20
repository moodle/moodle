<?php
defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_captcha extends feedback_item_base {
    var $type = "captcha";
    var $commonparams;
    var $item_form = false;
    var $item = false;
    var $feedback = false;

    function init() {

    }

    function build_editform($item, $feedback, $cm) {
        global $DB;

        $editurl = new moodle_url('/mod/feedback/edit.php', array('id'=>$cm->id));

        //ther are no settings for recaptcha
        if(isset($item->id) AND $item->id > 0) {
            notice(get_string('there_are_no_settings_for_recaptcha', 'feedback'), $editurl->out());
            exit;
        }

        //only one recaptcha can be in a feedback
        if($DB->record_exists('feedback_item', array('feedback'=>$feedback->id, 'typ'=>$this->type))) {
            notice(get_string('only_one_captcha_allowed', 'feedback'), $editurl->out());
            exit;
        }

        $this->item = $item;
        $this->feedback = $feedback;
        $this->item_form = true; //dummy

        $lastposition = $DB->count_records('feedback_item', array('feedback'=>$feedback->id));

        $this->item->feedback = $feedback->id;
        $this->item->template = 0;
        $this->item->name = get_string('captcha', 'feedback');
        $this->item->label = get_string('captcha', 'feedback');
        $this->item->presentation = '';
        $this->item->typ = $this->type;
        $this->item->hasvalue = $this->get_hasvalue();
        $this->item->position = $lastposition + 1;
        $this->item->required = 1;
        $this->item->dependitem = 0;
        $this->item->dependvalue = '';
        $this->item->options = '';
    }

    function show_editform() {
    }

    function is_cancelled() {
        return false;
    }

    function get_data() {
        return true;
    }

    function save_item() {
        global $DB;

        if(!$this->item) {
            return false;
        }

        if(empty($this->item->id)) {
            $this->item->id = $DB->insert_record('feedback_item', $this->item);
        }else {
            $DB->update_record('feedback_item', $this->item);
        }

        return $DB->get_record('feedback_item', array('id'=>$this->item->id));
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

    function excelprint_item(&$worksheet, $rowOffset, $xlsFormats, $item, $groupid, $courseid = false) {
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
        global $DB;

        $align = right_to_left() ? 'right' : 'left';

        $cmid = 0;
        $feedbackid = $item->feedback;
        if($feedbackid > 0) {
            $feedback = $DB->get_record('feedback', array('id'=>$feedbackid));
            if($cm = get_coursemodule_from_instance("feedback", $feedback->id, $feedback->course)) {
                $cmid = $cm->id;
            }
        }

        $requiredmark = '<span class="feedback_required_mark">*</span>';

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
        echo '('.$item->label.') ';
        echo format_text($item->name.$requiredmark, true, false, false);
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
        global $SESSION, $CFG, $DB, $USER;
        require_once($CFG->libdir.'/recaptchalib.php');

        $align = right_to_left() ? 'right' : 'left';

        $cmid = 0;
        $feedbackid = $item->feedback;
        if($feedbackid > 0) {
            $feedback = $DB->get_record('feedback', array('id'=>$feedbackid));
            if($cm = get_coursemodule_from_instance("feedback", $feedback->id, $feedback->course)) {
                $cmid = $cm->id;
            }
        }

        //check if an false value even the value is not required
        if($highlightrequire AND !$this->check_value($value, $item)) {
            $falsevalue = true;
        }else {
            $falsevalue = false;
        }

        if($falsevalue) {
            $highlight = 'missingrequire';
        }else {
            $highlight = '';
        }
        $requiredmark = '<span class="feedback_required_mark">*</span>';

        if(isset($SESSION->feedback->captchacheck) AND $SESSION->feedback->captchacheck == $USER->sesskey AND $value == $USER->sesskey) {
            //print the question and label
            echo '<div class="feedback_item_label_'.$align.'">';
            echo '('.$item->label.') ';
            echo format_text($item->name.$requiredmark, true, false, false);
            echo '<input type="hidden" value="'.$USER->sesskey.'" name="'.$item->typ.'_'.$item->id.'" />';
            echo '</div>';
            return;
        }

        $strincorrectpleasetryagain = get_string('incorrectpleasetryagain', 'auth');
        $strenterthewordsabove = get_string('enterthewordsabove', 'auth');
        $strenterthenumbersyouhear = get_string('enterthenumbersyouhear', 'auth');
        $strgetanothercaptcha = get_string('getanothercaptcha', 'auth');
        $strgetanaudiocaptcha = get_string('getanaudiocaptcha', 'auth');
        $strgetanimagecaptcha = get_string('getanimagecaptcha', 'auth');

        $recaptureoptions = Array('theme'=>'custom', 'custom_theme_widget'=>'recaptcha_widget');
        $html = html_writer::script(js_writer::set_variable('RecaptchaOptions', $recaptureoptions));
        $html .= '

        <div  class="'.$highlight.'" id="recaptcha_widget" style="display:none">

        <div id="recaptcha_image"></div>
        <div class="recaptcha_only_if_incorrect_sol" style="color:red">' . $strincorrectpleasetryagain . '</div>
        <span class="recaptcha_only_if_image"><label for="recaptcha_response_field">' . $strenterthewordsabove . $requiredmark. '</label></span>
        <span class="recaptcha_only_if_audio"><label for="recaptcha_response_field">' . $strenterthenumbersyouhear . '</label></span>

        <input type="text" id="recaptcha_response_field" name="'.$item->typ.'_'.$item->id.'" />

        <div><a href="javascript:Recaptcha.reload()">' . $strgetanothercaptcha . '</a></div>
        <div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type(\'audio\')">' . $strgetanaudiocaptcha . '</a></div>
        <div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type(\'image\')">' . $strgetanimagecaptcha . '</a></div>
        </div>';
        //we have to rename the challengefield
        $captchahtml = recaptcha_get_html($CFG->recaptchapublickey, NULL);
        echo $html.$captchahtml;
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
        global $DB;

        $align = right_to_left() ? 'right' : 'left';

        $cmid = 0;
        $feedbackid = $item->feedback;
        if($feedbackid > 0) {
            $feedback = $DB->get_record('feedback', array('id'=>$feedbackid));
            if($cm = get_coursemodule_from_instance("feedback", $feedback->id, $feedback->course)) {
                $cmid = $cm->id;
            }
        }

        $requiredmark = '<span class="feedback_required_mark">*</span>';

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
        echo '('.$item->label.') ';
        echo format_text($item->name.$requiredmark, true, false, false);
        echo '</div>';
    }


    function check_value($value, $item) {
        global $SESSION, $CFG, $USER;
        require_once($CFG->libdir.'/recaptchalib.php');

        $challenge = optional_param('recaptcha_challenge_field', '', PARAM_RAW);

        if($value == $USER->sesskey AND $challenge == '') {
            return true;
        }
        $remoteip = getremoteaddr(null);
        $response = recaptcha_check_answer($CFG->recaptchaprivatekey, $remoteip, $challenge, $value);
        if($response->is_valid) {
            $SESSION->feedback->captchacheck = $USER->sesskey;
            return true;
        }
        unset($SESSION->feedback->captchacheck);

        return false;
    }

    function create_value($data) {
        global $USER;
        return $USER->sesskey;
    }

    //compares the dbvalue with the dependvalue
    //dbvalue is value stored in the db
    //dependvalue is the value to check
    function compare_value($item, $dbvalue, $dependvalue) {
        if($dbvalue == $dependvalue) {
            return true;
        }
        return false;
    }

    function get_presentation($data) {
        return '';
    }

    function get_hasvalue() {
        return 1;
    }

    function can_switch_require() {
        return false;
    }

    function clean_input_value($value) {
        return clean_param($value, PARAM_RAW);
    }
}
