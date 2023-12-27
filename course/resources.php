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
 * List of all resource type modules in course
 *
 * @package   moodlecore
 * @copyright 2009 Petr Skoda (http://skodak.org)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once("$CFG->libdir/resourcelib.php");

$id = required_param('id', PARAM_INT); // course id

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
$PAGE->set_pagelayout('incourse');
require_course_login($course, true);

// get list of all resource-like modules
$allmodules = $DB->get_records('modules', array('visible'=>1));
$availableresources = array();
foreach ($allmodules as $key=>$module) {
    $modname = $module->name;
    $libfile = "$CFG->dirroot/mod/$modname/lib.php";
    if (!file_exists($libfile)) {
        continue;
    }
    $archetype = plugin_supports('mod', $modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
    if ($archetype != MOD_ARCHETYPE_RESOURCE) {
        continue;
    }

    $availableresources[] = $modname;
}

// Triger view event.
$event = \core\event\course_resources_list_viewed::create(array('context' => context_course::instance($course->id)));
$event->add_record_snapshot('course', $course);
$event->trigger();

$strresources    = get_string('resources');
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_url('/course/resources.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$strresources);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($strresources);
echo $OUTPUT->header();

$modinfo = get_fast_modinfo($course);
$usesections = course_format_uses_sections($course->format);
$cms = array();
$resources = array();
foreach ($modinfo->cms as $cm) {
    if (!in_array($cm->modname, $availableresources)) {
        continue;
    }
    // Exclude activities that aren't visible or have no view link (e.g. label). Account for folder being displayed inline.
    if (!$cm->uservisible || (!$cm->has_view() && strcmp($cm->modname, 'folder') !== 0)) {
        continue;
    }
    $cms[$cm->id] = $cm;
    $resources[$cm->modname][] = $cm->instance;
}

// preload instances
foreach ($resources as $modname=>$instances) {
    $additionalfields = '';
    if (plugin_supports('mod', $modname, FEATURE_MOD_INTRO)) {
        $additionalfields = ',intro,introformat';
    }
    $resources[$modname] = $DB->get_records_list($modname, 'id', $instances, 'id', 'id,name'.$additionalfields);
}

if (!$cms) {
    notice(get_string('thereareno', 'moodle', $strresources), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$currentsection = '';
foreach ($cms as $cm) {
    if (!isset($resources[$cm->modname][$cm->instance])) {
        continue;
    }
    $resource = $resources[$cm->modname][$cm->instance];
    $printsection = '';
    if ($usesections) {
        if ($cm->sectionnum !== $currentsection) {
            if ($cm->sectionnum) {
                $printsection = get_section_name($course, $cm->sectionnum);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $cm->sectionnum;
        }
    }

    $extra = empty($cm->extra) ? '' : $cm->extra;
    $icon = '<img src="'.$cm->get_icon_url().'" class="activityicon" alt="'.$cm->get_module_type_name().'" /> ';

    if (isset($resource->intro) && isset($resource->introformat)) {
        $intro = format_module_intro($cm->modname, $resource, $cm->id);
    } else {
        $intro = '';
    }

    $class = $cm->visible ? '' : 'class="dimmed"'; // hidden modules are dimmed
    $url = $cm->url ?: new moodle_url("/mod/{$cm->modname}/view.php", ['id' => $cm->id]);

    $table->data[] = array (
        $printsection,
        "<a $class $extra href=\"" . $url ."\">" . $icon . $cm->get_formatted_name() . "</a>",
        $intro);
}

echo html_writer::table($table);

echo $OUTPUT->footer();
