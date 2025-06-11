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

use Behat\Gherkin\Node\TableNode;

require_once(__DIR__ . '/../../../../../lib/behat/behat_deprecated_base.php');

/**
 * Steps definitions that are now deprecated and will be removed in the next releases.
 *
 * This file only contains the steps that previously were in the behat_*.php files in the SAME DIRECTORY.
 * When deprecating steps from other components or plugins, create a behat_COMPONENT_deprecated.php
 * file in the same directory where the steps were defined.
 *
 * @package    gradereport_grader
 * @category   test
 * @copyright  2023 Ilya Tregubov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_gradereport_grader_deprecated extends behat_deprecated_base {

    /**
     * Remove focus for a grade value cell.
     *
     * @deprecated since 4.2 - we don't allow ajax edit on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @Given /^I click away from student "([^"]*)" and grade item "([^"]*)" feedback$/
     * @param string $student
     * @param string $itemname
     */
    public function i_click_away_from_student_and_grade_feedback($student, $itemname) {
        $this->deprecated_message(['behat_gradereport_grader::i_click_away_from_student_and_grade_feedback']);
        $xpath = $this->get_student_and_grade_feedback_selector($student, $itemname);

        $this->execute('behat_general::i_take_focus_off_field', array($this->escape($xpath), 'xpath_element'));
    }

    /**
     * Look for a feedback editing field.
     *
     * @deprecated since 4.2 - we don't allow ajax edit on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @Then /^I should see a feedback field for "([^"]*)" and grade item "([^"]*)"$/
     * @param string $student
     * @param string $itemname
     */
    public function i_should_see_feedback_field($student, $itemname) {
        $this->deprecated_message(['behat_gradereport_grader::i_should_see_feedback_field']);
        $xpath = $this->get_student_and_grade_feedback_selector($student, $itemname);

        $this->execute('behat_general::should_be_visible', array($this->escape($xpath), 'xpath_element'));
    }

    /**
     * Look for a lack of the feedback editing field.
     *
     * @deprecated since 4.2 - we don't allow ajax edit on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @Then /^I should not see a feedback field for "([^"]*)" and grade item "([^"]*)"$/
     * @param string $student
     * @param string $itemname
     */
    public function i_should_not_see_feedback_field($student, $itemname) {
        $this->deprecated_message(['behat_gradereport_grader::i_should_not_see_feedback_field']);
        $xpath = $this->get_student_and_grade_feedback_selector($student, $itemname);

        $this->execute('behat_general::should_not_exist', array($this->escape($xpath), 'xpath_element'));
    }

    /**
     * Gets xpath for a particular student/grade item feedback cell.
     *
     * @deprecated since 4.2 - we don't allow ajax edit on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @throws Exception
     * @param string $student
     * @param string $itemname
     * @return string
     */
    protected function get_student_and_grade_feedback_selector($student, $itemname) {
        $this->deprecated_message(['behat_gradereport_grader::get_student_and_grade_feedback_selector']);

        $cell = $this->get_student_and_grade_cell_selector($student, $itemname);
        return $cell . "//input[contains(@id, 'feedback_') or @name='ajaxfeedback']";
    }

    /**
     * Click a given user grade cell.
     *
     * @deprecated since 4.2 - we don't allow ajax edit on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @Given /^I click on student "([^"]*)" for grade item "([^"]*)"$/
     * @param string $student
     * @param string $itemname
     */
    public function i_click_on_student_and_grade_item($student, $itemname) {
        $this->deprecated_message(['behat_gradereport_grader::i_click_on_student_and_grade_item']);

        $xpath = $this->get_student_and_grade_cell_selector($student, $itemname);

        $this->execute("behat_general::i_click_on", array($this->escape($xpath), "xpath_element"));
    }

    /**
     * Remove focus for a grade value cell.
     *
     * @deprecated since 4.2 - we don't allow ajax edit on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @Given /^I click away from student "([^"]*)" and grade item "([^"]*)" value$/
     * @param string $student
     * @param string $itemname
     */
    public function i_click_away_from_student_and_grade_value($student, $itemname) {
        $this->deprecated_message(['behat_gradereport_grader::i_click_away_from_student_and_grade_value']);

        $xpath = $this->get_student_and_grade_value_selector($student, $itemname);

        $this->execute('behat_general::i_take_focus_off_field', array($this->escape($xpath), 'xpath_element'));
    }

    /**
     * Checks grade values with or without a edit box.
     *
     * @deprecated since 4.2 - we don't allow ajax edit on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @Then /^the grade for "([^"]*)" in grade item "([^"]*)" should match "([^"]*)"$/
     * @throws Exception
     * @throws ElementNotFoundException
     * @param string $student
     * @param string $itemname
     * @param string $value
     */
    public function the_grade_should_match($student, $itemname, $value) {
        $this->deprecated_message(['behat_gradereport_grader::the_grade_should_match']);

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
            $valueliteral = behat_context_helper::escape($value);

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
     * @deprecated since 4.2 - we don't allow ajax edit on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @Then /^I should see a grade field for "([^"]*)" and grade item "([^"]*)"$/
     * @param string $student
     * @param string $itemname
     */
    public function i_should_see_grade_field($student, $itemname) {
        $this->deprecated_message(['behat_gradereport_grader::i_should_see_grade_field']);

        $xpath = $this->get_student_and_grade_value_selector($student, $itemname);

        $this->execute('behat_general::should_be_visible', array($this->escape($xpath), 'xpath_element'));
    }

    /**
     * Look for a lack of the grade editing field.
     *
     * @deprecated since 4.2 - we don't allow ajax edit on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @Then /^I should not see a grade field for "([^"]*)" and grade item "([^"]*)"$/
     * @param string $student
     * @param string $itemname
     */
    public function i_should_not_see_grade_field($student, $itemname) {
        $this->deprecated_message(['behat_gradereport_grader::i_should_not_see_grade_field']);
        $xpath = $this->get_student_and_grade_value_selector($student, $itemname);

        $this->execute('behat_general::should_not_exist', array($this->escape($xpath), 'xpath_element'));
    }

    /**
     * Gets unique xpath selector for a student/grade item combo.
     *
     * @deprecated since 4.2 - we don't allow ajax edit on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @throws Exception
     * @param string $student
     * @param string $itemname
     * @return string
     */
    protected function get_student_and_grade_cell_selector($student, $itemname) {
        $this->deprecated_message(['behat_gradereport_grader::get_student_and_grade_cell_selector']);

        $itemid = 'u' . $this->get_user_id($student) . 'i' . $this->get_grade_item_id($itemname);
        return "//table[@id='user-grades']//td[@id='" . $itemid . "']";
    }

    /**
     * Gets xpath for a particular student/grade item grade value cell.
     *
     * @deprecated since 4.2 - we don't allow ajax edit on grader report anymore.
     * @todo MDL-77107 This will be deleted in Moodle 4.6.
     * @throws Exception
     * @param string $student
     * @param string $itemname
     * @return string
     */
    protected function get_student_and_grade_value_selector($student, $itemname) {
        $this->deprecated_message(['behat_gradereport_grader::get_student_and_grade_value_selector']);

        $cell = $this->get_student_and_grade_cell_selector($student, $itemname);
        return $cell . "//*[contains(@id, 'grade_') or @name='ajaxgrade']";
    }

}
