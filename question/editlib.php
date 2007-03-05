<?php // $Id$
/**
* Functions used by showbank.php to show question editing interface
*
* TODO: currently the function question_list still provides controls specific
*       to the quiz module. This needs to be generalised.
*
* @version $Id$
* @author Martin Dougiamas and many others. This has recently been extensively
*         rewritten by members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package question
*/

require_once($CFG->libdir.'/questionlib.php');

define('DEFAULT_QUESTIONS_PER_PAGE', 20);

/**
* Function to read all questions for category into big array
*
* @param int $category category number
* @param bool $noparent if true only questions with NO parent will be selected
* @param bool $recurse include subdirectories
* @author added by Howard Miller June 2004
*/
function get_questions_category( $category, $noparent=false, $recurse=true ) {

    global $QTYPES;

    // questions will be added to an array
    $qresults = array();

    // build sql bit for $noparent
    $npsql = '';
    if ($noparent) {
      $npsql = " and parent='0' ";
    }

    // get (list) of categories
    if ($recurse) {
        $categorylist = question_categorylist( $category->id );
    }
    else {
        $categorylist = $category->id;
    }

    // get the list of questions for the category
    if ($questions = get_records_select("question","category IN ($categorylist) $npsql", "qtype, name ASC")) {

        // iterate through questions, getting stuff we need
        foreach($questions as $question) {
            $questiontype = $QTYPES[$question->qtype];
            $questiontype->get_question_options( $question );
            $qresults[] = $question;
        }
    }

    return $qresults;
}

/**
* Gets the default category in a course
*
* It returns the first category with no parent category. If no categories
* exist yet then one is created.
* @return object The default category
* @param integer $courseid  The id of the course whose default category is wanted
*/
function get_default_question_category($courseid) {
    // If it already exists, just return it.
    if ($category = get_records_select("question_categories", "course = '$courseid' AND parent = '0'", 'id', '*', '', 1)) {
        return reset($category);
    }

    // Otherwise, we need to make one
    $category = new stdClass;
    $category->name = get_string("default", "quiz");
    $category->info = get_string("defaultinfo", "quiz");
    $category->course = $courseid;
    $category->parent = 0;
    $category->sortorder = 999; // By default, all categories get this number, and are sorted alphabetically.
    $category->publish = 0;
    $category->stamp = make_unique_id_code();

    if (!$category->id = insert_record("question_categories", $category)) {
        notify("Error creating a default category!");
        return false;
    }
    return $category;
}

/**
 * prints a form to choose categories
 */
function question_category_form($course, $current, $recurse=1, $showhidden=false, $showquestiontext=false) {
    global $CFG;

/// Make sure the default category exists for this course
    get_default_question_category($course->id);

/// Get all the existing categories now
    $catmenu = question_category_options($course->id, true);

    $strcategory = get_string("category", "quiz");
    $strshow = get_string("show", "quiz");
    $streditcats = get_string("editcategories", "quiz");

    echo "<table><tr><td style=\"white-space:nowrap;\">";
    echo "<strong>$strcategory:</strong>&nbsp;";
    echo "</td><td>";
    popup_form ("edit.php?courseid=$course->id&amp;cat=", $catmenu, "catmenu", $current, "", "", "", false, "self");
    echo "</td><td align=\"right\">";
    echo "<form method=\"get\" action=\"$CFG->wwwroot/question/category.php\">";
    echo "<div>";
    echo "<input type=\"hidden\" name=\"id\" value=\"$course->id\" />";
    echo "<input type=\"submit\" value=\"$streditcats\" />";
    echo '</div>';
    echo "</form>";
    echo '</td></tr></table>';

    echo '<form method="post" action="edit.php" id="displayoptions">';
    echo "<fieldset class='invisiblefieldset'>";
    echo "<input type=\"hidden\" name=\"courseid\" value=\"{$course->id}\" />\n";
    question_category_form_checkbox('recurse', $recurse);
    question_category_form_checkbox('showhidden', $showhidden);
    question_category_form_checkbox('showquestiontext', $showquestiontext);
    echo '<noscript><div class="centerpara"><input type="submit" value="'. get_string('go') .'" />';
    echo '</div></noscript></fieldset></form>';
}

