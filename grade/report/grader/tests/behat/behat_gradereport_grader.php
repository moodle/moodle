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
 * Behat steps definitions for drag and drop onto image.
 *
 * @package   gradereport_grader
 * @category  test
 * @copyright 2015 Oakland University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Steps definitions related with the drag and drop onto image question type.
 *
 * @copyright 2015 Oakland University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_gradereport_grader extends behat_base {

    /**
     * Gets the user id from its name.
     *
     * @throws Exception
     * @param string $name
     * @return int
     */
    protected function get_user_id($name) {
        global $DB;
        $names = explode(' ', $name);

        if (!$id = $DB->get_field('user', 'id', array('firstname' => $names[0], 'lastname' => $names[1]))) {
            throw new Exception('The specified user with username "' . $name . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the grade item id from its name.
     *
     * @throws Exception
     * @param string $itemname
     * @return int
     */
    protected function get_grade_item_id($itemname) {

        global $DB;

        if ($id = $DB->get_field('grade_items', 'id', array('itemname' => $itemname))) {
            return $id;
        }

        // The course total is a special case.
        if ($itemname === "Course total") {
            if (!$id = $DB->get_field('grade_items', 'id', array('itemtype' => 'course'))) {
                throw new Exception('The specified grade_item with name "' . $itemname . '" does not exist');
            }
            return $id;
        }

        // Find a category with the name.
        if ($catid = $DB->get_field('grade_categories', 'id', array('fullname' => $itemname))) {
            if ($id = $DB->get_field('grade_items', 'id', array('iteminstance' => $catid))) {
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
    protected function get_course_grade_category_id(string $coursename) : int {

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
    protected function get_grade_category_id(string $categoryname) : int {

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
     * @Given /^I click on grade item menu "([^"]*)"$/
     * @param string $itemname
     */
    public function i_click_on_grade_item_menu(string $itemname) {

        $xpath = $this->get_gradeitem_selector($itemname);

        $this->execute("behat_general::i_click_on", array($this->escape($xpath), "xpath_element"));
    }

    /**
     * Clicks on course grade category menu.
     *
     * @Given /^I click on course grade category menu "([^"]*)"$/
     * @param string $coursename
     */
    public function i_click_on_course_category_menu(string $coursename) {

        $xpath = $this->get_course_grade_category_selector($coursename);

        $this->execute("behat_general::i_click_on", array($this->escape($xpath), "xpath_element"));
    }

    /**
     * Clicks on given grade category menu.
     *
     * @Given /^I click on grade category menu "([^"]*)"$/
     * @param string $categoryname
     */
    public function i_click_on_category_menu(string $categoryname) {

        $xpath = $this->get_grade_category_selector($categoryname);

        $this->execute("behat_general::i_click_on", array($this->escape($xpath), "xpath_element"));
    }


    /**
     * Gets unique xpath selector for a grade item.
     *
     * @throws Exception
     * @param string $itemname
     * @return string
     */
    protected function get_gradeitem_selector(string $itemname) : string {

        $itemid = $this->get_grade_item_id($itemname);
        return "//table[@id='user-grades']//*[@data-id='" . $itemid . "']";
    }

    /**
     * Gets unique xpath selector for a course category.
     *
     * @throws Exception
     * @param string $coursename
     * @return string
     */
    protected function get_course_grade_category_selector(string $coursename) {

        $itemid = $this->get_course_grade_category_id($coursename);
        return "//table[@id='user-grades']//*[@data-id='" . $itemid . "']";
    }

    /**
     * Gets unique xpath selector for a grade category.
     *
     * @throws Exception
     * @param string $categoryname
     * @return string
     */
    protected function get_grade_category_selector(string $categoryname) : string {

        $itemid = $this->get_grade_category_id($categoryname);
        return "//table[@id='user-grades']//*[@data-id='" . $itemid . "']";
    }

    /**
     * Clicks on given user menu.
     *
     * @Given /^I click on user menu "([^"]*)"$/
     * @param string $student
     */
    public function i_click_on_user_menu(string $student) {

        $xpath = $this->get_user_selector($student);

        $this->execute("behat_general::i_click_on", array($this->escape($xpath), "xpath_element"));
    }

    /**
     * Gets unique xpath selector for a user.
     *
     * @throws Exception
     * @param string $student
     * @return string
     */
    protected function get_user_selector(string $student) : string {

        $userid = $this->get_user_id($student);
        return "//table[@id='user-grades']//*[@data-id='" . $userid . "']";
    }

}
