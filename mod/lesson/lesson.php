<?PHP  // $Id: lesson.php, v 1.0 25 Jan 2004

/*************************************************
	ACTIONS handled are:

	addbranchtable
    addendofbranch
	addcluster      /// CDC-FLAG /// added two new items
	addendofcluster
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
	require("locallib.php");
	require_once("styles.php");

	
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
		//// CDC-FLAG /////
		$jump[LESSON_PREVIOUSPAGE] = get_string("previouspage", "lesson");
		if (!isset($_GET['firstpage'])) {	    
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
 		}
		//// CDC-FLAG /////
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
        <!-- //CDC hidden-label added.--><label for="title" class="hidden-label">Title</label><input type="text" id="title" name="title" size="80" maxsize="255" value=""></td></tr>
        <?PHP
        echo "<tr><td><b>";
        echo get_string("pagecontents", "lesson").":</b><br />\n";
        print_textarea($usehtmleditor, 25,70, 630, 400, "contents");
        use_html_editor("contents");
		echo "</td></tr>\n";
		/// CDC-FLAG /// 6/16/04
		echo "<tr><td>\n";
		echo "<center><input name=\"layout\" type=\"checkbox\" value=\"1\" CHECKED>";
		echo get_string("arrangebuttonshorizontally", "lesson")."\n";
		echo "<br><input name=\"display\" type=\"checkbox\" value=\"1\" CHECKED>";
		echo get_string("displayinleftmenu", "lesson");
		echo "</center>\n";
		echo "</td></tr>\n";
		/// CDC-FLAG ///				
        for ($i = 0; $i < $lesson->maxanswers; $i++) {
            $iplus1 = $i + 1;
            echo "<tr><td><b>".get_string("description", "lesson")." $iplus1:</b><br />\n";
            print_textarea(false, 10, 70, 630, 300, "answer[$i]");  // made the default set to off also removed use_html_editor(); line from down below, which made all textareas turn into html editors
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

/// CDC-FLAG 6/17/04 ///	
	/************** add cluster ************************************/
    elseif ($action == 'addcluster' ) {

       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

        // first get the preceeding page
		// if $pageid = 0, then we are inserting a new page at the beginning of the lesson
        $pageid = $_GET['pageid'];
            
        $timenow = time();
        
		if ($pageid == 0) {
			if (!$page = get_record("lesson_pages", "prevpageid", 0, "lessonid", $lesson->id)) {
				error("Error: Add cluster: page record not found");
			}
		} else {
			if (!$page = get_record("lesson_pages", "id", $pageid)) {
				error("Error: Add cluster: page record not found");
        	}
		}
        
		$newpage->lessonid = $lesson->id;
		$newpage->prevpageid = $pageid;
		if ($pageid != 0) {
			$newpage->nextpageid = $page->nextpageid;
		} else {
			$newpage->nextpageid = $page->id;
		}
		$newpage->qtype = LESSON_CLUSTER;
		$newpage->timecreated = $timenow;
		$newpage->title = get_string("clustertitle", "lesson");
		$newpage->contents = get_string("clustertitle", "lesson");
		if (!$newpageid = insert_record("lesson_pages", $newpage)) {
			error("Insert page: new page not inserted");
		}
		// update the linked list...
		if ($pageid != 0) {
			if (!set_field("lesson_pages", "nextpageid", $newpageid, "id", $pageid)) {
				error("Add cluster: unable to update link");
			}
		}
		
		if ($pageid == 0) {
			$page->nextpageid = $page->id;
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
		$newanswer->jumpto = LESSON_CLUSTERJUMP;
		if(!$newanswerid = insert_record("lesson_answers", $newanswer)) {
			error("Add cluster: answer record not inserted");
		}
		redirect("view.php?id=$cm->id", get_string("ok"));
	}
/// CDC-FLAG ///	

/// CDC-FLAG 6/17/04 ///	
	/************** add end of cluster ************************************/
    elseif ($action == 'addendofcluster' ) {

       	if (!isteacher($course->id)) {
	    	error("Only teachers can look at this page");
	    }

        // first get the preceeding page
        $pageid = $_GET['pageid'];
            
        $timenow = time();
        
        // the new page is not the first page (end of cluster always comes after an existing page)
        if (!$page = get_record("lesson_pages", "id", $pageid)) {
			error("Error: Could not find page");
        }
		
		// could put code in here to check if the user really can insert an end of cluster
		
		$newpage->lessonid = $lesson->id;
		$newpage->prevpageid = $pageid;
		$newpage->nextpageid = $page->nextpageid;
		$newpage->qtype = LESSON_ENDOFCLUSTER;
		$newpage->timecreated = $timenow;
		$newpage->title = get_string("endofclustertitle", "lesson");
		$newpage->contents = get_string("endofclustertitle", "lesson");
		if (!$newpageid = insert_record("lesson_pages", $newpage)) {
			error("Insert page: end of cluster page not inserted");
		}
		// update the linked list...
		if (!set_field("lesson_pages", "nextpageid", $newpageid, "id", $pageid)) {
			error("Add end of cluster: unable to update link");
		}
		if ($page->nextpageid) {
			// the new page is not the last page
			if (!set_field("lesson_pages", "prevpageid", $newpageid, "id", $page->nextpageid)) {
				error("Insert end of cluster: unable to update previous link");
			}
		}
		// ..and the single "answer"
		$newanswer->lessonid = $lesson->id;
		$newanswer->pageid = $newpageid;
		$newanswer->timecreated = $timenow;
		$newanswer->jumpto = LESSON_NEXTPAGE;
		if(!$newanswerid = insert_record("lesson_answers", $newanswer)) {
			error("Add end of cluster: answer record not inserted");
		}
		redirect("view.php?id=$cm->id", get_string("ok"));
	}
/// CDC-FLAG ///
	
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
		//// CDC-FLAG 6/18/04 /////
		$jump[LESSON_PREVIOUSPAGE] = get_string("previouspage", "lesson");
		if(lesson_display_branch_jumps($lesson->id, $pageid)) {
			$jump[LESSON_UNSEENBRANCHPAGE] = get_string("unseenpageinbranch", "lesson");
			$jump[LESSON_RANDOMPAGE] = get_string("randompageinbranch", "lesson");
		}
		if(lesson_display_cluster_jump($lesson->id, $pageid)) {
			$jump[LESSON_CLUSTERJUMP] = get_string("clusterjump", "lesson");
		}
		/// CDC-FLAG ///
		if (!isset($_GET['firstpage'])) {
			$linkadd = "";	  
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
		} else {
			$linkadd = "&firstpage=1";
		}
 
        // give teacher a blank proforma
		print_heading_with_help(get_string("addaquestionpage", "lesson"), "overview", "lesson");
        ?>
        <form name="form" method="post" action="lesson.php">
        <input type="hidden" name="id" value="<?PHP echo $cm->id ?>">
        <input type="hidden" name="action" value="insertpage">
        <input type="hidden" name="pageid" value="<?PHP echo $_GET['pageid'] ?>">
		<center><table cellpadding=5 border=1>
  		<?php
		    echo "<tr><td align=\"center\"><b>";
			echo get_string("questiontype", "lesson").":</b> \n";
			echo helpbutton("questiontype", get_string("questiontype", "lesson"), "lesson")."<br>";
			if (isset($_GET['qtype'])) {
				lesson_qtype_menu($LESSON_QUESTION_TYPE, $_GET['qtype'], 
								  "lesson.php?id=$cm->id&action=addpage&pageid=".$_GET['pageid'].$linkadd);
				// NoticeFix rearraged
				if ( $_GET['qtype'] == LESSON_SHORTANSWER || $_GET['qtype'] == LESSON_MULTICHOICE || !isset($_GET['qtype']) ) {  // only display this option for Multichoice and shortanswer
					if ($_GET['qtype'] == LESSON_SHORTANSWER) {
						echo "<br><br><b>".get_string("casesensitive", "lesson").":</b> \n";
					} else {
						echo "<br><br><b>".get_string("multianswer", "lesson").":</b> \n";
					}
					echo " <label for=\"qoption\" class=\"hidden-label\">Question Option</label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\"/>"; //CDC hidden label added.
					helpbutton("questionoption", get_string("questionoption", "lesson"), "lesson");
				}
			} else {
				lesson_qtype_menu($LESSON_QUESTION_TYPE, LESSON_MULTICHOICE, 
								  "lesson.php?id=$cm->id&action=addpage&pageid=".$_GET['pageid'].$linkadd);
			}
			echo "</td></tr>\n";
		?>
        <tr><td align="center">
        <tr valign="top">
        <td><b><?php print_string("pagetitle", "lesson"); ?>:</b><br />
        <!-- //CDC hidden-label added.--><label for="title" class="hidden-label">Title</label><input type="text" id="title" name="title" size="80" maxsize="255" value=""></td></tr>
        <?PHP
        echo "<tr><td><b>";
        echo get_string("pagecontents", "lesson").":</b><br />\n";
        print_textarea($usehtmleditor, 25,70, 630, 400, "contents");
        use_html_editor("contents");
        echo "</td></tr>\n";
		if (isset($_GET['qtype'])) {
			switch ($_GET['qtype']) {
				case LESSON_TRUEFALSE :
					for ($i = 0; $i < 2; $i++) {
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
						/// CDC-FLAG ///
						if($lesson->custom) {
							if ($i) {
								echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"-1\" size=\"5\">";
							} else {
								echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\">";
							}
						}
						/// CDC-FLAG ///
						echo "</td></tr>\n";
					}
					break;
				case LESSON_ESSAY :
						echo "<tr><td><B>".get_string("jump", "lesson").":</b> \n";
						lesson_choose_from_menu($jump, "jumpto[0]", LESSON_NEXTPAGE, "");
						helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
						if ($lesson->custom) {
							echo get_string("score", "lesson").": <input type=\"text\" name=\"score[0]\" value=\"1\" size=\"5\">";
						}
						echo "</td></tr>\n";
					break;
				case LESSON_MATCHING :
					for ($i = 0; $i < $lesson->maxanswers+2; $i++) {
						$icorrected = $i - 1;
						if ($i == 0) {
							echo "<tr><td><b>".get_string("correctresponse", "lesson").":</b><br />\n";
							print_textarea(false, 6, 70, 630, 300, "answer[$i]");
							echo "</td></tr>\n";
						} elseif ($i == 1) {
							echo "<tr><td><b>".get_string("wrongresponse", "lesson").":</b><br />\n";
							print_textarea(false, 6, 70, 630, 300, "answer[$i]");
							echo "</td></tr>\n";
						} else {												
							echo "<tr><td><b>".get_string("answer", "lesson")." $icorrected:</b><br />\n";
							print_textarea(false, 6, 70, 630, 300, "answer[$i]");
							echo "</td></tr>\n";
							echo "<tr><td><b>".get_string("matchesanswer", "lesson")." $icorrected:</b><br />\n";
							print_textarea(false, 6, 70, 630, 300, "response[$i]");
							echo "</td></tr>\n";
						}
						if ($i == 2) {
							echo "<tr><td><B>".get_string("correctanswerjump", "lesson").":</b> \n";
							lesson_choose_from_menu($jump, "jumpto[$i]", LESSON_NEXTPAGE, "");
							helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
							if($lesson->custom) {
								echo get_string("correctanswerscore", "lesson").": <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\">";
							}
							echo "</td></tr>\n";
						} elseif ($i == 3) {
							echo "<tr><td><B>".get_string("wronganswerjump", "lesson").":</b> \n";
							lesson_choose_from_menu($jump, "jumpto[$i]", 0, "");
							helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
							if($lesson->custom) {
								echo get_string("wronganswerscore", "lesson").": <input type=\"text\" name=\"score[$i]\" value=\"-1\" size=\"5\">";
							}
							echo "</td></tr>\n";
						}
					}
					break;
				case LESSON_SHORTANSWER :
				case LESSON_NUMERICAL :
				case LESSON_MULTICHOICE :
					// default code
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
						/// CDC-FLAG ///
						if($lesson->custom) {
							if ($i) {
								echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"-1\" size=\"5\">";
							} else {
								echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\">";
							}
						}
						/// CDC-FLAG ///
						echo "</td></tr>\n";
					}
					break;
			}
		} else {
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
				/// CDC-FLAG ///
				if($lesson->custom) {
					if ($i) {
						echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"-1\" size=\"5\">";
					} else {
						echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\">";
					}
				}
				/// CDC-FLAG ///
				echo "</td></tr>\n";
			}
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
			 "lesson.php?action=delete&amp;id=$cm->id&amp;pageid=$pageid", 
             "view.php?id=$cm->id");
		}
	

	/****************** continue ************************************/
	elseif ($action == 'continue' ) {
		//CDC Chris Berri added this echo call for left menu.  must match that in view.php for styles
		if ($lesson->displayleft) {
			echo '<div class="leftmenu1">';	
					if ($page = get_record_select("lesson_pages", "lessonid = $lesson->id AND prevpageid = 0")) {
						// print the pages
						echo "<form name=\"lessonpages2\" method=\"post\" action=\"view.php\">\n";
						echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
						echo "<input type=\"hidden\" name=\"action\" value=\"navigation\">\n";
						echo "<input type=\"hidden\" name=\"pageid\">\n";
								echo "<div class='lmlinks'><table bgcolor=\"$THEME->cellheading\"><tr></tr>";
								echo "<tr><td class='lmheading'>".get_string("lessonmenu", "lesson")."</td></tr><br>";
								echo "<tr><td class='lmMainlinks'>";
								echo "<a href=\"../../course/view.php?id=$course->id\">".get_string("mainmenu", "lesson")."</a></td></tr>";
								echo "<td>"; 
								lesson_print_tree_menu($lesson->id, $page->id, $cm->id);
								echo "</td></tr></table></div>"; 
						echo "</form>";
					}
			echo   "</div>"; //close leftmenu1		
			echo "<div class='slidepos'>"; //CDC slidepos 
		} elseif ($lesson->slideshow) {
			echo "<table align=\"center\"><tr><td>";
		}
		/// CDC-FLAG /// Slideshow styles
		if($lesson->slideshow) {
			echo "<div style=\"
					background-color: $lesson->bgcolor;
					height: ".$lesson->height."px;
					width: ".$lesson->width."px;
					overflow: auto;
					border: 0px solid #ccc;
					padding: 8px;
					\">\n";
		}
		/// CDC-FLAG ///	
		
		/// CDC-FLAG /// 6/21/04  This is the warning msg for teachers to inform them that cluster and unseen does not work while logged in as a teacher
		if(isteacher($course->id)) {
			if (execute_teacherwarning($lesson->id)) {
				$warningvars->cluster = get_string("clusterjump", "lesson");
				$warningvars->unseen = get_string("unseenpageinbranch", "lesson");
				echo "<div align=\"center\">".get_string("teacherjumpwarning", "lesson", $warningvars)."<div><br>";
			}
		}		
		/// CDC-FLAG ///

		/// CDC-FLAG /// 6/14/04 -- This is the code updates the lesson time for a timed test
		$outoftime = false;
		if($lesson->timed) {
			if(isteacher($course->id)) {
				echo "<div align=\"center\">".get_string("teachertimerwarning", "lesson")."</div>";
			} else {
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
						echo "window.onload = function () { show_clock(); }";
					echo "</script>\n";
					echo "</td></tr></table><br><br>";
				} else {
						redirect("view.php?id=$cm->id&action=navigation&pageid=".LESSON_EOL."&outoftime=normal", get_string("outoftime", "lesson"));
				}
				if ((($timer->starttime + $lesson->maxtime * 60) - time()) < 60 && !((($timer->starttime + $lesson->maxtime * 60) - time()) < 0)) {
					echo "<div align=\"center\">".get_string("studentoneminwarning", "lesson")."<div>";
				} elseif (($timer->starttime + $lesson->maxtime * 60) < time()) {
					echo "<div align=\"center\">".get_string("studentoutoftime", "lesson")."</div>";
					$outoftime = true;
				}
				unset($newtime);
				$newtime->id = $timer->id;
				$newtime->lessontime = time();
				
				if (!update_record("lesson_timer", $newtime)) {
					error("Error: could not update lesson_timer table");
				}
			}
		}
		/// CDC-FLAG ///			

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
			/// CDC-FLAG ///
			 case LESSON_ESSAY :
                if (!$useranswer = $_POST['answer']) {
                    $noanswer = true;
                    break;
                }
	            if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                    error("Continue: No answers found");
                }
				$correctanswer = false;
				$response = "Your essay will be graded by the course instructor.";
				foreach ($answers as $answer) {
					$answerid = $answer->id;
					$newpageid = $answer->jumpto;
				}
				/// 6/29/04 //
	            $nretakes = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id); 

				$newessay->lessonid = $lesson->id;
				$newessay->pageid = $pageid;
				$newessay->answerid = $answerid;
				$newessay->try = $nretakes;
				$newessay->userid = $USER->id;
				$newessay->answer = $useranswer;
				$newessay->timesubmitted = time();
				if (!isteacher($course->id)) {
					if (!insert_record("lesson_essay", $newessay)) {
						error("Error: could not submit essay");
					}
				}
			 	break;
			/// CDC-FLAG ///
		     case LESSON_SHORTANSWER :
                if (!$useranswer = $_POST['answer']) {
                    $noanswer = true;
                    break;
                }
                if (!$answers = get_records("lesson_answers", "pageid", $pageid, "id")) {
                    error("Continue: No answers found");
                }
                foreach ($answers as $answer) {
					/// CDC-FLAG ///
					if ($lesson->custom) {
						if($answer->score > 0) {
							if ($page->qoption) {
								// case sensitive
								if ($answer->answer == $useranswer) {
									$correctanswer = true;
									$answerid = $answer->id;
									$newpageid = $answer->jumpto;
									if (trim(strip_tags($answer->response))) {
										$response = $answer->response;
									}
								}
							} else {
								// case insensitive
								if (strcasecmp($answer->answer, $useranswer) == 0) {
									$correctanswer = true;
									$answerid = $answer->id;
									$newpageid = $answer->jumpto;
									if (trim(strip_tags($answer->response))) {
										$response = $answer->response;
									}
								}
							}
						}
					} elseif (lesson_iscorrect($pageid, $answer->jumpto)) {  /// CDC-FLAG 6/21/04 ///
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
							$answerid = $answer->id;
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
				/* CDC-FLAG */  
				if ($lesson->custom) {
					if ($answer->score > 0) {
						$correctanswer = true;
					} else {
						$correctanswer = false;
					}
				}
				/// CDC-FLAG 6/21/04 ///
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
					/// CDC-FLAG /// 6/11/04 this is for custom scores.  If score on answer is positive, it is correct					
					if ($lesson->custom) {
						$ncorrect = 0;
						$nhits = 0;
						foreach ($answers as $answer) {
							if ($answer->score > 0) {
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
					} else {
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
					}
					/// CDC-FLAG ///
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
					/* CDC-FLAG */
					if ($lesson->custom) {
						if ($answer->score > 0) {
							$correctanswer = true;
						} else {
							$correctanswer = false;
						}
					}
					/// CDC-FLAG ///
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
                
            /// CDC-FLAG /// 6/14/04  -- added responses    
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
                    if ($i == 2) {
                        $correctpageid = $answer->jumpto;
						$correctanswerid = $answer->id;
                    }
                    if ($i == 3) {
                        $wrongpageid = $answer->jumpto;
						$wronganswerid = $answer->id;						
                    }
                    $i++;
                }
                if ($ncorrect == count($answers)) {
                   	$response = get_string("thatsthecorrectanswer", "lesson");
					foreach ($answers as $answer) {
						if ($answer->response == NULL && $answer->answer != NULL) {
		                    $response = $answer->answer;
							break;
						}
					}
					// NoticeFix
                    if (isset($correctpageid)) {
						$newpageid = $correctpageid;
					}
					if (isset($correctasnwerid)) {
						$answerid = $correctanswerid;
					}
                    $correctanswer = true;
                } else {
                   	$response = get_string("thatsthewronganswer", "lesson");
					$t = 0;
					foreach ($answers as $answer) {
						if ($answer->response == NULL && $answer->answer != NULL) {
		                    if ($t == 1) {
								$response = $answer->answer;
								break;
							}
							$t++;
						}
					}
                    $newpageid = $wrongpageid;
					$answerid = $wronganswerid;
                }
                break;
				/// CDC-FLAG ///

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
						/// CDC-FLAG ///
						if ($lesson->custom) {
							if ($answer->score > 0) {
								$correctanswer = true;
								$answerid = $answer->id;
							}
						}
						/// CDC-FLAG ///
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
				/// CDC-FLAG /// 6/15/04 going to insert into lesson_branch				
				if ($newpageid == LESSON_RANDOMBRANCH) {
					$branchflag = 1;
				} else {
					$branchflag = 0;
				}
				if ($grades = get_records_select("lesson_grades", "lessonid = $lesson->id AND userid = $USER->id",
							"grade DESC")) {
					$retries = count($grades);
				} else {
					$retries = 0;
				}
				unset($branch);
				$branch->lessonid = $lesson->id;
				$branch->userid = $USER->id;
				$branch->pageid = $_POST['pageid'];
				$branch->retry = $retries;
				$branch->flag = $branchflag;
				$branch->timeseen = time();
				
				if (!insert_record("lesson_branch", $branch)) {
					error("Error: could not insert row into lesson_branch table");
				}
				/// CDC-FLAG ///

				/// CDC-FLAG ///  this is called when jumping to random from a branch table
				if($newpageid == LESSON_UNSEENBRANCHPAGE)
				{
					if (isteacher($course->id)) {
						 $newpageid = LESSON_NEXTPAGE;
					} else {
						 $newpageid = lesson_unseen_question_jump($lesson->id, $USER->id, $_POST['pageid']);  // this may return 0 //CDC Chris Berri.....this is where it sets the next page id for unseen?
					}
				}
				/// CDC-FLAG 6/15/04 ///
                // convert jumpto page into a proper page id
                if ($newpageid == 0) {
                    $newpageid = $pageid;
                } elseif ($newpageid == LESSON_NEXTPAGE) {
                    if (!$newpageid = $page->nextpageid) {
                        // no nextpage go to end of lesson
                        $newpageid = LESSON_EOL;
                    }
/* CDC-FLAG */  } elseif ($newpageid == LESSON_PREVIOUSPAGE) {
					$newpageid = $page->prevpageid;
				} elseif ($newpageid == LESSON_RANDOMPAGE) {
					$newpageid = lesson_random_question_jump($lesson->id, $_POST['pageid']);
				} elseif ($newpageid == LESSON_RANDOMBRANCH) {  // 6/15/04
					$newpageid = lesson_unseen_branch_jump($lesson->id, $USER->id);
				}
				/// CDC-FLAG ///
                // no need to record anything in lesson_attempts				 
                redirect("view.php?id=$cm->id&amp;action=navigation&amp;pageid=$newpageid");
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
				/// CDC-FLAG /// -- dont want to insert the attempt if they ran out of time
				if (!$outoftime) {
					if (!$newattemptid = insert_record("lesson_attempts", $attempt)) {
						error("Continue: attempt not inserted");
					}
				}
				/// CDC-FLAG ///
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
            
			/// CDC-FLAG 6/21/04 ///  this calculates the ongoing score
			if ($lesson->ongoing) {
				if (isteacher($course->id)) {
					echo "<div align=\"center\">".get_string("teacherongoingwarning", "lesson")."</div><br>";
				} else {
					lesson_calculate_ongoing_score($lesson, $USER);
				}
			}
			/// CDC-FLAG ///
		
            // display response (if there is one - there should be!)
            if ($response) {
                //$title = get_field("lesson_pages", "title", "id", $pageid);
                //print_heading($title);
                echo "<table width=\"80%\" border=\"0\" align=\"center\"><tr><td>\n";
				if ($lesson->review && !$correctanswer) {
					$nretakes = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id); 
					$qattempts = count_records("lesson_attempts", "userid", $USER->id, "retry", $nretakes, "pageid", $pageid);
					echo "<br><br>";
					if ($qattempts == 1) {
						print_simple_box(get_string("firstwrong", "lesson"), "center");
					} else {
						print_simple_box(get_string("secondpluswrong", "lesson"), "center");
					}
				} else {
	                print_simple_box(format_text($response), 'center');
				}
                echo "</td></tr></table>\n";
			}
        }

		
		/// CDC-FLAG 6/18/04 ///  - this is where some jump numbers are interpreted
		if ($newpageid != LESSON_CLUSTERJUMP && $pageid != 0 && $newpageid > 0) {  // going to check to see if the page that the user is going to view next, is a cluster page.  If so, dont display, go into the cluster.  The $newpageid > 0 is used to filter out all of the negative code jumps.
			if (!$page = get_record("lesson_pages", "id", $newpageid)) {
				error("Error: could not find page");
			}
			if ($page->qtype == LESSON_CLUSTER) {
				$newpageid = LESSON_CLUSTERJUMP;
				$pageid = $page->id;
			}
		}
		if($outoftime) {
			$newpageid = LESSON_EOL;  // ran out of time for the test, so go to eol
		} elseif($newpageid == LESSON_UNSEENBRANCHPAGE) {
			if (isteacher($course->id)) {
				if ($page->nextpageid == 0) {
					$newpageid = LESSON_EOL;
				} else {
					$newpageid = $page->nextpageid;
				}
			} else {
				$newpageid = lesson_unseen_question_jump($lesson->id, $USER->id, $pageid);
			}			
		} elseif ($newpageid == LESSON_PREVIOUSPAGE) {
			$newpageid = $page->prevpageid;
		} elseif ($newpageid == LESSON_RANDOMPAGE) {
			$newpageid = lesson_random_question_jump($lesson->id, $pageid);
		} elseif ($newpageid == LESSON_CLUSTERJUMP) {
			if (isteacher($course->id)) {
				if ($page->nextpageid == 0) {  // if teacher, go to next page
					$newpageid = LESSON_EOL;
				} else {
					$newpageid = $page->nextpageid;
				}			
			} else {
				$newpageid = lesson_cluster_jump($lesson->id, $USER->id, $pageid);
			}
		}
		/// CDC-FLAG ///		
        echo "<form name=\"pageform\" method =\"post\" action=\"view.php\">\n";
        echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
        echo "<input type=\"hidden\" name=\"action\" value=\"navigation\">\n";
        echo "<input type=\"hidden\" name=\"pageid\" value=\"$newpageid\">\n";
		/// CDC-FLAG ///
		if ($lesson->slideshow) {
			echo "</div>"; //Close Mark's big div tag???
			
			echo "<table width=\"$lesson->width\" cellpadding=\"5\" cellspacing=\"5\"><tr><td>\n";
			if ($lesson->review && !$correctanswer && !$noanswer) {
				echo "<p class=\"lessonAbutton\" align=\"center\"><input type=\"submit\" onClick='pageform.pageid.value=$pageid;' name=\"review\" value=\"".
					get_string("reviewquestionback", "lesson")."\"></p>\n";
				echo "<p class=\"lessonAbutton\" align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
					get_string("reviewquestioncontinue", "lesson")."\"></p>\n";
			} else {
				echo "<p class=\"lessonAbutton\" align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
					get_string("continue", "lesson")."\"></p>\n";
			}
			echo "</td></tr></table>";

		} else {
			if ($lesson->review && !$correctanswer && !$noanswer) {
				echo "<p class=\"lessonAbutton\" align=\"center\"><input type=\"submit\" onClick='pageform.pageid.value=$pageid;' name=\"review\" value=\"".
					get_string("reviewquestionback", "lesson")."\"></p>\n";
				echo "<p class=\"lessonAbutton\" align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
					get_string("reviewquestioncontinue", "lesson")."\"></p>\n";
			} else {
				echo "<p class=\"lessonAbutton\" align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
					get_string("continue", "lesson")."\"></p>\n";
			}
		}
		echo "</form>\n";
		
		if ($lesson->displayleft) {
			echo "</div><!-- close slidepos class -->"; //CDC Chris Berri for styles, closes slidepos.
		} elseif ($lesson->slideshow) {
			echo "</td></tr></table>";
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
		
		if (isset($_GET['qtype'])) {
			$page->qtype = $_GET['qtype'];
		}
		
        // set of jump array
        $jump[0] = get_string("thispage", "lesson");
        $jump[LESSON_NEXTPAGE] = get_string("nextpage", "lesson");
		//// CDC-FLAG 6/18/04 /////
		$jump[LESSON_PREVIOUSPAGE] = get_string("previouspage", "lesson");
		if(lesson_display_branch_jumps($lesson->id, $page->id)) {
			$jump[LESSON_UNSEENBRANCHPAGE] = get_string("unseenpageinbranch", "lesson");
			$jump[LESSON_RANDOMPAGE] = get_string("randompageinbranch", "lesson");
		}
		if ($page->qtype == LESSON_ENDOFBRANCH || $page->qtype == LESSON_BRANCHTABLE) {
			$jump[LESSON_RANDOMBRANCH] = get_string("randombranch", "lesson");
		}
		if(lesson_display_cluster_jump($lesson->id, $page->id) && $page->qtype != LESSON_BRANCHTABLE && $page->qtype != LESSON_ENDOFCLUSTER) {
			$jump[LESSON_CLUSTERJUMP] = get_string("clusterjump", "lesson");
		}
	    //// CDC-FLAG /////		
        $jump[LESSON_EOL] = get_string("endoflesson", "lesson");
        if (!$apageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
            error("Edit page: first page not found");
        }
        while (true) {
            if ($apageid) {
                if (!$apage = get_record("lesson_pages", "id", $apageid)) {
                    error("Edit page: apage record not found");
                }
				/// CDC-FLAG /// 6/15/04 removed != LESSON_ENDOFBRANCH...
				if (trim($page->title)) { // ...nor nuffin pages
					$jump[$apageid] = $apage->title;
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
   		<?php
		    switch ($page->qtype) {
				case LESSON_MULTICHOICE :
					echo "<tr><td align=\"center\"><b>";
					echo get_string("questiontype", "lesson").":</b> \n";
					echo helpbutton("questiontype", get_string("questiontype", "lesson"), "lesson")."<br>";
					lesson_qtype_menu($LESSON_QUESTION_TYPE, $page->qtype, 
									  "lesson.php?id=$cm->id&action=editpage&pageid=$page->id",
									  "document.editpage.redisplay.value=1;document.editpage.submit();");
					echo "<br><br><b>".get_string("multianswer", "lesson").":</b> \n";
					if ($page->qoption) {
						echo "<label for=\"qoption\" class=\"hidden-label\">Question Option</label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\" checked=\"checked\"/>"; //CDC hidden label added.
					} else {
						echo "<label for=\"qoption\" class=\"hidden-label\">Question Option</label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\"/>"; //CDC hidden label added.
					}
					helpbutton("questionoption", get_string("questionoption", "lesson"), "lesson");
					echo "</td></tr>\n";
					break;
				case LESSON_SHORTANSWER :
					echo "<tr><td align=\"center\"><b>";
					echo get_string("questiontype", "lesson").":</b> \n";
					echo helpbutton("questiontype", get_string("questiontype", "lesson"), "lesson")."<br>";
					lesson_qtype_menu($LESSON_QUESTION_TYPE, $page->qtype, 
									  "lesson.php?id=$cm->id&action=editpage&pageid=$page->id",
									  "document.editpage.redisplay.value=1;document.editpage.submit();");
					echo "<br><br><b>".get_string("casesensitive", "lesson").":</b> \n";
					if ($page->qoption) {
						echo "<label for=\"qoption\" class=\"hidden-label\">Question Option</label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\" checked=\"checked\"/>"; //CDC hidden label added.
					} else {
						echo "<label for=\"qoption\" class=\"hidden-label\">Question Option</label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\"/>"; //CDC hidden label added.
					}
					helpbutton("questionoption", get_string("questionoption", "lesson"), "lesson");
					echo "</td></tr>\n";
					break;
				case LESSON_TRUEFALSE :
				case LESSON_ESSAY :
				case LESSON_MATCHING :
				case LESSON_NUMERICAL :
					echo "<tr><td align=\"center\"><b>";
					echo get_string("questiontype", "lesson").":</b> \n";
					echo helpbutton("questiontype", get_string("questiontype", "lesson"), "lesson")."<br>";
					lesson_qtype_menu($LESSON_QUESTION_TYPE, $page->qtype, 
									  "lesson.php?id=$cm->id&action=editpage&pageid=$page->id",
									  "document.editpage.redisplay.value=1;document.editpage.submit();");
					echo "</td></tr>\n";
					break;
			}
		?>
		<tr><td align="center">
        <tr valign="top">
        <td><b><?php print_string("pagetitle", "lesson"); ?>:</b><br />
        <!-- //CDC hidden-label added.--><label for="title" class="hidden-label">Title</label><input type="text" id="title" name="title" size="80" maxsize="255" value="<?PHP echo $page->title ?>"></td>
        </tr>
        <?PHP
        echo "<tr><td><b>";
        echo get_string("pagecontents", "lesson").":</b><br />\n";
        print_textarea($usehtmleditor, 25, 70, 630, 400, "contents", $page->contents);
        use_html_editor("contents"); // always the editor
        echo "</td></tr>\n";
        $n = 0;
        switch ($page->qtype) {
            case LESSON_BRANCHTABLE :
                echo "<input type=\"hidden\" name=\"qtype\" value=\"$page->qtype\">\n";
				/// CDC-FLAG /// 6/16/04
				echo "<tr><td>\n";
				echo "<center>";
				if ($page->layout) {
					echo "<input CHECKED name=\"layout\" type=\"checkbox\" value=\"1\">";
				} else {
					echo "<input name=\"layout\" type=\"checkbox\" value=\"1\">";
				}
				echo get_string("arrangebuttonshorizontally", "lesson")."<center>\n";
				echo "<br>";
				if ($page->display) {
					echo "<center><input name=\"display\" type=\"checkbox\" value=\"1\" CHECKED>";
				} else {
					echo "<center><input name=\"display\" type=\"checkbox\" value=\"1\">";
				}				
				echo get_string("displayinleftmenu", "lesson")."<center>\n";
				echo "</td></tr>\n";
				/// CDC-FLAG ///								
                echo "<tr><td><b>".get_string("branchtable", "lesson")."</b> \n";
                break;
			case LESSON_CLUSTER :
                echo "<input type=\"hidden\" name=\"qtype\" value=\"$page->qtype\">\n";
                echo "<tr><td><b>".get_string("clustertitle", "lesson")."</b> \n";
                break;                
			case LESSON_ENDOFCLUSTER :
                echo "<input type=\"hidden\" name=\"qtype\" value=\"$page->qtype\">\n";
                echo "<tr><td><b>".get_string("endofclustertitle", "lesson")."</b> \n";
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
                    case LESSON_MATCHING:
						if ($n == 0) {
							echo "<tr><td><b>".get_string("correctresponse", "lesson").":</b>\n";
							if ($flags & LESSON_ANSWER_EDITOR) {
								echo " [".get_string("useeditor", "lesson").": ".
									"<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" 
									checked=\"checked\">"; //CDC hidden label added.
								helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
								echo "]<br />\n";
								print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
								use_html_editor("answer[$n]"); // switch on the editor
							} else {
								echo " [".get_string("useeditor", "lesson").": ".
									"<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\">"; //CDC hidden label.
								helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
								echo "]<br />\n";
								print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
							}
						} elseif ($n == 1) {
							echo "<tr><td><b>".get_string("wrongresponse", "lesson").":</b>\n";
							if ($flags & LESSON_ANSWER_EDITOR) {
								echo " [".get_string("useeditor", "lesson").": ".
									"<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" 
									checked=\"checked\">"; //CDC hidden label added.
								helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
								echo "]<br />\n";
								print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
								use_html_editor("answer[$n]"); // switch on the editor
							} else {
								echo " [".get_string("useeditor", "lesson").": ".
									"<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\">"; //CDC hidden label.
								helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
								echo "]<br />\n";
								print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
							}
						} else {
							$ncorrected = $n - 1;
							echo "<tr><td><b>".get_string("answer", "lesson")." $ncorrected:</b>\n";
							if ($flags & LESSON_ANSWER_EDITOR) {
								echo " [".get_string("useeditor", "lesson").": ".
									"<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" 
									checked=\"checked\">"; //CDC hidden label added.
								helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
								echo "]<br />\n";
								print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
								use_html_editor("answer[$n]"); // switch on the editor
							} else {
								echo " [".get_string("useeditor", "lesson").": ".
									"<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\">"; //CDC hidden label.
								helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
								echo "]<br />\n";
								print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
							}
							echo "</td></tr>\n";
							echo "<tr><td><b>".get_string("matchesanswer", "lesson")." $ncorrected:</b>\n";
							if ($flags & LESSON_RESPONSE_EDITOR) {
								echo " [".get_string("useeditor", "lesson").": ".
									"<label for=\"responseeditor[$n]\" class=\"hidden-label\">responseeditor[$n]</label><input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\" 
									checked=\"checked\">"; //CDC hidden label added.
								helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
								echo "]<br />\n";
								print_textarea($usehtmleditor, 20, 70, 630, 300, "response[$n]", $answer->response);
								use_html_editor("response[$n]"); // switch on the editor
							} else {
								echo " [".get_string("useeditor", "lesson").": ".
									"<label for=\"responseeditor[$n]\" class=\"hidden-label\">responseeditor[$n]</label><input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\">"; //CDC hidden label added.
								helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
								echo "]<br />\n";
								print_textarea(false, 6, 70, 630, 300, "response[$n]", $answer->response);
							}
						}
						echo "</td></tr>\n";
                        break;
                    case LESSON_TRUEFALSE:
                    case LESSON_MULTICHOICE:
                    case LESSON_SHORTANSWER:
                    case LESSON_NUMERICAL:					
                        echo "<tr><td><b>".get_string("answer", "lesson")." $nplus1:</b>\n";
                        if ($flags & LESSON_ANSWER_EDITOR) {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" 
                                checked=\"checked\">"; //CDC hidden label added.
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                            use_html_editor("answer[$n]"); // switch on the editor
                        } else {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\">"; //CDC hidden label.
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
                        }
                        echo "</td></tr>\n";
                        echo "<tr><td><b>".get_string("response", "lesson")." $nplus1:</b>\n";
                        if ($flags & LESSON_RESPONSE_EDITOR) {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<label for=\"responseeditor[$n]\" class=\"hidden-label\">responseeditor[$n]</label><input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\" 
                                checked=\"checked\">"; //CDC hidden label added.
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "response[$n]", $answer->response);
                            use_html_editor("response[$n]"); // switch on the editor
                        } else {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<label for=\"responseeditor[$n]\" class=\"hidden-label\">responseeditor[$n]</label><input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\">"; //CDC hidden label added.
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
                                "<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" name=\"answereditor[$n]\" value=\"1\" 
                                checked=\"checked\">"; //CDC hidden label added.
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                            use_html_editor("answer[$n]"); // switch on the editor  CDC-FLAG added in this line... editor would not turn on w/o it
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
				switch ($page->qtype) {
					case LESSON_MATCHING :
						if ($n == 2) {
							echo "<tr><td><b>".get_string("correctanswerjump", "lesson").":</b> \n";
							lesson_choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
							helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
							if($lesson->custom)
								echo get_string("correctanswerscore", "lesson").": <input type=\"text\" name=\"score[$n]\" value=\"$answer->score\" size=\"5\">";
							}
						if ($n == 3) {
							echo "<tr><td><b>".get_string("wronganswerjump", "lesson").":</b> \n";
							lesson_choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
							helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
							if($lesson->custom)
								echo get_string("wronganswerscore", "lesson").": <input type=\"text\" name=\"score[$n]\" value=\"$answer->score\" size=\"5\">";
							}
						echo "</td></tr>\n";
						break;
					case LESSON_ESSAY :
						echo "<tr><td><b>".get_string("jump", "lesson").":</b> \n";
						lesson_choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
						helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
						if($lesson->custom) {
							echo get_string("score", "lesson").": <input type=\"text\" name=\"score[$n]\" value=\"$answer->score\" size=\"5\">";
						}
						echo "</td></tr>\n";
						break;
                    case LESSON_TRUEFALSE:
                    case LESSON_MULTICHOICE:
                    case LESSON_SHORTANSWER:
                    case LESSON_NUMERICAL:
						echo "<tr><td><b>".get_string("jump", "lesson")." $nplus1:</b> \n";
						lesson_choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
						helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
						if($lesson->custom) {
							echo get_string("score", "lesson")." $nplus1: <input type=\"text\" name=\"score[$n]\" value=\"$answer->score\" size=\"5\">";
						}
						echo "</td></tr>\n";
						break;
					case LESSON_BRANCHTABLE:
					case LESSON_CLUSTER:
					case LESSON_ENDOFCLUSTER:
					case LESSON_ENDOFBRANCH:
						echo "<tr><td><b>".get_string("jump", "lesson")." $nplus1:</b> \n";
						lesson_choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
						helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
						echo "</td></tr>\n";
						break;
				}
                $n++;
				if ($page->qtype == LESSON_ESSAY) {
					break; // only one answer for essays
				}				
            }
        }
        if ($page->qtype != LESSON_ENDOFBRANCH && $page->qtype != LESSON_CLUSTER && $page->qtype != LESSON_ENDOFCLUSTER) {  /// CDC-FLAG 6/17/04 added to the condition ///
			if ($page->qtype == LESSON_MATCHING) {
				$maxanswers = $lesson->maxanswers + 2;
			} else {
				$maxanswers = $lesson->maxanswers;
			}
            for ($i = $n; $i < $maxanswers; $i++) {
				if ($page->qtype == LESSON_TRUEFALSE && $i > 1) {
					break; // stop printing answers... only need two for true/false
				}
                $iplus1 = $i + 1;
                echo "<input type=\"hidden\" name=\"answerid[$i]\" value=\"0\">\n";
                switch ($page->qtype) {
                    case LESSON_MATCHING:
						$icorrected = $i - 1;
                        echo "<tr><td><b>".get_string("answer", "lesson")." $icorrected:</b>\n";
                        echo " [".get_string("useeditor", "lesson").": ".
                            "<input type=\"checkbox\" name=\"answereditor[$i]\" value=\"1\">";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea(false, 10, 70, 630, 300, "answer[$i]");
                        echo "</td></tr>\n";
                        echo "<tr><td><b>".get_string("matchesanswer", "lesson")." $icorrected:</b>\n";
                        echo " [".get_string("useeditor", "lesson").": ".
                            "<input type=\"checkbox\" name=\"responseeditor[$i]\" value=\"1\">";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea(false, 10, 70, 630, 300, "response[$i]");
                        echo "</td></tr>\n";
                        break;
                    case LESSON_TRUEFALSE:
                    case LESSON_MULTICHOICE:
                    case LESSON_SHORTANSWER:
                    case LESSON_NUMERICAL:
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
				switch ($page->qtype) {
					case LESSON_ESSAY :
						if ($i < 1) {
							echo "<tr><td><B>".get_string("jump", "lesson").":</b> \n";
							lesson_choose_from_menu($jump, "jumpto[$i]", 0, "");
							helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
							if($lesson->custom) {
								echo get_string("score", "lesson").": <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\">";
							}
							echo "</td></tr>\n";
						}
						break;
					case LESSON_MATCHING :
						if ($i == 2) {
							echo "<tr><td><b>".get_string("correctanswerjump", "lesson").":</b> \n";
							lesson_choose_from_menu($jump, "jumpto[$i]", $answer->jumpto, "");
							helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
							if($lesson->custom)
								echo get_string("correctanswerscore", "lesson").": <input type=\"text\" name=\"score[$i]\" value=\"$answer->score\" size=\"5\">";
							}
						if ($i == 3) {
							echo "<tr><td><b>".get_string("wronganswerjump", "lesson").":</b> \n";
							lesson_choose_from_menu($jump, "jumpto[$i]", $answer->jumpto, "");
							helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
							if($lesson->custom)
								echo get_string("wronganswerscore", "lesson").": <input type=\"text\" name=\"score[$i]\" value=\"$answer->score\" size=\"5\">";
							}

						echo "</td></tr>\n";
						break;
                    case LESSON_TRUEFALSE:
                    case LESSON_MULTICHOICE:
                    case LESSON_SHORTANSWER:
                    case LESSON_NUMERICAL:
						echo "<tr><td><B>".get_string("jump", "lesson")." $iplus1:</b> \n";
						lesson_choose_from_menu($jump, "jumpto[$i]", 0, "");
						helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
						if($lesson->custom) {
							echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"-1\" size=\"5\">";
						}
						echo "</td></tr>\n";
						break;
					case LESSON_BRANCHTABLE :
						echo "<tr><td><B>".get_string("jump", "lesson")." $iplus1:</b> \n";
						lesson_choose_from_menu($jump, "jumpto[$i]", 0, "");
						helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
						echo "</td></tr>\n";
						break;
				}
            }
        }
        // close table and form
        ?>
        </table><br />
        <input type="button" value="<?php print_string("redisplaypage", "lesson") ?>" 
            onClick="document.editpage.redisplay.value=1;document.editpage.submit();">
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
			/// CDC-FLAG /// 6/16/04
			if (isset($form->layout)) {
				$newpage->layout = $form->layout;
			} else {
				$newpage->layout = 0;
			}
			if (isset($form->display)) {
				$newpage->display = $form->display;
			} else {
				$newpage->display = 0;
			}
			/// CDC-FLAG ///
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
				/// CDC-FLAG /// 6/16/04				
				if (isset($form->layout)) {
					$newpage->layout = $form->layout;
				} else {
					$newpage->layout = 0;
				}
				if (isset($form->display)) {
					$newpage->display = $form->display;
				} else {
					$newpage->display = 0;
				}				
				/// CDC-FLAG ///
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
				/// CDC-FLAG /// 6/16/04
				if (isset($form->layout)) {
					$newpage->layout = $form->layout;
				} else {
					$newpage->layout = 0;
				}
				if (isset($form->display)) {
					$newpage->display = $form->display;
				} else {
					$newpage->display = 0;
				}				
				/// CDC-FLAG ///
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
		/// CDC-FLAG 6/16/04 added new code to handle essays
		if ($form->qtype == LESSON_ESSAY) {
			$newanswer->lessonid = $lesson->id;
			$newanswer->pageid = $newpageid;
			$newanswer->timecreated = $timenow;
			if (isset($form->jumpto[0])) {
				$newanswer->jumpto = $form->jumpto[0];
			}
			if (isset($form->score[0])) {
				$newanswer->score = $form->score[0];
			}
			$newanswerid = insert_record("lesson_answers", $newanswer);
			if (!$newanswerid) {
				error("Insert Page: answer record $i not inserted");
			}
		} else {
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
					/// CDC-FLAG ///
					if ($lesson->custom) {
						if (isset($form->score[$i])) {
							$newanswer->score = $form->score[$i];
						}
					}
					/// CDC-FLAG ///
					$newanswerid = insert_record("lesson_answers", $newanswer);
					if (!$newanswerid) {
						error("Insert Page: answer record $i not inserted");
					}
				} else {
					if ($form->qtype == LESSON_MATCHING) {
						if ($i < 2) {
							$newanswer->lessonid = $lesson->id;
							$newanswer->pageid = $newpageid;
							$newanswer->timecreated = $timenow;
							$newanswerid = insert_record("lesson_answers", $newanswer);
							if (!$newanswerid) {
								error("Insert Page: answer record $i not inserted");
							}
						}
					} else {
						break;
					}
				}
			}
		}
		/// CDC-FLAG ///
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
        echo "<tr><td><a href=\"lesson.php?id=$cm->id&amp;action=moveit&amp;pageid=$pageid&amp;after=0\"><small>".
            get_string("movepagehere", "lesson")."</small></a></td></tr>\n";
        while (true) {
            if ($page->id != $pageid) {
                if (!$title = trim($page->title)) {
                    $title = "<< ".get_string("notitle", "lesson")."  >>";
                }
                echo "<tr><td bgcolor=\"$THEME->cellheading2\"><b>$title</b></td></tr>\n";
                echo "<tr><td><a href=\"lesson.php?id=$cm->id&amp;action=moveit&amp;pageid=$pageid&amp;after={$page->id}\"><small>".
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
		/// CDC-FLAG /// 6/16/04
		if (isset($form->layout)) {
			$page->layout = $form->layout;
		} else {
			$page->layout = 0;
		}
		if (isset($form->display)) {
			$page->display = $form->display;
		} else {
			$page->display = 0;
		}
		/// CDC-FLAG ///		
        $page->title = $form->title;
        $page->contents = trim($form->contents);
        if (!update_record("lesson_pages", $page)) {
            error("Update page: page not updated");
        }
        if ($page->qtype == LESSON_ENDOFBRANCH || $page->qtype == LESSON_ESSAY || $page->qtype == LESSON_CLUSTER || $page->qtype == LESSON_ENDOFCLUSTER) {
            // there's just a single answer with a jump
            $oldanswer->id = $form->answerid[0];
            $oldanswer->timemodified = $timenow;
            $oldanswer->jumpto = $form->jumpto[0];
			if (isset($form->score[0])) {
				$oldanswer->score = $form->score[0];
			}
			// delete other answers  this if mainly for essay questions.  If one switches from using a qtype like Multichoice,
			// then switches to essay, the old answers need to be removed because essay is
			// supposed to only have one answer record
			if ($answers = get_records_select("lesson_answers", "pageid = $form->pageid")) {
				foreach ($answers as $answer) {
					if ($answer->id != $form->answerid[0]) {
                        if (!delete_records("lesson_answers", "id", $answer->id)) {
                            error("Update page: unable to delete answer record");
						}
					}
				}
			}		
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
						/// CDC-FLAG ///
						if ($lesson->custom) {
							$oldanswer->score = $form->score[$i];
						}
						/// CDC-FLAG ///
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
						/// CDC-FLAG ///
						$newanswer->score = $form->score[$i];
						/// CDC-FLAG ///
                        $newanswerid = insert_record("lesson_answers", $newanswer);
                        if (!$newanswerid) {
                            error("Update page: answer record not inserted");
                        }
                    }
                } else {
				 	if ($form->qtype == LESSON_MATCHING) {
						if ($i >= 2) {
							if ($form->answerid[$i]) {
								// need to delete blanked out answer
								if (!delete_records("lesson_answers", "id", $form->answerid[$i])) {
									error("Update page: unable to delete answer record");
								}
							}
						} else {
							unset($oldanswer);
							$oldanswer->id = $form->answerid[$i];
							$oldanswer->flags = $form->answereditor[$i] * LESSON_ANSWER_EDITOR +
								$form->responseeditor[$i] * LESSON_RESPONSE_EDITOR;
							$oldanswer->timemodified = $timenow;
	                        $oldanswer->answer = NULL;
							if (!update_record("lesson_answers", $oldanswer)) {
								error("Update page: answer $i not updated");
							}
						}						
                    } elseif ($form->answerid[$i]) {
                        // need to delete blanked out answer
                        if (!delete_records("lesson_answers", "id", $form->answerid[$i])) {
                            error("Update page: unable to delete answer record");
                        }
                    }
                }
            }
        }
        if ($form->redisplay) {
            redirect("lesson.php?id=$cm->id&amp;action=editpage&amp;pageid=$page->id");
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

