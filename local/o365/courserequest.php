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
 * A page that allows authorised users to make a course request based on a Microsoft Team.
 *
 * @package     local_o365
 * @copyright   Enovation Solutions Ltd. {@link https://enovation.ie}
 * @author      Patryk Mroczko <patryk.mroczko@enovation.ie>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/course/lib.php');

use local_o365\feature\courserequest\main;
use local_o365\form\courserequestform;

// Where we came from. Used in a number of redirects.
$url = new moodle_url('/local/o365/courserequest.php');
$return = optional_param('return', null, PARAM_ALPHANUMEXT);
$categoryid = optional_param('category', null, PARAM_INT);
if ($return === 'management') {
    $url->param('return', $return);
    $returnurl = new moodle_url('/course/management.php', ['categoryid' => $CFG->defaultrequestcategory]);
} else {
    $returnurl = new moodle_url('/course/index.php');
}

$PAGE->set_url($url);

// Check permissions.
require_login(null, false);
if (isguestuser()) {
    throw new moodle_exception('guestsarenotallowed', '', $returnurl);
}
if (empty($CFG->enablecourserequests)) {
    throw new moodle_exception('courserequestdisabled', '', $returnurl);
}

if ($CFG->lockrequestcategory) {
    // Course request category is locked, user will always request in the default request category.
    $categoryid = null;
} else if (!$categoryid) {
    // Category selection is enabled but category is not specified.
    // Find a category where user has capability to request courses (preferably the default category).
    $list = core_course_category::make_categories_list('moodle/course:request');
    $categoryid = array_key_exists($CFG->defaultrequestcategory, $list) ? $CFG->defaultrequestcategory : key($list);
}

$context = context_coursecat::instance($categoryid ?: $CFG->defaultrequestcategory);
$PAGE->set_context($context);
require_capability('moodle/course:request', $context);

// Set up the form.
$data = $categoryid ? (object) ['category' => $categoryid] : null;
$data = course_request::prepare($data);
$requestform = new courserequestform($url);
$requestform->set_data($data);

$strtitle = get_string('courserequest_title', 'local_o365');
$PAGE->set_title($strtitle);
$coursecategory = core_course_category::get($categoryid, MUST_EXIST, true);
$PAGE->set_heading($coursecategory->get_formatted_name());
$PAGE->set_primary_active_tab('home');
$PAGE->set_secondary_navigation(false);

// Standard form processing if statement.
if ($requestform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $requestform->get_data()) {
    $apiclient = main::get_unified_api();
    if (empty($apiclient)) {
        throw new moodle_exception('courserequest_graphapi_disabled', 'local_o365', $returnurl);
    }
    $courserequestmain = new main($apiclient);

    $teamdata = $courserequestmain->get_user_team_details_by_team_oid($data->team);

    if (!$teamdata) {
        throw new moodle_exception('courserequest_invalid_team', 'local_o365', $returnurl);
    }

    $data->reason .= get_string('courserequest_customrequestnote', 'local_o365',
        ['name' => $teamdata['name'], 'url' => $teamdata['url']]);

    $request = course_request::create($data);

    if (!$courserequestmain->save_custom_course_request_data($request, $teamdata)) {
        throw new moodle_exception('courserequestfailed', '', $returnurl);
    }

    // And redirect back to the course listing.
    notice(get_string('courserequestsuccess'), $returnurl);
}

$categoryurl = new moodle_url('/course/index.php');
if ($categoryid) {
    $categoryurl->param('categoryid', $categoryid);
}
navigation_node::override_active_url($categoryurl);

$PAGE->navbar->add($strtitle);
echo $OUTPUT->header();
echo $OUTPUT->heading($strtitle);
// Show the request form.
$requestform->display();
echo $OUTPUT->footer();
