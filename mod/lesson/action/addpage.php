<?php
/**
 *  Action for adding a question page.  Prints an HTML form.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/
    // first get the preceeding page
    $pageid = required_param('pageid', PARAM_INT);
    $qtype = optional_param('qtype', LESSON_MULTICHOICE, PARAM_INT);

    // set of jump array
    $jump = array();
    $jump[0] = get_string("thispage", "lesson");
    $jump[LESSON_NEXTPAGE] = get_string("nextpage", "lesson");
    $jump[LESSON_PREVIOUSPAGE] = get_string("previouspage", "lesson");
    $jump[LESSON_EOL] = get_string("endoflesson", "lesson");
    if(lesson_display_branch_jumps($lesson->id, $pageid)) {
        $jump[LESSON_UNSEENBRANCHPAGE] = get_string("unseenpageinbranch", "lesson");
        $jump[LESSON_RANDOMPAGE] = get_string("randompageinbranch", "lesson");
    }
    if(lesson_display_cluster_jump($lesson->id, $pageid)) {
        $jump[LESSON_CLUSTERJUMP] = get_string("clusterjump", "lesson");
    }
    if (!optional_param('firstpage', 0, PARAM_INT)) {
        $linkadd = "";
        $apageid = $DB->get_field("lesson_pages", "id", array("lessonid" => $lesson->id, "prevpageid" => 0));

        while (true) {
            if ($apageid) {
                $title = $DB->get_field("lesson_pages", "title", array("id" => $apageid));
                $jump[$apageid] = strip_tags(format_string($title,true));
                $apageid = $DB->get_field("lesson_pages", "nextpageid", array("id" => $apageid));
            } else {
                // last page reached
                break;
            }
        }
    } else {
        $linkadd = "&amp;firstpage=1";
    }

    // give teacher a blank proforma
    $helpicon = new moodle_help_icon();
    $helpicon->text = get_string("addaquestionpage", "lesson");
    $helpicon->page = "overview";
    $helpicon->module = "lesson";
    echo $OUTPUT->heading_with_help($helpicon);

    ?>
    <form id="form" method="post" action="lesson.php" class="addform">
    <fieldset class="invisiblefieldset fieldsetfix">
    <input type="hidden" name="id" value="<?php echo $cm->id ?>" />
    <input type="hidden" name="action" value="insertpage" />
    <input type="hidden" name="pageid" value="<?php echo $pageid ?>" />
    <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
      <?php
        echo '<b>'.get_string("questiontype", "lesson").":</b> \n";
        echo $OUTPUT->help_icon(moodle_help_icon::make("questiontypes", get_string("questiontype", "lesson"), "lesson"))."<br />";
        lesson_qtype_menu($LESSON_QUESTION_TYPE, $qtype,
                          "lesson.php?id=$cm->id&amp;action=addpage&amp;pageid=".$pageid.$linkadd);

        if ( $qtype == LESSON_SHORTANSWER || $qtype == LESSON_MULTICHOICE ) {  // only display this option for Multichoice and shortanswer
            echo '<p>';
            if ($qtype == LESSON_SHORTANSWER) {
                $qoptionstr = get_string('casesensitive', 'lesson');
            } else {
                $qoptionstr = get_string('multianswer', 'lesson');
            }
            echo "<label for=\"qoption\"><strong>$qoptionstr</strong></label><input type=\"checkbox\" id=\"qoption\" name=\"qoption\" value=\"1\"/>";
            echo $OUTPUT->help_icon(moodle_help_icon::make("questionoption", get_string("questionoption", "lesson"), "lesson"));
            echo '</p>';
        }
    ?>
    <table cellpadding="5" class="generalbox boxaligncenter" border="1">
    <tr valign="top">
    <td><b><label for="title"><?php print_string("pagetitle", "lesson"); ?>:</label></b><br />
    <input type="text" id="title" name="title" size="80" value="" /></td></tr>
    <?php
    echo "<tr><td><b>";
    echo get_string("pagecontents", "lesson").":</b><br />\n";
    print_textarea($usehtmleditor, 25,70, 630, 400, "contents");
    echo "</td></tr>\n";
    switch ($qtype) {
        case LESSON_TRUEFALSE :
            for ($i = 0; $i < 2; $i++) {
                $iplus1 = $i + 1;
                echo "<tr><td><b>".get_string("answer", "lesson")." $iplus1:</b><br />\n";
                print_textarea(false, 6, 70, 630, 300, "answer[$i]");
                echo "</td></tr>\n";
                echo "<tr><td><b>".get_string("response", "lesson")." $iplus1:</b><br />\n";
                print_textarea(false, 6, 70, 630, 300, "response[$i]");
                echo "</td></tr>\n";
                echo "<tr><td><b>".get_string("jump", "lesson")." $iplus1:</b> \n";
                if ($i) {
                    // answers 2, 3, 4... jumpto this page
                    echo $OUTPUT->select(html_select::make($jump, "jumpto[$i]", 0, false));
                } else {
                    // answer 1 jumpto next page
                    echo $OUTPUT->select(html_select::make($jump, "jumpto[$i]", LESSON_NEXTPAGE, false));
                }
                echo $OUTPUT->help_icon(moodle_help_icon::make("jumpto", get_string("jump", "lesson"), "lesson"));
                if($lesson->custom) {
                    if ($i) {
                        echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"0\" size=\"5\" />";
                    } else {
                        echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\" />";
                    }
                }
                echo "</td></tr>\n";
            }
            break;
        case LESSON_ESSAY :
                echo "<tr><td><b>".get_string("jump", "lesson").":</b> \n";
                echo $OUTPUT->select(html_select::make($jump, "jumpto[0]", LESSON_NEXTPAGE, false));
                echo $OUTPUT->help_icon(moodle_help_icon::make("jumpto", get_string("jump", "lesson"), "lesson"));
                if ($lesson->custom) {
                    echo get_string("score", "lesson").": <input type=\"text\" name=\"score[0]\" value=\"1\" size=\"5\" />";
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
                    echo "<tr><td><b>".get_string("correctanswerjump", "lesson").":</b> \n";
                    echo $OUTPUT->select(html_select::make($jump, "jumpto[$i]", LESSON_NEXTPAGE, false));
                    echo $OUTPUT->help_icon(moodle_help_icon::make("jumpto", get_string("jump", "lesson"), "lesson"));
                    if($lesson->custom) {
                        echo get_string("correctanswerscore", "lesson").": <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\" />";
                    }
                    echo "</td></tr>\n";
                } elseif ($i == 3) {
                    echo "<tr><td><b>".get_string("wronganswerjump", "lesson").":</b> \n";
                    echo $OUTPUT->select(html_select::make($jump, "jumpto[$i]", 0, false));
                    echo $OUTPUT->help_icon(moodle_help_icon::make("jumpto", get_string("jump", "lesson"), "lesson"));
                    if($lesson->custom) {
                        echo get_string("wronganswerscore", "lesson").": <input type=\"text\" name=\"score[$i]\" value=\"0\" size=\"5\" />";
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
                echo "<tr><td><b>".get_string("jump", "lesson")." $iplus1:</b> \n";
                if ($i) {
                    // answers 2, 3, 4... jumpto this page
                    echo $OUTPUT->select(html_select::make($jump, "jumpto[$i]", 0, false));
                } else {
                    // answer 1 jumpto next page
                    echo $OUTPUT->select(html_select::make($jump, "jumpto[$i]", LESSON_NEXTPAGE, false));
                }
                echo $OUTPUT->help_icon(moodle_help_icon::make("jumpto", get_string("jump", "lesson"), "lesson"));
                if($lesson->custom) {
                    if ($i) {
                        echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"0\" size=\"5\" />";
                    } else {
                        echo get_string("score", "lesson")." $iplus1: <input type=\"text\" name=\"score[$i]\" value=\"1\" size=\"5\" />";
                    }
                }
                echo "</td></tr>\n";
            }
            break;
    }
    // close table and form
    ?>
    </table><br />
    <input type="submit" value="<?php  print_string("addaquestionpage", "lesson") ?>" />
    <input type="submit" name="cancel" value="<?php  print_string("cancel") ?>" />
    </fieldset>
    </form>
