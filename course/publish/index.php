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
 * @package    course
 * @subpackage publish
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * The user selects if he wants to publish the course on Moodle.org hub or
 * on a specific hub. The site must be registered on a hub to be able to
 * publish a course on it.
*/

require('../../config.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/registration/lib.php');
require_once($CFG->dirroot . '/course/publish/lib.php');

$id = required_param('id', PARAM_INT);
$hubname = optional_param('hubname', 0, PARAM_TEXT);
$huburl = optional_param('huburl', 0, PARAM_URL);

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

require_login($course);
$context = context_course::instance($course->id);
$shortname = format_string($course->shortname, true, array('context' => $context));

$PAGE->set_url('/course/publish/index.php', array('id' => $course->id));
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($course->fullname);

//check that the PHP xmlrpc extension is enabled
if (!extension_loaded('xmlrpc')) {
    $notificationerror = $OUTPUT->doc_link('admin/environment/php_extension/xmlrpc', '');
    $notificationerror .= get_string('xmlrpcdisabledpublish', 'hub');
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('publishcourse', 'hub', $shortname), 3, 'main');
    echo $OUTPUT->notification($notificationerror);
    echo $OUTPUT->footer();
    die();
}

if (has_capability('moodle/course:publish', context_course::instance($id))) {

    $publicationmanager = new course_publish_manager();
    $confirmmessage = '';

    //update the courses status
    $updatestatusid = optional_param('updatestatusid', false, PARAM_INT);
    if (!empty($updatestatusid) and confirm_sesskey()) {
        //get the communication token from the publication
        $hub = $publicationmanager->get_registeredhub_by_publication($updatestatusid);
        if (empty($hub)) {
            $confirmmessage = $OUTPUT->notification(get_string('nocheckstatusfromunreghub', 'hub'));
        } else {
            //get all site courses registered on this hub
            $function = 'hub_get_courses';
            $params = array('search' => '', 'downloadable' => 1,
                'enrollable' => 1, 'options' => array( 'allsitecourses' => 1));
            $serverurl = $hub->huburl."/local/hub/webservice/webservices.php";
            require_once($CFG->dirroot."/webservice/xmlrpc/lib.php");
            $xmlrpcclient = new webservice_xmlrpc_client($serverurl, $hub->token);
            $result = $xmlrpcclient->call($function, $params);
            $sitecourses = $result['courses'];

            //update status for all these course
            foreach ($sitecourses as $sitecourse) {
                //get the publication from the hub course id
                $publication = $publicationmanager->get_publication($sitecourse['id'], $hub->huburl);
                if (!empty($publication)) {
                    $publication->status = $sitecourse['privacy'];
                    $publication->timechecked = time();
                    $publicationmanager->update_publication($publication);
                } else {
                    $msgparams = new stdClass();
                    $msgparams->id = $sitecourse['id'];
                    $msgparams->hubname = html_writer::tag('a', $hub->hubname, array('href' => $hub->huburl));
                    $confirmmessage .= $OUTPUT->notification(
                            get_string('detectednotexistingpublication', 'hub', $msgparams));
                }
            }
        }
    }

    //if the site os registered on no hub display an error page
    $registrationmanager = new registration_manager();
    $registeredhubs = $registrationmanager->get_registered_on_hubs();
    if (empty($registeredhubs)) {
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('publishon', 'hub'), 3, 'main');
        echo $OUTPUT->box(get_string('notregisteredonhub', 'hub'));
        echo $OUTPUT->footer();
        die();
    }

    $renderer = $PAGE->get_renderer('core', 'course');

    /// UNPUBLISH
    $cancel = optional_param('cancel', 0, PARAM_BOOL);
    if (!empty($cancel) and confirm_sesskey()) {
        $confirm = optional_param('confirm', 0, PARAM_BOOL);
        $hubcourseid = optional_param('hubcourseid', 0, PARAM_INT);
        $publicationid = optional_param('publicationid', 0, PARAM_INT);
        $timepublished = optional_param('timepublished', 0, PARAM_INT);
        $publication = new stdClass();
        $publication->courseshortname = $course->shortname;
        $publication->courseid = $course->id;
        $publication->hubname = $hubname;
        $publication->huburl = $huburl;
        $publication->hubcourseid = $hubcourseid;
        $publication->timepublished = $timepublished;
        if (empty($publication->hubname)) {
             $publication->hubname = $huburl;
        }
        $publication->id = $publicationid;
        if($confirm) {
            //unpublish the publication by web service
            $registeredhub = $registrationmanager->get_registeredhub($huburl);
            $function = 'hub_unregister_courses';
            $params = array('courseids' => array( $publication->hubcourseid));
            $serverurl = $huburl."/local/hub/webservice/webservices.php";
            require_once($CFG->dirroot."/webservice/xmlrpc/lib.php");
            $xmlrpcclient = new webservice_xmlrpc_client($serverurl, $registeredhub->token);
            $result = $xmlrpcclient->call($function, $params);

            //delete the publication from the database
            $publicationmanager->delete_publication($publicationid);

            //display confirmation message
            $confirmmessage = $OUTPUT->notification(get_string('courseunpublished', 'hub', $publication), 'notifysuccess');

        } else {
            //display confirmation page for unpublishing

            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('unpublishcourse', 'hub', $shortname), 3, 'main');
            echo $renderer->confirmunpublishing($publication);
            echo $OUTPUT->footer();
            die();
        }
    }

    //check if a course was published
    if (optional_param('published', 0, PARAM_TEXT)) {
        $confirmmessage = $OUTPUT->notification(get_string('coursepublished', 'hub',
                empty($hubname)?$huburl:$hubname), 'notifysuccess');
    }


    /// OUTPUT
    echo $OUTPUT->header();
    echo $confirmmessage;

    echo $OUTPUT->heading(get_string('publishcourse', 'hub', $shortname), 3, 'main');
    echo $renderer->publicationselector($course->id);

    $publications = $publicationmanager->get_course_publications($course->id);
    if (!empty($publications)) {
        echo $OUTPUT->heading(get_string('publishedon', 'hub'), 3, 'main');
        echo $renderer->registeredonhublisting($course->id, $publications);
    }

    echo $OUTPUT->footer();

}
