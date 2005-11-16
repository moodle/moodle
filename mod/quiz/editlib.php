<?php // $Id$
/**
* Functions used by edit.php to edit quizzes
*
* @version $Id$
* @author Martin Dougiamas and many others. This has recently been extensively
*         rewritten by members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package quiz
*/

require_once("locallib.php");

/**
* Array of question types names translated to the user's language
*
* The $QUIZ_QUESTION_TYPE array holds the names of all the question types that the user should
* be able to create directly. Some internal question types like random questions are excluded.
* The complete list of question types can be found in {@link $QUIZ_QTYPES}.
*/

$QUIZ_QUESTION_TYPE = array ( MULTICHOICE   => get_string("multichoice", "quiz"),
                              TRUEFALSE     => get_string("truefalse", "quiz"),
                              SHORTANSWER   => get_string("shortanswer", "quiz"),
                              NUMERICAL     => get_string("numerical", "quiz"),
                              CALCULATED    => get_string("calculated", "quiz"),
                              MATCH         => get_string("match", "quiz"),
                              DESCRIPTION   => get_string("description", "quiz"),
                              RANDOMSAMATCH => get_string("randomsamatch", "quiz"),
                              MULTIANSWER   => get_string("multianswer", "quiz"),
                              ESSAY         => get_string("essay", "quiz")
                              );
// add remote question types
if ($rqp_types = get_records('quiz_rqp_types')) {
    foreach($rqp_types as $type) {
        $QUIZ_QUESTION_TYPE[100+$type->id] = $type->name;
    }
}


/**
* Delete a question from a quiz
*
* Deletes a question or a pagebreak from a quiz by updating $modform
* as well as the quiz, quiz_question_instances, quiz_attemtps, quiz_states
* and quiz_newest_states tables.
* @return boolean         false if the question was not in the quiz
* @param int $id          The id of the question to be deleted
* @param object $modform  The extended quiz object as used by edit.php
*                         This is updated by this function
*/
function quiz_delete_quiz_question($id, &$modform) {
    $questions = explode(",", $modform->questions);

    // only do something if this question exists
    if (!isset($questions[$id])) {
        return false;
    }

    $question = $questions[$id];
    unset($questions[$id]);
    // If we deleted the question at the top and it was followed by
    // a page break then delete page break as well
    if ($id == 0 and $questions[1] == 0) {
        unset($questions[1]);
    }
    // if what we deleted was not a page break but a question then also
    // delete associated grades, instances, attempts, states
    if ($question != 0) {
        unset($modform->grades[$question]);
        // Delete all states associated with all attempts for this question in the quiz.
        if ($attempts = get_records('quiz_attempts', 'quiz', $modform->instance)) {
            foreach ($attempts as $attempt) {
                delete_records('quiz_states', 'question', $question, 'attempt', $attempt->uniqueid);
                delete_records('quiz_newest_states', 'questionid', $question, 'attemptid', $attempt->uniqueid);
            }
        }
        // Delete all instances of the question in the quiz (there
        // should only be one)
        delete_records('quiz_question_instances', 'quiz',
         $modform->instance, 'question', $question);
    }
    $modform->questions = implode(",", $questions);
    // Avoid duplicate page breaks
    $modform->questions = str_replace(',0,0', ',0', $modform->questions);
    // save new questionlist in database
    if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
        error('Could not save question list');
    }
}


/**
* Add a question to a quiz
*
* Adds a question to a quiz by updating $modform as well as the
* quiz and quiz_question_instances tables. It also adds a page break
* if required.
* @return boolean         false if the question was already in the quiz
* @param int $id          The id of the question to be added
* @param object $modform  The extended quiz object as used by edit.php
*                         This is updated by this function
*/
function quiz_add_quiz_question($id, &$modform) {
    $questions = explode(",", $modform->questions);

    if (in_array($id, $questions)) {
        return false;
    }

    // remove ending page break if it is not needed
    if ($breaks = array_keys($questions, 0)) {
        // determine location of the last two page breaks
        $end = end($breaks);
        $last = prev($breaks);
        $last = $last ? $last : -1;
        if (!$modform->questionsperpage or (($end - $last -1) < $modform->questionsperpage)) {
            array_pop($questions);
        }
    }
    // add question
    $questions[] = $id;
    // add ending page break
    $questions[] = 0;

    // Save new questionslist in database
    $modform->questions = implode(",", $questions);
    if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->id)) {
        error('Could not save question list');
    }

    // update question grades
    $questionrecord = get_record("quiz_questions", "id", $id);
    if (!empty($questionrecord->defaultgrade)) {
        $modform->grades[$id] = $questionrecord->defaultgrade;
    } else if ($questionrecord->qtype == DESCRIPTION){
        $modform->grades[$id] = 0;
    } else {
        $modform->grades[$id] = 1;
    }
    quiz_update_question_instance($modform->grades[$id], $id, $modform->instance);
    return true;
}

