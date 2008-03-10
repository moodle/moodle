<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * textarea_counter.php
 *
 * @category  Admin
 * @package   admin
 * @author    Nicolas Connault <nicolasconnault@gmail.com>
 * @version   $Id$
 */


/**
 * @category Admin
 * @package  admin
 */
class MoodleQuickForm_recaptcha extends HTML_QuickForm_input {

    /**
     * <code>
     * $form->addElement('textarea_counter', 'message', 'Message',
     *   array('cols'=>60, 'rows'=>10), 160);
     * </code>
     */
    function HTML_QuickForm_recaptcha($elementName = null, $elementLabel = null, $attributes = null) {
        parent::HTML_QuickForm_input($elementName, $elementLabel, $attributes);
        $this->_type = 'recaptcha';
    }

    /**
     * Returns the recaptcha element in HTML
     *
     * @since     1.0
     * @access    public
     * @return    string
     */
    function toHtml() {
        global $CFG;
        require_once $CFG->libdir . '/recaptchalib.php';

        $html = '<script type="text/javascript">
            var RecaptchaOptions = {
                theme : \'custom\',
                tabindex : 2,
                custom_theme_widget : \'recaptcha_widget\'
            };
              </script>' . "\n";

              
        if (empty($_SESSION['recaptcha_error'])) {
            $_SESSION['recaptcha_error'] = null;
        }
        $error = $_SESSION['recaptcha_error'];
        unset($_SESSION['recaptcha_error']);
        
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

<span class="recaptcha_only_if_image">' . $strenterthewordsabove . '</span>
<span class="recaptcha_only_if_audio">' . $strenterthenumbersyouhear . '</span>

<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />

<div><a href="javascript:Recaptcha.reload()">' . $strgetanothercaptcha . '</a></div>
<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type(\'audio\')">' . $strgetanaudiocaptcha . '</a></div>
<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type(\'image\')">' . $strgetanimagecaptcha . '</a></div>
</div>'; 

        return $html . recaptcha_get_html($CFG->recaptchapublickey, $error);
    }
}

?>