/**
 * Private funciton to help the preceeding function.
 */
function question_category_form_checkbox($name, $checked) {
    echo '<div><input type="hidden" id="' . $name . '_off" name="' . $name . '" value="0" />';
    echo '<input type="checkbox" id="' . $name . '_on" name="' . $name . '" value="1"';
    if ($checked) {
        echo ' checked="checked"';
    }
    echo ' onchange="getElementById(\'displayoptions\').submit(); return true;" />';
    echo '<label for="' . $name . '_on">';
    print_string($name, 'quiz');
    echo "</label></div>\n";
}

/**
* Prints the table of questions in a category with interactions
*
* @param object $course   The course object
* @param int $categoryid  The id of the question category to be displayed
* @param int $quizid      The quiz id if we are in the context of a particular quiz, 0 otherwise
* @param int $recurse     This is 1 if subcategories should be included, 0 otherwise
* @param int $page        The number of the page to be displayed
* @param int $perpage     Number of questions to show per page
* @param boolean $showhidden   True if also hidden questions should be displayed
*/
function question_list($course, $categoryid, $quizid=0,
        $recurse=1, $page=0, $perpage=100, $showhidden=false, $sortorder='qtype, name ASC',
        $showquestiontext = false) {
    global $QTYPE_MENU, $USER, $CFG, $THEME;
    
    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    $qtypemenu = $QTYPE_MENU;
    if ($rqp_types = get_records('question_rqp_types')) {
        foreach($rqp_types as $type) {
            $qtypemenu['rqp_'.$type->id] = $type->name;
        }
    }

    $strcategory = get_string("category", "quiz");
    $strquestion = get_string("question", "quiz");
    $straddquestions = get_string("addquestions", "quiz");
    $strimportquestions = get_string("importquestions", "quiz");
    $strexportquestions = get_string("exportquestions", "quiz");
    $strnoquestions = get_string("noquestions", "quiz");
    $strselect = get_string("select", "quiz");
    $strselectall = get_string("selectall", "quiz");
    $strselectnone = get_string("selectnone", "quiz");
    $strcreatenewquestion = get_string("createnewquestion", "quiz");
    $strquestionname = get_string("questionname", "quiz");
    $strdelete = get_string("delete");
    $stredit = get_string("edit");
    $straction = get_string("action");
    $strrestore = get_string('restore');

    $straddtoquiz = get_string("addtoquiz", "quiz");
    $strtype = get_string("type", "quiz");
    $strcreatemultiple = get_string("createmultiple", "quiz");
    $strpreview = get_string("preview","quiz");

    if (!$categoryid) {
        echo "<p style=\"text-align:center;\"><b>";
        print_string("selectcategoryabove", "quiz");
        echo "</b></p>";
        if ($quizid) {
            echo "<p>";
            print_string("addingquestions", "quiz");
            echo "</p>";
        }
        return;
    }

    if (!$category = get_record("question_categories", "id", "$categoryid")) {
        notify("Category not found!");
        return;
    }
    echo '<div class="boxaligncenter">';
    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    echo format_text($category->info, FORMAT_MOODLE, $formatoptions, $course->id);

    echo '<table><tr>';

    // check if editing of this category is allowed
    if (has_capability('moodle/question:managecategory', $context)) {
        echo "<td valign=\"top\"><b>$strcreatenewquestion:</b></td>";
        echo '<td valign="top" align="right">';
        popup_form ("$CFG->wwwroot/question/question.php?category=$category->id&amp;qtype=", $qtypemenu, "addquestion",
                    "", "choose", "", "", false, "self");
        echo '</td><td valign="top" align="right">';
        helpbutton("questiontypes", $strcreatenewquestion, "quiz");
        echo '</td></tr>';
    }
    else {
        echo '<tr><td>';
        print_string("publishedit","quiz");
        echo '</td></tr>';
    }

    echo '</table>';
    echo '</div>';

    $categorylist = ($recurse) ? question_categorylist($category->id) : $category->id;

    // hide-feature
    $showhidden = $showhidden ? '' : " AND hidden = '0'";

    if (!$totalnumber = count_records_select('question', "category IN ($categorylist) AND parent = '0' $showhidden")) {
        echo "<p style=\"text-align:center;\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return;
    }

    if (!$questions = get_records_select('question', "category IN ($categorylist) AND parent = '0' $showhidden", $sortorder, '*', $page*$perpage, $perpage)) {
        // There are no questions on the requested page.
        $page = 0;
        if (!$questions = get_records_select('question', "category IN ($categorylist) AND parent = '0' $showhidden", $sortorder, '*', 0, $perpage)) {
            // There are no questions at all
            echo "<p style=\"text-align:center;\">";
            print_string("noquestions", "quiz");
            echo "</p>";
            return;
        }
    }

    print_paging_bar($totalnumber, $page, $perpage,
                "edit.php?courseid={$course->id}&amp;perpage=$perpage&amp;");

    $canedit = has_capability('moodle/question:manage', $context);

    echo '<form method="post" action="edit.php?courseid='.$course->id.'">';
    echo '<fieldset class="invisiblefieldset" style="display: block;">';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';

    echo '<table id="categoryquestions" style="width: 100%"><tr>';
    echo "<th style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$straction</th>";
    
    $sortoptions = array('name, qtype ASC' => get_string("sortalpha", "quiz"),
                         'qtype, name ASC' => get_string("sorttypealpha", "quiz"),
                         'id ASC' => get_string("sortage", "quiz"));
    $orderselect  = choose_from_menu ($sortoptions, 'sortorder', $sortorder, false, 'this.form.submit();', '0', true);
    $orderselect .= '<noscript><div><input type="submit" value="'.get_string("sortsubmit", "quiz").'" /></div></noscript>';
    echo "<th style=\"white-space:nowrap; text-align: left;\" class=\"header\" scope=\"col\">$strquestionname $orderselect</th>
    <th style=\"white-space:nowrap; text-align: right;\" class=\"header\" scope=\"col\">$strtype</th>";
    echo "</tr>\n";
    foreach ($questions as $question) {
        $nameclass = '';
        $textclass = '';
        if ($question->hidden) {
            $nameclass = 'dimmed_text';
            $textclass = 'dimmed_text';
        }
        if ($showquestiontext) {
            $nameclass .= ' header';
        }
        if ($nameclass) {
            $nameclass = 'class="' . $nameclass . '"';
        }
        if ($textclass) {
            $textclass = 'class="' . $textclass . '"';
        }
        
        echo "<tr>\n<td style=\"white-space:nowrap;\" $nameclass>\n";
        
        // add to quiz
        if ($quizid && has_capability('mod/quiz:manage', $context)) {
            echo "<a title=\"$straddtoquiz\" href=\"edit.php?addquestion=$question->id&amp;quizid=$quizid&amp;sesskey=$USER->sesskey\"><img
                  src=\"$CFG->pixpath/t/moveleft.gif\" alt=\"$straddtoquiz\" /></a>&nbsp;";
        }
        
        // preview
        echo "<a title=\"$strpreview\" href=\"javascript:void();\" onclick=\"openpopup('/question/preview.php?id=$question->id&amp;quizid=$quizid','$strpreview', " .
                QUESTION_PREVIEW_POPUP_OPTIONS . ", false)\"><img
                src=\"$CFG->pixpath/t/preview.gif\" alt=\"$strpreview\" /></a>&nbsp;";
        
        // edit, hide, delete question, using question capabilities, not quiz capabilieies
        if (has_capability('moodle/question:manage', $context)) {
            echo "<a title=\"$stredit\" href=\"$CFG->wwwroot/question/question.php?id=$question->id\"><img
                    src=\"$CFG->pixpath/t/edit.gif\" alt=\"$stredit\" /></a>&nbsp;";
            // hide-feature
            if($question->hidden) {
                echo "<a title=\"$strrestore\" href=\"edit.php?courseid=$course->id&amp;unhide=$question->id&amp;sesskey=$USER->sesskey\"><img
                        src=\"$CFG->pixpath/t/restore.gif\" alt=\"$strrestore\" /></a>";
            } else {
                echo "<a title=\"$strdelete\" href=\"edit.php?courseid=$course->id&amp;deleteselected=$question->id&amp;q$question->id=1\"><img
                        src=\"$CFG->pixpath/t/delete.gif\" alt=\"$strdelete\" /></a>";
            }
        }
        echo "&nbsp;<input title=\"$strselect\" type=\"checkbox\" name=\"q$question->id\" value=\"1\" />";
        echo "</td>\n";

        echo "<td $nameclass>" . $question->name . "</td>\n";
        echo "<td $nameclass style='text-align: right'>\n";
        print_question_icon($question, $canedit);
        echo "</td>\n";
        echo "</tr>\n";
        if($showquestiontext){
            echo '<tr><td colspan="3" ' . $textclass . '>';
            $formatoptions = new stdClass;
            $formatoptions->noclean = true;
            $formatoptions->para = false;
            echo format_text($question->questiontext, $question->questiontextformat,
                    $formatoptions, $course->id);
            echo "</td></tr>\n";
        }
    }
    echo "</table>\n";

    $paging = print_paging_bar($totalnumber, $page, $perpage,
            "edit.php?courseid={$course->id}&amp;perpage=$perpage&amp;", 'page',
            false, true);
    if ($totalnumber > DEFAULT_QUESTIONS_PER_PAGE) {
        if ($perpage == DEFAULT_QUESTIONS_PER_PAGE) {
            $showall = '<a href="edit.php?courseid='.$course->id.'&amp;perpage=1000">'.get_string('showall', 'moodle', $totalnumber).'</a>';
        } else {
            $showall = '<a href="edit.php?courseid='.$course->id.'&amp;perpage=' . DEFAULT_QUESTIONS_PER_PAGE . '">'.get_string('showperpage', 'moodle', DEFAULT_QUESTIONS_PER_PAGE).'</a>';
        }
        if ($paging) {
            $paging = substr($paging, 0, strrpos($paging, '</div>'));
            $paging .= "<br />$showall</div>";
        } else {
            $paging = "<div class='paging'>$showall</div>"; 
        }
    }
    echo $paging;

    echo '<table class="quiz-edit-selected"><tr><td colspan="2">';
    echo '<a href="javascript:select_all_in(\'TABLE\', null, \'categoryquestions\');">'.$strselectall.'</a> /'.
     ' <a href="javascript:deselect_all_in(\'TABLE\', null, \'categoryquestions\');">'.$strselectnone.'</a>'.
     '</td><td align="right"><b>&nbsp;'.get_string('withselected', 'quiz').':</b></td></tr><tr><td>';

    if ($quizid && has_capability('mod/quiz:manage', $context)) {
        echo "<input type=\"submit\" name=\"add\" value=\"{$THEME->larrow} $straddtoquiz\" />\n";
        echo '</td><td>';
    }
    // print delete and move selected question
    if (has_capability('moodle/question:manage', $context)) {
        echo '<input type="submit" name="deleteselected" value="'.$strdelete."\" /></td><td>\n";
        echo '<input type="submit" name="move" value="'.get_string('moveto', 'quiz')."\" />\n";
        question_category_select_menu($course->id, false, true, $category->id);
    }
    echo "</td></tr></table>";

    // add random question
    if ($quizid && has_capability('mod/quiz:manage', $context)) {
        for ($i = 1;$i <= min(10, $totalnumber); $i++) {
            $randomcount[$i] = $i;
        }
        for ($i = 20;$i <= min(100, $totalnumber); $i += 10) {
            $randomcount[$i] = $i;
        }
        echo '<br />';
        print_string('addrandom', 'quiz',
         choose_from_menu($randomcount, 'randomcount', '1', '', '', '', true));
        echo '<input type="hidden" name="recurse" value="'.$recurse.'" />';
        echo "<input type=\"hidden\" name=\"categoryid\" value=\"$category->id\" />";
        echo ' <input type="submit" name="addrandom" value="'. get_string('add') .'" />';
        helpbutton('random', get_string('random', 'quiz'), 'quiz');
    }
    echo '</fieldset>';
    echo "</form>\n";
}

?>