<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('../managers/query.php');
include('../lib.php');
include("../classes/output/psicosocial_users_page.php");
include("../classes/output/renderer.php");

global $PAGE;

// Variables for page setup
$title = get_string('pluginname', 'block_ases');
$pagetitle = $title;
$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('instanceid', PARAM_INT);

require_login($courseid, false);

// Instance is consulted for its registration
if(!consultInstance($blockid)){
    header("Location: /blocks/ases/view/instanceconfiguration.php?courseid=$courseid&instanceid=$blockid");
}

$contextcourse = context_course::instance($courseid);
$contextblock =  context_block::instance($blockid);

$url = new moodle_url("/blocks/ases/view/psicosocial_users.php",array('courseid' => $courseid, 'instanceid' => $blockid));

//Navigation setup
$coursenode = $PAGE->navigation->find($courseid, navigation_node::TYPE_COURSE);
$blocknode = navigation_node::create($title,$url, null, 'block', $blockid);
$coursenode->add_node($blocknode);
$blocknode->make_active();

$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);

$PAGE->requires->css('/blocks/ases/style/styles_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/simple-sidebar.css', true);
$PAGE->requires->css('/blocks/ases/style/forms_pilos.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.foundation.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/dataTables.jqueryui.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables.min.css', true);
$PAGE->requires->css('/blocks/ases/js/DataTables-1.10.12/css/jquery.dataTables_themeroller.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.css', true);
$PAGE->requires->css('/blocks/ases/style/bootstrap_pilos.min.css', true);
$PAGE->requires->css('/blocks/ases/style/sweetalert.css', true);
$PAGE->requires->css('/blocks/ases/style/round-about_pilos.css', true);


$PAGE->requires->js('/blocks/ases/js/jquery-2.2.4.min.js', true);
$PAGE->requires->js('/blocks/ases/js/sweetalert-dev.js', true);
$PAGE->requires->js('/blocks/ases/js/user_ps_management.js', true);
$PAGE->requires->js('/blocks/ases/js/main.js', true);
$PAGE->requires->js('/blocks/ases/js/checkrole.js', true);
$PAGE->requires->js('/blocks/ases/js/bootstrap.js', true);
$PAGE->requires->js('/blocks/ases/js/bootstrap.min.js', true);
$PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/jquery.dataTables.js', true);
$PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/jquery.dataTables.min.js', true);
$PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.jqueryui.min.js', true);
$PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.bootstrap.min.js', true);
$PAGE->requires->js('/blocks/ases/js/DataTables-1.10.12/js/dataTables.bootstrap.js', true);

$output = $PAGE->get_renderer('block_ases');

echo $output->header();
//echo $output->standard_head_html(); 
$prueba = new \block_ases\output\psicosocial_users_page('Some text');
echo $output->render($prueba);
echo $output->footer();