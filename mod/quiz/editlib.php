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
* Delete a question from a quiz
*
* Deletes a question or a pagebreak from a quiz by updating $modform
* as well as the quiz, quiz_question_instances
* @return boolean         false if the question was not in the quiz
* @param int $id          The id of the question to be deleted
* @param object $modform  The extended quiz object as used by edit.php
*                         This is updated by this function
*/
function quiz_delete_quiz_question($id, &$modform) {
    // TODO: For the sake of safety check that this question can be deleted
    // safely, i.e., that it is not already in use.
    $questions = explode(",", $modform->questions);

    // only do something if this question exists
    if (!isset($questions[$id])) {
        return false;
    }

    $question = $questions[$id];
    unset($questions[$id]);
    // If we deleted the question at the top and it was followed by
    // a page break then delete page break as well
    if ($id == 0 && count($questions) > 1 && $questions[1] == 0) {
        unset($questions[1]);
    }
    $modform->questions = implode(",", $questions);
    // Avoid duplicate page breaks
    $modform->questions = str_replace(',0,0', ',0', $modform->questions);
    // save new questionlist in database
    if (!set_field('quiz', 'questions', $modform->questions, 'id', $modform->instance)) {
        error('Could not save question list');
    }
    delete_records('quiz_question_instances', 'quiz', $modform->instance, 'question', $question);
    return true;
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
    $questionrecord = get_record("question", "id", $id);
    $modform->grades[$id] = $questionrecord->defaultgrade;
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
* @param boolean $showbreaks  Indicates whether the reorder tool should be displayed
*/
function quiz_print_question_list($quiz, $allowdelete=true, $showbreaks=true, $reordertool=false) {
    global $USER, $CFG, $QTYPES;

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
        echo "<p class=\"quizquestionlistcontrols\">";
        print_string("noquestions", "quiz");
        echo "</p>";
        return 0;
    }

    if (!$questions = get_records_sql("SELECT q.*,c.course
                              FROM {$CFG->prefix}question q,
                                   {$CFG->prefix}question_categories c
                             WHERE q.id in ($quiz->questions)
                               AND q.category = c.id")) {
        echo "<p class=\"quizquestionlistcontrols\">";
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
    echo '<fieldset class="invisiblefieldset" style="display: block;">';
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";

    echo "<table style=\"width:100%;\">\n";
    echo "<tr><th colspan=\"3\" style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$strorder</th>";
    echo "<th class=\"header\" scope=\"col\">#</th>";
    echo "<th align=\"left\" style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$strquestionname</th>";
    echo "<th style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$strtype</th>";
    echo "<th style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$strgrade</th>";
    echo "<th align=\"center\" style=\"white-space:nowrap;\" class=\"header\" scope=\"col\">$straction</th>";
    echo "</tr>\n";
    foreach ($order as $i => $qnum) {

        if ($qnum and empty($questions[$qnum])) {
            continue;
        }

        // If the questiontype is missing change the question type
        if ($qnum and !array_key_exists($questions[$qnum]->qtype, $QTYPES)) {
            $questions[$qnum]->qtype = 'missingtype';
        }

        // Show the re-ordering field if the tool is turned on.
        // But don't show it in front of pagebreaks if they are hidden.
        if ($reordertool) {
            if ($qnum or $showbreaks) {
                echo '<tr><td><input type="text" name="o'.$i.'" size="2" value="'.(10*$count+10).'" /></td>';
            } else {
                echo '<tr><td><input type="hidden" name="o'.$i.'" size="2" value="'.(10*$count+10).'" /></td>';
            }
        } else {
            echo '<tr><td></td>';
        }
        if ($qnum == 0) { // This is a page break
            if ($showbreaks) {
                echo '<td colspan ="3">&nbsp;</td>';
                echo '<td><table style="width:100%; line-height:11px; font-size:9px; margin: -5px -5px;"><tr>';
                echo '<td><hr /></td>';
                echo '<td style="width:50px;">Page break</td>';
                echo '<td><hr /></td>';
                echo '<td style="width:45px;">';
                if ($count > 1) {
                    echo "<a title=\"$strmoveup\" href=\"edit.php?up=$count&amp;quizid=$quiz->id&amp;sesskey=$USER->sesskey\"><img
                         src=\"$CFG->pixpath/t/up.gif\" class=\"iconsmall\" alt=\"$strmoveup\" /></a>";
                }
                echo '&nbsp;';
                if ($count < $lastindex) {
                    echo "<a title=\"$strmovedown\" href=\"edit.php?down=$count&amp;quizid=$quiz->id&amp;sesskey=$USER->sesskey\"><img
                         src=\"$CFG->pixpath/t/down.gif\" class=\"iconsmall\" alt=\"$strmovedown\" /></a>";

                    echo "<a title=\"$strremove\" href=\"edit.php?delete=$count&amp;quizid=$quiz->id&amp;sesskey=$USER->sesskey\">
                          <img src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strremove\" /></a>";
                }
                echo '</td></tr></table></td>';
                echo '<td colspan="2">&nbsp;</td>';
            }
            $count++;
            // missing </tr> here, if loop is broken, need to close the </tr> from line 199/201
            echo "</tr>";
            continue;
        }
        $question = $questions[$qnum];
        $canedit = has_capability('moodle/question:manage', get_context_instance(CONTEXT_COURSE, $question->course));

        echo "<td>";
        if ($count != 0) {
            echo "<a title=\"$strmoveup\" href=\"edit.php?up=$count&amp;quizid=$quiz->id&amp;sesskey=$USER->sesskey\"><img
                 src=\"$CFG->pixpath/t/up.gif\" class=\"iconsmall\" alt=\"$strmoveup\" /></a>";
        }
        echo "</td>";
        echo "<td>";
        if ($count < $lastindex-1) {
            echo "<a title=\"$strmovedown\" href=\"edit.php?down=$count&amp;quizid=$quiz->id&amp;sesskey=$USER->sesskey\"><img
                 src=\"$CFG->pixpath/t/down.gif\" class=\"iconsmall\" alt=\"$strmovedown\" /></a>";
        }
        echo "</td>";

        if (!$quiz->shufflequestions) {
            // Print and increment question number
            echo '<td>'.($question->length ? $qno : '&nbsp;').'</td>';
            $qno += $question->length;
        } else {
            echo '<td>&nbsp;</td>';
        }

        echo '<td>' . format_string($question->name) . '</td>';
        echo "<td align=\"center\">";
        print_question_icon($question, $canedit);
        echo "</td>";
        echo '<td align="left">';
        if ($question->qtype == 'description') {
            echo "<input type=\"hidden\" name=\"q$qnum\" value=\"0\" /> \n";
        } else {
            echo '<input type="text" name="q'.$qnum.'" size="2" value="'.$quiz->grades[$qnum].
             '" tabindex="'.($lastindex+$qno).'" />';
        }
        echo '</td><td align="center">';

        $context = $quiz->id ? '&amp;contextquiz='.$quiz->id : '';
        $quiz_id = $quiz->id ? '&amp;quizid=' . $quiz->id : '';
        if ($question->qtype != 'random') {
            echo "<a title=\"$strpreview\" href=\"javascript:void(0)\" onclick=\"openpopup('/question/preview.php?id=$qnum$quiz_id','questionpreview', " . 
                    QUESTION_PREVIEW_POPUP_OPTIONS . ", false)\">
                    <img src=\"$CFG->pixpath/t/preview.gif\" class=\"iconsmall\" alt=\"$strpreview\" /></a>";
        }
        if ($canedit) {
            echo "<a title=\"$stredit\" href=\"$CFG->wwwroot/question/question.php?id=$qnum$context\">
                    <img src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a>";
        }
        if ($allowdelete) {
            echo "<a title=\"$strremove\" href=\"edit.php?delete=$count&amp;quizid=$quiz->id&amp;sesskey=$USER->sesskey\">
                    <img src=\"$CFG->pixpath/t/removeright.gif\" class=\"iconsmall\" alt=\"$strremove\" /></a>";
        }

        echo "</td></tr>";
        $count++;
        $sumgrade += $quiz->grades[$qnum];
    }

    echo "<tr><td colspan=\"6\" align=\"right\">\n";
    print_string('total');
    echo ": </td>";
    echo "<td align=\"left\">\n";
    echo "<strong>$sumgrade</strong>";
    echo "</td><td>&nbsp;\n</td></tr>\n";

    echo "<tr><td colspan=\"6\" align=\"right\">\n";
    print_string('maximumgrade');
    echo ": </td>";
    echo "<td align=\"left\">\n";
    echo '<input type="text" name="maxgrade" size="2" tabindex="'.($qno+1)
     .'" value="'.$quiz->grade.'" />';
    echo '</td><td align="left">';
    helpbutton("maxgrade", get_string("maximumgrade"), "quiz");
    echo "</td></tr></table>\n";

    echo '<div class="quizquestionlistcontrols"><input type="submit" value="'.get_string('savechanges').'" />';
    echo '<input type="hidden" name="savechanges" value="save" /></div>';
    echo '<input type="hidden" name="savequizid" value="'.$quiz->id.'" />'; // ugly hack to prevent modform session "mistakes"

    echo '</fieldset>';
    echo "</form>\n";

/// Form to choose to show pagebreaks and to repaginate quiz
    echo '<form method="post" action="edit.php" id="showbreaks">';
    echo '<fieldset class="invisiblefieldset">';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    echo '<input type="hidden" name="showbreaks" value="0" />';
    echo '<input type="checkbox" name="showbreaks" value="1"';
    if ($showbreaks) {
        echo ' checked="checked"';
    }
    echo ' onchange="getElementById(\'showbreaks\').submit(); return true;" />';
    print_string('showbreaks', 'quiz');

    if ($showbreaks) {
        $perpage= array();
        for ($i=0; $i<=50; ++$i) {
            $perpage[$i] = $i;
        }
        $perpage[0] = get_string('allinone', 'quiz');
        echo '<br />&nbsp;&nbsp;';
        print_string('repaginate', 'quiz',
         choose_from_menu($perpage, 'questionsperpage', $quiz->questionsperpage, '', '', '', true));
    }

    echo '<br /><input type="hidden" name="reordertool" value="0" />';
    echo '<input type="checkbox" name="reordertool" value="1"';
    if ($reordertool) {
        echo ' checked="checked"';
    }
    echo ' onchange="getElementById(\'showbreaks\').submit(); return true;" />';
    print_string('reordertool', 'quiz');
    echo ' ';
    helpbutton('reorderingtool', get_string('reorderingtool', 'quiz'), 'quiz');
    
    echo '<div class="quizquestionlistcontrols"><input type="submit" name="repaginate" value="'. get_string('go') .'" /></div>';
    echo '</fieldset>';
    echo '</form>';

    return $sumgrade;
}

?>
