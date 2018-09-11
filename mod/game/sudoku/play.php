<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plays the game "Sudoku".
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once( "../../lib/questionlib.php");

/**
 * Plays the game Sudoku
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $sudoku
 * @param boolean $endofgame
 * @param stdClass $context
 */
function game_sudoku_continue( $id, $game, $attempt, $sudoku, $endofgame, $context) {
    global $CFG, $DB, $USER;

    if ($endofgame) {
        game_updateattempts( $game, $attempt, -1, true);
        $endofgame = false;
    }

    if ($attempt != false and $sudoku != false) {
        return game_sudoku_play( $id, $game, $attempt, $sudoku, false, false, $context);
    }

    if ($attempt == false) {
        $attempt = game_addattempt( $game);
    }

    // New game.
    srand( (double)microtime() * 1000000);

    $recsudoku = getrandomsudoku();
    if ($recsudoku == false) {
        print_error( 'Empty sudoku database');
    }

    $newrec = new stdClass();
    $newrec->id = $attempt->id;
    $newrec->guess = '';
    $newrec->data = $recsudoku->data;
    $newrec->opened  = $recsudoku->opened;

    $need = 81 - $recsudoku->opened;
    $closed = game_sudoku_getclosed( $newrec->data);
    $n = min( count($closed), $need);
    // If the teacher set the maximum number of questions.
    if ($game->param2 > 0) {
        if ($game->param2 < $n) {
            $n = $game->param2;
        }
    }
    $recs = game_questions_selectrandom( $game, CONST_GAME_TRIES_REPETITION * $n);

    if ($recs === false) {
        $sql = "DELETE FROM {game_sudoku} WHERE id={$game->id}";
        $DB->execute( $sql);
        print_error( get_string( 'no_questions', 'game'));
    }

    $closed = array_rand($closed, $n);

    $selectedrecs = game_select_from_repetitions( $game, $recs, $n);

    if (!game_insert_record('game_sudoku', $newrec)) {
        print_error('error inserting in game_sudoku');
    }

    $i = 0;
    $field = ($game->sourcemodule == 'glossary' ? 'glossaryentryid' : 'questionid');
    foreach ($recs as $rec) {
        if ($game->sourcemodule == 'glossary') {
            $key = $rec->glossaryentryid;
        } else {
            $key = $rec->questionid;
        }

        if (!array_key_exists( $key, $selectedrecs)) {
            continue;
        }

        $query = new stdClass();
        $query->attemptid = $newrec->id;
        $query->gamekind = $game->gamekind;
        $query->gameid = $game->id;
        $query->userid = $USER->id;
        $query->col = $closed[ $i++];
        $query->sourcemodule = $game->sourcemodule;
        $query->questionid = $rec->questionid;
        $query->glossaryentryid = $rec->glossaryentryid;
        $query->score = 0;
        if (($query->id = $DB->insert_record( 'game_queries', $query)) == 0) {
            print_error( 'error inserting in game_queries');
        }

        game_update_repetitions($game->id, $USER->id, $query->questionid, $query->glossaryentryid);
    }

    game_updateattempts( $game, $attempt, 0, 0);

    game_sudoku_play( $id, $game, $attempt, $newrec, false, false, $context);
}

/**
 * Plays the game Sudoku
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $sudoku
 * @param boolean $onlyshow
 * @param boolean $showsolution
 * @param stdClass $context
 */
function game_sudoku_play( $id, $game, $attempt, $sudoku, $onlyshow, $showsolution, $context) {
    $offsetquestions = game_sudoku_compute_offsetquestions( $game->sourcemodule, $attempt, $numbers, $correctquestions);

    if ($game->toptext != '') {
        echo $game->toptext.'<br>';
    }

    game_sudoku_showsudoku( $sudoku->data, $sudoku->guess, true, $showsolution, $offsetquestions,
        $correctquestions, $id, $attempt, $game);
    switch ($game->sourcemodule) {
        case 'quiz':
        case 'question':
            game_sudoku_showquestions_quiz( $id, $game, $attempt, $sudoku, $offsetquestions,
                $numbers, $correctquestions, $onlyshow, $showsolution, $context);
            break;
        case 'glossary':
            game_sudoku_showquestions_glossary( $id, $game, $attempt, $sudoku, $offsetquestions,
                $numbers, $correctquestions, $onlyshow, $showsolution);
            break;
    }

    if ($game->bottomtext != '') {
        echo '<br>'.$game->bottomtext;
    }
}

