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

require_once('../../config.php');
require_once($CFG->libdir.'/completionlib.php');
require_once('select_form.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');

// Deal with params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$dodownload = optional_param('dodownload', 0, PARAM_INT);

// Check permissions.
require_login($SITE);
$context = context_system::instance();
iomad::require_capability('local/report_scorm_overview:view', $context);

// Url stuff.
$url = new moodle_url('/local/report_scorm_overview/index.php',
                      array('courseid' => $courseid));
$dashboardurl = new moodle_url('/my');

// Page stuff:.
$strcompletion = get_string('pluginname', 'local_report_scorm_overview');
$PAGE->set_url($url);
$PAGE->set_pagelayout('report');
$PAGE->set_title($strcompletion);
$PAGE->requires->css("/local/report_scorm_overvew/styles.css");

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $strcompletion");

// Set the url.
company_admin_fix_breadcrumb($PAGE, $strcompletion, $url);

// Navigation and header.
if (empty($dodownload)) {
    echo $OUTPUT->header();
}

// Get the SCORM data.
if (!$scormmod = $DB->get_record('modules', array('name' => 'scorm'))) {
    if (empty($dodownload)) {
        echo "<h1>".get_string('scormnotinstalled', 'local_report_scorm_overview')."<h1>";
        echo $OUTPUT->footer();
    }
    die;
}

// Get the company course instances.
$sql = "SELECT DISTINCT c.id, c.fullname from {course_modules} cm, {course} c WHERE
        cm.module = ".$scormmod->id." AND c.id = cm.course";

if (!$courselist = $DB->get_records_sql($sql)) {
    if (empty($dodownload)) {
        echo "<h3>".get_string('noscormcourses', 'local_report_scorm_overview')."</h3>";
        echo $OUTPUT->footer();
    }
    die;
}

// Process the course list.
if (!empty($courselist)) {
    $courseselect = array();
    foreach ($courselist as $selectinfo) {
        $courseselect[$selectinfo->id] = $selectinfo->fullname;
    }
    $select = new single_select($url, 'courseid', $courseselect, $courseid);
    $select->label = get_string('courseselect', 'local_report_scorm_overview');
    $select->formid = 'choosecourse';
    if (empty($dodownload)) {
        echo html_writer::tag('div', $OUTPUT->render($select),
                               array('id' => 'iomad_course_selector'));
        $fwselectoutput = html_writer::tag('div', $OUTPUT->render($select),
                                            array('id' => 'iomad_course_selector'));
    }
}

// Check if we have been passed some data.
if (!empty($courseid)) {
    // Get the scorm id's in this course.
    $scormids = $DB->get_records('scorm', array('course' => $courseid));
    foreach ($scormids as $scormid) {
        $scormdata = new stdclass();
        $scormident = $scormid->id;
        // Get the users who have attempted this.
        if ($scormusers = $DB->get_records_sql("SELECT DISTINCT(userid)
                                                FROM {scorm_scoes_track}
                                                WHERE scormid=".$scormid->id)) {
            foreach ($scormusers as $scormuser) {
                if ($scormscores = $DB->get_records('scorm_scoes_track',
                                                     array('scormid' => $scormid->id,
                                                           'userid' => $scormuser->userid))) {
                    // Process the scores.
                    foreach ($scormscores as $data) {
                        if ($data->element == "x.start.time") {
                            continue;
                        } else if ($data->element == "cmi.core.lesson_status") {
                            continue;
                        } else if ($data->element == "cmi.core.score.raw") {
                            continue;
                        } else if ($data->element == "cmi.core.score.min") {
                            continue;
                        } else if ($data->element == "cmi.core.score.max") {
                            continue;
                        } else if ($data->element == "cmi.core.exit") {
                            continue;
                        } else if ($data->element == "cmi.core.total_time") {
                            continue;
                        } else if ($data->element == "cmi.core.lesson_location") {
                            continue;
                        } else if ($data->element == "cmi.completion_status") {
                            continue;
                        } else {
                            // Check we have interaction_ and not interaction.
                            if (strpos($data->element, 'interaction_') || strpos($data->element, 'interactions_')) {
                                list($cmi, $elementraw, $target) = explode('.', $data->element);
                            } else {
                                list($cmi, $elementraw, $g, $target) = explode('.', $data->element);
                            }
                            if ($target == 'id') {
                                $questionid = preg_replace('/[qQ]/', '', $data->value);
                                if (!isset($scormdata->$questionid->correct)) {
                                    $scormdata->$questionid->correct = 0;
                                    $scormdata->$questionid->wrong = 0;
                                }
                            } else if ($target == 'type') {
                                $scormdata->$questionid->type = $data->value;
                            } else if ($target == 'result') {
                                if ($data->value == 'correct') {
                                    ++$scormdata->$questionid->correct;
                                } else if ($data->value == 'wrong') {
                                    ++$scormdata->$questionid->wrong;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if (empty($dodownload)) {
                echo "<h2>".$scormid->name. "</h2>";
                echo get_string('scormnoresults', 'local_report_scorm_overview');
            }
            continue;
        }
        if (empty($dodownload)) {
            echo "<h2>".$scormid->name."</h2>";
            echo $OUTPUT->single_button(new moodle_url('index.php',
                                        array('courseid' => $courseid,
                                              'dodownload' => '1')),
                                        get_string("downloadcsv", 'local_report_completion'));
        } else {
            //  Set up the Excel workbook.

            header("Content-Type: application/download\n");
            header("Content-Disposition: attachment; filename=\"scormreport.csv\"");
            header("Expires: 0");
            header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
            header("Pragma: public");

            echo '"'.$scormid->name."\"\n";
        }
        // Create the table of results.
        $scormscoretable = new html_table();
        if (!empty($dodownload)) {
            echo '"'.get_string('question', 'local_report_scorm_overview').'","'.
                     get_string('questiontype', 'local_report_scorm_overview').'","'.
                     get_string('numattempts', 'local_report_scorm_overview').'","'.
                     get_string('percentright', 'local_report_scorm_overview').'","'.
                     get_string('percentwrong', 'local_report_scorm_overview')."\"\n";
        }
        $scormscoretable->head = array(get_string('question', 'local_report_scorm_overview'),
                                       get_string('questiontype', 'local_report_scorm_overview'),
                                       get_string('numattempts', 'local_report_scorm_overview'),
                                       get_string('percentright', 'local_report_scorm_overview'),
                                       get_string('percentwrong', 'local_report_scorm_overview'));
        $scormscoretable->align = array('center', 'center', 'center', 'center', 'center');
        $scormscoretable->width = '95%';
        $tscormdata = (array) $scormdata;
        ksort($tscormdata);
        $scormdata = (object) $tscormdata;
        foreach ($scormdata as $question => $info) {
            $qstring = str_replace('_', ' ', $question);
            $questiontotal = $info->correct + $info->wrong;
            $questionright = round(($info->correct / $questiontotal) * 100, 2);
            $questionwrong = 100 - $questionright;
            $scormscoretable->data[$question] = array($qstring,
                                                      $info->type,
                                                      $questiontotal,
                                                      $questionright.'%',
                                                      $questionwrong.'%');
            if (!empty($dodownload)) {
                echo '"'.$string.'","'.
                      $info->type.'","'.
                      $questiontotal.'","'.
                      $questionright.'%","'.
                      $questionwrong."%\"\n";
            }
        }
        if (empty($dodownload)) {
            echo html_writer::table($scormscoretable);
        }
    }
}
if (!empty($dodownload)) {
    die;
}
echo $OUTPUT->footer();
