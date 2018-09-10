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
 * Ases block
 *
 * @author     Edgar Mauricio Ceron Florez
 * @package    block_ases
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Standard GPL and phpdocs
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/query.php');
include('../lib.php');
global $PAGE;

include("../classes/output/permisos_rol_page.php");
include("../classes/output/renderer.php");
require_once('../managers/query.php');

// Set up the page.
$title = "Permisos por rol";
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/permisos_rol.php",array('courseid' => $courseid, 'instanceid' => $blockid));

// Navegation set up
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create('Permisos Rol',$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/sugerenciaspilos.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->js('/blocks/ases/js/jquery-2.2.4.min.js', true);

$PAGE->requires->js('/blocks/ases/js/jquery.validate.min.js', true);
$PAGE->requires->js('/blocks/ases/js/bootstrap.min.js', true);
$PAGE->requires->js('/blocks/ases/js/bootstrap.js', true);
$PAGE->requires->js('/blocks/ases/js/sweetalert-dev.js', true);
$PAGE->requires->js('/blocks/ases/js/npm.js', true);
$PAGE->requires->js('/blocks/ases/js/checkrole.js', true);
$PAGE->requires->js('/blocks/ases/js/permisos_rol.js', true);
$PAGE->set_url($url);
$PAGE->set_title($title);

$PAGE->set_heading($title);

$output = $PAGE->get_renderer('block_ases');

echo $output->header();

$permisos_rol_page = new \block_ases\output\permisos_rol_page('Some text');
echo $output->render($permisos_rol_page);
echo $output->footer();