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

/** LearnerScript
 * A Moodle block for creating LearnerScript Reports
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
require_once("../../config.php");

$id = required_param('id', PARAM_INT);
$comp = required_param('comp', PARAM_ALPHA);
$courseid = optional_param('courseid', null, PARAM_INT);
if ($comp != 'permissions') {
    print_error(get_string('nocomponent', 'block_learnerscript'));
}
if (!$report = $DB->get_record('block_learnerscript', array('id' => $id))) {
    print_error(get_string('noreportexists', 'block_learnerscript'));
}

// Ignore report's courseid, If we are running this report on a specific courseid
// (For permission checks).
if (empty($courseid)) {
    $courseid = $report->courseid;
}

if (!$course = $DB->get_record("course", array("id" => $courseid))) {
    print_error(get_string('nocourseid', 'block_learnerscript'));
}

// Force user login in course (SITE or Course).
if ($course->id == SITEID) {
    require_login();
    $context = context_system::instance();
} else {
    require_login($course->id);
    $context = context_course::instance($course->id);
}

$learnerscript = get_config('block_learnerscript', 'ls_serialkey');
if (empty($learnerscript)) {
    throw new moodle_exception(get_string('licencekeyrequired', 'block_learnerscript'));
    exit();
}
$lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');
if (!$lsreportconfigstatus) {
    redirect(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/lsconfig.php?import=1'));
    exit;
}

$PAGE->set_url('/blocks/learnerscript/editcomp.php', array('id' => $id, 'comp' => $comp));
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');

$PAGE->requires->data_for_js("M.cfg.accessls", $learnerscript , true);
// $PAGE->requires->js(new moodle_url('https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'));
$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->js('/blocks/learnerscript/js/learnerscript.js');

if (!has_capability('block/learnerscript:managereports', $context) && !has_capability('block/learnerscript:manageownreports', $context))
    print_error(get_string('badpermissions', 'block_learnerscript'));

if (!has_capability('block/learnerscript:managereports', $context) && $report->ownerid != $USER->id)
    print_error(get_string('badpermissions', 'block_learnerscript'));

require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');

$reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;

$properties = new stdClass();
$properties->courseid = $courseid;
$properties->start = 0;
$properties->length = 1;
$properties->search = '';
$properties->filters = array();

$reportclass = new $reportclassname($report->id, $properties);

if (!in_array($comp, $reportclass->components)){
    print_error(get_string('badcomponent','block_learnerscript'));
}

$elements = (new block_learnerscript\local\ls)->cr_unserialize($report->components);

$elements = isset($elements[$comp]['elements']) ? $elements[$comp]['elements'] : array();

require_once($CFG->dirroot . '/blocks/learnerscript/components/' . $comp . '/component.class.php');
$componentclassname = 'component_' . $comp;
$compclass = new $componentclassname($report->id);

if ($compclass->form) {
    require_once($CFG->dirroot . '/blocks/learnerscript/components/' . $comp . '/form.php');
    $classname = $comp . '_form';
    $editform = new $classname('editcomp.php?id=' . $id . '&comp=' . $comp, compact('compclass', 'comp', 'id', 'report', 'reportclass', 'elements'));

    if ($editform->is_cancelled()) {
        redirect($CFG->wwwroot . '/blocks/learnerscript/viewreport.php?id=' . $id);
    } else if ($data = $editform->get_data()) {
        $compclass->form_process_data($editform);
        redirect($PAGE->url);
    }

    $compclass->form_set_data($editform);
}

if ($compclass->plugins) {
    $currentplugins = array();
    if ($elements) {
        foreach ($elements as $e) {
            $currentplugins[] = $e['pluginname'];
        }
    }
    $plugins = get_list_of_plugins('blocks/learnerscript/components/' . $comp);
    $optionsplugins = array();

    foreach ($plugins as $p) {
        require_once($CFG->dirroot . '/blocks/learnerscript/components/' . $comp . '/' . $p . '/plugin.class.php');
        $pluginclassname = 'block_learnerscript\lsreports\plugin_' . $p;
        $pluginclass = new $pluginclassname($report);
        if (in_array($report->type, $pluginclass->reporttypes)) {
            if ($pluginclass->unique && in_array($p, $currentplugins))
                continue;
            $optionsplugins[$p] = get_string($p, 'block_learnerscript');
        }
    }
    asort($optionsplugins);
}
$managereporturl = new moodle_url($CFG->wwwroot . '/blocks/learnerscript/managereport.php');
$PAGE->navbar->add(get_string('managereports', 'block_learnerscript'), $managereporturl);
$reporturl = new moodle_url($CFG->wwwroot . '/blocks/learnerscript/viewreport.php', array('id' => $report->id));
$PAGE->navbar->add($report->name, $reporturl);
$PAGE->navbar->add(get_string($comp, 'block_learnerscript'));

$title = format_string($report->name); //.' '.get_string($comp,'block_learnerscript');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_cacheable(true);

echo $OUTPUT->header();
echo "<script src='https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'></script>";
echo html_writer::start_tag('div',array('id' => 'licenceresult', 'class' => 'lsacccess'));

$renderer = $PAGE->get_renderer('block_learnerscript');
if (has_capability('block/learnerscript:managereports', $context) ||
    (has_capability('block/learnerscript:manageownreports', $context)) &&
        $report->ownerid == $USER->id) {
    // $plots = (new block_learnerscript\local\ls)->get_components_data($report->id, 'plot');
    $plots = false;
    $calcbutton = false;
    $plotoptions = new \block_learnerscript\output\plotoption($plots, $report->id, $calcbutton,
        'permissions');
    echo $renderer->render($plotoptions);
}
if ($elements) {
    echo '<br/>'; /*added break to give gap between tabs and content*/
    $table = new html_table();
    $table->head = array(get_string('name'), get_string('summary'), get_string('edit'));
    $i = 0;
    foreach ($elements as $e) {
        if (!empty($e)) {
            require_once($CFG->dirroot . '/blocks/learnerscript/components/' . $comp . '/' . $e['pluginname'] . '/plugin.class.php');
            $pluginclassname = 'block_learnerscript\lsreports\plugin_' . $e['pluginname'];
            $pluginclass = new $pluginclassname($report);
            $editcell = '';
            if ($pluginclass->form) {
                $editcell .= '<a href="editplugin.php?id=' . $id . '&comp=' . $comp . '&pname=' . $e['pluginname'] . '&cid=' . $e['id'] . '"><img src="' . $OUTPUT->image_url('/t/edit') . '" class="iconsmall" title = "Edit"></a>';
            }

            $editcell .= '<a href="editplugin.php?id=' . $id . '&comp=' . $comp . '&pname=' . $e['pluginname'] . '&cid=' . $e['id'] . '&delete=1&amp;sesskey=' . sesskey() . '"><img src="' . $OUTPUT->image_url('/t/delete') . '" class="iconsmall" title = "Delete"></a>';

            if ($compclass->ordering && $i != 0 && count($elements) > 1)
                $editcell .= '<a href="editplugin.php?id=' . $id . '&comp=' . $comp . '&pname=' . $e['pluginname'] . '&cid=' . $e['id'] . '&moveup=1&amp;sesskey=' . sesskey() . '"><img src="' . $OUTPUT->image_url('/t/up') . '" class="iconsmall" title = "Up"></a>';
            if ($compclass->ordering && $i != count($elements) - 1)
                $editcell .= '<a href="editplugin.php?id=' . $id . '&comp=' . $comp . '&pname=' . $e['pluginname'] . '&cid=' . $e['id'] . '&movedown=1&amp;sesskey=' . sesskey() . '"><img src="' . $OUTPUT->image_url('/t/down') . '" class="iconsmall" title = "Down"></a>';
            if($comp == 'plot') {
                $table->data[] = array($e['formdata']->chartname, $e['summary'], $editcell);
            } else {
                $table->data[] = array($e['pluginfullname'], $e['summary'], $editcell);
            }
            $i++;
        }
    }

    echo '<div class="overflow_x">'.html_writer::table($table).'</div>';
} else {
    if ($compclass->plugins)
        echo $OUTPUT->heading(get_string('no' . $comp . 'yet', 'block_learnerscript'));
}

