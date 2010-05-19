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
require_once($CFG->dirroot.'/lib/hublib.php');

$id = optional_param('id', 0, PARAM_INT);

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

require_login($course);

if (has_capability('moodle/course:publish', get_context_instance(CONTEXT_COURSE, $id))) {

    $PAGE->set_url('/course/publish/index.php', array('id' => $course->id));
    $PAGE->set_pagelayout('course');
    $PAGE->set_title(get_string('course') . ': ' . $course->fullname);
    $PAGE->set_heading($course->fullname);

    $renderer = $PAGE->get_renderer('core', 'publish');

    $hub = new hub();

    /// UNPUBLISH
    $confirmmessage = '';
    $cancel = optional_param('cancel', 0, PARAM_BOOL);
    if (!empty($cancel) and confirm_sesskey()) {
        $confirm = optional_param('confirm', 0, PARAM_BOOL);
        $hubname = optional_param('hubname', 0, PARAM_TEXT);
        $huburl = optional_param('huburl', 0, PARAM_URL);
        $hubcourseid = optional_param('hubcourseid', 0, PARAM_INT);
        $publicationid = optional_param('publicationid', 0, PARAM_INT);
        $timepublished = optional_param('timepublished', 0, PARAM_INT);
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
            $registeredhub = $hub->get_registeredhub($huburl);
            $function = 'hub_unregister_courses';
            $params = array(array( $publication->hubcourseid));
            $serverurl = $huburl."/local/hub/webservice/webservices.php";
            require_once($CFG->dirroot."/webservice/xmlrpc/lib.php");
            $xmlrpcclient = new webservice_xmlrpc_client();
            $result = $xmlrpcclient->call($serverurl, $registeredhub->token, $function, $params);

            //delete the publication from the database
            $hub->delete_publication($publicationid);

            //display confirmation message
            $confirmmessage = $OUTPUT->notification(get_string('courseunpublished', 'hub', $publication), 'notifysuccess');

        } else {
            //display confirmation page for unpublishing
           
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('unpublishcourse', 'hub', $course->shortname), 3, 'main');
            echo $renderer->confirmunpublishing($publication);
            echo $OUTPUT->footer();
            die();
        }
    }

    //check if a course was published
    if (optional_param('published', 0, PARAM_TEXT)) {
        $confirmmessage = $OUTPUT->notification(get_string('coursepublished', 'hub'), 'notifysuccess');
    }

 
    /// OUTPUT
    echo $OUTPUT->header();
    echo $confirmmessage;

    echo $OUTPUT->heading(get_string('publishcourse', 'hub', $course->shortname), 3, 'main');
    echo $renderer->publicationselector($course->id);

    $publications = $hub->get_course_publications($course->id);
    if (!empty($publications)) {
        echo $OUTPUT->heading(get_string('publishedon', 'hub'), 3, 'main');
        echo $renderer->registeredonhublisting($course->id, $publications);
    }

    echo $OUTPUT->footer();

}