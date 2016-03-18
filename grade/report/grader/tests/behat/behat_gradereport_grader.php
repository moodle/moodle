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
     * Click a given user grade cell.
     *
     * @Given /^I click on student "([^"]*)" for grade item "([^"]*)"$/
     * @param string $student
     * @param string $itemname
     */
    public function i_click_on_student_and_grade_item($student, $itemname) {
        $xpath = $this->get_student_and_grade_cell_selector($student, $itemname);

        $this->execute("behat_general::i_click_on", array($this->escape($xpath), "xpath_element"));
    }

    /**
     * Remove focus for a grade value cell.
     *
     * @Given /^I click away from student "([^"]*)" and grade item "([^"]*)" value$/
     * @param string $student
     * @param string $itemname
     */
    public function i_click_away_from_student_and_grade_value($student, $itemname) {
        $xpath = $this->get_student_and_grade_value_selector($student, $itemname);

        $this->execute('behat_general::i_take_focus_off_field', array($this->escape($xpath), 'xpath_element'));
    }

    /**
     * Remove focus for a grade value cell.
     *
     * @Given /^I click away from student "([^"]*)" and grade item "([^"]*)" feedback$/
     * @param string $student
     * @param string $itemname
     */
    public function i_click_away_from_student_and_grade_feedback($student, $itemname) {
        $xpath = $this->get_student_and_grade_feedback_selector($student, $itemname);

        $this->execute('behat_general::i_take_focus_off_field', array($this->escape($xpath), 'xpath_element'));
    }

    /**
     * Checks grade values with or without a edit box.
     *
     * @Then /^the grade for "([^"]*)" in grade item "([^"]*)" should match "([^"]*)"$/
     * @throws Exception
     * @throws ElementNotFoundException
     * @param string $student
     * @param string $itemname
     * @param string $value
     */
    public function the_grade_should_match($student, $itemname, $value) {
        $xpath = $this->get_student_and_grade_value_selector($student, $itemname);

        $gradefield = $this->getSession()->getPage()->find('xpath', $xpath);
        if (!empty($gradefield)) {
            // Get the field.
            $fieldtype = behat_field_manager::guess_field_type($gradefield, $this->getSession());
            if (!$fieldtype) {
                throw new Exception('Could not get field type for grade field "' . $itemname . '"');
            }
            $field = behat_field_manager::get_field_instance($fieldtype, $gradefield, $this->getSession());
            if (!$field->matches($value)) {
                $fieldvalue = $field->get_value();
                throw new ExpectationException(
                    'The "' . $student . '" and "' . $itemname . '" grade is "' . $fieldvalue . '", "' . $value . '" expected' ,
                    $this->getSession()
                );
            }
        } else {
            // If there isn't a form field, just search for contents.
            $valueliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($value);

            $xpath = $this->get_student_and_grade_cell_selector($student, $itemname);
            $xpath .= "[contains(normalize-space(.)," . $valueliteral . ")]";

            $node = $this->getSession()->getDriver()->find($xpath);
            if (empty($node)) {
                $locatorexceptionmsg = 'Cell for "' . $student . '" and "' . $itemname . '" with value "' . $value . '"';
                throw new ElementNotFoundException($this->getSession(), $locatorexceptionmsg, null, $xpath);
            }
        }
    }

    /**
     * Look for a grade editing field.
     *
     * @Then /^I should see a grade field for "([^"]*)" and grade item "([^"]*)"$/
     * @param string $student
     * @param string $itemname
     */
    public function i_should_see_grade_field($student, $itemname) {
        $xpath = $this->get_student_and_grade_value_selector($student, $itemname);

        $this->execute('behat_general::should_be_visible', array($this->escape($xpath), 'xpath_element'));
    }

    /**
     * Look for a feedback editing field.
     *
     * @Then /^I should see a feedback field for "([^"]*)" and grade item "([^"]*)"$/
     * @param string $student
     * @param string $itemname
     */
    public function i_should_see_feedback_field($student, $itemname) {
        $xpath = $this->get_student_and_grade_feedback_selector($student, $itemname);

        $this->execute('behat_general::should_be_visible', array($this->escape($xpath), 'xpath_element'));
    }

    /**
     * Look for a lack of the grade editing field.
     *
     * @Then /^I should not see a grade field for "([^"]*)" and grade item "([^"]*)"$/
     * @param string $student
     * @param string $itemname
     */
    public function i_should_not_see_grade_field($student, $itemname) {
        $xpath = $this->get_student_and_grade_value_selector($student, $itemname);

        $this->execute('behat_general::should_not_exist', array($this->escape($xpath), 'xpath_element'));
    }

    /**
     * Look for a lack of the feedback editing field.
     *
     * @Then /^I should not see a feedback field for "([^"]*)" and grade item "([^"]*)"$/
     * @param string $student
     * @param string $itemname
     */
    public function i_should_not_see_feedback_field($student, $itemname) {
        $xpath = $this->get_student_and_grade_feedback_selector($student, $itemname);

        $this->execute('behat_general::should_not_exist', array($this->escape($xpath), 'xpath_element'));
    }

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
     * Gets unique xpath selector for a student/grade item combo.
     *
     * @throws Exception
     * @param string $student
     * @param string $itemname
     * @return string
     */
    protected function get_student_and_grade_cell_selector($student, $itemname) {
        $itemid = 'u' . $this->get_user_id($student) . 'i' . $this->get_grade_item_id($itemname);
        return "//table[@id='user-grades']//td[@id='" . $itemid . "']";
    }

    /**
     * Gets xpath for a particular student/grade item grade value cell.
     *
     * @throws Exception
     * @param string $student
     * @param string $itemname
     * @return string
     */
    protected function get_student_and_grade_value_selector($student, $itemname) {
        $cell = $this->get_student_and_grade_cell_selector($student, $itemname);
        return $cell . "//*[contains(@id, 'grade_') or @name='ajaxgrade']";
    }

    /**
     * Gets xpath for a particular student/grade item feedback cell.
     *
     * @throws Exception
     * @param string $student
     * @param string $itemname
     * @return string
     */
    protected function get_student_and_grade_feedback_selector($student, $itemname) {
        $cell = $this->get_student_and_grade_cell_selector($student, $itemname);
        return $cell . "//input[contains(@id, 'feedback_') or @name='ajaxfeedback']";
    }

}