/**
 * Returns a map with an offset and id of each question.
 *
 * @param string $sourcemodule
 * @param stdClass $attempt
 * @param int $numbers
 * @param int $correctquestions
 */
function game_sudoku_compute_offsetquestions( $sourcemodule, $attempt, &$numbers, &$correctquestions) {
    global $CFG, $DB;

    $select = "attemptid = $attempt->id";

    $fields = 'id, col, score';
    switch( $sourcemodule)
    {
        case 'quiz':
        case 'question':
            $fields .= ',questionid as id2';
            break;
        case 'glossary':
            $fields .= ',glossaryentryid as id2';
            break;
    }
    if (($recs = $DB->get_records_select( 'game_queries', $select, null, '', $fields)) == false) {
        $DB->execute( "DELETE FROM {$CFG->prefix}game_sudoku WHERE id={$attempt->id}");
        print_error( 'There are no questions '.$attempt->id);
    }
    $offsetquestions = array();
    $numbers = array();
    $correctquestions = array();
    foreach ($recs as $rec) {
        $offsetquestions[ $rec->col] = $rec->id2;
        $numbers[ $rec->id2] = $rec->col;
        if ( $rec->score == 1) {
            $correctquestions[ $rec->col] = 1;
        }
    }

    ksort( $offsetquestions);

    return $offsetquestions;
}

/**
 * Select a sudoku randomly
 */
function getrandomsudoku() {
    global $DB;

    $count = $DB->count_records( 'game_sudoku_database');
    if ($count == 0) {
        require_once(dirname(__FILE__) . '/../db/importsudoku.php');

        $count = $DB->count_records( 'game_sudoku_database');
        if ($count == 0) {
            return false;
        }
    }

    $i = mt_rand( 0, $count - 1);

    if (($recs = $DB->get_records( 'game_sudoku_database', null, '', '*', $i, 1)) != false) {
        foreach ($recs as $rec) {
            return $rec;
        }
    }

    return false;
}

/**
 * Get closed
 *
 * @param string $data
 */
function game_sudoku_getclosed( $data) {
    $a = array();

    $n = game_strlen( $data);
    for ($i = 1; $i <= $n; $i++) {
        $c = game_substr( $data, $i - 1, 1);
        if ($c >= "1" and $c <= "9") {
            $a[ $i] = $i;
        }
    }

    return $a;
}

/**
 * Shows the sudoku
 *
 * @param string $data
 * @param string $guess
 * @param boolean $bshowlegend
 * @param boolean $bshowsolution
 * @param int $offsetquestions
 * @param int $correctquestions
 * @param int $id
 * @param stdClass $attempt
 * @param stdClass $game
 */
