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
 * Basic library.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Checks for common problems
 *
 * @param stdClass $context
 * @param stdClass $game
 */
function game_check_common_problems($context, $game) {
    if (!has_capability('mod/game:viewreports', $context)) {
        return '';
    }

    $warnings = array();

    switch( $game->gamekind) {
        case 'millionaire':
            game_check_common_problems_multichoice( $game, $warnings);
            break;
        case 'hangman':
            game_check_common_problems_shortanswer( $game, $warnings);
            break;
        case 'cross':
        case 'cryptex':
            game_check_common_problems_shortanswer( $game, $warnings);
            game_check_common_problems_crossword_cryptex( $game, $warnings);
            break;
    }

    if (count( $warnings) == 0) {
        return '';
    }

    $s = '<ul><b>'.get_string( 'common_problems', 'game').'</b>';
    foreach ($warnings as $line) {
        $s .= '<li>'.$line.'</li>';
    }

    return $s.'</ul>';
}

/**
 * Checks for common problems on multichoice answers
 *
 * @param stdClass $game
 * @param string $warnings
 */
function game_check_common_problems_multichoice($game, &$warnings) {

    if ($game->sourcemodule == 'question') {
        game_check_common_problems_multichoice_question($game, $warnings);
    } else if ( $game->sourcemodule == 'quiz') {
        game_check_common_problems_multichoice_quiz($game, $warnings);
    }
}

/**
 * Checks for common problems on multichoice answers (questions)
 *
 * @param stdClass $game
 * @param string $warnings
 */
function game_check_common_problems_multichoice_question($game, &$warnings) {
    global $CFG, $DB;

    if ($game->questioncategoryid == 0) {
        $warnings[] = get_string( 'must_select_questioncategory', 'game');
        return;
    }

    // Include subcategories.
    $select = 'category='.$game->questioncategoryid;
    if ($game->subcategories) {
        $cats = question_categorylist( $game->questioncategoryid);
        if (count( $cats)) {
            $select = 'q.category in ('.implode(',', $cats).')';
        }
    }
    $select0 = $select;

    if (game_get_moodle_version() < '02.06') {
        $table = "{$CFG->prefix}question q, {$CFG->prefix}question_multichoice qmo";
        $select .= " AND qtype='multichoice' AND qmo.single <> 1 AND qmo.question=q.id";
    } else {
         $table = "{$CFG->prefix}question q, {$CFG->prefix}qtype_multichoice_options qmo";
        $select .= " AND qtype='multichoice' AND qmo.single <> 1 AND qmo.questionid=q.id";
    }

    $sql = "SELECT COUNT(*) as c FROM $table WHERE $select";
    $rec = $DB->get_record_sql( $sql);
    if ($rec->c != 0) {
        $warnings[] = get_string( 'millionaire_also_multichoice', 'game').': '.$rec->c;
    }

    $select = $select0;
    if (game_get_moodle_version() < '02.06') {
        $select .= " AND qtype='multichoice' AND qmo.single = 1 AND qmo.question=q.id";
    } else {
        $select .= " AND qtype='multichoice' AND qmo.single = 1 AND qmo.questionid=q.id";
    }

    $sql = "SELECT COUNT(*) as c FROM $table WHERE $select";
    $rec = $DB->get_record_sql( $sql);
    if ($rec->c == 0) {
        $warnings[] = get_string( 'millionaire_no_multichoice_questions', 'game');
    }
}

/**
 * Checks for common problems on multichoice answers (quiz)
 *
 * @param stdClass $game
 * @param string $warnings
 */
function game_check_common_problems_multichoice_quiz($game, &$warnings) {
    global $CFG, $DB;

    if (game_get_moodle_version() < '02.06') {
        $select = "qtype='multichoice' AND quiz='$game->quizid' AND qmo.question=q.id".
        " AND qqi.question=q.id";
        $table = "{quiz_question_instances} qqi,{question} q, {question_multichoice} qmo";
    } else if (game_get_moodle_version() < '02.07') {
        $select = "qtype='multichoice' AND quiz='$game->quizid' AND qmo.questionid=q.id".
        " AND qqi.question=q.id";
        $table = "{quiz_question_instances} qqi,{question} q, {qtype_multichoice_options} qmo";
    } else {
        $select = "qtype='multichoice' AND qs.quizid='$game->quizid' AND qmo.questionid=q.id".
        " AND qs.questionid=q.id";
        $table = "{quiz_slots} qs,{question} q, {qtype_multichoice_options} qmo";
    }

    $sql = "SELECT COUNT(*) as c FROM $table WHERE $select";
    $rec = $DB->get_record_sql( $sql);
    if ($rec->c != 0) {
        $warnings[] = get_string( 'millionaire_also_multichoice', 'game').': '.$rec->c;
    }
}

