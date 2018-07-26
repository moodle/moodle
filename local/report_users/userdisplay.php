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

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/blocks/iomad_company_admin/lib.php');
require_once(dirname(__FILE__).'/lib.php');

// Params.
$courseid = optional_param('courseid', 0, PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$dodownload = optional_param('dodownload', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_CLEAN);
$confirm = optional_param('confirm', 0, PARAM_INT);
$showhistoric = optional_param('showhistoric', 1, PARAM_BOOL);

// Check permissions.
require_login();
$context = context_system::instance();
iomad::require_capability('local/report_users:view', $context);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

$linktext = get_string('user_detail_title', 'local_report_users');
// Set the url.
$linkurl = new moodle_url('/local/report_users/index.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('pluginname', 'block_iomad_reports') . " - $linktext");

// Build the nav bar.
company_admin_fix_breadcrumb($PAGE, $linktext, $linkurl);

$baseurl = new moodle_url(basename(__FILE__));
$returnurl = $baseurl;
if (empty($dodownload)) {
    echo $OUTPUT->header();

    // Check the userid is valid.
    if (!company::check_valid_user($companyid, $userid)) {
        print_error('invaliduser', 'block_iomad_company_management');
    }
} else {
    // Check the userid is valid.
    if (!company::check_valid_user($companyid, $userid)) {
        print_error('invaliduser', 'block_iomad_company_management');
    }
}


// Get this list of courses the user is a member of.
$enrolcourses = enrol_get_users_courses($userid, true, null, 'visible DESC, sortorder ASC');
if ($showhistoric) {
    $completioncourses = $DB->get_records_sql("SELECT distinct courseid as id FROM {local_iomad_track} 
                                               WHERE userid = :userid", array('userid' => $userid));
} else {
    $completioncourses = array();
}

// Get non started courses.
$licensecourses = $DB->get_records_sql("SELECT distinct licensecourseid as id FROM {companylicense_users} 
                                               WHERE userid = :userid AND isusing = 0", array('userid' => $userid));
$rawusercourses = array();
// We only want student roles here.
$studentrole = $DB->get_record('role', array('shortname' => 'student'));
foreach ($enrolcourses as $enrolcourse) {
    $roles = get_user_roles(context_course::instance($enrolcourse->id), $userid, false);
    foreach ($roles as $role) {
        if ($role->roleid == $studentrole->id) {

            $rawusercourses[$enrolcourse->id] = $enrolcourse;
        }
    }
}
foreach ($completioncourses as $completioncourse) {
    $rawusercourses[$completioncourse->id] = $completioncourse;
}
foreach ($licensecourses as $licensecourse) {
    $rawusercourses[$licensecourse->id] = $licensecourse;
}

// Sort them by name.
if (!empty($rawusercourses)) {
    $usercourses = $DB->get_records_sql("SELECT id FROM {course}
                                         WHERE id IN (" . implode(',', array_keys($rawusercourses)) . ")
                                         ORDER BY fullname");
                                         
} else {
    $usercourses = array();
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

// Check for user/course delete?
if ($delete) {
    $confirm = new moodle_url('/local/report_users/userdisplay.php', array(
        'userid' => $userid,
        'confirm' => $delete,
        'courseid' => $courseid,
        'action' => $action
        ));
    $cancel = new moodle_url('/local/report_users/userdisplay.php', array(
        'userid' => $userid));
    if ($action == 'delete') {
        echo $OUTPUT->confirm(get_string('deleteconfirm', 'local_report_users'), $confirm, $cancel);
    } else if ($action == 'clear') {
        echo $OUTPUT->confirm(get_string('clearconfirm', 'local_report_users'), $confirm, $cancel);
    }
    echo $OUTPUT->footer();
    die;
}

// Check for confirmed delete?
if ($confirm) {
   company_user::delete_user_course($userid, $courseid, $action);
   redirect(new moodle_url('/local/report_users/userdisplay.php', array(
        'userid' => $userid)));
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
                                                                            'showhistoric' => $showhistoric,
                                                                            'page' => $page,
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
                             get_string('timeexpires', 'local_report_completion'),
                             get_string('finalscore', 'local_report_completion'));
$compusertable->align = array('left', 'center', 'center', 'center', 'center', 'center');
$compusertable->width = '95%';
//if ($hasiomadcertificate) {
//    $compusertable->head[] = get_string('certificate', 'local_report_users');
//    $compusertable->align[] = 'center';
//}
$compusertable->head[] = get_string('actions', 'local_report_users');
$compusertable->align[] = 'center';

// Set that there is nothing found here first.
$results = false;

foreach ($usercourses as $usercourse) {
    if ($usercompletion[$usercourse->id] = userrep::get_completion($userid, $usercourse->id, $showhistoric) ) {
        $results = true;
        $usercourseid = $usercourse->id;

        // Check if the course is also in progress.

        if (empty($usercompletion[$usercourse->id]->data[$usercourseid]->completion)) {
            continue;
        }
        foreach ($usercompletion[$usercourse->id]->data[$usercourseid]->completion as $usercompcourse) {

            // Get the completion summary.
            $coursestring = '<div class="usercompcoursename"><b>'.$usercompletion[$usercourse->id]->data[$usercourseid]->coursename.'</b></div>'.
                            "</br><div class='usercompcourselink'><a href='".
                            new moodle_url("/local/report_users/userdisplay.php",
                                            array('userid' => $userid,
                                                  'courseid' => $usercourseid,
                                                  'showhistoric' => $showhistoric)).
                             "'>(".get_string('usercoursedetails', 'local_report_users').
                             ")</a></div><div class='usercompdetailslink'><a href='".
                             new moodle_url("/local/report_completion/index.php",
                                              array('courseid' => $usercourseid, 'showhistoric' => $showhistoric)).
                             "'>(".get_string('coursedetails', 'local_report_users').
                             ")</a></div>";
            if (!empty($usercompcourse->status)) {
                $userstat = $usercompcourse->status;
                $statusstring = get_string($userstat, 'local_report_users');
            } else {
                $statusstring = get_string('notstarted', 'local_report_users');
            }
    
            // Get the score for the course.
            if (!empty($usercompcourse->result)) {
                $resultstring = $usercompcourse->result.
                                "%";
            } else {
                $resultstring = "0%";
            }
            // Set the strings.
            if (!empty($usercompcourse->timestarted)) {
                $starttime = date($CFG->iomad_date_format, $usercompcourse->timestarted);
            } else {
                $starttime = "";
            }
            if (!empty($usercompcourse->timeenrolled)) {
                $enrolledtime = date($CFG->iomad_date_format, $usercompcourse->timeenrolled);
            } else {
                $enrolledtime = "";
            }
            $expiretime = "";
            if (!empty($usercompcourse->timecompleted)) {
                $completetime = date($CFG->iomad_date_format, $usercompcourse->timecompleted);
                if ($iomadcourserec = $DB->get_record('iomad_courses', array('courseid' => $usercourseid))) {
                    if (!empty($iomadcourserec->validlength)) {
                        $expiretime = date($CFG->iomad_date_format, $usercompcourse->timecompleted + $iomadcourserec->validlength * 24 * 60 * 60);
                    }
                }
            } else {
                $completetime = "";
            }

            // Link for user delete (TODO: this all needs refactored)
            $dellink = new moodle_url('/local/report_users/userdisplay.php', array(
                    'userid' => $userid,
                    'delete' => $userid,
                    'courseid' => $usercourseid,
                    'action' => 'delete'
                ));
            $clearlink = new moodle_url('/local/report_users/userdisplay.php', array(
                    'userid' => $userid,
                    'delete' => $userid,
                    'courseid' => $usercourseid,
                    'action' => 'clear'
                ));
            if (empty($usercompcourse->certsource) && has_capability('block/iomad_company_admin:editusers', $context)) {
                // Its from the course_completions table.  Check the license type.
                if ($DB->get_record_sql("SELECT cl.* FROM {companylicense} cl
                                         JOIN {companylicense_users} clu
                                         ON (cl.id = clu.licenseid)
                                         WHERE cl.program = 1
                                         AND clu.userid = :userid
                                         AND clu.licensecourseid = :courseid",
                                         array('userid' => $userid,
                                               'courseid' => $usercourseid))) {
                    $delaction = '<a class="btn btn-danger" href="'.$clearlink.'">' . get_string('clear', 'local_report_users') . '</a>';
                } else {
                    $delaction = '<a class="btn btn-danger" href="'.$dellink.'">' . get_string('delete', 'local_report_users') . '</a>' .
                                 '<a class="btn btn-danger" href="'.$clearlink.'">' . get_string('clear', 'local_report_users') . '</a>';
                }
            } else {
                $delaction = '';
            }

            // Deal with the iomadcertificate info.
            if ($hasiomadcertificate) {
                if ($iomadcertificateinfo = $DB->get_record('iomadcertificate',
                                                        array('course' => $usercourseid))) {
                    // Check if user has completed the course - if so, show the iomadcertificate.
                    $compstat = $usercompcourse->status;
                    if ($compstat == 'completed' ) {
                        if (empty($usercompcourse->certsource) ) {
                            // Get the course module.
                            $certcminfo = $DB->get_record('course_modules',
                                                           array('course' => $usercourseid,
                                                                 'instance' => $iomadcertificateinfo->id,
                                                                 'module' => $certmodule->id));
                            $certstring = "<a class=\"btn btn-info\" href='".$CFG->wwwroot."/mod/iomadcertificate/view.php?id=".
                                          $certcminfo->id."&action=get&userid=".$userid."&sesskey=".
                                          sesskey()."'>".get_string('certificate', 'local_report_users').
                                          "</a>";
                        } else {
                            // Get the certificate from the download files thing.
                            if ($traccertrec = $DB->get_record('local_iomad_track_certs', array('trackid' => $usercompcourse->certsource))) {
                                // create the file download link.
                                $coursecontext = context_course::instance($usercourseid);
                                $certstring = "<a class=\"btn btn-info\" href='".
                                               moodle_url::make_file_url('/pluginfile.php', '/'.$coursecontext->id.'/local_iomad_track/issue/'.$traccertrec->trackid.'/'.$traccertrec->filename) .
                                              "'>" . get_string('certificate', 'local_report_users').
                                              "</a>";
                            }
                        }
                    } else {
                        $certstring = get_string('nocerttodownload', 'local_report_users');
                    }
                } else {
                    $certstring = "";
                }
                $compusertable->data[] = array($coursestring,
                                               $statusstring,
                                               $enrolledtime,
                                               $starttime,
                                               $completetime,
                                               $expiretime,
                                               $resultstring,
                                               $certstring . '&nbsp;' . $delaction);
            } else {
                $compusertable->data[] = array($coursestring,
                                               $statusstring,
                                               $enrolledtime,
                                               $starttime,
                                               $completetime,
                                               $expiretime,
                                               $resultstring,
                                               $delaction);
            }
        }
    }
}
if (empty($dodownload)) {
    if (!$showhistoric) {
        $historicuserslink = new moodle_url($baseurl, array('courseid' => $courseid,
                                                        'userid' => $userid,
                                                        'page' => $page,
                                                        'showhistoric' => 1
                                                        ));
        echo $OUTPUT->single_button($historicuserslink, get_string("historicusers", 'local_report_completion'));
    } else {
        $historicuserslink = new moodle_url($baseurl, array('courseid' => $courseid,
                                                        'userid' => $userid,
                                                        'page' => $page,
                                                        'showhistoric' => 0
                                                        ));
        echo $OUTPUT->single_button($historicuserslink, get_string("hidehistoricusers", 'local_report_completion'));
    }

    // If we have anything show it.
    if ($results) {
        echo html_writer::table($compusertable);
    } else {
        echo "</br><b>" . get_string('noresults') . "</b>";
    }
}

if (!empty($courseid) && !empty($usercompletion[$courseid])) {
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
            $scormdata = new stdclass();
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
                         date($CFG->iomad_date_format .' h:i:s A', $scormdata->starttime)."</br>";
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
                             date($CFG->iomad_date_format . ' h:i:s A', $scormdata->starttime).'",';
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
