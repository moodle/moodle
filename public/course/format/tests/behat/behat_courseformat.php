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

declare(strict_types=1);

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ExpectationException;

/**
 * Behat step definitions for Course format
 *
 * @package     core_courseformat
 * @copyright   2023 Mikel Martín <mikel@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_courseformat extends behat_base {

    /**
     * Return the list of partial named selectors
     *
     * @return behat_component_named_selector[]
     */
    public static function get_partial_named_selectors(): array {
        return [
            new behat_component_named_selector('Activity completion', [
                ".//*[@data-activityname=%locator%]//*[@data-region='completionrequirements']",
            ]),
            new behat_component_named_selector('Activity groupmode', [
                ".//*[@data-activityname=%locator%]//*[@data-region='groupmode']",
            ]),
            new behat_component_named_selector('Activity visibility', [
                ".//*[@data-activityname=%locator%]//*[@data-region='visibility']",
            ]),
            new behat_component_named_selector('Activity icon', [
                ".//*[@data-activityname=%locator%]//*[@data-region='activity-icon']",
            ]),
            new behat_component_named_selector('Section visibility', [
                ".//*[@data-sectionname=%locator%]//*[@data-region='sectionvisibility']",
            ]),
            new behat_component_named_selector('Section actions menu', [
                ".//*[@data-sectionname=%locator%]//*[@data-for='section_title']//*[@data-region='sectionactionsmmenu']",
            ]),
        ];
    }

    /**
     * Checks that the course linear navigation is visible.
     *
     * @Then /^the course linear navigation should be visible$/
     * @throws ExpectationException
     */
    public function the_course_linear_navigation_should_be_visible(): void {
        if (!$this->running_javascript()) {
            return;
        }

        $node = $this->get_selected_node('css', '.course-linear-navigation');
        if (!$node->isVisible()) {
            throw new ExpectationException('The course linear navigation is not visible', $this->getSession());
        }
    }

    /**
     * Checks that the course linear navigation is not visible.
     *
     * @Then /^the course linear navigation should not be visible$/
     * @throws ExpectationException
     */
    public function the_course_linear_navigation_should_not_be_visible(): void {
        try {
            $this->the_course_linear_navigation_should_be_visible();
        } catch (ExpectationException $e) {
            // All as expected.
            return;
        }

        throw new ExpectationException('The course linear navigation is visible', $this->getSession());
    }
}