/**
 * Checks for common problems on short answers
 *
 * @param stdClass $game
 * @param string $warnings
 */
function game_check_common_problems_shortanswer($game, &$warnings) {
    if ($game->sourcemodule == 'question') {
        game_check_common_problems_shortanswer_question($game, $warnings);
    } else if ( $game->sourcemodule == 'glossary') {
        game_check_common_problems_shortanswer_glossary($game, $warnings);
    }
}

/**
 * Checks for common problems on short answers (glossaries)
 *
 * @param stdClass $game
 * @param string $warnings
 */
function game_check_common_problems_shortanswer_glossary($game, &$warnings) {

    global $CFG, $DB;

    $sql = "SELECT id,concept FROM {$CFG->prefix}glossary_entries WHERE glossaryid={$game->glossaryid}";
    $recs = $DB->get_records_sql( $sql);
    $a = array();
    foreach ($recs as $rec) {
        $a[] = $rec->concept;
    }

    game_check_common_problems_shortanswer_allowspaces( $game, $warnings, $a);
    if ($game->gamekind == 'hangman') {
        game_check_common_problems_shortanswer_hangman( $game, $warnings, $a);
    }
}

/**
 * Checks for common problems on short answers (questions)
 *
 * @param stdClass $game
 * @param string $warnings
 */
function game_check_common_problems_shortanswer_question($game, &$warnings) {

    global $CFG, $DB;

    if ($game->questioncategoryid == 0) {
        $warnings[] = get_string( 'must_select_questioncategory', 'game');
        return;
    }

    $select = 'category='.$game->questioncategoryid;
    if ($game->subcategories) {
        $cats = question_categorylist( $game->questioncategoryid);
        if (count( $cats) > 0) {
            $s = implode( ',', $cats);
            $select = 'category in ('.$s.')';
        }
    }
    $select .= " AND qtype='shortanswer'";

    $sql = "SELECT id FROM {$CFG->prefix}question WHERE $select";
    if (($recs = $DB->get_records_sql( $sql)) === false) {
        return;
    }
    $a = array();
    foreach ($recs as $rec) {
        // Maybe there are more answers to one question. I use as correct the one with bigger fraction.
        $sql = "SELECT DISTINCT answer, fraction ".
        "FROM {$CFG->prefix}question_answers WHERE question={$rec->id} ORDER BY fraction DESC";
        $recs2 = $DB->get_records_sql( $sql);
        foreach ($recs2 as $rec2) {
            $a[] = $rec2->answer;
            break;
        }
    }
    game_check_common_problems_shortanswer_allowspaces( $game, $warnings, $a);
    if ($game->gamekind == 'hangman') {
        game_check_common_problems_shortanswer_hangman( $game, $warnings, $a);
    }
}

/**
 * Checks for common problems (check if are answers with spaces and the game doesn't allow spaces)
 *
 * @param stdClass $game
 * @param string $warnings
 * @param array $a the words contained
 */
function game_check_common_problems_shortanswer_allowspaces( $game, &$warnings, $a) {
    if ($game->param7 != 0) {
        // Allow spaces, so no check is needed.
        return;
    }

    $ret = array();
    foreach ($a as $word) {
        if (strpos( $word, ' ') === false) {
            continue;
        }
        $ret[] = $word;
    }

    if (count( $ret) != 0) {
        $warnings[] = get_string( 'common_problems_allowspaces', 'game').': '.count($ret).' ('.implode( ', ', $ret).')';
    }
}

/**
 * Checks for common problems (check if are answers with spaces and the game doesn't allow spaces)
 *
 * @param stdClass $game
 * @param string $warnings
 * @param array $a the words contained
 */
function game_check_common_problems_shortanswer_hangman( $game, &$warnings, $a) {
    $ret = array();
    foreach ($a as $word) {

        $word = game_upper( str_replace( ' ', '', $word), $game->language);
        if ($game->language == '') {
            $game->language = game_detectlanguage( $word);
            $word = game_upper( $word, $game->language);
        }
        $allletters = game_getallletters( $word, $game->language, $game->userlanguage);

        if ($allletters != '') {
            continue;
        }

        $ret[] = $word;
    }

    if (count( $ret) != 0) {
        $warnings[] = get_string( 'common_problems_shortanswer_hangman', 'game').': '.count($ret).' ('.implode( ', ', $ret).')';
    }
}

/**
 * Checks for common problems (check crossword/cryptex parameters)
 *
 * @param stdClass $game
 * @param string $warnings
 */
function game_check_common_problems_crossword_cryptex($game, &$warnings) {

    global $CFG, $DB;

    if (($game->param1 < 10) && ($game->param1 > 0)) {
        $warnings[] = get_string( 'common_problems_crossword_param1', 'game').' (='.$game->param1.')';
    }
}

