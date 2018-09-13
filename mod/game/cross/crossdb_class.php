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
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * CrossDB class
 *
 * @package mod_game
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This class is a cross that can load and save to a table
 *
 * @package mod_game
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
class CrossDB extends Cross {
    /**
     * Save cross.
     *
     * @param stdClass $game
     * @param array $crossm
     * @param array $crossd
     * @param int $id
     */
    public function savecross( $game, &$crossm, $crossd, $id) {
        global $DB, $USER;

        $crossm->id = $id;
        $crossm->sourcemodule = $game->sourcemodule;

        $this->delete_records( $id);

        if (!(game_insert_record( "game_cross", $crossm))) {
            print_error( 'Insert page: new page game_cross not inserted');
        }

        foreach ($crossd as $rec) {
            $rec->attemptid = $id;
            $rec->questiontext = addslashes( $rec->questiontext);

            $rec->gamekind = $game->gamekind;
            $rec->gameid = $game->id;
            $rec->userid = $USER->id;
            $rec->sourcemodule = $game->sourcemodule;

            if (!$DB->insert_record( 'game_queries', $rec)) {
                print_error( 'Insert page: new page game_queries not inserted');
            }
            game_update_repetitions($game->id, $USER->id, $rec->questionid, $rec->glossaryentryid);
        }

        return true;
    }

    /**
     * Delete records.
     *
     * @param int $id
     */
    public function delete_records( $id) {
        global $DB;

        if (!$DB->delete_records( 'game_queries', array( 'attemptid' => $id))) {
            print_error( "Can't delete from game_queries attemptid=$id");
        }
        if (!$DB->delete_records( 'game_cross', array( 'id' => $id))) {
            print_error( "Can't delete from game_cross id=$id");
        }
    }


    /**
     * Load cross.
     *
     * @param string $g
     * @param boolean $done
     * @param string $html
     * @param stdClass $game
     * @param stdClass $attempt
     * @param stdClass $crossrec
     * @param boolean $onlyshow
     * @param boolean $showsolution
     * @param boolean $finishattempt
     * @param boolean $showhtmlsolutions
     * @param string $language
     * @param boolean $showstudentguess
     * @param stdClass $context
     */
    public function loadcross( $g, &$done, &$html, $game, $attempt, $crossrec, $onlyshow, $showsolution,
    &$finishattempt, $showhtmlsolutions, &$language, $showstudentguess, $context) {
        global $DB;

        $info = '';
        $correctletters = 0;
        $allletters = 0;
        $wrongletters = 0;
        $html = '';
        $done = false;

        $loadfromdb = ( $g == "");

        $this->mmincol = $this->mminrow = 0;
        $this->mmaxcol = $crossrec->usedcols;
        $this->mmaxrow = $crossrec->usedrows;

        if ($g == "") {
            $g = str_repeat( ' ', $this->mmaxcol * $this->mmaxrow);
        }

        $load = false;

        $puzzle = str_repeat('.', $this->mmaxrow * $this->mmaxcol);
        if ($recs = $DB->get_records( 'game_queries', array( 'attemptid' => $crossrec->id))) {
            $a = array();
            foreach ($recs as $rec) {
                if ($rec->horizontal) {
                    $key = sprintf( 'h%10d %10d', $rec->row, $rec->col);
                } else {
                    $key = sprintf( 'v%10d %10d', $rec->col, $rec->row);
                }
                $a[ $key] = $rec;
            }

            ksort( $a);
            $b = array();
            $correctletters = $wrongletters = $restletters = 0;

            foreach ($a as $rec) {
                $this->updatecrossquestions( $rec, $g, $pos, $correctletters,
                    $wrongletters, $restletters, $game, $attempt, $crossrec, $loadfromdb);
                $b[] = $rec;

                if (($rec->col != 0) and ($rec->row != 0)) {
                    $load = true;
                }
                if ($language == '') {
                    $language = game_detectlanguage( $rec->answertext);
                }
            }
            $info = $this->game_cross_computecheck( $correctletters,  $wrongletters,
                $restletters, $game, $attempt, $done, $onlyshow, $showsolution, $finishattempt);
            $html = $this->showhtml_base( $crossrec, $b, $showsolution, $showhtmlsolutions, $showstudentguess, $context, $game);
        }

        if ($load == false) {
            $finishattempt = true;
        }

        return $info;
    }

