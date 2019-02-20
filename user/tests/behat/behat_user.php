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
 * User steps definition.
 *
 * @package    core_user
 * @category   test
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException;

/**
 * Steps definitions for users.
 *
 * @package    core_user
 * @category   test
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_user extends behat_base {

    /**
     * Choose from the bulk action menu.
     *
     * @Given /^I choose "(?P<nodetext_string>(?:[^"]|\\")*)" from the participants page bulk action menu$/
     * @param string $nodetext The menu item to select.
     */
    public function i_choose_from_the_participants_page_bulk_action_menu($nodetext) {
        $nodetext = behat_context_helper::escape($nodetext);

        // Open the select.
        $this->execute("behat_general::i_click_on", array("//select[@id='formactionid']", "xpath_element"));

        // Click on the option.
        $this->execute("behat_general::i_click_on", array("//select[@id='formactionid']" .
                                                          "/option[contains(., " . $nodetext . ")]", "xpath_element"));
    }

    /**
     * The input field should have autocomplete set to this value.
     *
     * @Then /^the field "(?P<field_string>(?:[^"]|\\")*)" should have purpose "(?P<purpose_string>(?:[^"]|\\")*)"$/
     * @param string $field The field to select.
     * @param string $purpose The expected purpose.
     */
    public function the_field_should_have_purpose($field, $purpose) {
        $fld = behat_field_manager::get_form_field_from_label($field, $this);

        $value = $fld->get_attribute('autocomplete');
        if ($value != $purpose) {
            $reason = 'The "' . $field . '" field does not have purpose "' . $purpose . '"';
            throw new ExpectationException($reason, $this->getSession());
        }
    }

    /**
     * The input field should not have autocomplete set to this value.
     *
     * @Then /^the field "(?P<field_string>(?:[^"]|\\")*)" should not have purpose "(?P<purpose_string>(?:[^"]|\\")*)"$/
     * @param string $field The field to select.
     * @param string $purpose The expected purpose we do not want.
     */
    public function the_field_should_not_have_purpose($field, $purpose) {
        $fld = behat_field_manager::get_form_field_from_label($field, $this);

        $value = $fld->get_attribute('autocomplete');
        if ($value == $purpose) {
            throw new ExpectationException('The "' . $field . '" field does have purpose "' . $purpose . '"', $this->getSession());
        }
    }
}
