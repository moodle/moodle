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
 * @package mod_dataform
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * The Dataform has been developed as an enhanced counterpart
 * of Moodle's Database activity module (1.9.11+ (20110323)).
 * To the extent that Dataform code corresponds to Database code,
 * certain copyrights on the Database module may obtain
 */

require_once("../../config.php");
require_once("$CFG->dirroot/mod/dataform/lib.php");

$courseid = optional_param('id', 0, PARAM_INT);

$modulename = get_string('modulename', 'dataform');
$modulenameplural  = get_string('modulenameplural', 'dataform');
$pageurl = new moodle_url('/mod/dataform/index.php');

// Get the course if we have course id.
if (!$courseid or !$course = $DB->get_record('course', array('id' => $courseid))) {
    $courseid = 0;
}

if (!$courseid) {
    $PAGE->set_context(context_system::instance());
} else {
    $pageurl->param('id', $courseid);
    $context = context_course::instance($course->id);
    $PAGE->set_context($context);
    require_course_login($course);
}

$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add($modulename, $pageurl);
$PAGE->set_title($modulename);
$PAGE->set_heading('');

// Course menu selector.
$courseselector = null;
// Display a list of course where the user has indexview capability.
if ($courses = get_user_capability_course('mod/dataform:indexview', null, true, 'fullname')) {
    $coursemenu = array();
    foreach ($courses as $crs) {
        $coursemenu[$crs->id] = $crs->fullname;
    }
    $select = new \single_select($pageurl, 'id', $coursemenu, $courseid, array(0 => get_string('choosedots')), 'index_jump');
    $select->set_label(get_string('course'). '&nbsp;');
    $courseselector = $OUTPUT->render($select);
}

echo $OUTPUT->header();
echo $courseselector;

// Without course id, display just the selector.
if (!$courseid) {
    echo $OUTPUT->footer();
    exit;
}

// Must have indexview capability.
require_capability('mod/dataform:indexview', $context);

if (!$dataforms = get_all_instances_in_course('dataform', $course)) {
    notice(get_string('thereareno', 'moodle', $modulenameplural) , new moodle_url('/course/view.php', array('id' => $course->id)));
}

if ($usesections = course_format_uses_sections($course->format)) {
    $modinfo = get_fast_modinfo($course);
    $sections = $modinfo->get_section_info_all();
}

$table = new html_table();
$table->attributes['align'] = 'center';
$table->attributes['cellpadding'] = '2';
$table->head  = array ();
$table->align = array ();

// Section.
if ($usesections) {
    $table->head[] = get_string('sectionname', 'format_'.$course->format);
    $table->align[] = 'center';
}

// Name.
$table->head[] = get_string('name');
$table->align[] = 'left';

// Description.
$table->head[] = get_string('description');
$table->align[] = 'left';

// Number of entries.
$table->head[] = get_string('entries', 'dataform');
$table->align[] = 'center';

// Rss.
$rss = (!empty($CFG->enablerssfeeds) and !empty($CFG->dataform_enablerssfeeds));
if ($rss) {
    $table->head[] = get_string('rss');
    $table->align[] = 'center';
}

// Actions.
if ($showeditbuttons = $PAGE->user_allowed_editing()) {
    $table->head[] = '';
    $table->align[] = 'center';
    $editingurl = new moodle_url('/course/mod.php',
                                array('sesskey' => sesskey()));
}

$options = new \stdClass;
$options->noclean = true;
$currentsection = null;
$stredit = get_string('edit');
$strdelete = get_string('delete');

foreach ($dataforms as $dataform) {
    $tablerow = array();

    $df = mod_dataform_dataform::instance($dataform->id);

    if (!has_capability('mod/dataform:indexview', $df->context)) {
        continue;
    }

    // Section.
    if ($usesections) {
        if ($dataform->section !== $currentsection) {
            if ($currentsection !== null) {
                $table->data[] = 'hr';
            }
            $currentsection = $dataform->section;
            $tablerow[] = get_section_name($course, $sections[$dataform->section]);
        } else {
            $tablerow[] = '';
        }
    }

    // Name (linked; dim if not visible).
    $linkparams = !$dataform->visible ? array('class' => 'dimmed') : null;
    $linkedname = html_writer::link(new moodle_url('/mod/dataform/view.php', array('id' => $dataform->coursemodule)),
                                format_string($dataform->name, true),
                                $linkparams);
    $tablerow[] = $linkedname;

    // Description.
    $tablerow[] = format_text($dataform->intro, $dataform->introformat, $options);

    // Number of entries.
    $tablerow[] = $df->get_entries_count(mod_dataform_dataform::COUNT_ALL);

    // Rss.
    $rsslinks = array();
    if ($rss and $rssviews = $df->get_rss_views()) {
        foreach ($rssviews as $view) {
            $rsslinks[] = $view->get_rss_link();
        }
    }
    $tablerow[] = implode(' ', $rsslinks);

    if ($showeditbuttons) {
        $buttons = array();
        $editingurl->param('update', $dataform->coursemodule);
        $buttons['edit'] = html_writer::link($editingurl, $OUTPUT->pix_icon('t/edit', $stredit));
        $editingurl->remove_params('update');

        $editingurl->param('delete', $dataform->coursemodule);
        $buttons['delete'] = html_writer::link($editingurl, $OUTPUT->pix_icon('t/delete', $strdelete));
        $editingurl->remove_params('delete');

        $tablerow[] = implode('&nbsp;&nbsp;&nbsp;', $buttons);
    }

    $table->data[] = $tablerow;
}

echo html_writer::empty_tag('br');
echo html_writer::tag('div', html_writer::table($table), array('class' => 'no-overflow'));
echo $OUTPUT->footer();