    /**
     * Compute check.
     *
     * @param int $correctletters
     * @param int $wrongletters
     * @param string $restletters
     * @param stdClass $game
     * @param stdClass $attempt
     * @param boolean $done
     * @param boolean $onlyshow
     * @param boolean $showsolution
     * @param boolean $finishattempt
     */
    public function game_cross_computecheck( $correctletters,  $wrongletters, $restletters, $game,
        $attempt, &$done, $onlyshow, $showsolution, $finishattempt) {

        $ret = '';

        $and = get_string( 'and', 'game');

        $a = array();
        if ($correctletters) {
            $a[] = $correctletters.' '.
                ( $correctletters > 1 ? get_string( 'cross_corrects', 'game') : get_string( 'cross_correct', 'game'));
        }
        if ($wrongletters) {
            $a[] = '<b>'.$wrongletters.' '.
                ( $wrongletters > 1 ? get_string( 'cross_errors', 'game') : get_string( 'cross_error', 'game')).'</b>';
        }

        if ($correctletters > 1 or $wrongletters > 1) {
            $ret = get_string( 'cross_found_many', 'game');
        } else if ( count( $a)) {
            $ret = get_string( 'cross_found_one', 'game');
        } else {
            $ret = '';
        }

        $i = 0;
        foreach ($a as $msg) {
            $i++;

            if ($i == 1) {
                $ret .= ' '.$msg;
            } else if ( $i == count($a)) {
                $ret .= ' '.get_string( 'and', 'game').' '.$msg;
            } else {
                $ret .= ', '.$msg;
            }
        }

        $done = ( $restletters == 0 ? true : false);

        if ($finishattempt == false) {
            if ($onlyshow or $showsolution) {
                return $ret;
            }
        } else {
            $done = 1;
        }

        $grade = $correctletters / ($correctletters + $restletters);
        $ret .= '<br>'.get_string( 'grade', 'game').' '.round( $grade * 100).' %';

        game_updateattempts( $game, $attempt, $grade, $done);

        return $ret;
    }

    /**
     * Update cross questions.
     *
     * @param stdClass $rec  (is a record of cross_questions).
     * @param string $g
     * @param int $pos
     * @param int $correctletters
     * @param int $wrongletters
     * @param int $restletters
     * @param stdClass $game
     * @param stdClass $attempt
     * @param stdClass $crossrec
     * @param boolean $loadfromdb
     */
    public function updatecrossquestions( &$rec, &$g, &$pos, &$correctletters, &$wrongletters,
        &$restletters, $game, $attempt, $crossrec, $loadfromdb) {

        global $DB, $USER;

        $word = $rec->answertext;
        $len = game_strlen( $word);

        if ($loadfromdb) {
            $guess = $rec->studentanswer;
        } else {
            $guess = game_substr( $g, $pos, $len);
        }

        $lenguess = game_strlen( $guess);;
        $pos += $len;

        $isempty = true;
        for ($i = 0; $i < $len; $i++) {
            if ($i < $lenguess) {
                $letterguess = game_substr( $guess, $i, 1);
            } else {
                $letterguess = " ";
            }

            if ($letterguess != ' ') {
                $isempty = false;
            }
            $letterword = game_substr( $word, $i, 1);
            if ($letterword != $letterguess) {
                if (($letterguess != ' ' and $letterguess != '_')) {
                    $wrongletters++;
                }
                game_setchar( $guess, $i, '_');
                $restletters++;
            } else if ($letterguess == ' ') {
                if ($guess == $word) {
                    $correctletters++;
                }
            } else {
                $correctletters++;
            }
        }

        if ($isempty) {
            return;
        }
        if (($rec->studentanswer == $guess )) {
            return;
        }

        $rec->studentanswer = $guess;

        $updrec = new stdClass();
        $updrec->studentanswer = $guess;
        $updrec->id = $rec->id;
        if (!$DB->update_record( 'game_queries', $updrec, $rec->id)) {
            print_error( 'Update game_queries: not updated');
        }

        $score = $correctletters / $len;
        game_update_queries( $game, $attempt, $rec, $score, $guess);
    }
}
