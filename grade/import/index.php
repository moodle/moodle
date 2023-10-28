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
 * Redirects the user to a default grades import plugin page.
 *
 * @package    core_grades
 * @copyright  2021 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

// Course ID.
$courseid = required_param('id', PARAM_INT);

$PAGE->set_url(new moodle_url('/grade/import/index.php', ['id' => $courseid]));

// Basic access checks.
if (!$course = $DB->get_record('course', ['id' => $courseid])) {
    throw new moodle_exception('invalidcourseid', 'error');
}
require_login($course);
$context = context_course::instance($courseid);
require_capability('moodle/grade:import', $context);

// Retrieve all grade import plugins the current user can access.
$importplugins = array_filter(core_component::get_plugin_list('gradeimport'),
    static function(string $importplugin) use ($context): bool {
        return has_capability("gradeimport/{$importplugin}:view", $context);
    },
    ARRAY_FILTER_USE_KEY
);

if (!empty($importplugins)) {
    $importplugin = array_key_first($importplugins);
    $url = new moodle_url("/grade/import/{$importplugin}/index.php", ['id' => $courseid]);
    redirect($url);
}

// Otherwise, output the page with a notification stating that there are no available grade import options.
$PAGE->set_title(get_string('import', 'grades'));
$PAGE->set_pagelayout('incourse');
$PAGE->set_heading($course->fullname);
$PAGE->set_pagetype('course-view-' . $course->format);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('import', 'grades'));
echo html_writer::div($OUTPUT->notification(get_string('nogradeimport', 'grades'), 'error'), 'mt-3');
echo $OUTPUT->footer();
