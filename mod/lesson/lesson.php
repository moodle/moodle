<?PHP  // $Id: lesson.php, v 1.0 25 Jan 2004

/*************************************************
	ACTIONS handled are:

	addbranchtable
    addendofbranch
    addpage
    confirmdelete
    continue
	delete
   	editpage
    insertpage
    move
	moveit
	updatepage

************************************************/

    require("../../config.php");
	require("lib.php");

	require_variable($id);    // Course Module ID
 
    // get some esential stuff...
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
	
    // set up some general variables
    $usehtmleditor = can_use_html_editor();
    
    $navigation = "";
    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strlessons = get_string("modulenameplural", "lesson");
    $strlesson  = get_string("modulename", "lesson");
    $strlessonname = $lesson->name;
	
	// ... print the header and...
    print_header("$course->shortname: $lesson->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strlessons</A> -> 
                  <A HREF=\"view.php?id=$cm->id\">$lesson->name</A>", 
                  "", "", true);

	//...get the action 
	require_variable($action);
	

	/************** add branch table ************************************/
	if ($action == 'addbranchtable' ) {

       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

        // first get the preceeding page
        $pageid = $_GET['pageid'];
            
        // set of jump array
        $jump[0] = get_string("thispage", "lesson");
        $jump[LESSON_NEXTPAGE] = get_string("nextpage", "lesson");
        $jump[LESSON_EOL] = get_string("endoflesson", "lesson");
        if (!$apageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
            error("Add page: first page not found");
        }
        while (true) {
            if ($apageid) {
                $title = get_field("lesson_pages", "title", "id", $apageid);
                $jump[$apageid] = $title;
                $apageid = get_field("lesson_pages", "nextpageid", "id", $apageid);
            } else {
                // last page reached
                break;
            }
        }
 
        // give teacher a blank proforma
		print_heading_with_help(get_string("addabranchtable", "lesson"), "overview", "lesson");
        ?>
        <form name="form" method="post" action="lesson.php">
        <input type="hidden" name="id" value="<?PHP echo $cm->id ?>">
        <input type="hidden" name="action" value="insertpage">
        <input type="hidden" name="pageid" value="<?PHP echo $_GET['pageid'] ?>">
        <input type="hidden" name="qtype" value="<?PHP echo LESSON_BRANCHTABLE ?>">
        <center><table cellpadding=5 border=1>
        <tr><td align="center">
        <tr valign="top">
        <td><b><?php print_string("pagetitle", "lesson"); ?>:</b><br />
        <input type="text" name="title" size="80" maxsize="255" value=""></td></tr>
        <?PHP
        echo "<tr><td><b>";
        echo get_string("pagecontents", "lesson").":</b><br />\n";
        print_textarea($usehtmleditor, 25,70, 630, 400, "contents");
        echo "</td></tr>\n";
        for ($i = 0; $i < $lesson->maxanswers; $i++) {
            $iplus1 = $i + 1;
            echo "<tr><td><b>".get_string("description", "lesson")." $iplus1:</b><br />\n";
            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$i]");
            echo "</td></tr>\n";
            echo "<tr><td><B>".get_string("jump", "lesson")." $iplus1:</b> \n";
            if ($i) {
                // answers 2, 3, 4... jumpto this page
                lesson_choose_from_menu($jump, "jumpto[$i]", 0, "");
            } else {
                // answer 1 jumpto next page
                lesson_choose_from_menu($jump, "jumpto[$i]", LESSON_NEXTPAGE, "");
            }
            helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
            echo "</td></tr>\n";
        }
        use_html_editor();
        // close table and form
        ?>
        </table><br />
        <input type="submit" value="<?php  print_string("addabranchtable", "lesson") ?>">
        <input type="submit" name="cancel" value="<?php  print_string("cancel") ?>">
        </center>
        </form>
        <?PHP
	}
	

	/************** add end of branch ************************************/
    elseif ($action == 'addendofbranch' ) {

       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

        // first get the preceeding page
        $pageid = $_GET['pageid'];
            
        $timenow = time();
        
        // the new page is not the first page (end of branch always comes after an existing page)
        if (!$page = get_record("lesson_pages", "id", $pageid)) {
            error("Add end of branch: page record not found");
        }
        // chain back up to find the (nearest branch table)
        $btpageid = $pageid;
        if (!$btpage = get_record("lesson_pages", "id", $btpageid)) {
            error("Add end of branch: btpage record not found");
        }
        while (($btpage->qtype != LESSON_BRANCHTABLE) AND ($btpage->prevpageid > 0)) {
            $btpageid = $btpage->prevpageid;
            if (!$btpage = get_record("lesson_pages", "id", $btpageid)) {
                error("Add end of branch: btpage record not found");
            }
        }
        if ($btpage->qtype == LESSON_BRANCHTABLE) {
            $newpage->lessonid = $lesson->id;
            $newpage->prevpageid = $pageid;
            $newpage->nextpageid = $page->nextpageid;
            $newpage->qtype = LESSON_ENDOFBRANCH;
            $newpage->timecreated = $timenow;
            $newpage->title = get_string("endofbranch", "lesson");
            $newpage->contents = get_string("endofbranch", "lesson");
            if (!$newpageid = insert_record("lesson_pages", $newpage)) {
                error("Insert page: new page not inserted");
            }
            // update the linked list...
            if (!set_field("lesson_pages", "nextpageid", $newpageid, "id", $pageid)) {
                error("Add end of branch: unable to update link");
            }
            if ($page->nextpageid) {
                // the new page is not the last page
                if (!set_field("lesson_pages", "prevpageid", $newpageid, "id", $page->nextpageid)) {
                    error("Insert page: unable to update previous link");
                }
            }
            // ..and the single "answer"
            $newanswer->lessonid = $lesson->id;
            $newanswer->pageid = $newpageid;
            $newanswer->timecreated = $timenow;
            $newanswer->jumpto = $btpageid;
            if(!$newanswerid = insert_record("lesson_answers", $newanswer)) {
                error("Add end of branch: answer record not inserted");
            }
            redirect("view.php?id=$cm->id", get_string("ok"));
        } else {
            notice(get_string("nobranchtablefound", "lesson"), "view.php?id=$cm->id");
        }
	}
	

	/************** add page ************************************/
    elseif ($action == 'addpage' ) {

       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

        // first get the preceeding page
        $pageid = $_GET['pageid'];
            
        // set of jump array
        $jump[0] = get_string("thispage", "lesson");
        $jump[LESSON_NEXTPAGE] = get_string("nextpage", "lesson");
        $jump[LESSON_EOL] = get_string("endoflesson", "lesson");
        if (!$apageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
            error("Add page: first page not found");
        }
        while (true) {
            if ($apageid) {
                $title = get_field("lesson_pages", "title", "id", $apageid);
                $jump[$apageid] = $title;
                $apageid = get_field("lesson_pages", "nextpageid", "id", $apageid);
            } else {
                // last page reached
                break;
            }
        }
 
        // give teacher a blank proforma
		print_heading_with_help(get_string("addaquestionpage", "lesson"), "overview", "lesson");
        ?>
        <form name="form" method="post" action="lesson.php">
        <input type="hidden" name="id" value="<?PHP echo $cm->id ?>">
        <input type="hidden" name="action" value="insertpage">
        <input type="hidden" name="pageid" value="<?PHP echo $_GET['pageid'] ?>">
        <center><table cellpadding=5 border=1>
        <tr><td align="center">
        <tr valign="top">
        <td><b><?php print_string("pagetitle", "lesson"); ?>:</b><br />
        <input type="text" name="title" size="80" maxsize="255" value=""></td></tr>
        <?PHP
        echo "<tr><td><b>";
        echo get_string("pagecontents", "lesson").":</b><br />\n";
        print_textarea($usehtmleditor, 25,70, 630, 400, "contents");
        use_html_editor("contents");
        echo "</td></tr>\n";
        echo "<tr><td><b>".get_string("questiontype", "lesson").":</b> \n";
        choose_from_menu($LESSON_QUESTION_TYPE, "qtype", LESSON_MULTICHOICE, "");
        helpbutton("questiontype", get_string("questiontype", "lesson"), "lesson");
        echo "<br /><b>".get_string("questionoption", "lesson").":</b>\n";
        echo " <input type=\"checkbox\" name=\"qoption\" value=\"1\"/>";
        helpbutton("questionoption", get_string("questionoption", "lesson"), "lesson");
        echo "</td></tr>\n";
        for ($i = 0; $i < $lesson->maxanswers; $i++) {
            $iplus1 = $i + 1;
            echo "<tr><td><b>".get_string("answer", "lesson")." $iplus1:</b><br />\n";
            print_textarea(false, 6, 70, 630, 300, "answer[$i]");
            echo "</td></tr>\n";
            echo "<tr><td><b>".get_string("response", "lesson")." $iplus1:</b><br />\n";
            print_textarea(false, 6, 70, 630, 300, "response[$i]");
            echo "</td></tr>\n";
            echo "<tr><td><B>".get_string("jump", "lesson")." $iplus1:</b> \n";
            if ($i) {
                // answers 2, 3, 4... jumpto this page
                lesson_choose_from_menu($jump, "jumpto[$i]", 0, "");
            } else {
                // answer 1 jumpto next page
                lesson_choose_from_menu($jump, "jumpto[$i]", LESSON_NEXTPAGE, "");
            }
            helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
            echo "</td></tr>\n";
        }
        // close table and form
        ?>
        </table><br />
        <input type="submit" value="<?php  print_string("addaquestionpage", "lesson") ?>">
        <input type="submit" name="cancel" value="<?php  print_string("cancel") ?>">
        </center>
        </form>
        <?PHP
		}
	

	/******************* confirm delete ************************************/
    elseif ($action == 'confirmdelete' ) {

       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

		if (empty($_GET['pageid'])) {
			error("Confirm delete: pageid missing");
		}
        $pageid = $_GET['pageid'];
        if (!$thispage = get_record("lesson_pages", "id", $pageid)) {
            error("Confirm delete: the page record not found");
        }
        print_heading(get_string("deletingpage", "lesson", $thispage->title));
        // print the jumps to this page
        if ($answers = get_records_select("lesson_answers", "lessonid = $lesson->id AND jumpto = $pageid + 1")) {
            print_heading(get_string("thefollowingpagesjumptothispage", "lesson"));
            echo "<p align=\"center\">\n";
            foreach ($answers as $answer) {
                if (!$title = get_field("lesson_pages", "title", "id", $answer->pageid)) {
                    error("Confirm delete: page title not found");
                }
                echo $title."<br />\n";
            }
        }
		notice_yesno(get_string("confirmdeletionofthispage","lesson"), 
			 "lesson.php?action=delete&id=$cm->id&pageid=$pageid]", 
             "view.php?id=$cm->id");
		}
	

	/****************** continue ************************************/
	elseif ($action == 'continue' ) {
        // record answer (if necessary) and show response (if none say if answer is correct or not)


        if (empty($_POST['pageid'])) {
			error("Continue: pageid missing");
		}
        $pageid = $_POST['pageid'];
        if (!$page = get_record("lesson_pages", "id", $pageid)) {
            error("Continue: Page record not found");
        }
        // set up some defaults
        $answerid = 0;
        $noanswer = false;
        $correctanswer = false;
        $newpageid = 0; // stay on the page
        switch ($page->qtype) {
            case LESSON_SHORTANSWER :
                if (!$useranswer = $_POST['answer']) {
                    $noanswer = true;
                    break;
                }
                if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                    error("Continue: No answers found");
                }
                foreach ($answers as $answer) {
                    if (lesson_iscorrect($pageid, $answer->jumpto)) {
                        if ($page->qoption) {
                            // case sensitive
                            if ($answer->answer == $useranswer) {
                                $correctanswer = true;
                                $newpageid = $answer->jumpto;
                                if (trim(strip_tags($answer->response))) {
                                    $response = $answer->response;
                                }
                            }
                        } else {
                            // case insensitive
                            if (strcasecmp($answer->answer, $useranswer) == 0) {
                                $correctanswer = true;
                                $newpageid = $answer->jumpto;
                                if (trim(strip_tags($answer->response))) {
                                    $response = $answer->response;
                                }
                            }
                        }
                    } else {
                        // see if user typed in any of the wrong answers
                        // don't worry about case
                        if (strcasecmp($answer->answer, $useranswer) == 0) {
                            $newpageid = $answer->jumpto;
                            if (trim(strip_tags($answer->response))) {
                                $response = $answer->response;
                            }
                        }
                    }
                }
                if (!isset($response)) {
                    if ($correctanswer) {
                        $response = get_string("thatsthecorrectanswer", "lesson");
                    } else {
                        $response = get_string("thatsthewronganswer", "lesson");
                    }
                }
                break;
                
            case LESSON_TRUEFALSE :
                if (empty($_POST['answerid'])) {
                    $noanswer = true;
                    break;
                }
                $answerid = $_POST['answerid']; 
                if (!$answer = get_record("lesson_answers", "id", $answerid)) {
                    error("Continue: answer record not found");
                } 
                if (lesson_iscorrect($pageid, $answer->jumpto)) {
                    $correctanswer = true;
                }
                $newpageid = $answer->jumpto;
                if (!$response = trim($answer->response)) {
                    if ($correctanswer) {
                        $response = get_string("thatsthecorrectanswer", "lesson");
                    } else {
                        $response = get_string("thatsthewronganswer", "lesson");
                    }
                }
                break;
                
            case LESSON_MULTICHOICE :
                if ($page->qoption) {
                    // MULTIANSWER allowed, user's answer is an array
                    if (isset($_POST['answer'])) {
                        $useranswers = $_POST['answer'];
                    } else {
                        $noanswer = true;
                        break;
                    }
                    // get the answers in a set order, the id order
                    if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                        error("Continue: No answers found");
                    }
                    $ncorrect = 0;
                    $nhits = 0;
                    $correctresponse = '';
                    $wrongresponse = '';
                    foreach ($answers as $answer) {
                        if (lesson_iscorrect($pageid, $answer->jumpto)) {
                            $ncorrect++;
                            foreach ($useranswers as $key => $answerid) {
                                if ($answerid == $answer->id) {
                                    $nhits++;
                                }
                            }
                            // save the first jumpto page id, may be needed!...
                            if (!isset($correctpageid)) {  
                                // leave in its "raw" state - will converted into a proper page id later
                                $correctpageid = $answer->jumpto;
                            }
                            // ...also save any response from the correct answers...
                            if (trim(strip_tags($answer->response))) {
                                $correctresponse = $answer->response;
                            }
                        } else {
                            // save the first jumpto page id, may be needed!...
                            if (!isset($wrongpageid)) {   
                                // leave in its "raw" state - will converted into a proper page id later
                                $wrongpageid = $answer->jumpto;
                            }
                            // ...and from the incorrect ones, don't know which to use at this stage
                            if (trim(strip_tags($answer->response))) {
                                $wrongresponse = $answer->response;
                            }
                        }
                    }
                    if ((count($useranswers) == $ncorrect) and ($nhits == $ncorrect)) {
                        $correctanswer = true;
                        if (!$response = $correctresponse) {
                            $response = get_string("thatsthecorrectanswer", "lesson");
                        }
                        $newpageid = $correctpageid;
                    } else {
                        if (!$response = $wrongresponse) {
                            $response = get_string("thatsthewronganswer", "lesson");
                        }
                        $newpageid = $wrongpageid;
                    }
                } else {
                    // only one answer allowed
                    if (empty($_POST['answerid'])) {
                        $noanswer = true;
                        break;
                    }
                    $answerid = $_POST['answerid']; 
                    if (!$answer = get_record("lesson_answers", "id", $answerid)) {
                        error("Continue: answer record not found");
                    } 
                    if (lesson_iscorrect($pageid, $answer->jumpto)) {
                        $correctanswer = true;
                    }
                    $newpageid = $answer->jumpto;
                    if (!$response = trim($answer->response)) {
                        if ($correctanswer) {
                            $response = get_string("thatsthecorrectanswer", "lesson");
                        } else {
                            $response = get_string("thatsthewronganswer", "lesson");
                        }
                    }
                }
                break;
                
            case LESSON_MATCHING :
                if (isset($_POST['response'])) {
                    $response = $_POST['response'];
                } else {
                    $noanswer = true;
                    break;
                }
                if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                    error("Continue: No answers found");
                }
                $ncorrect = 0;
                $i = 0;
                foreach ($answers as $answer) {
                    if ($answer->response == $response[$answer->id]) {
                        $ncorrect++;
                    }
                    if ($i == 0) {
                        $correctpageid = $answer->jumpto;
                    }
                    if ($i == 1) {
                        $wrongpageid = $answer->jumpto;
                    }
                    $i++;
                }
                if ($ncorrect == count($answers)) {
                    $response = get_string("thatsthecorrectanswer", "lesson");
                    $newpageid = $correctpageid;
                    $correctanswer = true;
                } else {
                    $response = get_string("numberofcorrectmatches", "lesson", $ncorrect);
                    $newpageid = $wrongpageid;
                }
                break;

            case LESSON_NUMERICAL :
                // set defaults
                $response = '';
                $newpageid = 0;

                if (!$useranswer = (float) $_POST['answer']) {
                    $noanswer = true;
                    break;
                }
                if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                    error("Continue: No answers found");
                }
                foreach ($answers as $answer) {
                    if (strpos($answer->answer, ':')) {
                        // there's a pairs of values
                        list($min, $max) = explode(':', $answer->answer);
                        $minimum = (float) $min;
                        $maximum = (float) $max;
                    } else {
                        // there's only one value
                        $minimum = (float) $answer->answer;
                        $maximum = $minimum;
                    }
                    if (($useranswer >= $minimum) and ($useranswer <= $maximum)) {
                        $newpageid = $answer->jumpto;
                        $response = trim($answer->response);
                        if (lesson_iscorrect($pageid, $newpageid)) {
                            $correctanswer = true;
                        }
                        break;
                    }
                }
                
                if ($correctanswer) {
                    if (!$response) {
                        $response = get_string("thatsthecorrectanswer", "lesson");
                    }
                } else {
                    if (!$response) {
                        $response = get_string("thatsthewronganswer", "lesson");
                    }
                }           
                break;

            case LESSON_BRANCHTABLE:
                $noanswer = false;
                $newpageid = $_POST['jumpto'];
                // convert jumpto page into a proper page id
                if ($newpageid == 0) {
                    $newpageid = $pageid;
                } elseif ($newpageid == LESSON_NEXTPAGE) {
                    if (!$newpageid = $page->nextpageid) {
                        // no nextpage go to end of lesson
                        $newpageid = LESSON_EOL;
                    }
                }
                // no need to record anything in lesson_attempts 
                redirect("view.php?id=$cm->id&action=navigation&pageid=$newpageid");
            	print_footer($course);
                exit();
                break;
                
        }
        if ($noanswer) {
            $newpageid = $pageid; // display same page again
            print_simple_box(get_string("noanswer", "lesson"), "center");
        } else {
            $nretakes = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id); 
            if (isstudent($course->id)) {
                // record student's attempt
                $attempt->lessonid = $lesson->id;
                $attempt->pageid = $pageid;
                $attempt->userid = $USER->id;
                $attempt->answerid = $answerid;
                $attempt->retry = $nretakes;
                $attempt->correct = $correctanswer;
                $attempt->timeseen = time();
                if (!$newattemptid = insert_record("lesson_attempts", $attempt)) {
                    error("Continue: attempt not inserted");
                }
                if (!$correctanswer and ($newpageid == 0)) {
                    // wrong answer and student is stuck on this page - check how many attempts 
                    // the student has had at this page/question
                    $nattempts = count_records("lesson_attempts", "pageid", $pageid, "userid", $USER->id,
                        "retry", $nretakes);

                    if ($nattempts >= $lesson->maxattempts) {
                        if ($lesson->maxattempts > 1) { // don't bother with message if only one attempt
                            echo "<p align=\"center\">(".get_string("maximumnumberofattempts", "lesson").
                                " ".get_string("reached", "lesson")." - ".
                                get_string("movingtonextpage", "lesson").")</p>\n";
                        }
                        $newpageid = LESSON_NEXTPAGE;
                    }
                }
            }
            // convert jumpto page into a proper page id
            if ($newpageid == 0) {
                $newpageid = $pageid;
            } elseif ($newpageid == LESSON_NEXTPAGE) {
                if ($lesson->nextpagedefault) {
                    // in Flash Card mode...
                    // ... first get the page ids (lessonid the 5th param is needed to make get_records play)
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
                } elseif (!$newpageid = $page->nextpageid) {
                    // no nextpage go to end of lesson
                    $newpageid = LESSON_EOL;
                }
            }
            
            // display response (if there is one - there should be!)
            if ($response) {
                $title = get_field("lesson_pages", "title", "id", $pageid);
                print_heading($title);
                echo "<table width=\"80%\" border=\"0\" align=\"center\"><tr><td>\n";
                print_simple_box(format_text($response), 'center');
                echo "</td></tr></table>\n";
            }
        }
        echo "<form name=\"pageform\" method =\"post\" action=\"view.php\">\n";
        echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
        echo "<input type=\"hidden\" name=\"action\" value=\"navigation\">\n";
        echo "<input type=\"hidden\" name=\"pageid\" value=\"$newpageid\">\n";
        echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
            get_string("continue", "lesson")."\"></p>\n";
        echo "</form>\n";
	}
	


	/******************* delete ************************************/
	elseif ($action == 'delete' ) {

       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

		if (empty($_GET['pageid'])) {
			error("Delete: pageid missing");
		}
        $pageid = $_GET['pageid'];
	    if (!$thispage = get_record("lesson_pages", "id", $pageid)) {
		    error("Delete: page record not found");
        }
        
        print_string("deleting", "lesson");
		// first delete all the associated records...
		delete_records("lesson_attempts", "pageid", $pageid);
		// ...now delete the answers...
		delete_records("lesson_answers", "pageid", $pageid);
        // ..and the page itself
        delete_records("lesson_pages", "id", $pageid);
		
        // repair the hole in the linkage
        if (!$thispage->prevpageid) {
            // this is the first page...
            if (!$page = get_record("lesson_pages", "id", $thispage->nextpageid)) {
                error("Delete: next page not found");
            }
            if (!set_field("lesson_pages", "prevpageid", 0, "id", $page->id)) {
                error("Delete: unable to set prevpage link");
            }
        } elseif (!$thispage->nextpageid) {
            // this is the last page...
            if (!$page = get_record("lesson_pages", "id", $thispage->prevpageid)) {
                error("Delete: prev page not found");
            }
            if (!set_field("lesson_pages", "nextpageid", 0, "id", $page->id)) {
                error("Delete: unable to set nextpage link");
            }
        } else {
            // page is in the middle...
            if (!$prevpage = get_record("lesson_pages", "id", $thispage->prevpageid)) {
                error("Delete: prev page not found");
            }
            if (!$nextpage = get_record("lesson_pages", "id", $thispage->nextpageid)) {
                error("Delete: next page not found");
            }
            if (!set_field("lesson_pages", "nextpageid", $nextpage->id, "id", $prevpage->id)) {
                error("Delete: unable to set next link");
            }
            if (!set_field("lesson_pages", "prevpageid", $prevpage->id, "id", $nextpage->id)) {
                error("Delete: unable to set prev link");
            }
        }
   		redirect("view.php?id=$cm->id", get_string("ok"));
	}
	


	/************** edit page ************************************/
    elseif ($action == 'editpage' ) {

       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

        // get the page
        if (!$page = get_record("lesson_pages", "id", $_GET['pageid'])) {
            error("Edit page: page record not found");
        }
        // set of jump array
        $jump[0] = get_string("thispage", "lesson");
        $jump[LESSON_NEXTPAGE] = get_string("nextpage", "lesson");
        $jump[LESSON_EOL] = get_string("endoflesson", "lesson");
        if (!$apageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
            error("Edit page: first page not found");
        }
        while (true) {
            if ($apageid) {
                if (!$apage = get_record("lesson_pages", "id", $apageid)) {
                    error("Edit page: apage record not found");
                }
                if ($apage->qtype != LESSON_ENDOFBRANCH) {
                    // don't include EOB's in the list...
                    if (trim($page->title)) { // ...nor nuffin pages
                        $jump[$apageid] = $apage->title;
                    }
                }
                $apageid = $apage->nextpageid;
            } else {
                // last page reached
                break;
            }
        }
        
        // give teacher a proforma
        ?>
        <form name="editpage" method="post" action="lesson.php">
        <input type="hidden" name="id" value="<?PHP echo $cm->id ?>">
        <input type="hidden" name="action" value="updatepage">
        <input type="hidden" name="pageid" value="<?PHP echo $_GET['pageid'] ?>">
        <input type="hidden" name="redisplay" value="0">
        <center><table cellpadding=5 border=1>
        <tr><td align="center">
        <tr valign="top">
        <td><b><?php print_string("pagetitle", "lesson"); ?>:</b><br />
        <input type="text" name="title" size="80" maxsize="255" value="<?PHP echo $page->title ?>"></td>
        </tr>
        <?PHP
        echo "<tr><td><b>";
        echo get_string("pagecontents", "lesson").":</b><br />\n";
        print_textarea($usehtmleditor, 25, 70, 630, 400, "contents", $page->contents);
        use_html_editor("contents"); // always the editor
        echo "</td></tr>\n";
        $n = 0;
        switch ($page->qtype) {
            case LESSON_SHORTANSWER :
                echo "<tr><td><b>".get_string("questiontype", "lesson").":</b> \n";
                choose_from_menu($LESSON_QUESTION_TYPE, "qtype", $page->qtype, "");
                echo "&nbsp;&nbsp;";
                echo " <b>".get_string("casesensitive", "lesson").":</b> \n";
                if ($page->qoption) {
                    echo "<input type=\"checkbox\" name=\"qoption\" value=\"1\" checked=\"checked\"/>";
                } else {
                    echo "<input type=\"checkbox\" name=\"qoption\" value=\"1\"/>";
                }
                helpbutton("questiontypes", get_string("questiontype", "lesson"), "lesson");
                break;
            case LESSON_MULTICHOICE :
                echo "<tr><td><b>".get_string("questiontype", "lesson").":</b> \n";
                choose_from_menu($LESSON_QUESTION_TYPE, "qtype", $page->qtype, "");
                echo "&nbsp;&nbsp;";
                echo " <b>".get_string("multianswer", "lesson").":</b> \n";
                if ($page->qoption) {
                    echo "<input type=\"checkbox\" name=\"qoption\" value=\"1\" checked=\"checked\"/>";
                } else {
                    echo "<input type=\"checkbox\" name=\"qoption\" value=\"1\"/>";
                }
                helpbutton("questiontypes", get_string("questiontype", "lesson"), "lesson");
                break;
            case LESSON_TRUEFALSE :
            case LESSON_MATCHING :
            case LESSON_NUMERICAL :
                echo "<tr><td><b>".get_string("questiontype", "lesson").":</b> \n";
                choose_from_menu($LESSON_QUESTION_TYPE, "qtype", $page->qtype, "");
                helpbutton("questiontypes", get_string("questiontype", "lesson"), "lesson");
                break;
            case LESSON_BRANCHTABLE :
                echo "<input type=\"hidden\" name=\"qtype\" value=\"$page->qtype\">\n";
                echo "<tr><td><b>".get_string("branchtable", "lesson")."</b> \n";
                break;
            case LESSON_ENDOFBRANCH :
                echo "<input type=\"hidden\" name=\"qtype\" value=\"$page->qtype\">\n";
                echo "<tr><td><b>".get_string("endofbranch", "lesson")."</b> \n";
                break;                
        }       
        echo "</td></tr>\n";
        // get the answers in a set order, the id order
        if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
            foreach ($answers as $answer) {
                $flags = intval($answer->flags); // force into an integer
                $nplus1 = $n + 1;
                echo "<input type=\"hidden\" name=\"answerid[$n]\" value=\"$answer->id\">\n";
                switch ($page->qtype) {
                    case LESSON_MULTICHOICE:
                    case LESSON_TRUEFALSE:
                    case LESSON_SHORTANSWER:
                    case LESSON_NUMERICAL:
                    case LESSON_MATCHING:
                        echo "<tr><td><b>".get_string("answer", "lesson")." $nplus1:</b>\n";
                        if ($flags & LESSON_ANSWER_EDITOR) {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<input type=\"checkbox\" name=\"answereditor[$n]\" value=\"1\" 
                                checked=\"checked\">";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                            use_html_editor("answer[$n]"); // switch on the editor
                        } else {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<input type=\"checkbox\" name=\"answereditor[$n]\" value=\"1\">";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
                        }
                        echo "</td></tr>\n";
                        echo "<tr><td><b>".get_string("response", "lesson")." $nplus1:</b>\n";
                        if ($flags & LESSON_RESPONSE_EDITOR) {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<input type=\"checkbox\" name=\"responseeditor[$n]\" value=\"1\" 
                                checked=\"checked\">";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "response[$n]", $answer->response);
                            use_html_editor("response[$n]"); // switch on the editor
                        } else {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<input type=\"checkbox\" name=\"responseeditor[$n]\" value=\"1\">";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea(false, 6, 70, 630, 300, "response[$n]", $answer->response);
                        }
                        echo "</td></tr>\n";
                        break;
                    case LESSON_BRANCHTABLE:
                        echo "<tr><td><b>".get_string("description", "lesson")." $nplus1:</b>\n";
                        if ($flags & LESSON_ANSWER_EDITOR) {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<input type=\"checkbox\" name=\"answereditor[$n]\" value=\"1\" 
                                checked=\"checked\">";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                        } else {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<input type=\"checkbox\" name=\"answereditor[$n]\" value=\"1\">";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea(false, 10, 70, 630, 300, "answer[$n]", $answer->answer);
                        }
                        echo "</td></tr>\n";
                        break;
                }
                echo "<tr><td><b>".get_string("jump", "lesson")." $nplus1:</b> \n";
                lesson_choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
                helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
                echo "</td></tr>\n";
                $n++;
            }
        }
        if ($page->qtype != LESSON_ENDOFBRANCH) {
            for ($i = $n; $i < $lesson->maxanswers; $i++) {
                $iplus1 = $i + 1;
                echo "<input type=\"hidden\" name=\"answerid[$i]\" value=\"0\">\n";
                switch ($page->qtype) {
                    case LESSON_MULTICHOICE:
                    case LESSON_TRUEFALSE:
                    case LESSON_SHORTANSWER:
                    case LESSON_NUMERICAL:
                    case LESSON_MATCHING:
                        echo "<tr><td><b>".get_string("answer", "lesson")." $iplus1:</b>\n";
                        echo " [".get_string("useeditor", "lesson").": ".
                            "<input type=\"checkbox\" name=\"answereditor[$i]\" value=\"1\">";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea(false, 10, 70, 630, 300, "answer[$i]");
                        echo "</td></tr>\n";
                        echo "<tr><td><b>".get_string("response", "lesson")." $iplus1:</b>\n";
                        echo " [".get_string("useeditor", "lesson").": ".
                            "<input type=\"checkbox\" name=\"responseeditor[$i]\" value=\"1\">";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea(false, 10, 70, 630, 300, "response[$i]");
                        echo "</td></tr>\n";
                        break;
                    case LESSON_BRANCHTABLE:
                        echo "<tr><td><b>".get_string("description", "lesson")." $iplus1:</b>\n";
                        echo " [".get_string("useeditor", "lesson").": ".
                            "<input type=\"checkbox\" name=\"answereditor[$i]\" value=\"1\">";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea(false, 10, 70, 630, 300, "answer[$i]");
                        echo "</td></tr>\n";
                        break;
                }
                echo "<tr><td><B>".get_string("jump", "lesson")." $iplus1:</b> \n";
                lesson_choose_from_menu($jump, "jumpto[$i]", 0, "");
                helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
                echo "</td></tr>\n";
            }
        }
        // close table and form
        ?>
        </table><br />
        <input type="button" value="<?php print_string("redisplaypage", "lesson") ?>" 
            onclick="document.editpage.redisplay.value=1;document.editpage.submit();">
        <input type="submit" value="<?php  print_string("savepage", "lesson") ?>">
        <input type="submit" name="cancel" value="<?php  print_string("cancel") ?>">
        </center>
        </form>
        <?PHP
		}
	

	/****************** insert page ************************************/
	elseif ($action == 'insertpage' ) {
        
       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

        $timenow = time();
		$form = data_submitted();
        
        if ($form->pageid) {
            // the new page is not the first page
            if (!$page = get_record("lesson_pages", "id", $form->pageid)) {
                error("Insert page: page record not found");
            }
            $newpage->lessonid = $lesson->id;
            $newpage->prevpageid = $form->pageid;
            $newpage->nextpageid = $page->nextpageid;
            $newpage->timecreated = $timenow;
            $newpage->qtype = $form->qtype;
            if (isset($form->qoption)) {
                $newpage->qoption = $form->qoption;
            } else {
                $newpage->qoption = 0;
            }
            $newpage->title = $form->title;
            $newpage->contents = trim($form->contents);
            $newpageid = insert_record("lesson_pages", $newpage);
            if (!$newpageid) {
                error("Insert page: new page not inserted");
            }
            // update the linked list
            if (!set_field("lesson_pages", "nextpageid", $newpageid, "id", $form->pageid)) {
                error("Insert page: unable to update next link");
            }
            if ($page->nextpageid) {
                // new page is not the last page
                if (!set_field("lesson_pages", "prevpageid", $newpageid, "id", $page->nextpageid)) {
                    error("Insert page: unable to update previous link");
                }
            }
        } else {
            // new page is the first page
            // get the existing (first) page (if any)
            if (!$page = get_record_select("lesson_pages", "lessonid = $lesson->id AND prevpageid = 0")) {
                // there are no existing pages
                $newpage->lessonid = $lesson->id;
                $newpage->prevpageid = 0; // this is a first page
                $newpage->nextpageid = 0; // this is the only page
                $newpage->timecreated = $timenow;
                $newpage->qtype = $form->qtype;
                if (isset($form->qoption)) {
                    $newpage->qoption = $form->qoption;
                } else {
                    $newpage->qoption = 0;
                }
                $newpage->title = $form->title;
                $newpage->contents = trim($form->contents);
                $newpageid = insert_record("lesson_pages", $newpage);
                if (!$newpageid) {
                    error("Insert page: new first page not inserted");
                }
            } else {
                // there are existing pages put this at the start
                $newpage->lessonid = $lesson->id;
                $newpage->prevpageid = 0; // this is a first page
                $newpage->nextpageid = $page->id;
                $newpage->timecreated = $timenow;
                $newpage->qtype = $form->qtype;
                if (isset($form->qoption)) {
                    $newpage->qoption = $form->qoption;
                } else {
                    $newpage->qoption = 0;
                }
                $newpage->title = $form->title;
                $newpage->contents = trim($form->contents);
                $newpageid = insert_record("lesson_pages", $newpage);
                if (!$newpageid) {
                    error("Insert page: first page not inserted");
                }
                // update the linked list
                if (!set_field("lesson_pages", "prevpageid", $newpageid, "id", $page->id)) {
                    error("Insert page: unable to update link");
                }
            }
        }
        // now add the answers
        for ($i = 0; $i < $lesson->maxanswers; $i++) {
            if (trim(strip_tags($form->answer[$i]))) { // strip_tags because the HTML editor adds <p><br />...
                $newanswer->lessonid = $lesson->id;
                $newanswer->pageid = $newpageid;
                $newanswer->timecreated = $timenow;
                $newanswer->answer = trim($form->answer[$i]);
                if (isset($form->response[$i])) {
                    $newanswer->response = trim($form->response[$i]);
                }
                if (isset($form->jumpto[$i])) {
                    $newanswer->jumpto = $form->jumpto[$i];
                }
                $newanswerid = insert_record("lesson_answers", $newanswer);
                if (!$newanswerid) {
                    error("Insert Page: answer record $i not inserted");
                }
            } else {
                break;
            }
        }
   	    redirect("view.php?id=$cm->id", get_string("ok"));
	}
	

	/****************** move ************************************/
    elseif ($action == 'move') {
        
       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

        $pageid = $_GET['pageid'];
        $title = get_field("lesson_pages", "title", "id", $pageid);
        print_heading(get_string("moving", "lesson", $title));
        
        if (!$page = get_record_select("lesson_pages", "lessonid = $lesson->id AND prevpageid = 0")) {
            error("Move: first page not found");
        }

        echo "<center><table cellpadding=\"5\" border=\"1\">\n";
        echo "<tr><td><a href=\"lesson.php?id=$cm->id&action=moveit&pageid=$pageid&after=0\"><small>".
            get_string("movepagehere", "lesson")."</small></a></td></tr>\n";
        while (true) {
            if ($page->id != $pageid) {
                if (!$title = trim($page->title)) {
                    $title = "<< ".get_string("notitle", "lesson")."  >>";
                }
                echo "<tr><td bgcolor=\"$THEME->cellheading2\"><b>$title</b></td></tr>\n";
                echo "<tr><td><a href=\"lesson.php?id=$cm->id&action=moveit&pageid=$pageid&after={$page->id}\"><small>".
                    get_string("movepagehere", "lesson")."</small></a></td></tr>\n";
            }
            if ($page->nextpageid) {
                if (!$page = get_record("lesson_pages", "id", $page->nextpageid)) {
                    error("Teacher view: Next page not found!");
                }
            } else {
                // last page reached
                break;
            }
        }
        echo "</table>\n";
    }
	

	/****************** moveit ************************************/
    elseif ($action == 'moveit') {
        
       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

        $pageid = $_GET['pageid']; //  page to move
        if (!$page = get_record("lesson_pages", "id", $pageid)) {
            error("Moveit: page not found");
        }
        $after = $_GET['after']; // target page

        print_heading(get_string("moving", "lesson", $page->title));
        
        // first step. determine the new first page
        // (this is done first as the current first page will be lost in the next step)
        if (!$after) {
            // the moved page is the new first page
            $newfirstpageid = $pageid;
            // reset $after so that is points to the last page 
            // (when the pages are in a ring this will in effect be the first page)
            if ($page->nextpageid) {
                if (!$after = get_field("lesson_pages", "id", "lessonid", $lesson->id, "nextpageid", 0)) {
                    error("Moveit: last page id not found");
                }
            } else {
                // the page being moved is the last page, so the new last page will be
                $after = $page->prevpageid;
            }
        } elseif (!$page->prevpageid) {
            // the page to be moved was the first page, so the following page must be the new first page
            $newfirstpageid = $page->nextpageid;
        } else {
            // the current first page remains the first page
            if (!$newfirstpageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
                error("Moveit: current first page id not found");
            }
        }
        // the rest is all unconditional...
        
        // second step. join pages into a ring 
        if (!$firstpageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
            error("Moveit: firstpageid not found");
        }
        if (!$lastpageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "nextpageid", 0)) {
            error("Moveit: lastpage not found");
        }
        if (!set_field("lesson_pages", "prevpageid", $lastpageid, "id", $firstpageid)) {
            error("Moveit: unable to update link");
        }
        if (!set_field("lesson_pages", "nextpageid", $firstpageid, "id", $lastpageid)) {
            error("Moveit: unable to update link");
        }

        // third step. remove the page to be moved
        if (!$prevpageid = get_field("lesson_pages", "prevpageid", "id", $pageid)) {
            error("Moveit: prevpageid not found");
        }
        if (!$nextpageid = get_field("lesson_pages", "nextpageid", "id", $pageid)) {
            error("Moveit: nextpageid not found");
        }
        if (!set_field("lesson_pages", "nextpageid", $nextpageid, "id", $prevpageid)) {
            error("Moveit: unable to update link");
        }
        if (!set_field("lesson_pages", "prevpageid", $prevpageid, "id", $nextpageid)) {
            error("Moveit: unable to update link");
        }
        
        // fourth step. insert page to be moved in new place...
        if (!$nextpageid = get_field("lesson_pages", "nextpageid", "id", $after)) {
            error("Movit: nextpageid not found");
        }
        if (!set_field("lesson_pages", "nextpageid", $pageid, "id", $after)) {
            error("Moveit: unable to update link");
        }
        if (!set_field("lesson_pages", "prevpageid", $pageid, "id", $nextpageid)) {
            error("Moveit: unable to update link");
        }
        // ...and set the links in the moved page
        if (!set_field("lesson_pages", "prevpageid", $after, "id", $pageid)) {
            error("Moveit: unable to update link");
        }
        if (!set_field("lesson_pages", "nextpageid", $nextpageid, "id", $pageid)) {
            error("Moveit: unable to update link");
        }
        
        // fifth step. break the ring
        if (!$newlastpageid = get_field("lesson_pages", "prevpageid", "id", $newfirstpageid)) {
            error("Moveit: newlastpageid not found");
        }
        if (!set_field("lesson_pages", "prevpageid", 0, "id", $newfirstpageid)) {
            error("Moveit: unable to update link");
        }
        if (!set_field("lesson_pages", "nextpageid", 0, "id", $newlastpageid)) {
                error("Moveit: unable to update link");
        }
   	    redirect("view.php?id=$cm->id", get_string("ok"));
    }
	

	/****************** update page ************************************/
    elseif ($action == 'updatepage' ) {
        
       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

        $timenow = time();
		$form = data_submitted();

        $page->id = $form->pageid;
        $page->timemodified = $timenow;
        $page->qtype = $form->qtype;
        if (isset($form->qoption)) {
            $page->qoption = $form->qoption;
        } else {
            $page->qoption = 0;
        }
        $page->title = $form->title;
        $page->contents = trim($form->contents);
        if (!update_record("lesson_pages", $page)) {
            error("Update page: page not updated");
        }
        if ($page->qtype == LESSON_ENDOFBRANCH) {
            // there's just a single answer with a jump
            $oldanswer->id = $form->answerid[0];
            $oldanswer->timemodified = $timenow;
            $oldanswer->jumpto = $form->jumpto[0];
            if (!update_record("lesson_answers", $oldanswer)) {
                error("Update page: EOB not updated");
            }
        } else {
            // it's an "ordinary" page
            for ($i = 0; $i < $lesson->maxanswers; $i++) {
                // strip tags because the editor gives <p><br />...
                // also save any answers where the editor is (going to be) used
                if (trim(strip_tags($form->answer[$i])) or $form->answereditor[$i] or $form->responseeditor[$i]) {
                    if ($form->answerid[$i]) {
                        unset($oldanswer);
                        $oldanswer->id = $form->answerid[$i];
                        $oldanswer->flags = $form->answereditor[$i] * LESSON_ANSWER_EDITOR +
                            $form->responseeditor[$i] * LESSON_RESPONSE_EDITOR;
                        $oldanswer->timemodified = $timenow;
                        $oldanswer->answer = trim($form->answer[$i]);
                        if (isset($form->response[$i])) {
                            $oldanswer->response = trim($form->response[$i]);
                        }
                        $oldanswer->jumpto = $form->jumpto[$i];
                        if (!update_record("lesson_answers", $oldanswer)) {
                            error("Update page: answer $i not updated");
                        }
                    } else {
                        // it's a new answer
                        unset($newanswer); // need to clear id if more than one new answer is ben added
                        $newanswer->lessonid = $lesson->id;
                        $newanswer->pageid = $page->id;
                        $newanswer->flags = $form->answereditor[$i] * LESSON_ANSWER_EDITOR +
                            $form->responseeditor[$i] * LESSON_RESPONSE_EDITOR;
                        $newanswer->timecreated = $timenow;
                        $newanswer->answer = trim($form->answer[$i]);
                        if (isset($form->response[$i])) {
                            $newanswer->response = trim($form->response[$i]);
                        }
                        $newanswer->jumpto = $form->jumpto[$i];
                        $newanswerid = insert_record("lesson_answers", $newanswer);
                        if (!$newanswerid) {
                            error("Update page: answer record not inserted");
                        }
                    }
                } else {
                    if ($form->answerid[$i]) {
                        // need to delete blanked out answer
                        if (!delete_records("lesson_answers", "id", $form->answerid[$i])) {
                            error("Update page: unable to delete answer record");
                        }
                    }
                }
            }
        }
        if ($form->redisplay) {
            redirect("lesson.php?id=$cm->id&action=editpage&pageid=$page->id");
        } else {
       		redirect("view.php?id=$cm->id", get_string("ok"));
        }
    }
	

	/*************** no man's land **************************************/
	else {
		error("Fatal Error: Unknown Action: ".$action."\n");
	}

	print_footer($course);
 
?>

