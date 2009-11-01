<?php
/**
 *  Action for adding a branch table.  Prints an HTML form.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package lesson
 **/

    // first get the preceeding page
    $pageid = required_param('pageid', PARAM_INT);

    // set of jump array
    $jump = array();
    $jump[0] = get_string("thispage", "lesson");
    $jump[LESSON_NEXTPAGE] = get_string("nextpage", "lesson");
    $jump[LESSON_PREVIOUSPAGE] = get_string("previouspage", "lesson");
    $jump[LESSON_EOL] = get_string("endoflesson", "lesson");
    if (!optional_param('firstpage', 0, PARAM_INT)) {
        if (!$apageid = $DB->get_field("lesson_pages", "id", array("lessonid" => $lesson->id, "prevpageid" => 0))) {
            print_error('cannotfindfirstpage', 'lesson');
        }
        while (true) {
            if ($apageid) {
                $title = $DB->get_field("lesson_pages", "title", array("id" => $apageid));
                $jump[$apageid] = $title;
                $apageid = $DB->get_field("lesson_pages", "nextpageid", array("id" => $apageid));
            } else {
                // last page reached
                break;
            }
        }
     }
    // give teacher a blank proforma
    $helpicon = new moodle_help_icon();
    $helpicon->text = get_string("addabranchtable", "lesson");
    $helpicon->page = "overview";
    $helpicon->module = "lesson";
    echo $OUTPUT->heading_with_help($helpicon);

    ?>
    <form id="form" method="post" action="lesson.php" class="addform">
    <fieldset class="invisiblefieldset fieldsetfix">
    <input type="hidden" name="id" value="<?PHP echo $cm->id ?>" />
    <input type="hidden" name="action" value="insertpage" />
    <input type="hidden" name="pageid" value="<?PHP echo $pageid ?>" />
    <input type="hidden" name="qtype" value="<?PHP echo LESSON_BRANCHTABLE ?>" />
    <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />
    <table class="generalbox boxaligncenter" cellpadding="5" border="1">
    <tr valign="top">
    <td><strong><label for="title"><?php print_string("pagetitle", "lesson"); ?>:</label></strong><br />
    <input type="text" id="title" name="title" size="80" value="" /></td></tr>
    <?php
    echo "<tr><td><strong>";
    echo get_string("pagecontents", "lesson").":</strong><br />\n";
    print_textarea($usehtmleditor, 25,70, 0, 0, "contents");
    echo "</td></tr>\n";
    echo "<tr><td>\n";
    echo "<div class=\"boxaligncenter addform\"><input name=\"layout\" type=\"checkbox\" value=\"1\" checked=\"checked\" />";
    echo get_string("arrangebuttonshorizontally", "lesson")."\n";
    echo "<br /><input name=\"display\" type=\"checkbox\" value=\"1\" checked=\"checked\" />";
    echo get_string("displayinleftmenu", "lesson");
    echo "</div>\n";
    echo "</td></tr>\n";
    for ($i = 0; $i < $lesson->maxanswers; $i++) {
        $iplus1 = $i + 1;
        echo "<tr><td><strong>".get_string("description", "lesson")." $iplus1:</strong><br />\n";
        print_textarea(false, 10, 70, 630, 300, "answer[$i]");
        echo "</td></tr>\n";
        echo "<tr><td><strong>".get_string("jump", "lesson")." $iplus1:</strong> \n";
        if ($i) {
            // answers 2, 3, 4... jumpto this page
            echo $OUTPUT->select(html_select::make($jump, "jumpto[$i]", 0, false));
        } else {
            // answer 1 jumpto next page
            echo $OUTPUT->select(html_select::make($jump, "jumpto[$i]", LESSON_NEXTPAGE, false));
        }
        echo $OUTPUT->help_icon(moodle_help_icon::make("jumpto", get_string("jump", "lesson"), "lesson"));
        echo "</td></tr>\n";
    }
    // close table and form
    ?>
    </table><br />
    <input type="submit" value="<?php  print_string("addabranchtable", "lesson") ?>" />
    <input type="submit" name="cancel" value="<?php  print_string("cancel") ?>" />
    </fieldset>
    </form>
