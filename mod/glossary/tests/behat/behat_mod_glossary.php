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
 * Steps definitions related with the glossary activity.
 *
 * @package    mod_glossary
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Glossary-related steps definitions.
 *
 * @package    mod_glossary
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_glossary extends behat_base {

    /**
     * Adds an entry to the current glossary with the provided data. You should be in the glossary page.
     *
     * @Given /^I add a glossary entry with the following data:$/
     * @param TableNode $data
     */
    public function i_add_a_glossary_entry_with_the_following_data(TableNode $data) {
        $this->execute("behat_forms::press_button", get_string('addsingleentry', 'mod_glossary'));

        $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $data);

        $this->execute("behat_forms::press_button", get_string('savechanges'));
    }

    /**
     * Adds a category with the specified name to the current glossary. You need to be in the glossary page.
     *
     * @Given /^I add a glossary entries category named "(?P<category_name_string>(?:[^"]|\\")*)"$/
     * @param string $categoryname Category name
     */
    public function i_add_a_glossary_entries_category_named($categoryname) {
        $params = [
            get_string('categoryview', 'mod_glossary'),
            get_string('explainalphabet', 'glossary')
        ];
        $this->execute("behat_forms::i_select_from_the_singleselect", $params);
        $this->execute("behat_forms::press_button", get_string('editcategories', 'mod_glossary'));

        $this->execute("behat_forms::press_button", get_string('addcategory', 'glossary'));

        $this->execute('behat_forms::i_set_the_field_to', array('name', $this->escape($categoryname)));

        $this->execute("behat_forms::press_button", get_string('savechanges'));
        $this->execute("behat_forms::press_button", get_string('back', 'mod_glossary'));
    }

    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "mod_glossary > [page type]" page'.
     *
     * Recognised page names are:
     * | pagetype | name meaning                        | description                                    |
     * | Entry    | Glossary name > Entry concept       | The glossary view page showing a single entry  |
     *
     * @param string $type identifies which type of page this is, e.g. 'Entry'.
     * @param string $identifier identifies the particular page, e.g. 'Test glossary > Test concept'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
        global $DB;

        switch (strtolower($type)) {
            case 'entry':
                [$glossaryname, $concept] = explode(' > ', $identifier, 2);
                return new moodle_url('/mod/glossary/view.php', [
                    'id' => $this->get_cm_by_activity_name('glossary', $glossaryname)->id,
                    'mode' => 'entry',
                    'hook' => $DB->get_field('glossary_entries', 'id', ['concept' => $concept], MUST_EXIST),
                ]);

            default:
                throw new Exception("Unrecognised page type '{$type}'");
        }
    }
}
