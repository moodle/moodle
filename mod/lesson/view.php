<?PHP  // $Id$

/// This page prints a particular instance of lesson
/// (Replace lesson with the name of your module)

    require_once("../../config.php");
    require_once("lib.php");

    require_variable($id);    // Course Module ID, or

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

    add_to_log($course->id, "lesson", "view", "view.php?id=$cm->id", "$lesson->id");

/// Print the page header

    if ($course->category) {
        $navigation = "<A HREF=\"../../course/view.php?id=$course->id\">$course->shortname</A> ->";
    }

    $strlessons = get_string("modulenameplural", "lesson");
    $strlesson  = get_string("modulename", "lesson");

    print_header("$course->shortname: $lesson->name", "$course->fullname",
                 "$navigation <A HREF=index.php?id=$course->id>$strlessons</A> -> <a href=\"view.php?id=$cm->id\">$lesson->name</a>", 
                  "", "", true, update_module_button($cm->id, $course->id, $strlesson), 
                  navmenu($course, $cm));

    // set up some general variables
    $usehtmleditor = can_use_html_editor();
    $path = "$CFG->wwwroot/course";
    if (empty($THEME->custompix)) {
        $pixpath = "$path/../pix";
    } else {
        $pixpath = "$path/../theme/$CFG->theme/pix";
    }

    if (empty($action)) {
        if (isteacher($course->id)) {
            $action = 'teacherview';
        } else {
            $action = 'navigation';
        }
    }

    /************** navigation **************************************/
    if ($action == 'navigation') {
        // display individual pages and their sets of answers
        // if pageid is EOL then the end of the lesson has been reached
        print_heading($lesson->name);
		if (empty($_GET['pageid'])) {
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
                    } elseif ($jumpto == NEXTPAGE) {
                        if (!$lastpageseen = get_field("lesson_pages", "nextpageid", "id", 
                                    $attempt->pageid)) {
                            // no nextpage go to end of lesson
                            $lastpageseen = EOL;
                        }
                    } else {
                        $lastpageseen = $jumpto;
                    }
                    break; // only look at the latest correct attempt 
                }
                if ($lastpageseen != $firstpageid) {
                    notice_yesno(get_string("youhaveseen","lesson"), 
                        "view.php?id=$cm->id&action=navigation&pageid=$lastpageseen", 
                        "view.php?id=$cm->id&action=navigation&pageid=$firstpageid");
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
          		    redirect("../../course/view.php?id=$course->id", get_string("alreadytaken", "lesson"));
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
		} else {
            $pageid = $_GET['pageid'];
        }
        if ($pageid != EOL) {
            if (!$page = get_record("lesson_pages", "id", $pageid)) {
                error("Navigation: the page record not found");
            }
            echo "<table align=\"center\" width=\"80%\" border=\"0\"><tr><td>\n";
            print_heading($page->title);
            print_simple_box(format_text($page->contents), 'center');
            echo "<br />\n";
            if ($answers = get_records("lesson_answers", "pageid", $page->id)) {
                shuffle($answers);
                echo "<form name=\"pageform\" method =\"post\" action=\"lesson.php\">\n";
                echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
                echo "<input type=\"hidden\" name=\"action\" value=\"continue\">\n";
                echo "<input type=\"hidden\" name=\"pageid\" value=\"$pageid\">\n";
                print_simple_box_start("center");
                echo '<table width="100%">';
                foreach ($answers as $answer) {
                    echo "<tr><td><input type=\"radio\" name=\"answerid\" value=\"{$answer->id}\"></td>\n";
                    echo "<td>\n";
                    $options->para = false; // no <p></p>
                    echo format_text(trim($answer->answer), FORMAT_MOODLE, $options); 
                    echo "</td></tr>\n";
                }
                echo '</table>';
                print_simple_box_end();
                echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
                    get_string("pleasecheckoneanswer", "lesson")."\"></p>\n";
                echo "</form>\n";
            } else {
                // a page without answers - find the next (logical) page
                if (!$newpageid = get_field("lesson_pages", "nextpageid", "id", $pageid)) {
                    // this is the last page - flag end of lesson
                    $newpageid = EOL;
                }
		        print_continue("view.php?id=$cm->id&action=navigation&pageid=$newpageid");
            }
            echo "</table>\n";
        } else {
            // end of lesson reached work out grade
            print_heading(get_string("congratulations", "lesson"));
            print_simple_box_start("center");
            $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id);
            if (isstudent($course->id)) {
                $ncorrect = count_records_select("lesson_attempts", "lessonid = $lesson->id AND
                        userid = $USER->id AND retry = $ntries AND correct = 1");
                $nviewed = count_records("lesson_attempts", "lessonid", $lesson->id, "userid", $USER->id,
                        "retry", $ntries);
                if ($nviewed) {
                    $thegrade = intval(100 * $ncorrect / $nviewed);
                } else {
                    $thegrade = 0;
                }
                echo "<p align=\"center\">".get_string("numberofpagesviewed", "lesson", $nviewed)."</p>\n";
                echo "<p align=\"center\">".get_string("numberofcorrectanswers", "lesson", $ncorrect).
                    "</p>\n";
                echo "<p align=\"center\">".get_string("gradeis", "lesson", 
                        number_format($thegrade * $lesson->grade / 100, 1)).
                        " (".get_string("outof", "lesson", $lesson->grade).")</p>\n";
                $grade->lessonid = $lesson->id;
                $grade->userid = $USER->id;
                $grade->grade = $thegrade;
                $grade->completed = time();
                if (!$newgradeid = insert_record("lesson_grades", $grade)) {
                    error("Navigation: grade not inserted");
                }
            } else { 
                // display for teacher
                echo "<p align=\"center\">".get_string("displayofgrade", "lesson")."</p>\n";
            }
            print_simple_box_end();
		    print_continue("../../course/view.php?id=$course->id");
        }
            
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
            // if there are no pages give teacher a blank proforma
            ?>
            <form name="form" method="post" action="lesson.php">
            <input type="hidden" name="id" value="<?PHP echo $cm->id ?>">
            <input type="hidden" name="action" value="insertpage">
            <input type="hidden" name="pageid" value="0">
            <center><table cellpadding=5 border=1>
            <tr><td align="center">
            <tr valign="top">
            <td><p><b><?php print_string("pagetitle", "lesson"); ?>:</b></p></td></tr>
            <tr><td><input type="text" name="title" size="80" maxsize="255" value=""></td></tr>
            <?PHP
            echo "<tr><td><b>";
            echo get_string("pagecontents", "lesson").":</b><br />\n";
            print_textarea($usehtmleditor, 25, 70, 630, 400, "contents");
            echo "</td></tr>\n";
            for ($i = 0; $i < $lesson->maxanswers; $i++) {
                $iplus1 = $i + 1;
                echo "<tr><td><b>".get_string("answer", "lesson")." $iplus1:</b><br />\n";
                print_textarea($usehtmleditor, 20, 70, 630, 100, "answer[$i]");
                echo "</td></tr>\n";
                echo "<tr><td><b>".get_string("response", "lesson")." $iplus1:</b><br />\n";
                print_textarea($usehtmleditor, 20, 70, 630, 100, "response[$i]");
                echo "</td></tr>\n";
                if ($i) {
                    // answers 2,3,4... jump to this page
                    echo "<input type=\"hidden\" name=\"jumpto[$i]\" value =\"0\">\n";
                } else {
                    // answer 1 jumps to next page
                    echo "<input type=\"hidden\" name=\"jumpto[$i]\" value =\"".NEXTPAGE."\">\n";
                }
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
        } else {
            // print the pages
            echo "<center><table cellpadding=\"5\" border=\"0\" width=\"80%\">\n";
            echo "<tr><td align=\"right\"><a href=\"lesson.php?id=$cm->id&action=addpage&pageid=0\"><small>".
                get_string("addpagehere", "lesson")."</small></a></td></tr><tr><td>\n";
            while (true) {
                echo "<table width=\"100%\" border=\"1\"><tr><td bgcolor=\"$THEME->cellheading2\" colspan=\"2\"><b>$page->title</b>&nbsp;&nbsp;\n";
                if ($npages > 1) {
                    echo "<a title=\"".get_string("move")."\" href=\"lesson.php?id=$cm->id&action=move&pageid=$page->id\">\n".
                        "<img src=\"$pixpath/t/move.gif\" hspace=\"2\" height=11 width=11 border=0></a>\n";
                }
                echo "<a title=\"".get_string("update")."\" href=\"lesson.php?id=$cm->id&action=editpage&pageid=$page->id\">\n".
                    "<img src=\"$pixpath/t/edit.gif\" hspace=\"2\" height=11 width=11 border=0></a>\n".
                    "<a title=\"".get_string("delete")."\" href=\"lesson.php?id=$cm->id&action=confirmdelete&pageid=$page->id\">\n".
                    "<img src=\"$pixpath/t/delete.gif\" hspace=\"2\" height=11 width=11 border=0></a>".
                    "</td></tr>\n";             
                echo "<tr><td colspan=\"2\">\n";
                print_simple_box(format_text($page->contents), "center");
                echo "</td></tr>\n";
                if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
                    $i = 1;
                    foreach ($answers as $answer) {
                        echo "<tr><td bgcolor=\"$THEME->cellheading2\" colspan=\"2\">&nbsp;</td></tr>\n";
                        echo "<tr><td align=\"right\" valign=\"top\" width=\"20%\">\n";
                        if (lesson_iscorrect($page->id, $answer->jumpto)) {
                            // underline correct answers
                            echo "<b><u>".get_string("answer", "lesson")." $i:</u></b> \n";
                        } else {
                            echo "<b>".get_string("answer", "lesson")." $i:</b> \n";
                        }
                        echo "</td><td width=\"80%\">\n";
                        echo format_text($answer->answer);
                        echo "</td></tr>\n";
                        echo "<tr><td align=\"right\" valign=\"top\"><b>".get_string("response", "lesson")." $i:</b> \n";
                        echo "</td><td>\n";
                        echo format_text($answer->response); 
                        echo "</td></tr>\n";
                        if ($answer->jumpto == 0) {
                            $jumptitle = get_string("thispage", "lesson");
                        } elseif ($answer->jumpto == NEXTPAGE) {
                            $jumptitle = get_string("nextpage", "lesson");
                        } elseif ($answer->jumpto == EOL) {
                            $jumptitle = get_string("endoflesson", "lesson");
                        } else {
                            if (!$jumptitle = get_field("lesson_pages", "title", "id", $answer->jumpto)) {
                                $jumptitle = "<b>".get_string("notdefined", "lesson")."</b>";
                            }
                        }
                        echo "<tr><td align=\"right\"><b>".get_string("jumpto", "lesson").": </b>\n";
                        echo "</td><td>\n";
                        echo "$jumptitle</td></tr>\n";
                        $i++;
                    }
                    // print_simple_box_end();
                }
                echo "</td></tr></table></td></tr><tr><td align=\"right\"><a href=\"lesson.php?id=$cm->id&action=addpage&pageid=$page->id\"><small>".
                    get_string("addpagehere", "lesson")."</small></a></td></tr><tr><td>\n";
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
            print_heading("<a href=\"view.php?id=$cm->id&action=navigation\">".get_string("checknavigation",
                        "lesson")."</a>\n");
        }
    }


    /*************** no man's land **************************************/
	else {
		error("Fatal Error: Unknown Action: ".$action."\n");
	}

/// Finish the page
    print_footer($course);

?>
