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
 * LearnerScript Reports
 * A Moodle block for configure LearnerScript Reports
 * @package block_learnerscript
 * @author: Arun Kumar Mukka
 * @date: 2018
 *
 */
require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');
use block_learnerscript\local\ls as ls;
use block_learnerscript_licence_setting;

$import = optional_param('import', 0, PARAM_INT);
$reset = optional_param('reset', 0, PARAM_INT);

require_login();

$context = context_system::instance();

$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->css('/blocks/learnerscript/css/slideshow.css');

$learnerscript = get_config('block_learnerscript', 'ls_serialkey');
if (empty($learnerscript)) {
    throw new moodle_exception('reqlicencekey', 'block_learnerscript');
    exit();
}

$lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');
$PAGE->set_url($CFG->wwwroot . '/blocks/learnerscript/import.php');
$PAGE->set_context($context);
$PAGE->set_pagelayout('maintenance');
$PAGE->set_title('Import Reports');
$PAGE->set_heading('LearnerScript Reports Configuration');
die();
if ($reset) {
    $DB->delete_records('logstore_standard_log',
                            array('objecttable' => 'block_learnerscript'));

    $DB->delete_records('block_learnerscript');
    $DB->delete_records('block_ls_schedule');
    $blockinstancessql = "SELECT id
                            FROM {block_instances}
                           WHERE (pagetypepattern LIKE :pagetypepattern
                                    OR blockname = :blockname)";
    $blockinstances = $DB->get_fieldset_sql($blockinstancessql, ['pagetypepattern' => '%blocks-reportdashboard%', 'blockname' => 'coursels']);

    if (!empty($blockinstances)) {
        blocks_delete_instances($blockinstances);
    }
    set_config('lsreportconfigstatus', 0, 'block_learnerscript');
    set_config('lsreportconfigimport', 0, 'block_learnerscript');

    $usertours = $CFG->dirroot . '/blocks/learnerscript/usertours/';
    $usertoursjson = glob($usertours . '*.json');

    foreach ($usertoursjson as $usertour) {
        $data = file_get_contents($usertour);
        $tourconfig = json_decode($data);
        $DB->delete_records('tool_usertours_tours', array('name' => $tourconfig->name));
    }

    redirect($CFG->wwwroot . '/blocks/learnerscript/import.php?import=1');
    exit;
}

$lsreportconfigimport = get_config('block_learnerscript', 'lsreportconfigimport');
if ($lsreportconfigimport) {
    throw new moodle_exception("Already Import Started");
    exit();
}

$path = $CFG->dirroot . '/blocks/learnerscript/backup/';
$learnerscriptreports = glob($path . '*.xml');
$lsreportscount = $DB->count_records('block_learnerscript');
$lsimportlogssql = "SELECT other
                      FROM {logstore_standard_log}
                     WHERE action = :action AND target = :target
                            AND objecttable = :objecttable AND other <> :other";
$lsimportlogs = $DB->get_fieldset_sql($lsimportlogssql, array('action' => 'import',
    'target' => 'report', 'objecttable' => 'block_learnerscript', 'other' => 'N;'));
$lastreport = 0;
foreach ($lsimportlogs as $lsimportlog) {
    $lslog = unserialize($lsimportlog);
    if ($lslog['status'] == false) {
        $errorreportsposition[$lslog['position']] = $lslog['position'];
    }

    if ($lslog['status'] == true) {
        $lastreportposition = $lslog['position'];
    }
}

$importstatus = false;
if (empty($lsimportlogs) || $lsreportscount < 1) {
    $total = count($learnerscriptreports);

    $current = 1;
    $percentwidth = $current / $total * 100;
    $importstatus = true;
    $errorreportsposition = array();
    $lastreportposition = 0;
} else {
    $total = 0;
    foreach ($learnerscriptreports as $position => $learnerscriptreport) {
        if ((!empty($errorreportsposition) && in_array($position, $errorreportsposition)) || $position >= $lastreportposition) {
            $total++;
        }
    }
    if (empty($errorreportsposition)) {
        $current = $lastreportposition + 1;
        $errorreportsposition = array();
    } else {
        $occuredpositions = array_merge($errorreportsposition, array($lastreportposition));
        $current = min($occuredpositions);
    }
    if ($total > 0) {
        $importstatus = true;
    }
}
$errorreportspositiondata = serialize($errorreportsposition);

echo $OUTPUT->header();
$slideshowimagespath = '/blocks/learnerscript/images/slideshow/';
$slideshowimages = scandir($CFG->dirroot . $slideshowimagespath, SCANDIR_SORT_ASCENDING);
$slideshowcount = 0;
echo "<div><center class='lsoverviewimageslider'>";
if (!empty($slideshowimages)) {
    foreach ($slideshowimages as $slideshowimage) {
        if (exif_imagetype($CFG->wwwroot . $slideshowimagespath . $slideshowimage)) {
            $slideshowcount++;
            echo '<div class="mySlides"><div style="width:500px; height:350px;">
            <img class="lsoverviewimages" style="width:100%;height:100%"
            src="' . $CFG->wwwroot . $slideshowimagespath . $slideshowimage . '"></div></div>';
        }
    }
}

$reportdashboardblockexists = $PAGE->blocks->is_known_block_type('reportdashboard', false);
if ($reportdashboardblockexists) {
    $redirecturl = $CFG->wwwroot . '/blocks/reportdashboard/dashboard.php';
} else {
    $redirecturl = $CFG->wwwroot . '/blocks/learnerscript/managereport.php';
}

if ($importstatus && !$lsreportconfigstatus) {
    $pluginsettings = new block_learnerscript_licence_setting('block_learnerscript/lsreportconfigimport',
                'lsreportconfigimport', get_string('lsreportconfigimport', 'block_learnerscript'), '', PARAM_INT, 2);
    $pluginsettings->config_write('lsreportconfigimport', 1);

    echo '<div id="progressbar"></div>';
    echo '<div><center style="display:none;" id="reportdashboardnav">
                <a href="' . $redirecturl . '" >
                    <button>Continue</button>
                </a>
        </center></div>';
    $usertours = $CFG->dirroot . '/blocks/learnerscript/usertours/';
    $totalusertours = count(glob($usertours . '*.json'));
    $usertoursjson = glob($usertours . '*.json');
    $pluginmanager = new \tool_usertours\manager();
    for ($i = 0; $i < $totalusertours; $i++) {
        $importurl = $usertoursjson[$i];
        if (file_exists($usertoursjson[$i])
                && pathinfo($usertoursjson[$i], PATHINFO_EXTENSION) == 'json') {
            $data = file_get_contents($importurl);
            $tourconfig = json_decode($data);
            $tourexists = $DB->record_exists('tool_usertours_tours', array('name' => $tourconfig->name));
            if (!$tourexists) {
                $tour = $pluginmanager->import_tour_from_json($data);
            }
        }
    }
} else {
    echo "<div class='alert alert-info'>LearnerScript Reports already Configured, <a href='" . $redirecturl . "' >click here
                </a> to continue.</div>";
}
echo "</center></div>";

if ($importstatus && !$lsreportconfigstatus) {
    $PAGE->requires->js_call_amd('block_learnerscript/lsreportconfig', 'init',
                                    array(array('total' => $total,
                                                'current' => $current,
                                                'errorreportspositiondata' => $errorreportspositiondata,
                                                'lastreportposition' => $lastreportposition
                                            ),
                                    ));

}
echo $OUTPUT->footer();