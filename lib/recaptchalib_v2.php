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
 * This is a PHP library that handles calling reCAPTCHA v2.
 *
 *    - Documentation
 *          {@link https://developers.google.com/recaptcha/docs/display}
 *    - Get a reCAPTCHA API Key
 *          {@link https://www.google.com/recaptcha/admin}
 *    - Discussion group
 *          {@link http://groups.google.com/group/recaptcha}
 *
 * @package core
 * @copyright 2018 Jeff Webster
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The reCAPTCHA URL's
 */
define('RECAPTCHA_API_URL', 'https://www.recaptcha.net/recaptcha/api.js');
define('RECAPTCHA_VERIFY_URL', 'https://www.recaptcha.net/recaptcha/api/siteverify');

/**
 * Returns the language code the reCAPTCHA element should use.
 * Google reCAPTCHA uses different language codes than Moodle so we must convert.
 * https://developers.google.com/recaptcha/docs/language
 *
 * @param string $lang Language to use. If not provided, get current language.
 * @return string A language code
 */
function recaptcha_lang($lang = null) {

    if (empty($lang)) {
        $lang = current_language();
    }

    $glang = $lang;
    switch ($glang) {
        case 'en':
            $glang = 'en-GB';
            break;
        case 'en_us':
            $glang = 'en';
            break;
        case 'zh_cn':
            $glang = 'zh-CN';
            break;
        case 'zh_tw':
            $glang = 'zh-TW';
            break;
        case 'fr_ca':
            $glang = 'fr-CA';
            break;
        case 'pt_br':
            $glang = 'pt-BR';
            break;
        case 'he':
            $glang = 'iw';
            break;
    }
    // For any language code that didn't change reduce down to the base language.
    if (($lang === $glang) and (strpos($lang, '_') !== false)) {
        list($glang, $trash) = explode('_', $lang, 2);
    }
    return $glang;
}

/**
 * Gets the challenge HTML
 * This is called from the browser, and the resulting reCAPTCHA HTML widget
 * is embedded within the HTML form it was called from.
 *
 * @param string $apiurl URL for reCAPTCHA API
 * @param string $pubkey The public key for reCAPTCHA
 * @param string $lang Language to use. If not provided, get current language.
 * @return string - The HTML to be embedded in the user's form.
 */
function recaptcha_get_challenge_html($apiurl, $pubkey, $lang = null) {
    global $CFG, $PAGE;

    // To use reCAPTCHA you must have an API key.
    if ($pubkey === null || $pubkey === '') {
        return get_string('getrecaptchaapi', 'auth');
    }

    $jscode = "
        var recaptchacallback = function() {
            grecaptcha.render('recaptcha_element', {
              'sitekey' : '$pubkey'
            });
        }";

    $lang = recaptcha_lang($lang);
    $apicode = "\n<script type=\"text/javascript\" ";
    $apicode .= "src=\"$apiurl?onload=recaptchacallback&render=explicit&hl=$lang\" async defer>";
    $apicode .= "</script>\n";

    $return = html_writer::script($jscode, '');
    $return .= html_writer::div('', 'recaptcha_element', array('id' => 'recaptcha_element'));
    $return .= $apicode;

    return $return;
}

/**
 * Calls an HTTP POST function to verify if the user's response was correct
 *
 * @param string $verifyurl URL for reCAPTCHA verification
 * @param string $privkey The private key for reCAPTCHA
 * @param string $remoteip The user's IP
 * @param string $response The response from reCAPTCHA
 * @return ReCaptchaResponse
 */
function recaptcha_check_response($verifyurl, $privkey, $remoteip, $response) {
    global $CFG;
    require_once($CFG->libdir.'/filelib.php');

    // Check response - isvalid boolean, error string.
    $checkresponse = array('isvalid' => false, 'error' => 'check-not-started');

    // To use reCAPTCHA you must have an API key.
    if ($privkey === null || $privkey === '') {
        $checkresponse['isvalid'] = false;
        $checkresponse['error'] = 'no-apikey';
        return $checkresponse;
    }

    // For security reasons, you must pass the remote ip to reCAPTCHA.
    if ($remoteip === null || $remoteip === '') {
        $checkresponse['isvalid'] = false;
        $checkresponse['error'] = 'no-remoteip';
        return $checkresponse;
    }

    // Discard spam submissions.
    if ($response === null || strlen($response) === 0) {
        $checkresponse['isvalid'] = false;
        $checkresponse['error'] = 'incorrect-captcha-sol';
        return $checkresponse;
    }

    $params = array('secret' => $privkey, 'remoteip' => $remoteip, 'response' => $response);
    $curl = new curl();
    $curlresponse = $curl->post($verifyurl, $params);

    if ($curl->get_errno() === 0) {
        $curldata = json_decode($curlresponse);

        if (isset($curldata->success) && $curldata->success === true) {
            $checkresponse['isvalid'] = true;
            $checkresponse['error'] = '';
        } else {
            $checkresponse['isvalid'] = false;
            $checkresponse['error'] = $curldata->{error-codes};
        }
    } else {
        $checkresponse['isvalid'] = false;
        $checkresponse['error'] = 'check-failed';
    }
    return $checkresponse;
}

