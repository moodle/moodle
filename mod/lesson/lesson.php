<?PHP  // $Id: lesson.php, v 1.0 25 Jan 2004

/*************************************************
	ACTIONS handled are:

	addpage
    confirmdelete
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
                  <A HREF=\"view.php?id=$cm->id\">$lesson->name</A> -> $action", 
                  "", "", true);

	//...get the action 
	require_variable($action);
	

	/************** add page ************************************/
	if ($action == 'addpage' ) {

       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

        // first get the preceeding page
        $pageid = $_GET['pageid'];
            
        // set of jump array
        $jump[0] = get_string("thispage", "lesson");
        $jump[NEXTPAGE] = get_string("nextpage", "lesson");
        $jump[EOL] = get_string("endoflesson", "lesson");
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
		print_heading_with_help(get_string("addanewpage", "lesson"), "overview", "lesson");
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
        echo "</td></tr>\n";
        for ($i = 0; $i < $lesson->maxanswers; $i++) {
            $iplus1 = $i + 1;
            echo "<tr><td><b>".get_string("answer", "lesson")." $iplus1:</b><br />\n";
            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$i]");
            echo "</td></tr>\n";
            echo "<tr><td><b>".get_string("response", "lesson")." $iplus1:</b><br />\n";
            print_textarea($usehtmleditor, 20, 70, 630, 300, "response[$i]");
            echo "</td></tr>\n";
            echo "<tr><td><B>".get_string("jumpto", "lesson").":</b> \n";
            if ($i) {
                // answers 2, 3, 4... jumpto this page
                lesson_choose_from_menu($jump, "jumpto[$i]", 0, "");
            } else {
                // answer 1 jumpto next page
                lesson_choose_from_menu($jump, "jumpto[$i]", NEXTPAGE, "");
            }
            helpbutton("jumpto", get_string("jumpto", "lesson"), "lesson");
            echo "</td></tr>\n";
        }
        use_html_editor();
        // close table and form
        ?>
        </table><br />
        <input type="submit" value="<?php  print_string("addanewpage", "lesson") ?>">
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
        // record answer (if necessary) and show response (if any)

		if (empty($_POST['pageid'])) {
			error("Continue: pageid missing");
		}
        $pageid = $_POST['pageid'];
        // get the answer
        if (empty($_POST['answerid'])) {
            print_heading(get_string("noanswer", "lesson"));
   		    redirect("view.php?id=$cm->id&action=navigation&pageid=$pageid", 
                    get_string("continue", "lesson"));
        } else {
            $answerid = $_POST['answerid']; 
            if (!$answer = get_record("lesson_answers", "id", $answerid)) {
                error("Continue: answer record not found");
            } 
            $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id); 
            if (isstudent($course->id)) {
                // record student's attempt
                $correct = get_field("lesson_answers", "correct", "id", $answerid);
                $attempt->lessonid = $lesson->id;
                $attempt->pageid = $pageid;
                $attempt->userid = $USER->id;
                $attempt->answerid = $answerid;
                $attempt->retry = $ntries;
                $attempt->correct = lesson_iscorrect($pageid, $answer->jumpto);
                $attempt->timeseen = time();
                if (!$newattemptid = insert_record("lesson_attempts", $attempt)) {
                    error("Continue: attempt not inserted");
                }
            }
            
            // convert jumpto to a proper page id
            if ($answer->jumpto == 0) {
                $newpageid = $pageid;
            } elseif ($answer->jumpto == NEXTPAGE) {
                if (!$newpageid = get_field("lesson_pages", "nextpageid", "id", $pageid)) {
                    // no nextpage go to end of lesson
                    $newpageid = EOL;
                }
            } else {
                $newpageid = $answer->jumpto;
            }
            
            // display response (if there is one)
            if ($answer->response) {
                $title = get_field("lesson_pages", "title", "id", $pageid);
                print_heading($title);
                echo "<table width=\"80%\" border=\"0\" align=\"center\"><tr><td>\n";
                print_simple_box(format_text($answer->response), 'center');
                echo "</td></tr></table>\n";
		        print_continue("view.php?id=$cm->id&action=navigation&pageid=$newpageid");
            } else {
                // there's no response text - just go straight to the next page
       		    redirect("lesson.php?id=$cm->id&action=navigation&pageid=$newpageid", 
                        get_string("continue"));
            }
        }
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
        $jump[NEXTPAGE] = get_string("nextpage", "lesson");
        $jump[EOL] = get_string("endoflesson", "lesson");
        if (!$apageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
            error("Edit page: first page not found");
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
        
        // give teacher a proforma
        ?>
        <form name="form" method="post" action="lesson.php">
        <input type="hidden" name="id" value="<?PHP echo $cm->id ?>">
        <input type="hidden" name="action" value="updatepage">
        <input type="hidden" name="pageid" value="<?PHP echo $_GET['pageid'] ?>">
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
        echo "</td></tr>\n";
        $n = 0;
        if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
            foreach ($answers as $answer) {
                $nplus1 = $n + 1;
                echo "<input type=\"hidden\" name=\"answerid[$n]\" value=\"$answer->id\">\n";
                echo "<tr><td><b>".get_string("answer", "lesson")." $nplus1:</b><br />\n";
                print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                echo "</td></tr>\n";
                echo "<tr><td><b>".get_string("response", "lesson")." $nplus1:</b><br />\n";
                print_textarea($usehtmleditor, 20, 70, 630, 300, "response[$n]", $answer->response);
                echo "</td></tr>\n";
                echo "<tr><td><B>".get_string("jumpto", "lesson").":</b> \n";
                lesson_choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
                helpbutton("jumpto", get_string("jumpto", "lesson"), "lesson");
                echo "</td></tr>\n";
                $n++;
            }
        }
        for ($i = $n; $i < $lesson->maxanswers; $i++) {
            $iplus1 = $i + 1;
            echo "<input type=\"hidden\" name=\"answerid[$i]\" value=\"0\">\n";
            echo "<tr><td><b>".get_string("answer", "lesson")." $iplus1:</b><br />\n";
            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$i]");
            echo "</td></tr>\n";
            echo "<tr><td><b>".get_string("response", "lesson")." $iplus1:</b><br />\n";
            print_textarea($usehtmleditor, 20, 70, 630, 300, "response[$i]");
            echo "</td></tr>\n";
            echo "<tr><td><B>".get_string("jumpto", "lesson").":</b> \n";
            lesson_choose_from_menu($jump, "jumpto[$i]", 0, "");
            helpbutton("jumpto", get_string("jumpto", "lesson"), "lesson");
            echo "</td></tr>\n";
        }
        use_html_editor();
        // close table and form
        ?>
        </table><br />
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
		$form = (object) $HTTP_POST_VARS;
        
        if ($form->pageid) {
            // the new page is not the first page
            if (!$page = get_record("lesson_pages", "id", $form->pageid)) {
                error("Insert page: page record not found");
            }
            $newpage->lessonid = $lesson->id;
            $newpage->prevpageid = $form->pageid;
            $newpage->nextpageid = $page->nextpageid;
            $newpage->timecreated = $timenow;
            $newpage->title = $form->title;
            $newpage->contents = trim($form->contents);
            $newpageid = insert_record("lesson_pages", $newpage);
            if (!$newpageid) {
                error("Insert page: new page not inserted");
            }
            // update the linked list
            if (!set_field("lesson_pages", "nextpageid", $newpageid, "id", $form->pageid)) {
                error("Insert page: unable to update link");
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
                $newanswer->response = trim($form->response[$i]);
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
                echo "<tr><td bgcolor=\"$THEME->cellheading2\"><b>$page->title</b></td></tr>\n";
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
        $form = (object) $HTTP_POST_VARS;

        $page->id = $form->pageid;
        $page->timemodified = $timenow;
        $page->title = $form->title;
        $page->contents = trim($form->contents);
        if (!update_record("lesson_pages", $page)) {
            error("Update page: page not updated");
        }
        for ($i = 0; $i < $lesson->maxanswers; $i++) {
            if (trim(strip_tags($form->answer[$i]))) { // strip_tags because the HTML gives <p><br />...
                if ($form->answerid[$i]) {
                    $oldanswer->id = $form->answerid[$i];
                    $oldanswer->timemodified = $timenow;
                    $oldanswer->answer = trim($form->answer[$i]);
                    $oldanswer->response = trim($form->response[$i]);
                    $oldanswer->jumpto = $form->jumpto[$i];
                    if (!update_record("lesson_answers", $oldanswer)) {
                        error("Update page: answer $i not updated");
                    }
                } else {
                    // it's a new answer
                    $newanswer->lessonid = $lesson->id;
                    $newanswer->pageid = $page->id;
                    $newanswer->timecreated = $timenow;
                    $newanswer->answer = trim($form->answer[$i]);
                    $newanswer->response = trim($form->response[$i]);
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
   		redirect("view.php?id=$cm->id", get_string("ok"));
    }
	

	/*************** no man's land **************************************/
	else {
		error("Fatal Error: Unknown Action: ".$action."\n");
	}

	print_footer($course);
 
?>

