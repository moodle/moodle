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

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('publishon', 'hub'), 3, 'main');

    //check if the site is registered on Moodle.org hub
    //check if the site is registered on any other specific hub
    $hub = new hub();
    $registeredonmoodleorg = false;
    $registeredonhub = false;

    $hubs = $hub->get_registered_on_hubs();
    foreach ($hubs as $hub) {
        if ($hub->huburl == MOODLEORGHUBURL) {
            $registeredonmoodleorg = true;
        } else {
            $registeredonhub = true;
        }
    }

    echo $renderer->publicationselector($course->id, $registeredonmoodleorg, $registeredonhub);
    echo $OUTPUT->footer();

}