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
 * This file plays the game hangman.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Plays the game hangman
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $hangman
 * @param string $newletter
 * @param string $action
 * @param stdClass $context
 */
function game_hangman_continue( $id, $game, $attempt, $hangman, $newletter, $action, $context) {
    global $DB, $USER;

    if ($attempt != false and $hangman != false) {
        if (($action == 'nextword') and ($hangman->finishedword != 0)) {
            // Finish with one word and continue to another.
            if (!$DB->set_field( 'game_hangman', 'finishedword', 0, array( 'id' => $hangman->id))) {
                error( "game_hangman_continue: Can't update game_hangman");
            }
        } else {
            return game_hangman_play( $id, $game, $attempt, $hangman, false, false, $context);
        }
    }

    $updatehangman = (($attempt != false) and ($hangman != false));

    // New game.
    srand ((double)microtime() * 1000003);

    // I try 10 times to find a new question.
    $found = false;
    $minnum = 0;
    $unchanged = 0;
    for ($i = 1; $i <= 10; $i++) {
        $rec = game_question_shortanswer( $game, $game->param7, false);
        if ($rec === false) {
            continue;
        }

        $answer = game_upper( $rec->answertext, $game->language);
        if ($game->language == '') {
            $game->language = game_detectlanguage( $answer);
            $answer = game_upper( $rec->answertext, $game->language);
        }

        $answer2 = $answer;
        if ($game->param7) {
            // Have to delete space.
            $answer2 = str_replace( ' ', '', $answer2);
        }

        if ($game->param8) {
            // Have to delete -.
            $answer2 = str_replace( '-', '', $answer2);
        }

        $allletters = game_getallletters( $answer2, $game->language, $game->userlanguage);

        if ($allletters == '') {
            continue;
        }

        if ($game->param7) {
            $allletters .= '_';
        }

        if ($game->param8) {
            $allletters .= '-';
        }

        if ($game->param7 == false) {
            // I don't allow spaces.
            if (strpos( $answer, " ")) {
                continue;
            }
        }

        $copy = false;
        $select2 = 'gameid=? AND userid=? AND questionid=? AND glossaryentryid=?';
        if (($rec2 = $DB->get_record_select( 'game_repetitions', $select2,
            array( $game->id, $USER->id, $rec->questionid, $rec->glossaryentryid), 'id,repetitions AS r')) != false) {
            if (($rec2->r < $minnum) or ($minnum == 0)) {
                $minnum = $rec2->r;
                $copy = true;
            }
        } else {
            $minnum = 0;
            $copy = true;
        }

        if ($copy) {
            $found = true;

            $min = new stdClass();
            $min->questionid = $rec->questionid;
            $min->glossaryentryid = $rec->glossaryentryid;
            $min->attachment = $rec->attachment;
            $min->questiontext = $rec->questiontext;
            $min->answerid = $rec->answerid;
            $min->answer = $answer;
            $min->language = $game->language;
            $min->allletters = $allletters;
            if ($minnum == 0) {
                break;  // We found an unused word.
            }
        } else {
            $unchanged++;
        }

        if ($unchanged > 2) {
            if ($found) {
                break;
            }
        }
    }

    if ($found == false) {
        print_error( get_string( 'no_words', 'game'));
    }

    // Found one word for hangman.
    if ($attempt == false) {
        $attempt = game_addattempt( $game);
    }

    if (!$DB->set_field( 'game_attempts', 'language', $min->language, array( 'id' => $attempt->id))) {
        print_error( "game_hangman_continue: Can't set language");
    }

    $query = new stdClass();
    $query->attemptid = $attempt->id;
    $query->gamekind = $game->gamekind;
    $query->gameid = $game->id;
    $query->userid = $USER->id;
    $query->sourcemodule = $game->sourcemodule;
    $query->questionid = $min->questionid;
    $query->glossaryentryid = $min->glossaryentryid;
    $query->attachment = $min->attachment;
    $query->questiontext = addslashes( $min->questiontext);
    $query->score = 0;
    $query->timelastattempt = time();
    $query->answertext = $min->answer;
    $query->answerid = $min->answerid;
    if (!($query->id = $DB->insert_record( 'game_queries', $query))) {
        print_error( "game_hangman_continue: Can't insert to table game_queries");
    }

    $newrec = new stdClass();
    $newrec->id = $attempt->id;
    $newrec->queryid = $query->id;
    if ($updatehangman == false) {
        $newrec->maxtries = $game->param4;
        if ($newrec->maxtries == 0) {
            $newrec->maxtries = 1;
        }
        $newrec->finishedword = 0;
        $newrec->corrects = 0;
    }

    $newrec->allletters = $min->allletters;

    $letters = '';
    if ($game->param1) {
        $letters .= game_substr( $min->answer, 0, 1);
    }

    if ($game->param2) {
        $letters .= game_substr( $min->answer, -1, 1);
    }
    $newrec->letters = $letters;

    if ($updatehangman == false) {
        if (!game_insert_record(  'game_hangman', $newrec)) {
            print_error( 'game_hangman_continue: error inserting in game_hangman');
        }
    } else {
        if (!$DB->update_record(  'game_hangman', $newrec)) {
            print_error( 'game_hangman_continue: error updating in game_hangman');
        }
        $newrec = $DB->get_record( 'game_hangman', array( 'id' => $newrec->id));
    }

    game_update_repetitions( $game->id, $USER->id, $query->questionid, $query->glossaryentryid);

    game_hangman_play( $id, $game, $attempt, $newrec, false, false, $context);
}

