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
 * Reset course indentation
 *
 * @copyright 2023 Amaia Anabitarte <amaia@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

require_admin();

$format = required_param('format', PARAM_PLUGIN);
$confirm = optional_param('confirm', false, PARAM_BOOL);
$backurl = new moodle_url('/admin/settings.php', ['section' => 'formatsetting'.$format]);

$PAGE->set_url('/admin/course/resetindentation.php', ['format' => $format]);
$PAGE->set_context(context_system::instance());

if ($confirm) {
    require_sesskey();
    $courses = $DB->get_records('course', ['format' => $format], 'id', 'id');
    if (!empty($courses)) {
        $courseids = array_keys($courses);
        list($courseinsql, $courseparams) = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED, 'course');
        $DB->set_field_select('course_modules', 'indent', '0', "course $courseinsql AND indent <> 0", $courseparams);
        rebuild_course_cache(0, true);
    }
    redirect(
        $backurl,
        get_string('resetindentationsuccess', 'admin'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$strtitle = get_string('resetindentation', 'admin');

$PAGE->set_title($strtitle);
$PAGE->set_heading($strtitle);

navigation_node::override_active_url(new moodle_url(
    '/admin/course/resetindentation.php',
    ['action' => 'confirm', 'format' => $format]
));

echo $OUTPUT->header();

$displayoptions = ['confirmtitle' => get_string('resetindentation_title', 'admin')];
$confirmbutton = new single_button(
    new moodle_url('/admin/course/resetindentation.php', ['confirm' => 1, 'format' => $format, 'sesskey' => sesskey()]),
    get_string('resetindentation', 'admin'),
    'post',
    single_button::BUTTON_DANGER
);
$cancelbutton = new single_button($backurl, get_string('cancel'));
echo $OUTPUT->confirm(
    get_string('resetindentation_help', 'admin', ['format' => get_string('pluginname', 'format_'.$format)]),
    $confirmbutton,
    $cancelbutton,
    $displayoptions
);

echo $OUTPUT->footer();
