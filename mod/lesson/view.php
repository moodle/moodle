<?PHP  // $Id$

/// This page prints a particular instance of lesson
/// (Replace lesson with the name of your module)

    require_once('../../config.php');
    require_once('locallib.php');
    require_once('lib.php');

    $id      = required_param('id', PARAM_INT);             // Course Module ID
    $pageid  = optional_param('pageid', NULL, PARAM_INT);   // Lesson Page ID
    $action  = optional_param('action', '', PARAM_ALPHA);
    $display = optional_param('display', 0, PARAM_INT);     // for teacherview action
    $mode    = optional_param('mode', '', PARAM_ALPHA);     // for eacherview action todo use user pref
    

    if (! $cm = get_coursemodule_from_id('lesson', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $lesson = get_record('lesson', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }

    require_login($course->id, false, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    require_capability('mod/lesson:view', $context);

/// Print the page header

    if ($course->category) {
        $navigation = '<a href="../../course/view.php?id='. $course->id .'">'. $course->shortname .'</a> ->';
    } else {
        $navigation = '';
    }

    $strlessons = get_string('modulenameplural', 'lesson');
    $strlesson  = get_string('modulename', 'lesson');
    
    // moved the action up because I needed to know what the action will be before the header is printed
    if (empty($action)) {
        if (has_capability('mod/lesson:manage', $context)) {
            $action = 'teacherview';
        } elseif  (time() < $lesson->available) {
            print_header($course->shortname .': '. format_string($lesson->name), $course->fullname,
                         $navigation .'<a href="index.php?id='. $course->id .'">'. $strlessons .'</a> -> '.
                         '<a href="view.php?id='. $cm->id .'">'. format_string($lesson->name,true) .'</a>', 
                         '', '', true, '', navmenu($course, $cm));
            print_simple_box_start('center');
            echo '<div align="center">';
            echo get_string('lessonopen', 'lesson', userdate($lesson->available)).'<br />';
            echo '<div class="lessonbutton standardbutton" style="padding: 5px;"><a href="../../course/view.php?id='. $course->id .'">'. get_string('returnmainmenu', 'lesson') .'</a></div>';
            echo '</div>';
            print_simple_box_end();
            print_footer($course);
            exit();
        } elseif (time() > $lesson->deadline) {
            print_header($course->shortname .': '. format_string($lesson->name), $course->fullname,
                         "$navigation <a href=\"index.php?id=$course->id\">$strlessons</a> -> <a href=\"view.php?id=$cm->id\">".format_string($lesson->name,true)."</a>", '', "", true,
                          '', navmenu($course, $cm));
            print_simple_box_start('center');
            echo '<div align="center">';
            echo get_string('lessonclosed', 'lesson', userdate($lesson->deadline)) .'<br />';
            echo '<div class="lessonbutton standardbutton" style="padding: 5px;"><a href="../../course/view.php?id='. $course->id. '">'. get_string('returnmainmenu', 'lesson') .'</a></div>';
            echo '</div>';
            print_simple_box_end();
            print_footer($course);
            exit();
        } elseif ($lesson->highscores && !$lesson->practice) {
            $action = 'highscores';
        } else {
            $action = 'navigation';
        }
    } 

    // changed the update_module_button and added another button when a teacher is checking the navigation of the lesson
    if (has_capability('mod/lesson:edit', $context)) {
        $button = '<table><tr><td>';
        $button .= '<form target="'. $CFG->framename .'" method="get" action="'. $CFG->wwwroot .'/course/mod.php">'.
               '<input type="hidden" name="sesskey" value="'. $USER->sesskey .'" />'.
               '<input type="hidden" name="update" value="'. $cm->id .'" />'.
               '<input type="hidden" name="return" value="true" />'.
               '<input type="submit" value="'. get_string('editlessonsettings', 'lesson') .'" /></form>';
        if ($action == 'navigation' && $pageid != LESSON_EOL) {
            $currentpageid = $pageid;  // very important not to alter $pageid.
            if (empty($currentpageid)) {
                $currentpageid = get_field('lesson_pages', 'id', 'lessonid', $lesson->id, 'prevpageid', 0);
            }
            if (!empty($currentpageid)) {  // if still empty, then something is wrong
                $button .= '</td><td>'.
                       '<form target="'. $CFG->framename .'" method="get" action="'. $CFG->wwwroot .'/mod/lesson/lesson.php">'.
                       '<input type="hidden" name="id" value="'. $cm->id .'" />'.
                       '<input type="hidden" name="action" value="editpage" />'.
                       '<input type="hidden" name="redirect" value="navigation" />'.
                       '<input type="hidden" name="pageid" value="'. $currentpageid .'" />'.
                       '<input type="submit" value="'. get_string('editpagecontent', 'lesson') .'" /></form>';
            }
        }
        $button .= '</td></tr></table>';
    } else {
        $button = '';
    }

    print_header($course->shortname .': '. format_string($lesson->name), $course->fullname,
                 "$navigation <a href=\"index.php?id=$course->id\">$strlessons</a> -> <a href=\"view.php?id=$cm->id\">".format_string($lesson->name,true)."</a>", '', '', true,
                 $button, // took out update_module_button($cm->id, $course->id, $strlesson) and replaced it with $button
                  navmenu($course, $cm));

    if (has_capability('mod/lesson:manage', $context)) {
        
        if ($action == 'teacherview' and $display) {
            // teacherview tab not selected when displaying a single page/question
            $currenttab = '';
        } else {
            $currenttab = $action;
        }
        include('tabs.php');
    }
    
    // set up some general variables
    $usehtmleditor = can_use_html_editor();
    $path = $CFG->wwwroot .'/course';

    /************** navigation **************************************/
    if ($action == 'navigation') {
        // password protected lesson code
        if ($lesson->usepassword && !has_capability('mod/lesson:manage', $context)) {
            $correctpass = false;
            if ($password = optional_param('userpassword', '', PARAM_CLEAN)) {
                if ($lesson->password == md5(trim($password))) {
                    $USER->lessonloggedin[$lesson->id] = true;
                    $correctpass = true;
                }
            } elseif (isset($USER->lessonloggedin[$lesson->id])) {
                $correctpass = true;
            }

            if (!$correctpass) {
                echo "<div class=\"password-form\">\n";
                print_simple_box_start('center');
                echo '<form name="password" method="post" action="view.php">' . "\n";
                echo '<input type="hidden" name="id" value="'. $cm->id .'" />' . "\n";
                echo '<input type="hidden" name="action" value="navigation" />' . "\n";
                if (optional_param('userpassword', 0, PARAM_CLEAN)) {
                    notify(get_string('loginfail', 'lesson'));
                }
                
                echo get_string('passwordprotectedlesson', 'lesson', format_string($lesson->name))."<br /><br />\n".
                     get_string('enterpassword', 'lesson')." <input type=\"password\" name=\"userpassword\" /><br /><br />\n".
                     '<span class="lessonbutton standardbutton"><a href="'.$CFG->wwwroot.'/course/view.php?id='. $course->id .'">'. get_string('cancel', 'lesson') .'</a></span> ';
                
                lesson_print_submit_link(get_string('continue', 'lesson'), 'password', 'center', 'standardbutton submitbutton');
                print_simple_box_end();
                echo "</div>\n";
                exit();
            }
        }
    
        // this is called if a student leaves during a lesson
        if($pageid == LESSON_UNSEENBRANCHPAGE) {
                $pageid = lesson_unseen_question_jump($lesson->id, $USER->id, $pageid);
        }
        
        // display individual pages and their sets of answers
        // if pageid is EOL then the end of the lesson has been reached
               // for flow, changed to simple echo for flow styles, michaelp, moved lesson name and page title down
       $timedflag = false;
       $attemptflag = false;
        if (empty($pageid)) {
            // make sure there are pages to view
            if (!get_field('lesson_pages', 'id', 'lessonid', $lesson->id, 'prevpageid', 0)) {
                if (!has_capability('mod/lesson:manage', $context)) {
                    notify(get_string('lessonnotready', 'lesson', $course->teacher)); // a nice message to the student
                } else {
                    if (!count_records('lesson_pages', 'lessonid', $lesson->id)) {
                        redirect('view.php?id='.$cm->id); // no pages - redirect to add pages
                    } else {
                        notify(get_string('lessonpagelinkingbroken', 'lesson'));  // ok, bad mojo
                    }
                }
                print_footer($course);
                exit();
            }
            
            // check for dependencies
            if ($lesson->dependency and !has_capability('mod/lesson:manage', $context)) {
                if ($dependentlesson = get_record('lesson', 'id', $lesson->dependency)) {
                    // lesson exists, so we can proceed            
                    $conditions = unserialize($lesson->conditions);
                    // assume false for all
                    $timespent = false;
                    $completed = false;
                    $gradebetterthan = false;
                    // check for the timespent condition
                    if ($conditions->timespent) {
                        if ($attempttimes = get_records_select('lesson_timer', "userid = $USER->id AND lessonid = $dependentlesson->id")) {
                            // go through all the times and test to see if any of them satisfy the condition
                            foreach($attempttimes as $attempttime) {
                                $duration = $attempttime->lessontime - $attempttime->starttime;
                                if ($conditions->timespent < $duration/60) {
                                    $timespent = true;
                                }
                            }
                        } 
                    } else {
                        $timespent = true; // there isn't one set
                    }
                    
                    // check for the gradebetterthan condition
                    if($conditions->gradebetterthan) {
                        if ($studentgrades = get_records_select('lesson_grades', "userid = $USER->id AND lessonid = $dependentlesson->id")) {
                            // go through all the grades and test to see if any of them satisfy the condition
                            foreach($studentgrades as $studentgrade) {
                                if ($studentgrade->grade >= $conditions->gradebetterthan) {
                                    $gradebetterthan = true;
                                }
                            }
                        }
                    } else {
                        $gradebetterthan = true; // there isn't one set
                    }
                
                    // check for the completed condition
                    if ($conditions->completed) {
                        if (count_records('lesson_grades', 'userid', $USER->id, 'lessonid', $dependentlesson->id)) {
                            $completed = true;
                        }
                    } else {
                        $completed = true; // not set
                    }
                
                    $errors = array();
                    // collect all of our error statements
                    if (!$timespent) {
                        $errors[] = get_string('timespenterror', 'lesson', $conditions->timespent);
                    }
                    if (!$completed) {
                        $errors[] = get_string('completederror', 'lesson');
                    }
                    if (!$gradebetterthan) {
                        $errors[] = get_string('gradebetterthanerror', 'lesson', $conditions->gradebetterthan);
                    }
                    if (!empty($errors)) {  // print out the errors if any
                        echo '<p>';
                        print_simple_box_start('center');
                        print_string('completethefollowingconditions', 'lesson', $dependentlesson->name);
                        echo '<p align="center">'.implode('<br />'.get_string('and', 'lesson').'<br />', $errors).'</p>';
                        print_simple_box_end();
                        echo '</p>';
                        print_footer($course);
                        exit();
                    } 
                }
            }
            add_to_log($course->id, 'lesson', 'start', 'view.php?id='. $cm->id, $lesson->id, $cm->id);
            // if no pageid given see if the lesson has been started
            if ($grades = get_records_select('lesson_grades', 'lessonid = '. $lesson->id .' AND userid = '. $USER->id,
                        'grade DESC')) {
                $retries = count($grades);
            } else {
                $retries = 0;
            }
            if ($retries) {
                $attemptflag = true;
            }
            
            if (isset($USER->modattempts[$lesson->id])) { 
                unset($USER->modattempts[$lesson->id]);  // if no pageid, then student is NOT reviewing
            }
            
            // if there are any questions have been answered correctly in this attempt
            if ($attempts = get_records_select('lesson_attempts', 
                        "lessonid = $lesson->id AND userid = $USER->id AND retry = $retries AND 
                        correct = 1", 'timeseen DESC')) {
                
                foreach ($attempts as $attempt) {
                    $jumpto = get_field('lesson_answers', 'jumpto', 'id', $attempt->answerid);
                    // convert the jumpto to a proper page id
                    if ($jumpto == 0) { // unlikely value!
                        $lastpageseen = $attempt->pageid;
                    } elseif ($jumpto == LESSON_NEXTPAGE) {
                        if (!$lastpageseen = get_field('lesson_pages', 'nextpageid', 'id', 
                                    $attempt->pageid)) {
                            // no nextpage go to end of lesson
                            $lastpageseen = LESSON_EOL;
                        }
                    } else {
                        $lastpageseen = $jumpto;
                    }
                    break; // only look at the latest correct attempt 
                }
            } else {
                $attempts = NULL;
            }

            if ($branchtables = get_records_select('lesson_branch', 
                                "lessonid = $lesson->id AND userid = $USER->id AND retry = $retries", 'timeseen DESC')) {
                // in here, user has viewed a branch table
                $lastbranchtable = current($branchtables);
                if ($attempts != NULL) {
                    foreach($attempts as $attempt) {
                        if ($lastbranchtable->timeseen > $attempt->timeseen) {
                            // branch table was viewed later than the last attempt
                            $lastpageseen = $lastbranchtable->pageid;
                        }
                        break;
                    }
                } else {
                    // hasnt answered any questions but has viewed a branch table
                    $lastpageseen = $lastbranchtable->pageid;
                }
            }
            //if ($lastpageseen != $firstpageid) {
            if (isset($lastpageseen) and count_records('lesson_attempts', 'lessonid', $lesson->id, 'userid', $USER->id, 'retry', $retries) > 0) {
                // get the first page
                if (!$firstpageid = get_field('lesson_pages', 'id', 'lessonid', $lesson->id,
                            'prevpageid', 0)) {
                    error('Navigation: first page not found');
                }
                if ($lesson->timed) {
                    if ($lesson->retake) {
                        print_simple_box('<p align="center">'. get_string('leftduringtimed', 'lesson') .'</p>', 'center');
                        echo '<div align="center" class="lessonbutton standardbutton">'.
                                  '<a href="view.php?id='.$cm->id.'&amp;action=navigation&amp;pageid='.$firstpageid.'&amp;startlastseen=no">'.
                                    get_string('continue', 'lesson').'</a></div>';
                    } else {
                        print_simple_box_start('center');
                        echo '<div align="center">';
                        echo get_string('leftduringtimednoretake', 'lesson');
                        echo '<br /><br /><div class="lessonbutton standardbutton"><a href="../../course/view.php?id='. $course->id .'">'. get_string('returntocourse', 'lesson') .'</a></div>';
                        echo '</div>';
                        print_simple_box_end();
                    }
                    
                } else {
                    print_simple_box("<p align=\"center\">".get_string('youhaveseen','lesson').'</p>',
                            "center");
                    
                    echo '<div align="center">';
                    echo '<span class="lessonbutton standardbutton">'.
                            '<a href="view.php?id='.$cm->id.'&amp;action=navigation&amp;pageid='.$lastpageseen.'&amp;startlastseen=yes">'.
                            get_string('yes').'</a></span>&nbsp;&nbsp;&nbsp;';
                    echo '<span class="lessonbutton standardbutton">'.
                            '<a href="view.php?id='.$cm->id.'&amp;action=navigation&amp;pageid='.$firstpageid.'&amp;startlastseen=no">'.
                            get_string('no').'</a></div>';
                    echo '</span>';
                }
                print_footer($course);
                exit();
            }
            
            if ($grades) {
                foreach ($grades as $grade) {
                    $bestgrade = $grade->grade;
                    break;
                }
                if (!$lesson->retake) {
                    print_simple_box_start('center');
                    echo "<div align=\"center\">";
                    echo get_string("noretake", "lesson");
                    echo "<br /><br /><div class=\"lessonbutton standardbutton\"><a href=\"../../course/view.php?id=$course->id\">".get_string('returntocourse', 'lesson').'</a></div>';
                    echo "</div>";
                    print_simple_box_end();
                    print_footer($course);
                    exit();
                      //redirect("../../course/view.php?id=$course->id", get_string("alreadytaken", "lesson"));
                // allow student to retake course even if they have the maximum grade
                // } elseif ($bestgrade == 100) {
                  //     redirect("../../course/view.php?id=$course->id", get_string("maximumgradeachieved",
                //                 "lesson"));
                }
            }
            // start at the first page
            if (!$pageid = get_field('lesson_pages', 'id', 'lessonid', $lesson->id, 'prevpageid', 0)) {
                    error('Navigation: first page not found');
            }
            /// This is the code for starting a timed test
            if(!isset($USER->startlesson[$lesson->id]) && !has_capability('mod/lesson:manage', $context)) {
                $USER->startlesson[$lesson->id] = true;
                $startlesson = new stdClass;
                $startlesson->lessonid = $lesson->id;
                $startlesson->userid = $USER->id;
                $startlesson->starttime = time();
                $startlesson->lessontime = time();
                
                if (!insert_record('lesson_timer', $startlesson)) {
                    error('Error: could not insert row into lesson_timer table');
                }
                if ($lesson->timed) {
                    $timedflag = true;
                }
            }
            
            if (!empty($lesson->mediafile)) {
                // open our pop-up
                $url = '/mod/lesson/mediafile.php?id='.$cm->id;
                $name = 'lessonmediafile';
                $options = 'menubar=0,location=0,left=5,top=5,scrollbars,resizable,width='. $lesson->mediawidth .',height='. $lesson->mediaheight;
                echo "\n<script language=\"javascript\" type=\"text/javascript\">";
                echo "\n<!--\n";
                echo "     openpopup('$url', '$name', '$options', 0);";
                echo "\n-->\n";
                echo '</script>';
            }
        }
        
        if ($pageid != LESSON_EOL) {
            /// This is the code updates the lessontime for a timed test
            if ($startlastseen = optional_param('startlastseen', '', PARAM_ALPHA)) {  /// this deletes old records  not totally sure if this is necessary anymore
                if ($startlastseen == 'no') {
                    if ($grades = get_records_select('lesson_grades', "lessonid = $lesson->id AND userid = $USER->id",
                                'grade DESC')) {
                        $retries = count($grades);
                    } else {
                        $retries = 0;
                    }
                    if (!delete_records('lesson_attempts', 'userid', $USER->id, 'lessonid', $lesson->id, 'retry', $retries)) {
                        error('Error: could not delete old attempts');
                    }
                    if (!delete_records('lesson_branch', 'userid', $USER->id, 'lessonid', $lesson->id, 'retry', $retries)) {
                        error('Error: could not delete old seen branches');
                    }
                }
            }
            
            add_to_log($course->id, 'lesson', 'view', 'view.php?id='. $cm->id, $pageid, $cm->id);
            if (!$page = get_record('lesson_pages', 'id', $pageid)) {
                error('Navigation: the page record not found');
            }

            if ($page->qtype == LESSON_CLUSTER) {  //this only gets called when a user starts up a new lesson and the first page is a cluster page
                if (!has_capability('mod/lesson:manage', $context)) {
                    // get new id
                    $pageid = lesson_cluster_jump($lesson->id, $USER->id, $pageid);
                    // get new page info
                    if (!$page = get_record('lesson_pages', 'id', $pageid)) {
                        error('Navigation: the page record not found');
                    }
                    add_to_log($course->id, 'lesson', 'view', 'view.php?id='. $cm->id, $pageid, $cm->id);
                } else {
                    // get the next page
                    $pageid = $page->nextpageid;
                    if (!$page = get_record('lesson_pages', 'id', $pageid)) {
                        error('Navigation: the page record not found');
                    }
                }
            } elseif ($page->qtype == LESSON_ENDOFCLUSTER) {
                if ($page->nextpageid == 0) {
                    $nextpageid = LESSON_EOL;
                } else {
                    $nextpageid = $page->nextpageid;
                }
                redirect("view.php?id=$cm->id&amp;action=navigation&amp;pageid=$nextpageid", get_string('endofclustertitle', 'lesson'));
            }
            
            
            // check to see if the user can see the left menu
            if (!has_capability('mod/lesson:manage', $context)) {
                $lesson->displayleft = lesson_displayleftif($lesson);
            }
            
            // start of left menu
            if ($lesson->displayleft) {
               echo '<table><tr valign="top"><td>';
               // skip navigation link
               echo '<a href="#maincontent" class="skip">'.get_string('skip', 'lesson').'</a>';
               if($firstpageid = get_field('lesson_pages', 'id', 'lessonid', $lesson->id, 'prevpageid', 0)) {
                        // print the pages
                        echo "<div class=\"leftmenu_container\">\n";
                            echo '<div class="leftmenu_title">'.get_string('lessonmenu', 'lesson')."</div>\n";
                            echo '<div class="leftmenu_courselink">';
                            echo "<a href=\"$CFG->wwwroot/course/view.php?id=$course->id\">".get_string("mainmenu", "lesson")."</a>\n";
                            echo "</div>\n";
                            echo "<div class=\"leftmenu_links\">\n";
                            lesson_print_tree_menu($lesson->id, $firstpageid, $cm->id);
                            echo "</div>\n";
                        echo "</div>\n";
                }
                if ($page->qtype == LESSON_BRANCHTABLE) {
                    $width = '';
                } else {
                    $width = ' width="100%" ';
                }
                echo '</td><td align="center" '.$width.'>';
                // skip to anchor
                echo '<a name="maincontent" id="maincontent" title="'.get_string('anchortitle', 'lesson').'"></a>';
            } elseif ($lesson->slideshow && $page->qtype == LESSON_BRANCHTABLE) {
                echo '<table align="center"><tr><td>';  // only want this if no left menu
            }

            // starts the slideshow div
            if($lesson->slideshow && $page->qtype == LESSON_BRANCHTABLE) { 
                echo "<table align=\"center\" width=\"100%\" border=\"0\"><tr><td>\n".
                     "<div class=\"slideshow\" style=\"
                        background-color: $lesson->bgcolor;
                        height: ".$lesson->height."px;
                        width: ".$lesson->width."px;
                        \">\n";
            } else {
                echo "<table align=\"center\" width=\"100%\" border=\"0\"><tr><td>\n";
                $lesson->slideshow = false; // turn off slide show for all pages other than LESSON_BRANTCHTABLE
            }

            // This is where several messages (usually warnings) are displayed
            // all of this is displayed above the actual page
            
            if (!empty($lesson->mediafile)) {
                $url = '/mod/lesson/mediafile.php?id='.$cm->id;
                $options = 'menubar=0,location=0,left=5,top=5,scrollbars,resizable,width='. $lesson->mediawidth .',height='. $lesson->mediaheight;
                $name = 'lessonmediafile';
                echo '<div align="right">';
                link_to_popup_window ($url, $name, get_string('mediafilepopup', 'lesson'), '', '', get_string('mediafilepopup', 'lesson'), $options);
                helpbutton("mediafilestudent", get_string("mediafile", "lesson"), "lesson");
                echo '</div>';
            }
            // clock code
            // get time information for this user
            if(!has_capability('mod/lesson:manage', $context)) {
                if (!$timer = get_records_select('lesson_timer', "lessonid = $lesson->id AND userid = $USER->id", 'starttime')) {
                    error('Error: could not find records');
                } else {
                    $timer = array_pop($timer); // this will get the latest start time record
                }
            }
    
            $startlastseen = optional_param('startlastseen', '', PARAM_ALPHA);
            if ($startlastseen == 'yes') {  // continue a previous test, need to update the clock  (think this option is disabled atm)
                $timer->starttime = time() - ($timer->lessontime - $timer->starttime);
                $timer->lessontime = time();
            } else if ($startlastseen == 'no') {  // starting over
                // starting over, so reset the clock
                $timer->starttime = time();
                $timer->lessontime = time();
            }
                
            // for timed lessons, display clock
            if ($lesson->timed) {
                if(has_capability('mod/lesson:manage', $context)) {
                    echo '<p align="center">'. get_string('teachertimerwarning', 'lesson') .'<p>';
                } else {
                    if ((($timer->starttime + $lesson->maxtime * 60) - time()) > 0) {
                        // code for the clock
                        echo '<table align="right" width="150px" class="generaltable generalbox" cellspacing="0" cellpadding="5px" border="0" valign="top">'.
                            "<tr><th valign=\"top\" class=\"header\">".get_string("timeremaining", "lesson").
                            "</th></tr><tr><td align=\"center\" class=\"c0\">";
                        echo "<script language=\"javascript\">\n";
                            echo "var starttime = ". $timer->starttime . ";\n";
                            echo "var servertime = ". time() . ";\n";
                            echo "var testlength = ". $lesson->maxtime * 60 .";\n";
                            echo "document.write('<script type=\"text/javascript\" src=\"$CFG->wwwroot/mod/lesson/timer.js\"><\/script>');\n";
                            echo "window.onload = function () { show_clock(); }\n";
                        echo "</script>\n";
                        echo '<noscript>'.print_time_remaining($timer->starttime, $lesson->maxtime, true)."</noscript>\n";
                        echo "</td></tr></table>";
                        echo "<br /><br /><br />";
                    } else {
                        redirect("view.php?id=$cm->id&amp;action=navigation&amp;pageid=".LESSON_EOL."&amp;outoftime=normal", get_string("outoftime", "lesson"));
                    }
                    // update clock when viewing a new page... no special treatment
                    if ((($timer->starttime + $lesson->maxtime * 60) - time()) < 60) {
                        echo "<p align=\"center\">".get_string('studentoneminwarning', 'lesson')."</p>";
                    }    
                    
                    if ($timedflag) {
                        print_simple_box(get_string('maxtimewarning', 'lesson', $lesson->maxtime), 'center');
                    }
                }
            }

            // update the clock
            if (!has_capability('mod/lesson:manage', $context)) {
                $timer->lessontime = time();
                if (!update_record('lesson_timer', $timer)) {
                    error('Error: could not update lesson_timer table');
                }
            }
            
            if ($attemptflag) {
                print_heading(get_string('attempt', 'lesson', $retries + 1));
            }
                        
            // before we output everything check to see if the page is a EOB, if so jump directly 
            // to it's associated branch table
            if ($page->qtype == LESSON_ENDOFBRANCH) {
                if ($answers = get_records('lesson_answers', 'pageid', $page->id, 'id')) {
                    // print_heading(get_string('endofbranch', 'lesson'));
                    foreach ($answers as $answer) {
                        // just need the first answer
                        if ($answer->jumpto == LESSON_RANDOMBRANCH) {
                            $answer->jumpto = lesson_unseen_branch_jump($lesson->id, $USER->id);
                        } elseif ($answer->jumpto == LESSON_CLUSTERJUMP) {
                            if (!has_capability('mod/lesson:manage', $context)) {
                                $answer->jumpto = lesson_cluster_jump($lesson->id, $USER->id, $pageid);
                            } else {
                                if ($page->nextpageid == 0) {  
                                    $answer->jumpto = LESSON_EOL;
                                } else {
                                    $answer->jumpto = $page->nextpageid;
                                }
                            }
                        } else if ($answer->jumpto == LESSON_NEXTPAGE) {
                            if ($page->nextpageid == 0) {  
                                $answer->jumpto = LESSON_EOL;
                            } else {
                                $answer->jumpto = $page->nextpageid;
                            }
                        } else if ($answer->jumpto == 0) {
                            $answer->jumpto = $page->id;
                        } else if ($answer->jumpto == LESSON_PREVIOUSPAGE) {
                            $answer->jumpto = $page->prevpageid;                            
                        }
                        redirect("view.php?id=$cm->id&amp;action=navigation&amp;pageid=$answer->jumpto");// REMOVED: , get_string("endofbranch", "lesson")
                        break;
                    } 
                    print_footer($course);
                    exit();
                } else {
                    error('Navigation: No answers on EOB');
                }
            }
                        
             ///  This is the warning msg for teachers to inform them that cluster and unseen does not work while logged in as a teacher
            if(has_capability('mod/lesson:manage', $context)) {
                if (lesson_display_teacher_warning($lesson->id)) {
                    $warningvars->cluster = get_string('clusterjump', 'lesson');
                    $warningvars->unseen = get_string('unseenpageinbranch', 'lesson');
                    echo '<p align="center">'. get_string('teacherjumpwarning', 'lesson', $warningvars) .'</p>';
                }
            }
            
            /// This calculates and prints the ongoing score
            if ($lesson->ongoing and !empty($pageid)) {
                lesson_print_ongoing_score($lesson);
            }
            
            if ($page->qtype == LESSON_BRANCHTABLE) {
                if ($lesson->minquestions and !has_capability('mod/lesson:manage', $context)) {
                    // tell student how many questions they have seen, how many are required and their grade
                    $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id);
                    
                    $gradeinfo = lesson_grade($lesson, $ntries);
                    
                    if ($gradeinfo->attempts) {
                        echo "<p align=\"center\">".get_string("numberofpagesviewed", "lesson", $gradeinfo->nquestions).
                                "; (".get_string("youshouldview", "lesson", $lesson->minquestions).")<br />";
                        // count the number of distinct correct pages
                        if ($gradeinfo->nquestions < $lesson->minquestions) {
                            $gradeinfo->nquestions = $lesson->minquestions;
                        }
                        echo get_string("numberofcorrectanswers", "lesson", $gradeinfo->earned)."<br />\n";
                        echo get_string("yourcurrentgradeis", "lesson", 
                                number_format($gradeinfo->grade * $lesson->grade / 100, 1)).
                            " (".get_string("outof", "lesson", $lesson->grade).")</p>\n";
                    }
                }
            }
               
            // now starting to print the page's contents   
            echo "<div align=\"center\">";            
            echo "<em><strong>";
            echo format_string($lesson->name) . "</strong></em>";
            if ($page->qtype == LESSON_BRANCHTABLE) {
                echo ":<br />";
                print_heading(format_string($page->title));
            }
            echo "</div><br />";
            
            if (!$lesson->slideshow) {
                $options = new stdClass;
                $options->noclean = true;
                print_simple_box('<div class="contents">'.
                                format_text($page->contents, FORMAT_MOODLE, $options).
                                '</div>', 'center');
            }
            echo "<br />\n";
            
            // this is for modattempts option.  Find the users previous answer to this page,
            //   and then display it below in answer processing
            if (isset($USER->modattempts[$lesson->id])) {            
                $retries = count_records('lesson_grades', "lessonid", $lesson->id, "userid", $USER->id);
                $retries--;
                if (! $attempts = get_records_select("lesson_attempts", "lessonid = $lesson->id AND userid = $USER->id AND pageid = $page->id AND retry = $retries", "timeseen")) {
                    error("Previous attempt record could not be found!");
                }
                $attempt = end($attempts);
            }
            
            // get the answers in a set order, the id order
            if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
                echo "<form name=\"answerform\" method =\"post\" action=\"lesson.php\">";
                echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />";
                echo "<input type=\"hidden\" name=\"action\" value=\"continue\" />";
                echo "<input type=\"hidden\" name=\"pageid\" value=\"$pageid\" />";
                echo "<input type=\"hidden\" name=\"sesskey\" value=\"".$USER->sesskey."\" />";
                if (!$lesson->slideshow) {
                    if ($page->qtype != LESSON_BRANCHTABLE) {
                        print_simple_box_start("center");
                    }                    
                    echo '<table width="100%">';
                }
                // default format text options
                $options = new stdClass;
                $options->para = false; // no <p></p>
                $options->noclean = true;
                switch ($page->qtype) {
                    case LESSON_SHORTANSWER :
                    case LESSON_NUMERICAL :
                        if (isset($USER->modattempts[$lesson->id])) {     
                            $value = "value=\"$attempt->useranswer\"";
                        } else {
                            $value = "";
                        }       
                        echo '<tr><td align="center"><label for="answer">'.get_string('youranswer', 'lesson').'</label>'.
                            ": <input type=\"text\" id=\"answer\" name=\"answer\" size=\"50\" maxlength=\"200\" $value />\n";
                        echo '</table>';
                        print_simple_box_end();
                        lesson_print_submit_link(get_string('pleaseenteryouranswerinthebox', 'lesson'), 'answerform');
                        break;
                    case LESSON_TRUEFALSE :
                        shuffle($answers);
                        $i = 0;
                        foreach ($answers as $answer) {
                            echo '<tr><td valign="top">';
                            if (isset($USER->modattempts[$lesson->id]) && $answer->id == $attempt->answerid) {
                                $checked = 'checked="checked"';
                            } else {
                                $checked = '';
                            } 
                            echo "<input type=\"radio\" id=\"answerid$i\" name=\"answerid\" value=\"{$answer->id}\" $checked />";
                            echo "</td><td>";
                            echo "<label for=\"answerid$i\">".format_text(trim($answer->answer), FORMAT_MOODLE, $options).'</label>';
                            echo '</td></tr>';
                            if ($answer != end($answers)) {
                                echo "<tr><td><br></td></tr>";                            
                            }
                            $i++;
                        }
                        echo '</table>';
                        print_simple_box_end();
                        lesson_print_submit_link(get_string('pleasecheckoneanswer', 'lesson'), 'answerform');
                        break;
                    case LESSON_MULTICHOICE :
                        $i = 0;
                        shuffle($answers);

                        foreach ($answers as $answer) {
                            echo '<tr><td valign="top">';
                            if ($page->qoption) {
                                $checked = '';
                                if (isset($USER->modattempts[$lesson->id])) {
                                    $answerids = explode(",", $attempt->useranswer);
                                    if (in_array($answer->id, $answerids)) {
                                        $checked = ' checked="checked"';
                                    } else {
                                        $checked = '';
                                    }
                                }
                                // more than one answer allowed 
                                echo "<input type=\"checkbox\" id=\"answerid$i\" name=\"answer[$i]\" value=\"{$answer->id}\"$checked />";
                            } else {
                                if (isset($USER->modattempts[$lesson->id]) && $answer->id == $attempt->answerid) {
                                    $checked = ' checked="checked"';
                                } else {
                                    $checked = '';
                                } 
                                // only one answer allowed
                                echo "<input type=\"radio\" id=\"answerid$i\" name=\"answerid\" value=\"{$answer->id}\"$checked />";
                            }
                            echo '</td><td>';
                            echo "<label for=\"answerid$i\" >".format_text(trim($answer->answer), FORMAT_MOODLE, $options).'</label>'; 
                            echo '</td></tr>';
                            if ($answer != end($answers)) {
                                echo '<tr><td><br></td></tr>';
                            } 
                            $i++;
                        }
                        echo '</table>';
                        print_simple_box_end();
                        if ($page->qoption) {
                            $linkname = get_string('pleasecheckoneormoreanswers', 'lesson');
                        } else {
                            $linkname = get_string('pleasecheckoneanswer', 'lesson');
                        }
                        lesson_print_submit_link($linkname, 'answerform');
                        break;
                        
                    case LESSON_MATCHING :
                        echo '<tr><td><table width="100%">';
                        // don't suffle answers (could be an option??)
                        foreach ($answers as $answer) {
                            // get all the response
                            if ($answer->response != NULL) {
                                $responses[] = trim($answer->response);
                            }
                        }
                        shuffle($responses);
                        $responses = array_unique($responses);
                        
                        $responseoptions = array();
                        foreach ($responses as $response) {
                            $responseoptions[htmlspecialchars(trim($response))] = $response;
                        }
                        
                        if (isset($USER->modattempts[$lesson->id])) {
                            $useranswers = explode(',', $attempt->useranswer);
                            $t = 0;
                        }
                        foreach ($answers as $answer) {
                            if ($answer->response != NULL) {
                                echo '<tr><td align="right">';
                                echo "<b><label for=\"menuresponse[$answer->id]\">".
                                        format_text($answer->answer,FORMAT_MOODLE,$options).
                                        '</label>: </b></td><td valign="bottom">';

                                if (isset($USER->modattempts[$lesson->id])) {
                                    $selected = htmlspecialchars(trim($answers[$useranswers[$t]]->response));  // gets the user's previous answer
                                    choose_from_menu ($responseoptions, "response[$answer->id]", $selected);
                                    $t++;
                                } else {
                                    choose_from_menu ($responseoptions, "response[$answer->id]");
                                }
                                echo '</td></tr>';
                                if ($answer != end($answers)) {
                                    echo '<tr><td><br /></td></tr>';
                                } 
                            }
                        }
                        echo '</table></table>';
                        print_simple_box_end();
                        lesson_print_submit_link(get_string('pleasematchtheabovepairs', 'lesson'), 'answerform');
                        break;
                    case LESSON_BRANCHTABLE :                        
                        $options = new stdClass;
                        $options->para = false;
                        $buttons = array('next' => array(), 'prev' => array(), 'other' => array());
                    /// seperate out next and previous jumps from the other jumps 
                        foreach ($answers as $answer) {
                            if ($answer->jumpto == LESSON_NEXTPAGE) {
                                $type = 'next';
                            } else if ($answer->jumpto == LESSON_PREVIOUSPAGE) {
                                $type = 'prev';
                            } else {
                                $type = 'other';
                            }
                            $buttons[$type][] = '<a href="javascript:document.answerform.jumpto.value='.$answer->jumpto.';document.answerform.submit();">'.
                                    strip_tags(format_text($answer->answer, FORMAT_MOODLE, $options)).'</a>';
                        }
                    
                    /// set the order and orientation (order is very important for the divs to work for horizontal!)
                        if ($page->layout) {
                            $orientation = 'horizontal';
                            $a = 'a';
                            $b = 'b';
                            $c = 'c';
                            $implode = ' ';
                            $implode2 = "\n    ";
                            if (empty($buttons['other'])) {
                                $buttons['other'][] = '&nbsp;';  // very critical! If nothing is in the middle, 
                                                                 // then the div style float left/right will not 
                                                                 // render properly with next/previous buttons
                            }
                        } else {
                            $orientation = 'vertical';
                            $a = 'c';
                            $b = 'a';
                            $c = 'b';
                            $implode = '<br /><br />';
                            $implode2 = "<br /><br />\n    ";
                        }
                        $buttonsarranged = array();
                        $buttonsarranged[$a] = '<span class="lessonbutton prevbutton prev'.$orientation.'">'.implode($implode, $buttons['prev']).'</span>';
                        $buttonsarranged[$b] = '<span class="lessonbutton nextbutton next'.$orientation.'">'.implode($implode, $buttons['next']).'</span>';
                        $buttonsarranged[$c] = '<span class="lessonbutton standardbutton standard'.$orientation.'">'.implode($implode, $buttons['other']).'</span>';
                        ksort($buttonsarranged); // sort by key
                        
                        $fullbuttonhtml = "\n<div class=\"branchbuttoncontainer\">\n    " . implode($implode2, $buttonsarranged). "\n</div>\n";
                    
                        if ($lesson->slideshow) {
                            echo '<div class="branchslidetop">' . $fullbuttonhtml . '</div>';
                            $options = new stdClass;
                            $options->noclean = true;
                            echo '<div class="contents">'.format_text($page->contents, FORMAT_MOODLE, $options)."</div>\n";
                            echo '</div><!--end slideshow div-->';
                            echo '<div class="branchslidebottom">' . $fullbuttonhtml . '</div>';
                        } else {
                            echo '<tr><td>';
                            print_simple_box($fullbuttonhtml, 'center');
                            echo '</td></tr></table>'; // ends the answers table
                        }
                        echo '<input type="hidden" name="jumpto" />';
                        
                        break;
                    case LESSON_ESSAY :
                        if (isset($USER->modattempts[$lesson->id])) {
                            $essayinfo = unserialize($attempt->useranswer);
                            $value = $essayinfo->answer;
                        } else {
                            $value = "";
                        }
                        echo '<tr><td align="center" valign="top" nowrap><label for="answer">'.get_string("youranswer", "lesson").'</label>:</td><td>'.
                             '<textarea id="answer" name="answer" rows="15" cols="60">'.$value."</textarea>\n";
                        echo '</td></tr></table>';
                        print_simple_box_end();
                        lesson_print_submit_link(get_string('pleaseenteryouranswerinthebox', 'lesson'), 'answerform');
                        break;
                }
                echo "</form>\n"; 
            } else {
                // a page without answers - find the next (logical) page
                echo "<form name=\"pageform\" method =\"post\" action=\"view.php\">\n";
                echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
                echo "<input type=\"hidden\" name=\"action\" value=\"navigation\" />\n";
                if ($lesson->nextpagedefault) {
                    // in Flash Card mode...
                    // ...first get number of retakes
                    $nretakes = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id); 
                    // ...then get the page ids (lessonid the 5th param is needed to make get_records play)
                    $allpages = get_records("lesson_pages", "lessonid", $lesson->id, "id", "id,lessonid");
                    shuffle ($allpages);
                    $found = false;
                    if ($lesson->nextpagedefault == LESSON_UNSEENPAGE) {
                        foreach ($allpages as $thispage) {
                            if (!count_records("lesson_attempts", "pageid", $thispage->id, "userid", 
                                        $USER->id, "retry", $nretakes)) {
                                $found = true;
                                break;
                            }
                        }
                    } elseif ($lesson->nextpagedefault == LESSON_UNANSWEREDPAGE) {
                        foreach ($allpages as $thispage) {
                            if (!count_records_select("lesson_attempts", "pageid = $thispage->id AND
                                        userid = $USER->id AND correct = 1 AND retry = $nretakes")) {
                                $found = true;
                                break;
                            }
                        }
                    }
                    if ($found) {
                        $newpageid = $thispage->id;
                        if ($lesson->maxpages) {
                            // check number of pages viewed (in the lesson)
                            if (count_records("lesson_attempts", "lessonid", $lesson->id, "userid", $USER->id,
                                    "retry", $nretakes) >= $lesson->maxpages) {
                                $newpageid = LESSON_EOL;
                            }
                        }
                    } else {
                        $newpageid = LESSON_EOL;
                    }
                } else {
                    // in normal lesson mode...
                    if (!$newpageid = get_field("lesson_pages", "nextpageid", "id", $pageid)) {
                        // this is the last page - flag end of lesson
                        $newpageid = LESSON_EOL;
                    }
                }
                echo "<input type=\"hidden\" name=\"pageid\" value=\"$newpageid\" />\n";
                echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
                     get_string("continue", "lesson")."\" /></p>\n";
                echo "</form>\n";
            }
            lesson_print_progress_bar($lesson, $course);
            echo "</table>\n"; 
        } else {
            // end of lesson reached work out grade
            
            // check to see if the student ran out of time
            $outoftime = optional_param('outoftime', '', PARAM_ALPHA);
            if ($lesson->timed && !has_capability('mod/lesson:manage', $context)) {
                if ($outoftime == 'normal') {
                    print_simple_box(get_string("eolstudentoutoftime", "lesson"), "center");
                }
            }

            // Update the clock / get time information for this user
            if (!has_capability('mod/lesson:manage', $context)) {
                unset($USER->startlesson[$lesson->id]);
                if (!$timer = get_records_select('lesson_timer', "lessonid = $lesson->id AND userid = $USER->id", 'starttime')) {
                    error('Error: could not find records');
                } else {
                    $timer = array_pop($timer); // this will get the latest start time record
                }
                $timer->lessontime = time();
                
                if (!update_record("lesson_timer", $timer)) {
                    error("Error: could not update lesson_timer table");
                }
            }
            
            add_to_log($course->id, "lesson", "end", "view.php?id=$cm->id", "$lesson->id", $cm->id);
            print_heading(get_string("congratulations", "lesson"));
            print_simple_box_start("center");
            $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id);
            if (isset($USER->modattempts[$lesson->id])) {
                $ntries--;  // need to look at the old attempts :)
            }
            if (!has_capability('mod/lesson:manage', $context)) {
                
                $gradeinfo = lesson_grade($lesson, $ntries);
                
                if ($gradeinfo->attempts) {
                    if (!$lesson->custom) {
                        echo "<p align=\"center\">".get_string("numberofpagesviewed", "lesson", $gradeinfo->nquestions).
                            "</p>\n";
                        if ($lesson->minquestions) {
                            if ($gradeinfo->nquestions < $lesson->minquestions) {
                                // print a warning and set nviewed to minquestions
                                echo "<p align=\"center\">".get_string("youshouldview", "lesson", 
                                        $lesson->minquestions)."</p>\n";
                            }
                        }
                        echo "<p align=\"center\">".get_string("numberofcorrectanswers", "lesson", $gradeinfo->earned).
                            "</p>\n";
                    }
                    $a = new stdClass;
                    $a->score = $gradeinfo->earned;
                    $a->grade = $gradeinfo->total;
                    if ($gradeinfo->nmanual) {
                        $a->tempmaxgrade = $gradeinfo->total - $gradeinfo->manualpoints;
                        $a->essayquestions = $gradeinfo->nmanual;
                        echo "<div align=\"center\">".get_string("displayscorewithessays", "lesson", $a)."</div>";
                    } else {
                        echo "<div align=\"center\">".get_string("displayscorewithoutessays", "lesson", $a)."</div>";                        
                    }
                    echo "<p align=\"center\">".get_string("gradeis", "lesson", 
                            number_format($gradeinfo->grade * $lesson->grade / 100, 1)).
                        " (".get_string("outof", "lesson", $lesson->grade).")</p>\n";
                        
                    $grade->lessonid = $lesson->id;
                    $grade->userid = $USER->id;
                    $grade->grade = $gradeinfo->grade;
                    $grade->completed = time();
                    if (!$lesson->practice) {
                        if (isset($USER->modattempts[$lesson->id])) { // if reviewing, make sure update old grade record
                            if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id and userid = $USER->id", "completed")) {
                                error("Could not find Grade Records");
                            }
                            $oldgrade = end($grades);
                            $grade->id = $oldgrade->id;
                            if (!$update = update_record("lesson_grades", $grade)) {
                                error("Navigation: grade not updated");
                            }
                        } else {
                            if (!$newgradeid = insert_record("lesson_grades", $grade)) {
                                error("Navigation: grade not inserted");
                            }
                        }
                    } else {
                        if (!delete_records("lesson_attempts", "lessonid", $lesson->id, "userid", $USER->id, "retry", $ntries)) {
                            error("Could not delete lesson attempts");
                        }
                    }
                } else {
                    if ($lesson->timed) {
                        if ($outoftime == 'normal') {
                            $grade = new stdClass;
                            $grade->lessonid = $lesson->id;
                            $grade->userid = $USER->id;
                            $grade->grade = 0;
                            $grade->completed = time();
                            if (!$lesson->practice) {
                                if (!$newgradeid = insert_record("lesson_grades", $grade)) {
                                    error("Navigation: grade not inserted");
                                }
                            }
                            echo get_string("eolstudentoutoftimenoanswers", "lesson");
                        }
                    } else {
                        echo get_string("welldone", "lesson");
                    }
                }   
            } else { 
                // display for teacher
                echo "<p align=\"center\">".get_string("displayofgrade", "lesson")."</p>\n";
            }
            print_simple_box_end(); //End of Lesson button to Continue.

            // after all the grade processing, check to see if "Show Grades" is off for the course
            // if yes, redirect to the course page
            if (!$course->showgrades) {
                redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
            }

            // high scores code
            if ($lesson->highscores && !has_capability('mod/lesson:manage', $context) && !$lesson->practice) {
                echo "<div align=\"center\"><br>";
                if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id", "completed")) {
                    echo get_string("youmadehighscore", "lesson", $lesson->maxhighscores)."<br>";
                    echo "<a href=\"view.php?id=$cm->id&amp;action=nameforhighscores\">".get_string("clicktopost", "lesson")."</a><br>";
                } else {
                    if (!$highscores = get_records_select("lesson_high_scores", "lessonid = $lesson->id")) {
                        echo get_string("youmadehighscore", "lesson", $lesson->maxhighscores)."<br>";
                        echo "<div class=\"lessonbutton standardbutton\"><a href=\"view.php?id=$cm->id&amp;action=nameforhighscores\">".get_string("clicktopost", "lesson")."</a></div><br/>";
                    } else {
                        // get all the high scores into an array
                        foreach ($highscores as $highscore) {
                            $grade = $grades[$highscore->gradeid]->grade;
                            $topscores[] = $grade;
                        }
                        // sort to find the lowest score
                        sort($topscores);
                        $lowscore = $topscores[0];
                        
                        if ($thegrade >= $lowscore || count($topscores) <= $lesson->maxhighscores) {
                            echo get_string("youmadehighscore", "lesson", $lesson->maxhighscores)."<br>";
                            echo "<div class=\"lessonbutton standardbutton\"><a href=\"view.php?id=$cm->id&amp;action=nameforhighscores\">".get_string("clicktopost", "lesson")."</a></div><br />";
                        } else {
                            echo get_string("nothighscore", "lesson", $lesson->maxhighscores)."<br>";
                        }
                    }
                }
                echo "<br /><div style=\"padding: 5px;\" class=\"lessonbutton standardbutton\"><a href=\"view.php?id=$cm->id&amp;action=highscores&link=1\">".get_string("viewhighscores", "lesson").'</a></div>';
                echo "</div>";                            
            }

            if ($lesson->modattempts && !has_capability('mod/lesson:manage', $context)) {
                // make sure if the student is reviewing, that he/she sees the same pages/page path that he/she saw the first time
                // look at the attempt records to find the first QUESTION page that the user answered, then use that page id
                // to pass to view again.  This is slick cause it wont call the empty($pageid) code
                // $ntries is decremented above
                if (!$attempts = get_records_select("lesson_attempts", "lessonid = $lesson->id AND userid = $USER->id AND retry = $ntries", "timeseen")) {
                    $attempts = array();
                }
                $firstattempt = current($attempts);
                $pageid = $firstattempt->pageid;
                // IF the student wishes to review, need to know the last question page that the student answered.  This will help to make
                // sure that the student can leave the lesson via pushing the continue button.
                $lastattempt = end($attempts);
                $USER->modattempts[$lesson->id] = $lastattempt->pageid;
                echo "<div align=\"center\" style=\"padding: 5px;\" class=\"lessonbutton standardbutton\"><a href=\"view.php?id=$cm->id&amp;pageid=$pageid\">".get_string("reviewlesson", "lesson")."</a></div>\n"; 
            } elseif ($lesson->modattempts && has_capability('mod/lesson:manage', $context)) {
                echo "<p align=\"center\">".get_string("modattemptsnoteacher", "lesson")."</p>";                
            }
            
            if ($lesson->activitylink) {
                if ($module = get_record('course_modules', 'id', $lesson->activitylink)) {
                    if ($modname = get_field('modules', 'name', 'id', $module->module))
                        if ($instance = get_record($modname, 'id', $module->instance)) {
                            echo "<div align=\"center\" style=\"padding: 5px;\" class=\"lessonbutton standardbutton\">".
                                    "<a href=\"$CFG->wwwroot/mod/$modname/view.php?id=$lesson->activitylink\">".
                                    get_string('activitylinkname', 'lesson', $instance->name)."</a></div>\n";
                        }
                }
            }

            echo "<div align=\"center\" style=\"padding: 5px;\" class=\"lessonbutton standardbutton\"><a href=\"../../course/view.php?id=$course->id\">".get_string("mainmenu", "lesson")."</a></div>\n"; // Back to the menu (course view).
            echo "<div align=\"center\" style=\"padding: 5px;\" class=\"lessonbutton standardbutton\"><a href=\"../../grade/index.php?id=$course->id\">".get_string("viewgrades", "lesson")."</a></div>\n"; //view grades
        }
        
        if ($lesson->displayleft || $lesson->slideshow) {  // this ends the table cell and table for the leftmenu or for slideshow
            echo "</td></tr></table>";
        } 
    }


    /*******************teacher view **************************************/
    elseif ($action == 'teacherview') {
        // set collapsed flag
        if ($mode == 'collapsed') {
            $collapsed = true;
        } else {
            $collapsed = false;
        }

        print_heading_with_help(format_string($lesson->name,true), "overview", "lesson");   

        // get number of pages
        $npages = count_records('lesson_pages', 'lessonid', $lesson->id);

        if (!$page = get_record_select("lesson_pages", "lessonid = $lesson->id AND prevpageid = 0")) {
            // if there are no pages give teacher the option to create a new page or a new branch table
            echo "<div align=\"center\">";
            if (has_capability('mod/lesson:edit', $context)) {
                print_simple_box( "<table cellpadding=\"5\" border=\"0\">\n<tr><th>".get_string("whatdofirst", "lesson")."</th></tr><tr><td>".
                    "<a href=\"import.php?id=$cm->id&amp;pageid=0\">".
                    get_string("importquestions", "lesson")."</a></td></tr><tr><td>".
                    "<a href=\"importppt.php?id=$cm->id&amp;pageid=0\">".
                    get_string("importppt", "lesson")."</a></td></tr><tr><td>".
                    "<a href=\"lesson.php?id=$cm->id&amp;action=addbranchtable&amp;pageid=0&amp;firstpage=1\">".
                    get_string("addabranchtable", "lesson")."</a></td></tr><tr><td>".
                    "<a href=\"lesson.php?id=$cm->id&amp;action=addpage&amp;pageid=0&amp;firstpage=1\">".
                    get_string("addaquestionpage", "lesson")." ".get_string("here","lesson").
                    "</a></td></tr></table>\n");
            }
            echo '</div>';
        } else {
            // print the pages
            echo "<form name=\"lessonpages\" method=\"post\" action=\"view.php\">\n";
            echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
            echo "<input type=\"hidden\" name=\"action\" value=\"navigation\" />\n";
            echo "<input type=\"hidden\" name=\"pageid\" />\n";
            $branch = false;
            $singlePage = false;
            if($collapsed and !$display) {  
                echo "<div align=\"center\">\n";
                    echo "<table><tr><td>\n";
                    lesson_print_tree($page->id, $lesson, $cm->id);
                    echo "</td></tr></table>\n";
                echo "</div>\n";
            } else {
                if($display) {
                    while(true)
                    {
                        if($page->id == $display && $page->qtype == LESSON_BRANCHTABLE) {
                            $branch = true;
                            $singlePage = false;
                            break;
                        } elseif($page->id == $display) {
                            $branch = false;
                            $singlePage = true;    
                            break;
                        } elseif ($page->nextpageid) {
                            if (!$page = get_record("lesson_pages", "id", $page->nextpageid)) {
                                    error("Teacher view: Next page not found!");
                            }
                        } else {
                            // last page reached
                            break;
                        }
                    }
                    echo "<table align=\"center\" cellpadding=\"5\" border=\"0\" width=\"80%\">\n";
                    if (has_capability('mod/lesson:edit', $context)) {
                        echo "<tr><td align=\"left\"><small><a href=\"import.php?id=$cm->id&amp;pageid=$page->prevpageid\">".
                            get_string("importquestions", "lesson")."</a> | ".
                            "<a href=\"lesson.php?id=$cm->id&amp;sesskey=".$USER->sesskey."&amp;action=addcluster&amp;pageid=$page->prevpageid\">".
                            get_string("addcluster", "lesson")."</a> | ".
                            "<a href=\"lesson.php?id=$cm->id&amp;sesskey=".$USER->sesskey."&amp;action=addendofcluster&amp;pageid=$page->prevpageid\">".
                            get_string("addendofcluster", "lesson")."</a> | ".
                            "<a href=\"lesson.php?id=$cm->id&amp;action=addbranchtable&amp;pageid=$page->prevpageid\">".
                            get_string("addabranchtable", "lesson")."</a> | ".
                            "<a href=\"lesson.php?id=$cm->id&amp;action=addpage&amp;pageid=$page->prevpageid\">".
                            get_string("addaquestionpage", "lesson")." ".get_string("here","lesson").
                            "</a></small></td></tr>\n";
                    }                  
                } else {   
                    echo "<table align=\"center\" cellpadding=\"5\" border=\"0\" width=\"80%\">\n";
                    if (has_capability('mod/lesson:edit', $context)) {
                        echo "<tr><td align=\"left\"><small><a href=\"import.php?id=$cm->id&amp;pageid=0\">".
                            get_string("importquestions", "lesson")."</a> | ".
                            "<a href=\"lesson.php?id=$cm->id&amp;sesskey=".$USER->sesskey."&amp;action=addcluster&amp;pageid=0\">".
                            get_string("addcluster", "lesson")."</a> | ".
                            "<a href=\"lesson.php?id=$cm->id&amp;action=addbranchtable&amp;pageid=0\">".
                            get_string("addabranchtable", "lesson")."</a> | ".
                            "<a href=\"lesson.php?id=$cm->id&amp;action=addpage&amp;pageid=0\">".
                            get_string("addaquestionpage", "lesson")." ".get_string("here","lesson").
                            "</a></small></td></tr>\n";
                    }
                }
                /// end collapsed code    (note, there is an "}" below for an else above)
            while (true) {
                echo "<tr><td>\n";
                echo "<table width=\"100%\" border=\"1\" class=\"generalbox\"><tr><th colspan=\"2\">".format_string($page->title)."&nbsp;&nbsp;\n";
                if (has_capability('mod/lesson:edit', $context)) {
                    if ($npages > 1) {
                        echo "<a title=\"".get_string("move")."\" href=\"lesson.php?id=$cm->id&amp;action=move&amp;pageid=$page->id\">\n".
                            "<img src=\"$CFG->pixpath/t/move.gif\" hspace=\"2\" height=\"11\" width=\"11\" border=\"0\" alt=\"move\" /></a>\n";
                    }
                    echo "<a title=\"".get_string("update")."\" href=\"lesson.php?id=$cm->id&amp;action=editpage&amp;pageid=$page->id\">\n".
                        "<img src=\"$CFG->pixpath/t/edit.gif\" hspace=\"2\" height=\"11\" width=\"11\" border=\"0\" alt=\"edit\" /></a>\n".
                        "<a title=\"".get_string("delete")."\" href=\"lesson.php?id=$cm->id&amp;sesskey=".$USER->sesskey."&amp;action=confirmdelete&amp;pageid=$page->id\">\n".
                        "<img src=\"$CFG->pixpath/t/delete.gif\" hspace=\"2\" height=\"11\" width=\"11\" border=\"0\" alt=\"delete\" /></a>\n";
                }
                echo "</th></tr>\n";             
                echo "<tr><td colspan=\"2\">\n";
                $options = new stdClass;
                $options->noclean = true;
                print_simple_box(format_text($page->contents, FORMAT_MOODLE, $options), "center");
                echo "</td></tr>\n";
                // get the answers in a set order, the id order
                if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
                    echo "<tr><td colspan=\"2\" align=\"center\"><b>\n";
                    switch ($page->qtype) {
                        case LESSON_ESSAY :
                            echo $LESSON_QUESTION_TYPE[$page->qtype];
                            break;
                        case LESSON_SHORTANSWER :
                            echo $LESSON_QUESTION_TYPE[$page->qtype];
                            if ($page->qoption) {
                                echo " - ".get_string("casesensitive", "lesson");
                            }
                            break;
                        case LESSON_MULTICHOICE :
                            echo $LESSON_QUESTION_TYPE[$page->qtype];
                            if ($page->qoption) {
                                echo " - ".get_string("multianswer", "lesson");
                            }
                            break;
                        case LESSON_MATCHING :
                            echo $LESSON_QUESTION_TYPE[$page->qtype];
                            echo get_string("firstanswershould", "lesson");
                            break;
                        case LESSON_TRUEFALSE :
                        case LESSON_NUMERICAL :
                            echo $LESSON_QUESTION_TYPE[$page->qtype];
                            break;
                        case LESSON_BRANCHTABLE :    
                            echo get_string("branchtable", "lesson");
                            break;
                        case LESSON_ENDOFBRANCH :
                            echo get_string("endofbranch", "lesson");
                            break;
                        case LESSON_CLUSTER :
                            echo get_string("clustertitle", "lesson");
                            break;
                        case LESSON_ENDOFCLUSTER :
                            echo get_string("endofclustertitle", "lesson");
                            break;
                    }
                    echo "</b></td></tr>\n";
                    $i = 1;
                    $n = 0;
                    foreach ($answers as $answer) {
                        switch ($page->qtype) {
                            case LESSON_MULTICHOICE:
                            case LESSON_TRUEFALSE:
                            case LESSON_SHORTANSWER:
                            case LESSON_NUMERICAL:
                                echo "<tr><td align=\"right\" valign=\"top\" width=\"20%\">\n";
                                if ($lesson->custom) {
                                    // if the score is > 0, then it is correct
                                    if ($answer->score > 0) {
                                        echo "<b><u>".get_string("answer", "lesson")." $i:</u></b> \n";
                                    } else {
                                        echo "<b>".get_string("answer", "lesson")." $i:</b> \n";
                                    }
                                } else {
                                    if (lesson_iscorrect($page->id, $answer->jumpto)) {
                                        // underline correct answers
                                        echo "<b><u>".get_string("answer", "lesson")." $i:</u></b> \n";
                                    } else {
                                        echo "<b>".get_string("answer", "lesson")." $i:</b> \n";
                                    }
                                }
                                $options = new stdClass;
                                $options->noclean = true;
                                echo "</td><td width=\"80%\">\n";
                                echo format_text($answer->answer, FORMAT_MOODLE, $options);
                                echo "</td></tr>\n";
                                echo "<tr><td align=\"right\" valign=\"top\"><b>".get_string("response", "lesson")." $i:</b> \n";
                                echo "</td><td>\n";
                                echo format_text($answer->response, FORMAT_MOODLE, $options); 
                                echo "</td></tr>\n";
                                break;                            
                            case LESSON_MATCHING:
                                $options = new stdClass;
                                $options->noclean = true;
                                if ($n < 2) {
                                    if ($answer->answer != NULL) {
                                        if ($n == 0) {
                                            echo "<tr><td align=\"right\" valign=\"top\"><b>".get_string("correctresponse", "lesson").":</b> \n";
                                            echo "</td><td>\n";
                                            echo format_text($answer->answer, FORMAT_MOODLE, $options); 
                                            echo "</td></tr>\n";
                                        } else {
                                            echo "<tr><td align=\"right\" valign=\"top\"><b>".get_string("wrongresponse", "lesson").":</b> \n";
                                            echo "</td><td>\n";
                                            echo format_text($answer->answer, FORMAT_MOODLE, $options); 
                                            echo "</td></tr>\n";
                                        }
                                    }
                                    $n++;
                                    $i--;
                                } else {
                                    echo "<tr><td align=\"right\" valign=\"top\" width=\"20%\">\n";
                                    if ($lesson->custom) {
                                        // if the score is > 0, then it is correct
                                        if ($answer->score > 0) {
                                            echo "<b><u>".get_string("answer", "lesson")." $i:</u></b> \n";
                                        } else {
                                            echo "<b>".get_string("answer", "lesson")." $i:</b> \n";
                                        }
                                    } else {
                                        if (lesson_iscorrect($page->id, $answer->jumpto)) {
                                            // underline correct answers
                                            echo "<b><u>".get_string("answer", "lesson")." $i:</u></b> \n";
                                        } else {
                                            echo "<b>".get_string("answer", "lesson")." $i:</b> \n";
                                        }
                                    }
                                    echo "</td><td width=\"80%\">\n";
                                    echo format_text($answer->answer, FORMAT_MOODLE, $options);
                                    echo "</td></tr>\n";
                                   echo "<tr><td align=\"right\" valign=\"top\"><b>".get_string("matchesanswer", "lesson")." $i:</b> \n";
                                    echo "</td><td>\n";
                                    echo format_text($answer->response, FORMAT_MOODLE, $options); 
                                    echo "</td></tr>\n";
                                }
                                break;
                            case LESSON_BRANCHTABLE:
                                $options = new stdClass;
                                $options->noclean = true;
                                echo "<tr><td align=\"right\" valign=\"top\" width=\"20%\">\n";
                                echo "<b>".get_string("description", "lesson")." $i:</b> \n";
                                echo "</td><td width=\"80%\">\n";
                                echo format_text($answer->answer, FORMAT_MOODLE, $options);
                                echo "</td></tr>\n";
                                break;
                        }
                        if ($answer->jumpto == 0) {
                            $jumptitle = get_string("thispage", "lesson");
                        } elseif ($answer->jumpto == LESSON_NEXTPAGE) {
                            $jumptitle = get_string("nextpage", "lesson");
                        } elseif ($answer->jumpto == LESSON_EOL) {
                            $jumptitle = get_string("endoflesson", "lesson");
                        } elseif ($answer->jumpto == LESSON_UNSEENBRANCHPAGE) {
                            $jumptitle = get_string("unseenpageinbranch", "lesson");
                        } elseif ($answer->jumpto == LESSON_PREVIOUSPAGE) {
                            $jumptitle = get_string("previouspage", "lesson");
                        } elseif ($answer->jumpto == LESSON_RANDOMPAGE) {
                            $jumptitle = get_string("randompageinbranch", "lesson");
                        } elseif ($answer->jumpto == LESSON_RANDOMBRANCH) {
                            $jumptitle = get_string("randombranch", "lesson");
                        } elseif ($answer->jumpto == LESSON_CLUSTERJUMP) {
                            $jumptitle = get_string("clusterjump", "lesson");
                        } else {
                            if (!$jumptitle = get_field("lesson_pages", "title", "id", $answer->jumpto)) {
                                $jumptitle = "<b>".get_string("notdefined", "lesson")."</b>";
                            }
                        }
                        $jumptitle = format_string($jumptitle,true);
                        if ($page->qtype == LESSON_MATCHING) {
                            if ($i == 1) {
                                echo "<tr><td align=\"right\" width=\"20%\"><b>".get_string("correctanswerscore", "lesson").":";
                                echo "</b></td><td width=\"80%\">\n";
                                echo "$answer->score</td></tr>\n";
                                echo "<tr><td align=\"right\" width=\"20%\"><b>".get_string("correctanswerjump", "lesson").":";
                                echo "</b></td><td width=\"80%\">\n";
                                echo "$jumptitle</td></tr>\n";
                            } elseif ($i == 2) {
                                echo "<tr><td align=\"right\" width=\"20%\"><b>".get_string("wronganswerscore", "lesson").":";
                                echo "</b></td><td width=\"80%\">\n";
                                echo "$answer->score</td></tr>\n";
                                echo "<tr><td align=\"right\" width=\"20%\"><b>".get_string("wronganswerjump", "lesson").":";
                                echo "</b></td><td width=\"80%\">\n";
                                echo "$jumptitle</td></tr>\n";
                            }
                        } else {
                            if ($lesson->custom and 
                                $page->qtype != LESSON_BRANCHTABLE and 
                                $page->qtype != LESSON_ENDOFBRANCH and
                                $page->qtype != LESSON_CLUSTER and 
                                $page->qtype != LESSON_ENDOFCLUSTER) {
                                echo "<tr><td align=\"right\" width=\"20%\"><b>".get_string("score", "lesson")." $i:";
                                echo "</b></td><td width=\"80%\">\n";
                                echo "$answer->score</td></tr>\n";
                            }
                            echo "<tr><td align=\"right\" width=\"20%\"><b>".get_string("jump", "lesson")." $i:";
                            echo "</b></td><td width=\"80%\">\n";
                            echo "$jumptitle</td></tr>\n";
                        }
                        $i++;
                    }
                    // print_simple_box_end();  // not sure if i commented this out... hehe
                    echo "<tr><td colspan=\"2\" align=\"center\">";
                    if ($page->qtype != LESSON_ENDOFBRANCH) {
                        echo "<input type=\"button\" value=\"";
                        if ($page->qtype == LESSON_BRANCHTABLE) {
                            echo get_string("checkbranchtable", "lesson");
                        } else {
                            echo get_string("checkquestion", "lesson");
                        }
                        echo "\" onclick=\"document.lessonpages.pageid.value=$page->id;".
                            "document.lessonpages.submit();\" />";
                    }
                    echo "&nbsp;</td></tr>\n";
                }
                echo "</table></td></tr>\n";
                if (has_capability('mod/lesson:edit', $context)) {
                    echo "<tr><td align=\"left\"><small><a href=\"import.php?id=$cm->id&amp;pageid=$page->id\">".
                        get_string("importquestions", "lesson")."</a> | ".    
                         "<a href=\"lesson.php?id=$cm->id&amp;sesskey=".$USER->sesskey."&amp;action=addcluster&amp;pageid=$page->id\">".
                         get_string("addcluster", "lesson")."</a> | ".
                         "<a href=\"lesson.php?id=$cm->id&amp;sesskey=".$USER->sesskey."&amp;action=addendofcluster&amp;pageid=$page->id\">".
                         get_string("addendofcluster", "lesson")."</a> | ".
                         "<a href=\"lesson.php?id=$cm->id&amp;action=addbranchtable&amp;pageid=$page->id\">".
                        get_string("addabranchtable", "lesson")."</a><br />";
                    // the current page or the next page is an end of branch don't show EOB link
                    $nextqtype = 0; // set to anything else EOB
                    if ($page->nextpageid) {
                        $nextqtype = get_field("lesson_pages", "qtype", "id", $page->nextpageid);
                    }
                    if (($page->qtype != LESSON_ENDOFBRANCH) and ($nextqtype != LESSON_ENDOFBRANCH)) {
                        echo "<a href=\"lesson.php?id=$cm->id&amp;sesskey=".$USER->sesskey."&amp;action=addendofbranch&amp;pageid=$page->id\">".
                        get_string("addanendofbranch", "lesson")."</a> | ";
                    }
                    echo "<a href=\"lesson.php?id=$cm->id&amp;action=addpage&amp;pageid=$page->id\">".
                        get_string("addaquestionpage", "lesson")." ".get_string("here","lesson").
                        "</a></small></td></tr>\n";
                }
//                echo "<tr><td>\n";
                // check the prev links - fix (silently) if necessary - there was a bug in
                // versions 1 and 2 when add new pages. Not serious then as the backwards
                // links were not used in those versions
                if (isset($prevpageid)) {
                    if ($page->prevpageid != $prevpageid) {
                        // fix it
                        set_field("lesson_pages", "prevpageid", $prevpageid, "id", $page->id);
                        if ($CFG->debug) {
                            echo "<p>***prevpageid of page $page->id set to $prevpageid***";
                        }
                    }
                }
                $prevpageid = $page->id;
                // move to next page
                if($singlePage) {  // this will make sure only one page is displayed if needed
                    break;
                } elseif($branch && $page->qtype == LESSON_ENDOFBRANCH) {  // this will display a branch table and its contents
                    break;
                } elseif ($page->nextpageid) {
                    if (!$page = get_record("lesson_pages", "id", $page->nextpageid)) {
                        error("Teacher view: Next page not found!");
                    }
                } else {
                    // last page reached
                    break;
                }
            }
        } // end of else from above collapsed code!!!
        
            echo "</table></form>\n";
        } 
    }

    /*******************high scores **************************************/
    elseif ($action == 'highscores') {
        print_heading_with_help(format_string($lesson->name,true), "overview", "lesson");

        if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id", "completed")) {
            $grades = array();
        }
        
        print_heading(get_string("topscorestitle", "lesson", $lesson->maxhighscores), 'center', 4);

        if (!$highscores = get_records_select("lesson_high_scores", "lessonid = $lesson->id")) {
            print_heading(get_string("nohighscores", "lesson"), 'center', 3);
        } else {
            foreach ($highscores as $highscore) {
                $grade = $grades[$highscore->gradeid]->grade;
                $topscores[$grade][] = $highscore->nickname;
            }
            krsort($topscores);
                       
            $table = new stdClass;
            $table->align = array('center', 'left', 'right');
            $table->wrap = array();
            $table->width = "30%";
            $table->cellspacing = '10px';
            $table->size = array('*', '*', '*');
            
            $table->head = array(get_string("rank", "lesson"), $course->students, get_string("scores", "lesson"));
            
            $printed = 0;
            while (true) {
                $temp = current($topscores);
                $score = key($topscores);
                $rank = $printed + 1;
                sort($temp); 
                foreach ($temp as $student) {
                    $table->data[] = array($rank, $student, $score);
                }
                $printed++;
                if (!next($topscores) || !($printed < $lesson->maxhighscores)) { 
                    break;
                }
            }
            print_table($table);
        }
        
        if (!has_capability('mod/lesson:manage', $context)) {  // teachers don't need the links
            echo '<div align="center">';
            if (optional_param('link', 0, PARAM_INT)) {
                echo "<br /><div class=\"lessonbutton standardbutton\"><a href=\"../../course/view.php?id=$course->id\">".get_string("returntocourse", "lesson")."</a></div>";
            } else {
                echo "<br /><span class=\"lessonbutton standardbutton\"><a href=\"../../course/view.php?id=$course->id\">".get_string("cancel", "lesson").'</a></span> '.
                    " <span class=\"lessonbutton standardbutton\"><a href=\"view.php?id=$cm->id&amp;action=navigation\">".get_string("startlesson", "lesson").'</a></span>';
            }
            echo "</div>";
        }
    }
    /*******************update high scores **************************************/
    elseif ($action == 'updatehighscores') {
        print_heading_with_help(format_string($lesson->name,true), "overview", "lesson");
    
        confirm_sesskey();

        if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id", "completed")) {
            error("Error: could not find grades");
        }
        if (!$usergrades = get_records_select("lesson_grades", "lessonid = $lesson->id and userid = $USER->id", "completed DESC")) {
            error("Error: could not find grades");
        }
        echo "<div align=\"center\">";
        echo get_string("waitpostscore", "lesson")."<br>";
        
        foreach ($usergrades as $usergrade) {
            // get their latest grade
            $newgrade = $usergrade;
            break;
        }
        
        if ($pasthighscore = get_record_select("lesson_high_scores", "lessonid = $lesson->id and userid = $USER->id")) {
            $pastgrade = $grades[$pasthighscore->gradeid]->grade;
            if ($pastgrade >= $newgrade->grade) {
                redirect("view.php?id=$cm->id&amp;action=highscores&amp;link=1", "Update Successful");
            } else {
                // delete old and find out where new one goes
                if (!delete_records("lesson_high_scores", "id", $pasthighscore->id)) {
                    error("Error: could not delete old high score");
                }
            }
        }
        // find out if we need to delete any records
        if ($highscores = get_records_select("lesson_high_scores", "lessonid = $lesson->id")) {  // if no high scores... then just insert our new one
            foreach ($highscores as $highscore) {
                $grade = $grades[$highscore->gradeid]->grade;
                $topscores[$grade][] = $highscore->userid;
            }
            if (!(count($topscores) < $lesson->maxhighscores)) { // if the top scores list is not full then dont need to worry about removing old scores
                $scores = array_keys($topscores);
                $flag = true;                
                // see if the new score is already listed in the top scores list
                // if it is listed, then dont need to delete any records
                foreach ($scores as $score) {
                    if ($score = $newgrade->grade) {
                        $flag = false;
                    }
                }    
                if ($flag) { // if the score does not exist in the top scores list, then the lowest scores get thrown out.
                    ksort($topscores); // sort so the lowest score is first element
                    $lowscore = current($topscores);
                    // making a delete statement to delete all users with the lowest score
                    $deletestmt = 'lessonid = '. $lesson->id .' and userid = ';
                    $deletestmt .= current($lowscore);
                    while (next($lowscore)) {
                        $deletestmt .= " or userid = ".current($lowscore);
                    }
                    if (!delete_records_select('lesson_high_scores', $deletestmt)) {
                        /// not a big deal...
                        error('Did not delete extra high score(s)');
                    }
                }
            }
        }
        
        $newhighscore = new stdClass;
        $newhighscore->lessonid = $lesson->id;
        $newhighscore->userid = $USER->id;
        $newhighscore->gradeid = $newgrade->id;
        $newhighscore->nickname = optional_param('name', '', PARAM_CLEAN);
        
        if (!insert_record("lesson_high_scores", $newhighscore)) {
            error("Insert of new high score Failed!");
        }
        
        redirect("view.php?id=$cm->id&amp;action=highscores&amp;link=1", get_string("postsuccess", "lesson"));
        echo "</div>";
    }
    /*******************name for highscores **************************************/
    elseif ($action == 'nameforhighscores') {
        print_heading_with_help(format_string($lesson->name,true), "overview", "lesson");
        echo "<div align=\"center\">";
        if ($name = trim(optional_param('name', '', PARAM_CLEAN))) {
            if (lesson_check_nickname($name)) {
                redirect("view.php?id=$cm->id&amp;action=updatehighscores&amp;name=$name&amp;sesskey=".$USER->sesskey, get_string("nameapproved", "lesson"));
            } else {
                echo get_string("namereject", "lesson")."<br /><br />";
            }
        }
                
        echo "<form name=\"nickname\" method =\"post\" action=\"view.php\">";
        echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />";
        echo "<input type=\"hidden\" name=\"action\" value=\"nameforhighscores\" />";
        
        echo get_string("entername", "lesson").": <input type=\"text\" name=\"name\" maxlength=\"5\"><br />";
        echo "<input type=\"submit\" value=\"".get_string("submitname", "lesson")."\" />";
        echo "</form>";
        echo "</div>";
    }    
    /*************** no man's land **************************************/
    else {
        error("Fatal Error: Unknown Action: ".$action."\n");
    }
/// Finish the page
    print_footer($course);

?>
