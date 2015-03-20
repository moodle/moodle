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
 * Handle the return from the Tool Provider after selecting a content item.
 *
 * @package mod_lti
 * @copyright  2015 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->dirroot . '/mod/lti/lib.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once($CFG->dirroot . '/mod/lti/OAuth.php');
require_once($CFG->dirroot . '/mod/lti/TrivialStore.php');

use moodle\mod\lti as lti;

$courseid = required_param('course', PARAM_INT);
$sectionid = required_param('section', PARAM_INT);
$id = required_param('id', PARAM_INT);
$sectionreturn = required_param('sr', PARAM_INT);
$messagetype = required_param('lti_message_type', PARAM_TEXT);
$version = required_param('lti_version', PARAM_TEXT);
$consumer_key = required_param('oauth_consumer_key', PARAM_RAW);

$items = optional_param('content_items', '', PARAM_RAW);
$errormsg = optional_param('lti_errormsg', '', PARAM_TEXT);
$msg = optional_param('lti_msg', '', PARAM_TEXT);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$module = $DB->get_record('modules', array('name' => 'lti'), '*', MUST_EXIST);
$tool = lti_get_type($id);
$typeconfig = lti_get_type_config($id);

require_login($course);
require_sesskey();

if (isset($tool->toolproxyid)) {
    $toolproxy = lti_get_tool_proxy($tool->toolproxyid);
    $key = $toolproxy->guid;
    $secret = $toolproxy->secret;
} else {
    $toolproxy = null;
    if (!empty($instance->resourcekey)) {
        $key = $instance->resourcekey;
    } else if (!empty($typeconfig['resourcekey'])) {
        $key = $typeconfig['resourcekey'];
    } else {
        $key = '';
    }
    if (!empty($instance->password)) {
        $secret = $instance->password;
    } else if (!empty($typeconfig['password'])) {
        $secret = $typeconfig['password'];
    } else {
        $secret = '';
    }
}

if ($consumer_key !== $key) {
    throw new Exception('Consumer key is incorrect.');
}

$store = new lti\TrivialOAuthDataStore();
$store->add_consumer($key, $secret);

$server = new lti\OAuthServer($store);

$method = new lti\OAuthSignatureMethod_HMAC_SHA1();
$server->add_signature_method($method);
$request = lti\OAuthRequest::from_request();

try {
    $server->verify_request($request);
} catch (\Exception $e) {
    $message = $e->getMessage();
    debugging($e->getMessage() . "\n");
    throw new lti\OAuthException("OAuth signature failed: " . $message);
}

if ($items) {
    $items = json_decode($items);
    if ($items->{'@context'} !== 'http://purl.imsglobal.org/ctx/lti/v1/ContentItem') {
        throw new Exception('Invalid media type.');
    }
    if (!isset($items->{'@graph'}) || !is_array($items->{'@graph'}) || (count($items->{'@graph'}) > 1)) {
        throw new Exception('Invalid format.');
    }
}

$continueurl = course_get_url($course, $sectionid, array('sr' => $sectionreturn));
if (count($items->{'@graph'}) > 0) {
    foreach ($items->{'@graph'} as $item) {
        $moduleinfo = new stdClass();
        $moduleinfo->modulename = 'lti';
        $moduleinfo->name = '';
        if (isset($item->title)) {
            $moduleinfo->name = $item->title;
        }
        if (empty($moduleinfo->name)) {
            $moduleinfo->name = $tool->name;
        }
        $moduleinfo->module = $module->id;
        $moduleinfo->section = $sectionid;
        $moduleinfo->visible = 1;
        if (isset($item->url)) {
            $moduleinfo->toolurl = $item->url;
            $moduleinfo->typeid = 0;
        } else {
            $moduleinfo->typeid = $id;
        }
        $moduleinfo->instructorchoicesendname = LTI_SETTING_NEVER;
        $moduleinfo->instructorchoicesendemailaddr = LTI_SETTING_NEVER;
        $moduleinfo->instructorchoiceacceptgrades = LTI_SETTING_NEVER;
        $moduleinfo->launchcontainer = LTI_LAUNCH_CONTAINER_DEFAULT;
        if (isset($item->placementAdvice->presentationDocumentTarget)) {
            if ($item->placementAdvice->presentationDocumentTarget === 'window') {
                $moduleinfo->launchcontainer = LTI_LAUNCH_CONTAINER_WINDOW;
            } else if ($item->placementAdvice->presentationDocumentTarget === 'frame') {
                $moduleinfo->launchcontainer = LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS;
            } else if ($item->placementAdvice->presentationDocumentTarget === 'iframe') {
                $moduleinfo->launchcontainer = LTI_LAUNCH_CONTAINER_EMBED;
            }
        }
        if (isset($item->custom)) {
            $moduleinfo->instructorcustomparameters = '';
            $first = true;
            foreach ($item->custom as $key => $value) {
                if (!$first) {
                    $moduleinfo->instructorcustomparameters .= "\n";
                }
                $moduleinfo->instructorcustomparameters .= "{$key}={$value}";
                $first = false;
            }
        }
        $moduleinfo = add_moduleinfo($moduleinfo, $course, null);
    }
    $clickhere = get_string('click_to_continue', 'lti', (object)array('link' => $continueurl->out()));
} else {
    $clickhere = get_string('return_to_course', 'lti', (object)array('link' => $continueurl->out()));
}

if (!empty($errormsg) || !empty($msg)) {

    $url = new moodle_url('/mod/lti/contentitem_return.php',
        array('course' => $courseid));
    $PAGE->set_url($url);

    $pagetitle = strip_tags($course->shortname);
    $PAGE->set_title($pagetitle);
    $PAGE->set_heading($course->fullname);

    $PAGE->set_pagelayout('embedded');

    echo $OUTPUT->header();

    if (!empty($lti) and !empty($context)) {
        echo $OUTPUT->heading(format_string($lti->name, true, array('context' => $context)));
    }

    if (!empty($errormsg)) {

        echo '<p style="color: #f00; font-weight: bold; margin: 1em;">';
        echo get_string('lti_launch_error', 'lti') . ' ';
        p($errormsg);
        echo "</p>\n";

    }

    if (!empty($msg)) {

        echo '<p style="margin: 1em;">';
        p($msg);
        echo "</p>\n";

    }

    echo "<p style=\"margin: 1em;\">{$clickhere}</p>";

    echo $OUTPUT->footer();

} else {

    $url = $continueurl->out();

    echo '<html><body>';

    $script = "
        <script type=\"text/javascript\">
        //<![CDATA[
            if(window != top){
                top.location.href = '{$url}';
            }
        //]]
        </script>
    ";

    $noscript = "
        <noscript>
            {$clickhere}
        </noscript>
    ";

    echo $script;
    echo $noscript;

    echo '</body></html>';

}
