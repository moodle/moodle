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
 * Step definitions related to administration overrides for the Adaptable theme.
 *
 * @package    theme_adaptable
 * @category   test
 * @copyright  2019 Michael Hawkins (copied from theme_classic)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
// For that reason, we can't even rely on $CFG->admin being available here.

require_once(__DIR__ . '/../../../../admin/tests/behat/behat_admin.php');

use Behat\Gherkin\Node\TableNode,
    Behat\Mink\Exception\ElementNotFoundException;

/**
 * Site administration level steps definitions overrides for the Adaptable theme.
 *
 * @package    theme_adaptable
 * @category   test
 * @copyright  2019 Michael Hawkins (copied from theme classic)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class behat_theme_adaptable_behat_admin extends behat_admin {
    /**
     * Sets the specified site settings. A table with | Setting label | value | is expected.
     *
     * @param TableNode $table
     */
    public function i_set_the_following_administration_settings_values(TableNode $table) {

        if (!$data = $table->getRowsHash()) {
            return;
        }

        foreach ($data as $label => $value) {
            // We expect admin block to be visible, otherwise go to homepage.
            if (!$this->getSession()->getPage()->find('css', '.block_settings')) {
                $this->execute('behat_forms::i_am_on_homepage');
            }

            // Search by label.
            $this->execute('behat_forms::i_set_the_field_to', [get_string('searchinsettings', 'admin'), $label]);
            $this->execute("behat_general::i_click_on_in_the", [get_string('search', 'admin'), 'button',
                '.block_settings', 'css_element']);

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
