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
 * Steps definitions related with administration overrides.
 *
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
// For that reason, we can't even rely on $CFG->admin being available here.

require_once(__DIR__ . '/../../../../admin/tests/behat/behat_admin.php');

use Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Site administration level steps definitions overrides.
 *
 * @copyright 2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_boost_behat_admin extends behat_admin {

    public function i_set_the_following_administration_settings_values(TableNode $table) {

        if (!$data = $table->getRowsHash()) {
            return;
        }

        foreach ($data as $label => $value) {

            $this->execute('behat_navigation::i_select_from_flat_navigation_drawer', [get_string('administrationsite')]);

            // Search by label.
            $this->execute('behat_forms::i_set_the_field_to', [get_string('query', 'admin'), $label]);
            $this->execute("behat_general::i_click_on", [get_string('search', 'admin'), 'button']);

            // Admin settings does not use the same DOM structure than other moodle forms
            // but we also need to use lib/behat/form_field/* to deal with the different moodle form elements.
            $exception = new ElementNotFoundException($this->getSession(), '"' . $label . '" administration setting ');

            // The argument should be converted to an xpath literal.
            $label = behat_context_helper::escape($label);

            // Single element settings.
            try {
                $fieldxpath = "//*[self::input | self::textarea | self::select]" .
                    "[not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]" .
                    "[@id=//label[contains(normalize-space(.), $label)]/@for or " .
                    "@id=//span[contains(normalize-space(.), $label)]/preceding-sibling::label[1]/@for]";
                $fieldnode = $this->find('xpath', $fieldxpath, $exception);
            } catch (ElementNotFoundException $e) {

                // Multi element settings, interacting only the first one.
                $fieldxpath = "//*[label[contains(., $label)]|span[contains(., $label)]]" .
                    "/ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' form-item ')]" .
                    "/descendant::div[contains(concat(' ', @class, ' '), ' form-group ')]" .
                    "/descendant::*[self::input | self::textarea | self::select]" .
                    "[not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]";
                $fieldnode = $this->find('xpath', $fieldxpath);
            }

            $this->execute('behat_forms::i_set_the_field_with_xpath_to', [$fieldxpath, $value]);

            $this->execute("behat_general::i_click_on", [get_string('savechanges'), 'button']);
        }
    }

}
