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

require_once('../../../config.php');
require_once('forms.php');
require_once('lib.php');

$s = function($key, $a=null) {
    return get_string($key, 'gradeimport_smart', $a);
};

$id = required_param('id', PARAM_INT);

$url = new moodle_url('/grade/import/smart/index.php', array('id' => $id));
$PAGE->set_url($url);

if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('nocourseid');
}

require_login($course);

$context = context_course::instance($id);

$PAGE->set_context($context);

require_capability('moodle/grade:import', $context);
require_capability('gradeimport/smart:view', $context);

$filetext = optional_param('file_text', null, PARAM_TEXT);

print_grade_page_head($course->id, 'import', 'smart');

$fileform = new smart_file_form();
$resultsform = new smart_results_form(null, array('messages' => null));

if ($formdata = $fileform->get_data()) {
    $filetext = $fileform->get_file_content('userfile');
    $gradeitemid = $formdata->grade_item_id;

    $messages = array();

    $importsuccess = true;

    if (!$smartfile = smart_autodiscover_filetype($filetext)) {
        $messages[] = $s('file_not_identified');
        $importsuccess = false;
    }

    if ($importsuccess) {
        $smartfile->validate();
        $smartfile->extract_data();
        $smartfile->set_courseid($id);
        $smartfile->set_gi_id($gradeitemid);
        $smartfile->convert_ids();

        if (!$smartfile->insert_grades()) {
            $messages[] = $s('import_error');
            $importsuccess = false;
        }

        if ($smartfile->bad_lines) {
            foreach ($smartfile->bad_lines as $n => $line) {
                $messages[] = $s('bad_line', $n);
            }
        }

        if ($smartfile->bad_ids) {
            foreach ($smartfile->bad_ids as $userid) {
                $messages[] = $s('bad_userid', $userid);
            }
        }
    }

    if (!$importsuccess) {
        echo $OUTPUT->notification($s('failure'));
    } else {
        echo $OUTPUT->notification($s('success'), 'notifysuccess');
    }

    $data = array('messages' => $messages);

    if ($messages) {
        $resultsform = new smart_results_form(null, $data);
        $resultsform->display();
    }

    $url = new moodle_url('/grade/index.php', array('id' => $id));
    echo $OUTPUT->continue_button($url);
} else {
    $fileform->display();
}

echo $OUTPUT->footer();