/**
 * On finish game.
 *
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $hangman
 */
function game_hangman_onfinishgame( $game, $attempt, $hangman) {
    global $DB;

    $score = $hangman->corrects / $hangman->maxtries;

    game_updateattempts( $game, $attempt, $score, true);

    if (!$DB->set_field( 'game_hangman', 'finishedword', 0, array( 'id' => $hangman->id))) {
        print_error( "game_hangman_onfinishgame: Can't update game_hangman");
    }
}

/**
 * Plays the hangman game.
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $hangman
 * @param boolean $onlyshow
 * @param boolean $showsolution
 * @param stdClass $context
 */
function game_hangman_play( $id, $game, $attempt, $hangman, $onlyshow, $showsolution, $context) {
    global $CFG, $DB, $OUTPUT;

    $query = $DB->get_record( 'game_queries', array( 'id' => $hangman->queryid));

    if ($attempt->language != '') {
        $wordrtl = game_right_to_left( $attempt->language);
    } else {
        $wordrtl = right_to_left();
    }
    $reverseprint = ($wordrtl != right_to_left());

    if ($game->toptext != '') {
        echo $game->toptext.'<br>';
    }

    $max = $game->param10;  // Maximum number of wrongs.
    if ($max <= 0) {
        $max = 6;
    }
    hangman_showpage( $done, $correct, $wrong, $max, $wordline, $wordline2, $links, $game,
        $attempt, $hangman, $query, $onlyshow, $showsolution, $context);

    if (!$done) {
        if ($wrong > $max) {
            $wrong = $max;
        }
        if ($game->param3 == 0) {
            $game->param3 = 1;
        }
        echo "\r\n<br/><img src=\"".game_pix_url('hangman/'.$game->param3.'/hangman_'.$wrong, 'mod_game')."\"";
        $message  = sprintf( get_string( 'hangman_wrongnum', 'game'), $wrong, $max);
        echo ' ALIGN="MIDDLE" BORDER="0" HEIGHT="100" alt="'.$message.'"/>';

        if ($wrong >= $max) {
            // This word is incorrect. If reach the max number of word I have to finish else continue with next word.
            hangman_onincorrect( $id, $wordline, $query->answertext, $game, $attempt, $hangman, $onlyshow, $showsolution);
        } else {
            $i = $max - $wrong;
            if ($i > 1) {
                echo ' '.get_string( 'hangman_restletters_many', 'game', $i);
            } else {
                echo ' '.get_string( 'hangman_restletters_one', 'game');
            }
            if ($reverseprint) {
                echo '<SPAN dir="'.($wordrtl ? 'rtl' : 'ltr').'">';
            }

            echo "<br/><font size=\"5\">\n$wordline</font>\r\n";
            if ($wordline2 != '') {
                echo "<br/><font size=\"5\">\n$wordline2</font>\r\n";
            }

            if ($reverseprint) {
                echo "</SPAN>";
            }

            if ($hangman->finishedword == false) {
                echo "<br/><br/><BR/>".get_string( 'hangman_letters', 'game').' '.$links."\r\n";
            }
        }
    } else {
        // This word is correct. If reach the max number of word I have to finish else continue with next word.
        hangman_oncorrect( $id, $wordline, $game, $attempt, $hangman, $query);
    }

    echo "<br/><br/>".get_string( 'grade', 'game').' : '.round( $query->percent * 100).' %';
    if ($hangman->maxtries > 1) {
        $percent = ($correct - $wrong / $max) / game_strlen( $query->answertext);
        if ($done) {
            $percent = 0;
        }
        $score = $hangman->corrects / $hangman->maxtries + $percent / $hangman->maxtries;
        echo '<br/><br/>'.get_string( 'hangman_gradeinstance', 'game').' : '.
            round( $score * 100).' %';
    }

    if ($game->bottomtext != '') {
        echo '<br><br>'.$game->bottomtext;
    }
}

