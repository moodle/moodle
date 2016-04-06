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

use Behat\Gherkin\Node\TableNode as TableNode;

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
     */
    public function i_go_to_advanced_grading_page($activityname) {

        $this->execute('behat_general::click_link', $this->escape($activityname));

        $this->execute('behat_general::click_link', get_string('gradingmanagement', 'grading'));
    }

    /**
     * Goes to the selected advanced grading definition page. You should be in the course page when this step begins.
     *
     * @Given /^I go to "(?P<activity_name_string>(?:[^"]|\\")*)" advanced grading definition page$/
     * @param string $activityname
     */
    public function i_go_to_advanced_grading_definition_page($activityname) {

        // Transforming to literals, probably not necessary, just in case.
        $newactionliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string("manageactionnew", "grading"));
        $editactionliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string("manageactionedit", "grading"));

        // Working both when adding and editing.
        $definitionxpath = "//a[@class='action']" .
            "[./descendant::*[contains(., $newactionliteral) or contains(., $editactionliteral)]]";

        $this->execute('behat_grading::i_go_to_advanced_grading_page', $this->escape($activityname));

        $this->execute("behat_general::i_click_on", array($this->escape($definitionxpath), "xpath_element"));
    }
    /**
     * Goes to the student's advanced grading page.
     *
     * @Given /^I go to "(?P<user_fullname_string>(?:[^"]|\\")*)" "(?P<activity_name_string>(?:[^"]|\\")*)" activity advanced grading page$/
     * @param string $userfullname The user full name including firstname and lastname.
     * @param string $activityname The activity name
     */
    public function i_go_to_activity_advanced_grading_page($userfullname, $activityname) {

        // Step to access the user grade page from the grading page.
        $usergradetext = get_string('gradeuser', 'assign', $userfullname);

        // Shortcut in case we already are in the grading page.
        $usergradetextliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($usergradetext);
        if ($this->getSession()->getPage()->find('named_partial', array('link', $usergradetextliteral))) {
            $this->execute('behat_general::click_link', $this->escape($usergradetext));

            return true;
        }

        $this->execute('behat_general::click_link', $this->escape($activityname));

        $this->execute('behat_general::click_link', $this->escape(get_string('viewgrading', 'assign')));

        $this->execute('behat_general::click_link', $this->escape($usergradetext));
    }

    /**
     * Publishes current activity grading defined form as a public template.
     *
     * @Given /^I publish "(?P<activity_name_string>(?:[^"]|\\")*)" grading form definition as a public template$/
     * @param string $activityname
     */
    public function i_publish_grading_form_definition_as_a_public_template($activityname) {

        $this->execute('behat_grading::i_go_to_advanced_grading_page', $this->escape($activityname));

        $this->execute("behat_general::i_click_on", array($this->escape(get_string("manageactionshare", "grading")), "link"));

        $this->execute('behat_forms::press_button', get_string('continue'));
    }

    /**
     * Sets a previously created grading form as the activity grading form.
     *
     * @Given /^I set "(?P<activity_name_string>(?:[^"]|\\")*)" activity to use "(?P<grading_form_template_string>(?:[^"]|\\")*)" grading form$/
     * @param string $activityname
     * @param string $templatename
     */
    public function i_set_activity_to_use_grading_form($activityname, $templatename) {

        $templateliteral = $this->getSession()->getSelectorsHandler()->xpathLiteral($templatename);

        $templatexpath = "//h2[@class='template-name'][contains(., $templateliteral)]/" .
            "following-sibling::div[contains(concat(' ', normalize-space(@class), ' '), ' template-actions ')]";

        // Should work with both templates and own forms.
        $literaltemplate = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string('templatepick', 'grading'));
        $literalownform = $this->getSession()->getSelectorsHandler()->xpathLiteral(get_string('templatepickownform', 'grading'));
        $usetemplatexpath = "/a[./descendant::div[text()=$literaltemplate]]|" .
            "/a[./descendant::div[text()=$literalownform]]";

        $this->execute('behat_grading::i_go_to_advanced_grading_page', $this->escape($activityname));

        $this->execute('behat_general::click_link', $this->escape(get_string('manageactionclone', 'grading')));
        $this->execute('behat_forms::i_set_the_field_to', array(get_string('searchownforms', 'grading'), 1));
        $this->execute('behat_general::i_click_on_in_the',
            array(get_string('search'), "button", "region-main", "region")
        );
        $this->execute('behat_general::i_click_on_in_the',
            array($this->escape($usetemplatexpath), "xpath_element", $this->escape($templatexpath), "xpath_element")
        );
        $this->execute('behat_forms::press_button', get_string('continue'));

    }

    /**
     * Saves the current page advanced grading form.
     *
     * @When /^I save the advanced grading form$/
     */
    public function i_save_the_advanced_grading_form() {

        $this->execute('behat_forms::press_button', get_string('savechanges'));
        $this->execute('behat_forms::press_button', get_string('continue'));
    }

    /**
     * Grades an activity using advanced grading. Note the grade is set by other steps, depending on the grading method.
     *
     * @Given /^I complete the advanced grading form with these values:$/
     * @param TableNode $data
     */
    public function i_complete_the_advanced_grading_form_with_these_values(TableNode $data) {
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $data);
        $this->execute('behat_grading::i_save_the_advanced_grading_form');
    }
}
