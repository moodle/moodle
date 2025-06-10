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
 *
 * @package    enrol_ues
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Philip Cali, Adam Zapletal, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/enrol/ues/publiclib.php');

// Grab the required Data Access Objects.
ues::require_daos();

// Ensure the user is logged in.
require_login();

// Ensure the user is the site admin, if not send them back to /my.
if (!is_siteadmin($USER->id)) {
    redirect(new moodle_url('/my'));
}

$confirmed = optional_param('confirmed', null, PARAM_INT);

// Sting replacement function.
$s = ues::gen_str();

// Get the plugin name.
$pluginname = $s('pluginname');

// Set the action.
$action = $s('semester_ignore');

// Set the base URL.
$baseurl = new moodle_url('/admin/settings.php', array(
    'section' => 'enrolsettingsues'
));

// Set up the page.
$PAGE->set_context(context_system::instance());
$PAGE->set_title($pluginname . ': '. $action);
$PAGE->set_heading($pluginname . ': '. $action);
$PAGE->set_url('/enrol/ues/ignore.php');
$PAGE->set_pagetype('admin-settings-ues-semester-ignore');
$PAGE->set_pagelayout('admin');

// Set the navigation bar.
$PAGE->navbar->add($pluginname, $baseurl);
$PAGE->navbar->add($action);

// Grabd the semesters.
$semesters = ues_semester::get_all(array(), true);

if ($confirmed and $data = data_submitted()) {
    $semesterids = explode(',', $confirmed);

    foreach (get_object_vars($data) as $field => $value) {
        if (!preg_match('/semester_(\d+)/', $field, $ids)) {
            continue;
        }

        if (!isset($semesters[$ids[1]])) {
            continue;
        }

        $semester = $semesters[$ids[1]];
        $semester->semester_ignore = $value;

        $semester->save();
    }

    redirect(new moodle_url('/enrol/ues/ignore.php'));
    exit;
}

// Output the page header.
echo $OUTPUT->header();
echo $OUTPUT->heading($action);

if ($posts = data_submitted()) {
    $postparams = array();
    $data = html_writer::start_tag('ul');
    foreach (get_object_vars($posts) as $field => $value) {
        if (!preg_match('/semester_(\d+)/', $field, $matches)) {
            continue;
        }

        $id = $matches[1];
        if (!isset($semesters[$id])) {
            continue;
        }

        $semester = $semesters[$id];
        $curr = isset($semester->semester_ignore) ? $semester->semester_ignore : 0;

        // Filter same value.
        if ($curr == $value) {
            continue;
        }

        $sems = $semester->__toString();
        $stm = empty($value) ? $s('be_recoged', $sems) : $s('be_ignored', $sems);

        $data .= html_writer::tag('li', $stm);
        $postparams[$field] = $value;
    }
    $data .= html_writer::end_tag('ul');

    $msg = $s('please_note', $data);
    $confirmurl = new moodle_url('/enrol/ues/ignore.php', $postparams + array(
        'confirmed' => 1
    ));
    $cancelurl = new moodle_url('/enrol/ues/ignore.php');

    echo $OUTPUT->confirm($msg, $confirmurl, $cancelurl);
    echo $OUTPUT->footer();
    exit;
}

// Set up the table.
$table = new html_table();

// Set up the table header.
$table->head = array(
    $s('year'), get_string('name'), $s('campus'), $s('session_key'),
    $s('sections'), $s('ignore')
);

// Set up the table data array.
$table->data = array();

foreach ($semesters as $semester) {
    $name = 'semester_' . $semester->id;
    $hiddenparams = array(
        'name' => $name,
        'type' => 'hidden',
        'value' => 0
    );

    $checkboxparams = array(
        'name' => $name,
        'type' => 'checkbox',
        'value' => 1
    );

    if (!empty($semester->semester_ignore)) {
        $checkboxparams['checked'] = 'CHECKED';
    }

    $line = array(
        $semester->year,
        $semester->name,
        $semester->campus,
        $semester->session_key,
        ues_section::count(array('semesterid' => $semester->id)),
        html_writer::empty_tag('input', $hiddenparams) .
        html_writer::empty_tag('input', $checkboxparams)
    );

    $table->data[] = new html_table_row($line);
}

echo html_writer::start_tag('form', array('method' => 'POST'));
echo html_writer::table($table);

echo html_writer::start_tag('div', array('class' => 'buttons'));

echo html_writer::empty_tag('input', array(
    'type' => 'submit',
    'name' => 'ignore',
    'value' => $s('ignore')
));

echo html_writer::end_tag('div');
echo html_writer::end_tag('form');

echo $OUTPUT->footer();
