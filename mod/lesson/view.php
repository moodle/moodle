<?PHP  // $Id$

/// This page prints a particular instance of lesson
/// (Replace lesson with the name of your module)

    require_once("../../config.php");
	require_once("locallib.php");
	require_once("styles.php");
	
    require_variable($id);    // Course Module ID
    optional_variable($pageid);    // Lesson Page ID

    if (! $cm = get_record("course_modules", "id", $id)) {
        error("Course Module ID was incorrect");
    }

    if (! $course = get_record("course", "id", $cm->course)) {
        error("Course is misconfigured");
    }

    if (! $lesson = get_record("lesson", "id", $cm->instance)) {
        error("Course module is incorrect");
    }

    require_login($course->id);


/// Print the page header

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }

    $strlessons = get_string("modulenameplural", "lesson");
    $strlesson  = get_string("modulename", "lesson");
	
	/// CDC-FLAG moved the action up because I needed to know what the action will be before the header is printed
	if (empty($action)) {
        if (isteacher($course->id)) {
            $action = 'teacherview';
		} elseif  (time() < $lesson->available) {
			print_header("$course->shortname: $lesson->name", "$course->fullname",
						 "$navigation <A HREF=index.php?id=$course->id>$strlessons</A> -> <a href=\"view.php?id=$cm->id\">$lesson->name</a>", 
						  "", "", true, "", navmenu($course, $cm));
			print_simple_box_start("center");
			echo "<div align=\"center\">";
			echo get_string("lessonopen", "lesson", userdate($lesson->available))."<br>";
			echo "<a href=\"../../course/view.php?id=$course->id\">".get_string("returnmainmenu", "lesson")."</a>";
			echo "</div>";
			print_simple_box_end();
		    print_footer($course);
			exit();
		} elseif (time() > $lesson->deadline) {
			print_header("$course->shortname: $lesson->name", "$course->fullname",
						 "$navigation <A HREF=index.php?id=$course->id>$strlessons</A> -> <a href=\"view.php?id=$cm->id\">$lesson->name</a>", 
						  "", "", true, "", navmenu($course, $cm));
			print_simple_box_start("center");
			echo "<div align=\"center\">";
			echo get_string("lessonclosed", "lesson", userdate($lesson->deadline))."<br>";
			echo "<a href=\"../../course/view.php?id=$course->id\">".get_string("returnmainmenu", "lesson")."</a>";			
			echo "</div>";
			print_simple_box_end();
		    print_footer($course);
			exit();
		} elseif ($lesson->highscores) {
			$action = 'highscores';
        } else {
            $action = 'navigation';
        }
    } 

	/// CDC-FLAG changed the update_module_button and added another button when a teacher is checking the navigation of the lesson
    if (isteacheredit($course->id)) {
		$button = "<table><tr><td>";
        $button .= "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/course/mod.php\">".
               "<input type=\"hidden\" name=\"update\" value=\"$cm->id\" />".
               "<input type=\"hidden\" name=\"return\" value=\"true\" />".
               "<input type=\"submit\" value=\"".get_string("editlessonsettings", "lesson")."\" /></form>";
		if ($action == "navigation" && $pageid != LESSON_EOL) {
			$button .= "</td><td>".
				   "<form target=\"$CFG->framename\" method=\"get\" action=\"$CFG->wwwroot/mod/lesson/lesson.php\">".
				   "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />".
				   "<input type=\"hidden\" name=\"action\" value=\"editpage\" />".
				   "<input type=\"hidden\" name=\"pageid\" value=\"$pageid\" />".			   
				   "<input type=\"submit\" value=\"".get_string("editpagecontent", "lesson")."\" /></form>";
		}
		$button .= "</td></tr></table>";
	} else {
		$button = "";
	}

    print_header("$course->shortname: $lesson->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strlessons</A> -> <a href=\"view.php?id=$cm->id\">$lesson->name</a>", 
                  "", "", true, $button, // took out update_module_button($cm->id, $course->id, $strlesson) and replaced it with $button
                  navmenu($course, $cm));

    // set up some general variables
    $usehtmleditor = can_use_html_editor();
    $path = "$CFG->wwwroot/course";
    if (empty($THEME->custompix)) {
        $pixpath = "$path/../pix";
    } else {
        $pixpath = "$path/../theme/$CFG->theme/pix";
    }
				
    /************** navigation **************************************/
    if ($action == 'navigation') {
		//CDC Chris Berri added this echo call for left menu.  must match that in lesson.php for styles
		if ($lesson->displayleft) {
			echo '<div class="leftmenu">';
			   if($page = get_record_select("lesson_pages", "lessonid = $lesson->id AND prevpageid = 0")) {
						// print the pages
						echo "<form name=\"lessonpages2\" method=\"post\" action=\"view.php\">";
						echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">";
						echo "<input type=\"hidden\" name=\"action\" value=\"navigation\">";
						echo "<input type=\"hidden\" name=\"pageid\">";
								echo "<div class='lmlinks'><table bgcolor=\"$THEME->cellheading\"><tr></tr>";
								echo "<tr><td class='lmheading'>".get_string("lessonmenu", "lesson")."</td></tr><br>";
								echo "<tr><td class='lmMainlinks'>";
								echo "<a href=\"../../course/view.php?id=$course->id\">".get_string("mainmenu", "lesson")."</a></td></tr>";
								echo "<td>";
								lesson_print_tree_menu($lesson->id, $page->id, $cm->id);
								echo "</td></tr></table></div>"; //close lmlinks
						echo"</form>";
					}
			echo   "</div>"; //close left menu
			echo "<div class='slidepos'>"; //CDC chris berri for styles
		} elseif ($lesson->slideshow) {
			echo "<table align=\"center\"><tr><td>";
		}

		/// CDC-FLAG /// password protected lesson code
		if ($lesson->usepassword) {
			$correctpass = false;
			if (isset($_POST['userpassword'])) {
				if ($lesson->password == md5(trim($_POST['userpassword']))) {
					$USER->lessonloggedin[$lesson->id] = true;
					$correctpass = true;
				}
			} elseif (isset($USER->lessonloggedin[$lesson->id])) {
				$correctpass = true;
			}

			if (!$correctpass) {
				print_simple_box_start("center");
				echo "<form name=\"password\" method=\"post\" action=\"view.php\">\n";
				echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
				echo "<input type=\"hidden\" name=\"action\" value=\"navigation\">\n";
				echo "<table cellpadding=\"7px\">";
				if (isset($_POST['userpassword'])) {
					echo "<tr align=\"center\" style='color:#DF041E;'><td>".get_string("loginfail", "lesson")."</td></tr>";
				}
				echo "<tr align=\"center\"><td>".get_string("passwordprotectedlesson", "lesson", $lesson->name)."</td></tr>";
				echo "<tr align=\"center\"><td>".get_string("enterpassword", "lesson")." <input type=\"password\" name=\"userpassword\"></td></tr>";
						
				echo "<tr align=\"center\"><td>";
				echo "<input type=\"button\" value=\"".get_string("cancel", "lesson")."\" onclick=\"parent.location='../../course/view.php?id=$course->id';\">  ";
				echo "<input type=\"button\" value=\"".get_string("continue", "lesson")."\" onclick=\"document.password.submit();\">";
				echo "</td></tr></table>";
				print_simple_box_end();
				exit();
			}
		}
	
		/// CDC-FLAG /// Slideshow styles
		if($lesson->slideshow) { 
			//echo "<div class=\"slidepos\">\n";//CDC Chris Berri.  add the left menu theme stuff here.  must match on lesson.php
			echo "<div style=\"
					background-color: $lesson->bgcolor;
					height: ".$lesson->height."px;
					width: ".$lesson->width."px;
					overflow: auto;
					border: 0px solid #ccc;
					padding-right: 16px; /* for the benefit of macIE5 only */ 
					/* \ commented backslash hack - recover from macIE5 workarounds, it will ignore the following rule */
                    padding-right: 0;
					padding: 15px;
					\">\n";
		}
		// this is called if a student leaves during a lesson
		if($pageid == LESSON_UNSEENBRANCHPAGE) {
				$pageid = lesson_unseen_question_jump($lesson->id, $USER->id, $pageid);
		}
		/// CDC-FLAG ///		
				
		/// CDC-FLAG /// 6/21/04  This is the warning msg for teachers to inform them that cluster and unseen does not work while logged in as a teacher
		if(isteacher($course->id)) {
			if (execute_teacherwarning($lesson->id)) {
				$warningvars->cluster = get_string("clusterjump", "lesson");
				$warningvars->unseen = get_string("unseenpageinbranch", "lesson");
				echo "<div align=\"center\"><table><tr><td align=\"center\">".get_string("teacherjumpwarning", "lesson", $warningvars)."</td></tr></table></div>";
			}
		}		
		/// CDC-FLAG ///
		
        // display individual pages and their sets of answers
        // if pageid is EOL then the end of the lesson has been reached
       		// for flow, changed to simple echo for flow styles, michaelp, moved lesson name and page title down
	   //print_heading($lesson->name);
    	if (empty($pageid)) {
            add_to_log($course->id, "lesson", "start", "view.php?id=$cm->id", "$lesson->id", $cm->id);
            // if no pageid given see if the lesson has been started
			if ($grades = get_records_select("lesson_grades", "lessonid = $lesson->id AND userid = $USER->id",
                        "grade DESC")) {
                $retries = count($grades);
            } else {
                $retries = 0;
            }
            if ($retries) {
                print_heading(get_string("attempt", "lesson", $retries + 1));
            }
            // if there are any questions have been answered correctly in this attempt
            if ($attempts = get_records_select("lesson_attempts", 
                        "lessonid = $lesson->id AND userid = $USER->id AND retry = $retries AND 
                        correct = 1", "timeseen DESC")) {
                // get the first page
                if (!$firstpageid = get_field("lesson_pages", "id", "lessonid", $lesson->id,
                            "prevpageid", 0)) {
                    error("Navigation: first page not found");
                }
                foreach ($attempts as $attempt) {
                    $jumpto = get_field("lesson_answers", "jumpto", "id", $attempt->answerid);
                    // convert the jumpto to a proper page id
                    if ($jumpto == 0) { // unlikely value!
                        $lastpageseen = $attempt->pageid;
                    } elseif ($jumpto == LESSON_NEXTPAGE) {
                        if (!$lastpageseen = get_field("lesson_pages", "nextpageid", "id", 
                                    $attempt->pageid)) {
                            // no nextpage go to end of lesson
                            $lastpageseen = LESSON_EOL;
                        }
                    } else {
                        $lastpageseen = $jumpto;
                    }
                    break; // only look at the latest correct attempt 
                }
                //if ($lastpageseen != $firstpageid) {
				if (count_records("lesson_attempts", "lessonid", $lesson->id, "userid", $USER->id, "retry", $retries) > 0) {
					/// CDC-FLAG ///
					if ($lesson->timed) {
						if ($lesson->retake) {
							echo "<form name=\"queryform\" method =\"post\" action=\"view.php\">\n";
							echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
							echo "<input type=\"hidden\" name=\"action\" value=\"navigation\">\n";
							echo "<input type=\"hidden\" name=\"pageid\">\n";
							echo "<input type=\"hidden\" name=\"startlastseen\">\n";  /// CDC-FLAG added this line
							print_simple_box("<p align=\"center\">".get_string("leftduringtimed", "lesson")."</p>", "center");
							echo "<p align=\"center\"><input type=\"button\" value=\"".get_string("continue", "lesson").
								"\" onclick=\"document.queryform.pageid.value='$firstpageid';document.queryform.startlastseen.value='no';document.queryform.submit();\"></p>\n";  /// CDC-FLAG added document.queryform.startlastseen.value='yes'
							echo "</form>\n"; echo "</div></div>";///CDC Chris Berri added close div tag
						} else {
							print_simple_box_start("center");
							echo "<div align=\"center\">";
							echo get_string("leftduringtimednoretake", "lesson");
							echo "<br><br><a href=\"../../course/view.php?id=$course->id\">".get_string("returntocourse", "lesson")."</a>";
							echo "</div>";
							print_simple_box_end();
						}
					} else {
						echo "<form name=\"queryform\" method =\"post\" action=\"view.php\">\n";
						echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
						echo "<input type=\"hidden\" name=\"action\" value=\"navigation\">\n";
						echo "<input type=\"hidden\" name=\"pageid\">\n";
						echo "<input type=\"hidden\" name=\"startlastseen\">\n";  /// CDC-FLAG added this line
						print_simple_box("<p align=\"center\">".get_string("youhaveseen","lesson")."</p>",
								"center");
						echo "<p align=\"center\"><input type=\"button\" value=\"".get_string("yes").
							"\" onclick=\"document.queryform.pageid.value='$lastpageseen';document.queryform.startlastseen.value='yes';document.queryform.submit();\">&nbsp;&nbsp;&nbsp;<input type=\"button\" value=\"".get_string("no").  /// CDC-FLAG 6/11/04 ///
							"\" onclick=\"document.queryform.pageid.value='$firstpageid';document.queryform.startlastseen.value='no';document.queryform.submit();\"></p>\n";  /// CDC-FLAG added document.queryform.startlastseen.value='yes'
						echo "</form>\n"; echo "</div></div>";///CDC Chris Berri added close div tag
					}
                    print_footer($course);
                    exit();
                }
            }
            if ($grades) {
                foreach ($grades as $grade) {
                    $bestgrade = $grade->grade;
                    break;
                }
                if (!$lesson->retake) {
					print_simple_box_start("center");
					echo "<div align=\"center\">";
					echo get_string("noretake", "lesson");
					echo "<br><br><a href=\"../../course/view.php?id=$course->id\">".get_string("returntocourse", "lesson")."</a>";
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
            if (!$pageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
                error("Navigation: first page not found");
            }
			/// CDC-FLAG /// -- This is the code for starting a timed test
			if($lesson->timed && !isset($USER->startlesson[$lesson->id])) {
				unset($startlesson);
				$USER->startlesson[$lesson->id] = true;
				if($timeid = get_field("lesson_timer", "id", "lessonid", $lesson->id, "userid", $USER->id)) {
					$startlesson->id = $timeid;
				}
		
				$startlesson->lessonid = $lesson->id;
				$startlesson->userid = $USER->id;
				$startlesson->starttime = time();
				$startlesson->lessontime = time();
				
				if (!update_record("lesson_timer", $startlesson)) {
					if (!insert_record("lesson_timer", $startlesson)) {
						error("Error: could not insert row into lesson_timer table");
					}
				}
			}
			/// CDC-FLAG ///			
        }
        if ($pageid != LESSON_EOL) {
			/// CDC-FLAG /// 6/15/04 -- This is the code updates the lessontime for a timed test
			// NoticeFix
			if (isset($_POST["startlastseen"])) {  /// this deletes old records
				if ($_POST["startlastseen"] == "no") {
					if ($grades = get_records_select("lesson_grades", "lessonid = $lesson->id AND userid = $USER->id",
								"grade DESC")) {
						$retries = count($grades);
					} else {
						$retries = 0;
					}
					// NoticeFix  big fix on the two delete_records
					if (!delete_records("lesson_attempts", "userid", $USER->id, "lessonid", $lesson->id, "retry", $retries)) {
						error("Error: could not delete old attempts");
					}
					if (!delete_records("lesson_branch", "userid", $USER->id, "lessonid", $lesson->id, "retry", $retries)) {
						error("Error: could not delete old seen branches");
					}
				}
			}

			if($lesson->timed) {
				if(isteacher($course->id)) {
					echo "<div align=\"center\"><table><tr><td align=\"center\">".get_string("teachertimerwarning", "lesson")."</td></tr></table></div>";
				} else {
					if (isset($_POST["startlastseen"])) {
						if ($_POST["startlastseen"] == "yes") {  // continue a previous test, need to update the clock  (think this option is disabled atm)
							// get time information for this user
							if (!$timer = get_record("lesson_timer", "lessonid", $lesson->id, "userid", $USER->id)) {
								error("Error: could not find record");
							}
	
							unset($continuelesson);
							$continuelesson->id = $timer->id;
							$continuelesson->starttime = time() - ($timer->lessontime - $timer->starttime);
							$continuelesson->lessontime = time();
							if (!update_record("lesson_timer", $continuelesson)) {
								error("Error: could not update record in the lesson_timer table");
							}	
						} elseif ($_POST["startlastseen"] == "no") {  // starting over
							// get time information for this user
							if (!$timer = get_record("lesson_timer", "lessonid", $lesson->id, "userid", $USER->id)) {
								error("Error: could not find record");
							}
	
							// starting over, so reset the clock
							unset($startlesson);
							$startlesson->id = $timer->id;
							$startlesson->starttime = time();
							$startlesson->lessontime = time();
							
							if (!update_record("lesson_timer", $startlesson)) {
								error("Error: could not update record in the lesson_timer table");
							}
						}
					}
					// get time information for this user
					if (!$timer = get_record("lesson_timer", "lessonid", $lesson->id, "userid", $USER->id)) {
						error("Error: could not find record");
					}
					if ((($timer->starttime + $lesson->maxtime * 60) - time()) > 0) {
						// code for the clock
						echo "<table align=\"right\"><tr><td>";
						echo "<script language=\"javascript\">\n";
							echo "var starttime = ". $timer->starttime . ";\n";
							echo "var servertime = ". time() . ";\n";
							echo "var testlength = ". $lesson->maxtime * 60 .";\n";
							echo "document.write('<SCRIPT LANGUAGE=\"JavaScript\" SRC=\"timer.js\"><\/SCRIPT>');\n";
							echo "window.onload = function () { show_clock(); }\n";
						echo "</script>\n";
						echo "</td></tr></table><br><br>";
					} else {
						redirect("view.php?id=$cm->id&action=navigation&pageid=".LESSON_EOL."&outoftime=normal", get_string("outoftime", "lesson"));
					}
					// update clock when viewing a new page... no special treatment
					if ((($timer->starttime + $lesson->maxtime * 60) - time()) < 60) {
						echo "<div align=\"center\"><table><tr><td align=\"center\">".get_string("studentoneminwarning", "lesson").
							 "</td></tr></table></div>";
					}	
									
					unset($newtime);
					$newtime->id = $timer->id;
					$newtime->lessontime = time();
					
					if (!update_record("lesson_timer", $newtime)) {
						error("Error: could not update lesson_timer table");
					}
					
					// I dont like this... seems like there should be a better way...
					if (!$firstpageid = get_field("lesson_pages", "id", "lessonid", $lesson->id,
                            "prevpageid", 0)) {
    	                error("Navigation: first page not found");
	                }
					if ($pageid == $firstpageid) {
						print_simple_box(get_string("maxtimewarning", "lesson", $lesson->maxtime), "center");
					}

				}
			}
			/// CDC-FLAG ///
						
            add_to_log($course->id, "lesson", "view", "view.php?id=$cm->id", "$pageid", $cm->id);
            if (!$page = get_record("lesson_pages", "id", $pageid)) {
                error("Navigation: the page record not found");
            }
			/// CDC-FLAG 6/21/04 /// - this only gets called when a user starts up a new lesson and the first page is a cluster page
			if ($page->qtype == LESSON_CLUSTER) {
				if (!isteacher($course->id)) {
					// get new id
					$pageid = lesson_cluster_jump($lesson->id, $USER->id, $pageid);
					// get new page info
					if (!$page = get_record("lesson_pages", "id", $pageid)) {
						error("Navigation: the page record not found");
					}
				} else {
					// get the next page
					$pageid = $page->nextpageid;
					if (!$page = get_record("lesson_pages", "id", $pageid)) {
						error("Navigation: the page record not found");
					}
				}
			} elseif ($page->qtype == LESSON_ENDOFCLUSTER) {
				if ($page->nextpageid == 0) {
					$nextpageid = LESSON_EOL;
				} else {
					$nextpageid = $page->nextpageid;
				}
				redirect("view.php?id=$cm->id&action=navigation&pageid=$nextpageid", get_string("endofclustertitle", "lesson"));
			}
			/// CDC-FLAG ///

            // before we output everything check to see if the page is a EOB, if so jump directly 
            // to it's associated branch table
            if ($page->qtype == LESSON_ENDOFBRANCH) {
                if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
                    // print_heading(get_string("endofbranch", "lesson"));
                    foreach ($answers as $answer) {
                        // just need the first answer
						/// CDC-FLAG 6/21/04 ///
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
						}
						/// CDC-FLAG ///
                        redirect("view.php?id=$cm->id&amp;action=navigation&amp;pageid=$answer->jumpto",
                                get_string("endofbranch", "lesson"));
                        break;
                    } 
                    print_footer($course);
                    exit();
                } else {
                    error("Navigation: No answers on EOB");
                }
            }

			/// CDC-FLAG 6/21/04 ///  this calculates the ongoing score
			if ($lesson->ongoing && !empty($pageid)) {
				if (isteacher($course->id)) {
					echo "<div align=\"center\">".get_string("teacherongoingwarning", "lesson")."<br></div>";
				} else {
					lesson_calculate_ongoing_score($lesson, $USER);
				}
			}
			/// CDC-FLAG ///
            // it's not a EOB process it...
			/// CDC-FLAG ///
			if ($lesson->slideshow) {
	            echo "<table align=\"center\" width=\"100%\" border=\"0\"><tr><td>\n";
			} else {
	            echo "<table align=\"center\" width=\"80%\" border=\"0\"><tr><td>\n";
			}
			/// CDC-FLAG ///
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
          
		   	//print_heading($page->title);
			/// CDC-FLAG /// moved name and title down here for Flow style, michaelp
			echo "<div align=\"center\">";			
			echo "<i><strong>";
			echo ($lesson->name) . "</strong></i>";
			echo "<br><br></div>";
			
			/// CDC-FLAG ///
			if ($lesson->slideshow) {
				echo "<table><tr><td>";
            	echo format_text($page->contents);
				echo "</td></tr></table>";
			} else {
				print_simple_box(format_text($page->contents), 'center');
			}
			/// CDC-FLAG ///
            echo "<br />\n";
            // get the answers in a set order, the id order
            if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
                echo "<form name=\"answerform\" method =\"post\" action=\"lesson.php\">";
                echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">";
                echo "<input type=\"hidden\" name=\"action\" value=\"continue\">";
                echo "<input type=\"hidden\" name=\"pageid\" value=\"$pageid\">";
                /// CDC-FLAG ///
				if (!$lesson->slideshow || $page->qtype != 20) {
					print_simple_box_start("center");
				}
				/// CDC-FLAG ///
                echo '<table width="100%">';
                switch ($page->qtype) {
                    case LESSON_SHORTANSWER :
                    case LESSON_NUMERICAL :
                        echo "<tr><td align=\"center\">".get_string("youranswer", "lesson").
                            ": <label for=\"answer\" class=\"hidden-label\">Answer</label><input type=\"text\" id=\"answer\" name=\"answer\" size=\"50\" maxlength=\"200\">\n"; //CDC hidden label added.
                        echo '</table>';
                        print_simple_box_end();
						if (!$lesson->slideshow) {
							echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
								 get_string("pleaseenteryouranswerinthebox", "lesson")."\"></p>\n";
						}
                        break;
                    case LESSON_TRUEFALSE :
                        shuffle($answers);
                        foreach ($answers as $answer) {
                            echo "<tr><td valign=\"top\">";
                            echo "<label for=\"answerid\" class=\"hidden-label\">Answer ID</label><input type=\"radio\" id=\"answerid\" name=\"answerid\" value=\"{$answer->id}\">"; //CDC hidden label added.
							echo "</td><td>";
                            $options->para = false; // no <p></p>
                            echo format_text(trim($answer->answer), FORMAT_MOODLE, $options);
                            echo "</td></tr>";
							if ($answer != end($answers)) {
								echo "<tr><td><br></td></tr>";							
							} 
                        }
                        echo '</table>';
                        print_simple_box_end();
						if (!$lesson->slideshow) {
							echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
								get_string("pleasecheckoneanswer", "lesson")."\"></p>\n"; 
						}
                        break;
                    case LESSON_MULTICHOICE :
                        $i = 0;
                        shuffle($answers);
                        foreach ($answers as $answer) {
                            echo "<tr><td valign=\"top\">";
                            if ($page->qoption) {
                                // more than one answer allowed 
                                echo "<label for=\"answer[$i]\" class=\"hidden-label\">answer[$i]</label><input type=\"checkbox\" id=\"answer[$i]\" name=\"answer[$i]\" value=\"{$answer->id}\">"; //CDC hidden label added.
                            } else {
                                // only one answer allowed
                                echo "<label for=\"answerid\" class=\"hidden-label\">answer id</label><input type=\"radio\" id=\"answerid\" name=\"answerid\" value=\"{$answer->id}\">"; //CDC hidden label added.
                            }
                            echo "</td><td>";
                            $options->para = false; // no <p></p>
                            echo format_text(trim($answer->answer), FORMAT_MOODLE, $options); 
                            echo "</td></tr>";
							if ($answer != end($answers)) {
								echo "<tr><td><br></td></tr>";							
							} 
                            $i++;
                        }
                        echo '</table>';
                        print_simple_box_end();
						/// CDC-FLAG ///
						if (!$lesson->slideshow) {
							if ($page->qoption) {
								echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
									get_string("pleasecheckoneormoreanswers", "lesson")."\"></p>\n";
							} else {
								echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
									get_string("pleasecheckoneanswer", "lesson")."\"></p>\n";
							}
						}
						/// CDC-FLAG ///
                        break;
                        
                    /// CDC-FLAG /// 6/14/04  --- changed how matching works    
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
                        foreach ($answers as $answer) {
							if ($answer->response != NULL) {
								echo "<tr><td align=\"right\">";
								echo "<b>$answer->answer: </b></td><td valign=\"bottom\">";
								echo "<label for=\"response[$answer->id]\" class=\"hidden-label\">response[$answer->id]</label><select id=\"response[$answer->id]\" name=\"response[$answer->id]\">"; //CDC hidden label added.
								echo "<option value=\"0\" selected=\"selected\">Choose...</option>";
								foreach ($responses as $response) {
									echo "<option value=\"$response\">$response</option>";
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
						if (!$lesson->slideshow) {						
							echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
								get_string("pleasematchtheabovepairs", "lesson")."\"></p>\n";
						}
                        break;
					/// CDC-FLAG ///

                    case LESSON_BRANCHTABLE :
                        echo "<tr><td><table width=\"100%\">";
                        echo "<input type=\"hidden\" name=\"jumpto\">";
                        // don't suffle answers
						/// CDC-FLAG ///
						if(!$lesson->slideshow) {
							foreach ($answers as $answer) {
								echo "<tr><td align=\"center\">";
								echo "<input type=\"button\" value=\"$answer->answer\"";
								echo "onclick=\"document.answerform.jumpto.value=$answer->jumpto;document.answerform.submit();\">";
								echo "</td></tr>";
							}
						}
						
						/// CDC-FLAG ///
						echo '</table></table>';
                        print_simple_box_end();
                        break;
					case LESSON_ESSAY :
						echo "<tr><td align=\"center\" valign=\"top\" nowrap>".get_string("youranswer", "lesson").":</td><td>".
							 "<label for=\"answer\" class=\"hidden-label\">Answer</label><textarea id=\"answer\" name=\"answer\" rows=\"15\" cols=\"60\"></textarea>\n"; //CDC hidden label added.
						echo "</td></tr></table>";
						print_simple_box_end();
						if (!$lesson->slideshow) {
							echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
								 get_string("pleaseenteryouranswerinthebox", "lesson")."\"></p>\n";
						}
						break;
                }
				/// CDC-FLAG ///
				if (!$lesson->slideshow) {
	                echo "</form>\n"; 
				}
				/// CDC-FLAG ///
            } else {
                // a page without answers - find the next (logical) page
                echo "<form name=\"pageform\" method =\"post\" action=\"view.php\">\n";
                echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
                echo "<input type=\"hidden\" name=\"action\" value=\"navigation\">\n";
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
                echo "<input type=\"hidden\" name=\"pageid\" value=\"$newpageid\">\n";
			    echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
                     get_string("continue", "lesson")."\"></p>\n";
                echo "</form>\n";
            }
            echo "</table>\n"; 
        } else {
            // end of lesson reached work out grade
			/// CDC-FLAG ///
			if ($lesson->timed) {
				unset($USER->startlesson[$lesson->id]);  // take this variable out that I put in for timed tests
				if (isset($_GET["outoftime"])) {
					if ($_GET["outoftime"] == "normal") {
						print_simple_box(get_string("eolstudentoutoftime", "lesson"), "center");
					}
				}
			}
			if (isset($USER->lessonloggedin[$lesson->id])) {
				unset($USER->lessonloggedin[$lesson->id]);
			}
            add_to_log($course->id, "lesson", "end", "view.php?id=$cm->id", "$lesson->id", $cm->id);
            print_heading(get_string("congratulations", "lesson"));
            print_simple_box_start("center");
            $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id);
            if (isstudent($course->id)) {
                if ($nviewed = count_records("lesson_attempts", "lessonid", $lesson->id, "userid", 
                        $USER->id, "retry", $ntries)) {
					/// CDC-FLAG /// 6/11/04
					if (!$lesson->custom) {
						$ncorrect = 0;						
						if ($pagesanswered = get_records_select("lesson_attempts",  "lessonid = $lesson->id AND 
								userid = $USER->id AND retry = $ntries order by timeseen")) {

							foreach ($pagesanswered as $pageanswered) {
								if (@!array_key_exists($pageanswered->pageid, $temp)) {
									$temp[$pageanswered->pageid] = array($pageanswered->correct, 1);
								} else {
									if ($temp[$pageanswered->pageid][1] < $lesson->maxattempts) {
										$n = $temp[$pageanswered->pageid][1] + 1;
										$temp[$pageanswered->pageid] = array($pageanswered->correct, $n);
									}
								}
							}
							foreach ($temp as $value => $key) {
								if ($key[0] == 1) {
									$ncorrect += 1;
								}
							}
						}
						$nviewed = count($temp); // this counts number of Questions the user viewed

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
						$thegrade = intval(100 * $ncorrect / $nviewed);
						echo "<p align=\"center\">".get_string("gradeis", "lesson", 
								number_format($thegrade * $lesson->grade / 100, 1)).
							" (".get_string("outof", "lesson", $lesson->grade).")</p>\n";
						
					} else {
						$score = 0;
						if ($useranswers = get_records_select("lesson_attempts",  "lessonid = $lesson->id AND 
								userid = $USER->id AND retry = $ntries", "timeseen")) {

							foreach ($useranswers as $useranswer) {
								if (@!array_key_exists($useranswer->pageid, $temp)) {
									$temp[$useranswer->pageid] = array($useranswer->answerid, 1);
								} else {
									if ($temp[$useranswer->pageid][1] < $lesson->maxattempts) {
										$n = $temp[$useranswer->pageid][1] + 1;
										$temp[$useranswer->pageid] = array($useranswer->answerid, $n);
									}
								}
							}
							if ($answervalues = get_records_select("lesson_answers",  "lessonid = $lesson->id")) {
								if ($pages = get_records_select("lesson_pages", "lessonid = $lesson->id")) {
									foreach ($pages as $page) {
										$questions[$page->id] = $page->qtype;
									}
								} else {
									$questions = array();
								}
								$tempmaxgrade = $lesson->grade;
								$essayquestions = 0;
								foreach ($answervalues as $answervalue) {
									if (array_key_exists($answervalue->pageid, $temp)) {
										if ($temp[$answervalue->pageid][0] == $answervalue->id) {
											if ($questions[$answervalue->pageid] == LESSON_ESSAY) {
												$tempmaxgrade = $tempmaxgrade - $answervalue->score;
												$essayquestions++;
											} else {
												$score = $score + $answervalue->score;
											}
										}
									}
								}
							} else {
								error("Error: Could not find answers!");
							}
						}
						if ($score > $lesson->grade) {
							$thegrade = 100;
							$score = $lesson->grade;
						} elseif ($score < 0) {
							$thegrade = 0;
							$score = 0;
						} else {
							$thegrade = intval(100 * $score / $lesson->grade);
						}

						unset($a);
						if ($essayquestions > 0) {
							$a->score = $score;
							$a->tempmaxgrade = $tempmaxgrade;
							$a->essayquestions = $essayquestions;
							$a->grade = $lesson->grade;
							echo "<div align=\"center\">";
							echo get_string("displayscorewithessays", "lesson", $a);
							echo "</div>";
						} else {
							$a->score = $score;
							$a->grade = $lesson->grade;
							echo "<div align=\"center\">".get_string("displayscorewithoutessays", "lesson", $a)."</div>";						
						}
					}
					/// CDC-FLAG ///						
					$grade->lessonid = $lesson->id;
					$grade->userid = $USER->id;
					$grade->grade = $thegrade;
					$grade->completed = time();
					if (!$lesson->practice) {
						if (!$newgradeid = insert_record("lesson_grades", $grade)) {
							error("Navigation: grade not inserted");
						}
					} else {
						if (!delete_records("lesson_attempts", "lessonid", $lesson->id, "userid", $USER->id, "retry", $ntries)) {
							error("Could not delete lesson attempts");
						}
					}
                } else {
                    //print_string("noattemptrecordsfound", "lesson");					
					if ($lesson->timed) {
						if (isset($_GET["outoftime"])) {
							if ($_GET["outoftime"] == "normal") {
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
			///CDC-FLAG /// high scores code
			if ($lesson->highscores && !isteacher($course->id)) {
				echo "<div align=\"center\"><br>";
				if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id", "completed")) {
					echo get_string("youmadehighscore", "lesson", $lesson->maxhighscores)."<br>";
					echo "<a href=\"view.php?id=$cm->id&action=nameforhighscores\">".get_string("clicktopost", "lesson")."</a><br>";
				} else {
					if (!$highscores = get_records_select("lesson_high_scores", "lessonid = $lesson->id")) {
						echo get_string("youmadehighscore", "lesson", $lesson->maxhighsores)."<br>";
						echo "<a href=\"view.php?id=$cm->id&action=nameforhighscores\">".get_string("clicktopost", "lesson")."</a><br>";
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
							echo "<a href=\"view.php?id=$cm->id&action=nameforhighscores\">".get_string("clicktopost", "lesson")."</a><br>";
						} else {
							echo get_string("nothighscore", "lesson", $lesson->maxhighscores)."<br>";
						}
					}
				}
				echo "<br><a href=\"view.php?id=$cm->id&action=highscores&link=1\">".get_string("viewhighscores", "lesson")."</a>";
				echo "</div>";							
			}
			/// CDC-FLAG ///			
			echo "<p align=\"center\"><a href=\"../../course/view.php?id=$course->id\">".get_string("mainmenu", "lesson")."</a></p>\n"; //CDC Back to the menu (course view).
			echo "<p align=\"center\"><a href=\"../../course/grade.php?id=$course->id\">".get_string("viewgrades", "lesson")."</a></p>\n"; //CDC Back to the menu (course view).
        }

		/// CDC-FLAG ///
		if($lesson->slideshow) {
			echo "</td></tr></table></div>\n"; //Closes Mark's big div tag?
		}
		
		if($lesson->slideshow && $pageid != LESSON_EOL) {
			if (!$lesson->displayleft) {
				echo "<table width=\"$lesson->width\" cellpadding=\"5\" cellspacing=\"5\" align=\"center\">\n";
			} else {
				echo "<table width=\"$lesson->width\" cellpadding=\"5\" cellspacing=\"5\">\n";
			}
			switch ($page->qtype) {
				case LESSON_SHORTANSWER :
				case LESSON_NUMERICAL :
					echo "<tr><td><p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
						 get_string("pleaseenteryouranswerinthebox", "lesson")."\"></p></td></tr>\n";
					break;
				case LESSON_TRUEFALSE :
					echo "<tr><td><p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
						get_string("pleasecheckoneanswer", "lesson")."\"></p></td></tr>\n"; 
					break;
				case LESSON_MULTICHOICE :
					if ($page->qoption) {
						echo "<tr><td><p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
							get_string("pleasecheckoneormoreanswers", "lesson")."\"></p></td></tr>\n";
					} else {
						echo "<tr><td><p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
							 get_string("pleasecheckoneanswer", "lesson")."\"></p></td></tr>\n";
					}
					break;
				case LESSON_MATCHING :
					echo "<tr><td><p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
						get_string("pleasematchtheabovepairs", "lesson")."\"></p></td></tr>\n";
					break;
				case LESSON_ESSAY :
					echo "<tr><td><p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
						 get_string("pleaseenteryouranswerinthebox", "lesson")."\"></p></td></tr>\n";
					break;
				case LESSON_BRANCHTABLE : 
					$nextprevious = array();
					$otherjumps = array();
					// seperate out next and previous jumps from the other jumps 
					foreach ($answers as $answer) {
						if($answer->jumpto == LESSON_NEXTPAGE || $answer->jumpto == LESSON_PREVIOUSPAGE) {
							$nextprevious[] = $answer;
						} else {
							$otherjumps[] = $answer;
						}
					}
					if ($page->layout) {
						echo "<tr>";
						// next 3 foreach loops print out the links in correct order
						foreach ($nextprevious as $jump) {
							if ($jump->jumpto == LESSON_PREVIOUSPAGE) {
								echo "<td align=\"left\"><input type=\"button\" onclick=\"document.answerform.jumpto.value=$jump->jumpto;document.answerform.submit();\"".
									 "value = \"$jump->answer\"></td>";
							}
						}
						echo "<td align=\"center\"><table><tr>";
						foreach ($otherjumps as $otherjump) {
								echo "<td><input type=\"button\" onclick=\"document.answerform.jumpto.value=$otherjump->jumpto;document.answerform.submit();\"".
									 "value = \"$otherjump->answer\"></td>";
						}
						echo "</tr></table></td>";
						foreach ($nextprevious as $jump) {
							if ($jump->jumpto == LESSON_NEXTPAGE) {
								echo "<td align=\"right\"><input type=\"button\" onclick=\"document.answerform.jumpto.value=$jump->jumpto;document.answerform.submit();\"".
									 "value = \"$jump->answer\"></td>";
							}
						}
						echo "</tr>";
					} else {
						// next 3 foreach loops print out the links in correct order
						foreach ($nextprevious as $jump) {
							if ($jump->jumpto == LESSON_NEXTPAGE) {
								echo "<tr><td><input type=\"button\" onclick=\"document.answerform.jumpto.value=$jump->jumpto;document.answerform.submit();\"".
									 "value = \"$jump->answer\"></td></tr>";
							}
						}
						foreach ($otherjumps as $otherjump) {
								echo "<tr><td><input type=\"button\" onclick=\"document.answerform.jumpto.value=$otherjump->jumpto;document.answerform.submit();\"".
									 "value = \"$otherjump->answer\"></td></tr>";
						}
						foreach ($nextprevious as $jump) {
							if ($jump->jumpto == LESSON_PREVIOUSPAGE) {
								echo "<tr><td><input type=\"button\" onclick=\"document.answerform.jumpto.value=$jump->jumpto;document.answerform.submit();\"".
									 "value = \"$jump->answer\"></td></tr>";
							}
						}
					}
					break;
			}				
			echo "</table></form>\n";
		}

		if ($lesson->displayleft) {
			echo "</div><!-- close slidepos class -->"; //CDC Chris Berri for styles, closes slidepos.
		} elseif ($lesson->slideshow) {
			echo "</td></tr></table>";
		}
		/// CDC-FLAG ///            
    }


    /*******************teacher view **************************************/
    elseif ($action == 'teacherview') {
		print_heading_with_help($lesson->name, "overview", "lesson");
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
			/// CDC-FLAG ///
            // if there are no pages give teacher the option to create a new page or a new branch table
			echo "<div align=\"center\">";
			if (isteacheredit($course->id)) {
				print_simple_box( "<table cellpadding=\"5\" border=\"0\">\n<tr><th>".get_string("whatdofirst", "lesson")."</th></tr><tr><td>".
					"<a href=\"import.php?id=$cm->id&amp;pageid=0\">".
					get_string("importquestions", "lesson")."</a></td></tr><tr><td>".
					"<a href=\"lesson.php?id=$cm->id&amp;action=addbranchtable&amp;pageid=0&amp;firstpage=1\">".
					get_string("addabranchtable", "lesson")."</a></td></tr><tr><td>".
					"<a href=\"lesson.php?id=$cm->id&amp;action=addpage&amp;pageid=0&amp;firstpage=1\">".
					get_string("addaquestionpage", "lesson")." ".get_string("here","lesson").
					"</a></td></tr></table\n");
			}
			print_simple_box_end();
			echo "</div>"; //CDC Chris Berri added.
			/// CDC-FLAG ///		
        } else {
            // print the pages
            echo "<form name=\"lessonpages\" method=\"post\" action=\"view.php\">\n";
            echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
            echo "<input type=\"hidden\" name=\"action\" value=\"navigation\" />\n";
            echo "<input type=\"hidden\" name=\"pageid\" />\n";
			/// CDC-FLAG /// link to grade essay questions
			if (count_records("lesson_pages", "lessonid", $lesson->id, "qtype", LESSON_ESSAY) > 0) {
				echo "<div align=\"center\"><a href=\"view.php?id=$cm->id&amp;action=essayview\">".get_string("gradeessay", "lesson")."</a></div><br />";
			}
			/// CDC-FLAG /// tree code - in final release, will use lang file for all text output.
			// NoticeFix next two lines and bowth viewAlls
			$branch = false;
			$singlePage = false;
			if($lesson->tree && !isset($_GET['display']) && !isset($_GET['viewAll'])) {  
				echo "<div align=\"center\">";
					echo get_string("treeview", "lesson")."<br /><br />";
					echo "<a href=\"view.php?id=$id&amp;viewAll=1\">".get_string("viewallpages", "lesson")."</a><br /><br />\n";
					echo "<table><tr><td>";
					lesson_print_tree($page->id, $lesson->id, $cm->id, $pixpath);
					echo "</td></tr></table>";
					echo "<br /><a href=\"view.php?id=$id&amp;viewAll=1\">".get_string("viewallpages", "lesson")."</a>\n";
				echo "</div>";
			} else {
				if(isset($_GET['display']) && !isset($_GET['viewAll'])) {
					while(true)
					{
						if($page->id == $_GET['display'] && $page->qtype == LESSON_BRANCHTABLE) {
							$branch = true;
							$singlePage = false;
							break;
						} elseif($page->id == $_GET['display']) {
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
						/// CDC-FLAG 6/16/04 ///					
						echo "<tr><td align=\"right\"><small><a href=\"import.php?id=$cm->id&amp;pageid=$page->prevpageid\">".
							get_string("importquestions", "lesson")."</a> | ".
					        "<a href=\"lesson.php?id=$cm->id&amp;action=addcluster&amp;pageid=$page->prevpageid\">".
	                        get_string("addcluster", "lesson")."</a> | ".
							"<a href=\"lesson.php?id=$cm->id&amp;action=addendofcluster&amp;pageid=$page->prevpageid\">".
                    	    get_string("addendofcluster", "lesson")."</a> | ".
							"<a href=\"lesson.php?id=$cm->id&amp;action=addbranchtable&amp;pageid=$page->prevpageid\">".
							get_string("addabranchtable", "lesson")."</a> | ".
							"<a href=\"lesson.php?id=$cm->id&amp;action=addpage&amp;pageid=$page->prevpageid\">".
							get_string("addaquestionpage", "lesson")." ".get_string("here","lesson").
							"</a></small></td></tr>\n";
						/// CDC-FLAG ///							
					} 				 
				} else {
					if($lesson->tree) {
						echo "<center><a href=\"view.php?id=$id\">".get_string("backtreeview", "lesson")."</a><br /></center>\n";
					}	
					echo "<center><table cellpadding=\"5\" border=\"0\" width=\"80%\">\n";
					if (isteacheredit($course->id)) {
						/// CDC-FLAG 6/16/04 ///
						echo "<tr><td align=\"left\"><small><a href=\"import.php?id=$cm->id&amp;pageid=0\">".
							get_string("importquestions", "lesson")."</a> | ".
							"<a href=\"lesson.php?id=$cm->id&amp;action=addcluster&amp;pageid=0\">".
	                        get_string("addendofcluster", "lesson")."</a> | ".
							"<a href=\"lesson.php?id=$cm->id&action=addbranchtable&pageid=0\">".
							get_string("addabranchtable", "lesson")."</a> | ".
                            "<a href=\"lesson.php?id=$cm->id&amp;action=addpage&amp;pageid=0\">".
							get_string("addaquestionpage", "lesson")." ".get_string("here","lesson").
							"</a></small></td></tr>\n";
						/// CDC-FLAG ///
					}
				}
				/// CDC-FLAG /// end tree code	(note, there is an "}" below for an else above)
            echo "<tr><td>\n";
            while (true) {
                echo "<table width=\"100%\" border=\"1\"><tr><td bgcolor=\"$THEME->cellheading2\" colspan=\"2\"><b>$page->title</b>&nbsp;&nbsp;\n";
                if (isteacheredit($course->id)) {
                    if ($npages > 1) {
                        echo "<a title=\"".get_string("move")."\" href=\"lesson.php?id=$cm->id&amp;action=move&amp;pageid=$page->id\">\n".
                            "<img src=\"$pixpath/t/move.gif\" hspace=\"2\" height=\"11\" width=\"11\" border=\"0\" alt=\"move\" /></a>\n";
                    }
                    echo "<a title=\"".get_string("update")."\" href=\"lesson.php?id=$cm->id&amp;action=editpage&amp;pageid=$page->id\">\n".
                        "<img src=\"$pixpath/t/edit.gif\" hspace=\"2\" height=\"11\" width=\"11\" border=\"0\" alt=\"edit\" /></a>\n".
                        "<a title=\"".get_string("delete")."\" href=\"lesson.php?id=$cm->id&amp;action=confirmdelete&amp;pageid=$page->id\">\n".
                        "<img src=\"$pixpath/t/delete.gif\" hspace=\"2\" height=\"11\" width=\"11\" border=\"0\" alt=\"delete\" /></a>\n";
                    }
                    echo "</td></tr>\n";             
                echo "<tr><td colspan=\"2\">\n";
                print_simple_box(format_text($page->contents), "center");
                echo "</td></tr>\n";
                // get the answers in a set order, the id order
                if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
                    echo "<tr><td bgcolor=\"$THEME->cellheading2\" colspan=\"2\" align=\"center\"><b>\n";
                    switch ($page->qtype) {
						case LESSON_ESSAY :  /// CDC-FLAG /// 6/16/04  this line and the next
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
                            if (!lesson_iscorrect($page->id, $answer->jumpto)) {
                                echo " - ".get_string("firstanswershould", "lesson");
                            }
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
                    echo "</td></tr>\n";
                    $i = 1;
					$n = 0;
                    foreach ($answers as $answer) {
                        switch ($page->qtype) {
                            case LESSON_MULTICHOICE:
                            case LESSON_TRUEFALSE:
                            case LESSON_SHORTANSWER:
                            case LESSON_NUMERICAL:
                                echo "<tr><td bgcolor=\"$THEME->cellheading2\" align=\"right\" valign=\"top\" width=\"20%\">\n";
                                /// CDC-FLAG /// 6/11/04
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
								/// CDC-FLAG ///
                                echo "</td><td width=\"80%\">\n";
                                echo format_text($answer->answer);
                                echo "</td></tr>\n";
                               echo "<tr><td align=\"right\" valign=\"top\"><b>".get_string("response", "lesson")." $i:</b> \n";
                                echo "</td><td>\n";
                                echo format_text($answer->response); 
                                echo "</td></tr>\n";
                                break;							
							case LESSON_MATCHING:
								if ($n < 2) {
									if ($answer->answer != NULL) {
										if ($n == 0) {
											echo "<tr><td align=\"right\" valign=\"top\"><b>".get_string("correctresponse", "lesson").":</b> \n";
											echo "</td><td>\n";
											echo format_text($answer->answer); 
											echo "</td></tr>\n";
										} else {
											echo "<tr><td align=\"right\" valign=\"top\"><b>".get_string("wrongresponse", "lesson").":</b> \n";
											echo "</td><td>\n";
											echo format_text($answer->answer); 
											echo "</td></tr>\n";
										}
									}
									$n++;
									$i--;
								} else {
	                                echo "<tr><td bgcolor=\"$THEME->cellheading2\" align=\"right\" valign=\"top\" width=\"20%\">\n";
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
									echo format_text($answer->answer);
									echo "</td></tr>\n";
								   echo "<tr><td align=\"right\" valign=\"top\"><b>".get_string("matchesanswer", "lesson")." $i:</b> \n";
									echo "</td><td>\n";
									echo format_text($answer->response); 
									echo "</td></tr>\n";
								}
								break;
                            case LESSON_BRANCHTABLE:
                                echo "<tr><td align=\"right\" valign=\"top\" width=\"20%\">\n";
                                echo "<b>".get_string("description", "lesson")." $i:</b> \n";
                                echo "</td><td width=\"80%\">\n";
                                echo format_text($answer->answer);
                                echo "</td></tr>\n";
                                break;
                        }
                        if ($answer->jumpto == 0) {
                            $jumptitle = get_string("thispage", "lesson");
                        } elseif ($answer->jumpto == LESSON_NEXTPAGE) {
                            $jumptitle = get_string("nextpage", "lesson");
                        } elseif ($answer->jumpto == LESSON_EOL) {
                            $jumptitle = get_string("endoflesson", "lesson");
/* CDC-FLAG 6/17/04 */	} elseif ($answer->jumpto == LESSON_UNSEENBRANCHPAGE) {
                            $jumptitle = get_string("unseenpageinbranch", "lesson");  // a better way is get_string("unseenbranchpage", "lesson");  and define in lang file 
						} elseif ($answer->jumpto == LESSON_PREVIOUSPAGE) {
                            $jumptitle = get_string("previouspage", "lesson");
						} elseif ($answer->jumpto == LESSON_RANDOMPAGE) {
							$jumptitle = get_string("randompageinbranch", "lesson");
						} elseif ($answer->jumpto == LESSON_RANDOMBRANCH) {
							$jumptitle = get_string("randombranch", "lesson");
						} elseif ($answer->jumpto == LESSON_CLUSTERJUMP) {
							$jumptitle = get_string("clusterjump", "lesson");		/// CDC-FLAG ///															
                        } else {
                            if (!$jumptitle = get_field("lesson_pages", "title", "id", $answer->jumpto)) {
                                $jumptitle = "<b>".get_string("notdefined", "lesson")."</b>";
                            }
                        }
						/// CDC-FLAG ///
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
                    // print_simple_box_end();  /// CDC-FLAG /// not sure if i commented this out... hehe
                    echo "<tr><td bgcolor=\"$THEME->cellheading2\" colspan=\"2\" align=\"center\">";
                    if ($page->qtype != LESSON_ENDOFBRANCH) {
                        echo "<input type=\"button\" value=\"";
                        if ($page->qtype == LESSON_BRANCHTABLE) {
                            echo get_string("checkbranchtable", "lesson");
                        } else {
                            echo get_string("checkquestion", "lesson");
                        }
                        echo "\" onclick=\"document.lessonpages.pageid.value=$page->id;".
                            "document.lessonpages.submit();\">";
                    }
                    echo "&nbsp;</td></tr>\n";
                }
                echo "</td></tr></table></td></tr>\n";
                if (isteacheredit($course->id)) {
					/// CDC-FLAG /// 6/16/04				
                    echo "<tr><td align=\"left\"><small><a href=\"import.php?id=$cm->id&amp;pageid=$page->id\">".
                        get_string("importquestions", "lesson")."</a> | ".    
					     "<a href=\"lesson.php?id=$cm->id&amp;action=addcluster&amp;pageid=$page->id\">".
                         get_string("addcluster", "lesson")."</a> | ".
						 "<a href=\"lesson.php?id=$cm->id&amp;action=addendofcluster&amp;pageid=$page->id\">".
                         get_string("addendofcluster", "lesson")."</a> | ".
						 "<a href=\"lesson.php?id=$cm->id&amp;action=addbranchtable&amp;pageid=$page->id\">".
                        get_string("addabranchtable", "lesson")."</a><br />";
					/// CDC-FLAG ///					
                    // the current page or the next page is an end of branch don't show EOB link
					$nextqtype = 0; // set to anything else EOB
                    if ($page->nextpageid) {
                        $nextqtype = get_field("lesson_pages", "qtype", "id", $page->nextpageid);
                    }
                    if (($page->qtype != LESSON_ENDOFBRANCH) and ($nextqtype != LESSON_ENDOFBRANCH)) {
                        echo "<a href=\"lesson.php?id=$cm->id&amp;action=addendofbranch&amp;pageid=$page->id\">".
                        get_string("addanendofbranch", "lesson")."</a> | ";
                    }
                    echo "<a href=\"lesson.php?id=$cm->id&amp;action=addpage&amp;pageid=$page->id\">".
                        get_string("addaquestionpage", "lesson")." ".get_string("here","lesson").
                        "</a></small></td></tr>\n";
                }
                echo "<tr><td>\n";
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
				/// CDC-FLAG ///
				if($singlePage) {  // this will make sure only one page is displayed if needed
					break;
				} elseif($branch && $page->qtype == LESSON_ENDOFBRANCH) {  // this will display a branch table and its contents
					break;
                } elseif ($page->nextpageid) {  /// CDC-FLAG ///
                    if (!$page = get_record("lesson_pages", "id", $page->nextpageid)) {
                        error("Teacher view: Next page not found!");
                    }
                } else {
                    // last page reached
                    break;
                }
            }
		} /// CDC-FLAG /// end of else from above tree code!!!
		
            echo "</table></form>\n";
			/// CDC-FLAG ///
			// NoticeFix both viewAll's
			if(isset($_GET['display']) && !isset($_GET['viewAll'])) {
				echo "<center><a href=\"view.php?id=$id&amp;viewAll=1\">".get_string("viewallpages", "lesson")."</a><br />\n";
			}
			if($lesson->tree && (isset($_GET['display']) || isset($_GET['viewAll']))) {
				echo "<center><a href=\"view.php?id=$id\">".get_string("backtreeview", "lesson")."</a><br /></center>\n";
			}
			/// CDC-FLAG ///			
            print_heading("<a href=\"view.php?id=$cm->id&amp;action=navigation\">".get_string("checknavigation",
                        "lesson")."</a>\n");
        } 
    }

    /*******************essay view **************************************/ // 6/29/04
    elseif ($action == 'essayview') {
		print_heading_with_help($lesson->name, "overview", "lesson");
		if (!$essays = get_records_select("lesson_essay", "lessonid = $lesson->id", "timesubmitted")) {
			error("Error: could not find essays");
		}
		if (!$pages = get_records_select("lesson_pages", "lessonid = $lesson->id")) {
			error("Error: could not find lesson pages");
		}
		if (!$users = lesson_get_participants($lesson->id)) {
			error("Error: could not find users");
		}
		
		echo "<div align=\"center\"><a href=\"view.php?id=$cm->id\">Go Back to Lesson</a></div><br>";

		foreach ($essays as $essay) {
			$studentname = $users[$essay->userid]->lastname.", ".$users[$essay->userid]->firstname;
			$essay->studentname = $studentname;
			$temp[$studentname][] = $essay;
		}
		ksort($temp);

		echo "<table width=\"100%\" align=\"center\" cellspacing=\"10\">";
		echo "<tr align=\"center\" bgcolor=\"$THEME->cellheading2\"><td width=\"100px\"><b>$course->students</b></td><td><b>".get_string("essays", "lesson")."</b></td><td width=\"155px\"><b>".get_string("email", "lesson")."</b></td></tr>";
		foreach ($temp as $student) {
			echo "<tr><td>".$student[0]->studentname."</td><td>";
			$end = end($student);
			foreach ($student as $essay) {
				if (!$essay->graded) {
					$style = "style='color:#DF041E;text-decoration:underline;'";
				} elseif (!$essay->sent) {
					$style = "style='color:#006600;text-decoration:underline;'";
				} else {
					$style = "style='color:#999999;'";
				}
				$output = "<a $style href=\"view.php?id=$cm->id&action=essaygrade&essayid=$essay->id\">".$pages[$essay->pageid]->title."</a>";
				if ($essay->id != $end->id) {
					$output .= ", ";
				}
				echo $output;
			}
			echo "</td><td><a href=\"view.php?id=$cm->id&action=emailessay&userid=".$essay->userid."\">".get_string("emailgradedessays", "lesson")."</a></td></tr>";
		}
		echo "<td><td><td><a href=\"view.php?id=$cm->id&action=emailessay\">".get_string("emailallgradedessays", "lesson")."</a></td>";
		echo "</table>";

	}
	
	/*******************grade essays **************************************/ // 6/29/04
    elseif ($action == 'essaygrade') {
		print_heading_with_help($lesson->name, "overview", "lesson");
		if (!$essays = get_records_select("lesson_essay", "lessonid = $lesson->id", "timesubmitted")) {
			error("Error: could not find essays");
		}
		if (!$pages = get_records_select("lesson_pages", "lessonid = $lesson->id")) {
			error("Error: could not find lesson pages");
		}
		if (!$users = lesson_get_participants($lesson->id)) {
			error("Error: could not find users");
		}
		if (!$answers = get_records_select("lesson_answers", "lessonid = $lesson->id")) {
			error("Error: could not find essays");
		}

		$essayid = $_GET['essayid'];

		echo "<form name=\"essaygrade\" method=\"post\" action=\"view.php\">\n";
		echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
		echo "<input type=\"hidden\" name=\"action\">\n";
		echo "<input type=\"hidden\" name=\"essayid\" value=\"$essayid\">\n";
		echo "<input type=\"hidden\" name=\"userid\" value=\"".$essays[$essayid]->userid."\">\n";
		
		$style = "style=\"padding-left:40px;\"";
		
		
		echo "<table cellspacing=\"10\" align=\"center\">";
		echo "<tr><td>".get_string("question", "lesson").":</td></tr><tr><td $style>";
		print_simple_box_start("left");
		echo $pages[$essays[$essayid]->pageid]->contents;
		print_simple_box_end();
		echo "</td></tr>";

		$studentname = $users[$essays[$essayid]->userid]->firstname." ".$users[$essays[$essayid]->userid]->lastname;
		echo "<tr><td>".get_string("studentresponse", "lesson", $studentname).":</td></tr><tr><td $style>";
		print_simple_box_start("left");
		echo $essays[$essayid]->answer;
		print_simple_box_end();
		echo "</td></tr>";
		echo "<tr><td>".get_string("comments", "lesson").":<br></td></tr>";
		echo "<tr><td $style>";
		echo "<textarea id=\"answer\" name=\"response\" align=\"center\" rows=\"10\" cols=\"50\">".$essays[$essayid]->response."</textarea>\n";	
		echo "</td></tr>";
		
		echo "<tr><td>".get_string("essayscore", "lesson").": </td></tr><tr><td $style>";		
		if ($lesson->custom) {
			for ($i=$answers[$essays[$essayid]->answerid]->score; $i>=0; $i--) {
            	$grades[$i] = $i;
        	}
        	lesson_choose_from_menu($grades, "score", $essays[$essayid]->score, "");
		} else {
			$options[0] = "incorrect"; $options[1] = "correct";
			lesson_choose_from_menu($options, "score", $essays[$essayid]->score, "");
		}
		echo "</td></tr>";		
		echo "</table>";
		
		echo "<table align=\"center\"><tr><td>";
		echo "<input type=\"button\" value=\"Cancel\" onclick=\"document.essaygrade.action.value='essayview';".
			 "document.essaygrade.submit();\">";
		echo "</td><td>";
		echo "<input type=\"button\" value=\"Submit Grade\" onclick=\"document.essaygrade.action.value='updategrade';".
			 "document.essaygrade.submit();\">";
		echo "</td></tr></table>";
		echo "</form>";
	}

	/*******************update grade**************************************/ // 6/29/04
    elseif ($action == 'updategrade') {
		print_heading_with_help($lesson->name, "overview", "lesson");
		
		$userid = $_POST['userid'];
		
		if (!$essays = get_records_select("lesson_essay", "lessonid = $lesson->id", "timesubmitted")) {
			error("Error: could not find essays");
		}
		if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id and userid= $userid", "completed")) {
			error("Error: could not find grades");
		}

		echo "<div align=\"center\">";
		echo "Please wait while updating...<br>"; 

		$form = data_submitted();
		
		$update->id = $form->essayid;
		$update->graded = true;
		$update->score = $form->score;
		$update->response = $form->response;
		$update->sent = false;
		
		for ($i = 0; $i < $essays[$form->essayid]->try; $i++) {
			next($grades);
		}
		$grade = current($grades);
		// if teacher goes back and changes score, then need to take the old score off
		$resetgrade = $grade->grade - ($essays[$form->essayid]->score / $lesson->grade * 100);
		// now add the new score
		$newgrade = ($form->score / $lesson->grade * 100) + $resetgrade;
		if ($newgrade > 100) {
			$newgrade = 100;
		} elseif ($newgrade < 0) {
			$newgrade = 0;
		}
		$updategrade->id = $grade->id;
		$updategrade->grade = $newgrade;

		if(update_record("lesson_essay", $update) && update_record("lesson_grades", $updategrade)) {
			redirect("view.php?id=$cm->id&action=essayview", get_string("updatesuccess", "lesson"));
		} else {
			echo get_string("updatefailed", "lesson")."!<br>";
			echo "<a href=\"view.php?id=$cm->id&action=essayview\">".get_string("continue", "lesson")."</a>";
			exit();
		}
		echo "</div>";
	}

	/*******************email essay **************************************/ // 6/29/04
    elseif ($action == 'emailessay') {
		print_heading_with_help($lesson->name, "overview", "lesson");
	
		echo "<div align=\"center\">";
		echo "Please wait while emailing...<br>"; 

		if (isset($_GET['userid'])) {		
			$queryadd = "and userid = ".$_GET['userid'];
		} else {
			$queryadd = "";
		}

		if (!$essays = get_records_select("lesson_essay", "lessonid = $lesson->id $queryadd", "timesubmitted")) {
			error("Error: could not find essays");
		}
		if (!$pages = get_records_select("lesson_pages", "lessonid = $lesson->id")) {
			error("Error: could not find lesson pages");
		}
		if (!$users = lesson_get_participants($lesson->id)) {
			error("Error: could not find users");
		}
		if (!$answers = get_records_select("lesson_answers", "lessonid = $lesson->id")) {
			error("Error: could not find essays");
		}
		// NoticeFix  big fix, change $essay[]'s that use $USER to just $USER
		foreach ($essays as $essay) {
			if ($essay->graded && !$essay->sent) {
				$subject = "Your grade for ".$pages[$essay->pageid]->title." question";
				$message = "Question:<br>\r\n";
				$message .= $pages[$essay->pageid]->contents;
				$message .= "<br><br>\r\n\r\n";
				$message .= "Your response:<br>\r\n";
				$message .= $essay->answer;
				$message .= "<br><br>\r\n\r\n";
				$message .= $USER->firstname." ".$USER->lastname."'s comments:<br>\r\n";
				$message .= $essay->response;
				$message .= "<br><br>\r\n\r\n";
				$grades = get_records_select("lesson_grades", "lessonid = $lesson->id and userid = $essay->userid", "completed");
				for ($i = 0; $i < $essay->try; $i++) {
					next($grades);
				}
				$grade = current($grades);
				reset($grades);
				if ($lesson->custom) {
					$message .= "You have received $essay->score points out of $lesson->grade".".<br>\r\n";
					$message .= "Your grade for the lesson has been changed to $grade->grade"."%.\r\n";
				} else {
					// cannot think of a way to update if not custom...
				}
				if(email_to_user($users[$essay->userid], $USER, $subject, $message, $message)) {
					$updateessay->id = $essay->id;
					$updateessay->sent = true;
					update_record("lesson_essay", $updateessay);
				} else {
					echo "Email Failed!<br>";
					echo "<a href=\"view.php?id=$cm->id&action=essayview\">".get_string("continue", "lesson")."</a>";
					echo "</div>";
					exit();
				}
			}
		}
		redirect("view.php?id=$cm->id&action=essayview", get_string("emailsuccess", "lesson"));
	}

	/*******************high scores **************************************/
    elseif ($action == 'highscores') {
		print_heading_with_help($lesson->name, "overview", "lesson");

		if (!$grades = get_records_select("lesson_grades", "lessonid = $lesson->id", "completed")) {
			$grades = array();
		}
	
		echo "<div align=\"center\">";
		$titleinfo->maxhighscores = $lesson->maxhighscores;
		$titleinfo->name = $lesson->name;
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
			echo "<tr align=\"center\" bgcolor=\"$THEME->cellheading2\"><td>".get_string("rank", "lesson")."</td><td>$course->students</td><td>".get_string("scores", "lesson")."</td></tr>";
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
			echo "<br><a href=\"../../course/view.php?id=$course->id\">".get_string("returntocourse", "lesson")."</a>";
		} else {
			echo "<br><a href=\"../../course/view.php?id=$course->id\">".get_string("cancel", "lesson")."</a> | <a href=\"view.php?id=$cm->id&action=navigation\">".get_string("startlesson", "lesson")."</a>";
		}
		echo "</div>";
			
	}
	/*******************update high scores **************************************/
    elseif ($action == 'updatehighscores') {
		print_heading_with_help($lesson->name, "overview", "lesson");

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
				redirect("view.php?id=$cm->id&action=highscores&link=1", "Update Successful");
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
					$deletestmt = "lessonid = $lesson-> id and userid = ";
					$deletestmt .= current($lowscore);
					while (next($lowscore)) {
						$deletestmt .= " or userid = ".current($lowscore);
					}
					if (!delete_records_select("lesson_high_scores", $deletestmt)) {
						/// not a big deal...
						error("Did not delete extra high score(s)");
					}
				}
			}
		}
		
		$newhighscore->lessonid = $lesson->id;
		$newhighscore->userid = $USER->id;
		$newhighscore->gradeid = $newgrade->id;
		if (isset($_GET['name'])) {
			$newhighscore->nickname = $_GET['name'];
		}
		if (!insert_record("lesson_high_scores", $newhighscore)) {
			error("Insert of new high score Failed!");
		}
		
		redirect("view.php?id=$cm->id&action=highscores&link=1", get_string("postsuccess", "lesson"));
		echo "</div>";
	}
	/*******************name for highscores **************************************/
    elseif ($action == 'nameforhighscores') {
		print_heading_with_help($lesson->name, "overview", "lesson");
		echo "<div align=\"center\">";
		if (isset($_POST['name'])) {
			if (lesson_check_nickname(trim($_POST['name']))) {
				redirect("view.php?id=$cm->id&action=updatehighscores&name=".trim($_POST['name']), get_string("nameapproved", "lesson"));
			} else {
				echo get_string("namereject", "lesson")."<br><br>";
			}
		}
				
		echo "<form name=\"nickname\" method =\"post\" action=\"view.php\">";
		echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"nameforhighscores\">";
		
		echo get_string("entername", "lesson").": <input type=\"text\" name=\"name\" maxlength=\"5\"><br>";
		echo "<input type=\"submit\" value=\"".get_string("submitname", "lesson")."\">";
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
