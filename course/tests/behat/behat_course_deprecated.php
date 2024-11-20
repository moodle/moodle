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

require_once(__DIR__ . '/../../../lib/behat/behat_deprecated_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Steps definitions that are now deprecated and will be removed in the next releases.
 *
 * This file only contains the steps that previously were in the behat_*.php files in the SAME DIRECTORY.
 * When deprecating steps from other components or plugins, create a behat_COMPONENT_deprecated.php
 * file in the same directory where the steps were defined.
 *
 * @package    core_course
 * @category   test
 * @copyright  2024 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_course_deprecated extends behat_deprecated_base {
    /**
     * Opens the activity chooser and opens the activity/resource form page. Sections 0 and 1 are also allowed on frontpage.
     *
     * @Given /^I add a "(?P<activity_or_resource_name_string>(?:[^"]|\\")*)" to section "(?P<section_number>\d+)"$/
     * @param string $activity
     * @param int $section
     * @throws \Behat\Mink\Exception\ElementNotFoundException Thrown by behat_base::find
     * @deprecated Since Moodle 4.4
     */
    public function i_add_to_section($activity, $section) {
        $this->deprecated_message([
            'behat_course::i_add_to_course_section',
            'behat_course::i_add_to_section_using_the_activity_chooser',
        ]);

        $this->require_javascript('Please use the \'the following "activity" exists:\' data generator instead.');

        if ($this->getSession()->getPage()->find('css', 'body#page-site-index') && (int)$section <= 1) {
            // We are on the frontpage.
            if ($section) {
                // Section 1 represents the contents on the frontpage.
                $sectionxpath = "//body[@id='page-site-index']" .
                    "/descendant::div[contains(concat(' ',normalize-space(@class),' '),' sitetopic ')]";
            } else {
                // Section 0 represents "Site main menu" block.
                $sectionxpath = "//*[contains(concat(' ',normalize-space(@class),' '),' block_site_main_menu ')]";
            }
        } else {
            // We are inside the course.
            $sectionxpath = "//li[@id='section-" . $section . "']";
        }

        // Clicks add activity or resource section link.
        $sectionnode = $this->find('xpath', $sectionxpath);
        $this->execute('behat_general::i_click_on_in_the', [
            "//button[@data-action='open-chooser' and not(@data-beforemod)]",
            'xpath',
            $sectionnode,
            'NodeElement',
        ]);

        // Clicks the selected activity if it exists.
        $activityliteral = behat_context_helper::escape(ucfirst($activity));
        $activityxpath = "//div[contains(concat(' ', normalize-space(@class), ' '), ' modchooser ')]" .
            "/descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' optioninfo ')]" .
            "/descendant::div[contains(concat(' ', normalize-space(@class), ' '), ' optionname ')]" .
            "[normalize-space(.)=$activityliteral]" .
            "/parent::a";

        $this->execute('behat_general::i_click_on', [$activityxpath, 'xpath']);
    }

    /**
     * Adds the selected activity/resource filling the form data with the specified field/value pairs.
     *
     * Sections 0 and 1 are also allowed on frontpage.
     *
     * @When /^I add a "(?P<activity_or_resource_name_string>(?:[^"]|\\")*)" to section "(?P<section_number>\d+)" and I fill the form with:$/
     * @param string $activity The activity name
     * @param int $section The section number
     * @param TableNode $data The activity field/value data
     * @deprecated Since Moodle 4.4
     */
    public function i_add_to_section_and_i_fill_the_form_with($activity, $section, TableNode $data) {
        $this->deprecated_message(['behat_course::i_add_to_course_section_and_i_fill_the_form_with']);

        // Add activity to section.
        $this->execute(
            "behat_course::i_add_to_section",
            [$this->escape($activity), $this->escape($section)]
        );

        // Wait to be redirected.
        $this->execute('behat_general::wait_until_the_page_is_ready');

        // Set form fields.
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $data);

        // Save course settings.
        $this->execute("behat_forms::press_button", get_string('savechangesandreturntocourse'));
    }
}
