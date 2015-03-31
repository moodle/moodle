<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') OR die('not allowed');
require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_class.php');

class feedback_item_captcha extends feedback_item_base {
    protected $type = "captcha";
    private $commonparams;
    private $item_form = false;
    private $item = false;
    private $feedback = false;

    public function init() {

    }

    public function build_editform($item, $feedback, $cm) {
        global $DB;

        $editurl = new moodle_url('/mod/feedback/edit.php', array('id'=>$cm->id));

        //ther are no settings for recaptcha
        if (isset($item->id) AND $item->id > 0) {
            notice(get_string('there_are_no_settings_for_recaptcha', 'feedback'), $editurl->out());
            exit;
        }

        //only one recaptcha can be in a feedback
        $params = array('feedback' => $feedback->id, 'typ' => $this->type);
        if ($DB->record_exists('feedback_item', $params)) {
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

    public function show_editform() {
    }

    public function is_cancelled() {
        return false;
    }

    public function get_data() {
        return true;
    }

    public function save_item() {
        global $DB;

        if (!$this->item) {
            return false;
        }

        if (empty($this->item->id)) {
            $this->item->id = $DB->insert_record('feedback_item', $this->item);
        } else {
            $DB->update_record('feedback_item', $this->item);
        }

        return $DB->get_record('feedback_item', array('id'=>$this->item->id));
    }

    //liefert eine Struktur ->name, ->data = array(mit Antworten)
    public function get_analysed($item, $groupid = false, $courseid = false) {
        return null;
    }

    public function get_printval($item, $value) {
        return '';
    }

    public function print_analysed($item, $itemnr = '', $groupid = false, $courseid = false) {
        return $itemnr;
    }

    public function excelprint_item(&$worksheet, $row_offset,
                             $xls_formats, $item,
                             $groupid, $courseid = false) {
        return $row_offset;
    }

    /**
     * print the item at the edit-page of feedback
     *
     * @global object
     * @param object $item
     * @return void
     */
    public function print_item_preview($item) {
        global $DB, $OUTPUT;

        $align = right_to_left() ? 'right' : 'left';

        $cmid = 0;
        $feedbackid = $item->feedback;
        if ($feedbackid > 0) {
            $feedback = $DB->get_record('feedback', array('id'=>$feedbackid));
            $cm = get_coursemodule_from_instance("feedback", $feedback->id, $feedback->course);
            if ($cm) {
                $cmid = $cm->id;
            }
        }

        $requiredmark = '<img class="req" title="'.get_string('requiredelement', 'form').'" alt="'.
            get_string('requiredelement', 'form').'" src="'.$OUTPUT->pix_url('req') .'" />';

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
    public function print_item_complete($item, $value = '', $highlightrequire = false) {
        global $SESSION, $CFG, $DB, $USER, $OUTPUT;
        require_once($CFG->libdir.'/recaptchalib.php');

        $align = right_to_left() ? 'right' : 'left';

        $cmid = 0;
        $feedbackid = $item->feedback;
        if ($feedbackid > 0) {
            $feedback = $DB->get_record('feedback', array('id'=>$feedbackid));
            $cm = get_coursemodule_from_instance("feedback", $feedback->id, $feedback->course);
            if ($cm) {
                $cmid = $cm->id;
            }
        }

        //check if an false value even the value is not required
        if ($highlightrequire AND !$this->check_value($value, $item)) {
            $falsevalue = true;
        } else {
            $falsevalue = false;
        }

        if ($falsevalue) {
            $highlight = '<br class="error"><span id="id_error_recaptcha_response_field" class="error"> '.
                get_string('err_required', 'form').'</span><br id="id_error_break_recaptcha_response_field" class="error" >';
        } else {
            $highlight = '';
        }
        $requiredmark = '<img class="req" title="'.get_string('requiredelement', 'form').'" alt="'.
            get_string('requiredelement', 'form').'" src="'.$OUTPUT->pix_url('req') .'" />';

        if (isset($SESSION->feedback->captchacheck) AND
                $SESSION->feedback->captchacheck == $USER->sesskey AND
                $value == $USER->sesskey) {

            //print the question and label
            echo '<div class="feedback_item_label_'.$align.'">';
            echo '('.$item->label.') ';
            echo format_text($item->name.$requiredmark, true, false, false);
            $inputname = 'name="'.$item->typ.'_'.$item->id.'"';
            echo '<input type="hidden" value="'.$USER->sesskey.'" '.$inputname.' />';
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

        <div id="recaptcha_widget" style="display:none">

        <div id="recaptcha_image"></div>
        <div class="recaptcha_only_if_incorrect_sol" style="color:red">'.
        $strincorrectpleasetryagain.
        '</div>
        <span class="recaptcha_only_if_image">
        <label for="recaptcha_response_field">'.$strenterthewordsabove.$requiredmark.'</label>
        </span>
        <span class="recaptcha_only_if_audio">
        <label for="recaptcha_response_field">'.$strenterthenumbersyouhear.'</label>
        </span>
        <label for="recaptcha_response_field">'.$highlight.'</label>

        <input type="text" id="recaptcha_response_field" name="'.$item->typ.'_'.$item->id.'" />

        <div><a href="javascript:Recaptcha.reload()">' . $strgetanothercaptcha . '</a></div>
        <div class="recaptcha_only_if_image">
        <a href="javascript:Recaptcha.switch_type(\'audio\')">' . $strgetanaudiocaptcha . '</a>
        </div>
        <div class="recaptcha_only_if_audio">
        <a href="javascript:Recaptcha.switch_type(\'image\')">' . $strgetanimagecaptcha . '</a>
        </div>
        </div>';

        // Check if we are using SSL.
        if (is_https()) {
            $ssl = true;
        } else {
            $ssl = false;
        }

        //we have to rename the challengefield
        if (!empty($CFG->recaptchaprivatekey) AND !empty($CFG->recaptchapublickey)) {
            $captchahtml = recaptcha_get_html($CFG->recaptchapublickey, null, $ssl);
            echo $html.$captchahtml;
        }
    }

    /**
     * print the item at the complete-page of feedback
     *
     * @global object
     * @param object $item
     * @param string $value
     * @return void
     */
    public function print_item_show_value($item, $value = '') {
        global $DB;

        $align = right_to_left() ? 'right' : 'left';

        $cmid = 0;
        $feedbackid = $item->feedback;
        if ($feedbackid > 0) {
            $feedback = $DB->get_record('feedback', array('id'=>$feedbackid));
            if ($cm = get_coursemodule_from_instance("feedback", $feedback->id, $feedback->course)) {
                $cmid = $cm->id;
            }
        }

        $requiredmark = '<img class="req" title="'.get_string('requiredelement', 'form').'" alt="'.
            get_string('requiredelement', 'form').'" src="'.$OUTPUT->pix_url('req') .'" />';

        //print the question and label
        echo '<div class="feedback_item_label_'.$align.'">';
        echo '('.$item->label.') ';
        echo format_text($item->name.$requiredmark, true, false, false);
        echo '</div>';
    }


    public function check_value($value, $item) {
        global $SESSION, $CFG, $USER;
        require_once($CFG->libdir.'/recaptchalib.php');

        //is recaptcha configured in moodle?
        if (empty($CFG->recaptchaprivatekey) OR empty($CFG->recaptchapublickey)) {
            return true;
        }
        $challenge = optional_param('recaptcha_challenge_field', '', PARAM_RAW);

        if ($value == $USER->sesskey AND $challenge == '') {
            return true;
        }
        $remoteip = getremoteaddr(null);
        $response = recaptcha_check_answer($CFG->recaptchaprivatekey, $remoteip, $challenge, $value);
        if ($response->is_valid) {
            $SESSION->feedback->captchacheck = $USER->sesskey;
            return true;
        }
        unset($SESSION->feedback->captchacheck);

        return false;
    }

    public function create_value($data) {
        global $USER;
        return $USER->sesskey;
    }

    //compares the dbvalue with the dependvalue
    //dbvalue is value stored in the db
    //dependvalue is the value to check
    public function compare_value($item, $dbvalue, $dependvalue) {
        if ($dbvalue == $dependvalue) {
            return true;
        }
        return false;
    }

    public function get_presentation($data) {
        return '';
    }

    public function get_hasvalue() {
        global $CFG;

        //is recaptcha configured in moodle?
        if (empty($CFG->recaptchaprivatekey) OR empty($CFG->recaptchapublickey)) {
            return 0;
        }
        return 1;
    }

    public function can_switch_require() {
        return false;
    }

    public function value_type() {
        return PARAM_RAW;
    }

    public function clean_input_value($value) {
        return clean_param($value, $this->value_type());
    }
}
