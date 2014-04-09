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
 * Grading methods steps definitions.
 *
 * @package   core_grading
 * @category  test
 * @copyright 2013 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Behat\Context\Step\Given as Given,
    Behat\Behat\Context\Step\When as When;

/**
 * Generic grading methods step definitions.
 *
 * @package   core_grading
 * @category  test
 * @copyright 2013 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_grading extends behat_base {

    /**
     * Goes to the selected advanced grading page. You should be in the course page when this step begins.
     *
     * @Given /^I go to "(?P<activity_name_string>(?:[^"]|\\")*)" advanced grading page$/
     * @param string $activityname
     * @return Given[]
     */
    public function i_go_to_advanced_grading_page($activityname) {
        return array(
            new Given('I follow "' . $this->escape($activityname) . '"'),
            new Given('I follow "' . get_string('gradingmanagement', 'grading') . '"'),
        );
    }

    /**
     * Goes to the selected advanced grading definition page. You should be in the course page when this step begins.
     *
     * @Given /^I go to "(?P<activity_name_string>(?:[^"]|\\")*)" advanced grading definition page$/
     * @param string $activityname
     * @return Given[]
     */
    public function i_go_to_advanced_grading_definition_page($activityname) {

        // Transforming to literals, probably not necessary, just in case.
        $newactionliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string("manageactionnew", "grading"));
        $editactionliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string("manageactionedit", "grading"));

        // Working both when adding and editing.
        $definitionxpath = "//a[@class='action']" .
            "[./descendant::*[contains(., $newactionliteral) or contains(., $editactionliteral)]]";

        return array(
            new Given('I go to "' . $this->escape($activityname) . '" advanced grading page'),
            new Given('I click on "' . $this->escape($definitionxpath) . '" "xpath_element"'),
        );
    }
    /**
     * Goes to the student's advanced grading page.
     *
     * @Given /^I go to "(?P<user_fullname_string>(?:[^"]|\\")*)" "(?P<activity_name_string>(?:[^"]|\\")*)" activity advanced grading page$/
     * @param string $userfullname The user full name including firstname and lastname.
     * @param string $activityname The activity name
     * @return Given[]
     */
    public function i_go_to_activity_advanced_grading_page($userfullname, $activityname) {

        // Step to access the user grade page from the grading page.
        $usergradetext = get_string('gradeuser', 'assign', $userfullname);
        $gradeuserstep = new Given('I follow "' . $this->escape($usergradetext) . '"');

        // Shortcut in case we already are in the grading page.
        $usergradetextliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($usergradetext);
        if ($this->getSession()->getPage()->find('named', array('link', $usergradetextliteral))) {
            return $gradeuserstep;
        }

        return array(
            new Given('I follow "' . $this->escape($activityname) . '"'),
            new Given('I follow "' . $this->escape(get_string('viewgrading', 'assign')) . '"'),
            $gradeuserstep
        );
    }

    /**
     * Publishes current activity grading defined form as a public template.
     *
     * @Given /^I publish "(?P<activity_name_string>(?:[^"]|\\")*)" grading form definition as a public template$/
     * @param string $activityname
     * @return Given[]
     */
    public function i_publish_grading_form_definition_as_a_public_template($activityname) {

        return array(
            new Given('I go to "' . $this->escape($activityname) . '" advanced grading page'),
            new Given('I click on "' . $this->escape(get_string("manageactionshare", "grading")) . '" "link"'),
            new Given('I press "' . get_string('continue') . '"')
        );
    }

    /**
     * Sets a previously created grading form as the activity grading form.
     *
     * @Given /^I set "(?P<activity_name_string>(?:[^"]|\\")*)" activity to use "(?P<grading_form_template_string>(?:[^"]|\\")*)" grading form$/
     * @param string $activityname
     * @param string $templatename
     * @return Given[]
     */
    public function i_set_activity_to_use_grading_form($activityname, $templatename) {

        $templateliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($templatename);

        $templatexpath = "//h2[@class='template-name'][contains(., $templateliteral)]/" .
            "following-sibling::div[contains(concat(' ', normalize-space(@class), ' '), ' template-actions ')]";

        // Should work with both templates and own forms.
        $literaltemplate = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string('templatepick', 'grading'));
        $literalownform = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string('templatepickownform', 'grading'));
        $usetemplatexpath = "//a[./descendant::div[text()=$literaltemplate]]|" .
            "//a[./descendant::div[text()=$literalownform]]";

        return array(
            new Given('I go to "' . $this->escape($activityname) . '" advanced grading page'),
            new Given('I follow "' . $this->escape(get_string('manageactionclone', 'grading')) . '"'),
            new Given('I set the field "' . get_string('searchownforms', 'grading') . '" to "1"'),
            new Given('I click on "' . get_string('search') . '" "button" in the "region-main" "region"'),
            new Given('I click on "' . $this->escape($usetemplatexpath) . '" "xpath_element" ' .
                'in the "' . $this->escape($templatexpath) . '" "xpath_element"'),
            new Given('I press "' . get_string('continue') . '"')
        );
    }

    /**
     * Saves the current page advanced grading form.
     *
     * @When /^I save the advanced grading form$/
     * @return When[]
     */
    public function i_save_the_advanced_grading_form() {
        return array(
            new When('I press "' . get_string('savechanges') . '"'),
            new When('I press "' . get_string('continue') . '"')
        );
    }

    /**
     * Grades an activity using advanced grading. Note the grade is set by other steps, depending on the grading method.
     *
     * @Given /^I complete the advanced grading form with these values:$/
     * @param TableNode $data
     * @return Given[]
     */
    public function i_complete_the_advanced_grading_form_with_these_values(TableNode $data) {
        return array(
            new Given('I set the following fields to these values:', $data),
            new Given('I save the advanced grading form')
        );
    }
}
