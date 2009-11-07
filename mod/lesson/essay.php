<?php
/**
 * Provides the interface for grading essay questions
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    require_once('../../config.php');
    require_once('locallib.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/eventslib.php');

    $id   = required_param('id', PARAM_INT);             // Course Module ID
    $mode = optional_param('mode', 'display', PARAM_ALPHA);

    list($cm, $course, $lesson) = lesson_get_basics($id);

    require_login($course->id, false, $cm);

    $url = new moodle_url($CFG->wwwroot.'/mod/lesson/essay.php', array('id'=>$id));
    if ($mode !== 'display') {
        $url->param('mode', $mode);
    }
    $PAGE->set_url($url);
    $PAGE->navbar->add(get_string('manualgrading','lesson'));

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);

    require_capability('mod/lesson:edit', $context);

/// Handle any preprocessing before header is printed - based on $mode
    switch ($mode) {
        case 'display':  // Default view - get the necessary data
            // Get lesson pages that are essay
            $params = array ("lessonid" => $lesson->id, "qtype" => LESSON_ESSAY);
            if ($pages = $DB->get_records_select('lesson_pages', "lessonid = :lessonid AND qtype = :qtype", $params)) {
                // Get only the attempts that are in response to essay questions
                list($usql, $parameters) = $DB->get_in_or_equal(array_keys($pages));
                if ($essayattempts = $DB->get_records_select('lesson_attempts', 'pageid $usql', $parameters)) {
                    // Get all the users who have taken this lesson, order by their last name
                    if (!empty($CFG->enablegroupings) && !empty($cm->groupingid)) {
                        $params["groupinid"] = $cm->groupingid;
                        $sql = "SELECT DISTINCT u.*
                                FROM {lesson_attempts} a
                                    INNER JOIN {user} u ON u.id = a.userid
                                    INNER JOIN {groups_members} gm ON gm.userid = u.id
                                    INNER JOIN {groupings_groups} gg ON gm.groupid = :groupinid
                                WHERE a.lessonid = :lessonid
                                ORDER BY u.lastname";
                    } else {
                        $sql = "SELECT u.*
                                FROM {user} u,
                                     {lesson_attempts} a
                                WHERE a.lessonid = :lessonid and
                                      u.id = a.userid
                                ORDER BY u.lastname";
                    }
                    if (!$users = $DB->get_records_sql($sql, $params)) {
                        $mode = 'none'; // not displaying anything
                        lesson_set_message(get_string('noonehasanswered', 'lesson'));
                    }
                } else {
                    $mode = 'none'; // not displaying anything
                    lesson_set_message(get_string('noonehasanswered', 'lesson'));
                }
            } else {
                $mode = 'none'; // not displaying anything
                lesson_set_message(get_string('noessayquestionsfound', 'lesson'));
            }
            break;
        case 'grade':  // Grading form - get the necessary data
            require_sesskey();

            $attemptid = required_param('attemptid', PARAM_INT);

            if (!$attempt = $DB->get_record('lesson_attempts', array('id' => $attemptid))) {
                print_error('cannotfindattempt', 'lesson');
            }
            if (!$page = $DB->get_record('lesson_pages', array('id' => $attempt->pageid))) {
                print_error('cannotfindpages', 'lesson');
            }
            if (!$user = $DB->get_record('user', array('id' => $attempt->userid))) {
                print_error('cannotfinduser', 'lesson');
            }
            if (!$answer = $DB->get_record('lesson_answers', array('lessonid' => $lesson->id, 'pageid' => $page->id))) {
                print_error('cannotfindanswer', 'lesson');
            }
            break;
        case 'update':
            if (confirm_sesskey() and $form = data_submitted()) {
                if (optional_param('cancel', 0, PARAM_RAW)) {
                    redirect("$CFG->wwwroot/mod/lesson/essay.php?id=$cm->id");
                }

                $attemptid = required_param('attemptid', PARAM_INT);

                if (!$attempt = $DB->get_record('lesson_attempts', array('id' => $attemptid))) {
                    print_error('cannotfindattempt', 'lesson');
                }
                $params = array ("lessonid" => $lesson->id, "userid" => $attempt->userid);
                if (!$grades = $DB->get_records_select('lesson_grades', "lessonid = :lessonid and userid = :userid", $params, 'completed', '*', $attempt->retry, 1)) {
                    print_error('cannotfindgrade', 'lesson');
                }

                $essayinfo = new stdClass;
                $essayinfo = unserialize($attempt->useranswer);

                $essayinfo->graded = 1;
                $essayinfo->score = clean_param($form->score, PARAM_INT);
                $essayinfo->response = clean_param($form->response, PARAM_RAW);
                $essayinfo->sent = 0;
                if (!$lesson->custom && $essayinfo->score == 1) {
                    $attempt->correct = 1;
                } else {
                    $attempt->correct = 0;
                }

                $attempt->useranswer = serialize($essayinfo);

                $DB->update_record('lesson_attempts', $attempt);

                // Get grade information
                $grade = current($grades);
                $gradeinfo = lesson_grade($lesson, $attempt->retry, $attempt->userid);

                // Set and update
                $updategrade->id = $grade->id;
                $updategrade->grade = $gradeinfo->grade;
                $DB->update_record('lesson_grades', $updategrade);
                // Log it
                add_to_log($course->id, 'lesson', 'update grade', "essay.php?id=$cm->id", $lesson->name, $cm->id);

                lesson_set_message(get_string('changessaved'), 'notifysuccess');

                // update central gradebook
                lesson_update_grades($lesson, $grade->userid);

                redirect("$CFG->wwwroot/mod/lesson/essay.php?id=$cm->id");
            } else {
                print_error('invalidformdata');
            }
            break;
        case 'email': // Sending an email(s) to a single user or all
            require_sesskey();

            // Get our users (could be singular)
            if ($userid = optional_param('userid', 0, PARAM_INT)) {
                $queryadd = " AND userid = :userid";
                if (! $users = $DB->get_records('user', array('id' => $userid))) {
                    print_error('cannotfinduser', 'lesson');
                }
            } else {
                $queryadd = '';
                $params = array ("lessonid" => $lesson->id);
                if (!$users = $DB->get_records_sql("SELECT u.*
                                         FROM {user} u,
                                              {lesson_attempts} a
                                         WHERE a.lessonid = :lessonid and
                                               u.id = a.userid
                                         ORDER BY u.lastname", $params)) {
                    print_error('cannotfinduser', 'lesson');
                }
            }

            // Get lesson pages that are essay
            $params = array ("lessonid" => $lesson->id, "qtype" => LESSON_ESSAY);
            if (!$pages = $DB->get_records_select('lesson_pages', "lessonid = :lessonid AND qtype = :qtype", $params)) {
                print_error('cannotfindpages', 'lesson');
            }

            // Get only the attempts that are in response to essay questions
            list($usql, $params) = $DB->get_in_or_equal(array_keys($pages));
            if (isset($queryadd) && $queryadd!='') {
                $params["userid"] = $userid;
            }
            if (!$attempts = $DB->get_records_select('lesson_attempts', "pageid $usql".$queryadd, $params)) {
                print_error('nooneansweredthisquestion', 'lesson');
            }
            // Get the answers
            list($answerUsql, $parameters) = $DB->get_in_or_equal(array_keys($pages));
            $parameters["lessonid"] = $lesson->id;
            if (!$answers = $DB->get_records_select('lesson_answers', "lessonid = :lessonid AND pageid $answerUsql", $parameters, '', 'pageid, score')) {
                print_error('cannotfindanswer', 'lesson');
            }
            $options = new stdClass;
            $options->noclean = true;

            foreach ($attempts as $attempt) {
                $essayinfo = unserialize($attempt->useranswer);
                if ($essayinfo->graded and !$essayinfo->sent) {
                    // Holds values for the essayemailsubject string for the email message
                    $a = new stdClass;

                    // Set the grade
                    $params = array ("lessonid" => $lesson->id, "userid" => $attempt->userid);
                    $grades = $DB->get_records_select('lesson_grades', "lessonid = :lessonid and userid = :userid", $params, 'completed', '*', $attempt->retry, 1);
                    $grade  = current($grades);
                    $a->newgrade = $grade->grade;

                    // Set the points
                    if ($lesson->custom) {
                        $a->earned = $essayinfo->score;
                        $a->outof  = $answers[$attempt->pageid]->score;
                    } else {
                        $a->earned = $essayinfo->score;
                        $a->outof  = 1;
                    }

                    // Set rest of the message values
                    $a->question = format_text($pages[$attempt->pageid]->contents, FORMAT_MOODLE, $options);
                    $a->response = s($essayinfo->answer);
                    $a->comment  = s($essayinfo->response);

                    // Fetch message HTML and plain text formats
                    $message  = get_string('essayemailmessage2', 'lesson', $a);
                    $plaintxt = format_text_email($message, FORMAT_HTML);

                    // Subject
                    $subject = get_string('essayemailsubject', 'lesson', format_string($pages[$attempt->pageid]->title,true));

                    $eventdata = new object();
                    $eventdata->modulename       = 'lesson';
                    $eventdata->userfrom         = $USER;
                    $eventdata->userto           = $users[$attempt->userid];
                    $eventdata->subject          = $subject;
                    $eventdata->fullmessage      = $plaintext;
                    $eventdata->fullmessageformat = FORMAT_PLAIN;
                    $eventdata->fullmessagehtml  = $message;
                    $eventdata->smallmessage     = '';
                    message_send($eventdata);
                    $essayinfo->sent = 1;
                    $attempt->useranswer = serialize($essayinfo);
                    $DB->update_record('lesson_attempts', $attempt);
                    // Log it
                    add_to_log($course->id, 'lesson', 'update email essay grade', "essay.php?id=$cm->id", format_string($pages[$attempt->pageid]->title,true).': '.fullname($users[$attempt->userid]), $cm->id);
                }
            }
            lesson_set_message(get_string('emailsuccess', 'lesson'), 'notifysuccess');
            redirect("$CFG->wwwroot/mod/lesson/essay.php?id=$cm->id");
            break;
    }

    // Log it
    add_to_log($course->id, 'lesson', 'view grade', "essay.php?id=$cm->id", get_string('manualgrading', 'lesson'), $cm->id);

    lesson_print_header($cm, $course, $lesson, 'essay');

    switch ($mode) {
        case 'display':
            // Expects $user, $essayattempts and $pages to be set already

            // Group all the essays by userid
            $studentessays = array();
            foreach ($essayattempts as $essay) {
                // Not very nice :) but basically
                //   this organizes the essays so we know how many
                //   times a student answered an essay per try and per page
                $studentessays[$essay->userid][$essay->pageid][$essay->retry][] = $essay;
            }

            // Setup table
            $table = new html_table();
            $table->head = array(get_string('name'), get_string('essays', 'lesson'), get_string('email', 'lesson'));
            $table->align = array('left', 'left', 'left');
            $table->wrap = array('nowrap', 'nowrap', 'nowrap');

            // Get the student ids of the users who have answered the essay question
            $userids = array_keys($studentessays);

            // Cycle through all the students
            foreach ($userids as $userid) {
                $studentname = fullname($users[$userid], true);
                $essaylinks = array();

                // Number of attempts on the lesson
                $attempts = $DB->count_records('lesson_grades', array('userid'=>$userid, 'lessonid'=>$lesson->id));

                // Go through each essay page
                foreach ($studentessays[$userid] as $page => $tries) {
                    $count = 0;

                    // Go through each attempt per page
                    foreach($tries as $try) {
                        if ($count == $attempts) {
                            break;  // Stop displaying essays (attempt not completed)
                        }
                        $count++;

                        // Make sure they didn't answer it more than the max number of attmepts
                        if (count($try) > $lesson->maxattempts) {
                            $essay = $try[$lesson->maxattempts-1];
                        } else {
                            $essay = end($try);
                        }

                        // Start processing the attempt
                        $essayinfo = unserialize($essay->useranswer);

                        // Different colors for all the states of an essay (graded, if sent, not graded)
                        if (!$essayinfo->graded) {
                            $class = ' class="graded"';
                        } elseif (!$essayinfo->sent) {
                            $class = ' class="sent"';
                        } else {
                            $class = ' class="ungraded"';
                        }
                        // link for each essay
                        $essaylinks[] = "<a$class href=\"$CFG->wwwroot/mod/lesson/essay.php?id=$cm->id&amp;mode=grade&amp;attemptid=$essay->id&amp;sesskey=".sesskey().'">'.userdate($essay->timeseen, get_string('strftimedatetime')).' '.format_string($pages[$essay->pageid]->title,true).'</a>';
                    }
                }
                // email link for this user
                $emaillink = "<a href=\"$CFG->wwwroot/mod/lesson/essay.php?id=$cm->id&amp;mode=email&amp;userid=$userid&amp;sesskey=".sesskey().'">'.get_string('emailgradedessays', 'lesson').'</a>';

                $table->data[] = array($OUTPUT->user_picture(moodle_user_picture::make($users[$userid], $course->id)).$studentname, implode("<br />\n", $essaylinks), $emaillink);
            }
            // email link for all users
            $emailalllink = "<a href=\"$CFG->wwwroot/mod/lesson/essay.php?id=$cm->id&amp;mode=email&amp;sesskey=".sesskey().'">'.get_string('emailallgradedessays', 'lesson').'</a>';

            $table->data[] = array(' ', ' ', $emailalllink);

            echo $OUTPUT->table($table);
            break;
        case 'grade':
            // Grading form
            // Expects the following to be set: $attemptid, $answer, $user, $page, $attempt

            echo '<div class="grade">
                  <form id="essaygrade" method="post" action="'.$CFG->wwwroot.'/mod/lesson/essay.php">
                  <input type="hidden" name="id" value="'.$cm->id.'" />
                  <input type="hidden" name="mode" value="update" />
                  <input type="hidden" name="attemptid" value="'.$attemptid.'" />
                  <input type="hidden" name="sesskey" value="'.sesskey().'" />';

            // All tables will have these settings
            $originaltable = new html_table();
            $originaltable->align = array('left');
            $originaltable->wrap = array();
            $originaltable->width = '50%';
            $originaltable->size = array('100%');
            $originaltable->add_class('generaltable gradetable');

            // Print the question
            $table = clone($originaltable);
            $table->head = array(get_string('question', 'lesson'));
            $options = new stdClass;
            $options->noclean = true;
            $table->data[] = array(format_text($page->contents, FORMAT_MOODLE, $options));

            echo $OUTPUT->table($table);

            // Now the user's answer
            $essayinfo = unserialize($attempt->useranswer);

            $table = clone($originaltable);
            $table = new html_table();
            $table->head = array(get_string('studentresponse', 'lesson', fullname($user, true)));
            $table->data[] = array(s($essayinfo->answer));

            echo $OUTPUT->table($table);

            // Now a response box and grade drop-down for grader
            $table = clone($originaltable);
            $table->head = array(get_string('comments', 'lesson'));
            $table->data[] = array(print_textarea(false, 15, 60, 0, 0, 'response', $essayinfo->response, $course->id, true));
            $options = array();
            if ($lesson->custom) {
                for ($i=$answer->score; $i>=0; $i--) {
                    $options[$i] = $i;
                }
            } else {
                $options[0] = get_string('nocredit', 'lesson');
                $options[1] = get_string('credit', 'lesson');
            }
            $select = html_select::make($options, 'score', $essayinfo->score, false);
            $select->nothingvalue = '';
            $table->data[] = array(get_string('essayscore', 'lesson').': '.$OUTPUT->select($select));

            echo $OUTPUT->table($table);
            echo '<div class="buttons">
                  <input type="submit" name="cancel" value="'.get_string('cancel').'" />
                  <input type="submit" value="'.get_string('savechanges').'" />
                  </div>
                  </form>
                  </div>';
            break;
    }

    echo $OUTPUT->footer();

