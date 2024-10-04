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
 * Behat grade related step definition overrides for the Classic theme.
 *
 * @package    theme_classic
 * @category   test
 * @copyright  2019 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../grade/tests/behat/behat_grade.php');

use Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Behat grade overrides for the Classic theme.
 *
 * @package    theme_classic
 * @category   test
 * @copyright  2019 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_classic_behat_grade extends behat_grade {

    /**
     * Navigates to the course gradebook and selects a specified item from the grade navigation tabs.
     *
     * @param string $gradepath
     */
    public function i_navigate_to_in_the_course_gradebook($gradepath) {
        // If we are not on one of the gradebook pages already, follow "Grades" link in the navigation block.
        $xpath = '//div[contains(@class,\'grade-navigation\')]';
        if (!$this->getSession()->getPage()->findAll('xpath', $xpath)) {
            $this->execute("behat_general::i_click_on_in_the", array(get_string('grades'), 'link',
                    get_string('pluginname', 'block_navigation'), 'block'));
        }

        $this->execute('behat_forms::i_set_the_field_to', [get_string('gradebooknavigationmenu', 'grades'), $gradepath]);
    }
}
