<?php
/************** add branch table ************************************/
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
    // give teacher a blank proforma
    print_heading_with_help(get_string("addabranchtable", "lesson"), "overview", "lesson");
    ?>
    <form name="form" method="post" action="lesson.php" />
    <input type="hidden" name="id" value="<?PHP echo $cm->id ?>" />
    <input type="hidden" name="action" value="insertpage">
    <input type="hidden" name="pageid" value="<?PHP echo $pageid ?>" />
    <input type="hidden" name="qtype" value="<?PHP echo LESSON_BRANCHTABLE ?>" />
    <input type="hidden" name="sesskey" value="<?PHP echo $USER->sesskey ?>" />
    <center><table class="generalbox" cellpadding=5 border=1>
    <tr valign="top">
    <td><b><?php print_string("pagetitle", "lesson"); ?>:</b><br />
    <!-- hidden-label added.--><label for="title" class="hidden-label">Title</label><input type="text" id="title" name="title" size="80" maxsize="255" value="" /></td></tr>
    <?PHP
    echo "<tr><td><b>";
    echo get_string("pagecontents", "lesson").":</b><br />\n";
    print_textarea($usehtmleditor, 25,70, 630, 400, "contents");
    use_html_editor("contents");
    echo "</td></tr>\n";
    echo "<tr><td>\n";
    echo "<center><input name=\"layout\" type=\"checkbox\" value=\"1\" checked=\"checked\" />";
    echo get_string("arrangebuttonshorizontally", "lesson")."\n";
    echo "<br><input name=\"display\" type=\"checkbox\" value=\"1\" checked=\"checked\" />";
    echo get_string("displayinleftmenu", "lesson");
    echo "</center>\n";
    echo "</td></tr>\n";
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
    <input type="submit" value="<?php  print_string("addabranchtable", "lesson") ?>" />
    <input type="submit" name="cancel" value="<?php  print_string("cancel") ?>" />
    </center>
    </form>
