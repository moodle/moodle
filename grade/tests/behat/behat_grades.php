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

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

/**
 * Behat grade related steps definitions.
 *
 * @package    core_grades
 * @copyright  2022 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_grades extends behat_base {

    /**
     * Return the list of partial named selectors.
     *
     * @return array
     */
    public static function get_partial_named_selectors(): array {
        return [
            new behat_component_named_selector(
                'initials bar',
                [".//*[contains(concat(' ', @class, ' '), ' initialbar ')]//span[contains(., %locator%)]/parent::div"]
            ),
            new behat_component_named_selector(
                'grade_actions',
                ["//td[count(//table[@id='user-grades']//th[contains(., %locator%)]/preceding-sibling::th)]//*[@data-type='grade']"]
            ),
        ];
    }

    /**
     * Select a given element within a specific container instance.
     *
     * @Given /^I select "(?P<input_value>(?:[^"]|\\")*)" in the "(?P<instance>(?:[^"]|\\")*)" "(?P<instance_type>(?:[^"]|\\")*)"$/
     * @param string $value The Needle
     * @param string  $element The Haystack to select within
     * @param string $selectortype What type of haystack we are looking in
     */
    public function i_select_in_the($value, $element, $selectortype) {
        // Getting the container where the text should be found.
        $container = $this->get_selected_node($selectortype, $element);
        $node = $this->find('xpath', './/input[@value="' . $value . '"]', false, $container);
        $node->click();
    }

    /**
     * Gets the grade item id from its name.
     *
     * @throws Exception
     * @param string $itemname Item name
     * @return int
     */
    protected function get_grade_item_id(string $itemname): int {

        global $DB;

        if ($id = $DB->get_field('grade_items', 'id', ['itemname' => $itemname])) {
            return $id;
        }

        // The course total is a special case.
        if ($itemname === "Course total") {
            if (!$id = $DB->get_field('grade_items', 'id', ['itemtype' => 'course'])) {
                throw new Exception('The specified grade_item with name "' . $itemname . '" does not exist');
            }
            return $id;
        }

        // Find a category with the name.
        if ($catid = $DB->get_field('grade_categories', 'id', ['fullname' => $itemname])) {
            if ($id = $DB->get_field('grade_items', 'id', ['iteminstance' => $catid])) {
                return $id;
            }
        }

        throw new Exception('The specified grade_item with name "' . $itemname . '" does not exist');
    }

    /**
     * Gets course grade category id from coursename.
     *
     * @throws Exception
     * @param string $coursename
     * @return int
     */
    protected function get_course_grade_category_id(string $coursename): int {

        global $DB;

        $sql = "SELECT gc.id
                  FROM {grade_categories} gc
             LEFT JOIN {course} c
                    ON c.id = gc.courseid
                 WHERE c.fullname = ?
                   AND gc.depth = 1";

        if ($id = $DB->get_field_sql($sql, [$coursename])) {
            return $id;
        }

        throw new Exception('The specified course grade category with course name "' . $coursename . '" does not exist');
    }

    /**
     * Gets grade category id from its name.
     *
     * @throws Exception
     * @param string $categoryname
     * @return int
     */
    protected function get_grade_category_id(string $categoryname): int {

        global $DB;

        $sql = "SELECT gc.id
                  FROM {grade_categories} gc
             LEFT JOIN {course} c
                    ON c.id = gc.courseid
                 WHERE gc.fullname = ?";

        if ($id = $DB->get_field_sql($sql, [$categoryname])) {
            return $id;
        }

        throw new Exception('The specified grade category with name "' . $categoryname . '" does not exist');
    }

    /**
     * Clicks on given grade item menu.
     *
     * @Given /^I click on grade item menu "([^"]*)" of type "([^"]*)" on "([^"]*)" page$/
     * @param string $itemname Item name
     * @param string $itemtype Item type - grade item, category or course
     * @param string $page Page - setup or grader
     * @throws Exception
     */
    public function i_click_on_grade_item_menu(string $itemname, string $itemtype, string $page) {

        if ($itemtype == 'gradeitem') {
            $itemid = $this->get_grade_item_id($itemname);
        } else if ($itemtype == 'category') {
            $itemid = $this->get_grade_category_id($itemname);
        } else if ($itemtype == 'course') {
            $itemid = $this->get_course_grade_category_id($itemname);
        } else {
            throw new Exception('Unknown item type: ' . $itemtype);
        }

        $xpath = "//table[@id='grade_edit_tree_table']";

        if (($page == 'grader') || ($page == 'setup')) {
            if ($page == 'grader') {
                $xpath = "//table[@id='user-grades']";
            }

            if ($itemtype == 'gradeitem') {
                $xpath .= "//*[@data-type='item'][@data-id='" . $itemid . "']";
            } else if (($itemtype == 'category') || ($itemtype == 'course')) {
                $xpath .= "//*[@data-type='category'][@data-id='" . $itemid . "']";
            } else {
                throw new Exception('Unknown item type: ' . $itemtype);
            }
        } else {
            throw new Exception('Unknown page: ' . $page);
        }
        $this->execute("behat_general::i_click_on", [$this->escape($xpath), "xpath_element"]);
    }
}
