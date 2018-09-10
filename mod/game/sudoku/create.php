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
 * Creates a sudoku.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require( "../../../config.php");
require_once("class.Sudoku.php");
require( '../header.php');

$action = optional_param('action', PARAM_ALPHA);   // The action.
require_login();
if ($action == 'create') {
    AppendSudokuB();
} else {
    showform();
}

/**
 * Show form
 */
function showform() {
    $id = required_param('id', PARAM_NUMBER);   // The action.

?>
<form name="form" method="post" action="create.php">
<center>
<table cellpadding="5">
<tr valign="top">
    <td align="right"><b><?php  echo get_string( 'sudoku_create_count', 'game'); ?>:</b></td>
    <td>
        <input type="text" name="count" size="6" value="2" /><br>
    </td>
</tr>	
<tr><td colspan=2><center><br><input type="submit" value="<?php  print_string('sudoku_create_start', 'game') ?>" /></td></tr>
</table>
<input type="hidden" name=action        value="create" >
<input type="hidden" name=level1        value="1" >
<input type="hidden" name=level2        value="10" >
<input type="hidden" name=id        value="<?php  echo $id; ?>" />
</form>

<?php
}

/**
 * Append sudoku
 */
function appendsudokub() {
    global $DB;

    $level1 = required_param('level1', PARAM_NUMBER);
    $level2 = required_param('level2', PARAM_NUMBER);
    $count = required_param('count', PARAM_NUMBER);

    $level = $level1;

    for ($i = 1; $i <= $count; $i++) {
        create( $si, $sp, $level);

        $newrec->data = packsudoku( $si, $sp);
        if (strlen( $newrec->data) != 81) {
            return 0;
        }
        $newrec->level = $level;
        $newrec->opened = GetOpened( $si);

        $DB->insert_record( 'game_sudoku_database', $newrec, true);

        $level++;
        if ($level > $level2) {
            $level = $level1;
        }

        echo get_string( 'sudoku_creating', 'game', $i)."<br>\r\n";
    }
}

/**
 * Pack sudoku
 *
 * @param object $si
 * @param object $sp
 *
 * @return the packed sudoku
 */
function packsudoku( $si, $sp) {
    $data = '';

    for ($i = 1; $i <= 9; $i++) {
        for ($j = 1; $j <= 9; $j++) {
            $c = &$sp->thesquares[$i];
            $c = &$c->getcell($j);
            $solution = $c->asstring( false);

            $c = &$si->thesquares[$i];
            $c = &$c->getCell($j);
            $thesolvedstate = $c->solvedstate();

            if ($thesolvedstate == 1) {
                // Hint.
                $solution = substr( 'ABCDEFGHI', $c->asString( false) - 1, 1);
            }

            $data .= $solution;
        }
    }

    return $data;
}

/**
 * Creates a sudoku
 *
 * @param stdClass $si
 * @param object $sp
 * @param int $level
 *
 * @return true if created correctly
 */
function create( &$si, &$sp, $level=1) {
    for ($i = 1; $i <= 40; $i++) {
        $sp = new sudoku();
        $theinitialposition = $sp->generatepuzzle( 10, 50, $level);
        if (count( $theinitialposition)) {
            break;
        }
    }
    if ($i > 40) {
        return false;
    }

    $si = new sudoku();

    $si->initializepuzzlefromarray( $theinitialposition);

    return true;
}

/**
 * get opened
 *
 * @param stdClass $si
 *
 * @return count of opened
 */
function getopened( $si) {
    $count = 0;

    for ($i = 1; $i <= 9; $i++) {
        for ($j = 1; $j <= 9; $j++) {
            $c = &$si->thesquares[$i];
            $c = &$c->getcell($j);
            $thesolvedstate = $c->solvedstate();

            if ($thesolvedstate == 1) {
                // Hint.
                $count++;
            }
        }
    }

    return $count;
}
