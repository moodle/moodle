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

/*
 * @package    blocks
 * @subpackage community
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This page display the community course search form.
 * It also handles adding a course to the community block.
 * It also handles downloading a course template.
*/

require('../../config.php');
require_once($CFG->dirroot.'/blocks/community/locallib.php');
require_once($CFG->dirroot.'/blocks/community/forms.php');

require_login();

$PAGE->set_url('/blocks/community/communitycourse.php');
$PAGE->set_heading($SITE->fullname);
$PAGE->set_pagelayout('course');
$PAGE->set_title(get_string('searchcourse', 'block_community'));
$PAGE->navbar->ignore_active(true);
$PAGE->navbar->add(get_string('searchcourse', 'block_community'));

$search  = optional_param('search', '', PARAM_TEXT);

$community = new community();

/// Check if the page has been called with trust argument
$add  = optional_param('add', -1, PARAM_INTEGER);
$confirm  = optional_param('confirmed', false, PARAM_INTEGER);
if ($add != -1 and $confirm and confirm_sesskey()) {
    $course = new stdClass();
    $course->name  = optional_param('coursefullname', '', PARAM_TEXT);
    $course->description  = optional_param('coursedescription', '', PARAM_TEXT);
    $course->url  = optional_param('courseurl', '', PARAM_URL);
    $course->imageurl  = optional_param('courseimageurl', '', PARAM_URL);
    $community->add_community_course($course, $USER->id);
    $notificationmessage = $OUTPUT->notification(get_string('addedtoblock', 'hub', 'backup_'.$courseid.".zip"),
            'notifysuccess');
}

/// Download
$huburl  = optional_param('huburl', false, PARAM_URL);
$download  = optional_param('download', -1, PARAM_INTEGER);
$courseid  = optional_param('courseid', '', PARAM_INTEGER);
if ($download != -1 and !empty($courseid) and confirm_sesskey()) {
    $community->download_community_course_backup($courseid, $huburl);
    $notificationmessage = $OUTPUT->notification(get_string('downloadconfirmed', 'hub', 'backup_'.$courseid.".zip"),
            'notifysuccess');
}

/// Remove community
$remove  = optional_param('remove', '', PARAM_INTEGER);
$communityid  = optional_param('communityid', '', PARAM_INTEGER);
if ($remove != -1 and !empty($communityid) and confirm_sesskey()) {
    $community->remove_community_course($communityid, $USER->id);
    $notificationmessage = $OUTPUT->notification(get_string('communityremoved', 'hub'),
            'notifysuccess');
}


$renderer = $PAGE->get_renderer('block_community');

//forms
$hubselectorform = new community_hub_search_form('', array('search' => $search));
$fromform = $hubselectorform->get_data();

//Retrieve courses by web service
$courses = array();
if (!empty($fromform)) {
    $downloadable  = optional_param('downloadable', false, PARAM_INTEGER);
    $function = 'hub_get_courses';
    $params = array($search, $downloadable);
    $serverurl = $huburl."/local/hub/webservice/webservices.php";
    require_once($CFG->dirroot."/webservice/xmlrpc/lib.php");
    $xmlrpcclient = new webservice_xmlrpc_client();
    $courses = $xmlrpcclient->call($serverurl, 'publichub', $function, $params);
}

// OUTPUT
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('addcommunitycourse', 'block_community'), 3, 'main');
if (!empty($notificationmessage)) {
    echo $notificationmessage;
}
$hubselectorform->display();
echo $renderer->course_list($courses, $huburl);
echo $OUTPUT->footer();