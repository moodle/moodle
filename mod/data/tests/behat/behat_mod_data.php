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
 * Steps definitions related with the database activity.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2014 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given,
    Behat\Behat\Context\Step\When as When,
    Behat\Gherkin\Node\TableNode as TableNode;
/**
 * Database-related steps definitions.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2014 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_data extends behat_base {

    /**
     * Adds a new field to a database
     *
     * @Given /^I add a "(?P<fieldtype_string>(?:[^"]|\\")*)" field to "(?P<activityname_string>(?:[^"]|\\")*)" database and I fill the form with:$/
     *
     * @param string $fieldtype
     * @param string $activityname
     * @param TableNode $fielddata
     * @return Given[]
     */
    public function i_add_a_field_to_database_and_i_fill_the_form_with($fieldtype, $activityname, TableNode $fielddata) {

        $steps = array(
            new Given('I follow "' . $this->escape($activityname) . '"'),
            new Given('I follow "' . get_string('fields', 'mod_data') . '"'),
            new Given('I set the field "newtype" to "' . $this->escape($fieldtype) . '"')
        );

        if (!$this->running_javascript()) {
            $steps[] = new Given('I click on "' . get_string('go') . '" "button" in the ".fieldadd" "css_element"');
        }

        array_push(
            $steps,
            new Given('I set the following fields to these values:', $fielddata),
            new Given('I press "' . get_string('add') . '"')
        );

        return $steps;
    }

    /**
     * Adds an entry to a database.
     *
     * @Given /^I add an entry to "(?P<activityname_string>(?:[^"]|\\")*)" database with:$/
     *
     * @param string $activityname
     * @param TableNode $entrydata
     * @return When[]
     */
    public function i_add_an_entry_to_database_with($activityname, TableNode $entrydata) {

        return array(
            new When('I follow "' . $this->escape($activityname) . '"'),
            new When('I follow "' . get_string('add', 'mod_data') . '"'),
            new When('I set the following fields to these values:', $entrydata),
        );
    }
}
