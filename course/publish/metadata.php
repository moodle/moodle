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
 * This page display the publication metadata form
*/

require_once('../../config.php');
require_once($CFG->dirroot.'/course/publish/forms.php');
require_once($CFG->dirroot.'/lib/hublib.php');
require_once($CFG->dirroot.'/lib/filelib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

//check user access capability to this page
$id = optional_param('id', 0, PARAM_INT);
$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
require_login($course);

if (has_capability('moodle/course:publish', get_context_instance(CONTEXT_COURSE, $id))) {
    
    //page settings
    $PAGE->set_url('/course/publish/metadata.php', array('id' => $course->id));
    $PAGE->set_pagelayout('course');
    $PAGE->set_title(get_string('course') . ': ' . $course->fullname);
    $PAGE->set_heading($course->fullname);

    //retrieve hub name and hub url
    $huburl = optional_param('huburl', '', PARAM_URL);
    $hubname = optional_param('hubname', '', PARAM_TEXT);
    if (empty($huburl) or !confirm_sesskey()) {
        throw new moodle_exception('missingparameter');
    }

    //set the publication form
    $advertise = optional_param('advertise', false, PARAM_BOOL);
    $share = optional_param('share', false, PARAM_BOOL);
    $coursepublicationform = new course_publication_form( '',
            array('huburl' => $huburl, 'hubname' => $hubname, 'sesskey' => sesskey(),
                    'course' => $course, 'advertise' => $advertise, 'share' => $share,
                    'id' => $id));
    $fromform = $coursepublicationform->get_data();

    if (!empty($fromform)) {

        //retrieve the course information
        $courseinfo = new stdClass();
        $courseinfo->fullname = $fromform->name;
        $courseinfo->shortname = $fromform->courseshortname;
        $courseinfo->description = $fromform->description;
        $courseinfo->language = $fromform->language;
        $courseinfo->publishername = $fromform->publishername;
        $courseinfo->publisheremail = $fromform->publisheremail;
        $courseinfo->contributornames = $fromform->contributornames;
        $courseinfo->coverage = $fromform->coverage;
        $courseinfo->creatorname = $fromform->creatorname;
        $courseinfo->licenceshortname = $fromform->licence;
        $courseinfo->subject = $fromform->subject;
        $courseinfo->audience = $fromform->audience;
        $courseinfo->educationallevel = $fromform->educationallevel;
        $creatornotes = $fromform->creatornotes;
        $courseinfo->creatornotes = $creatornotes['text'];
        $courseinfo->creatornotesformat = $creatornotes['format'];
        $courseinfo->sitecourseid = $id;
        if ($share) {
            $courseinfo->demourl = $fromform->demourl;
            $courseinfo->enrollable = false;
        } else {
            $courseinfo->courseurl = $fromform->courseurl;
            $courseinfo->enrollable = true;
        }

        //save into screenshots field the references to the screenshot content hash
        //(it will be like a unique id from the hub perspective)
        if (!empty($fromform->screenshots)) {
            $screenshots = $fromform->screenshots;
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
        }

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

        //save the record into the published course table
        $publication =  $hub->get_publication($courseids[0]);
        if (empty($publication)) {
            //if never been published or if we share, we need to save this new publication record
            $hub->add_course_publication($registeredhub->id, $course->id, !$share, $courseids[0]);
        } else {
            //if we update the enrollable course publication we update the publication record
            $hub->update_enrollable_course_publication($publication->id);
        }


        // SEND FILES
         $curl = new curl();

        // send screenshots
        if (!empty($fromform->screenshots)) {
            require_once($CFG->dirroot. "/lib/filelib.php");
            $params = array('token' => $registeredhub->token, 'filetype' => SCREENSHOT_FILE_TYPE,
                    'courseshortname' => $courseinfo->shortname);
           
            foreach ($files as $file) {
                if ($file->is_valid_image()) {
                    $params['file'] = $file;
                    $params['filename'] = $file->get_filename();
                    $curl->post($huburl."/local/hub/webservice/upload.php", $params);
                }
            }
        }


        // send backup
        if ($share) {
            foreach ($courseids as $courseid) {
                $params = array();
                $params['filetype'] = BACKUP_FILE_TYPE;
                $params['courseid'] = $courseid;
                $params['file'] = $backupfile;
                $params['token'] = $registeredhub->token;
                $curl->post($huburl."/local/hub/webservice/upload.php", $params);
            }
            
            //Delete the backup from user_tohub
            $backupfile->delete();
        }

        //redirect to the index publis page
        redirect(new moodle_url('/course/publish/index.php',
                array('sesskey' => sesskey(), 'id' => $id, 'published' => true)));
    }


    /////// OUTPUT SECTION /////////////

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('publishcourseon', 'hub', !empty($hubname)?$hubname:$huburl), 3, 'main');
    if (!empty($courseregisteredmsg)) {
            echo $courseregisteredmsg;
    }
    $coursepublicationform->display();

    echo $OUTPUT->footer();

}
