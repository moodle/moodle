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
 * Implementaton of the quizaccess_safebrowser plugin.
 *
 * @package    quizaccess
 * @subpackage safebrowser
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/accessrule/accessrulebase.php');


/**
* A rule representing the safe browser check.
*
* @copyright  2009 Oliver Rahs
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
class safebrowser_access_rule extends quiz_access_rule_base {
    public function prevent_access() {
        if (!$this->_quizobj->is_preview_user() && !quiz_check_safe_browser()) {
            return get_string('safebrowsererror', 'quiz');
        } else {
            return false;
        }
    }

    public function description() {
        return get_string("safebrowsernotice", "quiz");
    }
}
