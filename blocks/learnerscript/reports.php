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
use block_learnerscript\form\import_form;
use block_learnerscript\local\ls as ls;

$courseid = optional_param('courseid', SITEID, PARAM_INT);
$importurl = optional_param('importurl', '', PARAM_RAW);
$contextlevel = optional_param('contextlevel', 10, PARAM_INT);
if (!$course = $DB->get_record("course", array("id" => $courseid))) {
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


$PAGE->set_url('/blocks/learnerscript/reports.php', array('courseid' => $course->id));
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$learnerscript = get_config('block_learnerscript', 'ls_serialkey');
if (empty($learnerscript)) {
    throw new moodle_exception("License Key Is Required");
    exit();
}

$lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');

if (!$lsreportconfigstatus) {
    redirect(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/lsconfig.php?import=1'));
    exit;
}
if (empty($_SESSION['role'])) {
    $rolelist = (new ls)->get_currentuser_roles();
    if (!is_siteadmin()) {
        if (!empty($role) && in_array($role, $rolelist)) {
            $role = empty($role) ? array_shift($rolelist) : $role;
        } else if (empty($role)) {
            $role = empty($role) ? array_shift($rolelist) : $role;
        } else {
            $role = '';
        }
        $_SESSION['role'] = $role;
    } else {
        $_SESSION['role'] = $role;
    }
}
$_SESSION['ls_contextlevel'] = $contextlevel; 
$rolecontexts = $DB->get_records_sql("SELECT DISTINCT CONCAT(r.id, '@', rcl.id), 
                        r.shortname, rcl.contextlevel 
                        FROM {role} AS r 
                        JOIN {role_context_levels} AS rcl ON rcl.roleid = r.id AND rcl.contextlevel NOT IN (70)
                        WHERE 1 = 1  
                        ORDER BY rcl.contextlevel ASC");
foreach ($rolecontexts as $rc) {
   if ($rc->contextlevel == 10 && ($rc->shortname == 'manager')) {
    continue;
   }
   $rcontext[] = $rc->shortname .'_'.$rc->contextlevel;
}
$_SESSION['rolecontextlist'] = $rcontext;

$PAGE->requires->data_for_js("M.cfg.accessls", $learnerscript , true);
// $PAGE->requires->js(new moodle_url('https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'));
$PAGE->requires->jquery_plugin('ui-css');

$statisticsreports = (new block_learnerscript\local\ls)->listofreportsbyrole(false, true, false, true);
$customreports = (new block_learnerscript\local\ls)->listofreportsbyrole(false, false, false, true);
$reports = array_merge($statisticsreports, $customreports);

$title = get_string('reports', 'block_learnerscript');
$PAGE->navbar->add(get_string('reports', 'block_learnerscript'));

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_cacheable(true);

echo $OUTPUT->header();
echo "<script src='https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'></script>";
echo html_writer::start_tag('div',array('id' => 'licenceresult', 'class' => 'lsacccess'));
echo html_writer::start_tag('div',array('id' => 'listofreports'));
echo '<div style="float:right;"><a class="btn linkbtn btn-primary" href="' . $CFG->wwwroot . '/blocks/reportdashboard/dashboard.php?role='.$_SESSION['role'].'&contextlevel='.$_SESSION['ls_contextlevel'].'">' . (get_string('dashboard', 'block_reportdashboard')) . '</a></div>';

if ($reports) {
    $table = new html_table();
    $table->width = "100%";
    $table->head = array(get_string('name'),  get_string('type', 'block_learnerscript'));
    $table->align = array('left', 'left');
    foreach ($reports as $r) {
        $reporttype = $DB->get_record_sql("SELECT type FROM {block_learnerscript} WHERE id = :reportid", ['reportid' => $r['id']]);
        $table->data[] = array('<a href="viewreport.php?id=' . $r['id'] . '">' . $r['name'] . '</a>', get_string('report_' . $reporttype->type, 'block_learnerscript'));
    }

    // $table->id = 'reportslist';
    echo '<div class="cmp_overflow">'.html_writer::table($table).'</div>';
} else {
    echo $OUTPUT->heading(get_string('noreportsavailable', 'block_learnerscript'));
}

echo html_writer::end_tag('div');
echo html_writer::end_tag('div');
echo $OUTPUT->footer();