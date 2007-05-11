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
function get_questions_category( $category, $noparent=false, $recurse=true, $export=true ) {

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
            $question->export_process = $export;
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

    if ($categories = get_records_select("question_categories", "course = '$courseid' AND parent = '0'", "id")) {
        foreach ($categories as $category) {
            return $category;   // Return the first one (lowest id)
        }
    }

    // Otherwise, we need to make one
    $category->name = get_string("default", "quiz");
    $category->info = get_string("defaultinfo", "quiz");
    $category->course = $courseid;
    $category->parent = 0;
    // TODO: Figure out why we use 999 below
    $category->sortorder = 999;
    $category->publish = 0;
    $category->stamp = make_unique_id_code();

    if (!$category->id = insert_record("question_categories", $category)) {
        notify("Error creating a default category!");
        return false;
    }
    return $category;
}

/**
 * Return a list of categories nicely formatted
 * @param int courseid id of course 
 * @param bool published true=include all published categories
 * @return array formatted category names
 */
function question_category_menu($courseid, $published=false) {
/// Returns the list of categories
    $publish = "";
    if ($published) {
        $publish = "OR publish = '1'";
    }

    if (!isadmin()) {
        $categories = get_records_select("question_categories", "course = '$courseid' $publish", 'parent, sortorder, name ASC');
    } else {
        $categories = get_records_select("question_categories", '', 'parent, sortorder, name ASC');
    }
    if (!$categories) {
        return false;
    }
    $categories = add_indented_names($categories);

    foreach ($categories as $category) {
       if ($catcourse = get_record("course", "id", $category->course)) {
           if ($category->publish && ($category->course != $courseid)) {
               $category->indentedname .= " ($catcourse->shortname)";
           }
           $catmenu[$category->id] = $category->indentedname;
       }
    }
    return $catmenu;
}

/**
 * prints a form to choose categories
 */