function game_sudoku_showsudoku( $data, $guess, $bshowlegend, $bshowsolution, $offsetquestions,
    $correctquestions, $id, $attempt, $game) {
    global $CFG, $DB;

    $correct = $count = 0;

    echo "<br>\r\n";
    echo '<table border="1" style="border-collapse: separate; border-spacing: 0px;">';
    $pos = 0;
    for ($i = 0; $i <= 2; $i++) {
        echo "<tr>";
        for ($j = 0; $j <= 2; $j++) {
            echo '<td><table border="1" width="100%">';
            for ($k1 = 0; $k1 <= 2; $k1++) {
                echo "<tr>";
                for ($k2 = 0; $k2 <= 2; $k2++) {
                    $s = substr( $data, $pos, 1);
                    $g = substr( $guess, $pos, 1);
                    $pos++;
                    if ($g != 0) {
                        $s = $g;
                    }
                    if ($s >= "1" and $s <= "9") {
                        // Closed number.
                        if ($bshowlegend) {
                            // Show legend.
                            if ($bshowsolution == false) {
                                if (!array_key_exists( $pos, $correctquestions)) {
                                    if (array_key_exists( $pos, $offsetquestions)) {
                                        if ($s != $g) {
                                            $s = '<input type="submit" value="A'.$pos.'" onclick="OnCheck( '.$pos.');" />';
                                        }
                                    } else if ($g == 0) {
                                        $s = '<input type="submit" value="" onclick="OnCheck( '.$pos.');" />';
                                    }
                                } else {
                                    // Correct question.
                                    $count++;
                                }
                            }
                            echo '<td width=33% style="text-align: center; padding: .6em; '.
                                ' color: red; font-weight: lighter; font-size: 1em;">'.$s.'</td>';
                        } else {
                            // Not show legend.
                            echo '<td width=33% style="text-align: center; padding: .6em;'.
                                ' color: red; font-weight: lighter; font-size: 1em;">&nbsp;</td>';
                        }
                    } else {
                        $s = strpos( "-ABCDEFGHI", $s);
                        $count++;
                        echo '<td width=33% style="text-align: center; padding: .6em; '.
                            ' color: black; font-weight: lighter; font-size: 1em;">'.$s.'</td>';
                    }
                }
                echo "</tr>";
            }
            echo "</table></td>\r\n";
        }
        echo "</tr>";
    }
    echo "</table>\r\n";
    $href = $CFG->wwwroot.'/mod/game/attempt.php?action=sudokucheckn&id='.$id;

?>
    <script language="javascript">
        function OnCheck( pos) {
            s = window.prompt( "<?php echo get_string ( 'sudoku_guessnumber', 'game') ?>", "");

            if (s < "1")
                return;
            if (s > "9")
                return;

            window.location.href = "<?php echo $href; ?>&pos=" + pos + "&num=" + s;
        }
    </script>
    <?php

    // Here are the congratulations.
    if ($attempt->timefinish) {
        return $count;
    }

    if (count($offsetquestions) != count( $correctquestions)) {
        return $count;
    }

    if (! $cm = $DB->get_record( 'course_modules', array( 'id' => $id))) {
        print_error( "Course Module ID was incorrect id=$id");
    }

    echo '<B><br>'.get_string( 'win', 'game').'</B><BR>';
    echo '<br>';
    echo "<a href=\"$CFG->wwwroot/mod/game/attempt.php?id=$id\">".
        get_string( 'nextgame', 'game').'</a> &nbsp; &nbsp; &nbsp; &nbsp; ';
    echo "<a href=\"$CFG->wwwroot/course/view.php?id=$cm->course\">".get_string( 'finish', 'game').'</a> ';

    game_updateattempts( $game, $attempt, 1, 1);

    return $count;
}

/**
 * Get question list
 *
 * @param int $offsetquestions
 */
function game_sudoku_getquestionlist( $offsetquestions) {
    $questionlist = '';
    foreach ($offsetquestions as $q) {
        if ($q != 0) {
            $questionlist .= ','.$q;
        }
    }
    $questionlist = substr( $questionlist, 1);

    if ($questionlist == '') {
        print_error( get_string('no_questions', 'game'));
    }

    return $questionlist;
}

/**
 * Get glossary entries
 *
 * @param stdClass $game
 * @param int $offsetentries
 * @param string $entrylist
 * @param int $numbers
 */
function game_sudoku_getglossaryentries( $game, $offsetentries, &$entrylist, $numbers) {
    global $DB;

    $entrylist = implode( ',', $offsetentries);

    if ($entrylist == '') {
        print_error( get_string( 'sudoku_noentriesfound', 'game'));
    }

    // Load the questions.
    if (!$entries = $DB->get_records_select( 'glossary_entries', "id IN ($entrylist)")) {
        print_error( get_string('sudoku_noentriesfound', 'game'));
    }

    return $entries;
}

