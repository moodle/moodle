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
 * Steps definitions related with administration.
 *
 * @package   core_admin
 * @category  test
 * @copyright 2013 David Monllaó
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../../lib/behat/behat_field_manager.php');

use Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

/**
 * Site administration level steps definitions.
 *
 * @package    core_admin
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_admin extends behat_base {

    /**
     * Sets the specified site settings. A table with | Setting label | value | is expected.
     *
     * @Given /^I set the following administration settings values:$/
     * @param TableNode $table
     */
    public function i_set_the_following_administration_settings_values(TableNode $table) {
        if (!$data = $table->getRowsHash()) {
            return;
        }

        foreach ($data as $label => $value) {
            // Navigate straight to the search results fo rthis label.
            $this->execute('behat_general::i_visit', ["/admin/search.php?query=" . urlencode($label)]);

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
                        "/descendant::*[self::input | self::textarea | self::select]" .
                        "[not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]";
            }

            $this->execute('behat_forms::i_set_the_field_with_xpath_to', [$fieldxpath, $value]);
            $this->execute("behat_general::i_click_on", [get_string('savechanges'), 'button']);
            // Wait for the page to be redirected.
            $this->execute("behat_general::i_wait_to_be_redirected");
        }
    }

    /**
     * Sets the specified site settings. A table with | config | value | (optional)plugin | (optional)encrypted | is expected.
     *
     * @Given /^the following config values are set as admin:$/
     * @param TableNode $table
     */
    #[\core\attribute\example('And the following config values are set as admin:
        | sendcoursewelcomemessage | 0 | enrol_manual |')]
    public function the_following_config_values_are_set_as_admin(TableNode $table) {

        if (!$data = $table->getRowsHash()) {
            return;
        }

        foreach ($data as $config => $value) {
            // Default plugin value is null.
            $plugin = null;
            $encrypted = false;

            if (is_array($value)) {
                $plugin = $value[1];
                if (array_key_exists(2, $value)) {
                    $encrypted = $value[2] === 'encrypted';
                }
                $value = $value[0];
            }

            if ($encrypted) {
                $value = \core\encryption::encrypt($value);
            }

            set_config($config, $value, $plugin);
        }
    }

    /**
     * Waits with the provided params if we are running a JS session.
     *
     * @param int $timeout
     * @param string $javascript
     * @return void
     */
    protected function wait($timeout, $javascript = false) {
        if ($this->running_javascript()) {
            $this->getSession()->wait($timeout, $javascript);
        }
    }
}
