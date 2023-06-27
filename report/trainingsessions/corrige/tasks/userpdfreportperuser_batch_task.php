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
 * This script handles the report generation in batch task for a single group.
 * It will produce a group Excel worksheet report that is pushed immediately to output
 * for downloading by a batch agent. No file is stored into the system.
 * groupid must be provided.
 * This script should be sheduled in a CURL call stack or a multi_CURL parallel call.
 *
 * @package    report_trainingsessions
 * @category   report
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// echo '<pre>';
// print_r( $_POST );
// echo '</pre>';
// exit;


require('../../../config.php');
require('fpdf/fpdf.php');

ob_start();

require_once($CFG->dirroot.'/blocks/use_stats/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/locallib.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/xlsrenderers.php');
require_once($CFG->dirroot.'/report/trainingsessions/renderers/pdfrenderers.php');
require_once($CFG->dirroot.'/report/trainingsessions/lib/excellib.php');
setlocale(LC_TIME, "fr_FR");

// $_POST['id'] = required_param('id', PARAM_INT); // The course id.
// $_POST['userid'] = required_param('userid', PARAM_INT); // The group id.
$reportscope = optional_param('scope', 'currentcourse', PARAM_TEXT); // Only currentcourse is consistant.

ini_set('memory_limit', '512M');

if (!$course = $DB->get_record('course', array('id' => $_POST['id']))) {
    die ('Invalid course ID');
}
$context = context_course::instance($_POST['id']);
$PAGE->set_context($context);

if (!$user = $DB->get_record('user', array('id' => $_POST['userid']))) {
    // Do NOT print_error here as we are a document writer.
    die ('Invalid user ID');
}

$input = report_trainingsessions_batch_input($course);

// Security.
report_trainingsessions_back_office_access($course, $_POST['userid']);

$cols = report_trainingsessions_get_summary_cols();

$coursestructure = report_trainingsessions_get_course_structure($_POST['id'], $items);

// Generate XLS.
$auser = $DB->get_record('user', array('id' => $_POST['userid']));
$startrow = report_trainingsessions_count_header_rows($_POST['id']);
$row = $startrow;
$logusers = $auser->id;
$logs = use_stats_extract_logs($_POST['from'], $_POST['to'], $_POST['userid'], $_POST['id']);
$aggregate = use_stats_aggregate_logs($logs, 'module',$_POST['from'], $_POST['to']);
$grantotal = report_trainingsessions_calculate_course_structure($coursestructure, $aggregate, $done, $items);
$grantotal->from = $input->from;
$grantotal->to =  $_POST['to'];
$grantotal->activityelapsed = 0 + @$aggregate['activities'][$_POST['id']]->elapsed;
$grantotal->otherelapsed = 0 + @$aggregate['other'][$_POST['id']]->elapsed;
$grantotal->courseelapsed = 0 + @$aggregate['course'][$_POST['id']]->elapsed;

$grantotal->elapsed = 0 + @$aggregate['coursetotal'][$_POST['id']]->elapsed;
$grantotal->elapsedlastweek = 0 + @$aggregatelastweek['coursetotal'][$_POST['id']]->elapsed;
$grantotal->extelapsed = 0 + @$aggregate['coursetotal'][$_POST['id']]->elapsed + @$aggregate['coursetotal'][0]->elapsed;
$grantotal->extelapsed += @$aggregate['coursetotal'][SITEID]->elapsed;
$grantotal->extelapsedlastweek = 0 + @$aggregatelastweek['coursetotal'][$_POST['id']]->elapsed + @$aggregatelastweek['coursetotal'][0]->elapsed;
$grantotal->extelapsedlastweek += @$aggregatelastweek['coursetotal'][SITEID]->elapsed;
$grantotal->extother = 0 + @$aggregate['coursetotal'][0]->elapsed + @$aggregate['coursetotal'][SITEID]->elapsed;
$grantotal->extotherlastweek = 0 + @$aggregatelastweek['coursetotal'][0]->elapsed + @$aggregatelastweek['coursetotal'][SITEID]->elapsed;

$grantotal->activityevents = 0 + @$aggregate['activities'][$_POST['id']]->events;
$grantotal->otherevents = 0 + @$aggregate['other'][$_POST['id']]->events;
$grantotal->courseevents = 0 + @$aggregate['course'][$_POST['id']]->events;
$grantotal->events = 0 + $grantotal->activityevents + $grantotal->otherevents + $grantotal->courseevents;
$grantotal->items = $grantotal->events; // Compatibility ?
$grantotal->extevents = 0 + @$aggregate['coursetotal'][$_POST['id']]->events + @$aggregate['coursetotal'][0]->events;
$grantotal->extevents += @$aggregate['coursetotal'][SITEID]->events;

