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

require_once(__DIR__ . '/../../../lib/behat/behat_deprecated_base.php');

/**
 * Steps definitions that are now deprecated and will be removed in the next releases.
 *
 * This file only contains the steps that previously were in the behat_*.php files in the SAME DIRECTORY.
 * When deprecating steps from other components or plugins, create a behat_COMPONENT_deprecated.php
 * file in the same directory where the steps were defined.
 *
 * @package    core_enrol
 * @category   test
 * @copyright  2022 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_enrol_deprecated extends behat_deprecated_base {

    /**
     * Adds the specified enrolment method to the current course filling the form with the provided data.
     *
     * @Given /^I add "(?P<enrolment_method_name_string>(?:[^"]|\\")*)" enrolment method with:$/
     * @param string $enrolmethod
     * @param TableNode $table
     *
     * @deprecated since 4.0 MDL-72090. We now need the course to enrol in. Please use i_add_enrolment_method_for_with()
     * @todo MDL-71733 This will be deleted in Moodle 4.4.
     */
    public function i_add_enrolment_method_with($enrolmethod, TableNode $table) {
        $this->deprecated_message(['behat_enrol::i_add_enrolment_method_for_with']);

        // Navigate to enrolment method page.
        $parentnodes = get_string('users', 'admin');
        $this->execute("behat_navigation::i_navigate_to_in_current_page_administration",
            array($parentnodes .' > '. get_string('type_enrol_plural', 'plugin'))
        );

        // Select enrolment method.
        $this->execute('behat_forms::i_select_from_the_singleselect',
            array($this->escape($enrolmethod), get_string('addinstance', 'enrol'))
        );

        // Wait again, for page to reloaded.
        $this->execute('behat_general::i_wait_to_be_redirected');

        // Set form fields.
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $table);

        // Ensure we get button in focus, before pressing button.
        if ($this->running_javascript()) {
            $this->execute('behat_general::i_press_named_key', ['', 'tab']);
        }

        // Save changes.
        $this->execute("behat_forms::press_button", get_string('addinstance', 'enrol'));
    }
}
