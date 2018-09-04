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
 * Source type: hp_6_jmatch_html_sort
 *
 * @package   mod-hotpot
 * @copyright 2010 Gordon Bateson <gordon.bateson@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// get parent class
require_once($CFG->dirroot.'/mod/hotpot/source/hp/6/jmatch/html/class.php');

/**
 * hotpot_source_hp_6_jmatch_html_sort
 *
 * @copyright 2010 Gordon Bateson
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class hotpot_source_hp_6_jmatch_html_sort extends hotpot_source_hp_6_jmatch_html {

    /**
     * is_quizfile
     *
     * @param xxx $sourcefile
     * @return xxx
     */
    static public function is_quizfile($sourcefile)  {
        if (! preg_match('/\.html?$/', $sourcefile->get_filename())) {
            // wrong file type
            return false;
        }

        if (! $content = self::get_content($sourcefile)) {
            // empty or non-existant file
            return false;
        }

        if (strpos($content, '<!-- JMatch_Sort v')) {
            if (strpos($content, 'var hauteurUserDefined1')) {
                if (strpos($content, 'var largeurUserDefined2')) {
                    // jmatch-sort
                    return true;
                }
            }
        }

        // not a jmatch-sort file
        return false;
    }
}
