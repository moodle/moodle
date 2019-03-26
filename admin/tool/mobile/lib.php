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
 * Lib functions, mostly callbacks.
 *
 * @package    tool_mobile
 * @copyright  2017 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Callback to add head elements.
 *
 * @return str valid html head content
 * @since  Moodle 3.3
 */
function tool_mobile_before_standard_html_head() {
    global $CFG, $PAGE;
    $output = '';
    // Smart App Banners meta tag is only displayed if mobile services are enabled and configured.
    if (!empty($CFG->enablemobilewebservice)) {
        $mobilesettings = get_config('tool_mobile');
        if (!empty($mobilesettings->enablesmartappbanners)) {
            if (!empty($mobilesettings->iosappid)) {
                $output .= '<meta name="apple-itunes-app" content="app-id=' . s($mobilesettings->iosappid) . ', ';
                $output .= 'app-argument=' . $PAGE->url->out() . '"/>';
            }

            if (!empty($mobilesettings->androidappid)) {
                $mobilemanifesturl = "$CFG->wwwroot/$CFG->admin/tool/mobile/mobile.webmanifest.php";
                $output .= '<link rel="manifest" href="'.$mobilemanifesturl.'" />';
            }
        }
    }
    return $output;
}

/**
 * Generate the app download url to promote moodle mobile.
 *
 * @return moodle_url|void App download moodle_url object or return if setuplink is not set.
 */
function tool_mobile_create_app_download_url() {
    global $CFG;

    $mobilesettings = get_config('tool_mobile');

    if (empty($mobilesettings->setuplink)) {
        return;
    }

    $downloadurl = new moodle_url($mobilesettings->setuplink);

    // Do not update the URL if it is a custom one (we may break it completely).
    if ($mobilesettings->setuplink != 'https://download.moodle.org/mobile') {
        return $downloadurl;
    }

    $downloadurl->param('version', $CFG->version);
    $downloadurl->param('lang', current_language());

    if (!empty($mobilesettings->iosappid)) {
        $downloadurl->param('iosappid', $mobilesettings->iosappid);
    }

    if (!empty($mobilesettings->androidappid)) {
        $downloadurl->param('androidappid', $mobilesettings->androidappid);
    }

    return $downloadurl;
}

/**
 * Checks if the given user has a mobile token (has used recently the app).
 *
 * @param  int $userid the user to check
 * @return bool        true if the user has a token, false otherwise.
 */
function tool_mobile_user_has_token($userid) {
    global $DB;

    $sql = "SELECT 1
              FROM {external_tokens} t, {external_services} s
             WHERE t.externalserviceid = s.id
               AND s.enabled = 1
               AND s.shortname IN ('moodle_mobile_app', 'local_mobile')
               AND t.userid = ?";

    return $DB->record_exists_sql($sql, [$userid]);
}

/**
 * User profile page callback.
 *
 * Used add a section about the moodle mobile app.
 *
 * @param \core_user\output\myprofile\tree $tree My profile tree where the setting will be added.
 * @param stdClass $user The user object.
 * @param bool $iscurrentuser Is this the current user viewing
 * @return void Return if the mobile web services setting is disabled or if not the current user.
 */
function tool_mobile_myprofile_navigation(\core_user\output\myprofile\tree $tree, $user, $iscurrentuser) {
    global $CFG;

    if (empty($CFG->enablemobilewebservice)) {
        return;
    }

    if (!$iscurrentuser) {
        return;
    }

    if (!$url = tool_mobile_create_app_download_url()) {
        return;
    }

    $userhastoken = tool_mobile_user_has_token($user->id);

    $mobilecategory = new core_user\output\myprofile\category('mobile', get_string('mobileapp', 'tool_mobile'),
            'loginactivity');
    $tree->add_category($mobilecategory);

    if ($userhastoken) {
        $mobilestr = get_string('mobileappconnected', 'tool_mobile');
    } else {
        $mobilestr = get_string('mobileappenabled', 'tool_mobile', $url->out());
    }

    $node = new  core_user\output\myprofile\node('mobile', 'mobileappnode', $mobilestr, null);
    $tree->add_node($node);
}

/**
 * Callback to add footer elements.
 *
 * @return str valid html footer content
 * @since  Moodle 3.4
 */
function tool_mobile_standard_footer_html() {
    global $CFG;
    $output = '';
    if (!empty($CFG->enablemobilewebservice) && $url = tool_mobile_create_app_download_url()) {
        $output .= html_writer::link($url, get_string('getmoodleonyourmobile', 'tool_mobile'));
    }
    return $output;
}

/**
 * Callback to be able to change a message/notification data per processor.
 *
 * @param  str $procname    processor name
 * @param  stdClass $data   message or notification data
 */
function tool_mobile_pre_processor_message_send($procname, $data) {
    global $CFG;

    if (empty($CFG->enablemobilewebservice)) {
        return;
    }

    if (empty($data->userto)) {
        return;
    }

    // Only hack email.
    if ($procname == 'email') {

        // Send a message only when there is an HTML version of the email, mobile services are enabled,
        // the user receiving the message has not used the app and there is an app download URL set.
        if (empty($data->fullmessagehtml)) {
            return;
        }

        if (!$url = tool_mobile_create_app_download_url()) {
            return;
        }

        $userto = is_object($data->userto) ? $data->userto->id : $data->userto;
        if (tool_mobile_user_has_token($userto)) {
            return;
        }

        $data->fullmessagehtml .= html_writer::tag('p', get_string('readingthisemailgettheapp', 'tool_mobile', $url->out()));
    }
}
