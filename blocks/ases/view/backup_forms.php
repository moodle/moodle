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
 *Backup reports
 *
 * @author     Juan Pablo Castro
 * @package    block_ases
 * @copyright  2018 Juan Pablo Castro <juan.castro.vasquez@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once ('../managers/validate_profile_action.php');
require_once ('../managers/menu_options.php');


global $PAGE;

include("../classes/output/backup_forms_page.php");
include("../classes/output/renderer.php");
include '../lib.php';

// Variables for setup the page.
$title = "Backup forms";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

// Menu items are created
$menu_option = create_menu_options($USER->id, $blockid, $courseid);


$url = new moodle_url("/blocks/ases/view/backup_forms.php", array('courseid' => $courseid, 'instanceid' => $blockid));

$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);

//Creates a class with information that'll be send to template
$data = new stdClass;
$actions = authenticate_user_view($USER->id, $blockid);
$data = $actions;
$data->menu = $menu_option;


$PAGE->requires->css('/blocks/ases/style/side_menu_style.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap.min.css', true);


//$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);

$output = $PAGE->get_renderer('block_ases');
$index_page = new \block_ases\output\backup_forms_page($data);

echo $output->header();
echo $output->render($index_page);
echo $output->footer();