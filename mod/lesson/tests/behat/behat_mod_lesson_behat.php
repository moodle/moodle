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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
// For that reason, we can't even rely on $CFG->admin being available here.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;
use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Step definitions related mod_lesson.
 *
 * @package    mod_lesson
 * @category   test
 * @copyright  2021 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_lesson_behat extends behat_base {

    /**
     * Select the lesson edit type [Collapsed|Expanded]
     *
     * @Given i select edit type :edittype
     *
     * @param  string $edittype The edit type of either Collapsed or Expanded
     */
    public function i_select_edit_type(string $edittype): void {

        $typestring = ($edittype == 'Collapsed') ? get_string('collapsed', 'mod_lesson') : get_string('full', 'mod_lesson');
        try {
            $this->execute("behat_forms::i_select_from_the_singleselect", [$typestring, 'jump']);
        } catch (ElementNotFoundException $e) {
            $this->execute("behat_general::click_link", [$typestring]);
        }
    }

    /**
     * Go to the lesson essay grading page.
     *
     * @Given i grade lesson essays
     */
    public function i_grade_lesson_essays(): void {
        try {
            $this->execute("behat_general::i_click_on", [get_string('manualgrading', 'mod_lesson'), 'button']);
        } catch (ElementNotFoundException $e) {
            $this->execute("behat_general::click_link", [get_string('manualgrading', 'mod_lesson')]);
        }
    }

    /**
     * Go to the lesson edit page.
     *
     * @Given i edit the lesson
     */
    public function i_edit_the_lesson(): void {
        try {
            $this->execute("behat_general::click_link", [get_string('editlesson', 'mod_lesson')]);
        } catch (ElementNotFoundException $e) {
            $this->execute("behat_general::i_click_on_in_the",
                [get_string('editlesson', 'mod_lesson'), 'button', 'region-main', 'region']
            );
        }
    }
}
