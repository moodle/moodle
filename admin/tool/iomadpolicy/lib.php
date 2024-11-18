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
 * Plugin version and other meta-data are defined here.
 *
 * @package     tool_iomadpolicy
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_user\output\myprofile\tree;
use tool_iomadpolicy\api;
use tool_iomadpolicy\iomadpolicy_version;

/**
 * Add nodes to myprofile page.
 *
 * @param tree $tree Tree object
 * @param stdClass $user User object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function tool_iomadpolicy_myprofile_navigation(tree $tree, $user, $iscurrentuser, $course) {
    global $CFG;

    // Do nothing if we are not set as the site policies handler.
    if (empty($CFG->sitepolicyhandler) || $CFG->sitepolicyhandler !== 'tool_iomadpolicy') {
        return;
    }

    // Get the Privacy and policies category.
    if (!array_key_exists('privacyandpolicies', $tree->__get('categories'))) {
        // Create the category.
        $categoryname = get_string('privacyandpolicies', 'admin');
        $category = new core_user\output\myprofile\category('privacyandpolicies', $categoryname, 'contact');
        $tree->add_category($category);
    } else {
        // Get the existing category.
        $category = $tree->__get('categories')['privacyandpolicies'];
    }

    // Add "Policies and agreements" node only for current user or users who can accept on behalf of current user.
    $usercontext = \context_user::instance($user->id);
    if ($iscurrentuser || has_capability('tool/iomadpolicy:acceptbehalf', $usercontext)) {
        $url = new moodle_url('/admin/tool/iomadpolicy/user.php', ['userid' => $user->id]);
        $node = new core_user\output\myprofile\node('privacyandpolicies', 'tool_iomadpolicy',
            get_string('policiesagreements', 'tool_iomadpolicy'), null, $url);
        $category->add_node($node);
    }

    return true;
}

/**
 * Load iomadpolicy message for guests.
 *
 * @return string The HTML code to insert before the head.
 */
function tool_iomadpolicy_before_standard_html_head() {
    global $CFG, $PAGE, $USER;

    $message = null;
    if (!empty($CFG->sitepolicyhandler)
            && $CFG->sitepolicyhandler == 'tool_iomadpolicy'
            && empty($USER->policyagreed)
            && (isguestuser() || !isloggedin())) {
        $output = $PAGE->get_renderer('tool_iomadpolicy');
        try {
            $page = new \tool_iomadpolicy\output\guestconsent();
            $message = $output->render($page);
        } catch (dml_read_exception $e) {
            // During upgrades, the new plugin code with new SQL could be in place but the DB not upgraded yet.
            $message = null;
        }
    }

    return $message;
}

/**
 * Callback to add footer elements.
 *
 * @return string HTML footer content
 */
function tool_iomadpolicy_standard_footer_html() {
    global $CFG, $PAGE;

    $output = '';
    if (!empty($CFG->sitepolicyhandler)
            && $CFG->sitepolicyhandler == 'tool_iomadpolicy') {
        $policies = api::get_current_versions_ids();
        if (!empty($policies)) {
            $url = new moodle_url('/admin/tool/iomadpolicy/viewall.php', ['returnurl' => $PAGE->url]);
            $output .= html_writer::link($url, get_string('useriomadpolicysettings', 'tool_iomadpolicy'));
            $output = html_writer::div($output, 'policiesfooter');
        }
    }

    return $output;
}

/**
 * Hooks redirection to iomadpolicy acceptance pages before sign up.
 */
function tool_iomadpolicy_pre_signup_requests() {
    global $CFG;

    // Do nothing if we are not set as the site policies handler.
    if (empty($CFG->sitepolicyhandler) || $CFG->sitepolicyhandler !== 'tool_iomadpolicy') {
        return;
    }

    $policies = api::get_current_versions_ids(iomadpolicy_version::AUDIENCE_LOGGEDIN);
    $useriomadpolicyagreed = cache::make('core', 'presignup')->get('tool_iomadpolicy_useriomadpolicyagreed');
    if (!empty($policies) && !$useriomadpolicyagreed) {
        // Redirect to "Policy" pages for consenting before creating the user.
        cache::make('core', 'presignup')->set('tool_iomadpolicy_issignup', 1);
        redirect(new \moodle_url('/admin/tool/iomadpolicy/index.php'));
    }
}

/**
 * Serve the embedded files.
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function tool_iomadpolicy_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $PAGE;

    // Do not allow access to files if we are not set as the site iomadpolicy handler.
    if (empty($CFG->sitepolicyhandler) || $CFG->sitepolicyhandler !== 'tool_iomadpolicy') {
        return false;
    }

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    $PAGE->set_context($context);

    if ($filearea !== 'iomadpolicydocumentsummary' && $filearea !== 'iomadpolicydocumentcontent') {
        return false;
    }

    $itemid = array_shift($args);

    $iomadpolicy = api::get_iomadpolicy_version($itemid);

    if ($iomadpolicy->status != iomadpolicy_version::STATUS_ACTIVE) {
        require_login();
    }

    if (!api::can_user_view_iomadpolicy_version($iomadpolicy)) {
        return false;
    }

    $filename = array_pop($args);

    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'tool_iomadpolicy', $filearea, $itemid, $filepath, $filename);

    if (!$file) {
        return false;
    }

    send_stored_file($file, null, 0, $forcedownload, $options);
}

/**
 * Map icons for font-awesome themes.
 */
function tool_iomadpolicy_get_fontawesome_icon_map() {
    return [
        'tool_iomadpolicy:agreed' => 'fa-check text-success',
        'tool_iomadpolicy:declined' => 'fa-times text-danger',
        'tool_iomadpolicy:pending' => 'fa-clock-o text-warning',
        'tool_iomadpolicy:partial' => 'fa-exclamation-triangle text-warning',
        'tool_iomadpolicy:level' => 'fa-level-up fa-rotate-90 text-muted',
    ];
}

/**
 * Serve the new group form as a fragment.
 *
 * @param array $args List of named arguments for the fragment loader.
 * @return string
 */
function tool_iomadpolicy_output_fragment_accept_on_behalf($args) {
    $args = (object) $args;

    $data = [];
    if (!empty($args->jsonformdata)) {
        $serialiseddata = json_decode($args->jsonformdata);
        parse_str($serialiseddata, $data);
    }

    $mform = new \tool_iomadpolicy\form\accept_iomadpolicy(null, $data);

    if (!empty($args->jsonformdata)) {
        // If we were passed non-empty form data we want the mform to call validation functions and show errors.
        $mform->is_validated();
    }

    return $mform->render();
}
