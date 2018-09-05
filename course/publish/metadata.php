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

require_capability('moodle/course:publish', context_course::instance($id));

// Retrieve hub name and hub url.
require_sesskey();

// Set the publication form.
$advertise = optional_param('advertise', false, PARAM_BOOL);
$publicationid = optional_param('publicationid', false, PARAM_INT);
$formparams = array('course' => $course, 'advertise' => $advertise);
if ($publicationid) {
    $publication = \core\hub\publication::get_publication($publicationid, $course->id, MUST_EXIST);
    $formparams['publication'] = $publication;
    $advertise = $formparams['advertise'] = $publication->enrollable;
}
$share = !$advertise;
$coursepublicationform = new \core\hub\course_publication_form('', $formparams);
$fromform = $coursepublicationform->get_data();

if (!empty($fromform)) {

    // Retrieve the course information.
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

    // Retrieve the outcomes of this course.
    require_once($CFG->libdir . '/grade/grade_outcome.php');
    $outcomes = grade_outcome::fetch_all_available($id);
    if (!empty($outcomes)) {
        foreach ($outcomes as $outcome) {
            $sentoutcome = new stdClass();
            $sentoutcome->fullname = $outcome->fullname;
            $courseinfo->outcomes[] = $sentoutcome;
        }
    }

    // Retrieve the content information from the course.
    $coursecontext = context_course::instance($course->id);
    $courseblocks = \core\hub\publication::get_block_instances_by_context($coursecontext->id);

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

    // Save into screenshots field the references to the screenshot content hash
    // (it will be like a unique id from the hub perspective).
    if (!empty($fromform->deletescreenshots) or $share) {
        $courseinfo->screenshots = 0;
    } else {
        $courseinfo->screenshots = $fromform->existingscreenshotnumber;
    }
    $files = [];
    if (!empty($fromform->screenshots)) {
        $fs = get_file_storage();
        $files = $fs->get_area_files(context_user::instance($USER->id)->id, 'user', 'draft', $fromform->screenshots,
            'filepath, filename', false);
        $files = array_filter($files, function(stored_file $file) {
            return $file->is_valid_image();
        });
        $courseinfo->screenshots += count($files);
    }

    // PUBLISH ACTION.
    $hubcourseid = \core\hub\publication::publish_course($courseinfo, $files);

    // Redirect to the backup process page.
    if ($share) {
        $params = array('sesskey' => sesskey(), 'id' => $id, 'hubcourseid' => $hubcourseid);
        $backupprocessurl = new moodle_url("/course/publish/backup.php", $params);
        redirect($backupprocessurl);
    } else {
        // Redirect to the index publis page.
        redirect(new moodle_url('/course/publish/index.php', ['id' => $id]),
            get_string('coursepublished', 'hub', 'Moodle.net'), null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

// OUTPUT SECTION.

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('publishcourseon', 'hub', 'Moodle.net'), 3, 'main');

// Display hub information (logo, name, description).
$renderer = $PAGE->get_renderer('core', 'course');
if ($hubinfo = \core\hub\registration::get_moodlenet_info()) {
    echo $renderer->hubinfo($hubinfo);
}

// Display metadata form.
$coursepublicationform->display();
echo $OUTPUT->footer();