/**
 * Shows page.
 *
 * @param boolean $done
 * @param boolean $correct
 * @param boolean $wrong
 * @param int $max
 * @param string $wordline
 * @param string $wordline2
 * @param array $links
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $hangman
 * @param stdClass $query
 * @param boolean $onlyshow
 * @param boolean $showsolution
 * @param stdClass $context
 */
function hangman_showpage(&$done, &$correct, &$wrong, $max, &$wordline, &$wordline2, &$links,
    $game, &$attempt, &$hangman, &$query, $onlyshow, $showsolution, $context) {
    global $USER, $CFG, $DB;

    $id = optional_param('id', 0, PARAM_INT); // Course Module ID.

    $word = $query->answertext;

    $newletter  = optional_param('newletter', "", PARAM_TEXT);
    if ( $newletter == '_') {
        $newletter = ' ';
    }

    $letters = $hangman->letters;
    if ($newletter != null) {
        if (game_strpos( $letters, $newletter) === false) {
            $letters .= $newletter;
        }
    }

    $links = "";

    $alpha = $hangman->allletters;
    $wrong = 0;

    if ($query->questionid) {
        $questiontext = str_replace( array("\'", '\"'), array("'", '"'), $query->questiontext);
        $query->questiontext = game_filterquestion($questiontext, $query->questionid, $context->id, $game->course);
    } else {
        $glossary = $DB->get_record_sql( "SELECT id,course FROM {$CFG->prefix}glossary WHERE id={$game->glossaryid}");
        $cmglossary = get_coursemodule_from_instance('glossary', $game->glossaryid, $glossary->course);
        $contextglossary = game_get_context_module_instance( $cmglossary->id);
        $query->questiontext = game_filterglossary(str_replace( '\"', '"',
            $query->questiontext), $query->glossaryentryid, $contextglossary->id, $game->course);
    }

    if ($game->param5) {
        $s = trim( game_filtertext( $query->questiontext, $game->course));
        if ($s != '.' and $s <> '') {
            echo "<br/><b>".$s.'</b>';
        }
        if ($query->attachment != '') {
            $file = "{$CFG->wwwroot}/file.php/$game->course/moddata/$query->attachment";
            echo "<img src=\"$file\" />";
        }
        echo "<br/><br/>";
    }

    $wordline = $wordline2 = "";

    $len = game_strlen( $word);

    $done = 1;
    $answer = '';
    $correct = 0;
    for ($x = 0; $x < $len; $x++) {
        $char = game_substr( $word, $x, 1);

        if ($showsolution) {
            $wordline2 .= ( $char == " " ? '&nbsp; ' : $char);
            $done = 0;
        }

        if (game_strpos($letters, $char) === false) {
            $wordline .= "_<font size=\"1\">&nbsp;</font>\r\n";
            $done = 0;
            $answer .= '_';
        } else {
            $wordline .= ( $char == " " ? '&nbsp; ' : $char);
            $answer .= $char;
            $correct++;
        }
    }

    $lenalpha = game_strlen( $alpha);
    $fontsize = 5;

    for ($c = 0; $c < $lenalpha; $c++) {
        $char = game_substr( $alpha, $c, 1);

        if (game_strpos($letters, $char) === false) {
            // User doesn't select this character.
            $params = 'id='.$id.'&amp;newletter='.urlencode( $char);
            if ($onlyshow or $showsolution) {
                $links .= $char;
            } else {
                $links .= "<font size=\"$fontsize\"><a href=\"attempt.php?$params\">$char</a></font>\r\n";
            }
            continue;
        }

        if (game_strpos($word, $char) === false) {
            $links .= "\r\n<font size=\"$fontsize\" color=\"red\">$char </font>";
            $wrong++;
        } else {
            $links .= "\r\n<B><font size=\"$fontsize\">$char </font></B> ";
        }
    }
    $finishedword = ($done or $wrong >= $max);
    $finished = false;

    $updrec = new stdClass();
    $updrec->id = $hangman->id;
    $updrec->letters = $letters;
    if ($finishedword) {
        if ($hangman->finishedword == 0) {
            // Only one time per word increace the variable try.
            $hangman->try = $hangman->try + 1;
            if ($hangman->try > $hangman->maxtries) {
                $finished = true;
            }
            if ($done) {
                $hangman->corrects = $hangman->corrects + 1;
                $updrec->corrects = $hangman->corrects;
            }
        }

        $updrec->try = $hangman->try;
        $updrec->finishedword = 1;
    }

    $query->percent = ($correct - $wrong / $max) / game_strlen( $word);
    if ($query->percent < 0) {
        $query->percent = 0;
    }

    if ($onlyshow or $showsolution) {
        return;
    }

    if (!$DB->update_record( 'game_hangman', $updrec)) {
        print_error( "hangman_showpage: Can't update game_hangman id=$updrec->id");
    }

    if ($done) {
        $score = 1;
    } else if ($wrong >= $max) {
        $score = 0;
    } else {
        $score = -1;
    }
    if ($hangman->maxtries > 0) {
        $percent = ($correct - $wrong / $max) / game_strlen( $word);
        $score = $hangman->corrects / $hangman->maxtries + $percent / $hangman->maxtries;
    }
    game_updateattempts( $game, $attempt, $score, $finished);
    game_update_queries( $game, $attempt, $query, $score, $answer);
}

