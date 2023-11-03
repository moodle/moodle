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

if (!has_capability('block/learnerscript:managereports', $context) && !has_capability('block/learnerscript:manageownreports', $context))
    print_error(get_string('badpermissions','block_learnerscript'));

$PAGE->set_url('/blocks/learnerscript/managereport.php');
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');

$learnerscript = get_config('block_learnerscript', 'ls_serialkey');

$lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');

if (!$lsreportconfigstatus) {
    redirect(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/lsconfig.php?import=1'));
    exit;
}
$PAGE->requires->data_for_js("M.cfg.accessls", $learnerscript, true);
$PAGE->requires->jquery_plugin('ui-css');

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

if ($importurl) {
    $c = new curl();
    if ($data = $c->get($importurl)) {
         $data = json_decode($data);
         $xml = base64_decode($data->content);
     } else {
         print_error(get_string('errorimporting',  'block_learnerscript'));
     }
     if ((new ls)->cr_import_xml($xml, $course)) {
         redirect("$CFG->wwwroot/blocks/learnerscript/managereport.php", get_string('reportcreated', 'block_learnerscript'));
     } else {
         print_error(get_string('errorimporting',  'block_learnerscript'));
     }
 }

$mform = new import_form(null, $course->id);

 if ($data = $mform->get_data()) {
     if ($xml = $mform->get_file_content('userfile')) {
         if ((new ls)->cr_import_xml($xml, $course)) {
             redirect("$CFG->wwwroot/blocks/learnerscript/managereport.php", get_string('reportcreated', 'block_learnerscript'));
         } else {
             print_error(get_string('errorimporting',  'block_learnerscript'));
         }
     }
 }

$reports = (new block_learnerscript\local\ls)->cr_get_my_reports($course->id, $USER->id);

$title = get_string('reports', 'block_learnerscript');
$PAGE->navbar->add(get_string('managereports', 'block_learnerscript')); //, $managereporturl);

$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_cacheable(true);

echo $OUTPUT->header();

echo "<script src='https://learnerscript.com/wp-content/plugins/learnerscript/js/highcharts.js'></script>";
echo html_writer::start_tag('div',array('id' => 'licenceresult', 'class' => 'lsacccess'));

// if ($CFG->version >= 2014051200) {
//     $plugins = get_config('tool_log', 'enabled_stores');
//     $plugins = explode(',', $plugins);

//     if (!get_config('logstore_legacy', 'loglegacy') or ! in_array("logstore_legacy", $plugins)) {
//         echo $OUTPUT->error_text(get_string('legacylognotenabled', 'block_learnerscript'), 'generalbox adminwarning');
//     }
// }

echo '<div style="float:right;"><a class="btn linkbtn btn-primary" href="' . $CFG->wwwroot . '/blocks/learnerscript/editreport.php?courseid=' . $course->id . '">' . (get_string('addreport', 'block_learnerscript')) . '</a></div>';

$fileexists = file_exists($CFG->dirroot . '/blocks/coursels/coursels_settings.php');
$courseblockexists = $PAGE->blocks->is_known_block_type('coursels');
if ($fileexists && $courseblockexists && is_siteadmin()) {
    echo '<div><a class="btn linkbtn btn-primary" href="' . $CFG->wwwroot . '/blocks/coursels/coursels_settings.php?tab=widgets">' . get_string('configcoursedashboard', 'block_learnerscript') . '</a></div>';
}

if ($reports) {
    $table = new html_table();
    $table->width = "100%";
    $table->head = array(get_string('name'),  get_string('type', 'block_learnerscript'), get_string('actions'), get_string('download', 'block_learnerscript'));
    $table->align = array('left', 'left', 'left', 'center', 'center');
    $table->size = array('20%', '20%', '10%', '20%', '20%');
    $stredit = get_string('edit');
    $strdelete = get_string('delete');
    $strhide = get_string('hide');
    $strshow = get_string('show');
    $strcopy = get_string('duplicate');
    $strexport = get_string('exportreport', 'block_learnerscript');
    $strschedule = get_string('schedulereport', 'block_learnerscript');

    foreach ($reports as $r) {
        if ($r->courseid == 1) {
            $coursename = '<a href="' . $CFG->wwwroot . '">' . get_string('site') . '</a>';
        } else if (!$coursename = $DB->get_field('course', 'fullname', array('id' => $r->courseid))) {
            $coursename = get_string('deleted');
        } else {
            $coursename = format_string($coursename);
            $coursename = '<a href="' . $CFG->wwwroot . '/blocks/learnerscript/managereport.php">' . $coursename . '</a>';
        }

        if ($owneruser = $DB->get_record('user', array('id' => $r->ownerid))) {
            $owner = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $r->ownerid . '">' . fullname($owneruser) . '</a>';
        } else {
            $owner = get_string('deleted');
        }

        $editcell = '';
        $editcell .= '<a title="' . $stredit . '"  href="editreport.php?id=' . $r->id . '"><img src="' . $OUTPUT->image_url('/t/edit') . '" class="iconsmall" alt="' . $stredit . '" /></a>';
        $editcell .= '<a title="' . $strdelete . '"  href="editreport.php?id=' . $r->id . '&amp;delete=1&amp;sesskey=' . $USER->sesskey . '"><img src="' . $OUTPUT->image_url('/t/delete') . '" class="iconsmall" alt="' . $strdelete . '" /></a>';


        if (!empty($r->visible)) {
            $editcell .= '<a title="' . $strhide . '" href="editreport.php?id=' . $r->id . '&amp;hide=1&amp;sesskey=' . $USER->sesskey . '">' . '<img src="' . $OUTPUT->image_url('/t/hide') . '" class="iconsmall" alt="' . $strhide . '" /></a> ';
        } else {
            $editcell .= '<a title="' . $strshow . '" href="editreport.php?id=' . $r->id . '&amp;show=1&amp;sesskey=' . $USER->sesskey . '">' . '<img src="' . $OUTPUT->image_url('/t/show') . '" class="iconsmall" alt="' . $strshow . '" /></a> ';
        }
        $editcell .= '<a title="' . $strcopy . '" href="editreport.php?id=' . $r->id . '&amp;duplicate=1&amp;sesskey=' . $USER->sesskey . '"><img src="' . $OUTPUT->image_url('/t/copy') . '" class="iconsmall" alt="' . $strcopy . '" /></a>';
        $editcell .= '<a title="' . $strexport . '" href="export.php?id=' . $r->id . '&amp;sesskey=' . $USER->sesskey . '"><img src="' . $OUTPUT->image_url('/t/backup') . '" class="iconsmall" alt="' . $strexport . '" /></a>';
        $properties = new stdClass();
        $properties->courseid = $courseid;
        $reportclass = (new \block_learnerscript\local\ls)->create_reportclass($r->id, $properties);
        if ($reportclass->parent && $r->type != 'statistics') {
            $editcell .= '<a title="' . $strschedule . '" href="./components/scheduler/schedule.php?id=' . $r->id . '&amp;courseid=' . $r->courseid . '&amp;sesskey=' . $USER->sesskey . '"><img src="' . $OUTPUT->image_url('/i/calendar') . '" class="iconsmall" alt="' . $strschedule . '" /></a>';
        }
        $download = '';
        $export = explode(',', $r->export);
        if (!empty($export)) {
            foreach ($export as $e) {
                if ($e) {
                    $download .= '<a href="viewreport.php?id=' . $r->id . '&amp;download=1&amp;format=' . $e . '" title="'.(strtoupper($e)).'"><img src="' . $CFG->wwwroot . '/blocks/learnerscript/export/' . $e . '/pix.gif" alt="' . $e . '">&nbsp;' . (strtoupper($e)) . '</a>';
                } else {
                    $download .= '--';
                }
            }
        }

        $table->data[] = array('<a href="viewreport.php?id=' . $r->id . '">' . $r->name . '</a>', get_string('report_' . $r->type, 'block_learnerscript'), $editcell, $download);
    }

    $table->id = 'reportslist';
    //cr_add_jsordering("#reportslist");
    echo '<div class="cmp_overflow">'.html_writer::table($table).'</div>';
    //cr_print_table($table);
} else {
    echo $OUTPUT->heading(get_string('noreportsavailable', 'block_learnerscript'));
}

$mform->display();
echo html_writer::end_tag('div');
echo $OUTPUT->footer();
