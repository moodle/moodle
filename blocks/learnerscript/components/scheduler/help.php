<?php

require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG, $DB;
$reportid = required_param('id', PARAM_INT);
$courseid = optional_param('courseid', SITEID, PARAM_INT);
$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url('/blocks/learnerscript/components/scheduler/help.php');
$PAGE->set_pagelayout('admin');
$strheading = get_string('pluginname', 'block_learnerscript') .' : '. get_string('manual', 'block_learnerscript');
$PAGE->set_title($strheading);
require_login();
if (!$report = $DB->get_record('block_learnerscript', array('id' => $reportid))) {
    print_error('reportdoesnotexists', 'block_learnerscript');
}
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add($report->name, new moodle_url('/blocks/learnerscript/viewreport.php',
                    array('id' => $reportid, 'courseid' => $courseid)));

$PAGE->navbar->add(get_string('uploadscheduletime', 'block_learnerscript'), new moodle_url('/blocks/learnerscript/components/scheduler/sch_upload.php', array('id' => $reportid, 'courseid' => $courseid)));
$PAGE->navbar->add(get_string('manual', 'block_learnerscript'));
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manual', 'block_learnerscript'));
echo $OUTPUT->box(get_string('helpmanual', 'block_learnerscript'));
echo '<div style="float:right;"><a href="' . $CFG->wwwroot . '/blocks/learnerscript/components/scheduler/sch_upload.php?id=' . $reportid . '"><button>' . get_string('back_upload', 'block_learnerscript') . '</button></a></div>';

$helpinstance = New stdClass();
$roles = $DB->get_records_sql("SELECT id, shortname FROM {role} WHERE shortname NOT IN ('guest', 'user', 'frontpage')");
$rolelist  = array();
asort($roles);
// $rolelist[] = '-1 : anyone';
foreach($roles as $role){

            switch ($role->shortname) {
                case 'manager':         $original = get_string('manager', 'role'); break;
                case 'coursecreator':   $original = get_string('coursecreators'); break;
                case 'editingteacher':  $original = get_string('defaultcourseteacher'); break;
                case 'teacher':         $original = get_string('noneditingteacher'); break;
                case 'student':         $original = get_string('defaultcoursestudent'); break;
                case 'guest':           $original = get_string('guest'); break;
                case 'user':            $original = get_string('authenticateduser'); break;
                case 'frontpage':       $original = get_string('frontpageuser', 'role'); break;
                // We should not get here, the role UI should require the name for custom roles!
                default:                $original = $role->shortname; break;
            }

	$rolelist[] = $role->id.' : '.$original;
}
$helpinstance->rolelist = implode(', ',$rolelist);
echo get_string('help_1', 'block_learnerscript',$helpinstance);
echo $OUTPUT->footer();
