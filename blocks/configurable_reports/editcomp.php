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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

require_once($CFG->dirroot . '/blocks/configurable_reports/locallib.php');
require_once($CFG->dirroot . '/blocks/configurable_reports/report.class.php');
require_once($CFG->dirroot . '/blocks/configurable_reports/component.class.php');
require_once($CFG->dirroot . '/blocks/configurable_reports/plugin.class.php');

$id = required_param('id', PARAM_INT);
$comp = required_param('comp', PARAM_ALPHA);
$courseid = optional_param('courseid', null, PARAM_INT);

if (!$report = $DB->get_record('block_configurable_reports', ['id' => $id])) {
    throw new moodle_exception('reportdoesnotexists');
}

// Ignore report's courseid, If we are running this report on a specific courseid
// (For permission checks).
if (empty($courseid)) {
    $courseid = $report->courseid;
}

if (!$course = $DB->get_record("course", ['id' => $courseid])) {
    throw new moodle_exception("No such course id");
}

// Force user login in course (SITE or Course).
if ($course->id == SITEID) {
    require_login();
    $context = context_system::instance();
} else {
    require_login($course->id);
    $context = context_course::instance($course->id);
}

$PAGE->set_url('/blocks/configurable_reports/editreport.php', ['id' => $id, 'comp' => $comp]);
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');

$PAGE->requires->js('/blocks/configurable_reports/js/configurable_reports.js');

$hasreportscap = has_capability('block/configurable_reports:managereports', $context);
if (!$hasreportscap && !has_capability('block/configurable_reports:manageownreports', $context)) {
    throw new moodle_exception('badpermissions');
}

if (!$hasreportscap && $report->ownerid != $USER->id) {
    throw new moodle_exception('badpermissions');
}

if ($report->type === 'sql' && !block_configurable_reports_can_managesqlreports($context)) {
    throw new moodle_exception('nosqlpermissions');
}

require_once($CFG->dirroot . '/blocks/configurable_reports/reports/' . $report->type . '/report.class.php');

$reportclassname = 'report_' . $report->type;
$reportclass = new $reportclassname($report->id);

if (!in_array($comp, $reportclass->components)) {
    throw new moodle_exception('badcomponent');
}

$elements = cr_unserialize($report->components);
$elements = $elements[$comp]['elements'] ?? [];

require_once($CFG->dirroot . '/blocks/configurable_reports/components/' . $comp . '/component.class.php');
$componentclassname = 'component_' . $comp;
$compclass = new $componentclassname($report->id);

if ($compclass->form) {
    require_once($CFG->dirroot . '/blocks/configurable_reports/components/' . $comp . '/form.php');
    $classname = $comp . '_form';
    $editform = new $classname(
        'editcomp.php?id=' . $id . '&comp=' . $comp,
        compact('compclass', 'comp', 'id', 'report', 'reportclass', 'elements')
    );

    if ($editform->is_cancelled()) {
        redirect($CFG->wwwroot . '/blocks/configurable_reports/editcomp.php?id=' . $id . '&amp;comp=' . $comp);
    } else if ($data = $editform->get_data()) {
        $compclass->form_process_data($editform);
    }

    $compclass->form_set_data($editform);
}

if ($compclass->plugins) {
    $currentplugins = [];
    if ($elements) {
        foreach ($elements as $e) {
            $currentplugins[] = $e['pluginname'];
        }
    }
    $plugins = get_list_of_plugins('blocks/configurable_reports/components/' . $comp);
    $optionsplugins = [];
    foreach ($plugins as $p) {
        require_once($CFG->dirroot . '/blocks/configurable_reports/components/' . $comp . '/' . $p . '/plugin.class.php');
        $pluginclassname = 'plugin_' . $p;
        $pluginclass = new $pluginclassname($report);
        if (in_array($report->type, $pluginclass->reporttypes)) {
            if ($pluginclass->unique && in_array($p, $currentplugins)) {
                continue;
            }
            $optionsplugins[$p] = get_string($p, 'block_configurable_reports');
        }
    }
    asort($optionsplugins);
}

