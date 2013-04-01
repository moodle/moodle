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
 * Behat course-related steps definitions.
 *
 * @package    core_course
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Course-related steps definitions.
 *
 * @package    core_course
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_course extends behat_base {

    /**
     * Turns editing mode on.
     * @Given /^I turn editing mode on$/
     */
    public function i_turn_editing_mode_on() {
        return new Given('I press "Turn editing on"');
    }

    /**
     * Turns editing mode off.
     * @Given /^I turn editing mode off$/
     */
    public function i_turn_editing_mode_off() {
        return new Given('I press "Turn editing off"');
    }

    /**
     * Adds the selected activity/resource filling the form data with the specified field/value pairs.
     *
     * @When /^I add a "(?P<activity_or_resource_string>(?:[^"]|\\")*)" to section "(?P<section_number>\d+)" and I fill the form with:$/
     * @param string $activity The activity name
     * @param string $section The section number
     * @param TableNode $data The activity field/value data
     */
    public function i_add_to_section_and_i_fill_the_form_with($activity, $section, TableNode $data) {

        return array(
            new Given('I add a "'.$activity.'" to section "'.$section.'"'),
            new Given('I fill the moodle form with:', $data),
            new Given('I press "Save and return to course"')
        );
    }

    /**
     * Opens the activity chooser and opens the activity/resource form page.
     *
     * @Given /^I add a "(?P<activity_or_resource_string>(?:[^"]|\\")*)" to section "(?P<section_number>\d+)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $activity
     * @param string $section
     */
    public function i_add_to_section($activity, $section) {

        $sectionxpath = "//*[@id='section-" . $section . "']";

        if ($this->running_javascript()) {

            // Clicks add activity or resource section link.
            $sectionxpath = $sectionxpath . "/descendant::div[@class='section-modchooser']/span/a";
            $sectionnode = $this->find('xpath', $sectionxpath);
            $sectionnode->click();

            // Clicks the selected activity if it exists.
            $activityxpath = ".//label[contains(.,'" . $activity . "')]/input";
            $activitynode = $this->find('xpath', $activityxpath);
            $activitynode->doubleClick();

        } else {
            // Without Javascript.

            // Selecting the option from the select box which contains the option.
            $selectxpath = $sectionxpath . "/descendant::div[contains(concat(' ', @class, ' '), ' section_add_menus ')]
/descendant::select[contains(., '" . $activity . "')]";
            $selectnode = $this->find('xpath', $selectxpath);
            $selectnode->selectOption($activity);

            // Go button.
            $gobuttonxpath = $selectxpath . "/ancestor::form/descendant::input[@type='submit']";
            $gobutton = $this->find('xpath', $gobuttonxpath);
            $gobutton->click();
        }

    }

    /**
     * Turns course section highlighting on.
     *
     * @Given /^I turn section "(?P<section_number>\d+)" highlighting on$/
     * @param int $sectionnumber The section number
     */
    public function i_turn_section_highlighting_on($sectionnumber) {

        // Ensures the section exists.
        $xpath = $this->section_exists($sectionnumber);

        return array(
            new Given('I click on "' . get_string('markthistopic') . '" "link" in the "' . $xpath . '" "xpath_element"'),
            new Given('I wait "2" seconds')
        );
    }

    /**
     * Turns course section highlighting off.
     *
     * @Given /^I turn section "(?P<section_number>\d+)" highlighting off$/
     * @param int $sectionnumber The section number
     */
    public function i_turn_section_highlighting_off($sectionnumber) {

        // Ensures the section exists.
        $xpath = $this->section_exists($sectionnumber);

        return array(
            new Given('I click on "' . get_string('markedthistopic') . '" "link" in the "' . $xpath . '" "xpath_element"'),
            new Given('I wait "2" seconds')
        );
    }

    /**
     * Checks if the specified course section hightlighting is turned on.
     *
     * @throws ElementNotFoundException
     * @throws ExpectationException
     * @Then /^section "(?P<section_number>\d+)" should be highlighted$/
     * @param int $sectionnumber The section number
     */
    public function section_should_be_highlighted($sectionnumber) {

        // Ensures the section exists.
        $xpath = $this->section_exists($sectionnumber);

        // The important checking, we can not check the img.
        $xpath = $xpath . "/descendant::img[@alt='" . get_string('markedthistopic') . "'][contains(@src, 'marked')]";
        $exception = new ExpectationException('The "' . $sectionnumber . '" section is not highlighted', $this->getSession());
        $this->find('xpath', $xpath, $exception);
    }

    /**
     * Checks if the specified course section highlighting is turned off.
     *
     * @Then /^section "(?P<section_number>\d+)" should not be highlighted$/
     * @param int $sectionnumber The section number
     */
    public function section_should_not_be_highlighted($sectionnumber) {

        // We only catch ExpectationException, ElementNotFoundException should be thrown if the specified section does not exist.
        try {
            $this->section_should_be_highlighted($sectionnumber);
        } catch (ExpectationException $e) {
            // ExpectedException means that it is not highlighted.
            return;
        }

        throw new ExpectationException('The "' . $sectionnumber . '" section is highlighted', $this->getSession());
    }

    /**
     * Checks if the course section exists.
     *
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param int $sectionnumber
     * @return string The xpath of the existing section.
     */
    protected function section_exists($sectionnumber) {

        // Just to give more info in case it does not exist.
        $xpath = "//li[@id='section-" . $sectionnumber . "']";
        $exception = new ElementNotFoundException($this->getSession(), "Section $sectionnumber ");
        $this->find('xpath', $xpath, $exception);

        return $xpath;
    }
}