/**
 * Plays the game hangman
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $sudoku
 * @param int $offsetquestions
 * @param string $numbers
 * @param int $correctquestions
 * @param boolean $onlyshow
 * @param boolean $showsolution
 * @param stdClass $context
 */
function game_sudoku_showquestions_quiz( $id, $game, $attempt, $sudoku, $offsetquestions, $numbers,
     $correctquestions, $onlyshow, $showsolution, $context) {
    global $CFG;

    $questionlist = game_sudoku_getquestionlist( $offsetquestions);
    $questions = game_sudoku_getquestions( $questionlist);

    // I will sort with the number of each question.
    $questions2 = array();
    foreach ($questions as $q) {
        $ofs = $numbers[ $q->id];
        $questions2[ $ofs] = $q;
    }
    ksort( $questions2);

    if (count( $questions2) == 0) {
        game_sudoku_showquestion_onfinish( $id, $game, $attempt, $sudoku);
        return;
    }

    $number = 0;
    $found = false;
    foreach ($questions2 as $question) {
        $ofs = $numbers[ $question->id];
        if (array_key_exists( $ofs, $correctquestions)) {
            continue;   // I don't show the correct answers.
        }

        if ( $found == false) {
            $found = true;
            // Start the form.
            echo "<form id=\"responseform\" method=\"post\" ".
                "action=\"{$CFG->wwwroot}/mod/game/attempt.php\" onclick=\"this.autocomplete='off'\">\n";
            if (($onlyshow === false) and ($showsolution === false)) {
                echo "<br><center><input type=\"submit\" name=\"submit\" value=\"".get_string('sudoku_submit', 'game')."\">";

                echo " &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"finishattempt\" value=\"".
                get_string('sudoku_finishattemptbutton', 'game')."\">";
            }

            // Add a hidden field with the quiz id.
            echo '<div>';
            echo '<input type="hidden" name="id" value="' . s($id) . "\" />\n";
            echo '<input type="hidden" name="action" value="sudokucheck" />';

            // Print all the questions.

            // Add a hidden field with questionids.
            echo '<input type="hidden" name="questionids" value="'.$questionlist."\" />\n";
        }

        $number = "<a name=\"a$ofs\">A$ofs</a>";

        game_print_question( $game, $question, $context);
    }

    if ($found) {
        echo "</div>";

        // Finish the form.
        echo '</div>';
        if (($onlyshow === false) and ($showsolution === false)) {
            echo "<center><input type=\"submit\" name=\"submit\" value=\"".get_string('sudoku_submit', 'game')."\"></center>\n";
        }

        echo "</form>\n";
    }
}

/**
 * Show the sudoku and glossaryentries.
 *
 * @param int $id
 * @param string $game
 * @param stdClass $attempt
 * @param stdClass $sudoku
 * @param int $offsetentries
 * @param int $numbers
 * @param int $correctentries
 * @param boolean $onlyshow
 * @param boolean $showsolution
 */
