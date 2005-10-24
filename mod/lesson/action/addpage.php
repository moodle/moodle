<?php

/************** add page ************************************/

    if (!isteacher($course->id)) {
        error("Only teachers can look at this page");
    }

    // first get the preceeding page
    $pageid = required_param('pageid', PARAM_INT);
    
    // set of jump array
    $jump = array();
    $jump[0] = get_string("thispage", "lesson");
    $jump[LESSON_NEXTPAGE] = get_string("nextpage", "lesson");
    $jump[LESSON_PREVIOUSPAGE] = get_string("previouspage", "lesson");
    if(lesson_display_branch_jumps($lesson->id, $pageid)) {
        $jump[LESSON_UNSEENBRANCHPAGE] = get_string("unseenpageinbranch", "lesson");
        $jump[LESSON_RANDOMPAGE] = get_string("randompageinbranch", "lesson");
    }
    if(lesson_display_cluster_jump($lesson->id, $pageid)) {
        $jump[LESSON_CLUSTERJUMP] = get_string("clusterjump", "lesson");
    }
    if (!isset($_GET['firstpage'])) {
        $linkadd = "";      
        $jump[LESSON_EOL] = get_string("endoflesson", "lesson");
        if (!$apageid = get_field("lesson_pages", "id", "lessonid", $lesson->id, "prevpageid", 0)) {
            error("Add page: first page not found");
        }
        while (true) {
            if ($apageid) {
                $title = get_field("lesson_pages", "title", "id", $apageid);
                $jump[$apageid] = strip_tags(format_string($title,true));
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
    <input type="hidden" name="pageid" value="<?PHP echo $pageid ?>">
    <input type="hidden" name="sesskey" value="<?PHP echo $USER->sesskey ?>">
    <center>
      <?php
        echo '<b>'.get_string("questiontype", "lesson").":</b> \n";
        echo helpbutton("questiontypes", get_string("questiontype", "lesson"), "lesson")."<br>";
        if (isset($_GET['qtype'])) {
            $qtype = clean_param($_GET['qtype'], PARAM_INT);
            lesson_qtype_menu($LESSON_QUESTION_TYPE, $qtype, 
                              "lesson.php?id=$cm->id&action=addpage&pageid=".$pageid.$linkadd);
            // NoticeFix rearraged
            if ( $qtype == LESSON_SHORTANSWER || $qtype == LESSON_MULTICHOICE ) {  // only display this option for Multichoice and shortanswer
                echo '<p>';
                if ($qtype == LESSON_SHORTANSWER) {
                    echo "<b>".get_string("casesensitive", "lesson").":</b> \n";
                } else {
                    echo "<b>".get_string("multianswer", "lesson").":</b> \n";
                }
                echo " <label for=\"qoption\" class=\"hidden-label\">Question Option</label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\"/>";
                helpbutton("questionoption", get_string("questionoption", "lesson"), "lesson");
                echo '</p>';
            }
        } else {
            lesson_qtype_menu($LESSON_QUESTION_TYPE, LESSON_MULTICHOICE, 
                              "lesson.php?id=$cm->id&action=addpage&pageid=".$pageid.$linkadd);
            echo "<br><br><b>".get_string("multianswer", "lesson").":</b> \n";
            echo " <label for=\"qoption\" class=\"hidden-label\">Question Option</label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\"/>";
            helpbutton("questionoption", get_string("questionoption", "lesson"), "lesson");
        }
    ?>
    <table cellpadding="5" class="generalbox" border="1">
    <tr valign="top">
    <td><b><?php print_string("pagetitle", "lesson"); ?>:</b><br />
    <!-- hidden-label added --><label for="title" class="hidden-label">Title</label><input type="text" id="title" name="title" size="80" maxsize="255" value=""></td></tr>
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
                    if($lesson->custom) {
                        if ($i) {
                            echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"0\" size=\"5\">";
                        } else {
                            echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\">";
                        }
                    }
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
                            echo get_string("wronganswerscore", "lesson").": <input type=\"text\" name=\"score[$i]\" value=\"0\" size=\"5\">";
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
                    if($lesson->custom) {
                        if ($i) {
                            echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"0\" size=\"5\">";
                        } else {
                            echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\">";
                        }
                    }
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
            if($lesson->custom) {
                if ($i) {
                    echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"0\" size=\"5\">";
                } else {
                    echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\">";
                }
            }
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
