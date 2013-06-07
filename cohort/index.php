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
 * @package    core_cohort
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../config.php');
require($CFG->dirroot.'/cohort/lib.php');
require_once($CFG->libdir.'/adminlib.php');

$contextid = optional_param('contextid', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$searchquery  = optional_param('search', '', PARAM_RAW);

require_login();

if ($contextid) {
    $context = context::instance_by_id($contextid, MUST_EXIST);
} else {
    $context = context_system::instance();
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
} else {
    admin_externalpage_setup('cohorts', '', null, '', array('pagelayout'=>'report'));
}

echo $OUTPUT->header();

$cohorts = cohort_get_cohorts($context->id, $page, 25, $searchquery);

$count = '';
if ($cohorts['allcohorts'] > 0) {
    if ($searchquery === '') {
        $count = ' ('.$cohorts['allcohorts'].')';
    } else {
        $count = ' ('.$cohorts['totalcohorts'].'/'.$cohorts['allcohorts'].')';
    }
}

echo $OUTPUT->heading(get_string('cohortsin', 'cohort', $context->get_context_name()).$count);

// Add search form.
$search  = html_writer::start_tag('form', array('id'=>'searchcohortquery', 'method'=>'get'));
$search .= html_writer::start_tag('div');
$search .= html_writer::label(get_string('searchcohort', 'cohort'), 'cohort_search_q'); // No : in form labels!
$search .= html_writer::empty_tag('input', array('id'=>'cohort_search_q', 'type'=>'text', 'name'=>'search', 'value'=>$searchquery));
$search .= html_writer::empty_tag('input', array('type'=>'submit', 'value'=>get_string('search', 'cohort')));
$search .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'contextid', 'value'=>$contextid));
$search .= html_writer::end_tag('div');
$search .= html_writer::end_tag('form');
echo $search;


// Output pagination bar.
$params = array('page' => $page);
if ($contextid) {
    $params['contextid'] = $contextid;
}
if ($search) {
    $params['search'] = $searchquery;
}
$baseurl = new moodle_url('/cohort/index.php', $params);
echo $OUTPUT->paging_bar($cohorts['totalcohorts'], $page, 25, $baseurl);

$data = array();
foreach($cohorts['cohorts'] as $cohort) {
    $line = array();
    $line[] = format_string($cohort->name);
    $line[] = s($cohort->idnumber); // All idnumbers are plain text.
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
echo $OUTPUT->paging_bar($cohorts['totalcohorts'], $page, 25, $baseurl);

if ($manager) {
    echo $OUTPUT->single_button(new moodle_url('/cohort/edit.php', array('contextid'=>$context->id)), get_string('add'));
}

echo $OUTPUT->footer();
