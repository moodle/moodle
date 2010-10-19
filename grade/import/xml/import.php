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

require_once '../../../config.php';
require_once 'lib.php';
require_once $CFG->libdir.'/filelib.php';

$url       = required_param('url', PARAM_URL); // only real urls here
$id        = required_param('id', PARAM_INT); // course id
$feedback  = optional_param('feedback', 0, PARAM_BOOL);

$url = new moodle_url('/grade/import/xml/import.php', array('id'=>$id,'url'=>$url));
if ($feedback !== 0) {
    $url->param('feedback', $feedback);
}
$PAGE->set_url($url);

if (!$course = $DB->get_record('course', array('id'=>$id))) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $id);

require_capability('moodle/grade:import', $context);
require_capability('gradeimport/xml:view', $context);


// Large files are likely to take their time and memory. Let PHP know
// that we'll take longer, and that the process should be recycled soon
// to free up memory.
@set_time_limit(0);
raise_memory_limit(MEMORY_EXTRA);

$text = download_file_content($url);
if ($text === false) {
    print_error('cannotreadfile');
}

$error = '';
$importcode = import_xml_grades($text, $course, $error);

if ($importcode !== false) {
    /// commit the code if we are up this far

    if (defined('USER_KEY_LOGIN')) {
        if (grade_import_commit($id, $importcode, $feedback, false)) {
            echo 'ok';
            die;
        } else {
            print_error('cannotimportgrade'); //TODO: localize
        }

    } else {
        print_grade_page_head($course->id, 'import', 'xml', get_string('importxml', 'grades'));

        grade_import_commit($id, $importcode, $feedback, true);

        echo $OUTPUT->footer();
        die;
    }

} else {
    print_error('error', 'gradeimport_xml');
}


