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
 * Form for creating new H5P Content
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

// Get Course ID.
$id = optional_param('id', 0, PARAM_INT);

// Set URL.
$url = new \moodle_url('/mod/hvp/index.php', array('id' => $id));
$PAGE->set_url($url);

// Load Course.
$course = $DB->get_record('course', array('id' => $id));
if (!$course) {
    print_error('invalidcourseid');
}

// Require login.
require_course_login($course);
$PAGE->set_pagelayout('incourse');
$coursecontext = context_course::instance($course->id);

// Trigger instances list viewed event.
$params = array(
    'context' => context_course::instance($course->id)
);
$event = \mod_hvp\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot('course', $course);
$event->trigger();

// Set title and heading.
$PAGE->set_title($course->shortname . ': ' . get_string('modulenameplural', 'mod_hvp'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

// Load H5P list data.
$rawh5ps = $DB->get_records_sql("SELECT cm.id AS coursemodule,
                                     cw.section,
                                     cm.visible,
                                     h.name,
                                     hl.title AS librarytitle
                                FROM {course_modules} cm,
                                     {course_sections} cw,
                                     {modules} md,
                                     {hvp} h,
                                     {hvp_libraries} hl
                               WHERE cm.course = ?
                                 AND cm.instance = h.id
                                 AND cm.section = cw.id
                                 AND md.name = 'hvp'
                                 AND md.id = cm.module
                                 AND hl.id = h.main_library_id
                             ", array($course->id));
if (!$rawh5ps) {
    notice(get_string('noh5ps', 'mod_hvp'), "../../course/view.php?id={$course->id}");
    die;
}

$modinfo = get_fast_modinfo($course, null);
if (empty($modinfo->instances['hvp'])) {
    $h5ps = $rawh5ps;
} else {
    // Lets try to order these bad boys.
    $h5ps = [];
    foreach ($modinfo->instances['hvp'] as $cm) {
        if (!$cm->uservisible || !isset($rawh5ps[$cm->id])) {
            continue; // Not visible or not found.
        }
        if (!empty($cm->extra)) {
            $rawh5ps[$cm->id]->extra = $cm->extra;
        }
        $h5ps[] = $rawh5ps[$cm->id];
    }
}

// Print H5P list.
$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

$table->head = array();
$table->align = array();

$usesections = course_format_uses_sections($course->format);
if ($usesections) {
    // Section name.
    $table->head[] = get_string('sectionname', 'format_'.$course->format);
    $table->align[] = 'center';
}

// Activity name.
$table->head[] = get_string('name');
$table->align[] = 'left';

// Content type.
$table->head[] = 'Content Type';
$table->align[] = 'left';

// Add data rows.
foreach ($h5ps as $h5p) {
    $row = [];

    if ($usesections) {
        // Section name.
        $row[] = get_section_name($course, $h5p->section);
    }

    // Activity name.
    $attrs = ($h5p->visible ? '' : ' class="dimmed"');
    $h5p->name = format_string($h5p->name);
    $row[] = "<a href=\"view.php?id={$h5p->coursemodule}\"{$attrs}>{$h5p->name}</a>";

    // Activity type.
    $row[] = $h5p->librarytitle;

    $table->data[] = $row;
}

echo html_writer::table($table);

echo $OUTPUT->footer();