$managereporturl = new moodle_url('/blocks/configurable_reports/managereport.php', ['courseid' => $courseid]);
$PAGE->navbar->add(get_string('managereports', 'block_configurable_reports'), $managereporturl);

$title = format_string($report->name);
$PAGE->navbar->add($title);

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_cacheable(true);

echo $OUTPUT->header();

$currenttab = $comp;
require('tabs.php');

if ($elements) {
    $table = new stdclass;
    $table->head = [get_string('idnumber'), get_string('name'), get_string('summary'), get_string('edit')];
    $i = 0;

    foreach ($elements as $e) {

        if (empty($e)) {
            continue;
        }

        require_once($CFG->dirroot . '/blocks/configurable_reports/components/' . $comp . '/' . $e['pluginname'] .
            '/plugin.class.php');
        $pluginclassname = 'plugin_' . $e['pluginname'];
        $pluginclass = new $pluginclassname($report);

        $editcell = '';

        if ($pluginclass->form) {
            $editcell .= '<a href="editplugin.php?id=' . $id . '&comp=' . $comp . '&pname=' . $e['pluginname'] . '&cid=' .
                $e['id'] . '">' .
                $OUTPUT->pix_icon('t/edit', get_string('edit')) .
                '</a>';
        }

        $editcell .= '<a href="editplugin.php?id=' . $id . '&comp=' . $comp . '&pname=' . $e['pluginname'] .
            '&cid=' . $e['id'] . '&delete=1&amp;sesskey=' . sesskey() . '">' .
            $OUTPUT->pix_icon('t/delete', get_string('delete')) .
            '</a>';

        if ($compclass->ordering && $i != 0 && count($elements) > 1) {
            $editcell .= '<a href="editplugin.php?id=' . $id . '&comp=' . $comp . '&pname=' . $e['pluginname'] . '&cid=' .
                $e['id'] .
                '&moveup=1&amp;sesskey=' . sesskey() . '">' .
                $OUTPUT->pix_icon('t/up', get_string('moveup')) .
                '</a>';
        }
        if ($compclass->ordering && $i != count($elements) - 1) {
            $editcell .= '<a href="editplugin.php?id=' . $id . '&comp=' . $comp . '&pname=' . $e['pluginname'] . '&cid=' .
                $e['id'] .
                '&movedown=1&amp;sesskey=' . sesskey() . '">' .
                $OUTPUT->pix_icon('t/down', get_string('movedown')) .
                '</a>';
        }

        $table->data[] = ['c' . ($i + 1), $e['pluginfullname'], $e['summary'], $editcell];
        $i++;
    }
    cr_print_table($table);
} else if ($compclass->plugins) {
    echo $OUTPUT->heading(get_string('no' . $comp . 'yet', 'block_configurable_reports'));
}

if ($compclass->plugins) {
    echo '<div class="boxaligncenter">';
    echo '<p class="centerpara">';
    print_string('add');
    echo ': &nbsp;';

    $attributes = ['id' => 'menuplugin'];

    echo html_writer::select($optionsplugins, 'plugin', '', ['' => get_string('choose')], $attributes);
    $OUTPUT->add_action_handler(
        new component_action('change', 'menuplugin', ['url' => "editplugin.php?id=" . $id . "&comp=" . $comp . "&pname="]),
        'menuplugin'
    );
    echo '</p>';
    echo '</div>';
}

if ($compclass->form) {
    $editform->display();
}

if ($compclass->help) {
    echo '<div class="boxaligncenter">';
    echo '<p class="centerpara">';
    echo $OUTPUT->help_icon(
        'comp_' . $comp,
        'block_configurable_reports',
        get_string('comp_' . $comp, 'block_configurable_reports')
    );
    echo '</p>';
    echo '</div>';
}

echo $OUTPUT->footer();