$row = 0;

$pdf = new FPDF();
$pdf->AddPage();
$pdf->Image('https://formassmat-moodle.fr/report/trainingsessions/img/formassmatLogo.png',160,0,45,0,'PNG');
$pdf->SetFont('Arial','B',16);
$pdf->MultiCell(0,0,utf8_decode('Relevé de temps'));
$row++;
$pdf->Ln(10);
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(0,0,utf8_decode(get_string('user')).' : '.utf8_decode(fullname($user)));
$row++;
$pdf->Ln(5);
$pdf->MultiCell(0,0,utf8_decode(get_string('email')).' : '.$user->email);
$row++;

$timeformat = get_string('profileinfotimeformat', 'report_trainingsessions');

if (!empty($config->extrauserinfo1)) {
    $fieldname = $DB->get_field('user_info_field', 'name', array('id' => $config->extrauserinfo1)).':';
    $fieldtype = $DB->get_field('user_info_field', 'datatype', array('id' => $config->extrauserinfo1));
    $info = $DB->get_field('user_info_data', 'data', array('userid' => $user->id, 'fieldid' => $config->extrauserinfo1));
    $pdf->Ln(5);
    $pdf->MultiCell(0,0,$fieldname);

    if ($fieldtype == 'datetime') {
        // Possible alternatives : write in real date cell or in text.
        $pdf->MultiCell(0,0,$info);

        $info = strftime($timeformat, $info);
        $pdf->MultiCell(0,0,$info);
    } else {
       $pdf->MultiCell(0,0,$info);
    }
    $row++;
}


if (!empty($config->extrauserinfo2)) {
    $fieldname = $DB->get_field('user_info_field', 'name', array('id' => $config->extrauserinfo2)).':';
    $fieldtype = $DB->get_field('user_info_field', 'datatype', array('id' => $config->extrauserinfo2));
    $info = $DB->get_field('user_info_data', 'data', array('userid' => $user->id, 'fieldid' => $config->extrauserinfo2));
    $pdf->Ln(5);
    $pdf->MultiCell(0,0,$fieldname);
    if ($fieldtype == 'datetime') {
        // Possible alternatives : write in real date cell or in text.
        $pdf->MultiCell(0,0,$info);

        $info = strftime($timeformat, $info);
        $pdf->MultiCell(0,0,$info);
    } else {
       $pdf->MultiCell(0,0,$info);
    }
    $row++;
}


if ($_POST['id']) {
    $pdf->Ln(5);
    $pdf->MultiCell(0,0,utf8_decode(get_string('course', 'report_trainingsessions')).' : '.utf8_decode(format_string($course->fullname)));
    $row++;
}

$pdf->Ln(5);
$pdf->MultiCell(0,0,'Du'.' : '.utf8_decode(strftime("%A %d %B %G", $grantotal->from)));
$row++;

$timeend = $DB->get_timeend($user->id, $course->id);

$pdf->Ln(5);
$pdf->MultiCell(0,0,'Au'.' : '.utf8_decode(strftime("%A %d %B %G",$timeend->timeend)));
$row++;



if ($_POST['id']) {
    $usergroups = groups_get_all_groups($_POST['id'], $_POST['userid'], 0, 'g.id, g.name');

    // Print group status.
    $str = '';
    if (!empty($usergroups)) {
        foreach ($usergroups as $group) {
            $str = $group->name;
            if ($group->id == groups_get_course_group($course)) {
                $str = "[$str]";
            }
            $groupnames[] = format_string($str);
        }
        $str = implode(', ', $groupnames);
    }
    $pdf->Ln(5);
    $pdf->MultiCell(0,0,get_string('groups').' : '.$str);
    $row++;
}

if (empty($grantotal->items)) {
    $completed = 0;
} else {
    $completed = (0 + @$grantotal->done) / $grantotal->items;
}

// $remaining = 1 - $completed;
// $completedpc = ceil($completed * 100);
// $remainingpc = 100 - $completedpc;

// $celldata = (0 + @$grantotal->done).' '.get_string('over', 'report_trainingsessions').' ';
// $celldata .= (0 + @$grantotal->items).' ('.$completedpc.' %)';
// $pdf->Ln(5);
// $pdf->MultiCell(0,0,utf8_decode(get_string('done', 'report_trainingsessions')).' : '.$celldata);

