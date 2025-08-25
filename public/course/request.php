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

use core\context\coursecat as context_coursecat;
use core\exception\moodle_exception;
use core\url;
use core_course\course_request;
use core_course\form\request_course as request_course_form;

/**
 * Allows a user to request a course be created for them.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once(__DIR__ . '/../config.php');
require_once($CFG->dirroot . '/course/lib.php');

// Where we came from. Used in a number of redirects.
$url = new url('/course/request.php');
$return = optional_param('return', null, PARAM_ALPHANUMEXT);
$categoryid = optional_param('category', null, PARAM_INT);
if ($return === 'management') {
    $url->param('return', $return);
    $returnurl = new url('/course/management.php', ['categoryid' => $CFG->defaultrequestcategory]);
} else {
    $returnurl = new url('/course/index.php');
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
$data = $categoryid ? (object)['category' => $categoryid] : null;
$data = course_request::prepare($data);
$requestform = new request_course_form($url);
$requestform->set_data($data);

$strtitle = get_string('courserequest');
$PAGE->set_title($strtitle);
$coursecategory = core_course_category::get($categoryid, MUST_EXIST, true);
$PAGE->set_heading($coursecategory->get_formatted_name());
$PAGE->set_primary_active_tab('home');

// Standard form processing if statement.
if ($requestform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $requestform->get_data()) {
    $request = course_request::create($data);

    // And redirect back to the course listing.
    notice(get_string('courserequestsuccess'), $returnurl);
}

$categoryurl = new url('/course/index.php');
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