if ($compclass->plugins) {
    echo '<div class="boxaligncenter">';
    echo '<p class="centerpara">';
    print_string('add');
    echo ': &nbsp;';
    //choose_from_menu($optionsplugins,'plugin','',get_string('choose'),"location.href = 'editplugin.php?id=".$id."&comp=".$comp."&pname='+document.getElementById('menuplugin').value");
    $attributes = array('id' => 'menuplugin');
    /*
     * Notice: line charts not available in NVD3 graphs.
     */
    if (get_config('block_learnerscript', 'reportchartui') === 'd3') {
        unset($optionsplugins['line']);
    }
    echo html_writer::select($optionsplugins, 'plugin', '', array('' => get_string('choose')), $attributes);
    $OUTPUT->add_action_handler(new component_action('change', 'menuplugin', array('url' => "editplugin.php?id=" . $id . "&comp=" . $comp . "&pname=")), 'menuplugin');
    echo '</p>';
    echo '</div>';
}

if ($compclass->form) {
    $editform->display();
}

if ($compclass->help) {
    echo '<div class="boxaligncenter">';
    echo '<p class="centerpara">';
    echo $OUTPUT->help_icon('comp_' . $comp, 'block_learnerscript', get_string('comp_' . $comp, 'block_learnerscript'));
    //helpbutton('comp_'.$comp, get_string('componenthelp','block_learnerscript'),'block_learnerscript', true, true);
    echo '</p>';
    echo '</div>';
}
echo html_writer::end_tag('div');
echo $OUTPUT->footer();
