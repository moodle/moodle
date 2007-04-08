<?php // $Id$
/**
 *  Action for editing a page.  Prints an HTML form.
 *
 * @version $Id$
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
 
    // get the page
    $pageid = required_param('pageid', PARAM_INT);
    $redirect = optional_param('redirect', '', PARAM_ALPHA);
    
    if (!$page = get_record("lesson_pages", "id", $pageid)) {
        error("Edit page: page record not found");
    }

    $page->qtype = optional_param('qtype', $page->qtype, PARAM_INT);

    // set of jump array
    $jump = array();
    $jump[0] = get_string("thispage", "lesson");
    $jump[LESSON_NEXTPAGE] = get_string("nextpage", "lesson");
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
    $jump[LESSON_EOL] = get_string("endoflesson", "lesson");
    if (!$apageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
        error("Edit page: first page not found");
    }
    while (true) {
        if ($apageid) {
            if (!$apage = get_record("lesson_pages", "id", $apageid)) {
                error("Edit page: apage record not found");
            }
            // removed != LESSON_ENDOFBRANCH...
            if (trim($page->title)) { // ...nor nuffin pages
                $jump[$apageid] = strip_tags(format_string($apage->title,true));
            }
            $apageid = $apage->nextpageid;
        } else {
            // last page reached
            break;
        }
    }
    // give teacher a proforma
    ?>
    <form id="editpage" method="post" action="lesson.php">
    <fieldset class="invisiblefieldset fieldsetfix">
    <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
    <input type="hidden" name="action" value="updatepage" />
    <input type="hidden" name="pageid" value="<?php echo $pageid ?>" />
    <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>" />        
    <input type="hidden" name="redirect" value="<?php echo $redirect ?>" />        
    <input type="hidden" name="redisplay" value="0" />
    <center>
       <?php
        switch ($page->qtype) {
            case LESSON_MULTICHOICE :
                echo '<b>'.get_string("questiontype", "lesson").":</b> \n";
                echo helpbutton("questiontypes", get_string("questiontype", "lesson"), "lesson")."<br />";
                lesson_qtype_menu($LESSON_QUESTION_TYPE, $page->qtype, 
                                  "lesson.php?id=$cm->id&amp;action=editpage&amp;pageid=$page->id",
                                  "getElementById('editpage').redisplay.value=1;getElementById('editpage').submit();");
                echo "<p><b><label for=\"qoption\">".get_string('multianswer', 'lesson').":</label></b> \n";
                if ($page->qoption) {
                    echo "<input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\" checked=\"checked\" />";
                } else {
                    echo "<input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\" />";
                }
                helpbutton("questionoption", get_string("questionoption", "lesson"), "lesson");
                echo "</p>\n";
                break;
            case LESSON_SHORTANSWER :
                echo '<b>'.get_string("questiontype", "lesson").":</b> \n";
                echo helpbutton("questiontypes", get_string("questiontype", "lesson"), "lesson")."<br />";
                lesson_qtype_menu($LESSON_QUESTION_TYPE, $page->qtype, 
                                  "lesson.php?id=$cm->id&amp;action=editpage&amp;pageid=$page->id",
                                  "getElementById('editpage').redisplay.value=1;getElementById('editpage').submit();");
                echo "<p><b><label for=\"qoption\">".get_string('casesensitive', 'lesson').":</label></b> \n";
                if ($page->qoption) {
                    echo "<input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\" checked=\"checked\" />";
                } else {
                    echo "<input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\" />";
                }
                helpbutton("questionoption", get_string("questionoption", "lesson"), "lesson");
                echo "</p>\n";
                break;
            case LESSON_TRUEFALSE :
            case LESSON_ESSAY :
            case LESSON_MATCHING :
            case LESSON_NUMERICAL :
                echo '<b>'.get_string("questiontype", "lesson").":</b> \n";
                echo helpbutton("questiontypes", get_string("questiontype", "lesson"), "lesson")."<br />";
                lesson_qtype_menu($LESSON_QUESTION_TYPE, $page->qtype, 
                                  "lesson.php?id=$cm->id&amp;action=editpage&amp;pageid=$page->id",
                                  "getElementById('editpage').redisplay.value=1;getElementById('editpage').submit();");
                break;
        }
    ?>
    <table cellpadding="5" class="generalbox" border="1">
    <tr valign="top">
    <td><b><label for="title"><?php print_string('pagetitle', 'lesson'); ?>:</label></b><br />
    <input type="text" id="title" name="title" size="80" maxsize="255" value="<?php p($page->title) ?>" /></td>
    </tr>
    <?PHP
    echo "<tr><td><b>";
    echo get_string("pagecontents", "lesson").":</b><br />\n";
    print_textarea($usehtmleditor, 25, 70, 630, 400, "contents", $page->contents);
    if ($usehtmleditor) {
        use_html_editor("contents");
    }
    echo "</td></tr>\n";
    $n = 0;
    switch ($page->qtype) {
        case LESSON_BRANCHTABLE :
            echo "<input type=\"hidden\" name=\"qtype\" value=\"$page->qtype\" />\n";
            echo "<tr><td>\n";
            echo "<center>";
            if ($page->layout) {
                echo "<input checked=\"checked\" name=\"layout\" type=\"checkbox\" value=\"1\" />";
            } else {
                echo "<input name=\"layout\" type=\"checkbox\" value=\"1\" />";
            }
            echo get_string("arrangebuttonshorizontally", "lesson")."\n";
            echo "<br />";
            if ($page->display) {
                echo "<input name=\"display\" type=\"checkbox\" value=\"1\" checked=\"checked\" />";
            } else {
                echo "<input name=\"display\" type=\"checkbox\" value=\"1\" />";
            }                
            echo get_string("displayinleftmenu", "lesson")."\n";
            echo "</center></td></tr>\n";
            echo "<tr><td><b>".get_string("branchtable", "lesson")."</b> \n";
            break;
        case LESSON_CLUSTER :
            echo "<input type=\"hidden\" name=\"qtype\" value=\"$page->qtype\" />\n";
            echo "<tr><td><b>".get_string("clustertitle", "lesson")."</b> \n";
            break;                
        case LESSON_ENDOFCLUSTER :
            echo "<input type=\"hidden\" name=\"qtype\" value=\"$page->qtype\" />\n";
            echo "<tr><td><b>".get_string("endofclustertitle", "lesson")."</b> \n";
            break;                            
        case LESSON_ENDOFBRANCH :
            echo "<input type=\"hidden\" name=\"qtype\" value=\"$page->qtype\" />\n";
            echo "<tr><td><b>".get_string("endofbranch", "lesson")."</b> \n";
            break;
        default :
            echo "<tr><td>";
        break;             
    }

    echo "</td></tr>\n";
    // get the answers in a set order, the id order

    if ($answers = get_records("lesson_answers", "pageid", $page->id, "id")) {
        foreach ($answers as $answer) {
            $flags = intval($answer->flags); // force into an integer
            $nplus1 = $n + 1;
            echo "<input type=\"hidden\" name=\"answerid[$n]\" value=\"$answer->id\" />\n";
            switch ($page->qtype) {
                case LESSON_MATCHING:
                    if ($n == 0) {
                        echo "<tr><td><b><label for=\"edit-answer[$n]\">".get_string('correctresponse', 'lesson').":</label></b>\n";
                        if ($flags & LESSON_ANSWER_EDITOR) {
                            echo " [<label for=\"answereditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                                "<input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" checked=\"checked\" />";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                            use_html_editor("answer[$n]"); // switch on the editor
                        } else {
                            echo " [<label for=\"answereditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                                "<input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" />";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
                        }
                    } elseif ($n == 1) {
                        echo "<tr><td><b><label for=\"edit-answer[$n]\">".get_string('wrongresponse', 'lesson').":</label></b>\n";
                        if ($flags & LESSON_ANSWER_EDITOR) {
                            echo " [<label for=\"answereditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                                "<input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" checked=\"checked\" />";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                            use_html_editor("answer[$n]"); // switch on the editor
                        } else {
                            echo " [<label for=\"answereditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                                "<input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" />";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
                        }
                    } else {
                        $ncorrected = $n - 1;
                        echo "<tr><td><b><label for=\"edit-answer[$n]\">".get_string('answer', 'lesson')." $ncorrected:</label></b>\n";
                        if ($flags & LESSON_ANSWER_EDITOR) {
                            echo " [<label for=\"answereditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                                "<input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" checked=\"checked\" />"; 
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                            use_html_editor("answer[$n]"); // switch on the editor
                        } else {
                            echo " [<label for=\"answereditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                                "<input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" />";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
                        }
                        echo "</td></tr>\n";
                        echo "<tr><td><b><label for=\"edit-response[$n]\">".get_string('matchesanswer', 'lesson')." $ncorrected:</label></b>\n";
                        if ($flags & LESSON_RESPONSE_EDITOR) {
                            echo " [<label for=\"responseeditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                                "<input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\" checked=\"checked\" />";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "response[$n]", $answer->response);
                            use_html_editor("response[$n]"); // switch on the editor
                        } else {
                            echo " [<label for=\"responseeditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                                "<input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\" />";
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
                    echo "<tr><td><b><label for=\"edit-answer[$n]\">".get_string('answer', 'lesson')." $nplus1:</label></b>\n";
                    if ($flags & LESSON_ANSWER_EDITOR and $page->qtype != LESSON_SHORTANSWER and $page->qtype != LESSON_NUMERICAL) {
                        echo " [<label for=\"answereditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                            "<input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" checked=\"checked\" />";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                        use_html_editor("answer[$n]"); // switch on the editor
                    } else {
                        if ($page->qtype != LESSON_SHORTANSWER and $page->qtype != LESSON_NUMERICAL) {
                            echo " [<label for=\"answereditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                                "<input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" />";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
                        } else {
                            echo "<br />\n";
                            print_textarea(false, 1, 70, 630, 300, "answer[$n]", $answer->answer);
                        }
                    }
                    echo "</td></tr>\n";
                    echo "<tr><td><b><label for=\"edit-response[$n]\">".get_string('response', 'lesson')." $nplus1:</label></b>\n";
                    if ($flags & LESSON_RESPONSE_EDITOR) {
                        echo " [<label for=\"responseeditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                            "<input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\" checked=\"checked\" />";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea($usehtmleditor, 20, 70, 630, 300, "response[$n]", $answer->response);
                        use_html_editor("response[$n]"); // switch on the editor
                    } else {
                        echo " [<label for=\"responseeditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                            "<input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\" />";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea(false, 6, 70, 630, 300, "response[$n]", $answer->response);
                    }
                    echo "</td></tr>\n";
                    break;
                case LESSON_BRANCHTABLE:
                    echo "<tr><td><b><label for=\"edit-answer[$n]\">".get_string("description", "lesson")." $nplus1:</label></b>\n";
                    if ($flags & LESSON_ANSWER_EDITOR) {
                        echo " [<label for=\"answereditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                            "<input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" checked=\"checked\" />";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                        use_html_editor("answer[$n]"); // switch on the editor
                    } else {
                        echo " [<label for=\"answereditor[$n]\">".get_string("useeditor", "lesson")."</label>: ".
                            "<input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" />";
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
                        choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
                        helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
                        if($lesson->custom)
                            echo get_string("correctanswerscore", "lesson").": <input type=\"text\" name=\"score[$n]\" value=\"$answer->score\" size=\"5\" />";
                        echo "</td></tr>\n";
                    }
                    if ($n == 3) {
                        echo "<tr><td><b>".get_string("wronganswerjump", "lesson").":</b> \n";
                        choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
                        helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
                        if($lesson->custom)
                            echo get_string("wronganswerscore", "lesson").": <input type=\"text\" name=\"score[$n]\" value=\"$answer->score\" size=\"5\" />";
                        echo "</td></tr>\n";
                    }
                    //echo "</td></tr>\n";
                    break;
                case LESSON_ESSAY :
                    echo "<tr><td><b>".get_string("jump", "lesson").":</b> \n";
                    choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
                    helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
                    if($lesson->custom) {
                        echo get_string("score", "lesson").": <input type=\"text\" name=\"score[$n]\" value=\"$answer->score\" size=\"5\" />";
                    }
                    echo "</td></tr>\n";
                    break;
                case LESSON_TRUEFALSE:
                case LESSON_MULTICHOICE:
                case LESSON_SHORTANSWER:
                case LESSON_NUMERICAL:
                    echo "<tr><td><b>".get_string("jump", "lesson")." $nplus1:</b> \n";
                    choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
                    helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
                    if($lesson->custom) {
                        echo get_string("score", "lesson")." $nplus1: <input type=\"text\" name=\"score[$n]\" value=\"$answer->score\" size=\"5\" />";
                    }
                    echo "</td></tr>\n";
                    break;
                case LESSON_BRANCHTABLE:
                case LESSON_CLUSTER:
                case LESSON_ENDOFCLUSTER:
                case LESSON_ENDOFBRANCH:
                    echo "<tr><td><b>".get_string("jump", "lesson")." $nplus1:</b> \n";
                    choose_from_menu($jump, "jumpto[$n]", $answer->jumpto, "");
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
    if ($page->qtype != LESSON_ENDOFBRANCH && $page->qtype != LESSON_CLUSTER && $page->qtype != LESSON_ENDOFCLUSTER) {
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
            echo "<input type=\"hidden\" name=\"answerid[$i]\" value=\"0\" />\n";
            switch ($page->qtype) {
                case LESSON_MATCHING:
                    $icorrected = $i - 1;
                    echo "<tr><td><b>".get_string("answer", "lesson")." $icorrected:</b>\n";
                    echo " [".get_string("useeditor", "lesson").": ".
                        "<input type=\"checkbox\" name=\"answereditor[$i]\" value=\"1\" />";
                    helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                    echo "]<br />\n";
                    print_textarea(false, 10, 70, 630, 300, "answer[$i]");
                    echo "</td></tr>\n";
                    echo "<tr><td><b>".get_string("matchesanswer", "lesson")." $icorrected:</b>\n";
                    echo " [".get_string("useeditor", "lesson").": ".
                        "<input type=\"checkbox\" name=\"responseeditor[$i]\" value=\"1\" />";
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
                    if ($page->qtype != LESSON_SHORTANSWER and $page->qtype != LESSON_NUMERICAL) {
                        echo " [".get_string("useeditor", "lesson").": ".
                            "<input type=\"checkbox\" name=\"answereditor[$i]\" value=\"1\" />";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea(false, 10, 70, 630, 300, "answer[$i]");
                    } else {
                        echo "<br />\n";
                        print_textarea(false, 1, 70, 630, 300, "answer[$i]");
                    }
                    echo "</td></tr>\n";
                    echo "<tr><td><b>".get_string("response", "lesson")." $iplus1:</b>\n";
                    echo " [".get_string("useeditor", "lesson").": ".
                        "<input type=\"checkbox\" name=\"responseeditor[$i]\" value=\"1\" />";
                    helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                    echo "]<br />\n";
                    print_textarea(false, 10, 70, 630, 300, "response[$i]");
                    echo "</td></tr>\n";
                    break;
                case LESSON_BRANCHTABLE:
                    echo "<tr><td><b>".get_string("description", "lesson")." $iplus1:</b>\n";
                    echo " [".get_string("useeditor", "lesson").": ".
                        "<input type=\"checkbox\" name=\"answereditor[$i]\" value=\"1\" />";
                    helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                    echo "]<br />\n";
                    print_textarea(false, 10, 70, 630, 300, "answer[$i]");
                    echo "</td></tr>\n";
                    break;
            }
            switch ($page->qtype) {
                case LESSON_ESSAY :
                    if ($i < 1) {
                        echo "<tr><td><b>".get_string("jump", "lesson").":</b> \n";
                        choose_from_menu($jump, "jumpto[$i]", 0, "");
                        helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
                        if($lesson->custom) {
                            echo get_string("score", "lesson").": <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\" />";
                        }
                        echo "</td></tr>\n";
                    }
                    break;
                case LESSON_MATCHING :
                    if ($i == 2) {
                        echo "<tr><td><b>".get_string("correctanswerjump", "lesson").":</b> \n";
                        choose_from_menu($jump, "jumpto[$i]", $answer->jumpto, "");
                        helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
                        if ($lesson->custom) {
                            echo get_string("correctanswerscore", "lesson").": <input type=\"text\" name=\"score[$i]\" value=\"$answer->score\" size=\"5\" />";
                        }
                        echo "</td></tr>\n";
                    }
                    if ($i == 3) {
                        echo "<tr><td><b>".get_string("wronganswerjump", "lesson").":</b> \n";
                        choose_from_menu($jump, "jumpto[$i]", $answer->jumpto, "");
                        helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
                        if ($lesson->custom) {
                            echo get_string("wronganswerscore", "lesson").": <input type=\"text\" name=\"score[$i]\" value=\"$answer->score\" size=\"5\" />";
                        }
                        echo "</td></tr>\n";
                    }

                    break;
                case LESSON_TRUEFALSE:
                case LESSON_MULTICHOICE:
                case LESSON_SHORTANSWER:
                case LESSON_NUMERICAL:
                    echo "<tr><td><b>".get_string("jump", "lesson")." $iplus1:</b> \n";
                    choose_from_menu($jump, "jumpto[$i]", 0, "");
                    helpbutton("jumpto", get_string("jump", "lesson"), "lesson");
                    if($lesson->custom) {
                        echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"0\" size=\"5\" />";
                    }
                    echo "</td></tr>\n";
                    break;
                case LESSON_BRANCHTABLE :
                    echo "<tr><td><b>".get_string("jump", "lesson")." $iplus1:</b> \n";
                    choose_from_menu($jump, "jumpto[$i]", 0, "");
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
        onclick="getElementById('editpage').redisplay.value=1;getElementById('editpage').submit();" />
    <input type="submit" value="<?php  print_string("savepage", "lesson") ?>" />
    <input type="submit" name="cancel" value="<?php  print_string("cancel") ?>" />
    </center>
    </fieldset>
    </form>
