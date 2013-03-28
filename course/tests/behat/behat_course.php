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
    Behat\Gherkin\Node\TableNode as TableNode;

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

}
