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
 * Steps definitions related with forms.
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Behat\Context\Step\When as When,
    Behat\Behat\Context\Step\Then as Then,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Element\NodeElement as NodeElement,
    Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Forms-related steps definitions.
 *
 * @package    core
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_forms extends behat_base {

    /**
     * Presses button with specified id|name|title|alt|value.
     *
     * @When /^I press "(?P<button_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     */
    public function press_button($button) {

        // Ensures the button is present.
        $buttonnode = $this->find_button($button);
        $buttonnode->press();
    }

    /**
     * Fills a moodle form with field/value data.
     *
     * @Given /^I fill the moodle form with:$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param TableNode $data
     */
    public function i_fill_the_moodle_form_with(TableNode $data) {

        $datahash = $data->getRowsHash();

        // The action depends on the field type.
        foreach ($datahash as $locator => $value) {

            unset($fieldnode);

            // Getting the NodeElement.
            $fieldnode = $this->find_field($locator);

            // Gets the field type from a parent node.
            $field = $this->get_field($fieldnode, $locator);

            // Delegates to the field class.
            $field->set_value($value);
        }
    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^I fill in "(?P<field_string>(?:[^"]|\\")*)" with "(?P<value_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     */
    public function fill_field($field, $value) {

        $fieldnode = $this->find_field($field);
        $fieldnode->setValue($value);
    }

    /**
     * Selects option in select field with specified id|name|label|value.
     *
     * @When /^I select "(?P<option_string>(?:[^"]|\\")*)" from "(?P<select_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     */
    public function select_option($option, $select) {

        $selectnode = $this->find_field($select);
        $selectnode->selectOption($option);

        // Adding a click as Selenium requires it to fire some JS events.
        $selectnode->click();
    }

    /**
     * Checks checkbox with specified id|name|label|value.
     *
     * @When /^I check "(?P<option_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     */
    public function check_option($option) {

        $checkboxnode = $this->find_field($option);
        $checkboxnode->check();
    }

    /**
     * Unchecks checkbox with specified id|name|label|value.
     *
     * @When /^I uncheck "(?P<option_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     */
    public function uncheck_option($option) {

        $checkboxnode = $this->find_field($option);
        $checkboxnode->uncheck();
    }

    /**
     * Checks that the form element field have the specified value.
     *
     * @Then /^the "(?P<field_string>(?:[^"]|\\")*)" field should match "(?P<value_string>(?:[^"]|\\")*)" value$/
     * @throws ExpectationException
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param mixed $locator
     * @param mixed $value
     */
    public function the_field_should_match_value($locator, $value) {

        $fieldnode = $this->find_field($locator);

        // Get the field.
        $field = $this->get_field($fieldnode, $locator);
        $fieldvalue = $field->get_value();

        // Checks if the provided value matches the current field value.
        if ($value != $fieldvalue) {
            throw new ExpectationException(
                'The \'' . $locator . '\' value is \'' . $fieldvalue . '\', \'' . $value . '\' expected' ,
                $this->getSession()
            );
        }
    }

    /**
     * Checks, that checkbox with specified in|name|label|value is checked.
     *
     * @Then /^the "(?P<checkbox_string>(?:[^"]|\\")*)" checkbox should be checked$/
     * @see Behat\MinkExtension\Context\MinkContext
     */
    public function assert_checkbox_checked($checkbox) {
        $this->assertSession()->checkboxChecked($checkbox);
    }

    /**
     * Checks, that checkbox with specified in|name|label|value is unchecked.
     *
     * @Then /^the "(?P<checkbox_string>(?:[^"]|\\")*)" checkbox should not be checked$/
     * @see Behat\MinkExtension\Context\MinkContext
     */
    public function assert_checkbox_not_checked($checkbox) {
        $this->assertSession()->checkboxNotChecked($checkbox);
    }

    /**
     * Checks, that given select box contains the specified option.
     *
     * @Then /^the "(?P<select_string>(?:[^"]|\\")*)" select box should contain "(?P<option_string>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $select The select element name
     * @param string $option The option text/value
     */
    public function the_select_box_should_contain($select, $option) {

        $selectnode = $this->find_field($select);

        $regex = '/' . preg_quote($option, '/') . '/ui';
        if (!preg_match($regex, $selectnode->getText())) {
            throw new ExpectationException(
                'The select box "' . $select . '" does not contains the option "' . $option . '"',
                $this->getSession()
            );
        }

    }

    /**
     * Checks, that given select box does not contain the specified option.
     *
     * @Then /^the "(?P<select_string>(?:[^"]|\\")*)" select box should not contain "(?P<option_string>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $select The select element name
     * @param string $option The option text/value
     */
    public function the_select_box_should_not_contain($select, $option) {

        $selectnode = $this->find_field($select);

        $regex = '/' . preg_quote($option, '/') . '/ui';
        if (preg_match($regex, $selectnode->getText())) {
            throw new ExpectationException(
                'The select box "' . $select . '" contains the option "' . $option . '"',
                $this->getSession()
            );
        }
    }

    /**
     * Gets an instance of the form field.
     *
     * Not all the fields are part of a moodle form, in this
     * cases it fallsback to the generic form field. Also note
     * that this generic field type is using a generic setValue()
     * method from the Behat API, which is not always good to set
     * the value of form elements.
     *
     * @param NodeElement $fieldnode The current node
     * @param string $locator Just to send an exception that makes sense for the user
     * @return behat_form_field
     */
    protected function get_field(NodeElement $fieldnode, $locator) {
        global $CFG;

        // Get the field type if is part of a moodleform.
        if ($this->is_moodleform_field($fieldnode)) {
            $type = $this->get_node_type($fieldnode, $locator);
        }

        // If is not a moodleforms field use the base field type.
        if (empty($type)) {
            $type = 'field';
        }

        $classname = 'behat_form_' . $type;

        // Fallsback on the default form field if nothing specific exists.
        $classpath = $CFG->libdir . '/behat/form_field/' . $classname . '.php';
        if (!file_exists($classpath)) {
            $classname = 'behat_form_field';
            $classpath = $CFG->libdir . '/behat/form_field/' . $classname . '.php';
        }

        // Returns the instance.
        require_once($classpath);
        return new $classname($this->getSession(), $fieldnode);
    }

    /**
     * Detects when the field is a moodleform field type.
     *
     * Note that there are fields inside moodleforms that are not
     * moodleform element; this method can not detect this, this will
     * be managed by get_node_type, after failing to find the form
     * element element type.
     *
     * @param NodeElement $fieldnode
     * @return bool
     */
    protected function is_moodleform_field(NodeElement $fieldnode) {

        // We already waited when getting the NodeElement and we don't want an exception if it's not part of a moodleform.
        $parentformfound = $fieldnode->find('xpath', "/ancestor::form[contains(concat(' ', normalize-space(@class), ' '), ' mform ')]/fieldset");

        return ($parentformfound != false);
    }

    /**
     * Recursive method to find the field type.
     *
     * Depending on the field the felement class node is a level or in another. We
     * look recursively for a parent node with a 'felement' class to find the field type.
     *
     * @param NodeElement $fieldnode The current node.
     * @param string $locator Just to send an exception that makes sense for the user.
     * @return mixed A NodeElement if we continue looking for the element type and String or false when we are done.
     */
    protected function get_node_type(NodeElement $fieldnode, $locator) {

        // We look for a parent node with 'felement' class.
        if ($class = $fieldnode->getParent()->getAttribute('class')) {

            if (strstr($class, 'felement') != false) {
                // Remove 'felement f' from class value.
                return substr($class, 10);
            }

            // Stop propagation through the DOM, if it does not have a felement is not part of a moodle form.
            if (strstr($class, 'fcontainer') != false) {
                return false;
            }
        }

        return $this->get_node_type($fieldnode->getParent(), $locator);
    }

}
