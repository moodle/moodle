<?php  // $Id$
/**
 * Provides the interface for overall authoring of lessons
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    require_once('../../config.php');
    require_once('locallib.php');
    require_once('lib.php');

    $id      = required_param('id', PARAM_INT);             // Course Module ID
    $display = optional_param('display', 0, PARAM_INT);
    $mode    = optional_param('mode', get_user_preferences('lesson_view', 'collapsed'), PARAM_ALPHA);
    
    set_user_preference('lesson_view', $mode);
    
    // set collapsed flag
    if ($mode == 'collapsed') {
        $collapsed = true;
    } else {
        $collapsed = false;
    }

    list($cm, $course, $lesson) = lesson_get_basics($id);
    
    require_login($course->id, false, $cm);
    
    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    
    require_capability('mod/lesson:manage');
    
    lesson_print_header($cm, $course, $lesson, $mode);
    
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
        echo "<form name=\"lessonpages\" method=\"post\" action=\"$CFG->wwwroot/mod/lesson/view.php\">\n";
        echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\" />\n";
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

    print_footer($course);
?>