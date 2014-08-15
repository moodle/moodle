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


/**
 * recaptcha type form element
 *
 * Contains HTML class for a recaptcha type element
 *
 * @package   core_form
 * @copyright 2008 Nicolas Connault <nicolasconnault@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('HTML/QuickForm/input.php');

/**
 * recaptcha type form element
 *
 * HTML class for a recaptcha type element
 *
 * @package   core_form
 * @category  form
 * @copyright 2008 Nicolas Connault <nicolasconnault@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_recaptcha extends HTML_QuickForm_input {

    /** @var string html for help button, if empty then no help */
    var $_helpbutton='';

    /** @var bool if true, recaptcha will be servered from https */
    var $_https=false;

    /**
     * constructor
     *
     * @param string $elementName (optional) name of the recaptcha element
     * @param string $elementLabel (optional) label for recaptcha element
     * @param mixed $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     */
    function MoodleQuickForm_recaptcha($elementName = null, $elementLabel = null, $attributes = null) {
        global $CFG;
        parent::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
        $this->_type = 'recaptcha';
        if (is_https()) {
            $this->_https = true;
        } else {
            $this->_https = false;
        }
    }

    /**
     * Returns the recaptcha element in HTML
     *
     * @return string
     */
    function toHtml() {
        global $CFG, $PAGE;
        require_once $CFG->libdir . '/recaptchalib.php';

        $recaptureoptions = Array('theme'=>'custom', 'custom_theme_widget'=>'recaptcha_widget');
        $html = html_writer::script(js_writer::set_variable('RecaptchaOptions', $recaptureoptions));

        $attributes = $this->getAttributes();
        if (empty($attributes['error_message'])) {
            $attributes['error_message'] = null;
            $this->setAttributes($attributes);
        }
        $error = $attributes['error_message'];
        unset($attributes['error_message']);

        $strincorrectpleasetryagain = get_string('incorrectpleasetryagain', 'auth');
        $strenterthewordsabove = get_string('enterthewordsabove', 'auth');
        $strenterthenumbersyouhear = get_string('enterthenumbersyouhear', 'auth');
        $strgetanothercaptcha = get_string('getanothercaptcha', 'auth');
        $strgetanaudiocaptcha = get_string('getanaudiocaptcha', 'auth');
        $strgetanimagecaptcha = get_string('getanimagecaptcha', 'auth');

        $html .= '
<div id="recaptcha_widget" style="display:none">

<div id="recaptcha_image"></div>
<div class="recaptcha_only_if_incorrect_sol" style="color:red">' . $strincorrectpleasetryagain . '</div>

<span class="recaptcha_only_if_image"><label for="recaptcha_response_field">' . $strenterthewordsabove . '</label></span>
<span class="recaptcha_only_if_audio"><label for="recaptcha_response_field">' . $strenterthenumbersyouhear . '</label></span>

<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />
<input type="hidden" name="recaptcha_element" value="dummyvalue" /> <!-- Dummy value to fool formslib -->
<div><a href="javascript:Recaptcha.reload()">' . $strgetanothercaptcha . '</a></div>
<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type(\'audio\')">' . $strgetanaudiocaptcha . '</a></div>
<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type(\'image\')">' . $strgetanimagecaptcha . '</a></div>
</div>';

        return $html . recaptcha_get_html($CFG->recaptchapublickey, $error, $this->_https);
    }

    /**
     * get html for help button
     *
     * @return string html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }

    /**
     * Checks input and challenged field
     *
     * @param string $challenge_field recaptcha shown  to user
     * @param string $response_field input value by user
     * @return bool
     */
    function verify($challenge_field, $response_field) {
        global $CFG;
        require_once $CFG->libdir . '/recaptchalib.php';
        $response = recaptcha_check_answer($CFG->recaptchaprivatekey,
                                           getremoteaddr(),
                                           $challenge_field,
                                           $response_field,
                                           $this->_https);
        if (!$response->is_valid) {
            $attributes = $this->getAttributes();
            $attributes['error_message'] = $response->error;
            $this->setAttributes($attributes);
            return $response->error;
        }
        return true;
    }
}