if (in_array('elapsed', $cols)) {
    $row++;
    $pdf->Ln(5);
    $pdf->MultiCell(0,0,'Temps total'.' : '.$_POST["totalTimElapsed"]);
}
// if (in_array('extelapsed', $cols)) {
//     $row++;
//     $pdf->Ln(5);
//     $elapsed = report_trainingsessions_format_time((0 + @$grantotal->extelapsed), 'pdf');
//     $pdf->MultiCell(0,0,utf8_decode(get_string('extelapsed', 'report_trainingsessions')).' : '.$elapsed);
// }
// if (in_array('extother', $cols)) {
//     $row++;
//     $pdf->Ln(5);
//     $elapsed = report_trainingsessions_format_time((0 + @$grantotal->extother), 'pdf');
//     $pdf->MultiCell(0,0,utf8_decode(get_string('extother', 'report_trainingsessions')).' : '.$elapsed);
// }

// if (in_array('elapsedlastweek', $cols)) {
//     $row++;
//     $pdf->Ln(5);
//     $elapsed = report_trainingsessions_format_time((0 + @$grantotal->elapsedlastweek), 'pdf');
//     $pdf->MultiCell(0,0,utf8_decode(get_string('elapsedlastweek', 'report_trainingsessions')).' : '.$elapsed);
// }
// if (in_array('extelapsedlastweek', $cols)) {
//     $row++;
//     $pdf->Ln(5);
//     $elapsed = report_trainingsessions_format_time((0 + @$grantotal->extelapsedlastweek), 'pdf');
//     $pdf->MultiCell(0,0,utf8_decode(get_string('extelapsedlastweek', 'report_trainingsessions')).' : '.$elapsed);
// }
// if (in_array('extotherlastweek', $cols)) {
//     $row++;
//     $pdf->Ln(5);
//     $elapsed = report_trainingsessions_format_time((0 + @$grantotal->extotherlastweek), 'pdf');
//     $pdf->MultiCell(0,0,utf8_decode(get_string('extotherlastweek', 'report_trainingsessions')).' : '.$elapsed);
// }
// if (in_array('coursetime', $cols)) {
//     $row++;
//     $pdf->Ln(5);
//     $elapsed = report_trainingsessions_format_time((0 + @$grantotal->courseelapsed), 'pdf');
//     $pdf->MultiCell(0,0,utf8_decode(get_string('coursetime', 'report_trainingsessions')).' : '.$elapsed);
// }
// if (in_array('activityelapsed', $cols)) {
//     $row++;
//     $pdf->Ln(5);
//     $elapsed = report_trainingsessions_format_time((0 + @$grantotal->activityelapsed), 'pdf');
//     $pdf->MultiCell(0,0,utf8_decode(get_string('activitytime', 'report_trainingsessions')).' : '.$elapsed);
// }
// if (in_array('otherelapsed', $cols)) {
//     $row++;
//     $pdf->Ln(5);
//     $elapsed = report_trainingsessions_format_time((0 + @$grantotal->otherelapsed + @$grantotal->courseelapsed), 'pdf');
//     $pdf->MultiCell(0,0,utf8_decode(get_string('othertime', 'report_trainingsessions')).' : '.$elapsed);
// }

// if (!empty($config->showhits)) {
//     $row++;
//     $pdf->Ln(5);
//     $pdf->MultiCell(0,0,utf8_decode(get_string('hits', 'report_trainingsessions')).' : '.(0 + @$grantotal->events));
// }

// if (!empty($aggregate['sessions'])) {


// }
$pdf->SetDrawColor(0,0,0);
$pdf->SetLineWidth(0.3);
$pdf->Line(0,55,220,55);
$pdf->Ln(15);
$pdf->SetFont('Arial','B',16);
$pdf->MultiCell(0,0,'Sessions de travail');
$pdf->SetFont('Arial','',10);
$pdf->Ln(10);
if (!empty($aggregate['sessions'])) {
    report_trainingsessions_print_sessions_pdf($pdf, 15, $aggregate['sessions'], $course, $xlsformats);
}

$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Ln(15);
$pdf->MultiCell(0,0,utf8_decode('Temps dans les activités'));
$pdf->SetFont('Arial','',10);
$pdf->Ln(10);
$pdf->SetX(175);
$pdf->MultiCell(0,0,utf8_decode(get_string('elapsed', 'report_trainingsessions')));
$pdf->SetX(0);
$dataobject = report_trainingsessions_print_pdf($pdf, $coursestructure, $aggregate, $done, $row, $xlsformats);


if (empty($dataobject)) {
    $dataobject = new stdClass();
}
$dataobject->items = $items;
$dataobject->done = $done;

if ($dataobject->done > $items) {
    $dataobject->done = $items;
}

// In-activity.

$dataobject->activityelapsed = @$aggregate['activities'][$_POST['id']]->elapsed;
$dataobject->activityevents = @$aggregate['activities'][$_POST['id']]->events;
$dataobject->otherelapsed = @$aggregate['other'][$_POST['id']]->elapsed;
$dataobject->otherevents = @$aggregate['other'][$_POST['id']]->events;

