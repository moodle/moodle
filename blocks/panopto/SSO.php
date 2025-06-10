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
 * manages the single sign on logic between Panopto and Moodle
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2016 /With contributions from Spenser Jones (sjones@ambrose.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This can't be defined Moodle internal because it is called from Panopto to authorize login.

// @codingStandardsIgnoreLine
global $CFG;
if (empty($CFG)) {
    // @codingStandardsIgnoreLine
    require_once(dirname(__FILE__) . '/../../config.php');
}
require_once($CFG->libdir . '/weblib.php');
require_once(dirname(__FILE__) . '/lib/block_panopto_lib.php');

$servername = required_param('serverName', PARAM_HOST);
$callbackurl = required_param('callbackURL', PARAM_URL);
$configuredserverarray = panopto_get_configured_panopto_servers();

$callbackverified = false;
foreach ($configuredserverarray as $possibleserver) {
    if (strcasecmp($possibleserver, $servername) == 0) {
        $callbackhost = parse_url($callbackurl, PHP_URL_HOST);

        if (stripos($callbackhost, $servername) !== false) {
            $callbackverified = true;
            break;
        }
    }
}

if ($callbackverified) {
    if (strpos($callbackurl, 'http%') !== false
     || strpos($callbackurl, 'https%') !== false) {
        $callbackurl = urldecode($callbackurl);
    }

    // A float doesn't have the required precision.
    $expiration = preg_replace('/[^0-9\.]/', '', required_param('expiration', PARAM_RAW));

    $requestauthcode = required_param('authCode', PARAM_ALPHANUM);
    $action = optional_param('action', '', PARAM_ALPHA);

    $relogin = ($action == 'relogin');

    if ($relogin || (isset($USER->username) && ($USER->username == 'guest'))) {
        require_logout();

        // Return to this page, minus the "action=relogin" parameter.
        redirect($CFG->wwwroot . '/blocks/panopto/SSO.php' .
                "?authCode=$requestauthcode" .
                "&serverName=$servername" .
                "&expiration=$expiration" .
                '&callbackURL=' . urlencode($callbackurl));
        return;
    }

    // No course ID (0). Don't autologin guests (false).
    require_login(0, false);

    // Reproduce canonically-ordered incoming auth payload.
    $requestauthpayload = 'serverName=' . $servername . '&expiration=' . $expiration;

    // Verify passed in parameters are properly signed.
    if (panopto_validate_auth_code($requestauthpayload, $requestauthcode)) {
        $userkey = panopto_decorate_username($USER->username);


        $selectedssotype = get_config('block_panopto', 'sso_sync_type');
        $ssosynctask = new \block_panopto\task\sync_user_login();

        $targetserver = new stdClass();
        $targetserver->name = $servername;
        $targetserver->appkey = panopto_get_app_key($servername);

        $ssosynctask->set_custom_data([
            'userid' => $USER->id,
            'targetserver' => $targetserver,
        ]);

        switch ($selectedssotype) {
            case 'sync':
                $ssosynctask->execute();
            break;
            case 'asyncsync':
                \core\task\manager::queue_adhoc_task($ssosynctask);
            break;
        }

        // Strip ReturnUrl so we can append it on the end.
        parse_str(parse_url($callbackurl, PHP_URL_QUERY), $params);
        $returnurl = isset($params['ReturnUrl']) ? $params['ReturnUrl'] : "";

        // Handle fragment with hash.
        $fragment = "#";
        if (strpos($callbackurl, "#") !== false) {
            $fragment .= strtok(explode("#", $callbackurl)[1], '&');
        } else {
            $fragment = "";
        }

        // Strip ReturnUrl from original URL and clean if necessary.
        // This is constant LoginPageRedirectParamKey = "ReturnUrl" so it should always arrive in this format.
        $url = preg_replace('/&?ReturnUrl=[^&]*/', '', $callbackurl);
        $url = str_replace('?&', '?', $url);
        $url = rtrim($url, '?');

        // Generate canonically-ordered auth payload string.
        $responseparams = 'serverName=' . $servername . '&externalUserKey=' . $userkey . '&expiration=' . $expiration;
        // Sign payload with shared key and hash.
        $responseauthcode = panopto_generate_auth_code($responseparams);

        // Encode user key in case the backslash causes a sequence to be interpreted as an escape sequence.
        // (e.g. in the case of usernames that begin with digits).
        // Maintain the original canonical string to avoid signature mismatch.
        $responseparamsencoded =
            'serverName=' . $servername . '&externalUserKey=' . urlencode($userkey) . '&expiration=' . $expiration;

        $fullreturnurl = !empty($returnurl) ? '&ReturnUrl=' . urlencode($returnurl) . $fragment : "";
        $separator = (strpos($url, '?') ? '&' : '?');
        $redirecturl = $url . $separator . $responseparamsencoded . '&authCode=' . $responseauthcode . $fullreturnurl;

        // Redirect to Panopto Focus login page.
        redirect($redirecturl);
    } else {
        echo $OUTPUT->header();

        echo get_string('sso_invalid_authcode', 'block_panopto');

        echo $OUTPUT->footer();
    }
} else {
        echo $OUTPUT->header();

        echo get_string('sso_invalid_server', 'block_panopto');

        echo $OUTPUT->footer();
}
/* End of file SSO.php */
