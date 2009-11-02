<?php  // $Id$
/**
 * Provides the interface for grading essay questions
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    require_once('../../config.php');
    require_once('locallib.php');
    require_once('lib.php');

    $id   = required_param('id', PARAM_INT);             // Course Module ID
    $mode = optional_param('mode', 'display', PARAM_ALPHA);

    list($cm, $course, $lesson) = lesson_get_basics($id);
    
    require_login($course->id, false, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
    require_capability('mod/lesson:edit', $context);
    
/// Handle any preprocessing before header is printed - based on $mode
    switch ($mode) {
        case 'display':  // Default view - get the necessary data
            // Get lesson pages that are essay
            if ($pages = get_records_select('lesson_pages', "lessonid = $lesson->id AND qtype = ".LESSON_ESSAY)) {
                // Get only the attempts that are in response to essay questions
                if ($essayattempts = get_records_select('lesson_attempts', 'pageid IN('.implode(',', array_keys($pages)).')')) {
                    // Get all the users who have taken this lesson, order by their last name
                    if (!empty($CFG->enablegroupings) && !empty($cm->groupingid)) {
                        $sql = "SELECT DISTINCT u.*
                                FROM {$CFG->prefix}lesson_attempts a 
                                    INNER JOIN {$CFG->prefix}user u ON u.id = a.userid
                                    INNER JOIN {$CFG->prefix}groups_members gm ON gm.userid = u.id
                                    INNER JOIN {$CFG->prefix}groupings_groups gg ON gm.groupid = {$cm->groupingid}
                                WHERE a.lessonid = '$lesson->id'
                                ORDER BY u.lastname";
                    } else {
                        $sql = "SELECT u.*
                                FROM {$CFG->prefix}user u,
                                     {$CFG->prefix}lesson_attempts a
                                WHERE a.lessonid = '$lesson->id' and
                                      u.id = a.userid
                                ORDER BY u.lastname";
                    }
                    if (!$users = get_records_sql($sql)) {
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

            if (!$attempt = get_record('lesson_attempts', 'id', $attemptid)) {
                error('Error: could not find attempt');
            }
            if (!$page = get_record('lesson_pages', 'id', $attempt->pageid)) {
                error('Error: could not find lesson page');
            }
            if (!$user = get_record('user', 'id', $attempt->userid)) {
                error('Error: could not find users');
            }
            if (!$answer = get_record('lesson_answers', 'lessonid', $lesson->id, 'pageid', $page->id)) {
                error('Error: could not find answer');
            }
            break;
        case 'update':
            if (confirm_sesskey() and $form = data_submitted($CFG->wwwroot.'/mod/lesson/essay.php')) {
                if (optional_param('cancel', 0)) {
                    redirect("$CFG->wwwroot/mod/lesson/essay.php?id=$cm->id");
                }
                
                $attemptid = required_param('attemptid', PARAM_INT);
                
                if (!$attempt = get_record('lesson_attempts', 'id', $attemptid)) {
                    error('Error: could not find essay');
                }
                if (!$grades = get_records_select('lesson_grades', "lessonid = $lesson->id and userid = $attempt->userid", 'completed', '*', $attempt->retry, 1)) {
                    error('Error: could not find grades');
                }

                $essayinfo = new stdClass;
                $essayinfo = unserialize($attempt->useranswer);

                $essayinfo->graded = 1;
                $essayinfo->score = clean_param($form->score, PARAM_INT);
                $essayinfo->response = stripslashes_safe(clean_param($form->response, PARAM_RAW));
                $essayinfo->sent = 0;
                if (!$lesson->custom && $essayinfo->score == 1) {
                    $attempt->correct = 1;
                } else {
                    $attempt->correct = 0;
                }

                $attempt->useranswer = addslashes(serialize($essayinfo));

                if (!update_record('lesson_attempts', $attempt)) {
                    error('Could not update essay score');
                }
                
                // Get grade information
                $grade = current($grades);
                $gradeinfo = lesson_grade($lesson, $attempt->retry, $attempt->userid);
                
                // Set and update
                $updategrade->id = $grade->id;
                $updategrade->grade = $gradeinfo->grade;
                if(update_record('lesson_grades', $updategrade)) {
                    // Log it
                    add_to_log($course->id, 'lesson', 'update grade', "essay.php?id=$cm->id", $lesson->name, $cm->id);
                    
                    lesson_set_message(get_string('changessaved'), 'notifysuccess');
                } else {
                    lesson_set_message(get_string('updatefailed', 'lesson'));
                }

                // update central gradebook
                lesson_update_grades($lesson, $grade->userid);

                redirect("$CFG->wwwroot/mod/lesson/essay.php?id=$cm->id");
            } else {
                error('Something is wrong with the form data');
            }
            break;
        case 'email': // Sending an email(s) to a single user or all
            require_sesskey();
            
            // Get our users (could be singular)
            if ($userid = optional_param('userid', 0, PARAM_INT)) {
                $queryadd = " AND userid = $userid";
                if (! $users = get_records('user', 'id', $userid)) {
                    error('Error: could not find users');
                }
            } else {
                $queryadd = '';
                if (!$users = get_records_sql("SELECT u.*
                                         FROM {$CFG->prefix}user u,
                                              {$CFG->prefix}lesson_attempts a
                                         WHERE a.lessonid = '$lesson->id' and
                                               u.id = a.userid
                                         ORDER BY u.lastname")) {
                    error('Error: could not find users');
                }
            }

            // Get lesson pages that are essay
            if (!$pages = get_records_select('lesson_pages', "lessonid = $lesson->id AND qtype = ".LESSON_ESSAY)) {
                error('Error: could not find lesson pages');
            }

            // Get only the attempts that are in response to essay questions
            $pageids = implode(',', array_keys($pages)); // all the pageids in comma seperated list
            if (!$attempts = get_records_select('lesson_attempts', "pageid IN($pageids)".$queryadd)) {
                error ('No one has answered essay questions yet...');
            }
            // Get the answers
            if (!$answers = get_records_select('lesson_answers', "lessonid = $lesson->id AND pageid IN($pageids)", '', 'pageid, score')) {
                error ('Could not find answer records.');
            }
            $options = new stdClass;
            $options->noclean = true;
            
            foreach ($attempts as $attempt) {
                $essayinfo = unserialize($attempt->useranswer);
                if ($essayinfo->graded and !$essayinfo->sent) {
                    // Holds values for the essayemailsubject string for the email message
                    $a = new stdClass;
                    
                    // Set the grade
                    $grades = get_records_select('lesson_grades', "lessonid = $lesson->id and userid = $attempt->userid", 'completed', '*', $attempt->retry, 1);
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
                    $a->response = s(stripslashes_safe($essayinfo->answer));
                    $a->teacher  = $course->teacher;
                    $a->comment  = s($essayinfo->response);
                    
                    
                    // Fetch message HTML and plain text formats
                    $message  = get_string('essayemailmessage', 'lesson', $a);
                    $plaintxt = format_text_email($message, FORMAT_HTML);

                    // Subject
                    $subject = get_string('essayemailsubject', 'lesson', format_string($pages[$attempt->pageid]->title,true));

                    if(email_to_user($users[$attempt->userid], $USER, $subject, $plaintxt, $message)) {
                        $essayinfo->sent = 1;
                        $attempt->useranswer = addslashes(serialize($essayinfo));
                        update_record('lesson_attempts', $attempt);
                        // Log it
                        add_to_log($course->id, 'lesson', 'update email essay grade', "essay.php?id=$cm->id", format_string($pages[$attempt->pageid]->title,true).': '.fullname($users[$attempt->userid]), $cm->id);
                    } else {
                        error('Emailing Failed');
                    }
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
            $table = new stdClass;
            $table->head = array($course->students, get_string('essays', 'lesson'), get_string('email', 'lesson'));
            $table->align = array('left', 'left', 'left');
            $table->wrap = array('nowrap', 'nowrap', 'nowrap');

            // Get the student ids of the users who have answered the essay question
            $userids = array_keys($studentessays);

            // Cycle through all the students
            foreach ($userids as $userid) {
                $studentname = fullname($users[$userid], true);
                $essaylinks = array();

                // Number of attempts on the lesson
                $attempts = count_records('lesson_grades', 'userid', $userid, 'lessonid', $lesson->id);

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

                $table->data[] = array(print_user_picture($userid, $course->id, $users[$userid]->picture, 0, true).$studentname, implode("<br />\n", $essaylinks), $emaillink);
            }
            // email link for all users
            $emailalllink = "<a href=\"$CFG->wwwroot/mod/lesson/essay.php?id=$cm->id&amp;mode=email&amp;sesskey=".sesskey().'">'.get_string('emailallgradedessays', 'lesson').'</a>';

            $table->data[] = array(' ', ' ', $emailalllink);
            
            print_table($table);
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
            $table = new stdClass;
            $table->align = array('left');
            $table->wrap = array();
            $table->width = '50%';
            $table->size = array('100%');
            $table->class = 'generaltable gradetable';

            // Print the question
            $table->head = array(get_string('question', 'lesson'));
            $options = new stdClass;
            $options->noclean = true;
            $table->data[] = array(format_text($page->contents, FORMAT_MOODLE, $options));

            print_table($table);

            unset($table->data);
            
            // Now the user's answer
            $essayinfo = unserialize($attempt->useranswer);
            
            $table->head = array(get_string('studentresponse', 'lesson', fullname($user, true)));
            $table->data[] = array(s(stripslashes_safe($essayinfo->answer)));

            print_table($table);

            unset($table->data);

            // Now a response box and grade drop-down for grader
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
            $table->data[] = array(get_string('essayscore', 'lesson').': '.choose_from_menu($options, 'score', $essayinfo->score, '', '', '', true));

            print_table($table);
            echo '<div class="buttons">
                  <input type="submit" name="cancel" value="'.get_string('cancel').'" />
                  <input type="submit" value="'.get_string('savechanges').'" />
                  </div>
                  </form>
                  </div>';
            break;
    }
    
    print_footer($course);
?>