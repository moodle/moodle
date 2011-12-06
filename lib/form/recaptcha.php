<?php
require_once('HTML/QuickForm/input.php');

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * textarea_counter.php
 *
 * @category  Admin
 * @package   admin
 * @author    Nicolas Connault <nicolasconnault@gmail.com>
 */


/**
 * @category Admin
 * @package  admin
 */
class MoodleQuickForm_recaptcha extends HTML_QuickForm_input {

    /**
     * html for help button, if empty then no help
     *
     * @var string
     */
    var $_helpbutton='';

    var $_https=false;

    /**
     * <code>
     * $form->addElement('textarea_counter', 'message', 'Message',
     *   array('cols'=>60, 'rows'=>10), 160);
     * </code>
     */
    function MoodleQuickForm_recaptcha($elementName = null, $elementLabel = null, $attributes = null) {
        global $CFG;
        parent::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
        $this->_type = 'recaptcha';
        if (!empty($attributes['https']) or strpos($CFG->httpswwwroot, 'https:') === 0) {
            $this->_https = true;
        } else {
            $this->_https = false;
        }
    }

    /**
     * Returns the recaptcha element in HTML
     *
     * @since     1.0
     * @access    public
     * @return    string
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
     * set html for help button
     *
     * @access   public
     * @param array $help array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    function setHelpButton($helpbuttonargs, $function='helpbutton'){
        debugging('component setHelpButton() is not used any more, please use $mform->setHelpButton() instead');
    }

    /**
     * get html for help button
     *
     * @access   public
     * @return  string html for help button
     */
    function getHelpButton(){
        return $this->_helpbutton;
    }

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
