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
 * @package     tool_policy
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_user\output\myprofile\tree;
use tool_policy\api;
use tool_policy\policy_version;

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
function tool_policy_myprofile_navigation(tree $tree, $user, $iscurrentuser, $course) {
    global $CFG;

    // Do nothing if we are not set as the site policies handler.
    if (empty($CFG->sitepolicyhandler) || $CFG->sitepolicyhandler !== 'tool_policy') {
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
    if ($iscurrentuser || has_capability('tool/policy:acceptbehalf', $usercontext)) {
        $url = new moodle_url('/admin/tool/policy/user.php', ['userid' => $user->id]);
        $node = new core_user\output\myprofile\node('privacyandpolicies', 'tool_policy',
            get_string('policiesagreements', 'tool_policy'), null, $url);
        $category->add_node($node);
    }

    return true;
}

/**
 * Hooks redirection to policy acceptance pages before sign up.
 */
function tool_policy_pre_signup_requests() {
    global $CFG;

    // Do nothing if we are not set as the site policies handler.
    if (empty($CFG->sitepolicyhandler) || $CFG->sitepolicyhandler !== 'tool_policy') {
        return;
    }

    $policies = api::get_current_versions_ids(policy_version::AUDIENCE_LOGGEDIN);
    $userpolicyagreed = cache::make('core', 'presignup')->get('tool_policy_userpolicyagreed');
    if (!empty($policies) && !$userpolicyagreed) {
        // Redirect to "Policy" pages for consenting before creating the user.
        cache::make('core', 'presignup')->set('tool_policy_issignup', 1);
        redirect(new \moodle_url('/admin/tool/policy/index.php'));
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
function tool_policy_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $PAGE;

    // Do not allow access to files if we are not set as the site policy handler.
    if (empty($CFG->sitepolicyhandler) || $CFG->sitepolicyhandler !== 'tool_policy') {
        return false;
    }

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    $PAGE->set_context($context);

    if ($filearea !== 'policydocumentsummary' && $filearea !== 'policydocumentcontent') {
        return false;
    }

    $itemid = array_shift($args);

    $policy = api::get_policy_version($itemid);

    if ($policy->status != policy_version::STATUS_ACTIVE) {
        require_login();
    }

    if (!api::can_user_view_policy_version($policy)) {
        return false;
    }

    $filename = array_pop($args);

    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'tool_policy', $filearea, $itemid, $filepath, $filename);

    if (!$file) {
        return false;
    }

    send_stored_file($file, null, 0, $forcedownload, $options);
}

/**
 * Map icons for font-awesome themes.
 */
function tool_policy_get_fontawesome_icon_map() {
    return [
        'tool_policy:agreed' => 'fa-check text-success',
        'tool_policy:declined' => 'fa-xmark text-danger',
        'tool_policy:level' => 'fa-turn-up fa-rotate-90 text-muted',
        'tool_policy:partial' => 'fa-triangle-exclamation text-warning',
        'tool_policy:pending' => 'fa-regular fa-clock text-warning',
    ];
}

/**
 * Serve the new group form as a fragment.
 *
 * @param array $args List of named arguments for the fragment loader.
 * @return string
 */
function tool_policy_output_fragment_accept_on_behalf($args) {
    $args = (object) $args;

    $data = [];
    if (!empty($args->jsonformdata)) {
        $serialiseddata = json_decode($args->jsonformdata);
        parse_str($serialiseddata, $data);
    }

    $mform = new \tool_policy\form\accept_policy(null, $data);

    if (!empty($args->jsonformdata)) {
        // If we were passed non-empty form data we want the mform to call validation functions and show errors.
        $mform->is_validated();
    }

    return $mform->render();
}
