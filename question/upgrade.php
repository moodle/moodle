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
 * This file contains datbase upgrade code that is called from lib/db/upgrade.php,
 * and also check methods that can be used for pre-install checks via
 * admin/environment.php and lib/environmentlib.php.
 *
 * @package    moodlecore
 * @subpackage questionbank
 * @copyright  2007 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * In Moodle, all random questions should have question.parent set to be the same
 * as question.id. One effect of MDL-5482 is that this will not be true for questions that
 * were backed up then restored. The probably does not cause many problems, except occasionally,
 * if the bogus question.parent happens to point to a multianswer question type, or when you
 * try to do a subsequent backup. Anyway, these question.parent values should be fixed, and
 * that is what this update does.
 */
function question_fix_random_question_parents() {
    global $CFG, $DB;
    $DB->execute("UPDATE {question} SET parent = id WHERE qtype = 'random' AND parent <> id");
    return true;
}
