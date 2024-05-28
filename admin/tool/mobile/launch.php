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
 * Launch page, launch the app using custom URL schemes.
 *
 * If the user is not logged when visiting this page, he will be redirected to the login page.
 * Once he is logged, he will be redirected again to this page and the app launched via custom URL schemes.
 *
 * @package    tool_mobile
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

$serviceshortname  = required_param('service',  PARAM_ALPHANUMEXT);
$passport          = required_param('passport',  PARAM_RAW);    // Passport send from the app to validate the response URL.
$urlscheme         = optional_param('urlscheme', 'moodlemobile', PARAM_NOTAGS); // The URL scheme the app supports.
$confirmed         = optional_param('confirmed', false, PARAM_BOOL);  // If we are being redirected after user confirmation.
$oauthsso          = optional_param('oauthsso', 0, PARAM_INT); // Id of the OpenID issuer (for OAuth direct SSO).

// Validate that the urlscheme is valid.
if (!preg_match('/^[a-zA-Z][a-zA-Z0-9-\+\.]*$/', $urlscheme)) {
    throw new moodle_exception('Invalid parameter: the value of urlscheme isn\'t valid. ' .
            'It should start with a letter and can only contain letters, numbers and the characters "." "+" "-".');
}

// Check web services enabled.
if (!$CFG->enablewebservices) {
    throw new moodle_exception('enablewsdescription', 'webservice');
}

// Check if the service exists and is enabled.
$service = $DB->get_record('external_services', ['shortname' => $serviceshortname, 'enabled' => 1]);
if (empty($service)) {
    throw new moodle_exception('servicenotavailable', 'webservice');
}

// Set a cookie indicating that there was a launch to authenticate via the site from the app.
$ldata = json_encode(['service' => $serviceshortname, 'passport' => $passport,
    'urlscheme' => $urlscheme, 'confirmed' => (int) $confirmed, 'oauthsso' => $oauthsso]);
$expires = time() + (15 * MINSECS); // 15 minutes for authentication should be enough.
setcookie('tool_mobile_launch', $ldata, $expires, $CFG->sessioncookiepath, $CFG->sessioncookiedomain);

// We have been requested to start a SSO process via OpenID.
if (!empty($oauthsso) && is_enabled_auth('oauth2')) {
    $wantsurl = new moodle_url('/admin/tool/mobile/launch.php',
        array('service' => $serviceshortname, 'passport' => $passport, 'urlscheme' => $urlscheme, 'confirmed' => $confirmed));
    $oauthurl = new moodle_url('/auth/oauth2/login.php',
        array('id' => $oauthsso, 'sesskey' => sesskey(), 'wantsurl' => $wantsurl));
    header('Location: ' . $oauthurl->out(false));
    die;
}

// Check if the plugin is properly configured.
$typeoflogin = get_config('tool_mobile', 'typeoflogin');
if (empty($SESSION->justloggedin) &&
        !is_enabled_auth('oauth2') &&
        $typeoflogin != tool_mobile\api::LOGIN_VIA_BROWSER &&
        $typeoflogin != tool_mobile\api::LOGIN_VIA_EMBEDDED_BROWSER) {
    throw new moodle_exception('pluginnotenabledorconfigured', 'tool_mobile');
}

// If the user is using the inapp (embedded) browser, we need to set the Secure and Partitioned attributes to the session cookie.
if (\core_useragent::is_moodle_app()) {
    \core\session\utility\cookie_helper::add_attributes_to_cookie_response_header(
        cookiename: "MoodleSession{$CFG->sessioncookie}",
        attributes: ['Secure', 'Partitioned'],
    );
}

require_login(0, false);

// Require an active user: not guest, not suspended.
core_user::require_active_user($USER);

// Remove cookie.
unset($_COOKIE['tool_mobile_launch']);
setcookie('tool_mobile_launch', '', -1, $CFG->sessioncookiepath);

// Get an existing token or create a new one.
$timenow = time();
$token = \core_external\util::generate_token_for_current_user($service);
$privatetoken = $token->privatetoken;
\core_external\util::log_token_request($token);

// Don't return the private token if the user didn't just log in and a new token wasn't created.
if (empty($SESSION->justloggedin) and $token->timecreated < $timenow) {
    $privatetoken = null;
}

$siteadmin = has_capability('moodle/site:config', context_system::instance(), $USER->id);

// Passport is generated in the mobile app, so the app opening can be validated using that variable.
// Passports are valid only one time, it's deleted in the app once used.
$siteid = md5($CFG->wwwroot . $passport);
$apptoken = $siteid . ':::' . $token->token;
if ($privatetoken and is_https() and !$siteadmin) {
    $apptoken .= ':::' . $privatetoken;
}

$apptoken = base64_encode($apptoken);

// Redirect using the custom URL scheme checking first if a URL scheme is forced in the site settings.
$forcedurlscheme = get_config('tool_mobile', 'forcedurlscheme');
if (!empty($forcedurlscheme)) {
    $urlscheme = $forcedurlscheme;
}

$location = "$urlscheme://token=$apptoken";

// For iOS 10 onwards, we have to simulate a user click.
// If we come from the confirmation page, we should display a nicer page.
$isios = core_useragent::is_ios();
if ($confirmed or $isios) {
    $PAGE->set_context(context_system::instance());
    $PAGE->set_heading($COURSE->fullname);
    $params = array('service' => $serviceshortname, 'passport' => $passport, 'urlscheme' => $urlscheme, 'confirmed' => $confirmed);
    $PAGE->set_url("/$CFG->admin/tool/mobile/launch.php", $params);

    echo $OUTPUT->header();
    if ($confirmed) {
        $confirmedstr = get_string('confirmed');
        $PAGE->navbar->add($confirmedstr);
        $PAGE->set_title($confirmedstr);
        echo $OUTPUT->notification($confirmedstr, \core\output\notification::NOTIFY_SUCCESS);
        echo $OUTPUT->box_start('generalbox centerpara boxwidthnormal boxaligncenter');
        echo $OUTPUT->single_button(new moodle_url('/course/'), get_string('courses'));
        echo $OUTPUT->box_end();
    }

    $notice = get_string('clickheretolaunchtheapp', 'tool_mobile');
    echo $OUTPUT->box(html_writer::link($location, $notice, ['id' => 'launchapp']), 'generalbox warning centerpara');
    echo html_writer::script(
        "window.onload = function() {
            document.getElementById('launchapp').click();
        };"
    );
    echo $OUTPUT->footer();
} else {
    // For Android a http redirect will do fine.
    header('Location: ' . $location);
    die;
}
