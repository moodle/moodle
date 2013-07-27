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
require_once(__DIR__ . '/../../../lib/behat/behat_field_manager.php');

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
     * @param string $button
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

        // Expand all fields in case we have.
        $this->expand_all_fields();

        $datahash = $data->getRowsHash();

        // The action depends on the field type.
        foreach ($datahash as $locator => $value) {

            // Getting the node element pointed by the label.
            $fieldnode = $this->find_field($locator);

            // Gets the field type from a parent node.
            $field = behat_field_manager::get_form_field($fieldnode, $this->getSession());

            // Delegates to the field class.
            $field->set_value($value);
        }
    }

    /**
     * Expands all moodleform's fields, including collapsed fieldsets and advanced fields if they are present.
     * @Given /^I expand all fieldsets$/
     */
    public function i_expand_all_fieldsets() {
        $this->expand_all_fields();
    }

    /**
     * Expands all moodle form fieldsets if they exists.
     *
     * Externalized from i_expand_all_fields to call it from
     * other form-related steps without having to use steps-group calls.
     *
     * @throws ElementNotFoundException Thrown by behat_base::find_all
     * @return void
     */
    protected function expand_all_fields() {

        // behat_base::find() throws an exception if there are no elements, we should not fail a test because of this.
        try {

            // Expand fieldsets link.
            $collapseexpandlink = $this->find('xpath', "//div[@class='collapsible-actions']" .
                "/descendant::a[contains(concat(' ', @class, ' '), ' collapseexpand ')]" .
                "[not(contains(concat(' ', @class, ' '), ' collapse-all '))]"
            );
            $collapseexpandlink->click();

        } catch (ElementNotFoundException $e) {
            // We continue if there are not expandable fields.
        }

        // Different try & catch as we can have expanded fieldsets with advanced fields on them.
        try {

            // Expand all fields xpath.
            $showmorexpath = "//a[normalize-space(.)='" . get_string('showmore', 'form') . "']" .
                "[contains(concat(' ', normalize-space(@class), ' '), ' moreless-toggler')]";

            // We don't wait here as we already waited when getting the expand fieldsets links.
            $showmores = $this->getSession()->getPage()->findAll('xpath', $showmorexpath);

            // Funny thing about this, with findAll() we specify a pattern and each element matching the pattern is added to the array
            // with of xpaths with a [0], [1]... sufix, but when we click on an element it does not matches the specified xpath
            // anymore (now is a "Show less..." link) so [1] becomes [0], that's why we always click on the first XPath match,
            // will be always the next one.
            $iterations = count($showmores);
            for ($i = 0; $i < $iterations; $i++) {
                $showmores[0]->click();
            }

        } catch (ElementNotFoundException $e) {
            // We continue with the test.
        }

    }

    /**
     * Fills in form field with specified id|name|label|value.
     *
     * @When /^I fill in "(?P<field_string>(?:[^"]|\\")*)" with "(?P<value_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $field
     * @param string $value
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
     * @param string $option
     * @param string $select
     */
    public function select_option($option, $select) {

        $selectnode = $this->find_field($select);
        $selectnode->selectOption($option);

        // Adding a click as Selenium requires it to fire some JS events.
        if ($this->running_javascript()) {

            // In some browsers the selectOption actions can perform a page reload
            // so we need to ensure the element is still available to continue interacting
            // with it. We don't wait here.
            if (!$this->getSession()->getDriver()->find($selectnode->getXpath())) {
                return;
            }

            // Single select needs an extra click in the option.
            if (!$selectnode->hasAttribute('multiple')) {

                // Avoid quotes problems.
                $option = $this->getSession()->getSelectorsHandler()->xpathLiteral($option);
                $xpath = "//option[(./@value=$option or normalize-space(.)=$option)]";
                $optionnode = $this->find('xpath', $xpath, false, $selectnode);
                $optionnode->click();
            } else {
                // Multiple ones needs the click in the select.
                $selectnode->click();
            }
        }
    }

    /**
     * Selects the specified id|name|label from the specified radio button.
     *
     * @When /^I select "(?P<radio_button_string>(?:[^"]|\\")*)" radio button$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $radio The radio button id, name or label value
     */
    public function select_radio($radio) {

        $radionode = $this->find_radio($radio);
        $radionode->check();

        // Adding a click as Selenium requires it to fire some JS events.
        if ($this->running_javascript()) {
            $radionode->click();
        }
    }

    /**
     * Checks checkbox with specified id|name|label|value.
     *
     * @When /^I check "(?P<option_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $option
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
     * @param string $option
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
     * @param string $locator
     * @param string $value
     */
    public function the_field_should_match_value($locator, $value) {

        $fieldnode = $this->find_field($locator);

        // Get the field.
        $field = behat_field_manager::get_form_field($fieldnode, $this->getSession());
        $fieldvalue = $field->get_value();

        // Checks if the provided value matches the current field value.
        if (trim($value) != trim($fieldvalue)) {
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
     * @param string $checkbox
     */
    public function assert_checkbox_checked($checkbox) {
        $this->assertSession()->checkboxChecked($checkbox);
    }

    /**
     * Checks, that checkbox with specified in|name|label|value is unchecked.
     *
     * @Then /^the "(?P<checkbox_string>(?:[^"]|\\")*)" checkbox should not be checked$/
     * @see Behat\MinkExtension\Context\MinkContext
     * @param string $checkbox
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

}
