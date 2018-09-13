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
 * ASES
 *
 * @author     Jeison Cardona Gomez.
 * @package    block_ases
 * @copyright  2018 Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once __DIR__ . '/../../../config.php';
require_once $CFG->libdir . '/adminlib.php';

require_once '../managers/monitor_assignments/monitor_assignments_lib.php';
require_once '../managers/instance_management/instance_lib.php';

global $PAGE;
global $USER;

include "../classes/output/monitor_assignments_page.php";
include "../classes/output/renderer.php";

// Set up the page.
$title = "Asignación de monitor";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

// Set up the page.
if (!consult_instance($blockid)) {
    header("Location: instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock = context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/monitor_assignments.php", array('courseid' => $courseid, 'instanceid' => $blockid));

$record = new stdClass;

$record->instance_id = $blockid;
$record->professionals = array_values( monitor_assignments_get_professionals_by_instance( $blockid ) );
$record->practitioners = array_values( monitor_assignments_get_practicing_by_instance( $blockid ) );
$record->monitors = array_values( monitor_assignments_get_monitors_by_instance( $blockid ) );
$record->monitors_programs = array_values( monitor_assignments_get_monitors_programs( $blockid ) );
$record->monitors_faculty = array_values( monitor_assignments_get_monitors_faculty( $blockid ) );
$record->students = array_values( monitor_assignments_get_students_by_instance( $blockid ) );
$record->students_programs = array_values( monitor_assignments_get_students_programs( $blockid ) );
$record->students_faculty = array_values( monitor_assignments_get_students_faculty( $blockid ) );
$record->monitors_students_relationship = json_encode( array_values( monitor_assignments_get_monitors_students_relationship_by_instance( $blockid ) ) );
$record->professional_practicant_relationship = json_encode( array_values( monitor_assignments_get_profesional_practicant_relationship_by_instance( $blockid ) ) );
$record->practicant_monitor_relationship = json_encode( array_values( monitor_assignments_get_practicant_monitor_relationship_by_instance( $blockid ) ) );

$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Asignaciones',$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

$PAGE->set_context($contextcourse);
$PAGE->set_context($contextblock);
$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->requires->css('/blocks/ases/style/jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert2.css', true);
$PAGE->requires->css('/blocks/ases/style/c3.css', true);
$PAGE->requires->css('/blocks/ases/style/switch.css', true);
$PAGE->requires->css('/blocks/ases/style/monitor_assignments.css', true);

$PAGE->requires->js_call_amd('block_ases/monitor_assignments', 'init');

$output = $PAGE->get_renderer('block_ases');


echo $output->header();
$monitor_assignments_page = new \block_ases\output\monitor_assignments_page($record);
echo $output->render($monitor_assignments_page);
echo $output->footer();