function question_category_form($course, $current, $recurse=1, $showhidden=false) {
    global $CFG;

/// Make sure the default category exists for this course
    if (!$categories = get_records("question_categories", "course", $course->id, "id ASC")) {
        if (!$category = get_default_question_category($course->id)) {
            notify("Error creating a default category!");
        }
    }

/// Get all the existing categories now
    if (!$categories = get_records_select("question_categories", "course = '{$course->id}' OR publish = '1'", "parent, sortorder, name ASC")) {
        notify("Could not find any question categories!");
        return false;    // Something is really wrong
    }

    $categories = add_indented_names( $categories );
    foreach ($categories as $key => $category) {
       if ($catcourse = get_record("course", "id", $category->course)) {
           if ($category->publish && $category->course != $course->id) {
               $category->indentedname .= " ($catcourse->shortname)";
           }
           $catmenu[$category->id] = $category->indentedname;
       }
    }
    $strcategory = get_string("category", "quiz");
    $strshow = get_string("show", "quiz");
    $streditcats = get_string("editcategories", "quiz");

    echo "<table width=\"100%\"><tr><td width=\"20\" nowrap=\"nowrap\">";
    echo "<b>$strcategory:</b>&nbsp;";
    echo "</td><td>";
    popup_form ("edit.php?courseid=$course->id&amp;cat=", $catmenu, "catmenu", $current, "", "", "", false, "self");
    echo "</td><td align=\"right\">";
    echo "<form method=\"get\" action=\"$CFG->wwwroot/question/category.php\">";
    echo "<input type=\"hidden\" name=\"id\" value=\"$course->id\" />";
    echo "<input type=\"submit\" value=\"$streditcats\" />";
    echo "</form>";
    echo '</td></tr></table>';
    echo '<form method="post" action="edit.php" name="displayoptions">';
    echo '<table><tr><td>';
    echo "<input type=\"hidden\" name=\"courseid\" value=\"{$course->id}\" />";
    echo '<input type="hidden" name="recurse" value="0" />';
    echo '<input type="checkbox" name="recurse" value="1"';
    if ($recurse) {
        echo ' checked="checked"';
    }
    echo ' onchange="document.displayoptions.submit(); return true;" />';
    print_string('recurse', 'quiz');
    // hide-feature
    echo '<br />';
    echo '<input type="hidden" name="showhidden" value="0" />';
    echo '<input type="checkbox" name="showhidden"';
    if ($showhidden) {
        echo ' checked="checked"';
    }
    echo ' onchange="document.displayoptions.submit(); return true;" />';
    print_string('showhidden', 'quiz');
    echo '</td><noscript><td valign="center">';
    echo ' <input type="submit" value="'. get_string('go') .'" />';
    echo '</td></noscript></tr></table></form>';
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
 $recurse=1, $page=0, $perpage=100, $showhidden=false, $sortorder='qtype, name ASC') {
    global $QTYPE_MENU, $USER, $CFG;

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
        echo "<p align=\"center\"><b>";
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
    echo "<center>";
    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    echo format_text($category->info, FORMAT_MOODLE, $formatoptions, $course->id);

    echo '<table><tr>';

    // check if editing of this category is allowed
    if (isteacheredit($category->course)) {
        echo "<td valign=\"top\"><b>$strcreatenewquestion:</b></td>";
        echo '<td valign="top" align="right">';
        popup_form ("$CFG->wwwroot/question/question.php?category=$category->id&amp;qtype=", $qtypemenu, "addquestion",
                    "", "choose", "", "", false, "self");
        echo '</td><td width="10" valign="top" align="right">';
        helpbutton("questiontypes", $strcreatenewquestion, "quiz");
        echo '</td></tr>';
    }
    else {
        echo '<tr><td>';
        print_string("publishedit","quiz");
        echo '</td></tr>';
    }

    echo '<tr><td colspan="3" align="right"><font size="2">';
    if (isteacheredit($category->course)) {
        echo '<a href="'.$CFG->wwwroot.'/question/import.php?category='.$category->id.'">'.$strimportquestions.'</a>';
        helpbutton("import", $strimportquestions, "quiz");
        echo ' | ';
    }
    echo "<a href=\"$CFG->wwwroot/question/export.php?category={$category->id}&amp;courseid={$course->id}\">$strexportquestions</a>";
    helpbutton("export", $strexportquestions, "quiz");
    echo '</font></td></tr>';

    echo '</table>';

    echo '</center>';

    $categorylist = ($recurse) ? question_categorylist($category->id) : $category->id;

    // hide-feature
    $showhidden = $showhidden ? '' : " AND hidden = '0'";

    if (!$totalnumber = count_records_select('question', "category IN ($categorylist) AND parent = '0' $showhidden")) {
        echo "<p align=\"center\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return;
    }

    if (!$questions = get_records_select('question', "category IN ($categorylist) AND parent = '0' $showhidden", $sortorder, '*', $page*$perpage, $perpage)) {
        // There are no questions on the requested page.
        $page = 0;
        if (!$questions = get_records_select('question', "category IN ($categorylist) AND parent = '0' $showhidden", $sortorder, '*', 0, $perpage)) {
            // There are no questions at all
            echo "<p align=\"center\">";
            print_string("noquestions", "quiz");
            echo "</p>";
            return;
        }
    }

    print_paging_bar($totalnumber, $page, $perpage,
                "edit.php?courseid={$course->id}&amp;perpage=$perpage&amp;");

    $canedit = isteacheredit($category->course);

    echo '<form method="post" action="edit.php?courseid='.$course->id.'">';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    print_simple_box_start('center', '100%', '#ffffff', 0);
    echo '<table id="categoryquestions" cellspacing="0"><tr>';
    $actionwidth = $canedit ? 95 : 70;
    echo "<th width=\"$actionwidth\" nowrap=\"nowrap\" class=\"header\">$straction</th>";
    
    $sortoptions = array('name, qtype ASC' => get_string("sortalpha", "quiz"),
                         'qtype, name ASC' => get_string("sorttypealpha", "quiz"),
                         'id ASC' => get_string("sortage", "quiz"));
    $orderselect  = choose_from_menu ($sortoptions, 'sortorder', $sortorder, false, 'this.form.submit();', '0', true);
    $orderselect .= '<noscript><input type="submit" value="'.get_string("sortsubmit", "quiz").'" /></noscript>';
    echo "<th width=\"100%\" align=\"left\" nowrap=\"nowrap\" class=\"header\">$strquestionname $orderselect</th>
    <th nowrap=\"nowrap\" class=\"header\">$strtype</th>";
    echo "</tr>\n";
    foreach ($questions as $question) {
        echo "<tr>\n<td nowrap=\"nowrap\">\n";
        if ($quizid) {
            echo "<a title=\"$straddtoquiz\" href=\"edit.php?addquestion=$question->id&amp;quizid=$quizid&amp;sesskey=$USER->sesskey\"><img
                  src=\"$CFG->pixpath/t/moveleft.gif\" border=\"0\" alt=\"$straddtoquiz\" /></a>&nbsp;";
        }
        echo "<a title=\"$strpreview\" href=\"javascript:void();\" onClick=\"openpopup('/question/preview.php?id=$question->id&quizid=$quizid','$strpreview','scrollbars=yes,resizable=yes,width=700,height=480', false)\"><img
              src=\"$CFG->pixpath/t/preview.gif\" border=\"0\" alt=\"$strpreview\" /></a>&nbsp;";
        if ($canedit) {
            echo "<a title=\"$stredit\" href=\"$CFG->wwwroot/question/question.php?id=$question->id\"><img
                 src=\"$CFG->pixpath/t/edit.gif\" border=\"0\" alt=\"$stredit\" /></a>&nbsp;";
            // hide-feature
            if($question->hidden) {
                echo "<a title=\"$strrestore\" href=\"edit.php?courseid=$course->id&amp;unhide=$question->id&amp;sesskey=$USER->sesskey\"><img
                     src=\"$CFG->pixpath/t/restore.gif\" border=\"0\" alt=\"$strrestore\" /></a>";
            } else {
                echo "<a title=\"$strdelete\" href=\"edit.php?courseid=$course->id&amp;deleteselected=$question->id&amp;q$question->id=1\"><img
                     src=\"$CFG->pixpath/t/delete.gif\" border=\"0\" alt=\"$strdelete\" /></a>";
            }
        }
        echo "&nbsp;<input title=\"$strselect\" type=\"checkbox\" name=\"q$question->id\" value=\"1\" />";
        echo "</td>\n";

        if ($question->hidden) {
            echo '<td class="dimmed_text">'.$question->name."</td>\n";
        } else {
            echo "<td>".$question->name."</td>\n";
        }
        echo "<td align=\"center\">\n";
        print_question_icon($question, $canedit);
        echo "</td>\n";
        echo "</tr>\n";
    }
    echo '<tr><td align="center" colspan="3">';
    print_paging_bar($totalnumber, $page, $perpage, "edit.php?courseid={$course->id}&amp;perpage=$perpage&amp;");
    if ($totalnumber > DEFAULT_QUESTIONS_PER_PAGE) {
        if ($perpage == DEFAULT_QUESTIONS_PER_PAGE) {
            echo '<a href="edit.php?courseid='.$course->id.'&amp;perpage=1000">'.get_string('showall', 'moodle', $totalnumber).'</a>';
        } else {
            echo '<a href="edit.php?courseid='.$course->id.'&amp;perpage=' . DEFAULT_QUESTIONS_PER_PAGE . '">'.get_string('showperpage', 'moodle', DEFAULT_QUESTIONS_PER_PAGE).'</a>';
        }
    }
    echo "</td></tr></table>\n";
    print_simple_box_end();

    echo '<table class="quiz-edit-selected"><tr><td colspan="2">';
    echo '<a href="javascript:select_all_in(\'TABLE\', null, \'categoryquestions\');">'.$strselectall.'</a> /'.
     ' <a href="javascript:deselect_all_in(\'TABLE\', null, \'categoryquestions\');">'.$strselectnone.'</a>'.
     '</td><td align="right"><b>&nbsp;'.get_string('withselected', 'quiz').':</b></td></tr><tr><td>';
    if ($quizid) {
        echo "<input type=\"submit\" name=\"add\" value=\"<< $straddtoquiz\" />\n";
        echo '</td><td>';
    }
    if ($canedit) {
        echo '<input type="submit" name="deleteselected" value="'.$strdelete."\" /></td><td>\n";
        echo '<input type="submit" name="move" value="'.get_string('moveto', 'quiz')."\" />\n";
        question_category_select_menu($course->id, false, true, $category->id);
    }
    echo "</td></tr></table>";

    if ($quizid) {
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

    echo "</form>\n";
}

?>