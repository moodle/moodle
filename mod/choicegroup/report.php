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
 * Version information
 *
 * @package    mod
 * @subpackage choicegroup
 * @copyright  2013 Université de Lausanne
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once("../../config.php");
require_once("lib.php");

$id         = required_param('id', PARAM_INT);   //moduleid
$format     = optional_param('format', CHOICEGROUP_PUBLISH_NAMES, PARAM_INT);
$download   = optional_param('download', '', PARAM_ALPHA);
$action     = optional_param('action', '', PARAM_ALPHA);
$grpsmemberids = optional_param_array('grpsmemberid', array(), PARAM_INT); //get array of responses to delete.

$url = new moodle_url('/mod/choicegroup/report.php', array('id'=>$id));
if ($format !== CHOICEGROUP_PUBLISH_NAMES) {
    $url->param('format', $format);
}
if ($download !== '') {
    $url->param('download', $download);
}
if ($action !== '') {
    $url->param('action', $action);
}
$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('choicegroup', $id)) {
    print_error("invalidcoursemodule");
}

if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
    print_error("coursemisconf");
}

require_login($course->id, false, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/choicegroup:readresponses', $context);

if (!$choicegroup = choicegroup_get_choicegroup($cm->instance)) {
    print_error('invalidcoursemodule');
}

$strchoicegroup = get_string("modulename", "choicegroup");
$strchoicegroups = get_string("modulenameplural", "choicegroup");
$strresponses = get_string("responses", "choicegroup");

$eventparams = array(
    'context' => $context,
    'objectid' => $choicegroup->id
);
$event = \mod_choicegroup\event\report_viewed::create($eventparams);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('choicegroup', $choicegroup);
$event->trigger();

if (data_submitted() && $action == 'delete' && has_capability('mod/choicegroup:deleteresponses',$context) && confirm_sesskey()) {
    choicegroup_delete_responses($grpsmemberids, $choicegroup, $cm, $course); //delete responses.
    redirect("report.php?id=$cm->id");
}

if (!$download) {
    $PAGE->navbar->add($strresponses);
    $PAGE->set_title(format_string($choicegroup->name).": $strresponses");
    $PAGE->set_heading(format_string($course->fullname));
    echo $OUTPUT->header();
    echo $OUTPUT->heading(format_string($choicegroup->name));
    /// Check to see if groups are being used in this choicegroup
    $groupmode = groups_get_activity_groupmode($cm);
    if ($groupmode) {
        groups_get_activity_group($cm, true);
        groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/choicegroup/report.php?id='.$id);
    }
} else {
    $groupmode = groups_get_activity_groupmode($cm);
    $groups = choicegroup_get_groups($choicegroup);
    $groups_ids = array();
    foreach($groups as $group) {
        $groups_ids[] = $group->id;
    }
}
$users = choicegroup_get_response_data($choicegroup, $cm, $groupmode);

if ($download == "ods" && has_capability('mod/choicegroup:downloadresponses', $context)) {
    require_once("$CFG->libdir/odslib.class.php");

/// Calculate file name
    $filename = clean_filename("$course->shortname ".strip_tags(format_string($choicegroup->name,true))).'.ods';
/// Creating a workbook
    $workbook = new MoodleODSWorkbook("-");
/// Send HTTP headers
    $workbook->send($filename);
/// Creating the first worksheet
    $myxls = $workbook->add_worksheet($strresponses);

/// Print names of all the fields
    $myxls->write_string(0,0,get_string("lastname"));
    $myxls->write_string(0,1,get_string("firstname"));
    $myxls->write_string(0,2,get_string("idnumber"));
    $myxls->write_string(0,3,get_string("email"));
    $myxls->write_string(0,4,get_string("group"));
    $myxls->write_string(0,5,get_string("choice","choicegroup"));

/// generate the data for the body of the spreadsheet
    $i=0;
    $row=1;
    if ($users) {
        $displayed = array();
        foreach ($users as $option => $userid) {
            foreach($userid as $user) {
                if (in_array($user->id, $displayed)) {
                    continue;
                }
                $displayed[] = $user->id;
                $myxls->write_string($row,0,$user->lastname);
                $myxls->write_string($row,1,$user->firstname);
                $studentid=(!empty($user->idnumber) ? $user->idnumber : " ");
                $myxls->write_string($row,2,$studentid);
                $myxls->write_string($row,3,$user->email);
                $ug2 = array();
                if ($usergrps = groups_get_all_groups($course->id, $user->id)) {
                    foreach ($groups_ids as $gid) {
                        if (array_key_exists($gid, $usergrps)) {
                            $ug2[] = format_string($usergrps[$gid]->name);
                        }
                    }
                }
                $myxls->write_string($row, 4, implode(', ', $ug2));
                $row++;
                $pos=5;
            }
        }
    }
    /// Close the workbook
    $workbook->close();

    exit;
}

//print spreadsheet if one is asked for:
if ($download == "xls" && has_capability('mod/choicegroup:downloadresponses', $context)) {
    require_once("$CFG->libdir/excellib.class.php");

/// Calculate file name
    $filename = clean_filename("$course->shortname ".strip_tags(format_string($choicegroup->name,true))).'.xls';
/// Creating a workbook
    $workbook = new MoodleExcelWorkbook("-");
/// Send HTTP headers
    $workbook->send($filename);
/// Creating the first worksheet
    // assigning by reference gives this: Strict standards: Only variables should be assigned by reference in /data_1/www/html/moodle/moodle/mod/choicegroup/report.php on line 157
    // removed the ampersand.
    $myxls = $workbook->add_worksheet($strresponses);
/// Print names of all the fields
    $myxls->write_string(0,0,get_string("lastname"));
    $myxls->write_string(0,1,get_string("firstname"));
    $myxls->write_string(0,2,get_string("idnumber"));
    $myxls->write_string(0,3,get_string("email"));
    $myxls->write_string(0,4,get_string("group"));
    $myxls->write_string(0,5,get_string("choice","choicegroup"));


/// generate the data for the body of the spreadsheet
    $i=0;
    $row=1;
    if ($users) {
        $displayed = array();
        foreach ($users as $option => $userid) {
            foreach($userid as $user) {
                if (in_array($user->id, $displayed)) {
                    continue;
                }
                $displayed[] = $user->id;
                $myxls->write_string($row,0,$user->lastname);
                $myxls->write_string($row,1,$user->firstname);
                $studentid=(!empty($user->idnumber) ? $user->idnumber : " ");
                $myxls->write_string($row,2,$studentid);
                $myxls->write_string($row,3,$user->email);
                $ug2 = array();
                if ($usergrps = groups_get_all_groups($course->id, $user->id)) {
                    foreach ($groups_ids as $gid) {
                        if (array_key_exists($gid, $usergrps)) {
                            $ug2[] = format_string($usergrps[$gid]->name);
                        }
                    }
                }
                $myxls->write_string($row, 4, implode(', ', $ug2));
                $row++;
            }
        }
        $pos=5;
    }
    /// Close the workbook
    $workbook->close();
    exit;
}

// print text file
if ($download == "txt" && has_capability('mod/choicegroup:downloadresponses', $context)) {
    $filename = clean_filename("$course->shortname ".strip_tags(format_string($choicegroup->name,true))).'.txt';

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    /// Print names of all the fields

    echo get_string("firstname")."\t".get_string("lastname") . "\t". get_string("idnumber") . "\t";
    echo get_string("email") . "\t";
    echo get_string("group"). "\t";
    echo get_string("choice","choicegroup"). "\n";

    /// generate the data for the body of the spreadsheet
    $i=0;
    if ($users) {
        $displayed = array();
        foreach ($users as $option => $userid) {
            foreach($userid as $user) {
                if (in_array($user->id, $displayed)) {
                    continue;
                }
                $displayed[] = $user->id;
                echo $user->lastname;
                echo "\t".$user->firstname;
                $studentid = " ";
                if (!empty($user->idnumber)) {
                    $studentid = $user->idnumber;
                }
                echo "\t". $studentid."\t";
                echo $user->email . "\t";
                $ug2 = array();
                if ($usergrps = groups_get_all_groups($course->id, $user->id)) {
                    foreach ($groups_ids as $gid) {
                        if (array_key_exists($gid, $usergrps)) {
                            $ug2[] = format_string($usergrps[$gid]->name);
                        }
                    }
                }
                echo implode(', ', $ug2) . "\t";
                echo "\n";
            }
        }
    }
    exit;
}
// Show those who haven't answered the question.
if (!empty($choicegroup->showunanswered)) {
    $choicegroup->option[0] = get_string('notanswered', 'choicegroup');
    $choicegroup->maxanswers[0] = 0;
}

$results = prepare_choicegroup_show_results($choicegroup, $course, $cm, $users);
$renderer = $PAGE->get_renderer('mod_choicegroup');
echo $renderer->display_result($results, has_capability('mod/choicegroup:readresponses', $context));

//now give links for downloading spreadsheets.
if (!empty($users) && has_capability('mod/choicegroup:downloadresponses',$context)) {
    $downloadoptions = array();
    $options = array();
    $options["id"] = "$cm->id";
    $options["download"] = "ods";
    $button =  $OUTPUT->single_button(new moodle_url("report.php", $options), get_string("downloadods"));
    $downloadoptions[] = html_writer::tag('li', $button, array('class'=>'reportoption'));

    $options["download"] = "xls";
    $button = $OUTPUT->single_button(new moodle_url("report.php", $options), get_string("downloadexcel"));
    $downloadoptions[] = html_writer::tag('li', $button, array('class'=>'reportoption'));

    $options["download"] = "txt";
    $button = $OUTPUT->single_button(new moodle_url("report.php", $options), get_string("downloadtext"));
    $downloadoptions[] = html_writer::tag('li', $button, array('class'=>'reportoption'));

    $downloadlist = html_writer::tag('ul', implode('', $downloadoptions));
    $downloadlist .= html_writer::tag('div', '', array('class'=>'clearfloat'));
    echo html_writer::tag('div',$downloadlist, array('class'=>'downloadreport'));
}

echo $OUTPUT->footer();