function game_sudoku_showquestions_glossary( $id, $game, $attempt, $sudoku, $offsetentries, $numbers,
 $correctentries, $onlyshow, $showsolution) {
    global $CFG;

    $entries = game_sudoku_getglossaryentries( $game, $offsetentries, $questionlist, $numbers);

    // I will sort with the number of each question.
    $entries2 = array();
    foreach ($entries as $q) {
        $ofs = $numbers[ $q->id];
        $entries2[ $ofs] = $q;
    }
    ksort( $entries2);

    if (count( $entries2) == 0) {
        game_sudoku_showquestion_onfinish( $id, $game, $attempt, $sudoku);
        return;
    }

    // Start the form.
    echo "<br><form id=\"responseform\" method=\"post\" ".
        "action=\"{$CFG->wwwroot}/mod/game/attempt.php\" onclick=\"this.autocomplete='off'\">\n";

    if ($onlyshow) {
        $hasquestions = false;
    } else {
        $hasquestions = ( count($correctentries) < count( $entries2));
    }

    if ($hasquestions) {
        echo "<center><input type=\"submit\" name=\"submit\" value=\"".get_string('sudoku_submit', 'game')."\"></center>\n";
    }

    // Add a hidden field with the quiz id.
    echo '<div>';
    echo '<input type="hidden" name="id" value="' . s($id) . "\" />\n";
    echo '<input type="hidden" name="action" value="sudokucheckg" />';

    // Print all the questions.

    // Add a hidden field with questionids.
    echo '<input type="hidden" name="questionids" value="'.$questionlist."\" />\n";

    $number = 0;
    foreach ($entries2 as $entry) {
        $ofs = $numbers[ $entry->id];
        if (array_key_exists( $ofs, $correctentries)) {
            continue;   // I don't show the correct answers.
        }

        $query = new StdClass;
        $query->glossaryid = $game->glossaryid;
        $query->glossaryentryid = $entry->id;
        $s = '<b>A'.$ofs.'.</b> '.game_show_query( $game, $query, $entry->definition, 0).'<br>';
        if ($showsolution) {
            $s .= get_string( 'answer').': ';
            $s .= "<input type=\"text\" name=\"resp{$entry->id}\" value=\"$entry->concept\"size=30 /><br>";
        } else if ($onlyshow === false) {
            $s .= get_string( 'answer').': ';
            $s .= "<input type=\"text\" name=\"resp{$entry->id}\" size=30 /><br>";
        }
        echo $s."<hr>\r\n";
    }

    echo "</div>";

    // Finish the form.
    if ($hasquestions) {
        echo "<center><input type=\"submit\" name=\"submit\" value=\"".get_string('sudoku_submit', 'game')."\"></center>\n";
    }

    echo "</form>\n";
}

/**
 * Show question onfinish
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $sudoku
 */
function game_sudoku_showquestion_onfinish( $id, $game, $attempt, $sudoku) {
    if (!set_field( 'game_attempts', 'finish', 1, 'id', $attempt->id)) {
        print_error( "game_sudoku_showquestion_onfinish: Can't update game_attempts id=$attempt->id");
    }

    echo '<B>'.get_string( 'win', 'game').'</B><BR>';
    echo '<br>';
    echo "<a href=\"{$CFG->wwwroot}/mod/game/attempt.php?id=$id\">".
        get_string( 'nextgame', 'game').'</a> &nbsp; &nbsp; &nbsp; &nbsp; ';
    echo "<a href=\"{$CFG->wwwroot}?id=$id\">".get_string( 'finish', 'game').'</a> ';
}

/**
 * Check answers
 */
function game_sudoku_checkanswers() {
    $responses = data_submitted();

    $actions = question_extract_responses($questions, $responses, $event);
}

/**
 * Checks questions
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $sudoku
 * @param boolean $finishattempt
 * @param stdClass $course
 */
function game_sudoku_check_questions( $id, $game, $attempt, $sudoku, $finishattempt, $course) {
    global $DB;

    $responses = data_submitted();

    $offsetquestions = game_sudoku_compute_offsetquestions( $game->sourcemodule, $attempt, $numbers, $correctquestions);

    $questionlist = game_sudoku_getquestionlist( $offsetquestions);

    $questions = game_sudoku_getquestions( $questionlist);

    foreach ($questions as $question) {
        $query = new stdClass();

        $select = "attemptid=$attempt->id";
        $select .= " AND questionid=$question->id";

        if (($query->id = $DB->get_field_select( 'game_queries', 'id', $select)) == 0) {
            die( "problem game_sudoku_check_questions (select=$select)");
            continue;
        }

        $grade = game_grade_responses( $question, $responses, 100, $answertext, $answered);
        if ($answered == false) {
            continue;
        }
        if ($grade < 99) {
            // Wrong answer.
            game_update_queries( $game, $attempt, $query, $grade / 100, $answertext);
            continue;
        }

        // Correct answer.
        game_update_queries( $game, $attempt, $query, 1, $answertext);
    }

    game_sudoku_check_last( $id, $game, $attempt, $sudoku, $finishattempt, $course);
}

