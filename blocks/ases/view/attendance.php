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
 * General Reports
 *
 * @author     Iader E. García Gómez
 * @package    block_ases
 * @copyright  2016 Iader E. García <iadergg@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/query.php');
include('../lib.php');
// include("../classes/output/index.php");
include("../classes/output/renderer.php");

global $PAGE;

// Variables for page setup
$title = get_string('pluginname', 'block_ases');
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

//Instance is consulted for its registration
if(!consultInstance($blockid)){
    header("Location: instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/attendance.php",array('courseid' => $courseid, 'instanceid' => $blockid));

//Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create($title,$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

$PAGE->set_url($url);
$PAGE->set_title($title);

// $PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
// $PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
// $PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
// $PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
// $PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.tableTools.css', true);
// $PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/NewCSSExport/buttons.dataTables.min.css', true);

// $PAGE->requires->js('/blocks/ases/js/jquery-2.2.4.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/jquery.dataTables.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/jquery.dataTables.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.jqueryui.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.bootstrap.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.bootstrap.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.tableTools.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.tableTools.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/buttons.flash.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/buttons.html5.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/buttons.print.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/dataTables.buttons.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/jszip.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/pdfmake.min.js', true);
// $PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/NewJSExport/vfs_fonts.js', true);
// $PAGE->requires->js('/blocks/ases/js/attendance_table.js', true);
// $PAGE->requires->js('/blocks/ases/js/main.js', true);
// $PAGE->requires->js('/blocks/ases/js/checkrole.js', true);
// $PAGE->requires->js('/blocks/ases/js/bootstrap.js', true);
// $PAGE->requires->js('/blocks/ases/js/bootstrap.min.js', true);

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
//echo $output->standard_head_html(); 
$attendance_page = new \block_ases\output\attendance_page('Some text');
echo $output->render($attendance_page);
echo $output->footer();