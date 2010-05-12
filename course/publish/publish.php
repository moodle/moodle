<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// This file is part of Moodle - http://moodle.org/                      //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//                                                                       //
// Moodle is free software: you can redistribute it and/or modify        //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation, either version 3 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// Moodle is distributed in the hope that it will be useful,             //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details.                          //
//                                                                       //
// You should have received a copy of the GNU General Public License     //
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.       //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/*
 * @package    course
 * @subpackage publish
 * @author     Jerome Mouneyrac <jerome@mouneyrac.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This page handles the publication process.
 * It sends metadata by web services
 * It also sends screenshots and backup files.
*/

require_once('../../config.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/course/publish/forms.php');
require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->dirroot . '/lib/hublib.php');

//check user access capability to this page
$id = optional_param('id', 0, PARAM_INT);
$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
require_login($course);

if (has_capability('moodle/course:publish', get_context_instance(CONTEXT_COURSE, $id))) {

//page settings
    $PAGE->set_url('/course/publish/publish.php', array('id' => $course->id));
    $PAGE->set_pagelayout('course');
    $PAGE->set_title(get_string('course') . ': ' . $course->fullname);
    $PAGE->set_heading($course->fullname);

//retrieve hub name and hub url, also check the session
    $huburl = optional_param('huburl', '', PARAM_URL);
    $hubname = optional_param('hubname', '', PARAM_TEXT);
    if (empty($huburl) or !confirm_sesskey()) {
        throw new moodle_exception('missingparameter');
    }

    $advertise = optional_param('advertise', false, PARAM_BOOL);
    $share = optional_param('share', false, PARAM_BOOL);

    $huburl = optional_param('huburl', false, PARAM_URL);
    $hubname = optional_param('hubname', false, PARAM_TEXT);

//retrieve the course information
    $courseinfo = new stdClass();
    $courseinfo->fullname = optional_param('name', false, PARAM_TEXT);
    $courseinfo->shortname = optional_param('courseshortname', false, PARAM_ALPHANUMEXT);
    $courseinfo->description = optional_param('description', false, PARAM_TEXT);
    $courseinfo->language = optional_param('language', false, PARAM_ALPHANUMEXT);
    $courseinfo->publishername = optional_param('publishername', false, PARAM_TEXT);
    $courseinfo->contributornames = optional_param('contributornames', false, PARAM_TEXT);
    $courseinfo->coverage = optional_param('coverage', false, PARAM_TEXT);
    $courseinfo->creatorname = optional_param('creatorname', false, PARAM_TEXT);
    $courseinfo->licenceshortname = optional_param('licence', false, PARAM_ALPHANUMEXT);
    $courseinfo->subject = optional_param('subject', false, PARAM_ALPHANUM);
    $courseinfo->audience = optional_param('audience', false, PARAM_ALPHA);
    $courseinfo->educationallevel = optional_param('educationallevel', false, PARAM_ALPHA);
    $creatornotes = optional_param('creatornotes', false, PARAM_RAW);
    $courseinfo->creatornotes = $creatornotes['text'];
    $courseinfo->creatornotesformat = $creatornotes['format'];
    if ($share) {
        $courseinfo->demourl = optional_param('demourl', false, PARAM_URL);
        $courseinfo->enrollable = false;
    } else {
        $courseinfo->courseurl = optional_param('courseurl', false, PARAM_URL);
        $courseinfo->enrollable = true;
    }

//save into screenshots field the references to the screenshot content hash
//(it will be like a unique id from the hub perspective)
    $screenshots = optional_param('screenshots', false, PARAM_INTEGER);
    $fs = get_file_storage();
    $files = $fs->get_area_files(get_context_instance(CONTEXT_USER, $USER->id)->id, 'user_draft', $screenshots);
    if (!empty($files)) {
        $courseinfo->screenshotsids = '';
        foreach ($files as $file) {
            if ($file->is_valid_image()) {
                $courseinfo->screenshotsids = $courseinfo->screenshotsids . "notsend:" . $file->get_contenthash() . ";";
            }
        }
    }

// HEADER - needed here because the backup write some stuff

    echo $OUTPUT->header();

// BACKUP ACTION
    if ($share) {
        $bc = new backup_controller(backup::TYPE_1COURSE, $course->id, backup::FORMAT_MOODLE,
                backup::INTERACTIVE_YES, backup::MODE_HUB, $USER->id);
        $bc->finish_ui();
        $bc->execute_plan();
        $backup = $bc->get_results();
        $backupfile = $backup['backup_destination'];
    }

// PUBLISH ACTION

//retrieve the token to call the hub
    $hub = new hub();
    $registeredhub = $hub->get_registeredhub($huburl);

//publish the course information
    $function = 'hub_register_courses';
    $params = array(array($courseinfo));
    $serverurl = $huburl."/local/hub/webservice/webservices.php";
    require_once($CFG->dirroot."/webservice/xmlrpc/lib.php");
    $xmlrpcclient = new webservice_xmlrpc_client();
    $courseids = $xmlrpcclient->call($serverurl, $registeredhub->token, $function, $params);
    
    if (count($courseids) != 1) {
        throw new moodle_exception('coursewronglypublished');
    }

    $courseregisteredmsg = $OUTPUT->notification(get_string('coursepublished', 'hub'), 'notifysuccess');


// SEND FILES

// send screenshots
    require_once($CFG->dirroot. "/lib/filelib.php");
    $params = array('token' => $registeredhub->token, 'filetype' => SCREENSHOT_FILE_TYPE,
            'courseshortname' => $courseinfo->shortname);
    $curl = new curl();
    foreach ($files as $file) {
        if ($file->is_valid_image()) {
            $params['file'] = $file;
            $params['filename'] = $file->get_filename();
            $curl->post($huburl."/local/hub/webservice/upload.php", $params);
        }
    }


// send backup
    if ($share) {
        foreach ($courseids as $courseid) {
            $params['filetype'] = BACKUP_FILE_TYPE;
            $params['courseid'] = $courseid;
            $params['file'] = $backupfile;
            $curl->post($huburl."/local/hub/webservice/upload.php", $params);
        }
    }
    

//TODO: Delete the backup from user_tohub

// OUTPUT SECTION

//Display update notification result
    if (!empty($courseregisteredmsg)) {
        echo $courseregisteredmsg;
    }

    echo $OUTPUT->footer();
}
