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

/*
 * Default class for report plugins
 *
 * Doesn't do anything on it's own -- it needs to be extended.
 * This class displays quiz reports.  Because it is called from
 * within /mod/game/report.php you can assume that the page header
 * and footer are taken care of.
 *
 * This file can refer to itself as report.php to pass variables
 * to itself - all these will also be globally available.  You must
 * pass "id=$cm->id" or q=$quiz->id", and "mode=reportname".
 */

// Included by ../report.php.

/**
 * the default report.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * default report
 *
 * @package    mod_game
 * @copyright  2014 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class game_default_report {

    /**
     * Display
     *
     * @param stdClass $cm
     * @param stdClass $course
     * @param stdClass $game
     */
    public function display($cm, $course, $game) {
        // This function just displays the report.
        return true;
    }

    /**
     * print header and tabs
     *
     * @param stdClass $cm
     * @param stdClass $course
     * @param stdClass $game
     * @param string $reportmode
     * @param string $meta
     */
    public function print_header_and_tabs($cm, $course, $game, $reportmode = "overview", $meta = "") {
        global $CFG;

        // Define some strings.
        $strgames = get_string("modulenameplural", "game");
        $strgame  = get_string("modulename", "game");

        // Print the page header.
        if (function_exists( 'build_navigation')) {
            $navigation = build_navigation('', $cm);
            echo $OUTPUT->heading( $course->shortname, $course->shortname, $navigation);
        } else {
            echo $OUTPUT->heading(format_string($game->name), "",
                     "<a href=\"index.php?id=$course->id\">$strgames</a>
                      -> ".format_string($game->name),
                     '', $meta, true, update_module_button($cm->id, $course->id, $strgame), navmenu($course, $cm));
        }

        // Print the tabs.
        $currenttab = 'reports';
        $mode = $reportmode;

        require('tabs.php');
    }
}
