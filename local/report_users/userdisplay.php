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
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');
require_once('lib.php');

// Params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$dodownload = optional_param('dodownload', 0, PARAM_INT);

// Check permissions.
require_login($SITE);
$context = context_system::instance();
iomad::require_capability('local/report_completion:view', $context);

$linktext = get_string('user_detail_title', 'local_report_users');
// Set the url.
$linkurl = new moodle_url('/local/report_users/index.php');
// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

// Print the page header.
$blockpage = new blockpage($PAGE, $OUTPUT, 'report_users', 'local', 'report_users_title');
$blockpage->setup();

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.

$baseurl = new moodle_url(basename(__FILE__));
$returnurl = $baseurl;
if (empty($dodownload)) {
    $blockpage->display_header();
}

// Get this list of courses the user is a member of.
$enrolcourses = enrol_get_users_courses($userid, true, null, 'visible DESC, sortorder ASC');
$completioncourses = $DB->get_records_sql("SELECT course as id FROM {course_completions} 
                                           WHERE userid = :userid", array('userid' => $userid));
$usercourses = array();
foreach ($enrolcourses as $enrolcourse) {
    $usercourses[$enrolcourse->id] = $enrolcourse;
}
foreach ($completioncourses as $completioncourse) {
    $usercourses[$completioncourse->id] = $completioncourse;
}

// Get the Users details.
$userinfo = $DB->get_record('user', array('id' => $userid));

// Check if there is a iomadcertificate module.
if ($certmodule = $DB->get_record('modules', array('name' => 'iomadcertificate'))) {
    $hasiomadcertificate = true;
    require_once($CFG->dirroot.'/mod/iomadcertificate/lib.php');
} else {
    $hasiomadcertificate = false;
}

if (!empty($dodownload)) {
    // Set up the Excel workbook.
    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=\"userreport.csv\"");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");
}

if (empty($dodownload)) {
    echo "<h2>".get_string('userdetails', 'local_report_users').
          $userinfo->firstname." ".
          $userinfo->lastname. " (".$userinfo->email.")";
          if (!empty($userinfo->suspended)) {
              echo " - Suspended</h2>";
          } else {
              echo "</h2>";
          }
    if (!empty($courseid)) {
        // Navigation and header.
        echo $OUTPUT->single_button(new moodle_url('userdisplay.php', array('courseid' => $courseid,
                                                                            'userid' => $userid,
                                                                            'dodownload' => '1')),
                                    get_string("downloadcsv", 'local_report_completion'));
    }
}
// Table for results.
$compusertable = new html_table();
$compusertable->head = array(get_string('course', 'local_report_completion'),
                             get_string('status', 'local_report_completion'),
                             get_string('dateenrolled', 'local_report_completion'),
                             get_string('datestarted', 'local_report_completion'),
                             get_string('datecompleted', 'local_report_completion'),
                             get_string('finalscore', 'local_report_completion'));
$compusertable->align = array('left', 'center', 'center', 'center', 'center', 'center');
$compusertable->width = '95%';
if ($hasiomadcertificate) {
    $compusertable->head[] = get_string('certificate', 'local_report_users');
    $compusertable->align[] = 'center';
}

