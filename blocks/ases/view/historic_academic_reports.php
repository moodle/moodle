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
 * Estrategia ASES
 *
 * @author     Camilo José Cruz Rivera
 * @package    block_ases
 * @copyright  2018 Camilo José Cruz Rivera <cruz.camilo@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once __DIR__ . '/../../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once '../managers/instance_management/instance_lib.php';
require_once '../managers/historic_academic_reports/historic_academic_reports_lib.php';
include "../classes/output/historic_academic_reports_page.php";
include "../classes/output/renderer.php";
require_once '../managers/permissions_management/permissions_lib.php';
require_once '../managers/validate_profile_action.php';
require_once '../managers/menu_options.php';

global $PAGE;

// Variables for page setup
$title = "Reportes Historicos Academicos";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

//Instance is consulted for its registration
if (!consult_instance($blockid)) {
    header("Location: /blocks/ases/view/instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock = context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/historic_academic_reports.php", array('courseid' => $courseid, 'instanceid' => $blockid));

//Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create($title, $url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/simple-sidebar.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.tableTools.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);

//Requires AMD modules
$PAGE->requires->js_call_amd('block_ases/historic_academic_reports', 'init');

$tableStudent = get_datatable_array_Students($blockid);
$paramsStudents = new stdClass();
$paramsStudents->table = $tableStudent;

// print_r($paramsStudents);

$tableTotals = get_datatable_array_totals($blockid);
$paramsTotals = new stdClass();
$paramsTotals->table = $tableTotals;

// print_r($paramsTotals);


$PAGE->requires->js_call_amd('block_ases/historic_academic_reports', 'load_table_students', $paramsStudents);
$PAGE->requires->js_call_amd('block_ases/historic_academic_reports', 'load_total_table', $paramsTotals);

//Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);

//Evaluates if user role has permissions assigned on this view
$actions = authenticate_user_view($USER->id, $blockid);
$data = $actions;
$data->menu = $menu_option;
$output = $PAGE->get_renderer('block_ases');

echo $output->header();
$historic_academic_reports_page = new \block_ases\output\historic_academic_reports_page($data);
echo $output->render($historic_academic_reports_page);
echo $output->footer();
