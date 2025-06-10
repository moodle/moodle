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
 * Wrapper around mod/lti/auth so we can do our custom auth.
 *
 * @package block_panopto
 * @copyright  Panopto 2009 - 2016 /With contributions from Spenser Jones (sjones@ambrose.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// @codingStandardsIgnoreLine
global $CFG;
if (empty($CFG)) {
    // @codingStandardsIgnoreLine
    require_once(dirname(__FILE__) . '/../../../../config.php');
}
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once($CFG->libdir . '/weblib.php');
require_once(dirname(__FILE__) . '/panoptoblock_lti_utility.php');
require_once(dirname(__FILE__) . '../../panopto_data.php');

global $_POST, $_SERVER;

if (!isloggedin() && empty($_POST['repost'])) {
    header_remove("Set-Cookie");
    $PAGE->set_pagelayout('popup');
    $PAGE->set_context(context_system::instance());
    $output = $PAGE->get_renderer('mod_lti');
    $page = new \mod_lti\output\repost_crosssite_page($_SERVER['REQUEST_URI'], $_POST);
    echo $output->header();
    echo $output->render($page);
    echo $output->footer();
    return;
}

$scope = optional_param('scope', '', PARAM_TEXT);
$responsetype = optional_param('response_type', '', PARAM_TEXT);
$clientid = optional_param('client_id', '', PARAM_TEXT);
$redirecturi = optional_param('redirect_uri', '', PARAM_URL);
$loginhint = optional_param('login_hint', '', PARAM_TEXT);
$ltimessagehintenc = optional_param('lti_message_hint', '', PARAM_TEXT);
$state = optional_param('state', '', PARAM_TEXT);
$responsemode = optional_param('response_mode', '', PARAM_TEXT);
$nonce = optional_param('nonce', '', PARAM_TEXT);
$prompt = optional_param('prompt', '', PARAM_TEXT);

// Specific logic for Moodle 4.1 and 4.2 needed, in order to handle auth.
$ismoodle41minimum = empty($CFG->version) ? false : $CFG->version >= 2022112800.00;
$ltimessagehint = $ismoodle41minimum ? json_decode($ltimessagehintenc) : $ltimessagehintenc;
$cmid = !empty($ltimessagehint->cmid) ? $ltimessagehint->cmid : '';

list(
    $pluginname,
    $callback,
    $toolid,
    $resourcelinkid,
    $contenturl,
    $customdata
) = array_pad(explode(
    ',',
    $ismoodle41minimum ? $cmid : $ltimessagehint),
    6,
    null
);

$ispanoptoplugin = false;
$pluginpath = '';
switch($pluginname)
{
    case 'mod_panoptosubmission':
        $ispanoptoplugin = true;
        $pluginpath = '/mod/panoptosubmission/contentitem_return.php';
        break;
    case 'atto_panoptoltibutton':
        $ispanoptoplugin = true;
        $pluginpath = '/lib/editor/atto/plugins/panoptoltibutton/contentitem_return.php';
        break;
    case 'mod_panoptocourseembed':
        $ispanoptoplugin = true;
        $pluginpath = '/mod/panoptocourseembed/contentitem_return.php';
        break;
    case 'tiny_panoptoltibutton':
        $ispanoptoplugin = true;
        $pluginpath = '/lib/editor/tiny/plugins/panoptoltibutton/contentitem_return.php';
        break;
    default:
        $ispanoptoplugin = false;
        break;
}

if (!$ispanoptoplugin) {
    redirect($CFG->wwwroot . '/mod/lti/auth.php' .
                "?client_id=$clientid" .
                "&response_type=$responsetype" .
                "&response_mode=$responsemode" .
                '&redirect_uri=' . urlencode($redirecturi) .
                "&scope=$scope" .
                '&state=' . urlencode($state) .
                '&nonce=' . urlencode($nonce) .
                "&login_hint=$loginhint" .
                "&prompt=$prompt" .
                "&lti_message_hint=$ltimessagehintenc"
            );
}

$ok = !empty($scope) && !empty($responsetype) && !empty($clientid) &&
      !empty($redirecturi) && !empty($loginhint) &&
      !empty($nonce) && ($ismoodle41minimum ? true : !empty($SESSION->lti_message_hint));

if (!$ok) {
    $error = 'invalid_request';
}
// This is only Moodle 4.1 check.
if ($ismoodle41minimum) {
    $ok = $ok && isset($ltimessagehint->launchid);
    if (!$ok) {
        $error = 'invalid_request';
        $desc = 'No launch id in LTI hint';
    }
}
if ($ok && ($scope !== 'openid')) {
    $ok = false;
    $error = 'invalid_scope';
}
if ($ok && ($responsetype !== 'id_token')) {
    $ok = false;
    $error = 'unsupported_response_type';
}
if ($ok) {
    if ($ismoodle41minimum) {
        $launchid = $ltimessagehint->launchid;
        list($courseid, $typeid, $id, $messagetype, $foruserid, $titleb64, $textb64) = explode(',', $SESSION->$launchid, 7);
        unset($SESSION->$launchid);
    } else {
        list($courseid, $typeid, $id, $titleb64, $textb64) = explode(',', $SESSION->lti_message_hint, 5);
    }

    $config = lti_get_type_type_config($typeid);
    $ok = ($clientid === $config->lti_clientid);
    if (!$ok) {
        $error = 'unauthorized_client';
    }
}
if ($ok && ($loginhint !== $USER->id)) {
    $ok = false;
    $error = 'access_denied';
}

$context = null;
$course = null;

if (empty($courseid)) {
    $courseid = 1;
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
} else {
    $context = context_course::instance($courseid);
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    $PAGE->set_context($context);
    $PAGE->set_course($course);
}

// Specific logic for Moodle 4.2.
$ismoodle42minimum = empty($CFG->version) ? false : $CFG->version >= 2023042400.00;
if ($ismoodle42minimum) {
    $panoptodata = new \panopto_data($course->id);
    $coursemodules = $panoptodata->get_cm_for_course($course->id);

    $cmid = 0;
    if (!empty($coursemodules)) {
        $cmid = reset($coursemodules)->id;
    }

    $_GET['id'] = $cmid;
}

// If we're unable to load up config; we cannot trust the redirect uri for POSTing to.
if (empty($config)) {
    throw new moodle_exception('invalidrequest', 'error');
} else {
    $uris = array_map("trim", explode("\n", $config->lti_redirectionuris));
    if (!in_array(strtolower($redirecturi), array_map("strtolower", $uris))) {
        throw new moodle_exception('invalidrequest', 'error');
    }
}
if ($ok) {
    if (isset($responsemode)) {
        $ok = ($responsemode === 'form_post');
        if (!$ok) {
            $error = 'invalid_request';
            $desc = 'Invalid response_mode';
        }
    } else {
        $ok = false;
        $error = 'invalid_request';
        $desc = 'Missing response_mode';
    }
}
if ($ok && !empty($prompt) && ($prompt !== 'none')) {
    $ok = false;
    $error = 'invalid_request';
    $desc = 'Invalid prompt';
}

if ($ok) {
    if ($toolid) {
        $lti = new stdClass();
        $lti->id = $resourcelinkid;
        $lti->typeid = $toolid;
        $lti->launchcontainer = LTI_LAUNCH_CONTAINER_WINDOW;
        $lti->toolurl = $contenturl;
        $lti->custom = new stdClass();
        $lti->instructorcustomparameters = [];
        $lti->debuglaunch = false;
        $lti->course = $courseid;
        if ($customdata) {
            $decoded = json_decode($customdata, true);

            foreach ($decoded as $key => $value) {
                $lti->custom->$key = $value;
            }
        }

        // If we get to this point we know this is a plug-in based request and will not support grading.
        $lti->custom->grading_not_supported = true;

        list($endpoint, $params) = panoptoblock_lti_utility::get_launch_data($lti, $nonce);
    } else {

        require_login($course);
        // Set the return URL. We send the launch container along to help us avoid frames-within-frames when the user returns.
        $returnurlparams = [
            'course' => $courseid,
            'id' => $typeid,
            'sesskey' => sesskey(),
            'callback' => $callback,
        ];
        $returnurl = new \moodle_url($pluginpath, $returnurlparams);

        // Prepare the request.
        $request = panoptoblock_lti_utility::build_content_item_selection_request($typeid, $course, $returnurl, '', '',
                                                            [], [], false, true, false, false, false, $nonce, $pluginname);
        $endpoint = $request->url;
        $params = $request->params;
    }
} else {
    $params['error'] = $error;
    if (!empty($desc)) {
        $params['error_description'] = $desc;
    }
}
if (isset($state)) {
    $params['state'] = $state;
}
$r = '<form action="' . $redirecturi . "\" name=\"ltiAuthForm\" id=\"ltiAuthForm\" " .
     "method=\"post\" enctype=\"application/x-www-form-urlencoded\">\n";
if (!empty($params)) {
    foreach ($params as $key => $value) {
        $key = htmlspecialchars($key);
        $value = htmlspecialchars($value);
        $r .= "  <input type=\"hidden\" name=\"{$key}\" value=\"{$value}\"/>\n";
    }
}
$r .= "</form>\n";
$r .= "<script type=\"text/javascript\">\n" .
    "//<![CDATA[\n" .
    "document.ltiAuthForm.submit();\n" .
    "//]]>\n" .
    "</script>\n";
echo $r;