foreach ($usercourses as $usercourse) {
    if ($usercompletion[$usercourse->id] = userrep::get_completion($userid, $usercourse->id) ) {
        $usercourseid = $usercourse->id;

        // Get the completion summary.
        $completionsummary = $DB->get_record('course_completions', array('userid' => $userid,
                                                                         'course' => $usercourseid));
        $coursestring = $usercompletion[$usercourse->id]->data[$usercourseid]->coursename.
                        "</br><a href='".
                        new moodle_url("/local/report_users/userdisplay.php",
                                        array('userid' => $userid,
                                              'courseid' => $usercourseid)).
                         "'>(".get_string('usercoursedetails', 'local_report_users').
                         ")</a> <a href='".
                         new moodle_url("/local/report_completion/index.php",
                                          array('courseid' => $usercourseid)).
                         "'>(".get_string('coursedetails', 'local_report_users').
                         ")</a>";
        if (!empty($usercompletion[$usercourse->id]->data[$usercourseid]->completion->status)) {
            $userstat = $usercompletion[$usercourse->id]->data[$usercourseid]->completion->status;
            $statusstring = get_string($userstat, 'local_report_users');
        } else {
            $statusstring = get_string('notstarted', 'local_report_users');
        }

        // Get the score for the course.
        if (!empty($usercompletion[$usercourseid]->data[$usercourseid]->completion->result)) {
            $resultstring = $usercompletion[$usercourseid]->data[$usercourseid]->completion->result.
                            "%";
        } else {
            $resultstring = "0%";
        }
        // Set the strings.
        if (!empty($completionsummary->timestarted)) {
            $starttime = date('d M Y', $completionsummary->timestarted);
        } else {
            $starttime = "";
        }
        if (!empty($completionsummary->timeenrolled)) {
            $enrolledtime = date('d M Y', $completionsummary->timeenrolled);
        } else {
            $enrolledtime = "";
        }
        if (!empty($completionsummary->timecompleted)) {
            $completetime = date('d M Y', $completionsummary->timecompleted);
        } else {
            $completetime = "";
        }

        // Deal with the iomadcertificate info.
        if ($hasiomadcertificate) {
            if ($iomadcertificateinfo = $DB->get_record('iomadcertificate',
                                                    array('course' => $usercourseid))) {
                // Check if user has completed the course - if so, show the iomadcertificate.
                $compstat = $usercompletion[$usercourseid]->data[$usercourseid]->completion->status;
                if ($compstat == 'completed' ) {
                    // Get the course module.
                    $certcminfo = $DB->get_record('course_modules',
                                                   array('course' => $usercourseid,
                                                         'instance' => $iomadcertificateinfo->id,
                                                         'module' => $certmodule->id));
                    $certstring = "<a href='".$CFG->wwwroot."/mod/iomadcertificate/view.php?id=".
                                  $certcminfo->id."&action=get&userid=".$userid."&sesskey=".
                                  sesskey()."'>".get_string('downloadcert', 'local_report_users').
                                  "</a>";
                } else {
                    $certstring = get_string('nocerttodownload', 'local_report_users');
                }
            } else {
                $certstring = get_string('nocerttodownload', 'local_report_users');
            }
            $compusertable->data[] = array($coursestring,
                                           $statusstring,
                                           $enrolledtime,
                                           $starttime,
                                           $completetime,
                                           $resultstring,
                                           $certstring);
        } else {
            $compusertable->data[] = array($coursestring,
                                           $statusstring,
                                           $enrolledtime,
                                           $starttime,
                                           $completetime,
                                           $resultstring);
        }
    }
}
if (empty($dodownload)) {
    echo html_writer::table($compusertable);
}

