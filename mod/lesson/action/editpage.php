<?php

/************** edit page ************************************/

    if (!isteacher($course->id)) {
        error("Only teachers can look at this page");
    }

    // get the page
    $pageid = required_param('pageid', PARAM_INT);
    if (!$page = get_record("lesson_pages", "id", $pageid)) {
        error("Edit page: page record not found");
    }

    if (isset($_GET['qtype'])) {
        $page->qtype = required_param('qtype', PARAM_INT);
    }

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
    <form name="editpage" method="post" action="lesson.php">
    <input type="hidden" name="id" value="<?PHP echo $cm->id ?>">
    <input type="hidden" name="action" value="updatepage">
    <input type="hidden" name="pageid" value="<?PHP echo $pageid ?>">
    <input type="hidden" name="sesskey" value="<?PHP echo $USER->sesskey ?>">        
    <input type="hidden" name="redisplay" value="0">
    <center>
       <?php
        switch ($page->qtype) {
            case LESSON_MULTICHOICE :
                echo '<b>'.get_string("questiontype", "lesson").":</b> \n";
                echo helpbutton("questiontypes", get_string("questiontype", "lesson"), "lesson")."<br>";
                lesson_qtype_menu($LESSON_QUESTION_TYPE, $page->qtype, 
                                  "lesson.php?id=$cm->id&action=editpage&pageid=$page->id",
                                  "document.editpage.redisplay.value=1;document.editpage.submit();");
                echo "<p><b>".get_string("multianswer", "lesson").":</b> \n";
                if ($page->qoption) {
                    echo "<label for=\"qoption\" class=\"hidden-label\">Question Option</label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\" checked=\"checked\"/>";
                } else {
                    echo "<label for=\"qoption\" class=\"hidden-label\">Question Option</label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\"/>";
                }
                helpbutton("questionoption", get_string("questionoption", "lesson"), "lesson");
                echo "</p>\n";
                break;
            case LESSON_SHORTANSWER :
                echo '<b>'.get_string("questiontype", "lesson").":</b> \n";
                echo helpbutton("questiontypes", get_string("questiontype", "lesson"), "lesson")."<br>";
                lesson_qtype_menu($LESSON_QUESTION_TYPE, $page->qtype, 
                                  "lesson.php?id=$cm->id&action=editpage&pageid=$page->id",
                                  "document.editpage.redisplay.value=1;document.editpage.submit();");
                echo "<p><b>".get_string("casesensitive", "lesson").":</b> \n";
                if ($page->qoption) {
                    echo "<label for=\"qoption\" class=\"hidden-label\">Question Option</label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\" checked=\"checked\"/>";
                } else {
                    echo "<label for=\"qoption\" class=\"hidden-label\">Question Option</label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\"/>";
                }
                helpbutton("questionoption", get_string("questionoption", "lesson"), "lesson");
                echo "</p>\n";
                break;
            case LESSON_TRUEFALSE :
            case LESSON_ESSAY :
            case LESSON_MATCHING :
            case LESSON_NUMERICAL :
                echo '<b>'.get_string("questiontype", "lesson").":</b> \n";
                echo helpbutton("questiontypes", get_string("questiontype", "lesson"), "lesson")."<br>";
                lesson_qtype_menu($LESSON_QUESTION_TYPE, $page->qtype, 
                                  "lesson.php?id=$cm->id&action=editpage&pageid=$page->id",
                                  "document.editpage.redisplay.value=1;document.editpage.submit();");
                break;
        }
    ?>
    <table cellpadding="5" class="generalbox" border="1">
    <tr valign="top">
    <td><b><?php print_string("pagetitle", "lesson"); ?>:</b><br />
    <!-- hidden-label added.--><label for="title" class="hidden-label">Title</label><input type="text" id="title" name="title" size="80" maxsize="255" value="<?php p($page->title) ?>"></td>
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
            echo "<tr><td>\n";
            echo "<center>";
            if ($page->layout) {
                echo "<input checked=\"checked\" name=\"layout\" type=\"checkbox\" value=\"1\">";
            } else {
                echo "<input name=\"layout\" type=\"checkbox\" value=\"1\">";
            }
            echo get_string("arrangebuttonshorizontally", "lesson")."<center>\n";
            echo "<br>";
            if ($page->display) {
                echo "<center><input name=\"display\" type=\"checkbox\" value=\"1\" checked=\"checked\">";
            } else {
                echo "<center><input name=\"display\" type=\"checkbox\" value=\"1\">";
            }                
            echo get_string("displayinleftmenu", "lesson")."<center>\n";
            echo "</td></tr>\n";
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
                                checked=\"checked\">";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                            use_html_editor("answer[$n]"); // switch on the editor
                        } else {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\">";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
                        }
                    } elseif ($n == 1) {
                        echo "<tr><td><b>".get_string("wrongresponse", "lesson").":</b>\n";
                        if ($flags & LESSON_ANSWER_EDITOR) {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\"answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\" 
                                checked=\"checked\">";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                            use_html_editor("answer[$n]"); // switch on the editor
                        } else {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\">";
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
                                checked=\"checked\">"; 
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                            use_html_editor("answer[$n]"); // switch on the editor
                        } else {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\">";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
                        }
                        echo "</td></tr>\n";
                        echo "<tr><td><b>".get_string("matchesanswer", "lesson")." $ncorrected:</b>\n";
                        if ($flags & LESSON_RESPONSE_EDITOR) {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<label for=\"responseeditor[$n]\" class=\"hidden-label\">responseeditor[$n]</label><input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\" 
                                checked=\"checked\">";
                            helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                            echo "]<br />\n";
                            print_textarea($usehtmleditor, 20, 70, 630, 300, "response[$n]", $answer->response);
                            use_html_editor("response[$n]"); // switch on the editor
                        } else {
                            echo " [".get_string("useeditor", "lesson").": ".
                                "<label for=\"responseeditor[$n]\" class=\"hidden-label\">responseeditor[$n]</label><input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\">";
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
                            checked=\"checked\">";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea($usehtmleditor, 20, 70, 630, 300, "answer[$n]", $answer->answer);
                        use_html_editor("answer[$n]"); // switch on the editor
                    } else {
                        echo " [".get_string("useeditor", "lesson").": ".
                            "<label for=\"answereditor[$n]\" class=\"hidden-label\">answereditor[$n]</label><input type=\"checkbox\" id=\answereditor[$n]\" name=\"answereditor[$n]\" value=\"1\">";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea(false, 6, 70, 630, 300, "answer[$n]", $answer->answer);
                    }
                    echo "</td></tr>\n";
                    echo "<tr><td><b>".get_string("response", "lesson")." $nplus1:</b>\n";
                    if ($flags & LESSON_RESPONSE_EDITOR) {
                        echo " [".get_string("useeditor", "lesson").": ".
                            "<label for=\"responseeditor[$n]\" class=\"hidden-label\">responseeditor[$n]</label><input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\" 
                            checked=\"checked\">";
                        helpbutton("useeditor", get_string("useeditor", "lesson"), "lesson");
                        echo "]<br />\n";
                        print_textarea($usehtmleditor, 20, 70, 630, 300, "response[$n]", $answer->response);
                        use_html_editor("response[$n]"); // switch on the editor
                    } else {
                        echo " [".get_string("useeditor", "lesson").": ".
                            "<label for=\"responseeditor[$n]\" class=\"hidden-label\">responseeditor[$n]</label><input type=\"checkbox\" id=\"responseeditor[$n]\" name=\"responseeditor[$n]\" value=\"1\">";
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
                        echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"0\" size=\"5\">";
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
        onClick="document.editpage.redisplay.value=1;document.editpage.submit();" />
    <input type="submit" value="<?php  print_string("savepage", "lesson") ?>" />
    <input type="submit" name="cancel" value="<?php  print_string("cancel") ?>" />
    </center>
    </form>