/**
* Save changes to question instance
*
* Saves changes to the question grades in the quiz_question_instances table.
* It does not update 'sumgrades' in the quiz table.
* @return boolean         Indicates success or failure.
* @param integer grade    The maximal grade for the question
* @param integer $questionid  The id of the question
* @param integer $quizid  The id of the quiz to update / add the instances for.
*/
function quiz_update_question_instance($grade, $questionid, $quizid) {
    if ($instance = get_record("quiz_question_instances", "quiz", $quizid, 'question', $questionid)) {
        $instance->grade = $grade;
        return update_record('quiz_question_instances', $instance);
    } else {
        unset($instance);
        $instance->quiz = $quizid;
        $instance->question = $questionid;
        $instance->grade = $grade;
        return insert_record("quiz_question_instances", $instance);
    }
}

/**
* Prints a list of quiz questions in a small layout form with knobs
*
* @return int sum of maximum grades
* @param object $quiz This is not the standard quiz object used elsewhere but
*     it contains the quiz layout in $quiz->questions and the grades in
*     $quiz->grades
* @param boolean $allowdelete Indicates whether the delete icons should be displayed
* @param boolean $showbreaks  Indicates whether the page breaks should be displayed
*/
function quiz_print_question_list($quiz, $allowdelete=true, $showbreaks=true) {
    global $USER, $CFG, $QUIZ_QTYPES;

    $strorder = get_string("order");
    $strquestionname = get_string("questionname", "quiz");
    $strgrade = get_string("grade");
    $strremove = get_string('remove', 'quiz');
    $stredit = get_string("edit");
    $straction = get_string("action");
    $strmoveup = get_string("moveup");
    $strmovedown = get_string("movedown");
    $strsavegrades = get_string("savegrades", "quiz");
    $strtype = get_string("type", "quiz");
    $strpreview = get_string("preview", "quiz");

    if (!$quiz->questions) {
        echo "<p align=\"center\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return 0;
    }

    if (!$questions = get_records_sql("SELECT q.*,c.course
                              FROM {$CFG->prefix}quiz_questions q,
                                   {$CFG->prefix}quiz_categories c
                             WHERE q.id in ($quiz->questions)
                               AND q.category = c.id")) {
        echo "<p align=\"center\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return 0;
    }

    $count = 0;
    $qno = 1;
    $sumgrade = 0;
    $order = explode(",", $quiz->questions);
    $lastindex = count($order)-1;
    // If the list does not end with a pagebreak then add it on.
    if ($order[$lastindex] != 0) {
        $order[] = 0;
        $lastindex++;
    }
    echo "<form method=\"post\" action=\"edit.php\">";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";

    print_simple_box_start('center', '100%', '#ffffff', 0);
    echo "<table border=\"0\" cellpadding=\"2\" cellspacing=\"0\" width=\"100%\">\n";
    echo "<tr><th colspan=\"3\" nowrap=\"nowrap\" class=\"header\">$strorder</th><th align=\"left\" width=\"100%\" nowrap=\"nowrap\" class=\"header\">$strquestionname</th><th nowrap=\"nowrap\" class=\"header\">$strtype</th><th nowrap=\"nowrap\" class=\"header\">$strgrade</th><th align=\"center\" width=\"60\" nowrap=\"nowrap\" class=\"header\">$straction</th></tr>\n";
    foreach ($order as $qnum) {

        if ($qnum == 0) { // This is a page break
            if ($showbreaks) {
                echo '<tr><td colspan ="3">&nbsp;</td>';
                echo '<td><table width="100%" style="line-height:11px; font-size:9px; margin: -5px -5px;"><tr>';
                echo '<td><hr noshade="noshade" /></td>';
                echo '<td width="50">Page break</td>';
                echo '<td><hr noshade="noshade" /></td><td width="45">';
                if ($count > 1) {
                    echo "<a title=\"$strmoveup\" href=\"edit.php?up=$count&amp;sesskey=$USER->sesskey\"><img
                         src=\"$CFG->pixpath/t/up.gif\" border=\"0\" alt=\"$strmoveup\" /></a>";
                }
                echo '&nbsp;';
                if ($count < $lastindex) {
                    echo "<a title=\"$strmovedown\" href=\"edit.php?down=$count&amp;sesskey=$USER->sesskey\"><img
                         src=\"$CFG->pixpath/t/down.gif\" border=\"0\" alt=\"$strmovedown\" /></a>";

                    echo "<a title=\"$strremove\" href=\"edit.php?delete=$count&amp;sesskey=$USER->sesskey\">
                          <img src=\"$CFG->pixpath/t/delete.gif\" border=\"0\" alt=\"$strremove\" /></a>";
                }
                echo '</td></tr></table></td>';
                echo '<td colspan="2">&nbsp;</td></tr>';
            }
            $count++;
            continue;
        }
        if (empty($questions[$qnum])) {
            continue;
        }
        $question = $questions[$qnum];
        $canedit = isteacheredit($question->course);

        echo "<tr>";
        echo "<td>";
        if ($count != 0) {
            echo "<a title=\"$strmoveup\" href=\"edit.php?up=$count&amp;sesskey=$USER->sesskey\"><img
                 src=\"$CFG->pixpath/t/up.gif\" border=\"0\" alt=\"$strmoveup\" /></a>";
        }
        echo "</td>";
        echo "<td>";
        if ($count < $lastindex-1) {
            echo "<a title=\"$strmovedown\" href=\"edit.php?down=$count&amp;sesskey=$USER->sesskey\"><img
                 src=\"$CFG->pixpath/t/down.gif\" border=\"0\" alt=\"$strmovedown\" /></a>";
        }
        echo "</td>";

        if (!$quiz->shufflequestions) {
            // Print and increment question number
            echo '<td>'.($question->length ? $qno : '&nbsp;').'</td>';
            $qno += $question->length;
        } else {
            echo '<td>&nbsp;</td>';
        }

        echo "<td>$question->name</td>";
        echo "<td align=\"center\">";
        quiz_print_question_icon($question, $canedit);
        echo "</td>";
        echo '<td align="left">';
        if ($question->qtype == DESCRIPTION) {
            echo "<input type=\"hidden\" name=\"q$qnum\" value=\"0\" /> \n";
        } else {
            echo '<input type="text" name="q'.$qnum.'" size="2" value="'.$quiz->grades[$qnum].
             '" tabindex="'.$qno.'" />';
        }
        echo '</td><td align="center">';

        $context = $quiz->id ? '&amp;contextquiz='.$quiz->id : '';
        $quiz_id = $quiz->id ? '&amp;quizid=' . $quiz->id : '';
        echo "<a title=\"$strpreview\" href=\"javascript:void();\" onClick=\"openpopup('/mod/quiz/preview.php?id=$qnum$quiz_id','$strpreview','scrollbars=yes,resizable=yes,width=700,height=480', false)\">
              <img src=\"$CFG->pixpath/t/preview.gif\" border=\"0\" alt=\"$strpreview\" /></a>";
        if ($canedit) {
            echo "<a title=\"$stredit\" href=\"question.php?id=$qnum$context\">
                  <img src=\"$CFG->pixpath/t/edit.gif\" border=\"0\" alt=\"$stredit\" /></a>";
        }
        if ($allowdelete) {
            echo "<a title=\"$strremove\" href=\"edit.php?delete=$count&amp;sesskey=$USER->sesskey\">
                  <img src=\"$CFG->pixpath/t/removeright.gif\" border=\"0\" alt=\"$strremove\" /></a>";
        }

        echo "</td>";
        $count++;
        $sumgrade += $quiz->grades[$qnum];
    }

    echo "<tr><td colspan=\"5\" align=\"right\">\n";
    print_string('total');
    echo ": <td align=\"left\">\n";
    echo "<b>$sumgrade</b>";
    echo "</td><td>&nbsp;\n</td></tr>\n";

    echo "<tr><td colspan=\"5\" align=\"right\">\n";
    print_string('maximumgrade');
    echo ": <td align=\"left\">\n";
    echo '<input type="text" name="maxgrade" size="2" tabindex="'.($qno+1)
     .'" value="'.$quiz->grade.'" />';
    echo '</td><td align="left">';
    helpbutton("maxgrade", get_string("maximumgrade"), "quiz");
    echo "</td></tr>\n";

    echo "<tr><td colspan=\"6\" align=\"right\">\n";

    echo "<input type=\"submit\" value=\"$strsavegrades\" />\n";
    echo "<input type=\"hidden\" name=\"setgrades\" value=\"save\" />\n";
    echo "</td><td>&nbsp;</td><td>\n</td></tr>\n";

    echo "</table>\n";
    print_simple_box_end();
    echo "</form>\n";

/// Form to choose to show pagebreaks and to repaginate quiz
    echo '<form method="post" action="edit.php" name="showbreaks">';
    echo '<input type="hidden" name="showbreaks" value="0" />';
    echo '<input type="checkbox" name="showbreaks" value="1"';
    if ($showbreaks) {
        echo ' checked="checked"';
    }
    echo ' onchange="document.showbreaks.submit(); return true;" />';
    print_string('showbreaks', 'quiz');
    echo ' <noscript><input type="submit" value="'. get_string('go') .'" /></noscript>';

    if ($showbreaks) {
        $perpage= array();
        for ($i=0; $i<=50; ++$i) {
            $perpage[$i] = $i;
        }
        $perpage[0] = get_string('allinone', 'quiz');
        echo '<br />';
        echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
        print_string('repaginate', 'quiz',
         choose_from_menu($perpage, 'questionsperpage', $quiz->questionsperpage, '', '', '', true));
        echo ' <input type="submit" name="repaginate" value="'. get_string('go') .'" />';
    }

    echo '</form>';

    return $sumgrade;
}

function quiz_print_category_form($course, $current, $recurse=1, $showhidden=false) {
/// Prints a form to choose categories

/// Make sure the default category exists for this course
    if (!$categories = get_records("quiz_categories", "course", $course->id, "id ASC")) {
        if (!$category = quiz_get_default_category($course->id)) {
            notify("Error creating a default category!");
        }
    }

/// Get all the existing categories now
    if (!$categories = get_records_select("quiz_categories", "course = '{$course->id}' OR publish = '1'", "parent, sortorder, name ASC")) {
        notify("Could not find any question categories!");
        return false;    // Something is really wrong
    }
    $categories = add_indented_names($categories);
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
    popup_form ("edit.php?cat=", $catmenu, "catmenu", $current, "", "", "", false, "self");
    echo "</td><td align=\"right\">";
    echo "<form method=\"get\" action=\"category.php\">";
    echo "<input type=\"hidden\" name=\"id\" value=\"$course->id\" />";
    echo "<input type=\"submit\" value=\"$streditcats\" />";
    echo "</form>";
    echo '</td></tr></table>';
    echo '<form method="post" action="edit.php" name="displayoptions">';
    echo '<table><tr><td>';
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
function quiz_print_cat_question_list($course, $categoryid, $quizid,
 $recurse=1, $page, $perpage, $showhidden=false, $sortorder='qtype, name ASC') {
    global $QUIZ_QUESTION_TYPE, $USER, $CFG;

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

    $strsortalpha  = get_string("sortalpha", "quiz");
    $strsortage    = get_string("sortage", "quiz");
    $strsortsubmit = get_string("sortsubmit", "quiz");

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

    if (!$category = get_record("quiz_categories", "id", "$categoryid")) {
        notify("Category not found!");
        return;
    }
    echo "<center>";
    echo format_text($category->info, FORMAT_MOODLE);

    echo '<table><tr>';

    // check if editing of this category is allowed
    if (isteacheredit($category->course)) {
        echo "<td valign=\"top\"><b>$strcreatenewquestion:</b></td>";
        echo '<td valign="top" align="right">';
        popup_form ("question.php?category=$category->id&amp;qtype=", $QUIZ_QUESTION_TYPE, "addquestion",
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
        echo '<a href="import.php?category='.$category->id.'">'.$strimportquestions.'</a>';
        helpbutton("import", $strimportquestions, "quiz");
        echo ' | ';
    }
    echo "<a href=\"export.php?category={$category->id}&amp;courseid={$course->id}\">$strexportquestions</a>";
    helpbutton("export", $strexportquestions, "quiz");
    echo '</font></td></tr>';

    echo '</table>';

    echo '</center>';

    $categorylist = ($recurse) ? quiz_categorylist($category->id) : $category->id;

    // hide-feature
    $showhidden = $showhidden ? '' : " AND hidden = '0'";

    if (!$totalnumber = count_records_select('quiz_questions', "category IN ($categorylist) AND parent = '0' $showhidden")) {
        echo "<p align=\"center\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return;
    }

    if (!$questions = get_records_select('quiz_questions', "category IN ($categorylist) AND parent = '0' $showhidden", $sortorder, '*', $page*$perpage, $perpage)) {
        // There are no questions on the requested page.
        $page = 0;
        if (!$questions = get_records_select('quiz_questions', "category IN ($categorylist) AND parent = '0' $showhidden", $sortorder, '*', 0, $perpage)) {
            // There are no questions at all
            echo "<p align=\"center\">";
            print_string("noquestions", "quiz");
            echo "</p>";
            return;
        }
    }

    print_paging_bar($totalnumber, $page, $perpage,
                "edit.php?perpage=$perpage&amp;");

    $canedit = isteacheredit($category->course);

    echo '<form method="post" action="edit.php">';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    print_simple_box_start('center', '100%', '#ffffff', 0);
    echo '<table id="categoryquestions" cellspacing="0"><tr>';
    $actionwidth = $canedit ? 95 : 70;
    echo "<th width=\"$actionwidth\" nowrap=\"nowrap\" class=\"header\">$straction</th>";
    $sortoptions = array('qtype, name ASC' => $strsortalpha,
                         'id ASC' => $strsortage);
    $orderselect  = choose_from_menu ($sortoptions, 'sortorder', $sortorder, false, 'this.form.submit();', '0', true);
    $orderselect .= '<noscript><input type="submit" value="'.$strsortsubmit.'" /></noscript>';
    echo "<th width=\"100%\" align=\"left\" nowrap=\"nowrap\" class=\"header\">$strquestionname $orderselect</th>
    <th nowrap=\"nowrap\" class=\"header\">$strtype</th>";
    echo "</tr>\n";
    foreach ($questions as $question) {
        if ($question->qtype == RANDOM) {
            //continue;
        }
        echo "<tr>\n<td>\n";
        if ($quizid) {
            echo "<a title=\"$straddtoquiz\" href=\"edit.php?addquestion=$question->id&amp;sesskey=$USER->sesskey\"><img
                  src=\"$CFG->pixpath/t/moveleft.gif\" border=\"0\" alt=\"$straddtoquiz\" /></a>&nbsp;";
        }
        echo "<a title=\"$strpreview\" href=\"javascript:void();\" onClick=\"openpopup('/mod/quiz/preview.php?id=$question->id&quizid=$quizid','$strpreview','scrollbars=yes,resizable=yes,width=700,height=480', false)\"><img
              src=\"$CFG->pixpath/t/preview.gif\" border=\"0\" alt=\"$strpreview\" /></a>&nbsp;";
        if ($canedit) {
            echo "<a title=\"$stredit\" href=\"question.php?id=$question->id\"><img
                 src=\"$CFG->pixpath/t/edit.gif\" border=\"0\" alt=\"$stredit\" /></a>&nbsp;";
            // hide-feature
            if($question->hidden) {
                echo "<a title=\"$strrestore\" href=\"question.php?id=$question->id&amp;hide=0&amp;sesskey=$USER->sesskey\"><img
                     src=\"$CFG->pixpath/t/restore.gif\" border=\"0\" alt=\"$strrestore\" /></a>";
            } else {
                echo "<a title=\"$strdelete\" href=\"question.php?id=$question->id&amp;delete=$question->id\"><img
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
        quiz_print_question_icon($question, $canedit);
        echo "</td>\n";
        echo "</tr>\n";
    }
    echo '<tr><td colspan="3">';
    print_paging_bar($totalnumber, $page, $perpage, "edit.php?perpage=$perpage&amp;");
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
        quiz_category_select_menu($course->id, false, true, $category->id);
    }
    echo "</td></tr></table>";

    if ($quizid) {
        for ($i=1;$i<=10; $i++) {
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
