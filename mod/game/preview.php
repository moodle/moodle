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
 * This page prints a particular attempt of game
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once("../../config.php");

require_login();

require_once("lib.php");
require_once("locallib.php");

require_once( "hangman/play.php");
require_once( "cross/play.php");
require_once( "cryptex/play.php");
require_once( "millionaire/play.php");
require_once( "sudoku/play.php");
require_once( "bookquiz/play.php");

require_once( "headergame.php");

$context = game_get_context_module_instance( $cm->id);

if (!has_capability('mod/game:viewreports', $context)) {
    print_error( get_string( 'only_teachers', 'game'));
}

$action  = required_param('action', PARAM_ALPHANUM);
$gamekind  = required_param('gamekind', PARAM_ALPHANUM);
$update  = required_param('update', PARAM_INT);

$attemptid = required_param('attemptid', PARAM_INT);
$attempt = $DB->get_record( 'game_attempts', array('id' => $attemptid));
$game = $DB->get_record( 'game', array( 'id' => $attempt->gameid));
$detail = $DB->get_record( 'game_'.$gamekind, array( 'id' => $attemptid));
$solution = ($action == 'solution');

$PAGE->navbar->add(get_string('preview', 'game'));

switch( $gamekind) {
    case 'cross':
        $g = '';
        $onlyshow = true;
        $endofgame = false;
        $print = false;
        $checkbutton = false;
        $showhtmlsolutions = false;
        $showhtmlprintbutton = true;
        $showstudentguess = false;
        game_cross_play( $update, $game, $attempt, $detail, $g, $onlyshow, $solution,
            $endofgame, $print, $checkbutton, $showhtmlsolutions, $showhtmlprintbutton,
            $showstudentguess, $context);
        break;
    case 'sudoku':
        game_sudoku_play( $update, $game, $attempt, $detail, true, $solution, $context);
        break;
    case 'hangman':
        $preview = ($action == 'preview');
        game_hangman_play( $update, $game, $attempt, $detail, $preview, $solution, $context);
        break;
    case 'cryptex':
        $crossm = $DB->get_record( 'game_cross', array('id' => $attemptid));
        game_cryptex_play( $update, $game, $attempt, $detail, $crossm, false, true, $solution, $context);
        break;
}

echo $OUTPUT->footer();
