<?PHP  // $Id$

/// This page prints a particular instance of lesson
/// (Replace lesson with the name of your module)

    require_once("../../config.php");
    require_once("lib.php");

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
                if ($lastpageseen != $firstpageid) {
                    echo "<form name=\"queryform\" method =\"post\" action=\"view.php\">\n";
                    echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
                    echo "<input type=\"hidden\" name=\"action\" value=\"navigation\">\n";
                    echo "<input type=\"hidden\" name=\"pageid\">\n";
                    print_simple_box("<p align=\"center\">".get_string("youhaveseen","lesson")."</p>",
                            "center");
                    echo "<p align=\"center\"><input type=\"button\" value=\"".get_string("yes").
                        "\" onclick=\"document.queryform.pageid.value='$lastpageseen';document.queryform.submit();\">&nbsp;&nbsp;&nbsp;<input type=\"button\" value=\"".get_string("no").
                        "\" onclick=\"document.queryform.pageid.value='$firstpageid';document.queryform.submit();\"></p>\n";
                    echo "</form>\n";
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
        }
        if ($pageid != LESSON_EOL) {
            add_to_log($course->id, "lesson", "view", "view.php?id=$cm->id", "$pageid", $cm->id);
            if (!$page = get_record("lesson_pages", "id", $pageid)) {
                error("Navigation: the page record not found");
            }
            // before we output everything check to see if the page is a EOB, if so jump directly 
            // to it's associated branch table
            if ($page->qtype == LESSON_ENDOFBRANCH) {
                if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
                    // print_heading(get_string("endofbranch", "lesson"));
                    foreach ($answers as $answer) {
                        // just need the first answer
                        redirect("view.php?id=$cm->id&action=navigation&pageid=$answer->jumpto",
                                get_string("endofbranch", "lesson"));
                        break;
                    }
                    print_footer($course);
                    exit();
                } else {
                    error("Navigation: No answers on EOB");
                }
            }
            // it's not a EOB process it...
            echo "<table align=\"center\" width=\"80%\" border=\"0\"><tr><td>\n";
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
            print_heading($page->title);
            print_simple_box(format_text($page->contents), 'center');
            echo "<br />\n";
            // get the answers in a set order, the id order
            if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
                echo "<form name=\"answerform\" method =\"post\" action=\"lesson.php\">";
                echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">";
                echo "<input type=\"hidden\" name=\"action\" value=\"continue\">";
                echo "<input type=\"hidden\" name=\"pageid\" value=\"$pageid\">";
                print_simple_box_start("center");
                echo '<table width="100%">';
                switch ($page->qtype) {
                    case LESSON_SHORTANSWER :
                    case LESSON_NUMERICAL :
                        echo "<tr><td align=\"center\">".get_string("youranswer", "lesson").
                            ": <input type=\"text\" name=\"answer\" size=\"50\" maxlength=\"200\">\n";
                        echo '</table>';
                        print_simple_box_end();
                        echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
                            get_string("pleaseenteryouranswerinthebox", "lesson")."\"></p>\n";
                        break;
                    case LESSON_TRUEFALSE :
                        shuffle($answers);
                        foreach ($answers as $answer) {
                            echo "<tr><td>";
                            echo "<input type=\"radio\" name=\"answerid\" value=\"{$answer->id}\">";
                            echo "</td><td>";
                            $options->para = false; // no <p></p>
                            echo format_text(trim($answer->answer), FORMAT_MOODLE, $options); 
                            echo "</td></tr>";
                        }
                        echo '</table>';
                        print_simple_box_end();
                        echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
                            get_string("pleasecheckoneanswer", "lesson")."\"></p>\n";
                        break;
                    case LESSON_MULTICHOICE :
                        $i = 0;
                        shuffle($answers);
                        foreach ($answers as $answer) {
                            echo "<tr><td>";
                            if ($page->qoption) {
                                // more than one answer allowed 
                                echo "<input type=\"checkbox\" name=\"answer[$i]\" value=\"{$answer->id}\">";
                            } else {
                                // only one answer allowed
                                echo "<input type=\"radio\" name=\"answerid\" value=\"{$answer->id}\">";
                            }
                            echo "</td><td>";
                            $options->para = false; // no <p></p>
                            echo format_text(trim($answer->answer), FORMAT_MOODLE, $options); 
                            echo "</td></tr>";
                            $i++;
                        }
                        echo '</table>';
                        print_simple_box_end();
                        if ($page->qoption) {
                            echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
                                get_string("pleasecheckoneormoreanswers", "lesson")."\"></p>\n";
                        } else {
                            echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
                                get_string("pleasecheckoneanswer", "lesson")."\"></p>\n";
                        }
                        break;
                        
                    case LESSON_MATCHING :
                        echo "<tr><td><table width=\"100%\">";
                        // don't suffle answers (could be an option??)
                        foreach ($answers as $answer) {
                            // get all the responses
                            $responses[] = trim($answer->response);
                        }
                        shuffle($responses);
                        foreach ($answers as $answer) {
                            echo "<tr><td align=\"right\">";
                            echo "<b>$answer->answer: </b></td><td>";
                            echo "<select name=\"response[$answer->id]\">";
                            echo "<option value=\"0\" selected=\"selected\">Choose...</option>";
                            foreach ($responses as $response) {
                                echo "<option value=\"$response\">$response</option>";
                            }
                            echo "</select>";
                            echo "</td></tr>";
                        }
                        echo '</table></table>';
                        print_simple_box_end();
                        echo "<p align=\"center\"><input type=\"submit\" name=\"continue\" value=\"".
                            get_string("pleasematchtheabovepairs", "lesson")."\"></p>\n";
                        break;

                    case LESSON_BRANCHTABLE :
                        echo "<tr><td><table width=\"100%\">";
                        echo "<input type=\"hidden\" name=\"jumpto\">";
                        // don't suffle answers
                        foreach ($answers as $answer) {
                            echo "<tr><td align=\"center\">";
                            echo "<input type=\"button\" value=\"$answer->answer\"";
                            echo "onclick=\"document.answerform.jumpto.value=$answer->jumpto;document.answerform.submit();\">";
                            echo "</td></tr>";
                        }
                        echo '</table></table>';
                        print_simple_box_end();
                        break;

                }
                echo "</form>\n";
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
            add_to_log($course->id, "lesson", "end", "view.php?id=$cm->id", "$lesson->id", $cm->id);
            print_heading(get_string("congratulations", "lesson"));
            print_simple_box_start("center");
            $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $USER->id);
            if (isstudent($course->id)) {
                if ($nviewed = count_records("lesson_attempts", "lessonid", $lesson->id, "userid", 
                        $USER->id, "retry", $ntries)) {
                    // count the number of distinct correct pages
                    if ($correctpages = get_records_select("lesson_attempts",  "lessonid = $lesson->id AND 
                            userid = $USER->id AND retry = $ntries AND correct = 1")) {
                        foreach ($correctpages as $correctpage) {
                            $temp[$correctpage->pageid] = 1;
                        }
                        $ncorrect = count($temp);
                    } else {
                        $ncorrect = 0;
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
                    $thegrade = intval(100 * $ncorrect / $nviewed);
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
                    print_string("noattemptrecordsfound", "lesson");
                    $thegrade = 0;
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
                print_textarea(false, 6, 70, 630, 100, "answer[$i]");
                echo "</td></tr>\n";
                echo "<tr><td><b>".get_string("response", "lesson")." $iplus1:</b><br />\n";
                print_textarea(false, 6, 70, 630, 100, "response[$i]");
                echo "</td></tr>\n";
                if ($i) {
                    // answers 2,3,4... jump to this page
                    echo "<input type=\"hidden\" name=\"jumpto[$i]\" value =\"0\">\n";
                } else {
                    // answer 1 jumps to next page
                    echo "<input type=\"hidden\" name=\"jumpto[$i]\" value =\"".LESSON_NEXTPAGE."\">\n";
                }
            }
            // close table and form
            ?>
            </table><br />
            <input type="submit" value="<?php  print_string("savepage", "lesson") ?>">
            <input type="submit" name="cancel" value="<?php  print_string("cancel") ?>">
            </center>
            </form>
            <?PHP
            // show import link
            print_heading("<a href=\"import.php?id=$cm->id&pageid=0\">".get_string("importquestions",
                    "lesson")."</a>\n");
        } else {
            // print the pages
            echo "<form name=\"lessonpages\" method=\"post\" action=\"view.php\">\n";
            echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">\n";
            echo "<input type=\"hidden\" name=\"action\" value=\"navigation\">\n";
            echo "<input type=\"hidden\" name=\"pageid\">\n";
            echo "<center><table cellpadding=\"5\" border=\"0\" width=\"80%\">\n";
            if (isteacheredit($course->id)) {
                echo "<tr><td align=\"right\"><small><a href=\"import.php?id=$cm->id&pageid=0\">".
                    get_string("importquestions", "lesson")."</a> | ".
                    "<a href=\"lesson.php?id=$cm->id&action=addbranchtable&pageid=0\">".
                    get_string("addabranchtable", "lesson")."</a> | ".
                    "<a href=\"lesson.php?id=$cm->id&action=addpage&pageid=0\">".
                    get_string("addaquestionpage", "lesson")." ".get_string("here","lesson").
                    "</a></small></td></tr>\n";
            }
            echo "<tr><td>\n";
            while (true) {
                echo "<table width=\"100%\" border=\"1\"><tr><td bgcolor=\"$THEME->cellheading2\" colspan=\"2\"><b>$page->title</b>&nbsp;&nbsp;\n";
                if (isteacheredit($course->id)) {
                    if ($npages > 1) {
                        echo "<a title=\"".get_string("move")."\" href=\"lesson.php?id=$cm->id&action=move&pageid=$page->id\">\n".
                            "<img src=\"$pixpath/t/move.gif\" hspace=\"2\" height=11 width=11 border=0></a>\n";
                    }
                    echo "<a title=\"".get_string("update")."\" href=\"lesson.php?id=$cm->id&action=editpage&pageid=$page->id\">\n".
                        "<img src=\"$pixpath/t/edit.gif\" hspace=\"2\" height=11 width=11 border=0></a>\n".
                        "<a title=\"".get_string("delete")."\" href=\"lesson.php?id=$cm->id&action=confirmdelete&pageid=$page->id\">\n".
                        "<img src=\"$pixpath/t/delete.gif\" hspace=\"2\" height=11 width=11 border=0></a>\n";
                    }
                    echo "</td></tr>\n";             
                echo "<tr><td colspan=\"2\">\n";
                print_simple_box(format_text($page->contents), "center");
                echo "</td></tr>\n";
                // get the answers in a set order, the id order
                if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
                    echo "<tr><td bgcolor=\"$THEME->cellheading2\" colspan=\"2\" align=\"center\"><b>\n";
                    switch ($page->qtype) {
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
                    foreach ($answers as $answer) {
                        switch ($page->qtype) {
                            case LESSON_MULTICHOICE:
                            case LESSON_TRUEFALSE:
                            case LESSON_SHORTANSWER:
                            case LESSON_NUMERICAL:
                            case LESSON_MATCHING:
                                echo "<tr><td bgcolor=\"$THEME->cellheading2\" align=\"right\" valign=\"top\" width=\"20%\">\n";
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
                        } else {
                            if (!$jumptitle = get_field("lesson_pages", "title", "id", $answer->jumpto)) {
                                $jumptitle = "<b>".get_string("notdefined", "lesson")."</b>";
                            }
                        }
                        echo "<tr><td align=\"right\" width=\"20%\"><b>".get_string("jump", "lesson")." $i:";
                        echo "</b></td><td width=\"80%\">\n";
                        echo "$jumptitle</td></tr>\n";
                        $i++;
                    }
                    // print_simple_box_end();
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
                    echo "<tr><td align=\"right\"><small><a href=\"import.php?id=$cm->id&pageid=$page->id\">".
                        get_string("importquestions", "lesson")."</a> | ".
                        "<a href=\"lesson.php?id=$cm->id&action=addbranchtable&pageid=$page->id\">".
                        get_string("addabranchtable", "lesson")."</a> | ";
                    // the current page or the next page is an end of branch don't show EOB link
                    $nextqtype = 0; // set to anything else EOB
                    if ($page->nextpageid) {
                        $nextqtype = get_field("lesson_pages", "qtype", "id", $page->nextpageid);
                    }
                    if (($page->qtype != LESSON_ENDOFBRANCH) and ($nextqtype != LESSON_ENDOFBRANCH)) {
                        echo "<a href=\"lesson.php?id=$cm->id&action=addendofbranch&pageid=$page->id\">".
                        get_string("addanendofbranch", "lesson")."</a> | ";
                    }
                    echo "<a href=\"lesson.php?id=$cm->id&action=addpage&pageid=$page->id\">".
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
                if ($page->nextpageid) {
                    if (!$page = get_record("lesson_pages", "id", $page->nextpageid)) {
                        error("Teacher view: Next page not found!");
                    }
                } else {
                    // last page reached
                    break;
                }
            }
            echo "</table></form>\n";
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