$dataobject->course = new StdClass;

// Calculate in-course-out-activities.

$dataobject->course->elapsed = 0;
$dataobject->course->events = 0;

if (!empty($aggregate['course'])) {
    $dataobject->course->elapsed = 0 + @$aggregate['course'][$_POST['id']]->elapsed;
    $dataobject->course->events = 0 + @$aggregate['course'][$_POST['id']]->events;
}

// Calculate everything.

$dataobject->elapsed = $dataobject->activityelapsed + $dataobject->course->elapsed;
$dataobject->extelapsed = $dataobject->activityelapsed + $dataobject->otherelapsed + $dataobject->course->elapsed;
$dataobject->events = $dataobject->activityevents + $dataobject->otherevents + $dataobject->course->events;

if (array_key_exists('upload', $aggregate)) {
    $dataobject->elapsed += @$aggregate['upload'][0]->elapsed;
    $dataobject->upload = new StdClass;
    $dataobject->upload->elapsed = 0 + @$aggregate['upload'][0]->elapsed;
    $dataobject->upload->events = 0 + @$aggregate['upload'][0]->events;
}

$pdf->Output();

exit();


// $xlsformats = report_trainingsessions_xls_formats($workbook);
$startrow = report_trainingsessions_count_header_rows($_POST['id']);

$row = $startrow;
$worksheet = report_trainingsessions_init_worksheet($auser->id, $row, $xlsformats, $workbook);

$logusers = $auser->id;
$logs = use_stats_extract_logs($input->from, $input->to, $auser->id, $_POST['id']);
$aggregate = use_stats_aggregate_logs($logs, $input->from, $input->to);

$overall = report_trainingsessions_print_xls($worksheet, $coursestructure, $aggregate, $done, $row, $xlsformats);

$grantotal = report_trainingsessions_calculate_course_structure($coursestructure, $aggregate, $done, $items);

$grantotal->from = $input->from;
$grantotal->activityelapsed = 0 + @$aggregate['activities'][$_POST['id']]->elapsed;
$grantotal->otherelapsed = 0 + @$aggregate['other'][$_POST['id']]->elapsed;
$grantotal->courseelapsed = 0 + @$aggregate['course'][$_POST['id']]->elapsed;

$grantotal->elapsed = 0 + @$aggregate['coursetotal'][$_POST['id']]->elapsed;
$grantotal->elapsedlastweek = 0 + @$aggregatelastweek['coursetotal'][$_POST['id']]->elapsed;
$grantotal->extelapsed = 0 + @$aggregate['coursetotal'][$_POST['id']]->elapsed + @$aggregate['coursetotal'][0]->elapsed;
$grantotal->extelapsed += @$aggregate['coursetotal'][SITEID]->elapsed;
$grantotal->extelapsedlastweek = 0 + @$aggregatelastweek['coursetotal'][$_POST['id']]->elapsed + @$aggregatelastweek['coursetotal'][0]->elapsed;
$grantotal->extelapsedlastweek += @$aggregatelastweek['coursetotal'][SITEID]->elapsed;
$grantotal->extother = 0 + @$aggregate['coursetotal'][0]->elapsed + @$aggregate['coursetotal'][SITEID]->elapsed;
$grantotal->extotherlastweek = 0 + @$aggregatelastweek['coursetotal'][0]->elapsed + @$aggregatelastweek['coursetotal'][SITEID]->elapsed;

$grantotal->activityevents = 0 + @$aggregate['activities'][$_POST['id']]->events;
$grantotal->otherevents = 0 + @$aggregate['other'][$_POST['id']]->events;
$grantotal->courseevents = 0 + @$aggregate['course'][$_POST['id']]->events;
$grantotal->events = 0 + $grantotal->activityevents + $grantotal->otherevents + $grantotal->courseevents;
$grantotal->items = $grantotal->events; // Compatibility ?
$grantotal->extevents = 0 + @$aggregate['coursetotal'][$_POST['id']]->events + @$aggregate['coursetotal'][0]->events;
$grantotal->extevents += @$aggregate['coursetotal'][SITEID]->events;

report_trainingsessions_print_header_xls($worksheet, $auser->id, $_POST['id'], $grantotal, $xlsformats);

if (!empty($aggregate['sessions'])) {
    $worksheet = report_trainingsessions_init_worksheet($auser->id, $startrow, $xlsformats, $workbook, 'sessions');
    report_trainingsessions_print_sessions_xls($worksheet, 15, $aggregate['sessions'], $course, $xlsformats);
    report_trainingsessions_print_header_xls($worksheet, $auser->id, $_POST['id'], $grantotal, $xlsformats);
}

$workbook->close();