/**
 * Check glossary entries
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $sudoku
 * @param boolean $finishattempt
 * @param string $course
 */
function game_sudoku_check_glossaryentries( $id, $game, $attempt, $sudoku, $finishattempt, $course) {
    global $DB;

    $responses = data_submitted();

    // This function returns offsetentries, numbers, correctquestions.
    $offsetentries = game_sudoku_compute_offsetquestions( $game->sourcemodule, $attempt, $numbers, $correctquestions);

    $entrieslist = game_sudoku_getquestionlist( $offsetentries );

    // Load the glossary entries.
    if (!($entries = $DB->get_records_select( 'glossary_entries', "id IN ($entrieslist)"))) {
        print_error( get_string('noglossaryentriesfound', 'game'));
    }
    foreach ($entries as $entry) {
        $answerundefined = optional_param('resp'.$entry->id, 'undefined', PARAM_TEXT);
        if ($answerundefined == 'undefined') {
            continue;
        }
        $answer = optional_param('resp'.$entry->id, '', PARAM_TEXT);
        if ($answer == '') {
            continue;
        }
        if (game_upper( $entry->concept) != game_upper( $answer)) {
            continue;
        }
        // Correct answer.
        $select = "attemptid=$attempt->id";
        $select .= " AND glossaryentryid=$entry->id AND col>0";
        // Check the student guesses not source glossary entry.
        $select .= " AND questiontext is null";

        $query = new stdClass();
        if (($query->id = $DB->get_field_select( 'game_queries', 'id', $select)) == 0) {
            echo "not found $select<br>";
            continue;
        }

        game_update_queries( $game, $attempt, $query, 1, $answer);
    }

    game_sudoku_check_last( $id, $game, $attempt, $sudoku, $finishattempt, $course);

    return true;
}

/**
 * This is the last function after submiting the answers.
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $sudoku
 * @param boolean $finishattempt
 * @param stdClass $course
 */
function game_sudoku_check_last( $id, $game, $attempt, $sudoku, $finishattempt, $course) {
    global $CFG, $DB;

    $correct = $DB->get_field_select( 'game_queries', 'COUNT(*) AS c', "attemptid=$attempt->id AND score > 0.9");
    $all = $DB->get_field_select( 'game_queries', 'COUNT(*) AS c', "attemptid=$attempt->id");

    if ($all) {
        $grade = $correct / $all;
    } else {
        $grade = 0;
    }
    game_updateattempts( $game, $attempt, $grade, $finishattempt);
}

/**
 * Check number
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $sudoku
 * @param int $pos
 * @param int $num
 * @param stdClass $context
 */
function game_sudoku_check_number( $id, $game, $attempt, $sudoku, $pos, $num, $context) {
    global $DB;

    $correct = game_substr( $sudoku->data, $pos - 1, 1);

    if ($correct != $num) {
        game_sudoku_play( $id, $game, $attempt, $sudoku, false, false, $context);
        return;
    }

    $leng = game_strlen( $sudoku->guess);
    $lend = game_strlen( $sudoku->data);
    if ($leng < $lend) {
        $sudoku->guess .= str_repeat( ' ', $lend - $leng);
    }
    game_setchar( $sudoku->guess, $pos - 1, $correct);

    if (!$DB->set_field_select('game_sudoku', 'guess', $sudoku->guess, "id=$sudoku->id")) {
        print_error( 'game_sudoku_check_number: Cannot update table game_sudoku');
    }

    game_sudoku_play( $id, $game, $attempt, $sudoku, false, false, $context);
}
