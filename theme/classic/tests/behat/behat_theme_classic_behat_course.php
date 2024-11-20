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
 * Behat course-related step definition overrides for the Classic theme.
 *
 * @package    theme_classic
 * @category   test
 * @copyright  2019 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../course/tests/behat/behat_course.php');

/**
 * Course-related step definition overrides for the Classic theme.
 *
 * @package    theme_classic
 * @category   test
 * @copyright  2019 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_classic_behat_course extends behat_course {

    /**
     * Go to the course participants.
     */
    public function i_navigate_to_course_participants() {
        $coursestr = behat_context_helper::escape(get_string('courses'));
        $mycoursestr = behat_context_helper::escape(get_string('mycourses'));
        $xpath = "//div[contains(@class,'block')]//li[contains(@class,'contains_branch')]" .
            "[p/*[string(.)=$coursestr or string(.)=$mycoursestr]]";
        $this->execute('behat_general::i_click_on_in_the', [get_string('participants'), 'link', $xpath, 'xpath_element']);
    }

    /**
     * Returns whether the user has permission to modify this course.
     *
     * @return bool
     */
    protected function is_course_editor(): bool {
        // If the course is already in editing mode then it will have the class 'editing' on the body.
        // This is a 'cheap' way of telling if the course is in editing mode.
        $body = $this->find('css', 'body');
        if ($body->hasClass('editing')) {
            return true;
        }

        // If the course is not already in editing mode, then the only real way to find out if the current user may edit
        // the page is to look for the "Turn editing on" button.
        // If the button is found then the user is a course editor.
        try {
            $this->find('button', get_string('turneditingon'), false, false, 0);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
