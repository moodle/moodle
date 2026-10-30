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

/**
 * Minimal Mentor home (assigned lp-* courses; review UI comes later).
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

use local_launchpad\account_persona;
use local_launchpad\persona_home;

require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url(persona_home::MENTOR_PATH));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('mentor_area_title', 'local_launchpad'));
$PAGE->set_heading(get_string('mentor_area_heading', 'local_launchpad'));

if (!account_persona::is_mentor((int) $USER->id) && !is_siteadmin()) {
    throw new \moodle_exception('mentor_access_denied', 'local_launchpad');
}

$courses = persona_home::list_mentor_courses((int) $USER->id);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('mentor_area_heading', 'local_launchpad'));
echo html_writer::tag('p', get_string('mentor_area_body', 'local_launchpad'));

if ($courses === []) {
    echo html_writer::tag('p', get_string('mentor_area_no_courses', 'local_launchpad'), ['class' => 'muted']);
} else {
    echo html_writer::tag('h3', get_string('mentor_area_courses', 'local_launchpad'));
    $items = '';
    foreach ($courses as $course) {
        $url = new moodle_url('/course/view.php', ['id' => $course->id]);
        $label = format_string($course->fullname);
        if (!empty($course->idnumber)) {
            $label .= ' (' . s($course->idnumber) . ')';
        }
        $items .= html_writer::tag('li', html_writer::link($url, $label));
    }
    echo html_writer::tag('ul', $items, ['class' => 'local-launchpad-mentor-courses']);
}

echo $OUTPUT->footer();
