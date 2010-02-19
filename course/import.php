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
 * Display all the interfaces for importing data into a specific course
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once('../config.php');

$id = required_param('id', PARAM_INT);   // course id to import TO

$PAGE->set_url('/course/import.php', array('id'=>$id));

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error("That's an invalid course id");
}

require_login($course->id);

require_capability('moodle/restore:restoretargetimport', get_context_instance(CONTEXT_COURSE, $id));

/// Always we begin an import, we delete all backup/restore/import session structures
if (isset($SESSION->course_header)) {
    unset ($SESSION->course_header);
}
if (isset($SESSION->info)) {
    unset ($SESSION->info);
}
if (isset($SESSION->backupprefs)) {
    unset ($SESSION->backupprefs);
}
if (isset($SESSION->restore)) {
    unset ($SESSION->restore);
}
if (isset($SESSION->import_preferences)) {
    unset ($SESSION->import_preferences);
}

$strimport = get_string('import');

$PAGE->set_title($course->fullname.': '.$strimport);
$PAGE->set_heading($course->fullname.': '.$strimport);
$PAGE->navbar->add($strimport);

echo $OUTPUT->header();

$imports = get_plugin_list('import');

foreach ($imports as $import => $importdir) {
    echo '<div class="plugin">';
    include($importdir.'/mod.php');
    echo '</div>';
}

echo $OUTPUT->footer();

