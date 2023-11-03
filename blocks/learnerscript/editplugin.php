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
 * A Moodle block for creating LearnerScript
 * @package blocks
 * @author: eAbyas Info Solutions
 * @date: 2017
 */
require_once("../../config.php");

use block_learnerscript\local\ls;

$id = required_param('id', PARAM_INT);
$comp = required_param('comp', PARAM_ALPHA);
$cid = optional_param('cid', '', PARAM_ALPHANUM);
$pname = optional_param('pname', '', PARAM_ALPHA);
$moveup = optional_param('moveup', 0, PARAM_INT);
$movedown = optional_param('movedown', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

if (!$pname) {
    redirect(new moodle_url('/blocks/learnerscript/editcomp.php', array('id' => $id, 'comp' => $comp)));
    exit;
}

$learnerscript = get_config('block_learnerscript', 'ls_serialkey');
if (empty($learnerscript)) {
    throw new moodle_exception(get_string('licencekeyrequired','block_learnerscript'));
    exit();
}
$lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');
if (!$lsreportconfigstatus) {
    redirect(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/lsconfig.php?import=1'));
    exit;
}

if (!$report = $DB->get_record('block_learnerscript', array('id' => $id))) {
    print_error(get_string('noreportexists','block_learnerscript'));
}

if (!$course = $DB->get_record("course", array("id" => $report->courseid))) {
    print_error(get_string('nocourseid','block_learnerscript'));
}

// Force user login in course (SITE or Course)
if ($course->id == SITEID) {
    require_login();
    $context = context_system::instance();
} else {
    require_login($course->id);
    $context = context_course::instance($course->id);
}

if (!has_capability('block/learnerscript:managereports', $context) && !has_capability('block/learnerscript:manageownreports', $context)) {
    print_error(get_string('badpermissions','block_learnerscript'));
}

if (!has_capability('block/learnerscript:managereports', $context) && $report->ownerid != $USER->id) {
    print_error(get_string('badpermissions','block_learnerscript'));
}

require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');

$reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;

$properties = new stdClass();
$properties->courseid = SITEID;
$properties->start = 0;
$properties->length = 1;
$properties->search = '';
$properties->filters = array();

$reportclass = new $reportclassname($report->id, $properties);

if (!in_array($comp, $reportclass->components))
    print_error(get_string('badcomponent', 'block_learnerscript'));

$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');

$PAGE->requires->data_for_js("M.cfg.accessls", $learnerscript , true);

// $PAGE->requires->js(new moodle_url('https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'));
$PAGE->requires->jquery_plugin('ui-css');

$PAGE->set_url('/blocks/learnerscript/editplugin.php', array('id' => $id, 'comp' => $comp, 'cid' => $cid, 'pname' => $pname));

$cdata = null;
$plugin = '';
if (!$cid) {
    if (filetype($CFG->dirroot . '/blocks/learnerscript/components/' . $comp . '/' . $pname) == 'dir') {
        $plugin = $pname;
    }
} else {
    $components = (new ls)->cr_unserialize($report->components);
    $elements = isset($components[$comp]['elements']) ? $components[$comp]['elements'] : array();

    if ($elements)
        foreach ($elements as $e) {
            if ($e['id'] == $cid) {
                $cdata = $e;
                $plugin = $e['pluginname'];
                break;
            }
        }

    if (($moveup || $movedown || $delete) && confirm_sesskey()) {
        foreach ($elements as $index => $e) {
            if ($e['id'] == $cid) {
                if ($delete) {
                    unset($elements[$index]);
                    break;
                }
                $newindex = ($moveup) ? $index - 1 : $index + 1;
                $tmp = $elements[$newindex];
                $elements[$newindex] = $e;
                $elements[$index] = $tmp;
                break;
            }
        }
        $components[$comp]['elements'] = $elements;
        $report->components = (new ls)->cr_serialize($components);
        $DB->update_record('block_learnerscript', $report);
        redirect(new moodle_url('/blocks/learnerscript/editcomp.php', array('id' => $id, 'comp' => $comp)));
        exit;
    }
}

if (!$plugin || $plugin != $pname)
    print_error(get_string('noplugin','block_learnerscript'));

require_once($CFG->dirroot . '/blocks/learnerscript/components/' . $comp . '/' . $pname . '/plugin.class.php');
$pluginclassname = 'block_learnerscript\lsreports\plugin_' . $pname;
$pluginclass = new $pluginclassname($report);

if (isset($pluginclass->form) && $pluginclass->form) {

    require_once($CFG->dirroot . '/blocks/learnerscript/components/' . $comp . '/component.class.php');
    $componentclassname = 'component_' . $comp;
    $compclass = new $componentclassname($report->id);

    require_once($CFG->dirroot . '/blocks/learnerscript/components/' . $comp . '/' . $pname . '/form.php');
    $classname = $pname . '_form';

    $formurlparams = array('id' => $id, 'comp' => $comp, 'pname' => $pname);
    if ($cid) {
        $formurlparams['cid'] = $cid;
    }
    $formurl = new moodle_url('/blocks/learnerscript/editplugin.php', $formurlparams);
    $editform = new $classname($formurl, compact('comp', 'cid', 'id', 'pluginclass', 'compclass', 'report', 'reportclass'));

    if (!empty($cdata)) {
        $editform->set_data($cdata['formdata']);
    }

    if ($editform->is_cancelled()) {
        if (!empty($report))
            redirect($CFG->wwwroot . '/blocks/learnerscript/editcomp.php?id=' . $report->id . '&comp=' .$comp);
        else
            redirect($CFG->wwwroot . '/blocks/learnerscript/editreport.php');
    }
    else if ($data = $editform->get_data()) {
        $allelements = (new ls)->cr_unserialize($report->components);
        if($pname == 'roleincourse'){
            $data->roleid = data_submitted()->roleid;
            $data->contextlevel = data_submitted()->contextlevel;
        }
        if (!empty($cdata)) {
            // cr_serialize() will add slashes
            $cdata['formdata'] = $data;
            $cdata['summary'] = $pluginclass->summary($data);
            $elements = (new ls)->cr_unserialize($report->components);
            $elements = isset($elements[$comp]['elements']) ? $elements[$comp]['elements'] : array();
            if ($elements)
                foreach ($elements as $key => $e) {
                    if ($e['id'] == $cid) {
                        $elements[$key] = $cdata;
                        break;
                    }
                }
            $allelements[$comp]['elements'] = $elements;

            $report->components = (new ls)->cr_serialize($allelements);
            if (!$DB->update_record('block_learnerscript', $report)) {
                print_error(get_string('errorsaving','block_learnerscript'));
            } else {
                redirect(new moodle_url('/blocks/learnerscript/editcomp.php', array('id' => $id, 'comp' => $comp)));
                exit;
            }
        } else {
           
            $uniqueid = random_string(15);
            while (strpos($report->components, $uniqueid) !== false) {
                $uniqueid = random_string(15);
            }
            if(isset($allelements['permissions']['elements'])){
                foreach ($allelements['permissions']['elements'] as $existpermission) {
                    if(!array_diff_assoc((array)$existpermission['formdata'], (array)$data)){
                        redirect(new moodle_url('/blocks/learnerscript/editcomp.php', array('id' => $id, 'comp' => $comp)));
                        exit;
                    }
                }
            }
            $cdata = array('id' => $uniqueid, 'formdata' => $data, 'pluginname' => $pname, 'pluginfullname' => $pluginclass->fullname, 'summary' => $pluginclass->summary($data));
            

            $allelements[$comp]['elements'][] = $cdata;
            $report->components = (new ls)->cr_serialize($allelements, false);
            if (!$DB->update_record('block_learnerscript', $report)) {
                print_error(get_string('errorsaving','block_learnerscript'));
            } else {
                redirect(new moodle_url('/blocks/learnerscript/editcomp.php', array('id' => $id, 'comp' => $comp)));
                exit;
            }
        }
    }
} else {
    $allelements = (new ls)->cr_unserialize($report->components);

    $uniqueid = random_string(15);
    while (strpos($report->components, $uniqueid) !== false) {
        $uniqueid = random_string(15);
    }

    $cdata = array('id' => $uniqueid, 'formdata' => new stdclass, 'pluginname' => $pname, 'pluginfullname' => $pluginclass->fullname, 'summary' => $pluginclass->summary(new stdclass));

    $allelements[$comp]['elements'][] = $cdata;
    $report->components = (new ls)->cr_serialize($allelements);
    if (!$DB->update_record('block_learnerscript', $report)) {
        print_error(get_string('errorsaving','block_learnerscript'));
    } else {
        redirect(new moodle_url('/blocks/learnerscript/editcomp.php', array('id' => $id, 'comp' => $comp)));
        exit;
    }
}

$title = format_string($report->name) . ' ' . get_string($comp, 'block_learnerscript');

$PAGE->navbar->add(get_string('managereports', 'block_learnerscript'), $CFG->wwwroot . '/blocks/learnerscript/managereport.php');
$PAGE->navbar->add($title, $CFG->wwwroot . '/blocks/learnerscript/editcomp.php?id=' . $id . '&amp;comp=' . $comp);
$PAGE->navbar->add(get_string($pname, 'block_learnerscript'));

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_cacheable(true);

echo $OUTPUT->header();
echo "<script src='https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'></script>";
echo html_writer::start_tag('div',array('id' => 'licenceresult', 'class' => 'lsacccess'));
$renderer = $PAGE->get_renderer('block_learnerscript');

if (has_capability('block/learnerscript:managereports', $context) ||
    (has_capability('block/learnerscript:manageownreports', $context)) && $report->ownerid == $USER->id) {
    $plots = (new block_learnerscript\local\ls)->get_components_data($report->id, 'plot');
    $calcbutton = false;
    $plotoptions = new \block_learnerscript\output\plotoption($plots, $report->id, $calcbutton,
        'permissions');
    echo $renderer->render($plotoptions);
}

if ($pluginclass->form)
    $editform->display();

echo html_writer::end_tag('div');
echo $OUTPUT->footer();