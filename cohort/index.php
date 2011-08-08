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
 * Cohort related management functions, this file needs to be included manually.
 *
 * @package    core
 * @subpackage cohort
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require_once($CFG->libdir.'/adminlib.php');

$contextid = optional_param('contextid', 0, PARAM_INT);

require_login();

if ($contextid) {
    $context = get_context_instance_by_id($contextid, MUST_EXIST);
} else {
    $context = get_context_instance(CONTEXT_SYSTEM);
}

if ($context->contextlevel != CONTEXT_COURSECAT and $context->contextlevel != CONTEXT_SYSTEM) {
    print_error('invalidcontext');
}

$category = null;
if ($context->contextlevel == CONTEXT_COURSECAT) {
    $category = $DB->get_record('course_categories', array('id'=>$context->instanceid), '*', MUST_EXIST);
}

$manager = has_capability('moodle/cohort:manage', $context);
$canassign = has_capability('moodle/cohort:assign', $context);
if (!$manager) {
    require_capability('moodle/cohort:view', $context);
}

$strcohorts = get_string('cohorts', 'cohort');

if ($category) {
    $PAGE->set_pagelayout('report');
    $PAGE->set_context($context);
    $PAGE->set_url('/cohort/index.php', array('contextid'=>$context->id));
    $PAGE->set_title($strcohorts);
    $PAGE->set_heading($COURSE->fullname);
    $PAGE->navbar->add($category->name, new moodle_url('/course/index.php', array('categoryedit'=>'1')));
    $PAGE->navbar->add($strcohorts);
} else {
    admin_externalpage_setup('cohorts', '', null, '', array('pagelayout'=>'report'));
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('cohortsin', 'cohort', print_context_name($context)));

$cohorts = $DB->get_records('cohort', array('contextid'=>$context->id));

$data = array();
foreach($cohorts as $cohort) {
    $line = array();
    $line[] = format_string($cohort->name);
    $line[] = $cohort->idnumber;
    $line[] = format_text($cohort->description, $cohort->descriptionformat);

    $line[] = $DB->count_records('cohort_members', array('cohortid'=>$cohort->id));

    if (empty($cohort->component)) {
        $line[] = get_string('nocomponent', 'cohort');
    } else {
        $line[] = get_string('pluginname', $cohort->component);
    }

    $buttons = array();
    if (empty($cohort->component)) {
        if ($manager) {
            $buttons[] = html_writer::link(new moodle_url('/cohort/edit.php', array('id'=>$cohort->id, 'delete'=>1)), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'), 'alt'=>get_string('delete'), 'class'=>'iconsmall')));
            $buttons[] =  html_writer::link(new moodle_url('/cohort/edit.php', array('id'=>$cohort->id)), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/edit'), 'alt'=>get_string('edit'), 'class'=>'iconsmall')));
        }
        if ($manager or $canassign) {
            $buttons[] = html_writer::link(new moodle_url('/cohort/assign.php', array('id'=>$cohort->id)), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('i/users'), 'alt'=>get_string('assign', 'core_cohort'), 'class'=>'iconsmall')));
        }
    }
    $line[] = implode(' ', $buttons);

    $data[] = $line;
}
$table = new html_table();
$table->head  = array(get_string('name', 'cohort'), get_string('idnumber', 'cohort'), get_string('description', 'cohort'),
                      get_string('memberscount', 'cohort'), get_string('component', 'cohort'), get_string('edit'));
$table->size  = array('20%', '10%', '40%', '10%', '10%', '10%');
$table->align = array('left', 'left', 'left', 'left','center', 'center');
$table->width = '80%';
$table->data  = $data;
echo html_writer::table($table);

if ($manager) {
    echo $OUTPUT->single_button(new moodle_url('/cohort/edit.php', array('contextid'=>$context->id)), get_string('add'));
}

echo $OUTPUT->footer();