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

// Include those library functions that are also used by core Moodle or other modules.
require_once($CFG->dirroot . '/mod/game/lib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// CONSTANTS.
/*
* Options determining how the grades from individual attempts are combined to give
* the overall grade for a user
*/

define( "GAME_GRADEMETHOD_HIGHEST", "1");
define( "GAME_GRADEMETHOD_AVERAGE", "2");
define( "GAME_GRADEMETHOD_FIRST",   "3");
define( "GAME_GRADEMETHOD_LAST",    "4");

$gamegrademethod = array ( GAME_GRADEMETHOD_HIGHEST => get_string("gradehighest", "game"),
                             GAME_GRADEMETHOD_AVERAGE => get_string("gradeaverage", "game"),
                             GAME_GRADEMETHOD_FIRST => get_string("attemptfirst", "game"),
                             GAME_GRADEMETHOD_LAST  => get_string("attemptlast", "game"));

define( "CONST_GAME_TRIES_REPETITION", "5");

/**
 * Returns the version of Moodle.
 *
 * @return string version
 */
function game_get_moodle_version() {
    global $DB;

    static $smoodleversion = null;

    if ($smoodleversion != null) {
        return $smoodleversion;
    }

    $rec = $DB->get_record_select( 'config', "name='release'");
    if ($rec == false) {
        return $smoodleversion = '';
    } else {
        $a = explode( '.', $rec->value);
        return $smoodleversion = sprintf( '%02u.%02u', $a[ 0], $a[ 1]);
    }
}

/**
 * Convert a string to upper.
 *
 * @param string $str
 * @param string $lang
 * @return string the uppercase of $str
 */
function game_upper( $str, $lang='') {
    if ($lang == 'user') {
        return $str;
    }

    $str = game_strtoupper( $str);

    switch ($lang) {
        case 'el':
            $from = 'ΆΈΉΊΌΎΏ';
            $to = 'ΑΕΗΙΟΥΩ';
            break;
        default:
            return $str;
    }

    $len = game_strlen( $from);
    for ($i = 0; $i < $len; $i++) {
        $str = str_replace( game_substr( $from, $i, 1), game_substr( $to, $i, 1), $str);
    }

    return $str;
}

/**
 * Returns the HTML of a select control.
 *
 * @param string $name
 * @param array $a
 * @param string $input
 * @param array $events
 *
 * @return string the HTML
 */
function game_showselectcontrol( $name, $a,  $input, $events='') {
    $ret = "<select id=\"$name\" name=\"$name\" $events>";

    foreach ($a as $key => $caption) {
        $ret .= '<option value="'.$key.'" ';
        if ($key == $input) {
            $ret .= ' selected="selected" ';
        }
        $ret .= '>'.$caption."</option>\r\n";
    }
    $ret .= "</select>\r\n";

    return $ret;
}

/**
 * Returns the HTML of a checkbox control.
 *
 * @param string $name
 * @param string $value
 * @return string the HTML
 */
function game_showcheckbox( $name, $value) {
    $a = array();
    $a[ 0] = get_string( 'no');
    $a[ 1] = get_string( 'yes');

    return game_showselectcontrol( $name, $a, $value);
}

/**
 * Used by hangman. Returns a short answer.
 *
 * @param stdClass $game
 * @param boolean $allowspaces
 * @param boolean $userepetitions
 *
 * @return string the HTML
 */
function game_question_shortanswer( $game, $allowspaces=false, $userepetitions=true) {
    switch( $game->sourcemodule)
    {
        case 'glossary':
            return game_question_shortanswer_glossary( $game, $allowspaces, $userepetitions);
        case 'quiz':
            return game_question_shortanswer_quiz( $game, $allowspaces, $userepetitions);
        case 'question':
            return game_question_shortanswer_question( $game, $allowspaces, $userepetitions);
    }

    return false;
}

/**
 * Used by hangman. Returns a short answer from glossary.
 *
 * @param stdClass $game
 * @param boolean $allowspaces
 * @param boolean $userepetitions
 *
 */
function game_question_shortanswer_glossary( $game, $allowspaces, $userepetitions) {
    global $DB;

    if ($game->glossaryid == 0) {
        print_error( get_string( 'must_select_glossary', 'game'));
    }

    $select = "glossaryid={$game->glossaryid}";
    $table = '{glossary_entries} ge';
    if ($game->glossarycategoryid) {
        $table .= ',{glossary_entries_categories} gec';
        $select .= ' AND gec.entryid = ge.id '.
            " AND gec.categoryid = {$game->glossarycategoryid}";
    }
    if ($allowspaces == false) {
        $select .= " AND concept NOT LIKE '% %'  ";
    }

    if ($game->glossaryonlyapproved) {
        // Only approved glossary entries will be used.
        $select .= ' AND (ge.approved=1 OR teacherentry=1)';
    }

    if (($id = game_question_selectrandom( $game, $table, $select, 'ge.id', $userepetitions)) == false) {
        return false;
    }

    $sql = 'SELECT id, concept as answertext, definition as questiontext, id as glossaryentryid, '.
            ' 0 as questionid, glossaryid, attachment, 0 as answerid'.
           " FROM {glossary_entries} ge WHERE id = $id";
    if (($rec = $DB->get_record_sql( $sql)) == false) {
        return false;
    }

    if ( $rec->attachment != '') {
        $rec->attachment = "glossary/{$game->glossaryid}/$rec->id/$rec->attachment";
    }

    return $rec;
}

/**
 * Used by hangman. Returns a short answer from quiz.
 *
 * @param stdClass $game
 * @param boolean $allowspaces
 * @param boolean $userepetitions
 */
function game_question_shortanswer_quiz( $game, $allowspaces, $userepetitions) {
    global $DB;

    if ($game->quizid == 0) {
        print_error( get_string( 'must_select_quiz', 'game'));
    }

    if (game_get_moodle_version() < '02.07') {
        $select = "qtype='shortanswer' AND quiz='$game->quizid' ".
            " AND qqi.question=q.id";
        $table = "{question} q,{quiz_question_instances} qqi";
    } else {
        $select = "qtype='shortanswer' AND qs.quizid='$game->quizid' ".
            " AND qs.questionid=q.id";
        $table = "{question} q,{quiz_slots} qs";
    }
    $fields = "q.id";

    if (($id = game_question_selectrandom( $game, $table, $select, $fields, $userepetitions)) == false) {
        return false;
    }

    $select = "q.id=$id AND qa.question=$id".
        " AND q.hidden=0 AND qtype='shortanswer'";
    $table = "{question} q,{question_answers} qa";
    $fields = "qa.id as answerid, q.id, q.questiontext as questiontext, ".
        "qa.answer as answertext, q.id as questionid, ".
        "0 as glossaryentryid, '' as attachment";

    // Maybe there are more answers to one question. I use as correct the one with bigger fraction.
    $sql = "SELECT $fields FROM $table WHERE $select ORDER BY fraction DESC";
    if (($recs = $DB->get_records_sql( $sql, null, 0, 1)) == false) {
        return false;
    }
    foreach ($recs as $rec) {
        return $rec;
    }
}

/**
 * Used by hangman. Returns a short answer from question.
 *
 * @param stdClass $game
 * @param boolean $allowspaces
 * @param boolean $userepetitions
 *
 * @return the short answer record
 */
function game_question_shortanswer_question( $game, $allowspaces, $userepetitions) {
    global $DB;

    if ($game->questioncategoryid == 0) {
        print_error( get_string( 'must_select_questioncategory', 'game'));
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

    $table = '{question} q';
    $fields = 'q.id';

    if (($id = game_question_selectrandom( $game, $table, $select, $fields, $userepetitions)) == false) {
        return false;
    }

    $select = "q.id=$id AND qa.question=$id".
        " AND q.hidden=0 AND qtype='shortanswer'";
    $table = "{question} q,{question_answers} qa";
    $fields = "qa.id as answerid, q.id, q.questiontext as questiontext, ".
        "qa.answer as answertext, q.id as questionid, ".
        "0 as glossaryentryid, '' as attachment";

    // Maybe there are more answers to one question. I use as correct the one with bigger fraction.
    $sql = "SELECT $fields FROM $table WHERE $select ORDER BY fraction DESC";
    if (($recs = $DB->get_records_sql( $sql, null, 0, 1)) == false) {
        return false;
    }
    foreach ($recs as $rec) {
        return $rec;
    }
}

/**
 * Select a random question. Used by millionaire, game_question_shortanswer_quiz, hidden picture.
 *
 * @param stdClass $game
 * @param string $table
 * @param string $select
 * @param string $idfields
 * @param boolean $userepetitions
 *
 * @return stdClass the random question
 */
function game_question_selectrandom( $game, $table, $select, $idfields='id', $userepetitions=true) {
    global $DB, $USER;

    $count = $DB->get_field_sql( "SELECT COUNT(*) FROM $table WHERE $select");

    if ($count == 0) {
        return false;
    }

    $minnum = 0;
    $minid = 0;
    for ($i = 1; $i <= CONST_GAME_TRIES_REPETITION; $i++) {
        $sel = mt_rand(0, $count - 1);

        $sql  = "SELECT $idfields, $idfields FROM ".$table." WHERE $select";
        if (($recs = $DB->get_records_sql( $sql, null, $sel, 1)) == false) {
            return false;
        }

        $id = 0;
        foreach ($recs as $rec) {
            $id = $rec->id;
        }
        if ($minid == 0) {
            $minid = $id;
        }

        if ($userepetitions == false) {
            return $id;
        }

        if ($count == 1) {
            break;
        }

        $questionid = $glossaryentryid = 0;
        if ($game->sourcemodule == 'glossary') {
            $glossaryentryid = $id;
        } else {
            $questionid = $id;
        }

        $a = array( 'gameid' => $game->id, 'userid' => $USER->id, 'questionid' => $questionid,
            'glossaryentryid' => $glossaryentryid);
        if (($rec = $DB->get_record( 'game_repetitions', $a, 'id,repetitions AS r')) != false) {
            if (($rec->r < $minnum) or ($minnum == 0)) {
                $minnum = $rec->r;
                $minid = $id;
            }
        } else {
            $minid = $questionid;
            break;
        }
    }

    if ($game->sourcemodule == 'glossary') {
        game_update_repetitions( $game->id, $USER->id, 0, $minid);
    } else {
        game_update_repetitions( $game->id, $USER->id, $minid, 0);
    }

    return $minid;
}


/**
 * Updates the repetition table.
 *
 * @param int $gameid
 * @param int $userid
 * @param int $questionid
 * @param int $glossaryentryid
 */
function game_update_repetitions( $gameid, $userid, $questionid, $glossaryentryid) {
    global $DB;

    $a = array( 'gameid' => $gameid, 'userid' => $userid, 'questionid' => $questionid, 'glossaryentryid' => $glossaryentryid);
    if (($rec = $DB->get_record( 'game_repetitions', $a, 'id,repetitions AS r')) != false) {
        $updrec = new stdClass();
        $updrec->id = $rec->id;
        $updrec->repetitions = $rec->r + 1;
        if (!$DB->update_record( 'game_repetitions', $updrec)) {
            print_error("Update page: can't update game_repetitions id={$updrec->id}");
        }
    } else {
        $newrec = new stdClass();
        $newrec->gameid = $gameid;
        $newrec->userid = $userid;
        $newrec->questionid = $questionid;
        $newrec->glossaryentryid = $glossaryentryid;
        $newrec->repetitions = 1;

        if ($newrec->questionid == '') {
            $newrec->questionid = 0;
        }
        if ($newrec->glossaryentryid == '') {
            $newrec->glossaryentryid = 0;
        }
        if (!$DB->insert_record( 'game_repetitions', $newrec)) {
            print_error("Insert page: new page game_repetitions not inserted");
        }
    }
}

/**
 * Select random questions for Sudoku.
 *
 * @param stdClass $game
 * @param int $count
 *
 * @return stdClass the random record(s)
 */
function game_questions_selectrandom( $game, $count=1) {
    global $DB;

    switch( $game->sourcemodule)
    {
        case 'quiz':
            if ($game->quizid == 0) {
                print_error( get_string( 'must_select_quiz', 'game'));
            }
            if (game_get_moodle_version() < '02.07') {
                $table = '{question} q, {quiz_question_instances} qqi';
                $select = " qqi.quiz=$game->quizid".
                    " AND qqi.question=q.id ".
                    " AND q.qtype in ('shortanswer', 'truefalse', 'multichoice')".
                    " AND q.hidden=0";
            } else {
                $table = '{question} q, {quiz_slots} qs';
                $select = " qs.quizid=$game->quizid".
                    " AND qs.questionid=q.id ".
                    " AND q.qtype in ('shortanswer', 'truefalse', 'multichoice')".
                    " AND q.hidden=0";
            }
            // Todo 'match'.
            $field = "q.id as id";
            $table2 = 'question';
            $fields2 = 'id as questionid,0 as glossaryentryid,qtype';
            break;
        case 'glossary':
            if ($game->glossaryid == 0) {
                print_error( get_string( 'must_select_glossary', 'game'));
            }
            $table = '{glossary_entries} ge';
            $select = "glossaryid='$game->glossaryid' ";
            if ($game->glossarycategoryid) {
                $table .= ',{glossary_entries_categories} gec';
                $select .= " AND gec.entryid = ge.id ".
                    " AND gec.categoryid = {$game->glossarycategoryid}";
            }
            $field = 'ge.id';
            $table2 = 'glossary_entries';
            $fields2 = 'id as glossaryentryid, 0 as questionid';
            break;
        case 'question':
            if ($game->questioncategoryid == 0) {
                print_error( get_string( 'must_select_questioncategory', 'game'));
            }
            $table = '{question} q';

            // Inlcude subcategories.
            $select = 'category='.$game->questioncategoryid;
            if ($game->subcategories) {
                $cats = question_categorylist( $game->questioncategoryid);
                if (count( $cats)) {
                    $select = 'category in ('.implode( ',', $cats).')';
                }
            }

            $select .= " AND q.qtype in ('shortanswer', 'truefalse', 'multichoice') AND q.hidden=0";
            // Todo 'match'.
            $field = "id";

            $table2 = 'question';
            $fields2 = 'id as questionid,0 as glossaryentryid';
            break;
        default:
            print_error( 'No sourcemodule defined');
            break;
    }

    $ids = game_questions_selectrandom_detail( $table, $select, $field, $count);
    if ($ids === false) {
        print_error( get_string( 'no_questions', 'game'));
    }

    if (count( $ids) > 1) {
        // Randomize the array.
        shuffle( $ids);
    }

    $ret = array();
    foreach ($ids as $id) {
        if ($recquestion = $DB->get_record( $table2, array( 'id' => $id), $fields2)) {
            $new = new stdClass();
            $new->questionid = (int )$recquestion->questionid;
            $new->glossaryentryid = (int )$recquestion->glossaryentryid;
            $ret[] = $new;
        }
    }

    return $ret;
}

/**
 * Select random questions. Used by game_questions_selectrandom.
 *
 * @param string $table
 * @param string $select
 * @param int $idfield
 * @param int $count
 *
 * @return stdClass the random record(s)
 */
function game_questions_selectrandom_detail( $table, $select, $idfield="id", $count=1) {
    global $DB;

    $sql = "SELECT $idfield FROM $table WHERE $select";
    if (($recs = $DB->get_records_sql( $sql)) == false) {
        return false;
    }

    // The array contains the ids of all questions.
    $a = array();
    foreach ($recs as $rec) {
        $a[ $rec->id] = $rec->id;
    }

    if ($count >= count( $a)) {
        return $a;
    } else {
        $id = array_rand(  $a, $count);
        return ($count == 1 ? array( $id) : $id);
    }
}

/**
 * Tries to detect the language of word.
 *
 * @param string $word
 *
 * @return the language detected
 */
function game_detectlanguage( $word) {
    global $CFG;

    $langs = get_string_manager()->get_list_of_translations();

    // English has more priority.
    if (array_key_exists( 'en', $langs)) {
        unset( $langs[ 'en']);
        $langs[ ''] = '';
    }
    ksort( $langs);
    $langsinstalled = get_string_manager()->get_list_of_translations();

    foreach ($langs as $lang => $name) {
        if ($lang == '') {
            $lang = 'en';
        }

        if (!array_key_exists( $lang, $langsinstalled)) {
            continue;
        }

        $strings = get_string_manager()->load_component_strings( 'game', $lang);
        if (isset( $strings[ 'lettersall'])) {
            $letters = $strings[ 'lettersall'];
            $word2 = game_upper( $word, $lang);

            if (hangman_existall( $word2, $letters)) {
                return $lang;
            }
        }
    }

    return false;
}

/**
 * The words maybe are in two languages e.g. greek or english so I try to find the correct one.
 *
 * @param string $word
 * @param string $lang
 * @param string $userlanguage
 *
 * @return the letters detected
 */
function game_getallletters( $word, $lang='', $userlanguage='') {
    for (;;) {
        if ($lang == 'user') {
            $letters = $userlanguage;
            if (hangman_existall( $word, $letters)) {
                return $letters;
            } else {
                return '';
            }
        } else {
            $strings = get_string_manager()->load_component_strings( 'game', ($lang == '' ? 'en' : $lang));
            if (isset( $strings[ 'lettersall'])) {
                $letters = $strings[ 'lettersall'];
                $word2 = game_upper( $word, $lang);
                if (hangman_existall( $word2, $letters)) {
                    return $letters;
                }
            }

            if ($lang == '') {
                break;
            } else {
                $lang = '';
            }
        }
    }

    return '';
}

/**
 * true if exist all the letters
 *
 * @param string $str
 * @param string $strfind
 *
 * @return the letters detected
 */
function hangman_existall( $str, $strfind) {
    $n = game_strlen( $str);
    for ($i = 0; $i < $n; $i++) {
        $pos = game_strpos( $strfind, game_substr( $str, $i, 1));
        if ($pos === false) {
            return false;
        }
    }

    return true;
}

/**
 * return a short answer randomly selected. used by cross
 *
 * @param stdClass $game
 *
 * @return a question
 */
function game_questions_shortanswer( $game) {
    switch( $game->sourcemodule) {
        case 'glossary':
            $recs = game_questions_shortanswer_glossary( $game);
            break;
        case 'quiz';
            $recs = game_questions_shortanswer_quiz( $game);
            break;
        case 'question';
            $recs = game_questions_shortanswer_question( $game);
            break;
    }

    return $recs;
}

/**
 * return a short answer (from glossary) randomly selected. used by cross
 *
 * @param stdClass $game
 *
 * @return a question
 */
function game_questions_shortanswer_glossary( $game) {
    global $DB;

    $select = "glossaryid={$game->glossaryid}";
    $table = '{glossary_entries} ge';
    if ($game->glossarycategoryid) {
        $table .= ',{glossary_entries_categories} gec';
        $select .= ' AND gec.entryid = ge.id '.
            ' AND gec.categoryid = '.$game->glossarycategoryid;
    }

    $sql = 'SELECT ge.id, concept as answertext, definition as questiontext, ge.id as glossaryentryid, '.
        ' 0 as questionid, attachment '.
        " FROM $table WHERE $select";

    return $DB->get_records_sql( $sql);
}

/**
 * return a short answer (from quiz) randomly selected. used by cross
 *
 * @param stdClass $game
 *
 * @return a question
 */
function game_questions_shortanswer_quiz( $game) {
    global $DB;

    if ($game->quizid == 0) {
        print_error( get_string( 'must_select_quiz', 'game'));
    }

    if (game_get_moodle_version() < '02.07') {
        $select = "qtype='shortanswer' AND quiz='$game->quizid' ".
            " AND qqi.question=q.id".
            " AND qa.question=q.id".
            " AND q.hidden=0";
        $table = "{question} q,{quiz_question_instances} qqi,{question_answers} qa";
        $fields = "qa.id as qaid, q.id, q.questiontext as questiontext, ".
            "qa.answer as answertext, q.id as questionid,".
            " 0 as glossaryentryid,'' as attachment";
    } else {
        $select = "qtype='shortanswer' AND qs.quizid='$game->quizid' ".
            " AND qs.questionid=q.id".
            " AND qa.question=q.id".
            " AND q.hidden=0";
        $table = "{question} q,{quiz_slots} qs,{question_answers} qa";
        $fields = "qa.id as qaid, q.id, q.questiontext as questiontext, ".
            "qa.answer as answertext, q.id as questionid,".
            " 0 as glossaryentryid,'' as attachment";
    }

    return game_questions_shortanswer_question_fraction( $table, $fields, $select);
}

/**
 * return a short answer (from question) randomly selected. used by cross
 *
 * @param stdClass $game
 *
 * @return a question
 */
function game_questions_shortanswer_question( $game) {
    if ($game->questioncategoryid == 0) {
        print_error( get_string( 'must_select_questioncategory', 'game'));
    }

    // Include subcategories.
    $select = 'q.category='.$game->questioncategoryid;
    if ($game->subcategories) {
        $cats = question_categorylist( $game->questioncategoryid);
        if (count( $cats)) {
            $select = 'q.category in ('.implode(',', $cats).')';
        }
    }

    $select .= " AND qtype='shortanswer' ".
        " AND qa.question=q.id".
        " AND q.hidden=0";
    $table = "{question} q,{question_answers} qa";
    $fields = "qa.id as qaid, q.id, q.questiontext as questiontext, ".
        "qa.answer as answertext, q.id as questionid";

    return game_questions_shortanswer_question_fraction( $table, $fields, $select);
}

/**
 * question fraction
 *
 * @param string $table
 * @param string $fields
 * @param string $select
 *
 * @return the record
 */
function game_questions_shortanswer_question_fraction( $table, $fields, $select) {
    global $DB;

    $sql = "SELECT $fields FROM $table WHERE $select ORDER BY fraction DESC";

    $recs = $DB->get_records_sql( $sql);
    if ($recs == false) {
        print_error( get_string( 'no_questions', 'game'));
    }

    $recs2 = array();
    $map = array();
    foreach ($recs as $rec) {
        if (array_key_exists( $rec->questionid, $map)) {
            continue;
        }
        $rec2 = new stdClass();
        $rec2->id = $rec->id;
        $rec2->questiontext = $rec->questiontext;
        $rec2->answertext = $rec->answertext;
        $rec2->questionid = $rec->questionid;
        $rec2->glossaryentryid = 0;
        $rec2->attachment = '';
        $recs2[] = $rec2;

        $map[ $rec->questionid] = $rec->questionid;
    }

    return $recs2;
}

/**
 * sets char
 *
 * @param string $s
 * @param int $pos
 * @param string $char
 */
function game_setchar( &$s, $pos, $char) {
    $ret = "";

    if ($pos > 0) {
        $ret .= game_substr( $s, 0, $pos);
    }

    $s = $ret . $char . game_substr( $s, $pos + 1);
}

/**
 * insert a record
 *
 * @param stdClass $table
 * @param stdClass $rec
 */
function game_insert_record( $table, $rec) {
    global $DB;

    if ($DB->get_record($table, array('id' => $rec->id), 'id,id') == false) {
        $sql = 'INSERT INTO {'.$table.'}(id) VALUES('.$rec->id.')';
        if (!$DB->execute( $sql)) {
            print_error( "Cannot insert an empty $table with id=$rec->id");
            return false;
        }
    }

    if (isset( $rec->question)) {
        $temp = $rec->question;
        $rec->question = addslashes( $rec->question);
    }
    $ret = $DB->update_record( $table, $rec);

    if (isset( $rec->question)) {
        $rec->question = $temp;
    }

    return $ret;
}

/**
 * If score is negative doesn't update the record score is between 0 and 1.
 *
 * @param stdClass $game
 * @param stdClass $attempt
 * @param float $score
 * @param boolean $finished
 *
 * @return the record
 */
function game_updateattempts( $game, $attempt, $score, $finished) {
    global $DB, $USER;

    if ($attempt != false) {
        $updrec = new stdClass();
        $updrec->id = $attempt->id;
        $updrec->timelastattempt = time();
        $updrec->lastip = getremoteaddr();
        if (isset( $_SERVER[ 'REMOTE_HOST'])) {
            $updrec->lastremotehost = $_SERVER[ 'REMOTE_HOST'];
        } else {
            $updrec->lastremotehost = gethostbyaddr( $updrec->lastip);
        }
        $updrec->lastip = substr( $updrec->lastip, 0, 30);
        $updrec->lastremotehost = substr( $updrec->lastremotehost, 0, 50);

        if ($score >= 0) {
            $updrec->score = $score;
        }

        if ($finished) {
            $updrec->timefinish = $updrec->timelastattempt;
        }

        $updrec->attempts = $attempt->attempts + 1;

        if (!$DB->update_record( 'game_attempts', $updrec)) {
            print_error( "game_updateattempts: Can't update game_attempts id=$updrec->id");
        }

        // Update grade item and send all grades to gradebook.
        $grades = new stdClass();
        $grades->userid = $USER->id;
        $grades->rawgrade = game_score_to_grade($score, $game);
        $grades->datesubmitted = time();
        game_grade_item_update( $game, $grades);
        game_update_grades( $game, $grades->userid);
    }

    // Update table game_grades.
    if ($finished) {
        game_save_best_score( $game);
    }
}

/**
 * Computes max grade
 *
 * @param stdClass $game
 * @param stdClass $attempt
 * @param float $grade
 * @param boolean $finished
 *
 * @return the record
 */
function game_updateattempts_maxgrade( $game, $attempt, $grade, $finished) {
    global $DB;

    $recgrade = $DB->get_field( 'game_attempts', 'score', array( 'id' => $attempt->id));

    if ($recgrade > $grade) {
        $grade = -1;    // Don't touch the grade.
    }

    game_updateattempts( $game, $attempt, $grade, $finished);
}

/**
 * Update queries
 *
 * @param stdClass $game
 * @param stdClass $attempt
 * @param string $query
 * @param float $score
 * @param string $studentanswer
 * @param boolean $updatetries
 *
 * @return the record
 */
function game_update_queries( $game, $attempt, $query, $score, $studentanswer, $updatetries=false) {
    global $DB, $USER;

    if ($query->id != 0) {
        $select = "id=$query->id";
    } else {
        $select = "attemptid = $attempt->id AND sourcemodule = '{$query->sourcemodule}'";
        switch ($query->sourcemodule) {
            case 'quiz':
                $select .= " AND questionid='$query->questionid' ";
                break;
            case 'glossary':
                $select .= " AND glossaryentryid='$query->glossaryentryid'";
                break;
        }
    }

    if (($recq = $DB->get_record_select( 'game_queries', $select)) === false) {
        $recq = new stdClass();
        $recq->gamekind = $game->gamekind;
        $recq->gameid = $attempt->gameid;
        $recq->userid = $attempt->userid;
        $recq->attemptid = $attempt->id;
        $recq->sourcemodule = $query->sourcemodule;
        $recq->questionid = $query->questionid;
        $recq->glossaryentryid = $query->glossaryentryid;
        if ($updatetries) {
            $recq->tries = 1;
        }

        if (!($recq->id = $DB->insert_record( 'game_queries', $recq))) {
            print_error( 'Insert page: new page game_queries not inserted');
        }
    }

    $updrec = new stdClass();
    $updrec->id = $recq->id;
    $updrec->timelastattempt = time();

    if ($score >= 0) {
        $updrec->score = $score;
    }

    if ($studentanswer != '') {
        $updrec->studentanswer = $studentanswer;
    }

    if ($updatetries) {
        $updrec->tries = $recq->tries + 1;
    }

    if (!($DB->update_record( 'game_queries', $updrec))) {
        print_error( "game_update_queries: not updated id=$updrec->id");
    }
}

/**
 * get attempt
 *
 * @param stdClass $game
 * @param stdclass $detail
 * @param boolean $autoadd
 */
function game_getattempt( $game, &$detail, $autoadd=false) {
    global $DB, $USER;

    $select = "gameid=$game->id AND userid=$USER->id and timefinish=0 ";
    if ($USER->id == 1) {
        $key = 'mod/game:instanceid'.$game->id;
        if (array_key_exists( $key, $_SESSION)) {
            $select .= ' AND id="'.$_SESSION[ $key].'"';
        } else {
            $select .= ' AND id=-1';
        }
    }

    if (($recs = $DB->get_records_select( 'game_attempts', $select))) {
        foreach ($recs as $attempt) {
            if ($USER->id == 1) {
                $_SESSION[ $key] = $attempt->id;
            }

            $detail = $DB->get_record( 'game_'.$game->gamekind, array( 'id' => $attempt->id));

            return $attempt;
        }
    }

    if ($autoadd) {
        game_addattempt( $game);
        return game_getattempt( $game, $detail, false);
    }

    return false;
}

/**
 * get user attempts
 *
 * @param integer $gameid the game id.
 * @param integer $userid the userid.
 * @param string $status 'all', 'finished' or 'unfinished' to control
 *
 * @return an array of all the user's attempts at this game. Returns an empty array if there are none.
 */
function game_get_user_attempts( $gameid, $userid, $status = 'finished') {
    global $DB;

    $statuscondition = array(
        'all' => '',
        'finished' => ' AND timefinish > 0',
        'unfinished' => ' AND timefinish = 0'
    );
    if ($attempts = $DB->get_records_select( 'game_attempts',
            "gameid = ? AND userid = ? AND preview = 0" . $statuscondition[$status],
            array( $gameid, $userid), 'attempt ASC')) {
        return $attempts;
    } else {
        return array();
    }
}


/**
 * Returns an unfinished attempt (if there is one) for the given user on the given game.
 * This function does not return preview attempts.
 *
 * @param integer $gameid the id of the game.
 * @param integer $userid the id of the user.
 *
 * @return mixed the unfinished attempt if there is one, false if not.
 */
function game_get_user_attempt_unfinished( $gameid, $userid) {
    $attempts = game_get_user_attempts( $gameid, $userid, 'unfinished');
    if ($attempts) {
        return array_shift($attempts);
    } else {
        return false;
    }
}

/**
 * Get the best current score for a particular user in a game.
 *
 * @param object $game the game object.
 * @param integer $userid the id of the user.
 *
 * @return float the user's current grade for this game.
 */
function game_get_best_score($game, $userid) {
    global $DB;

    $score = $DB->get_field( 'game_grades', 'score', array( 'gameid' => $game->id, 'userid' => $userid));

    // Need to detect errors/no result, without catching 0 scores.
    if (is_numeric($score)) {
        return $score;
    } else {
        return null;
    }
}

/**
 * Get the best current grade for a particular user in a game.
 *
 * @param object $game the game object.
 * @param integer $userid the id of the user.
 *
 * @return float the user's current grade for this game.
 */
function game_get_best_grade($game, $userid) {
    $score = game_get_best_score( $game, $userid);

    if (is_numeric( $score)) {
        return round( $score * $game->grade, $game->decimalpoints);
    } else {
        return null;
    }
}

/**
 * Converts score to grade
 *
 * @param float $score
 * @param stdClass $game
 *
 * @return float the user's current grade for this game.
 */
function game_score_to_grade($score, $game) {
    if ($score) {
        return round($score * $game->grade, $game->decimalpoints);
    } else {
        return 0;
    }
}

/**
 * Determine review options
 *
 * @param object $game the game instance.
 * @param object $attempt the attempt in question.
 * @param object $context the roles and permissions context, for game module instance
 *
 * @return object an object with boolean fields responses, scores, feedback,
 *          correct_responses, solutions and general feedback
 */
function game_get_reviewoptions($game, $attempt, $context=null) {

    $options = new stdClass;
    $options->readonly = true;

    // Provide the links to the question review and comment script.
    $options->questionreviewlink = '/mod/game/reviewquestion.php';

    if ($context and !$attempt->preview) {
        // The teacher should be shown everything except during preview when the teachers wants to see just what the students see.
        $options->responses = true;
        $options->scores = true;
        $options->feedback = true;
        $options->correct_responses = true;
        $options->solutions = false;
        $options->generalfeedback = true;
        $options->overallfeedback = true;

        // Show a link to the comment box only for closed attempts.
        if ($attempt->timefinish) {
            $options->questioncommentlink = '/mod/game/comment.php';
        }
    } else {
        if (((time() - $attempt->timefinish) < 120) || $attempt->timefinish == 0) {
            $gamestatemask = GAME_REVIEW_IMMEDIATELY;
        } else if (!$game->timeclose or time() < $game->timeclose) {
            $gamestatemask = GAME_REVIEW_OPEN;
        } else {
            $gamestatemask = GAME_REVIEW_CLOSED;
        }
        $options->responses = ($game->review & $gamestatemask & GAME_REVIEW_RESPONSES) ? 1 : 0;
        $options->scores = ($game->review & $gamestatemask & GAME_REVIEW_SCORES) ? 1 : 0;
        $options->feedback = ($game->review & $gamestatemask & GAME_REVIEW_FEEDBACK) ? 1 : 0;
        $options->correct_responses = ($game->review & $gamestatemask & GAME_REVIEW_ANSWERS) ? 1 : 0;
        $options->solutions = ($game->review & $gamestatemask & GAME_REVIEW_SOLUTIONS) ? 1 : 0;
        $options->generalfeedback = ($game->review & $gamestatemask & GAME_REVIEW_GENERALFEEDBACK) ? 1 : 0;
        $options->overallfeedback = $attempt->timefinish && ($game->review & $gamestatemask & GAME_REVIEW_FEEDBACK);
    }

    return $options;
}

/**
 * Compute attempt layout
 *
 * @param object $game the game object.
 * @param stdClass $attempt
 * @return float the user's current grade for this game.
 */
function game_compute_attempt_layout( $game, &$attempt) {
    global $DB;

    $ret = '';
    $recs = $DB->get_records_select( 'game_queries', "attemptid=$attempt->id", null, '',
        'id,questionid,sourcemodule,glossaryentryid');
    if ($recs) {
        foreach ($recs as $rec) {
            if ($rec->sourcemodule == 'glossary') {
                $ret .= $rec->glossaryentryid.'G,';
            } else {
                $ret .= $rec->questionid.',';
            }
        }
    }

    $attempt->layout = $ret.'0';
}

/**
 * Combines the review options from a number of different game attempts.
 *
 * @param object $game the game instance.
 * @param array $attempts an array of attempt objects.
 * @param object $context the roles and permissions context, the game instance
 *
 * @return array of two options objects, one showing which options are true for
 *          at least one of the attempts, the other showing which options are true
 *          for all attempts.
 */
function game_get_combined_reviewoptions($game, $attempts, $context=null) {
    $fields = array('readonly', 'scores', 'feedback', 'correct_responses', 'solutions', 'generalfeedback', 'overallfeedback');
    $someoptions = new stdClass;
    $alloptions = new stdClass;
    foreach ($fields as $field) {
        $someoptions->$field = false;
        $alloptions->$field = true;
    }
    foreach ($attempts as $attempt) {
        $attemptoptions = game_get_reviewoptions( $game, $attempt, $context);
        foreach ($fields as $field) {
            $someoptions->$field = $someoptions->$field || $attemptoptions->$field;
            $alloptions->$field = $alloptions->$field && $attemptoptions->$field;
        }
    }
    return array( $someoptions, $alloptions);
}

/**
 * Save the overall grade for a user at a game in the game_grades table
 *
 * @param object $game The game for which the best grade is to be calculated and then saved.
 *
 * @return boolean Indicates success or failure.
 */
function game_save_best_score($game) {
    global $DB, $USER;

    // Get all the attempts made by the user.
    if (!$attempts = game_get_user_attempts( $game->id, $USER->id, 'all')) {
        print_error( 'Could not find any user attempts gameid='.$game->id.' userid='.$USER->id);
    }

    // Calculate the best grade.
    $bestscore = game_calculate_best_score( $game, $attempts);

    // Save the best grade in the database.
    if ($grade = $DB->get_record('game_grades', array( 'gameid' => $game->id, 'userid' => $USER->id))) {
        $grade->score = $bestscore;
        $grade->timemodified = time();
        if (!$DB->update_record('game_grades', $grade)) {
            print_error('Could not update best grade');
        }
    } else {
        $grade = new stdClass();
        $grade->gameid = $game->id;
        $grade->userid = $USER->id;
        $grade->score = $bestscore;
        $grade->timemodified = time();
        if (!$DB->insert_record( 'game_grades', $grade)) {
            print_error( 'Could not insert new best grade');
        }
    }

    // Updates gradebook.
    $grades = new stdClass();
    $grades->userid = $USER->id;
    $grades->rawgrade = game_score_to_grade($bestscore, $game);
    $grades->datesubmitted = time();
    game_grade_item_update( $game, $grades);

    return true;
}

/**
 * Calculate the overall score for a game given a number of attempts by a particular user.
 *
 * @return double         The overall score
 * @param object $game    The game for which the best score is to be calculated
 * @param array $attempts An array of all the attempts of the user at the game
 */
function game_calculate_best_score($game, $attempts) {

    switch ($game->grademethod) {
        case GAME_GRADEMETHOD_FIRST:
            foreach ($attempts as $attempt) {
                return $attempt->score;
            }
            break;
        case GAME_GRADEMETHOD_LAST:
            foreach ($attempts as $attempt) {
                $final = $attempt->score;
            }
            return $final;
        case GAME_GRADEMETHOD_AVERAGE:
            $sum = 0;
            $count = 0;
            foreach ($attempts as $attempt) {
                $sum += $attempt->score;
                $count++;
            }
            return (float)$sum / $count;
        default:
        case GAME_GRADEMETHOD_HIGHEST:
            $max = 0;
            foreach ($attempts as $attempt) {
                if ($attempt->score > $max) {
                    $max = $attempt->score;
                }
            }
            return $max;
    }
}

/**
 * Return the attempt with the best score for a game
 *
 * Which attempt is the best depends on $game->grademethod. If the  grade
 * method is GRADEAVERAGE then this function simply returns the last attempt.
 * @return object         The attempt with the best grade
 * @param object $game    The game for which the best grade is to be calculated
 * @param array $attempts An array of all the attempts of the user at the game
 */
function game_calculate_best_attempt($game, $attempts) {

    switch ($game->grademethod) {

        case GAME_ATTEMPTFIRST:
            foreach ($attempts as $attempt) {
                return $attempt;
            }
            break;

        case GAME_GRADEAVERAGE: // Need to do something with it.
        case GAME_ATTEMPTLAST:
            foreach ($attempts as $attempt) {
                $final = $attempt;
            }
            return $final;

        default:
        case GAME_GRADEHIGHEST:
            $max = -1;
            foreach ($attempts as $attempt) {
                if ($attempt->sumgrades > $max) {
                    $max = $attempt->sumgrades;
                    $maxattempt = $attempt;
                }
            }
            return $maxattempt;
    }
}

/**
 * get questions for sudoku
 *
 * @param string $questionlist
 */
function game_sudoku_getquestions( $questionlist) {
    global $CFG, $DB;

    // Load the questions.
    $sql = "SELECT q.*,qmo.single ".
        " FROM {$CFG->prefix}question ".
            " LEFT JOIN {$CFG->prefix}qtype_multichoice_options qmo ON q.id=qmo.questionid AND q.qtype='multichoice' ".
        " WHERE q.id IN ($questionlist)";
    if (!$questions = $DB->get_records_select( 'question', "id IN ($questionlist)")) {
        print_error( get_string( 'no_questions', 'game'));
    }

    // Load the question type specific information.
    if (!get_question_options($questions)) {
        print_error('Could not load question options');
    }

    return $questions;
}

/**
 * Filter glossary
 *
 * @param string $text
 * @param int $entryid
 * @param int $contextid
 * @param int $courseid
 */
function game_filterglossary( $text, $entryid, $contextid, $courseid) {
    global $CFG, $DB;

    for (;;) {
        $find = '@@PLUGINFILE@@';
        $pos = strpos( $text, $find);
        if ($pos === false) {
            break;
        }

        $pos2 = strpos( $text, '/', $pos);
        if ($pos2 === false) {
            break;
        }

        $pos3 = strpos( $text, '"', $pos);
        if ($pos3 === false) {
            break;
        }

        $file = substr( $text, $pos2 + 1, $pos3 - $pos2 - 1);

        $new = $CFG->wwwroot."/pluginfile.php/$contextid/mod_glossary/entry/$entryid/$file";
        $text = substr( $text, 0, $pos).$new.substr( $text, $pos3);
    }

    $questiontext = str_replace( '$$'.'\\'.'\\'.'frac', '$$\\'.'frac', $text);
    return game_filtertext( $text, $courseid);
}

/**
 * Filter book
 *
 * @param string $text
 * @param int $chapterid
 * @param int $contextid
 * @param int $courseid
 */
function game_filterbook( $text, $chapterid, $contextid, $courseid) {
    global $CFG, $DB;

    for (;;) {
        $find = '@@PLUGINFILE@@';
        $pos = strpos( $text, $find);
        if ($pos === false) {
            break;
        }

        $pos2 = strpos( $text, '/', $pos);
        if ($pos2 === false) {
            break;
        }

        $pos3 = strpos( $text, '"', $pos);
        if ($pos3 === false) {
            break;
        }

        $file = substr( $text, $pos2 + 1, $pos3 - $pos2 - 1);

        $new = $CFG->wwwroot."/pluginfile.php/$contextid/mod_book/chapter/$chapterid/$file";
        $text = substr( $text, 0, $pos).$new.substr( $text, $pos3);
    }

    $questiontext = str_replace( '$$'.'\\'.'\\'.'frac', '$$\\'.'frac', $text);
    return game_filtertext( $text, $courseid);
}

/**
 * Filter questio
 *
 * @param string $questiontext
 * @param int $questionid
 * @param int $contextid
 * @param int $courseid
 */
function game_filterquestion( $questiontext, $questionid, $contextid, $courseid) {
    global $CFG, $DB;

    for (;;) {
        $find = '@@PLUGINFILE@@';
        $pos = strpos( $questiontext, $find);
        if ($pos === false) {
            break;
        }

        $pos2 = strpos( $questiontext, '/', $pos);
        if ($pos2 === false) {
            break;
        }

        $pos3 = strpos( $questiontext, '"', $pos);
        if ($pos3 === false) {
            break;
        }

        $file = substr( $questiontext, $pos2 + 1, $pos3 - $pos2 - 1);

        $new = $CFG->wwwroot."/pluginfile.php/$contextid/mod_game/questiontext/$questionid/$file";
        $questiontext = substr( $questiontext, 0, $pos).$new.substr( $questiontext, $pos3);
    }

    $questiontext = str_replace( '$$'.'\\'.'\\'.'frac', '$$\\'.'frac', $questiontext);
    return game_filtertext( $questiontext, $courseid);
}

/**
 * Filter question answer
 *
 * @param string $questiontext
 * @param int $questionid
 * @param int $contextid
 * @param int $courseid
 */
function game_filterquestion_answer( $questiontext, $questionid, $contextid, $courseid) {
    global $CFG, $DB;

    for (;;) {
        $find = '@@PLUGINFILE@@';
        $pos = strpos( $questiontext, $find);
        if ($pos === false) {
            break;
        }

        $pos2 = strpos( $questiontext, '/', $pos);
        if ($pos2 === false) {
            break;
        }

        $pos3 = strpos( $questiontext, '"', $pos);
        if ($pos3 === false) {
            break;
        }

        $file = substr( $questiontext, $pos2 + 1, $pos3 - $pos2 - 1);

        $new = $CFG->wwwroot."/pluginfile.php/$contextid/mod_game/answer/$questionid/$file";
        $questiontext = substr( $questiontext, 0, $pos).$new.substr( $questiontext, $pos3);
    }

    return game_filtertext( $questiontext, $courseid);
}

/**
 * Filter text
 *
 * @param string $text
 * @param int $courseid
 */
function game_filtertext( $text, $courseid) {
    $formatoptions = new stdClass();
    $formatoptions->noclean = true;
    $formatoptions->filter = 1;
    $text = trim( format_text( $text, FORMAT_MOODLE, $formatoptions));

    $start = '<div class="text_to_html">';
    if (substr( $text, 0, strlen( $start)) == $start) {
        if (substr( $text, -6) == '</div>') {
            $text = substr( $text, strlen( $start), -6);
        }
    }
    if (substr( $text, 0, 3) == '<p>') {
        if (substr( $text, -4) == '</p>') {
            $text = substr( $text, 3, -4);
        }
    }

    return $text;
}

/**
 * To javascript string
 *
 * @param string $text
 */
function game_tojavascriptstring( $text) {
    $from = array('"', "\r", "\n");
    $to = array('\"', ' ', ' ');

    $from[] = '<script ';   $to[] = '<" + "script ';
    $from[] = '</script>';   $to[] = '<" + "/script>';

    $text = str_replace( $from, $to, $text);

    return $text;
}

/**
 * Repair question
 *
 * @param string $s
 */
function game_repairquestion( $s) {
    if (substr( $s, 0, 3) == '<p>') {
        $s = substr( $s, 3);
    }
    if (substr( $s, -4) == '</p>') {
        $s = substr( $s, 0, -4);
    }
    if (substr( $s, 0, 4) == '<br>') {
        $s = substr( $s, 4);
    }
    if (substr( $s, 0, 6) == '<br />') {
        $s = substr( $s, 6);
    }
    if (substr( $s, 0, 5) == '<div ' and substr( $s, -6) == '</div>') {
        $pos = strpos( $s, '>');
        if ($pos != false) {
            $s = substr( $s, $pos + 1);
        }
        $s = substr( $s, 0, -6);
    }
    $s = str_replace( array("\'", '\"'), array("'", '"'), $s);

    return $s;
}

/**
 * Delete a game attempt.
 *
 * @param stdClass $attempt
 * @param stdClass $game
 */
function game_delete_attempt($attempt, $game) {
    global $DB;

    if (is_numeric($attempt)) {
        if (!$attempt = $DB->get_record('game_attempts', 'id', $attempt)) {
            return;
        }
    }

    if ($attempt->gameid != $game->id) {
        debugging("Trying to delete attempt $attempt->id which belongs to game $attempt->gameid " .
                "but was passed gameid $game->id.");
        return;
    }

    $DB->delete_records('game_attempts', array( 'id' => $attempt->id));

    /* Search game_attempts for other instances by this user.
       If none, then delete record for this game, this user from game_grades
       else recalculate best grade */
    $userid = $attempt->userid;
    if (!$DB->record_exists('game_attempts', array( 'userid' => $userid, 'gameid' => $game->id))) {
        $DB->delete_records('game_grades', array( 'userid' => $userid, 'gameid' => $game->id));
    } else {
        game_save_best_score( $game);
    }

    game_update_grades( $game, $userid);
}

/**
 * Returns the most recent attempt by a given user on a given game. May be finished, or may not.
 *
 * @param integer $gameid the id of the game.
 * @param integer $userid the id of the user.
 *
 * @return mixed the attempt if there is one, false if not.
 */
function game_get_latest_attempt_by_user($gameid, $userid) {
    global $DB;

    $attempt = $DB->get_records_sql('SELECT qa.* FROM {game_attempts} qa
            WHERE qa.gameid=? AND qa.userid= ? ORDER BY qa.timestart DESC, qa.id DESC', array($gameid, $userid), 0, 1);

    if ($attempt) {
        return array_shift( $attempt);
    } else {
        return false;
    }
}

/**
 * get grading option name
 *
 * @param int $option one of the values GAME_GRADEHIGHEST, GAME_GRADEAVERAGE, GAME_ATTEMPTFIRST or GAME_ATTEMPTLAST.
 * @return the lang string for that option.
 */
function game_get_grading_option_name($option) {
    if ($option == 0) {
        $option = GAME_GRADEHIGHEST;
    }

    $strings = game_get_grading_options();

    return $strings[$option];
}

/**
 * Right to left.
 *
 * @param string $lang
 */
function game_right_to_left( $lang) {
    return ( get_string_manager()->get_string('thisdirection', 'langconfig', null, $lang) == 'rtl');
}

/**
 * Compute reverse print
 *
 * @param stdClass $attempt
 * @param string $wordrtl
 * @param boolean $reverseprint
 */
function game_compute_reserve_print( $attempt, &$wordrtl, &$reverseprint) {
    if (function_exists( 'right_to_left')) {
        if ($attempt->language != '') {
            $wordrtl = game_right_to_left( $attempt->language);
        } else {
            $wordrtl = right_to_left();
        }
        $reverseprint = ($wordrtl != right_to_left());
    } else {
        $reverseprint = false;
        $wordrtl = 'ltr';
    }
}

/**
 * select from repetitions
 *
 * @param stdClass $game
 * @param stdClass $recs
 * @param booolean $need
 */
function game_select_from_repetitions( $game, $recs, $need) {
    global $DB, $USER;

    $ret = array();

    $field = ($game->sourcemodule == 'glossary' ? 'glossaryentryid' : 'questionid');

    if (count($recs) <= $need) {
        foreach ($recs as $rec) {
            $id = $rec->$field;
            $ret[ $id] = 1;
        }

        return $ret;
    }

    $countzero = 0;
    foreach ($recs as $rec) {
        $a = array( 'gameid' => $game->id, 'userid' => $USER->id, 'questionid' => $rec->questionid,
            'glossaryentryid' => $rec->glossaryentryid);
        $id = $rec->$field;
        if (($rec = $DB->get_record( 'game_repetitions', $a, 'id,repetitions AS r')) != false) {
            $reps[ $id] = $rec->r;
        } else {
            $reps[ $id] = 0;
            if (++$countzero >= $need) {
                break;
            }
        }
    }
    asort( $reps);
    foreach ($reps as $id => $r) {
        $ret[ $id] = 1;
        if (count( $ret) >= $need) {
            break;
        }
    }

    return $ret;
}

/**
 * Grades responses
 *
 * @param stdClass $question
 * @param array $responses
 * @param int $maxgrade
 * @param string $answertext
 * @param boolean $answered
 */
function game_grade_responses( $question, $responses, $maxgrade, &$answertext, &$answered) {
    $answered = true;

    if ($question->qtype == 'multichoice') {
        if ($question->options->single == 0) {
            return game_grade_responses_multianswer( $question, $responses, $maxgrade, $answertext);
        }
        $name = "resp{$question->id}_";
        if (!isset( $responses->$name)) {
            $answered = false;
            return 0;
        }
        $value = $responses->$name;
        $answer = $question->options->answers[ $value];
        $answertext = $answer->answer;

        return $answer->fraction * $maxgrade;
    } else {
        $name = "resp{$question->id}_";
        if (!isset( $responses->$name)) {
            $answered = false;
            return 0; // Not answered this question.
        }
        $answertext = game_upper( $responses->$name);

        foreach ($question->options->answers as $answer) {
            if (game_upper( $answer->answer) == $answertext) {
                return $answer->fraction * $maxgrade;
            }
        }

        return 0;
    }
}

/**
 * Responses multianswer
 *
 * @param stdClass $question
 * @param array $responses
 * @param int $maxgrade
 * @param string $answertext
 */
function game_grade_responses_multianswer( $question, $responses, $maxgrade, &$answertext) {
    $name = "resp{$question->id}_";

    $len = strlen( $name);
    $fraction = 0;
    foreach ($responses as $key => $value) {
        $sub = substr( $key, 0, strlen( $name));
        if ($sub != $name) {
            continue;
        }

        $name2 = $name.$value;
        $value2 = $responses->$name2;
        $answer = $question->options->answers[ $value2];
        $fraction += $answer->fraction;
    }

    return $fraction * $maxgrade;
}

/**
 * Print question
 *
 * @param stdClass $game
 * @param string $question
 * @param stdClass $context
 */
function game_print_question( $game, $question, $context) {
    if ($question->qtype == 'multichoice') {
        if ($question->options->single == 0) {
            game_print_question_multianswer( $game, $question, $context);
        } else {
            game_print_question_multichoice( $game, $question, $context);
        }
    } else if ($question->qtype == 'shortanswer') {
        game_print_question_shortanswer( $game, $question, $context);
    }
}

/**
 * Print question multichoice
 *
 * @param stdClass $game
 * @param string $question
 * @param stdClass $context
 */
function game_print_question_multichoice( $game, $question, $context) {
    global $CFG;

    $i = 0;
    $questiontext = $question->questiontext;
    $answerprompt = get_string( 'singleanswer', 'quiz');
    $feedback = '';
    $anss = array();
    foreach ($question->options->answers as $a) {
        $answer = new stdClass();
        if (substr( $a->answer, 0, 3) == '<p>' or substr( $a->answer, 0, 3) == '<P>') {
            $a->answer = substr( $a->answer, 3);
            $s = rtrim( $a->answer);
            if (substr( $s, 0, -3) == '<p>' or substr( $s, 0, -3) == '<P>') {
                $a->answer = substr( $a->answer, 0, -3);
            }
        }
        $a->answer = game_filterquestion_answer(str_replace( '\"', '"', $a->answer), $a->id, $context->id, $game->course);
        $answer->control = "<input  id=\"resp{$question->id}_{$a->id}\" name=\"resp{$question->id}_\"  ".
            " type=\"radio\" value=\"{$a->id}\" /> ".$a->answer;
        $answer->class = 'radio';
        $answer->id = $a->id;
        $answer->text = $a->answer;
        $answer->feedbackimg = '';
        $answer->feedback = '';
        $anss[] = $answer;
    }
?>
<div class="qtext">
    <?php echo game_filterquestion(str_replace( '\"', '"', $questiontext), $question->id, $context->id, $game->course); ?>
</div>


<div class="ablock clearfix">
    <div class="prompt">
        <?php echo $answerprompt; ?>
    </div>

    <table class="answer">
<?php
$row = 1;
foreach ($anss as $answer) {
?>
        <tr class="<?php echo 'r'.$row = $row ? 0 : 1; ?>">
            <td>
                <?php echo $answer->control; ?>
            </td>
        </tr>
<?php
}
?>        
    </table>
</div>
<?php
}

/**
 * Print question multianswer
 *
 * @param object $game the game object.
 * @param stdClass $question
 * @param stdClass $context
 */
function game_print_question_multianswer( $game, $question, $context) {
    global $CFG;

    $i = 0;
    $questiontext = $question->questiontext;
    $answerprompt = get_string( 'singleanswer', 'quiz');
    $feedback = '';
    $anss = array();
    foreach ($question->options->answers as $a) {
        $answer = new stdClass();
        if (substr( $a->answer, 0, 3) == '<p>' or substr( $a->answer, 0, 3) == '<P>') {
            $a->answer = substr( $a->answer, 3);
            $s = rtrim( $a->answer);
            if (substr( $s, 0, -3) == '<p>' or substr( $s, 0, -3) == '<P>') {
                $a->answer = substr( $a->answer, 0, -3);
            }
        }
        $a->answer = game_filterquestion_answer(str_replace( '\"', '"', $a->answer), $a->id, $context->id, $game->course);
        $answer->control = "<input  id=\"resp{$question->id}_{$a->id}\" name=\"resp{$question->id}_{$a->id}\"  ".
            " type=\"checkbox\" value=\"{$a->id}\" /> ".$a->answer;
        $answer->class = 'radio';
        $answer->id = $a->id;
        $answer->text = $a->answer;
        $answer->feedbackimg = '';
        $answer->feedback = '';
        $anss[] = $answer;
    }
?>
<div class="qtext">
    <?php echo game_filterquestion(str_replace( '\"', '"', $questiontext), $question->id, $context->id, $game->course); ?>
</div>


<div class="ablock clearfix">
    <div class="prompt">
        <?php echo $answerprompt; ?>
    </div>

    <table class="answer">
<?php
$row = 1;
foreach ($anss as $answer) {
?>
        <tr class="<?php echo 'r'.$row = $row ? 0 : 1; ?>">
            <td>
                <?php echo $answer->control; ?>
            </td>
        </tr>
<?php
}
?>
    </table>
</div>
<?php
}

/**
 * Print question show answer
 *
 * @param stdClass $game
 * @param stdClass $question
 * @param stdClass $context
 */
function game_print_question_shortanswer( $game, $question, $context) {
    $questiontext = $question->questiontext;

?>
<div class="qtext">
<?php
echo game_filterquestion(str_replace( '\"', '"', $questiontext), $question->id, $context->id, $game->course);
?>
</div>

<div class="ablock clearfix">
  <div class="prompt">
    <?php echo get_string("answer", "quiz").': '; ?>
  </div>
  <div class="answer">
    <input type="text" name="resp<?php echo $question->id; ?>_" size="80"/>
  </div>
</div>
<?php
}

/**
 * Snakes get board.
 *
 * @param object $game the game object.
 */
function game_snakes_get_board( $game) {
    global $CFG, $DB;

    if ($game->param3 != 0) {
        $board = $DB->get_record( 'game_snakes_database', array( 'id' => $game->param3));
        if ($board == false) {
            require_once(dirname(__FILE__) . '/../db/importsnakes.php');
            $board = $DB->get_record( 'game_snakes_database', array( 'id' => $game->param3));
        }
        if ($board == false) {
            print_error( 'No board');
        }
        $board->imagesrc = $CFG->wwwroot.'/mod/game/snakes/boards/'.$board->fileboard;
        list( $board->width, $board->height) = getimagesize( __DIR__.'/snakes/boards/'.$board->fileboard);
    } else {
        // User defined board.
        $board = game_snakes_create_user_defined_board( $game);
    }

    return $board;
}

/**
 * Creates user defined board.
 *
 * @param object $game the game object.
 */
function game_snakes_create_user_defined_board( &$game) {
    global $CFG, $DB;

    $board = game_snakes_get_board_params( $game);

    $cmg = get_coursemodule_from_instance('game', $game->id, $game->course);
    $modcontext = get_context_instance(CONTEXT_MODULE, $cmg->id);

    if ($game->param5) {
        // Param5 means dirty image. Create it again.
        require("snakes/createboard.php");
        $fs = get_file_storage();
        $files = $fs->get_area_files($modcontext->id, 'mod_game', 'snakes_file', $game->id);
        $f = false;
        foreach ($files as $f) {
            if ($f->is_directory()) {
                continue;
            }
            break;
        }
        if ($f === false) {
            print_error( 'No image specified');
        }
        $im = game_createsnakesboard($f->get_content(), $board->usedcols, $board->usedrows, $board->headery, $board->headery,
            $board->footerx, $board->headerx, $board->data, $board->width, $board->height);
        ob_start();
        imagepng($im);
        $data = ob_get_contents();
        ob_end_clean();
        $fileinfo = array(
            'contextid' => $modcontext->id, // ID of context.
            'component' => 'mod_game',      // Usually = table name.
            'filearea' => 'snakes_board',   // Usually = table name.
            'itemid' => $game->id,          // Usually = ID of row in table.
            'filepath' => '/',              // Any path beginning and ending in /.
            'filename' => 'board.png');     // any filename.
        $fs->delete_area_files($modcontext->id, 'mod_game', 'snakes_board', $game->id);
        $file = $fs->create_file_from_string($fileinfo, $data);
        $imageinfo = $file->get_imageinfo();
        $game->param6 = $imageinfo[ 'width'];
        $game->param7 = $imageinfo[ 'height'];
        $sql = "UPDATE {$CFG->prefix}game SET param5=0,param6=$game->param6,param7=$game->param7 WHERE id=$game->id";
        if ( !$DB->execute( $sql)) {
            error('problem in '.$sql);
        }
    }

    $board->imagesrc = "{$CFG->wwwroot}/pluginfile.php/{$modcontext->id}/mod_game/{$game->id}/snakes_board/board.png";
    $board->width = $game->param6;
    $board->height = $game->param7;
    $board->direction = 1;
    $board->fileboard = 'board.png';

    return $board;
}

/**
 * Snakes get board params.
 *
 * @param object $game the game object.
 */
function game_snakes_get_board_params( $game) {
    $board = new stdClass();

    $a = explode( '#', $game->param9);
    foreach ($a as $s) {
        $pos = strpos( $s, ':');
        if ($pos) {
            $name = substr( $s, 0, $pos);
            if (substr( $name, 0, 7) == 'snakes_') {
                $name = substr( $name, 7);
            }
            $board->$name = substr( $s, $pos + 1);
        }
    }

    return $board;
}

/**
 * Export create temp dir
 */
function game_export_createtempdir() {
    global $CFG;

    // Creates a random upload directory in temp.
    $newdir = $CFG->dataroot."/temp/game";
    if (!file_exists( $newdir)) {
        mkdir( $newdir);
    }

    srand( (double)microtime() * 1000000);
    while (true) {
        $rbasedir = "game/".date("Y-m-d-H.i.s-").rand(0, 10000);
        $newdir = $CFG->dataroot.'/temp/'.$rbasedir;
        if (!file_exists( $newdir)) {
            mkdir( $newdir);
            return $newdir;
        }
    }
}

/**
 * Create zip
 *
 * @param string $srcdir
 * @param int $courseid
 * @param string $filename
 */
function game_create_zip( $srcdir, $courseid, $filename) {
    global $CFG;

    $dir = $CFG->dataroot . '/' . $courseid;
    $filezip = $dir . "/export/{$filename}";

    if (file_exists( $filezip)) {
        unlink( $filezip);
    }

    if (!file_exists( $dir)) {
        mkdir( $dir);
    }

    if (!file_exists( $dir.'/export')) {
        mkdir( $dir.'/export');
    }

    $srcfiles = get_directory_list( $srcdir, '', true, true, true);
    $fullsrcfiles = array();
    foreach ($srcfiles as $file) {
        $fullsrcfiles[] = $srcdir.'/'.$file;
    }

    zip_files( $fullsrcfiles, $filezip);

    return (file_exists( $filezip) ? $filezip : '');
}

/**
 * Get string lang
 *
 * @param string $identifier
 * @param string $module
 * @param string $lang
 */
function game_get_string_lang( $identifier, $module, $lang) {
    global $CFG;

    return get_string_manager()->get_string($identifier, $module, null, $lang);
}

/**
 * Get string from file
 *
 * @param string $identifier
 * @param string $langfile
 * @param string $destination
 */
function get_string_from_file($identifier, $langfile, $destination) {
    static $strings;    // Keep the strings cached in memory.

    if (empty($strings[$langfile])) {
        $string = array();
        include( $langfile);
        $strings[$langfile] = $string;
    } else {
        $string = &$strings[$langfile];
    }

    if (!isset ($string[$identifier])) {
        return false;
    }

    return $string[ $identifier];
}

/**
 * Inserts a record to game_attempts.
 *
 * @param object $game the game object.
 */
function game_addattempt( $game) {
    global $DB, $USER;

    $newrec = new stdClass();
    $newrec->gamekind = $game->gamekind;
    $newrec->gameid = $game->id;
    $newrec->userid = $USER->id;
    $newrec->timestart = time();
    $newrec->timefinish = 0;
    $newrec->timelastattempt = 0;
    $newrec->preview = 0;
    $params = array( 'gameid' => $game->id, 'userid' => $USER->id);
    $newrec->attempt = $DB->get_field( 'game_attempts', 'max(attempt)', $params) + 1;
    $newrec->score = 0;

    if (!($newid = $DB->insert_record( 'game_attempts', $newrec))) {
        print_error("Insert game_attempts: new rec not inserted");
    }

    if ($USER->username == 'guest') {
        $key = 'mod/game:instanceid'.$game->id;
        $_SESSION[ $key] = $newid;
    }

    return $DB->get_record_select( 'game_attempts', 'id='.$newid);
}

/**
 * Get contexts
 */
function game_get_contexts() {
    global $CFG, $COURSE;

    require( $CFG->dirroot.'/question/editlib.php');
    $thiscontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);
    $contexts = new question_edit_contexts( $thiscontext);
    $caps = array( 'moodle/question:viewmine', 'moodle/question:viewall');

    return $contexts->having_one_cap( $caps);
}

/**
 * Export split files
 *
 * @param int $courseid
 * @param stdClass $context
 * @param string $filearea
 * @param int $id
 * @param string $line
 * @param string $destdir
 * @param array $files
 */
function game_export_split_files( $courseid, $context, $filearea, $id, $line, $destdir, &$files) {
    global $CFG, $DB;

    $contextcourse = false;

    $fs = get_file_storage();

    for (;;) {
        $pos1 = strpos( $line, '@@PLUGINFILE@@');
        if ($pos1 === false) {
            break;
        }

        $pos2 = strpos( $line, '"', $pos1);
        if ($pos2 === false) {
            break;
        }

        $file = urldecode( substr( $line, $pos1 + 15, $pos2 - $pos1 - 15));

        $posext = strrpos( $file, '.');
        $filenoext = substr( $file, $posext);
        $ext = substr( $file, $posext + 1);
        $oldfile = $CFG->wwwroot."/pluginfile.php/$context->id/mod_game/$filearea/$id/$file";
        for ($i = 0;; $i++) {
            $newfile = $filenoext.$i;
            $newfile = md5( $newfile).'.'.$ext;
            if (!array_search( $newfile, $files)) {
                break;
            }
        }

        $line = substr( $line, 0, $pos1).'images/'.$newfile.substr( $line, $pos2);
        $files[ $oldfile] = $newfile;

        // Have to copy the files.
        if (count( $files) == 1) {
            mkdir( $destdir.'/images');
        }

        if ($contextcourse === false) {
            if (!$contextcourse = get_context_instance(CONTEXT_COURSE, $courseid)) {
                print_error('nocontext');
            }
        }
        $params = array( 'component' => 'question', 'filearea' => $filearea,
            'itemid' => $id, 'filename' => $file, 'contextid' => $contextcourse->id);
        $rec = $DB->get_record( 'files', $params);

        if (!$file = $fs->get_file_by_hash($rec->pathnamehash) or $file->is_directory()) {
            continue;
        }
        $file->copy_content_to( $destdir.'/images/'.$newfile);
    }

    return $line;
}

/**
 * Grade questions
 *
 * @param array $questions
 */
function game_grade_questions( $questions) {
    $grades = array();
    foreach ($_POST as $key => $value) {
        $id = game_question_get_id_from_name_prefix( $key);
        if ($id === false) {
            continue;
        }

        if (array_key_exists( $id, $grades)) {
            $grade = $grades[ $id];
        } else {
            $grade = new stdClass();
            $grade->grade = 0;
            $grade->id = $id;
        }

        $grade->response = $value;

        $question = $questions[ $id];
        if ($question->qtype == 'multichoice') {
            $answer = $question->options->answers[ $value];
            $grade->grade += $answer->fraction;
        } else if ($question->qtype == 'shortanswer') {
            foreach ($question->options->answers as $answerid => $answer) {
                if (game_upper( $answer->answer == $value)) {
                    $grade->grade = $answer->fraction;
                    break;
                }
            }
        }

        $grades[ $grade->id] = $grade;
    }

    return $grades;
}

/**
 * Extract question id from the prefix of form element names
 *
 * @return integer      The question id
 * @param string $name  The name that contains a prefix that was
 *                      constructed with {@link question_make_name_prefix()}
 */
function game_question_get_id_from_name_prefix($name) {
    if (!preg_match('/^resp([0-9]+)_/', $name, $matches)) {
        return false;
    }
    return (integer) $matches[ 1];
}

/**
 * Get version.
 */
function game_get_version() {
    global $CFG, $DB;

    if (($rec = $DB->get_record( 'modules', array( 'name' => 'game'))) === false) {
        return '';
    }

    if (isset($rec->version)) {
        return $rec->version;
    }

    $module = new StdClass;
    require($CFG->dirroot . '/mod/game/version.php');
    return $module->version;
}

/**
 * Can start a new attempt
 *
 * @param object $game the game object.
 */
function game_can_start_new_attempt( $game) {
    global $DB, $USER;

    if ($game->maxattempts == 0) {
        return true;
    }

    $sql = "SELECT COUNT(*) as c FROM {game_attempts} WHERE gameid={$game->id} AND userid={$USER->id}";
    if (($rec = $DB->get_record_sql( $sql)) === false) {
        return true;
    }

    if ($rec->c >= $game->maxattempts) {
        return false;
    } else {
        return true;
    }
}

/**
 * strlen
 *
 * @param string $str
 */
function game_strlen( $str) {
    if (game_get_moodle_version() >= '02.08') {
        return core_text::strlen( $str);
    } else if (game_get_moodle_version() >= '02.04') {
        return textlib::strlen( $str);
    } else {
        return textlib_get_instance()->strlen( $str);
    }
}

/**
 * substr
 */
function game_substr() {
    $num = func_num_args();
    $a = func_get_args();

    if ($num == 3) {
        if (game_get_moodle_version() >= '02.08') {
            return core_text::substr( $a[ 0], $a[ 1], $a[ 2]);
        } else if (game_get_moodle_version() >= '02.04') {
            return textlib::substr( $a[ 0], $a[ 1], $a[ 2]);
        } else {
            return textlib_get_instance()->substr( $a[ 0], $a[ 1], $a[ 2]);
        }
    } else if ($num == 2) {
        if (game_get_moodle_version() >= '02.08') {
            return core_text::substr( $a[ 0], $a[ 1]);
        }
        if (game_get_moodle_version() >= '02.04') {
            return textlib::substr( $a[ 0], $a[ 1]);
        } else {
            return textlib_get_instance()->substr( $a[ 0], $a[ 1]);
        }
    } else {
        die( 'Substr requires 2 or 3 parameters');
    }
}

/**
 * strtoupper
 *
 * @param string $str
 */
function game_strtoupper( $str) {
    if (game_get_moodle_version() >= '02.08') {
        return core_text::strtoupper( $str);
    }
    if (game_get_moodle_version() >= '02.04') {
        return textlib::strtoupper( $str);
    } else if (game_get_moodle_version() >= '02.01') {
        return textlib_get_instance()->strtoupper( $str);
    } else {
        return textlib_get_instance()->qstrtoupper( $str);
    }
}

/**
 * strpos
 *
 * @param string $haystack
 * @param string $needle
 * @param int $offset
 */
function game_strpos( $haystack, $needle, $offset = 0) {
    if (game_get_moodle_version() >= '02.08') {
        return core_text::strpos( $haystack, $needle, $offset);
    }

    if (game_get_moodle_version() >= '02.04') {
        return textlib::strpos( $haystack, $needle, $offset);
    }

    return textlib_get_instance()->strpos( $haystack, $needle, $offset);
}

/**
 * show query
 *
 * @param object $game the game
 * @param stdClass $query
 * @param string $text
 */
function game_show_query( $game, $query, $text) {
    global $CFG, $DB;

    if ($game->glossaryid) {
        $sql = "SELECT id,course FROM {$CFG->prefix}glossary WHERE id={$game->glossaryid}";
        $glossary = $DB->get_record_sql( $sql);
        $cmglossary = get_coursemodule_from_instance('glossary', $game->glossaryid, $glossary->course);
        $contextglossary = game_get_context_module_instance( $cmglossary->id);
        return game_filterglossary(str_replace( '\"', '"', $text), $query->glossaryentryid, $contextglossary->id, $game->course);
    } else if ($query->questionid) {
        $cmgame = get_coursemodule_from_instance('game', $game->id, $game->course);
        $context = game_get_context_module_instance( $cmgame->id);
        $text = str_replace( array("\'", '\"'), array("'", '"'), $text);
        return game_filterquestion($text, $query->questionid, $context->id, $game->course);
    }

    return $text;
}

/**
 * use events?
 */
function game_use_events() {
    $version = game_get_moodle_version();

    return( $version >= '02.07');
}

/**
 * Get the feedback text that should be show to a student who got this grade on this game.
 *
 * @param float $grade a grade on this game.
 * @param integer $gameid the id of the game object.
 * @return string the comment that corresponds to this grade (empty string if there is not one.
 */
function game_feedback_for_grade($grade, $gameid) {
    return '';
}