/**
 * This word is correct. If reach the max number of words I have to finish else continue with next word.
 *
 * @param int $id
 * @param string $wordline
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $hangman
 * @param stdClass $query
 */
function hangman_oncorrect( $id, $wordline, $game, $attempt, $hangman, $query) {
    global $DB;

    echo "<br/><br/><font size=\"5\">\n$wordline</font>\r\n";

    echo '<p><br/><font size="5" color="green">'.get_string( 'win', 'game').'</font><BR/><BR/></p>';
    if ($query->answerid) {
        $feedback = $DB->get_field( 'question_answers', 'feedback', array( 'id' => $query->answerid));
        if ($feedback != '') {
            echo "$feedback<br>";
        }
    }

    game_hangman_show_nextword( $id, $game, $attempt, $hangman);
}

/**
 * On incorrect.
 *
 * @param int $id
 * @param string $wordline
 * @param string $word
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $hangman
 * @param boolean $onlyshow
 * @param boolean $showsolution
 */
function hangman_onincorrect( $id, $wordline, $word, $game, $attempt, $hangman, $onlyshow, $showsolution) {
    echo "\r\n<br/><br/><font size=\"5\">\n$wordline</font>\r\n";

    if ( $onlyshow or $showsolution) {
        return;
    }

    echo '<p><BR/><font size="5" color="red">'.get_string( 'hangman_loose', 'game').'</font><BR/><BR/></p>';

    if ($game->param6) {
        // Show the correct answer.
        if (game_strpos($word, ' ') != false) {
            echo '<br/>'.get_string( 'hangman_correct_phrase', 'game');
        } else {
            echo '<br/>'.get_string( 'hangman_correct_word', 'game');
        }
        echo '<B>'.$word."</B><BR/><BR/>\r\n";
    }

    game_hangman_show_nextword( $id, $game, $attempt, $hangman, true);
}

/**
 * Shows the next word.
 *
 * @param int $id
 * @param stdClass $game
 * @param stdClass $attempt
 * @param stdClass $hangman
 */
function game_hangman_show_nextword( $id, $game, $attempt, $hangman) {
    global $CFG, $DB;

    echo '<br/>';
    if (($hangman->try < $hangman->maxtries) or ($hangman->maxtries == 0)) {
        // Continue to next word.
        $params = "id=$id&action2=nextword\">".get_string( 'nextword', 'game').'</a> &nbsp; &nbsp; &nbsp; &nbsp;';
        echo "<a href=\"{$CFG->wwwroot}/mod/game/attempt.php?$params";
    } else {
        game_hangman_onfinishgame( $game, $attempt, $hangman);

        if (game_can_start_new_attempt( $game)) {
            echo "<a href=\"{$CFG->wwwroot}/mod/game/attempt.php?id=$id\">".
                get_string( 'nextgame', 'game').'</a> &nbsp; &nbsp; &nbsp; &nbsp; ';
        }
    }

    if (! $cm = $DB->get_record('course_modules', array( 'id' => $id))) {
        print_error( "Course Module ID was incorrect id=$id");
    }

    echo "<a href=\"{$CFG->wwwroot}/course/view.php?id=$cm->course\">".get_string( 'finish', 'game').'</a> ';
}
