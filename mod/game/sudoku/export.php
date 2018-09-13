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
 * Exports a sudoku.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require( "../../../config.php");
require_login();

export();

/**
 * Exports
 */
function export() {
    global $CFG;

    $file = "import.php";
    $h = fopen($file, 'w') or die("can't open file");

    fwrite( $h, "<?php\r\n");
    fwrite( $h, "require( \"../../../config.php\");\r\n\r\n");

    if (($recs = get_records_select( 'game_sudoku_database')) == false) {
        print_error('empty');
    }

    $i = 0;
    foreach ($recs as $rec) {
        fwrite( $h, "execute_sql( \"INSERT INTO {game_sudoku_database} ( level, opened, data) ".
            "VALUES ($rec->level, $rec->opened, '$rec->data')\", false);\r\n");
        if (++$i % 10 == 0) {
            fwrite( $h, "\r\n");
        }
    }
    fwrite( $h, "\r\necho'Finished importing';");

    fclose($h);
}
