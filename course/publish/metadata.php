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
        throw new moodle_exception('cannotaccessthispage');
    }

    //set the publication form
    $advertise = optional_param('advertise', false, PARAM_BOOL);
    $share = optional_param('share', false, PARAM_BOOL);
    $coursepublicationform = new course_publication_form( new moodle_url('/course/publish/publish.php'),
            array('huburl' => $huburl, 'hubname' => $hubname, 'sesskey' => sesskey(),
                    'course' => $course, 'advertise' => $advertise, 'share' => $share,
                    'id' => $id));
    $fromform = $coursepublicationform->get_data();


    /////// OUTPUT SECTION /////////////

    echo $OUTPUT->header();

    $coursepublicationform->display();

    echo $OUTPUT->footer();

}