if (!empty($courseid)) {
    if (empty($dodownload)) {
        echo "<h3>".$usercompletion[$courseid]->data[$courseid]->coursename.
             " (<a href='".
             new moodle_url('/local/report_completion/index.php', array('courseid' => $courseid)).
             "'> ".get_string('viewfullcourse', 'local_report_users')."</a>)</h3>";
    } else {
        echo '"'.$usercompletion[$courseid]->data[$courseid]->coursename."\"\n";
    }
    // Show some extra details.
    $extradetail = false;
    foreach ($usercompletion[$courseid]->criteria as $criteria) {
        if ($criteria->module == "quiz") {
            $extradetail = true;
            require_once($CFG->dirroot . '/mod/quiz/locallib.php');
            $quizinfo = array_pop($DB->get_records_sql("SELECT q.*
                                                        FROM {quiz} q, {course_modules} cm
                                                        WHERE cm.id=".$criteria->moduleinstance."
                                                        AND cm.instance=q.id"));
            if ($attemptids = $DB->get_records_sql("SELECT qa.id
                                                    FROM {quiz_attempts} qa, {course_modules} cm
                                                    WHERE cm.instance = qa.quiz
                                                    AND qa.userid=$userid
                                                    AND cm.id=".$criteria->moduleinstance)) {
                // Get the last attempt.
                $attemptid = array_pop($attemptids);
                $attemptobj = quiz_attempt::create($attemptid->id);
                $questionids = $attemptobj->get_question_ids($page);
                $attemptobj->load_questions($questionids);
                $attemptobj->load_question_states($questionids);
                $thispage = $page;
                $lastpage = $attemptobj->is_last_page($page);
                if (empty($dodownload)) {
                    echo "<h3>".$quizinfo->name ."</h3>";
                    foreach ($attemptobj->get_question_ids($thispage) as $id) {
                        $attemptobj->print_question($id, true,
                                                     $attemptobj->review_url($id, $page, 'false'));
                    }
                }
            }
        }
        if ($criteria->module == "scorm" ) {
            $extradetail = true;
            $instanceinfo = $DB->get_record('course_modules',
                                             array('id' => $criteria->moduleinstance));
            $scorminfo = $DB->get_record('scorm', array('id' => $instanceinfo->instance));
            $sql = "SELECT element, value, timemodified, attempt from {scorm_scoes_track}
                    WHERE userid = $userid AND scormid = ". $instanceinfo->instance ."
                    AND attempt = (SELECT MAX(attempt) from {scorm_scoes_track}
                    WHERE userid = $userid AND scormid = ". $instanceinfo->instance .")";
            $scormdata = new object();
            $numattempts = 0;
            if ($scormattempt = $DB->get_records_sql($sql)) {
                foreach ($scormattempt as $data) {
                    // Recrod the number of Moodle attemps.
                    if (empty($scormdata->numattempts)) {
                        $scormdata->numattempts = $data->attempt;
                    }
                    if ($data->element == "x.start.time") {
                        $scormdata->starttime = $data->value;
                    } else if ($data->element == "cmi.core.lesson_status") {
                        $scormdata->result = $data->value;
                    } else if ($data->element == "cmi.core.score.raw") {
                        $scormdata->score = $data->value;
                    } else if ($data->element == "cmi.core.score.min") {
                        $scormdata->minscore = $data->value;
                    } else if ($data->element == "cmi.core.score.max") {
                        $scormdata->maxscore = $data->value;
                    } else if ($data->element == "cmi.core.exit") {
                        continue;
                    } else if ($data->element == "cmi.core.total_time") {
                        continue;
                    } else if ($data->element == "cmi.core.lesson_location") {
                        continue;
                    } else if ($data->element == "cmi.comments") {
                        continue;
                    } else if (strpos($data->element, "student_response")) {
                        continue;
                    } else if (strpos($data->element, "correct_responses")) {
                        continue;
                    } else {
                        // Check we have interaction_ and not interaction.
                        if (strpos($data->element, 'interactions_')) {
                            list($cmi, $elementraw, $target) = explode('.', $data->element);
                            list($element, $number) = explode('_', $elementraw);
                        } else {
                            list($cmi, $element, $number, $target) = explode('.', $data->element);
                        }
                        $scormdata->$element->$number->$target = $data->value;
                    }
                }
                $numattempts = $scormdata->numattempts;
            }
            if (empty($dodownload)) {
                echo "<h3>".$scorminfo->name."</h3>";
                if (!empty($scormdata->starttime)) {
                    echo get_string('scormtimestarted', 'local_report_users')." " .
                         date('jS \of F Y h:i:s A', $scormdata->starttime)."</br>";
                    echo get_string('scormattempts', 'local_report_users')." ".$numattempts."</br>";
                    if (!empty($scormdata->score)&&!empty($scormdata->maxscore)) {
                        echo get_string('scormscore', 'local_report_users')." ".
                             $scormdata->score."/".$scormdata->maxscore."</br>";
                    } else {
                        echo get_string('scormscore', 'local_report_users')."</br>";
                    }
                    if (!empty($scormdata->result)) {
                        echo get_string('scormresults', 'local_report_users')." ".
                              $scormdata->result."</br>";
                    } else {
                        echo get_string('scormresults', 'local_report_users')."</br>";
                    }
                } else {
                    echo get_string('scormnotstarted', 'local_report_users')."</br>";
                }
            } else {
                echo '"'.$scorminfo->name.'",';
                if (!empty($scormdata->starttime)) {
                    echo '"'.get_string('scormtimestarted', 'local_report_users')." " .
                             date('jS \of F Y h:i:s A', $scormdata->starttime).'",';
                    echo '"'.get_string('scormattempts', 'local_report_users')." ".
                          $numattempts.'",';
                    if (!empty($scormdata->score)&& !empty($scormdata->maxscore)) {
                        echo '"'.get_string('scormscore', 'local_report_users')." ".
                              $scormdata->score."/".$scormdata->maxscore.'",';
                    } else {
                        echo '"'.get_string('scormscore', 'local_report_users').'",';
                    }
                    echo '"'.get_string('scormresults', 'local_report_users')." ".
                          $scormdata->result."\"\n\n";
                } else {
                    echo get_string('scormnotstarted', 'local_report_users')."\n";
                }
            }
            $scormtable = new html_table ();
            if (!empty($dodownload)) {
                echo '"'.get_string('scormquestion', 'local_report_users').'","'.
                         get_string('scormtype', 'local_report_users').'","'.
                         get_string('scormresult', 'local_report_users')."\"\n";
            }
            $scormtable->head = array(get_string('scormquestion', 'local_report_users'),
                                      get_string('scormtype', 'local_report_users'),
                                      get_string('scormresult', 'local_report_users'));
            $scormtable->align = array('left', 'center', 'center');
            $scormtable->width = '50%';
            if (!empty($scormdata->interactions)) {
                foreach ($scormdata->interactions as $interaction) {
                    if (!empty($dodownload)) {
                        echo '"'.$interaction->id.'","'. $interaction->type.'","'.
                              $interaction->result."\"\n";
                    }
                    $scormtable->data[] = array($interaction->id,
                                                $interaction->type,
                                                $interaction->result);
                }
            }
            if (empty($dodownload)) {
                echo html_writer::table($scormtable);
            }
        }
    }
    if (!$extradetail && empty($dodownload)) {
        echo "<h2>" . get_string('nofurtherdetail', 'local_report_users') . "</h2>";
    }

}
if (!empty($dodownload)) {
    exit;
}
echo $OUTPUT->footer();
