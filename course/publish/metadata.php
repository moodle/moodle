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
require_once($CFG->dirroot . '/course/publish/forms.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/registration/lib.php');
require_once($CFG->dirroot . '/course/publish/lib.php');
require_once($CFG->libdir . '/filelib.php');


//check user access capability to this page
$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_login($course);

//page settings
$PAGE->set_url('/course/publish/metadata.php', array('id' => $course->id));
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('course') . ': ' . $course->fullname);
$PAGE->set_heading($course->fullname);

//check that the PHP xmlrpc extension is enabled
if (!extension_loaded('xmlrpc')) {
    $errornotification = $OUTPUT->doc_link('admin/environment/php_extension/xmlrpc', '');
    $errornotification .= get_string('xmlrpcdisabledpublish', 'hub');
    $context = context_course::instance($course->id);
    $shortname = format_string($course->shortname, true, array('context' => $context));
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('publishcourse', 'hub', $shortname), 3, 'main');
    echo $OUTPUT->notification($errornotification);
    echo $OUTPUT->footer();
    die();
}

if (has_capability('moodle/course:publish', context_course::instance($id))) {

    //retrieve hub name and hub url
    $huburl = optional_param('huburl', '', PARAM_URL);
    $hubname = optional_param('hubname', '', PARAM_TEXT);
    if (empty($huburl) or !confirm_sesskey()) {
        throw new moodle_exception('missingparameter');
    }

    //set the publication form
    $advertise = optional_param('advertise', false, PARAM_BOOL);
    $share = optional_param('share', false, PARAM_BOOL);
    $coursepublicationform = new course_publication_form('',
                    array('huburl' => $huburl, 'hubname' => $hubname, 'sesskey' => sesskey(),
                        'course' => $course, 'advertise' => $advertise, 'share' => $share,
                        'id' => $id, 'page' => $PAGE));
    $fromform = $coursepublicationform->get_data();

    //retrieve the token to call the hub
    $registrationmanager = new registration_manager();
    $registeredhub = $registrationmanager->get_registeredhub($huburl);

    //setup web service xml-rpc client
    $serverurl = $huburl . "/local/hub/webservice/webservices.php";
    require_once($CFG->dirroot . "/webservice/xmlrpc/lib.php");
    $xmlrpcclient = new webservice_xmlrpc_client($serverurl, $registeredhub->token);

    if (!empty($fromform)) {

        $publicationmanager = new course_publish_manager();

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
        if (!empty($fromform->deletescreenshots)) {
            $courseinfo->deletescreenshots = $fromform->deletescreenshots;
        }
        if ($share) {
            $courseinfo->demourl = $fromform->demourl;
            $courseinfo->enrollable = false;
        } else {
            $courseinfo->courseurl = $fromform->courseurl;
            $courseinfo->enrollable = true;
        }

        //retrieve the outcomes of this course
        require_once($CFG->libdir . '/grade/grade_outcome.php');
        $outcomes = grade_outcome::fetch_all_available($id);
        if (!empty($outcomes)) {
            foreach ($outcomes as $outcome) {
                $sentoutcome = new stdClass();
                $sentoutcome->fullname = $outcome->fullname;
                $courseinfo->outcomes[] = $sentoutcome;
            }
        }

        //retrieve the content information from the course
        $coursecontext = context_course::instance($course->id);
        $courseblocks = $publicationmanager->get_block_instances_by_context($coursecontext->id, 'blockname');

        if (!empty($courseblocks)) {
            $blockname = '';
            foreach ($courseblocks as $courseblock) {
                if ($courseblock->blockname != $blockname) {
                    if (!empty($blockname)) {
                        $courseinfo->contents[] = $content;
                    }

                    $blockname = $courseblock->blockname;
                    $content = new stdClass();
                    $content->moduletype = 'block';
                    $content->modulename = $courseblock->blockname;
                    $content->contentcount = 1;
                } else {
                    $content->contentcount = $content->contentcount + 1;
                }
            }
            $courseinfo->contents[] = $content;
        }

        $activities = get_fast_modinfo($course, $USER->id);
        foreach ($activities->instances as $activityname => $activitydetails) {
            $content = new stdClass();
            $content->moduletype = 'activity';
            $content->modulename = $activityname;
            $content->contentcount = count($activities->instances[$activityname]);
            $courseinfo->contents[] = $content;
        }

        //save into screenshots field the references to the screenshot content hash
        //(it will be like a unique id from the hub perspective)
        if (!empty($fromform->deletescreenshots) or $share) {
            $courseinfo->screenshots = 0;
        } else {
            $courseinfo->screenshots = $fromform->existingscreenshotnumber;
        }
        if (!empty($fromform->screenshots)) {
            $screenshots = $fromform->screenshots;
            $fs = get_file_storage();
            $files = $fs->get_area_files(context_user::instance($USER->id)->id, 'user', 'draft', $screenshots);
            if (!empty($files)) {
                $courseinfo->screenshots = $courseinfo->screenshots + count($files) - 1; //minus the ./ directory
            }
        }

        // PUBLISH ACTION

        //publish the course information
        $function = 'hub_register_courses';
        $params = array('courses' => array($courseinfo));
        try {
            $courseids = $xmlrpcclient->call($function, $params);
        } catch (Exception $e) {
            throw new moodle_exception('errorcoursepublish', 'hub',
                    new moodle_url('/course/view.php', array('id' => $id)), $e->getMessage());
        }

        if (count($courseids) != 1) {
            throw new moodle_exception('errorcoursewronglypublished', 'hub');
        }

        //save the record into the published course table
        $publication = $publicationmanager->get_publication($courseids[0], $huburl);
        if (empty($publication)) {
            //if never been published or if we share, we need to save this new publication record
            $publicationmanager->add_course_publication($registeredhub->huburl, $course->id, !$share, $courseids[0]);
        } else {
            //if we update the enrollable course publication we update the publication record
            $publicationmanager->update_enrollable_course_publication($publication->id);
        }

        // SEND FILES
        $curl = new curl();

        // send screenshots
        if (!empty($fromform->screenshots)) {

            if (!empty($fromform->deletescreenshots) or $share) {
                $screenshotnumber = 0;
            } else {
                $screenshotnumber = $fromform->existingscreenshotnumber;
            }
            foreach ($files as $file) {
                if ($file->is_valid_image()) {
                    $screenshotnumber = $screenshotnumber + 1;
                    $params = array();
                    $params['filetype'] = HUB_SCREENSHOT_FILE_TYPE;
                    $params['file'] = $file;
                    $params['courseid'] = $courseids[0];
                    $params['filename'] = $file->get_filename();
                    $params['screenshotnumber'] = $screenshotnumber;
                    $params['token'] = $registeredhub->token;
                    $curl->post($huburl . "/local/hub/webservice/upload.php", $params);
                }
            }
        }

        //redirect to the backup process page
        if ($share) {
            $params = array('sesskey' => sesskey(), 'id' => $id, 'hubcourseid' => $courseids[0],
                'huburl' => $huburl, 'hubname' => $hubname);
            $backupprocessurl = new moodle_url("/course/publish/backup.php", $params);
            redirect($backupprocessurl);
        } else {
            //redirect to the index publis page
            redirect(new moodle_url('/course/publish/index.php',
                            array('sesskey' => sesskey(), 'id' => $id, 'published' => true,
                                'hubname' => $hubname, 'huburl' => $huburl)));
        }
    }

    /////// OUTPUT SECTION /////////////

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('publishcourseon', 'hub', !empty($hubname) ? $hubname : $huburl), 3, 'main');

    //display hub information (logo, name, description)
    $function = 'hub_get_info';
    $params = array();
    try {
        $hubinfo = $xmlrpcclient->call($function, $params);
    } catch (Exception $e) {
        //only print error log in apache (for backward compatibility)
        error_log(print_r($e->getMessage(), true));
    }
    $renderer = $PAGE->get_renderer('core', 'publish');
    if (!empty($hubinfo)) {
        echo $renderer->hubinfo($hubinfo);
    }

    //display metadata form
    $coursepublicationform->display();
    echo $OUTPUT->footer();
}
