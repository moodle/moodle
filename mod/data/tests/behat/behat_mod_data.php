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

use Behat\Gherkin\Node\TableNode as TableNode;
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
     */
    public function i_add_a_field_to_database_and_i_fill_the_form_with($fieldtype, $activityname, TableNode $fielddata) {

        $this->execute("behat_general::click_link", $this->escape($activityname));

        // Open "Fields" tab if it is not already open.
        $fieldsstr = get_string('fields', 'mod_data');
        $xpath = '//ul[contains(@class,\'nav-tabs\')]//*[contains(@class,\'active\') and contains(normalize-space(.), \'' .
            $fieldsstr . '\')]';
        if (!$this->getSession()->getPage()->findAll('xpath', $xpath)) {
            $this->execute("behat_general::i_click_on_in_the", array($fieldsstr, 'link', '.nav-tabs', 'css_element'));
        }

        $this->execute('behat_forms::i_set_the_field_to', array('newtype', $this->escape($fieldtype)));

        if (!$this->running_javascript()) {
            $this->execute('behat_general::i_click_on_in_the',
                array(get_string('go'), "button", ".fieldadd", "css_element")
            );
        }

        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $fielddata);
        $this->execute('behat_forms::press_button', get_string('add'));
    }

    /**
     * Adds an entry to a database.
     *
     * @Given /^I add an entry to "(?P<activityname_string>(?:[^"]|\\")*)" database with:$/
     *
     * @param string $activityname
     * @param TableNode $entrydata
     */
    public function i_add_an_entry_to_database_with($activityname, TableNode $entrydata) {

        $this->execute("behat_general::click_link", $this->escape($activityname));
        $this->execute("behat_navigation::i_navigate_to_in_current_page_administration",
                get_string('add', 'mod_data'));

        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $entrydata);
    }
}
