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
 * Handle sending a user to a tool provider to initiate a content-item selection.
 *
 * @package mod_lti
 * @copyright  2015 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/lti/lib.php');
require_once($CFG->dirroot.'/mod/lti/locallib.php');

$courseid = required_param('course', PARAM_INT);
$sectionid = required_param('section', PARAM_INT);
$id = required_param('id', PARAM_INT);
$sectionreturn = required_param('sr', PARAM_INT);

require_login($PAGE->course);

$tool = lti_get_type($id);
$typeconfig = lti_get_type_config($id);
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
$tool->enabledcapability = $typeconfig['enabledcapability_ContentItemSelectionRequest'];
$tool->parameter = $typeconfig['parameter_ContentItemSelectionRequest'];

$title = optional_param('title', $tool->name, PARAM_TEXT);

if (isset($typeconfig['toolurl_ContentItemSelectionRequest'])) {
    $endpoint = $typeconfig['toolurl_ContentItemSelectionRequest'];
} else {
    $endpoint = !empty($instance->toolurl) ? $instance->toolurl : $typeconfig['toolurl'];
}
$endpoint = trim($endpoint);

// If the current request is using SSL and a secure tool URL is specified, use it.
if (lti_request_is_using_ssl() && !empty($instance->securetoolurl)) {
    $endpoint = trim($instance->securetoolurl);
}

// If SSL is forced, use the secure tool url if specified. Otherwise, make sure https is on the normal launch URL.
if (isset($typeconfig['forcessl']) && ($typeconfig['forcessl'] == '1')) {
    if (!empty($instance->securetoolurl)) {
        $endpoint = trim($instance->securetoolurl);
    }

    $endpoint = lti_ensure_url_is_https($endpoint);
} else {
    if (!strstr($endpoint, '://')) {
        $endpoint = 'http://' . $endpoint;
    }
}

$orgid = (isset($typeconfig['organizationid'])) ? $typeconfig['organizationid'] : '';

$course = $PAGE->course;
$islti2 = isset($tool->toolproxyid);
$instance = new stdClass();
$instance->course = $courseid;
$allparams = lti_build_request($instance, $typeconfig, $course, $id, $islti2);
if ($islti2) {
    $requestparams = lti_build_request_lti2($tool, $allparams);
} else {
    $requestparams = $allparams;
}
$requestparams = array_merge($requestparams, lti_build_standard_request(null, $orgid, $islti2));
$customstr = '';
if (isset($typeconfig['customparameters'])) {
    $customstr = $typeconfig['customparameters'];
}
$requestparams = array_merge($requestparams, lti_build_custom_parameters($toolproxy, $tool, $instance, $allparams, $customstr,
    '', $islti2));

// Allow request params to be updated by sub-plugins.
$plugins = core_component::get_plugin_list('ltisource');
foreach (array_keys($plugins) as $plugin) {
    $pluginparams = component_callback('ltisource_'.$plugin, 'before_launch',
        array($instance, $endpoint, $requestparams), array());

    if (!empty($pluginparams) && is_array($pluginparams)) {
        $requestparams = array_merge($requestparams, $pluginparams);
    }
}

if ($islti2) {
   $requestparams['lti_version'] = 'LTI-2p0';
} else {
   $requestparams['lti_version'] = 'LTI-1p0';
}
$requestparams['lti_message_type'] = 'ContentItemSelectionRequest';

$requestparams['accept_media_types'] = 'application/vnd.ims.lti.v1.ltilink';
$requestparams['accept_presentation_document_targets'] = 'frame,iframe,window';
$requestparams['accept_unsigned'] = 'false';
$requestparams['accept_multiple'] = 'false';
$requestparams['auto_create'] = 'true';
$requestparams['can_confirm'] = 'false';
$requestparams['accept_copy_advice'] = 'false';
$requestparams['title'] = $title;

$returnurlparams = array('course' => $courseid,
                         'section' => $sectionid,
                         'id' => $id,
                         'sr' => $sectionreturn,
                         'sesskey' => sesskey());

// Add the return URL. We send the launch container along to help us avoid frames-within-frames when the user returns.
$url = new \moodle_url('/mod/lti/contentitem_return.php', $returnurlparams);
$returnurl = $url->out(false);

if (isset($typeconfig['forcessl']) && ($typeconfig['forcessl'] == '1')) {
    $returnurl = lti_ensure_url_is_https($returnurl);
}

$requestparams['content_item_return_url'] = $returnurl;


$parms = lti_sign_parameters($requestparams, $endpoint, "POST", $key, $secret);

$endpointurl = new \moodle_url($endpoint);
$endpointparams = $endpointurl->params();

// Strip querystring params in endpoint url from $parms to avoid duplication.
if (!empty($endpointparams) && !empty($parms)) {
    foreach (array_keys($endpointparams) as $paramname) {
        if (isset($parms[$paramname])) {
            unset($parms[$paramname]);
        }
    }
}

echo "<p id=\"id_warning\" style=\"display: none; color: red; font-weight: bold; margin-top: 1em; padding-top: 1em;\">\n";
echo get_string('register_warning', 'lti');
echo "\n</p>\n";

$loading = $OUTPUT->render(new \pix_icon('i/loading', '', 'moodle',
    array('style' => 'margin:auto;vertical-align:middle;margin-top:125px;',
          'opacity' => '0.5')));

echo "<p style=\"text-align:center;\">\n";
echo $loading;
echo "\n</p>\n";

$script = '
        <script type="text/javascript">
        //<![CDATA[
            function doReveal() {
              var el = document.getElementById(\'id_warning\');
              el.style.display = \'block\';
            }
            var mod_lti_timer = window.setTimeout(doReveal, 20000);
        //]]
        </script>
';

echo $script;

$content = lti_post_launch_html($parms, $endpoint, false);

echo $content;
