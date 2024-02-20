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

use Behat\Gherkin\Node\TableNode as TableNode;

require_once(__DIR__ . '/../../../../lib/behat/behat_deprecated_base.php');


/**
 * Steps definitions that are now deprecated and will be removed in the next releases.
 *
 * This file only contains the steps that previously were in the behat_*.php files in the SAME DIRECTORY.
 * When deprecating steps from other components or plugins, create a behat_COMPONENT_deprecated.php
 * file in the same directory where the steps were defined.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2024 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_data_deprecated extends behat_deprecated_base
{

    /**
     * Adds a new field to a database
     *
     * @Given /^I add a "(?P<fieldtype_string>(?:[^"]|\\")*)" field to "(?P<activityname_string>(?:[^"]|\\")*)" database and I fill the form with:$/
     *
     * @param string $fieldtype
     * @param string $activityname
     * @param TableNode $fielddata
     * @todo MDL-79721 This will be deleted in Moodle 4.8.
     *
     * @deprecated since 4.4
     */
    public function i_add_a_field_to_database_and_i_fill_the_form_with($fieldtype, $activityname, TableNode $fielddata)
    {
        $this->deprecated_message([
            'behat_mod_data::i_add_a_field_to_database_and_i_fill_the_form_with is deprecated',
            'mod_data > fields generator',
        ]);

        $this->execute('behat_navigation::i_am_on_page_instance', [$this->escape($activityname), 'data activity']);

        $fieldsstr = get_string('fields', 'mod_data');

        $this->execute("behat_navigation::i_navigate_to_in_current_page_administration", $fieldsstr);
        $this->execute('behat_general::i_click_on', [get_string('newfield', 'mod_data'), "button"]);
        $this->execute('behat_general::i_click_on_in_the',
            [$this->escape($fieldtype), "link", "#action_bar", "css_element"]
        );

        if (!$this->running_javascript()) {
            $this->execute('behat_general::i_click_on_in_the',
                array(get_string('go'), "button", ".fieldadd", "css_element")
            );
        }

        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $fielddata);
        $this->execute('behat_forms::press_button', get_string('save'));
    }

    /**
     * Adds an entry to a database.
     *
     * @Given /^I add an entry to "(?P<activityname_string>(?:[^"]|\\")*)" database with:$/
     *
     * @param string $activityname
     * @param TableNode $entrydata
     * @deprecated since 4.4
     * @todo MDL-79721 This will be deleted in Moodle 4.8.
     *
     */
    public function i_add_an_entry_to_database_with($activityname, TableNode $entrydata)
    {
        $this->deprecated_message([
            'behat_mod_data::i_add_an_entry_to_database_with is deprecated',
            'mod_data > entries generator',
        ]);

        $this->execute('behat_navigation::i_am_on_page_instance', [$this->escape($activityname), 'mod_data > add entry']);
        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $entrydata);
    }
}
