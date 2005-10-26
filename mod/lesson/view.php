<?PHP  // $Id$

/// This page prints a particular instance of lesson
/// (Replace lesson with the name of your module)

    require_once('../../config.php');
    require_once('locallib.php');
    require_once('lib.php');

    $id = required_param('id', PARAM_INT);    // Course Module ID
    $pageid = optional_param('pageid', NULL, PARAM_INT);    // Lesson Page ID

    if (! $cm = get_record('course_modules', 'id', $id)) {
        error('Course Module ID was incorrect');
    }

    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }

    if (! $lesson = get_record('lesson', 'id', $cm->instance)) {
        error('Course module is incorrect');
    }

    require_login($course->id, false, $cm);

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
        if (isteacher($course->id)) {
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
    if (isteacheredit($course->id)) {
        $button = '<table><tr><td>';
        $button .= '<form target="'. $CFG->framename .'" method="get" action="'. $CFG->wwwroot .'/course/mod.php">'.
               '<input type="hidden" name="sesskey" value="'. $USER->sesskey .'" />'.
               '<input type="hidden" name="update" value="'. $cm->id .'" />'.
               '<input type="hidden" name="return" value="true" />'.
               '<input type="submit" value="'. get_string('editlessonsettings', 'lesson') .'" /></form>';
        if ($action == 'navigation' && $pageid != LESSON_EOL) {
            $currentpageid = $pageid;  // very important not to alter $pageid.
            if (empty($currentpageid)) {
                if (!$currentpageid = get_field('lesson_pages', 'id', 'lessonid', $lesson->id, 'prevpageid', 0)) {
                    error('Navigation: first page not found');
                }
            }
            $button .= '</td><td>'.
                   '<form target="'. $CFG->framename .'" method="get" action="'. $CFG->wwwroot .'/mod/lesson/lesson.php">'.
                   '<input type="hidden" name="id" value="'. $cm->id .'" />'.
                   '<input type="hidden" name="action" value="editpage" />'.
                   '<input type="hidden" name="pageid" value="'. $currentpageid .'" />'.
                   '<input type="submit" value="'. get_string('editpagecontent', 'lesson') .'" /></form>';
        }
        $button .= '</td></tr></table>';
    } else {
        $button = '';
    }

    print_header($course->shortname .': '. format_string($lesson->name), $course->fullname,
                 "$navigation <a href=\"index.php?id=$course->id\">$strlessons</a> -> <a href=\"view.php?id=$cm->id\">".format_string($lesson->name,true)."</a>", '', '', true,
                 $button, // took out update_module_button($cm->id, $course->id, $strlesson) and replaced it with $button
                  navmenu($course, $cm));

    // set up some general variables
    $usehtmleditor = can_use_html_editor();
    $path = $CFG->wwwroot .'/course';

    /************** navigation **************************************/
    if ($action == 'navigation') {
        // password protected lesson code
        if ($lesson->usepassword && !isteacher($course->id)) {
            $correctpass = false;
            if (isset($_POST['userpassword'])) {
                if ($lesson->password == md5(trim(clean_param($_POST['userpassword'], PARAM_CLEAN)))) {
                    $USER->lessonloggedin[$lesson->id] = true;
                    $correctpass = true;
                }
            } elseif (isset($USER->lessonloggedin[$lesson->id])) {
                $correctpass = true;
            }

            if (!$correctpass) {
                print_simple_box_start('center');
                echo '<form name="password" method="post" action="view.php">' . "\n";
                echo '<input type="hidden" name="id" value="'. $cm->id .'" />' . "\n";
                echo '<input type="hidden" name="action" value="navigation" />' . "\n";
                echo '<table cellpadding="7px">';
                if (isset($_POST['userpassword'])) {
                    echo "<tr align=\"center\" style='color:#DF041E;'><td>".get_string('loginfail', 'lesson') .'</td></tr>';
                }
                echo '<tr align="center"><td>'. get_string('passwordprotectedlesson', 'lesson', format_string($lesson->name)) .'</td></tr>';
                echo '<tr align="center"><td>'. get_string('enterpassword', 'lesson').' <input type="password" name="userpassword" /></td></tr>';
                        
                echo '<tr align="center"><td>';
                echo '<span class="lessonbutton standardbutton"><a href="'.$CFG->wwwroot.'/course/view.php?id='. $course->id .'">'. get_string('cancel', 'lesson') .'</a></span> ';
                echo ' <span class="lessonbutton standardbutton"><a href="javascript:document.password.submit();">'. get_string('continue', 'lesson') .'</a></span>';
                echo '</td></tr></table>';
                print_simple_box_end();
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
            // check for dependencies first
            if ($lesson->dependency and !isteacher($course->id)) {
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
            if(!isset($USER->startlesson[$lesson->id]) && !isteacher($course->id)) {
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
                $path_parts = pathinfo($lesson->mediafile);
                $name = substr($path_parts['basename'], 0, strstr($path_parts['basename'], '.'));
                $options = 'menubar=0,location=0,left=5,top=5,scrollbars,resizable,width='. 650 .',height='. 100;
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
                if (!isteacher($course->id)) {
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
            if (!isteacher($course->id)) {
                $lesson->displayleft = lesson_displayleftif($lesson);
            }
            
            // start of left menu
            if ($lesson->displayleft) {
               echo '<table><tr valign="top"><td>';
               if($firstpageid = get_field('lesson_pages', 'id', 'lessonid', $lesson->id, 'prevpageid', 0)) {
                        // print the pages
                        echo '<form name="lessonpages2" method="post" action="view.php">'."\n";
                        echo '<input type="hidden" name="id" value="'. $cm->id .'" />'."\n";
                        echo '<input type="hidden" name="action" value="navigation" />'."\n";
                        echo '<input type="hidden" name="pageid" />'."\n";
                            echo '<div class="leftmenu_container">'."\n";
                                echo '<div class="leftmenu_title">'.get_string('lessonmenu', 'lesson').'</div>'."\n";
                                echo '<div class="leftmenu_courselink">';
                                echo "<a href=\"../../course/view.php?id=$course->id\">".get_string("mainmenu", "lesson")."</a>";
                                echo '</div>'."\n";
                                echo '<div class="leftmenu_links">'."\n";
                                lesson_print_tree_menu($lesson->id, $firstpageid, $cm->id);
                                echo '</div>'."\n";
                            echo '</div>'."\n";
                        echo '</form>'."\n";
                }
                if ($page->qtype == LESSON_BRANCHTABLE) {
                    $width = '';
                } else {
                    $width = ' width="100%" ';
                }
                echo '</td><td align="center" '.$width.'>';
            } elseif ($lesson->slideshow && $page->qtype == LESSON_BRANCHTABLE) {
                echo '<table align="center"><tr><td>';  // only want this if no left menu
            }

            // starts the slideshow div
            if($lesson->slideshow && $page->qtype == LESSON_BRANCHTABLE) { 
                echo "<div class=\"slideshow\" style=\"
                        background-color: $lesson->bgcolor;
                        height: ".$lesson->height."px;
                        width: ".$lesson->width."px;
                        \">\n";
                echo "<table align=\"center\" width=\"100%\" border=\"0\"><tr><td>\n";
            } else {
                echo "<table align=\"center\" width=\"100%\" border=\"0\"><tr><td>\n";
                $lesson->slideshow = false; // turn off slide show for all pages other than LESSON_BRANTCHTABLE
            }

            // This is where several messages (usually warnings) are displayed
            // all of this is displayed above the actual page
            
            if (!empty($lesson->mediafile)) {
                $url = '/mod/lesson/mediafile.php?id='.$cm->id;
                $options = 'menubar=0,location=0,left=5,top=5,scrollbars,resizable,width='. 650 .',height='. 100;
                $path_parts = pathinfo($lesson->mediafile);
                $name = substr($path_parts['basename'], 0, strstr($path_parts['basename'], '.'));
                echo '<div align="right">';
                link_to_popup_window ($url, $name, get_string('mediafilepopup', 'lesson'), '', '', get_string('mediafilepopup', 'lesson'), $options);
                helpbutton("mediafilestudent", get_string("mediafile", "lesson"), "lesson");
                echo '</div>';
            }
            // clock code
            // get time information for this user
            if(!isteacher($course->id)) {
                if (!$timer = get_records_select('lesson_timer', "lessonid = $lesson->id AND userid = $USER->id", 'starttime')) {
                    error('Error: could not find records');
                } else {
                    $timer = array_pop($timer); // this will get the latest start time record
                }
            }
    
            if (isset($_POST['startlastseen'])) {
                if ($_POST['startlastseen'] == 'yes') {  // continue a previous test, need to update the clock  (think this option is disabled atm)
                    $timer->starttime = time() - ($timer->lessontime - $timer->starttime);
                    $timer->lessontime = time();
                } elseif ($_POST['startlastseen'] == 'no') {  // starting over
                    // starting over, so reset the clock
                    $timer->starttime = time();
                    $timer->lessontime = time();
                }
            }
                
            // for timed lessons, display clock
            if ($lesson->timed) {
                if(isteacher($course->id)) {
                    echo '<p align="center">'. get_string('teachertimerwarning', 'lesson') .'<p>';
                } else {
                    if ((($timer->starttime + $lesson->maxtime * 60) - time()) > 0) {
                        // code for the clock
                        print_simple_box_start("right", "150px", "#ffffff", 0);
                        echo "<table border=\"0\" valign=\"top\" align=\"center\" class=\"generaltable\" width=\"100%\" cellspacing=\"0\">".
                            "<tr><th valign=\"top\" class=\"generaltableheader\">".get_string("timeremaining", "lesson").
                            "</th></tr><tr><td align=\"center\" class=\"generaltablecell\">";
                        echo "<script language=\"javascript\">\n";
                            echo "var starttime = ". $timer->starttime . ";\n";
                            echo "var servertime = ". time() . ";\n";
                            echo "var testlength = ". $lesson->maxtime * 60 .";\n";
                            echo "document.write('<SCRIPT LANGUAGE=\"JavaScript\" SRC=\"timer.js\"><\/SCRIPT>');\n";
                            echo "window.onload = function () { show_clock(); }\n";
                        echo "</script>\n";
                        echo "</td></tr></table>";
                        print_simple_box_end();
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
            if (!isteacher($course->id)) {
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
                            if (!isteacher($course->id)) {
                                $answer->jumpto = lesson_cluster_jump($lesson->id, $USER->id, $pageid);
                            } else {
                                if ($page->nextpageid == 0) {  
                                    $answer->jumpto = LESSON_EOL;
                                } else {
                                    $answer->jumpto = $page->nextpageid;
                                }
                            }
                        } else if ($answer->jumpto = LESSON_NEXTPAGE) {
                            if ($page->nextpageid == 0) {  
                                $answer->jumpto = LESSON_EOL;
                            } else {
                                $answer->jumpto = $page->nextpageid;
                            }
                        } else if ($answer->jumpto = 0) {
                            $answer->jumpto = $page->id;
                        } else if ($answer->jumpto = LESSON_PREVIOUSPAGE) {
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
            if(isteacher($course->id)) {
                if (execute_teacherwarning($lesson->id)) {
                    $warningvars->cluster = get_string('clusterjump', 'lesson');
                    $warningvars->unseen = get_string('unseenpageinbranch', 'lesson');
                    echo '<p align="center">'. get_string('teacherjumpwarning', 'lesson', $warningvars) .'</p>';
                }
            }
            
            /// this calculates the ongoing score
            if ($lesson->ongoing && !empty($pageid)) {
                if (isteacher($course->id)) {
                    echo "<p align=\"center\">".get_string('teacherongoingwarning', 'lesson').'</p>';
                } else {
                    $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id);
                    if (isset($USER->modattempts[$lesson->id])) {
                        $ntries--;
                    }
                    lesson_calculate_ongoing_score($lesson, $USER->id, $ntries);                
                }
            }
            
            if ($page->qtype == LESSON_BRANCHTABLE) {
                if ($lesson->minquestions and isstudent($course->id)) {
                    // tell student how many questions they have seen, how many are required and their grade
                    $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id);
                    $nviewed = count_records("lesson_attempts", "lessonid", $lesson->id, "userid", 
                            $USER->id, "retry", $ntries);
                    if ($nviewed) {
                        echo "<p align=\"center\">".get_string("numberofpagesviewed", "lesson", $nviewed).
                                "; (".get_string("youshouldview", "lesson", $lesson->minquestions).")<br />";
                        // count the number of distinct correct pages
                        if ($correctpages = get_records_select("lesson_attempts",  "lessonid = $lesson->id
                                AND userid = $USER->id AND retry = $ntries AND correct = 1")) {
                            foreach ($correctpages as $correctpage) {
                                $temp[$correctpage->pageid] = 1;
                            }
                            $ncorrect = count($temp);
                        } else {
                            $nccorrect = 0;
                        }
                        if ($nviewed < $lesson->minquestions) {
                            $nviewed = $lesson->minquestions;
                        }
                        echo get_string("numberofcorrectanswers", "lesson", $ncorrect)."<br />\n";
                        $thegrade = intval(100 * $ncorrect / $nviewed);
                        echo get_string("yourcurrentgradeis", "lesson", 
                                number_format($thegrade * $lesson->grade / 100, 1)).
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
                switch ($page->qtype) {
                    case LESSON_SHORTANSWER :
                    case LESSON_NUMERICAL :
                        if (isset($USER->modattempts[$lesson->id])) {     
                            $value = "value=\"$attempt->useranswer\"";
                        } else {
                            $value = "";
                        }       
                        echo "<tr><td align=\"center\">".get_string("youranswer", "lesson").
                            ": <label for=\"answer\" class=\"hidden-label\">Answer</label><input type=\"text\" id=\"answer\" name=\"answer\" size=\"50\" maxlength=\"200\" $value />\n";
                        echo '</table>';
                        print_simple_box_end();
                        echo "<div align=\"center\" class=\"lessonbutton standardbutton\"><a href=\"javascript:document.answerform.submit();\">".
                             get_string("pleaseenteryouranswerinthebox", "lesson")."</a></div></p>\n";
                        break;
                    case LESSON_TRUEFALSE :
                        shuffle($answers);
                        foreach ($answers as $answer) {
                            echo "<tr><td valign=\"top\">";
                            if (isset($USER->modattempts[$lesson->id]) && $answer->id == $attempt->answerid) {
                                $checked = "checked=\"checked\"";
                            } else {
                                $checked = "";
                            } 
                            echo "<label for=\"answerid\" class=\"hidden-label\">Answer ID</label><input type=\"radio\" id=\"answerid\" name=\"answerid\" value=\"{$answer->id}\" $checked />";
                            echo "</td><td>";
                            $options = new stdClass;
                            $options->para = false; // no <p></p>
                            $options->noclean = true;
                            echo format_text(trim($answer->answer), FORMAT_MOODLE, $options);
                            echo "</td></tr>";
                            if ($answer != end($answers)) {
                                echo "<tr><td><br></td></tr>";                            
                            } 
                        }
                        echo '</table>';
                        print_simple_box_end();
                        echo "<div align=\"center\" class=\"lessonbutton standardbutton\"><a href=\"javascript:document.answerform.submit();\">".
                            get_string("pleasecheckoneanswer", "lesson")."</a></div>\n"; 
                        break;
                    case LESSON_MULTICHOICE :
                        $i = 0;
                        shuffle($answers);
                        foreach ($answers as $answer) {
                            echo "<tr><td valign=\"top\">";
                            if ($page->qoption) {
                                $checked = "";
                                if (isset($USER->modattempts[$lesson->id])) {
                                    $answerids = explode(",", $attempt->useranswer);
                                    if (in_array($answer->id, $answerids)) {
                                        $checked = "checked=\"checked\"";
                                    } else {
                                        $checked = "";
                                    }
                                }
                                // more than one answer allowed 
                                echo "<label for=\"answer[$i]\" class=\"hidden-label\">answer[$i]</label><input type=\"checkbox\" id=\"answer[$i]\" name=\"answer[$i]\" value=\"{$answer->id}\" $checked />";
                            } else {
                                if (isset($USER->modattempts[$lesson->id]) && $answer->id == $attempt->answerid) {
                                    $checked = "checked=\"checked\"";
                                } else {
                                    $checked = "";
                                } 
                                // only one answer allowed
                                echo "<label for=\"answerid\" class=\"hidden-label\">answer id</label><input type=\"radio\" id=\"answerid\" name=\"answerid\" value=\"{$answer->id}\" $checked />";
                            }
                            echo "</td><td>";
                            $options = new stdClass;
                            $options->para = false; // no <p></p>
                            $options->noclean = true;
                            echo format_text(trim($answer->answer), FORMAT_MOODLE, $options); 
                            echo "</td></tr>";
                            if ($answer != end($answers)) {
                                echo "<tr><td><br></td></tr>";                            
                            } 
                            $i++;
                        }
                        echo '</table>';
                        print_simple_box_end();
                        if ($page->qoption) {
                            echo "<div align=\"center\" class=\"lessonbutton standardbutton\"><a href=\"javascript:document.answerform.submit();\">".
                                get_string("pleasecheckoneormoreanswers", "lesson")."</a></div>\n";
                        } else {
                            echo "<div align=\"center\" class=\"lessonbutton standardbutton\"><a href=\"javascript:document.answerform.submit();\">".
                                get_string("pleasecheckoneanswer", "lesson")."</a></div>\n";
                        }
                        break;
                        
                    case LESSON_MATCHING :
                        echo "<tr><td><table width=\"100%\">";
                        // don't suffle answers (could be an option??)
                        foreach ($answers as $answer) {
                            // get all the response
                            if ($answer->response != NULL) {
                                $responses[] = trim($answer->response);
                            }
                        }
                        shuffle($responses);
                        $responses = array_unique($responses);

                        if (isset($USER->modattempts[$lesson->id])) {
                            $useranswers = explode(",", $attempt->useranswer);
                            $t = 0;
                        }
                        foreach ($answers as $answer) {
                            if ($answer->response != NULL) {
                                echo "<tr><td align=\"right\">";
                                $options = new stdClass;
                                $options->para = false;
                                $options->noclean = true;
                                echo "<b>".format_text($answer->answer,FORMAT_MOODLE,$options).": </b></td><td valign=\"bottom\">";
                                echo "<label for=\"response[$answer->id]\" class=\"hidden-label\">response[$answer->id]</label><select id=\"response[$answer->id]\" name=\"response[$answer->id]\">";
                                if (isset($USER->modattempts[$lesson->id])) {
                                    $selected = trim($answers[$useranswers[$t]]->response);                                    
                                    echo "<option value=\"".htmlspecialchars($response)."\" selected=\"selected\">$selected</option>";
                                    foreach ($responses as $response) {
                                        if (trim($answers[$useranswers[$t]]->response) != $response) {
                                            echo "<option value=\"".htmlspecialchars($response)."\">$response</option>";
                                        }
                                    }
                                    $t++;
                                } else {
                                    echo "<option value=\"0\" selected=\"selected\">Choose...</option>";
                                    foreach ($responses as $response) {
                                        echo "<option value=\"".htmlspecialchars($response)."\">$response</option>";
                                    }
                                }
                                echo "</select>";
                                echo "</td></tr>";
                                if ($answer != end($answers)) {
                                    echo "<tr><td><br></td></tr>";                            
                                } 
                            }
                        }
                        echo '</table></table>';
                        print_simple_box_end();
                        echo "<div align=\"center\" class=\"lessonbutton standardbutton\"><a href=\"javascript:document.answerform.submit();\">".
                            get_string("pleasematchtheabovepairs", "lesson")."</a></div>\n";
                        break;
                    case LESSON_BRANCHTABLE :                        
                        $options = new stdClass;
                        $options->para = false;
                        $buttons = array('next' => '', 'prev' => '', 'other' => array());
                        // seperate out next and previous jumps from the other jumps 
                        foreach ($answers as $answer) {
                            if ($answer->jumpto == LESSON_NEXTPAGE) {
                                $buttons['next'] = '<div class="lessonbutton nextbutton"><a href="javascript:document.answerform.jumpto.value='.$answer->jumpto.';document.answerform.submit();">'.
                                        strip_tags(format_text($answer->answer, FORMAT_MOODLE, $options)).'</a></div>';
                            } else if ($answer->jumpto == LESSON_PREVIOUSPAGE) {
                                $buttons['prev'] = '<div class="lessonbutton previousbutton"><a href="javascript:document.answerform.jumpto.value='.$answer->jumpto.';document.answerform.submit();">'.
                                        strip_tags(format_text($answer->answer, FORMAT_MOODLE, $options)).'</a></div>';
                            } else {
                                $buttons['other'][] = '<div class="lessonbutton standardbutton"><a href="javascript:document.answerform.jumpto.value='.$answer->jumpto.';document.answerform.submit();">'.
                                        strip_tags(format_text($answer->answer, FORMAT_MOODLE, $options)).'</a></div>';
                            }
                        }
                        
                        if ($lesson->slideshow) {
                            $px = $lesson->width - 30; // give us some breathing room
                            $width = ' width="'.$px.'px"';
                        } else {
                            $width = ' width="100%"';
                        }
                        
                        //$fullbuttonhtml = '<div class="branchbuttoncontainer">'."\n";
                        $fullbuttonhtml = '';
                        if ($page->layout) {
                            // tried to do this with CSS, but couldnt get it to work in MacIE browser.  Using tables instead :(
                            // don't care if empty or not because we want to keep the table structure
                            $fullbuttonhtml .= '<table '.$width.' align="center"><tr><td align="left" width="20%">'.$buttons['prev']."</td>\n";
                            $fullbuttonhtml .= '<td><table align="center"><tr><td>'.implode("</td>\n<td>", $buttons['other'])."</td></tr></table></td>\n";
                            $fullbuttonhtml .= '<td align="right" width="20%">'.$buttons['next']."</td></tr></table>\n";
                        } else {
                            // care about emptyness here
                            $temparray = array();
                            if (!empty($buttons['next'])) {
                                $temparray[] = $buttons['next'];
                            }
                            if (!empty($buttons['other'])) {
                                $temparray = array_merge($temparray, $buttons['other']);
                            }
                            if (!empty($buttons['prev'])) {
                                $temparray[] = $buttons['prev'];
                            }
                            $fullbuttonhtml .= '<table align="center" cellpadding="3px"><tr><td align="center">'.
                                                implode("</td></tr>\n<tr><td align=\"center\">", $temparray).
                                                "</td></tr></table>\n";
                        }
                        //$fullbuttonhtml .= "</div>\n";
                    
                        if ($lesson->slideshow) {
                            echo '<div class="branchslidetop">' . $fullbuttonhtml . '</div>';
                            $options = new stdClass;
                            $options->noclean = true;
                            echo '<div class="contents">'.format_text($page->contents, FORMAT_MOODLE, $options).format_text($page->contents).'</div>';;
                            echo "</table></div><table cellpadding=\"5\" cellspacing=\"5\" align=\"center\">";
                        } else {
                            echo "<tr><td><table width=\"100%\">";
                        }
                        echo "<input type=\"hidden\" name=\"jumpto\" />";
                        
                        if (!$lesson->slideshow) {
                            if (!empty($buttons['next']) or !empty($buttons['prev'])) {
                                print_simple_box_start("center", '100%');
                            } else {
                                print_simple_box_start("center");
                            }
                            
                            echo '<div class="branchbuttoncontainer">' . $fullbuttonhtml . '</div>';
                            echo '</table></table>';
                            print_simple_box_end();
                        } else {
                            echo '<div class="branchslidebottom">' . $fullbuttonhtml . '</div>';
                        }
                        break;
                    case LESSON_ESSAY :
                        if (isset($USER->modattempts[$lesson->id])) {
                            $essayinfo = unserialize($attempt->useranswer);
                            $value = $essayinfo->answer;
                        } else {
                            $value = "";
                        }
                        echo "<tr><td align=\"center\" valign=\"top\" nowrap>".get_string("youranswer", "lesson").":</td><td>".
                             "<label for=\"answer\" class=\"hidden-label\">Answer</label><textarea id=\"answer\" name=\"answer\" rows=\"15\" cols=\"60\">$value</textarea>\n";
                        echo "</td></tr></table>";
                        print_simple_box_end();
                        echo "<div align=\"center\" class=\"lessonbutton standardbutton\"><a href=\"javascript:document.answerform.submit();\">".
                             get_string("pleaseenteryouranswerinthebox", "lesson")."</a></div>\n";
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
            if ($lesson->timed && !isteacher($course->id)) {
                if (isset($_GET["outoftime"])) {
                    if ($_GET["outoftime"] == "normal") {
                        print_simple_box(get_string("eolstudentoutoftime", "lesson"), "center");
                    }
                }
            }

            // Update the clock / get time information for this user
            if (!isteacher($course->id)) {
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
            if (isstudent($course->id)) {
                if ($nviewed = count_records("lesson_attempts", "lessonid", $lesson->id, "userid", 
                        $USER->id, "retry", $ntries)) {
                    if (!$lesson->custom) {
                        $ncorrect = 0;                        
                        $temp = array();
                        // count the number of distinct correct pages
                        if ($correctpages = get_records_select("lesson_attempts",  "lessonid = $lesson->id AND
                                userid = $USER->id AND retry = $ntries AND correct = 1")) {
                            foreach ($correctpages as $correctpage) {
                                $temp[$correctpage->pageid] = 1;
                            }
                            $ncorrect = count($temp);
                        }
                        echo "<p align=\"center\">".get_string("numberofpagesviewed", "lesson", $nviewed).
                            "</p>\n";
                        if ($lesson->minquestions) {
                            if ($nviewed < $lesson->minquestions) {
                                // print a warning and set nviewed to minquestions
                                echo "<p align=\"center\">".get_string("youshouldview", "lesson", 
                                        $lesson->minquestions)." ".get_string("pages", "lesson")."</p>\n";
                                $nviewed = $lesson->minquestions;
                            }
                        }
                        echo "<p align=\"center\">".get_string("numberofcorrectanswers", "lesson", $ncorrect).
                            "</p>\n";
                        $thegrade = round(100 * $ncorrect / $nviewed, 5);
                        echo "<p align=\"center\">".get_string("gradeis", "lesson", 
                                number_format($thegrade * $lesson->grade / 100, 1)).
                            " (".get_string("outof", "lesson", $lesson->grade).")</p>\n";
                        
                    } else {
                        $score = 0;
                        $essayquestions = 0;
                        $essayquestionpoints = 0;

                        if ($useranswers = get_records_select("lesson_attempts",  "lessonid = $lesson->id AND 
                                userid = $USER->id AND retry = $ntries", "timeseen")) {
                            // group each try with its page
                            foreach ($useranswers as $useranswer) {
                                $attemptset[$useranswer->pageid][] = $useranswer;                                
                            }
                            
                            $pageids = array_keys($attemptset);
                            $pageids = implode(",", $pageids);
                            
                            // get only the pages and their answers that the user answered
                            $answeredpages = get_records_select("lesson_pages", "lessonid = $lesson->id AND id IN($pageids)");
                            $pageanswers = get_records_select("lesson_answers", "lessonid = $lesson->id AND pageid IN($pageids)");

                            foreach ($attemptset as $attempts) {
                                if(count($attempts) > $lesson->maxattempts) { // if there are more tries than the max that is allowed, grab the last "legal" attempt
                                    $attempt = $attempts[$lesson->maxattempts - 1];
                                } else {
                                    // else, user attempted the question less than the max, so grab the last one
                                    $attempt = end($attempts);
                                }
                                // if essay question, handle it, otherwise add to score
                                if ($answeredpages[$attempt->pageid]->qtype == LESSON_ESSAY) {
                                    $essayinfo = unserialize($attempt->useranswer);
                                    $score += $essayinfo->score;
                                    $essayquestions++;
                                    $essayquestionpoints += $pageanswers[$attempt->answerid]->score;
                                } else {
                                    if (array_key_exists($attempt->answerid, $pageanswers)) {
                                        $score += $pageanswers[$attempt->answerid]->score;
                                    }
                                }
                            }
                            $bestscores = array();
                            // find the highest possible score per page
                            foreach ($pageanswers as $pageanswer) {
                                if(isset($bestscores[$pageanswer->pageid])) {
                                    if ($bestscores[$pageanswer->pageid] < $pageanswer->score) {
                                        $bestscores[$pageanswer->pageid] = $pageanswer->score;
                                    }
                                } else {
                                    $bestscores[$pageanswer->pageid] = $pageanswer->score;
                                }
                            }
                            
                            $bestscore = array_sum($bestscores);
                        }
                            
                        $thegrade = round(100 * $score / $bestscore, 5);
                        $a = new stdClass;
                        if ($essayquestions > 0) {
                            $a->score = $score;
                            $a->tempmaxgrade = $bestscore - $essayquestionpoints;
                            $a->essayquestions = $essayquestions;
                            $a->grade = $bestscore;
                            echo "<div align=\"center\">".get_string("displayscorewithessays", "lesson", $a)."</div>";
                        } else {
                            $a->score = $score;
                            $a->grade = $bestscore;
                            echo "<div align=\"center\">".get_string("displayscorewithoutessays", "lesson", $a)."</div>";                        
                        }
                        echo "<p align=\"center\">".get_string("gradeis", "lesson", 
                                number_format($thegrade * $lesson->grade / 100, 1)).
                            " (".get_string("outof", "lesson", $lesson->grade).")</p>\n";

                    }
                    $grade->lessonid = $lesson->id;
                    $grade->userid = $USER->id;
                    $grade->grade = $thegrade;
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
                        if (isset($_GET["outoftime"])) {
                            if ($_GET["outoftime"] == "normal") {
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
            if ($lesson->highscores && !isteacher($course->id) && !$lesson->practice) {
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

            if ($lesson->modattempts && !isteacher($course->id)) {
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
            } elseif ($lesson->modattempts && isteacher($course->id)) {
                echo "<p align=\"center\">".get_string("modattemptsnoteacher", "lesson")."</p>";                
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
        // link to grade essay questions and to report
        if ($userattempts = get_records("lesson_attempts", "lessonid", $lesson->id)) { // just check to see if anyone has answered any questions.
            $usercount = array();
            foreach ($userattempts as $userattempts) {
                $usercount[$userattempts->userid] = 0;
            }
            $a = new stdClass;
            $a->users = count($usercount);
            $a->usersname = $course->students;
            echo "<div align=\"right\"><a href=\"report.php?id=$cm->id\">".get_string("viewlessonstats", "lesson", $a)."</a></div>";
        }
        if ($essaypages = get_records_select("lesson_pages", "lessonid = $lesson->id AND qtype = ".LESSON_ESSAY)) { // get pages that are essay
            // get only the attempts that are in response to essay questions
            $essaypageids = implode(",", array_keys($essaypages)); // all the pageids in comma seperated list
            if ($essayattempts = get_records_select("lesson_attempts", "lessonid = $lesson->id AND pageid IN($essaypageids)")) {
                $studentessays = array();
                // makes an array that organizes essayattempts by grouping userid, then pageid, then try count
                foreach ($essayattempts as $essayattempt) {
                    $studentessays[$essayattempt->userid][$essayattempt->pageid][$essayattempt->retry][] = $essayattempt;            
                }
                $a = new stdClass;
                $a->notgradedcount = 0;
                $a->notsentcount = 0;
                foreach ($studentessays as $pages) {  // students
                    foreach ($pages as $tries) {  // pages
                        // go through each essay per page
                        foreach($tries as $try) {  // actual attempts
                            // make sure they didn't answer it more than the max number of attmepts
                            if (count($try) > $lesson->maxattempts) {
                                $essay = $try[$lesson->maxattempts-1];
                            } else {
                                $essay = end($try);
                            }
                            $essayinfo = unserialize($essay->useranswer);
                            if ($essayinfo->graded == 0) {
                                $a->notgradedcount++;
                            }
                            if ($essayinfo->sent == 0) {
                                $a->notsentcount++;
                            }
                        }
                    }
                }
                echo "<div align=\"right\"><a href=\"view.php?id=$cm->id&amp;action=essayview\">".get_string("gradeessay", "lesson", $a)."</a></div><br />";
            }
        }

        print_heading_with_help(format_string($lesson->name,true), "overview", "lesson");   

        // get number of pages
        if ($page = get_record_select("lesson_pages", "lessonid = $lesson->id AND prevpageid = 0")) {
            $npages = 1;
            while (true) {
                if ($page->nextpageid) {
                    if (!$page = get_record("lesson_pages", "id", $page->nextpageid)) {
                        error("Teacher view: Next page not found!");
                    }
                } else {
                    // last page reached
                    break;
                }
                $npages++;
            }
        }

        if (!$page = get_record_select("lesson_pages", "lessonid = $lesson->id AND prevpageid = 0")) {
            // if there are no pages give teacher the option to create a new page or a new branch table
            echo "<div align=\"center\">";
            if (isteacheredit($course->id)) {
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
            print_simple_box_end();
            echo "</div>";
        } else {
            print_heading("<a href=\"view.php?id=$cm->id&amp;action=navigation\">".get_string("checknavigation",
                "lesson")."</a>\n");
            // print the pages
            echo "<form name=\"lessonpages\" method=\"post\" action=\"view.php\">\n";
            echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
            echo "<input type=\"hidden\" name=\"action\" value=\"navigation\" />\n";
            echo "<input type=\"hidden\" name=\"pageid\" />\n";
            $branch = false;
            $singlePage = false;
            if($lesson->tree && !isset($_GET['display']) && !isset($_GET['viewAll'])) {  
                echo "<div align=\"center\">";
                    echo get_string("treeview", "lesson")."<br /><br />";
                    echo "<a href=\"view.php?id=$id&amp;viewAll=1\">".get_string("viewallpages", "lesson")."</a><br /><br />\n";
                    echo "<table><tr><td>";
                    lesson_print_tree($page->id, $lesson->id, $cm->id, $CFG->pixpath);
                    echo "</td></tr></table>";
                    echo "<br /><a href=\"view.php?id=$id&amp;viewAll=1\">".get_string("viewallpages", "lesson")."</a>\n";
                echo "</div>";
            } else {
                if(isset($_GET['display']) && !isset($_GET['viewAll'])) {
                    $display = clean_param($_GET['display'], PARAM_INT);
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
                    echo "<center><a href=\"view.php?id=$id&amp;viewAll=1\">".get_string("viewallpages", "lesson")."</a><br />\n";
                    echo "<a href=\"view.php?id=$id\">".get_string("backtreeview", "lesson")."</a><br />\n";
                    echo "<table cellpadding=\"5\" border=\"0\" width=\"80%\">\n";
                    if (isteacheredit($course->id)) {
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
                    if($lesson->tree) {
                        echo "<center><a href=\"view.php?id=$id\">".get_string("backtreeview", "lesson")."</a><br /></center>\n";
                    }    
                    echo "<table align=\"center\" cellpadding=\"5\" border=\"0\" width=\"80%\">\n";
                    if (isteacheredit($course->id)) {
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
                /// end tree code    (note, there is an "}" below for an else above)
            while (true) {
                echo "<tr><td>\n";
                echo "<table width=\"100%\" border=\"1\" class=\"generalbox\"><tr><th colspan=\"2\">".format_string($page->title)."&nbsp;&nbsp;\n";
                if (isteacheredit($course->id)) {
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
                            if ($lesson->custom && $page->qtype != LESSON_BRANCHTABLE && $page->qtype != LESSON_ENDOFBRANCH) {                        
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
                if (isteacheredit($course->id)) {
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
        } // end of else from above tree code!!!
        
            echo "</table></form>\n";
            if(isset($_GET['display']) && !isset($_GET['viewAll'])) {
                echo "<center><a href=\"view.php?id=$id&amp;viewAll=1\">".get_string("viewallpages", "lesson")."</a><br />\n";
            }
            if($lesson->tree && (isset($_GET['display']) || isset($_GET['viewAll']))) {
                echo "<center><a href=\"view.php?id=$id\">".get_string("backtreeview", "lesson")."</a><br /></center>\n";
            }
            print_heading("<a href=\"view.php?id=$cm->id&amp;action=navigation\">".get_string("checknavigation",
                        "lesson")."</a>\n");
        } 
    }

    /*******************essay view **************************************/ // 6/29/04
    elseif ($action == 'essayview') {
        print_heading_with_help(format_string($lesson->name,true), "overview", "lesson");

        // get lesson pages that are essay
        if (!$pages = get_records_select("lesson_pages", "lessonid = $lesson->id AND qtype = ".LESSON_ESSAY)) {
            error("Error: could not find lesson pages");
        }
        
        // get all the users who have taken this lesson, order by their last name
        if (!$users = get_records_sql("SELECT DISTINCT u.*
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}lesson_attempts a
                                 WHERE a.lessonid = '$lesson->id' and
                                       u.id = a.userid
                                 ORDER BY u.lastname")) {
            error("Error: could not find users");
        }
        
        // get only the attempts that are in response to essay questions
        $pageids = implode(",", array_keys($pages)); // all the pageids in comma seperated list
        if (!$essayattempts = get_records_select("lesson_attempts", "lessonid = $lesson->id AND pageid IN($pageids)")) {
            error ("No one has answered essay questions yet...");
        }
        // group all the essays by userid
        $studentessays = array();
        foreach ($essayattempts as $essay) {
            // not very nice :) but basically
            // this organizes the essays so I know how many times a student answered an essay per try and per page
            $studentessays[$essay->userid][$essay->pageid][$essay->retry][] = $essay;            
        }

           print_heading("<a href=\"view.php?id=$cm->id\">".get_string("gobacktolesson", "lesson")."</a>");
        $table = new stdClass;
        $table->head = array($course->students, get_string("essays", "lesson"), get_string("email", "lesson"));
        $table->align = array("left", "left", "left");
        $table->wrap = array("nowrap", "wrap", "nowrap");
        $table->width = "90%";
        $table->size = array("*", "70%", "*");             

        // get the student ids of the students who have answered the essay question
        $studentids = array_keys($studentessays);
        
        // cycle through all the ids
        foreach ($studentids as $id) {
            $studentname = $users[$id]->lastname.", ".$users[$id]->firstname;
            $essaylinks = array();
            // go through each essay
            foreach ($studentessays[$id] as $page => $tries) {
                // go through each essay per page
                foreach($tries as $try) {
                    // make sure they didn't answer it more than the max number of attmepts
                    if (count($try) > $lesson->maxattempts) {
                        $essay = $try[$lesson->maxattempts-1];
                    } else {
                        $essay = end($try);
                    }
                    $essayinfo = unserialize($essay->useranswer);
                    // different colors for all the states of an essay (graded, if sent, not graded)
                    if (!$essayinfo->graded) {
                        $style = "style='color:#DF041E;text-decoration:underline;'";
                    } elseif (!$essayinfo->sent) {
                        $style = "style='color:#006600;text-decoration:underline;'";
                    } else {
                        $style = "style='color:#999999;'";
                    }
                    // link for each essay
                    $essaylinks[] = "<a $style href=\"view.php?id=$cm->id&amp;action=essaygrade&attemptid=$essay->id\">".format_string($pages[$essay->pageid]->title,true)."</a>";
                }
            }
            // email link for this user
            $emaillink = "<a href=\"view.php?id=$cm->id&amp;action=emailessay&amp;userid=".$id."&amp;sesskey=".$USER->sesskey."\">".get_string("emailgradedessays", "lesson")."</a>";

            $table->data[] = array($studentname, implode(", ", $essaylinks), $emaillink);        
        }
        // email link for all users
        $emailalllink = "<a href=\"view.php?id=$cm->id&amp;action=emailessay&amp;sesskey=".$USER->sesskey."\">".get_string("emailallgradedessays", "lesson")."</a>";
        
        $table->data[] = array(" ", " ", $emailalllink);
        
        print_table($table);
    }
    
    /*******************grade essays **************************************/ // 6/29/04
    elseif ($action == 'essaygrade') {
        print_heading_with_help(format_string($lesson->name,true), "overview", "lesson");
        
        $attemptid = required_param('attemptid', PARAM_INT);

        if (!$essay = get_record("lesson_attempts", "id", $attemptid)) {
            error("Error: could not find attempt");
        }
        if (!$page = get_record("lesson_pages", "id", $essay->pageid)) {
            error("Error: could not find lesson pages");
        }
        if (!$student = get_record("user", "id", $essay->userid)) {
            error("Error: could not find users");
        }
        if (!$answer = get_record("lesson_answers", "lessonid", $lesson->id, "pageid", $page->id)) {
            error("Error: could not find answer");
        }


        echo "<form name=\"essaygrade\" method=\"post\" action=\"view.php\">\n";
        echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
        echo "<input type=\"hidden\" name=\"action\" />\n";
        echo "<input type=\"hidden\" name=\"attemptid\" value=\"$attemptid\" />\n";
        echo "<input type=\"hidden\" name=\"sesskey\" value=\"".$USER->sesskey."\" />\n";        
    
        // all tables will have these
        $table = new stdClass;
        $table->align = array("left");
        $table->wrap = array();
        $table->width = "70%";
        $table->size = array("100%");             
        
        
        $table->head = array(get_string("question", "lesson"));
        $options = new stdClass;
        $options->noclean = true;
        $table->data[] = array(format_text($page->contents, FORMAT_MOODLE, $options));

        print_table($table);
        echo "<br />";

        unset($table->data);
        $essayinfo = unserialize($essay->useranswer);        

        $studentname = $student->firstname." ".$student->lastname;
        $table->head = array(get_string("studentresponse", "lesson", $studentname));
        $table->data[] = array(format_text(stripslashes($essayinfo->answer)));
        
        print_table($table);
        echo "<br />";
        
        unset($table->data);

        $table->head = array(get_string("comments", "lesson"));
        $table->data[] = array("<textarea id=\"answer\" name=\"response\" rows=\"15\" cols=\"60\">".$essayinfo->response."</textarea>\n");    
        $options = array();
        if ($lesson->custom) {
            for ($i=$answer->score; $i>=0; $i--) {
                $options[$i] = $i;
            }
        } else {
            $options[0] = "incorrect";
            $options[1] = "correct";
        }
        $table->data[] = array(get_string("essayscore", "lesson").": ".lesson_choose_from_menu($options, "score", $essayinfo->score, "", "", "", true));
                
        print_table($table);
        echo "<br />";
        echo "<table align=\"center\"><tr><td>";
        echo "<input type=\"button\" value=\"Cancel\" onclick=\"document.essaygrade.action.value='essayview';".
             "document.essaygrade.submit();\" />";
        echo "</td><td>";
        echo "<input type=\"button\" value=\"Submit Grade\" onclick=\"document.essaygrade.action.value='updategrade';".
             "document.essaygrade.submit();\" />";
        echo "</td></tr></table>";
        echo "</form>";
        
        
    }

    /*******************update grade**************************************/ // 6/29/04
    elseif ($action == 'updategrade') {
        print_heading_with_help(format_string($lesson->name,true), "overview", "lesson");
        
        confirm_sesskey();
        
        $form = data_submitted();
        
        if (!$essay = get_record("lesson_attempts", "id", clean_param($form->attemptid, PARAM_INT))) {
            error("Error: could not find essay");
        }

        if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id and userid = $essay->userid", "completed", "*", $essay->retry, 1)) {
            error("Error: could not find grades");
        }
        
        $essayinfo = new stdClass;
        $essayinfo = unserialize($essay->useranswer);

        $essayinfo->graded = 1;
        $essayinfo->score = clean_param($form->score, PARAM_INT);
        $essayinfo->response = stripslashes_safe($form->response);
        $essayinfo->sent = 0;
        if (!$lesson->custom && $essayinfo->score == 1) {
            $essay->correct = 1;
        } else {
            $essay->correct = 0;
        }

        $essay->useranswer = addslashes(serialize($essayinfo));

        if (!update_record("lesson_attempts", $essay)) {
            error("Could not update essay score");
        }

        $grade = current($grades);

        // I modded this function a bit so it would work here...  :) ;) :P
        $updategrade->grade = lesson_calculate_ongoing_score($lesson, $essay->userid, $essay->retry, true);    
        $updategrade->id = $grade->id;
        
        if(update_record("lesson_grades", $updategrade)) {
            redirect("view.php?id=$cm->id&amp;action=essayview", get_string("updatesuccess", "lesson"));
        } else {
            echo get_string("updatefailed", "lesson")."!<br>";
            echo "<a href=\"view.php?id=$cm->id&amp;action=essayview\">".get_string("continue", "lesson")."</a>";
            exit();
        }
    }

    /*******************email essay **************************************/ // 6/29/04
    elseif ($action == 'emailessay') {
        print_heading_with_help(format_string($lesson->name,true), "overview", "lesson");
   
        confirm_sesskey();
    
        if (isset($_GET['userid'])) {
            $userid = clean_param($_GET['userid'], PARAM_INT);        
            $queryadd = " AND userid = ".$userid;
            if (! $users = get_records("user", "id", $userid)) {
                error("Error: could not find users");
            }
        } else {
            $queryadd = "";
            if (!$users = lesson_get_participants($lesson->id)) {
                error("Error: could not find users");
            }
        }
        
        // get lesson pages that are essay
        if (!$pages = get_records_select("lesson_pages", "lessonid = $lesson->id AND qtype = ".LESSON_ESSAY)) {
            error("Error: could not find lesson pages");
        }
        
        // get only the attempts that are in response to essay questions
        $pageids = implode(",", array_keys($pages)); // all the pageids in comma seperated list
        if (!$essayattempts = get_records_select("lesson_attempts", "lessonid = $lesson->id AND pageid IN($pageids)".$queryadd)) {
            error ("No one has answered essay questions yet...");
        }
        
        if (!$essayanswers = get_records_select("lesson_answers", "lessonid = $lesson->id AND pageid IN($pageids)", "", "pageid, score")) {
            error ("Could not find answer records.");
        }

        $options = new stdClass;
        $options->noclean = true;

        // NoticeFix  big fix, change $essay[]'s that use $USER to just $USER
        foreach ($essayattempts as $essay) {
            $essayinfo = unserialize($essay->useranswer);
            if ($essayinfo->graded && !$essayinfo->sent) {                
                $subject = get_string('essayemailsubject', 'lesson', format_string($pages[$essay->pageid]->title,true));
                $message = get_string('question', 'lesson').":<br>";
                $message .= format_text($pages[$essay->pageid]->contents, FORMAT_MOODLE, $options);
                $message .= "<br><br>";
                $message .= get_string('yourresponse', 'lesson').":<br>";
                $message .= format_text(stripslashes($essayinfo->answer));
                $message .= "<br><br>";
                $message .= get_string('commentswithname', 'lesson', $USER).":<br>";
                $message .= format_text(stripslashes($essayinfo->response), FORMAT_MOODLE, $options);
                $message .= "<br><br>";
                $grades = get_records_select("lesson_grades", "lessonid = $lesson->id and userid = $essay->userid", "completed", "*", $essay->retry, 1);
                $grade = current($grades);
                if ($lesson->custom) {
                    $points->score = $essayinfo->score;
                    $points->outof = $essayanswers[$essay->pageid]->score;
                    $message .= get_string("youhavereceived", "lesson", $points);
                } else {
                    $points->score = $essayinfo->score;
                    $points->outof = 1;
                    $message .= get_string("youhavereceived", "lesson", $points);
                }
                $message .= "<br><br>";
                $message .= get_string("yourgradeisnow", "lesson", $grade->grade)."%.";
                
                $plaintxt = format_text_email($message, FORMAT_HTML);

                if(email_to_user($users[$essay->userid], $USER, $subject, $plaintxt, $message)) {
                    $essayinfo->sent = 1;
                    $essay->useranswer = addslashes(serialize($essayinfo));
                    update_record("lesson_attempts", $essay);
                } else {
                    echo "Email Failed!<br>";
                    echo "<a href=\"view.php?id=$cm->id&amp;action=essayview\">".get_string("continue", "lesson")."</a>";
                    echo "</div>";
                    exit();
                }
            }
        }
        redirect("view.php?id=$cm->id&amp;action=essayview", get_string("emailsuccess", "lesson"));
    }

    /*******************high scores **************************************/
    elseif ($action == 'highscores') {
        print_heading_with_help(format_string($lesson->name,true), "overview", "lesson");

        if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id", "completed")) {
            $grades = array();
        }
    
        echo "<div align=\"center\">";
        $titleinfo->maxhighscores = $lesson->maxhighscores;
        $titleinfo->name = format_string($lesson->name);
        echo get_string("topscorestitle", "lesson", $titleinfo)."<br><br>";

        if (!$highscores = get_records_select("lesson_high_scores", "lessonid = $lesson->id")) {
            echo get_string("nohighscores", "lesson")."<br>";
        } else {
            foreach ($highscores as $highscore) {
                $grade = $grades[$highscore->gradeid]->grade;
                $topscores[$grade][] = $highscore->nickname;
            }
            krsort($topscores);
            
            echo "<table cellspacing=\"10px\">";
            echo "<tr align=\"center\"><td>".get_string("rank", "lesson")."</td><td>$course->students</td><td>".get_string("scores", "lesson")."</td></tr>";
            $printed = 0;
            while (true) {
                $temp = current($topscores);
                $score = key($topscores);
                $rank = $printed + 1;
                sort($temp); 
                foreach ($temp as $student) {
                    echo "<tr><td align=\"right\">$rank</td><td>$student</td><td align=\"right\">$score</td></tr>";
                    
                }
                $printed++;
                if (!next($topscores) || !($printed < $lesson->maxhighscores)) { 
                    break;
                }
            }
            echo "</table>";
        }
        if (isset($_GET['link'])) {
            echo "<br /><div class=\"lessonbutton standardbutton\"><a href=\"../../course/view.php?id=$course->id\">".get_string("returntocourse", "lesson")."</a></div>";
        } else {
            echo "<br /><span class=\"lessonbutton standardbutton\"><a href=\"../../course/view.php?id=$course->id\">".get_string("cancel", "lesson").'</a></span> '.
                " <span class=\"lessonbutton standardbutton\"><a href=\"view.php?id=$cm->id&amp;action=navigation\">".get_string("startlesson", "lesson").'</a></span>';
        }
        echo "</div>";
            
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
        if (isset($_GET['name'])) {
            $newhighscore->nickname = clean_param($_GET['name'], PARAM_CLEAN);
        }
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
        if (isset($_POST['name'])) {
            $name = trim(clean_param($_POST['name'], PARAM_CLEAN));
            if (lesson_check_nickname($name)) {
                redirect("view.php?id=$cm->id&amp;action=updatehighscores&amp;name=$name&amp;sesskey=".$USER->sesskey, get_string("nameapproved", "lesson"));
            } else {
                echo get_string("namereject", "lesson")."<br><br>";
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
