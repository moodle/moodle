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

$courseid = required_param('id', PARAM_INT); // Course id.
$publicationid = optional_param('publicationid', 0, PARAM_INT); // Id of course publication to unpublish.

require_login($courseid);
$shortname = format_string($COURSE->shortname);

$PAGE->set_url('/course/publish/index.php', array('id' => $courseid));
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('publish', 'core_hub') . ': ' . $COURSE->fullname);
$PAGE->set_heading($COURSE->fullname);

require_capability('moodle/course:publish', context_course::instance($courseid));

// If the site is not registered display an error page.
if (!\core\hub\registration::is_registered()) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('publishcourseon', 'hub', 'Moodle.net'), 3, 'main');
    echo $OUTPUT->box(get_string('notregisteredonhub', 'hub'));
    if (has_capability('moodle/site:config', context_system::instance())) {
        echo $OUTPUT->single_button(new moodle_url('/admin/registration/index.php'), get_string('register', 'admin'));
    }
    echo $OUTPUT->footer();
    die();
}

// When hub listing status is requested update statuses of all published courses.
$updatestatusid = optional_param('updatestatusid', false, PARAM_INT);
if (!empty($updatestatusid) && confirm_sesskey()) {
    if (core\hub\publication::get_publication($updatestatusid, $courseid)) {
        core\hub\publication::request_status_update();
        redirect($PAGE->url);
    }
}

$renderer = $PAGE->get_renderer('core', 'course');

// Unpublish course.
if ($publication = \core\hub\publication::get_publication($publicationid, $courseid)) {
    $confirm = optional_param('confirm', 0, PARAM_BOOL);
    if ($confirm && confirm_sesskey()) {
        \core\hub\publication::unpublish($publication);
    } else {
        // Display confirmation page for unpublishing.
        $publication = \core\hub\publication::get_publication($publicationid, $courseid, MUST_EXIST);
        $publication->courseshortname = format_string($COURSE->shortname);
        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('unpublishcourse', 'hub', $shortname), 3, 'main');
        echo $renderer->confirmunpublishing($publication);
        echo $OUTPUT->footer();
        die();
    }
}

// List current publications and "Publish" buttons.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('publishcourse', 'hub', $shortname), 3, 'main');
echo $renderer->publicationselector($courseid);

$publications = \core\hub\publication::get_course_publications($courseid);
if (!empty($publications)) {
    echo $OUTPUT->heading(get_string('publishedon', 'hub'), 3, 'main');
    echo $renderer->registeredonhublisting($courseid, $publications);
}

echo $OUTPUT->footer();
