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
 * Class to represent the source of a HotPot quiz
 * Source type: hp_6_jmatch_html
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/source/hp/6/jmatch/class.php');

/**
 * hotpot_source_hp_6_jmatch_html
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_source_hp_6_jmatch_html extends hotpot_source_hp_6_jmatch {
    const REQUIRED_FILETYPES = array(
        'htm', 'html'
    );
    const BANNED_STRINGS = array(
        // JMatch intro (Michael Rottmeier)
        '<div class="Feedback" id="DivIntroPage">'
    );

    /*
     * required_strings_html()
     *
     * @param string $content of HTML file (passed by reference)
     * @return array of required strings for HTML content
     */
    static public function required_strings_html(&$content)  {
        $strings = parent::required_strings_html($content);
        if (is_numeric(in_array('<div id="MainDiv" class="StdDiv">', $strings))) {
            $strings[] = '<div id="MatchDiv" style="text-align: center;">';
        } else {
            // drag-and-drop versions of JMatch and JMix
            $strings[] = 'F = new Array();';
            $strings[] = 'D = new Array();';
        }
        return $strings;
    }
}
