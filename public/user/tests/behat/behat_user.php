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
        $this->execute("behat_forms::i_set_the_field_to", [
            "With selected users...",
            $this->escape($nodetext)
        ]);
    }

    /**
     * Deletes a user.
     *
     * @Given the user :identifier is deleted
     * @param string $identifier
     */
    #[\core\attribute\example('And the user student1 is deleted')]
    public function the_user_is_deleted($identifier) {
        global $DB;
        $userid = $this->get_user_id_by_identifier($identifier);
        if (!$userid) {
            throw new moodle_exception('The specified user with username or email "' . $identifier . '" does not exist');
        }
        delete_user($DB->get_record('user', ['id' => $userid]));
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

    /**
     * Convert page names to URLs for steps like 'When I am on the "[page name]" page'.
     *
     * Recognised page names are:
     * | Page name            | Description                                                 |
     * | Contact Site Support | The Contact Site Support page (user/contactsitesupport.php) |
     *
     * @param string $page name of the page, with the component name removed e.g. 'Admin notification'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_url(string $page): moodle_url {

        switch (strtolower($page)) {
            case 'contact site support':
                return new moodle_url('/user/contactsitesupport.php');

            default:
                throw new Exception("Unrecognised core_user page type '{$page}'.");
        }
    }

    /**
     * Convert page names to URLs for steps like 'When I am on the "[identifier]" "[page type]" page'.
     *
     * Recognised page names are:
     * | Page Type | Identifier meaning | Description                                |
     * | editing   | username or email  | User editing page (/user/editadvanced.php) |
     * | profile   | username or email  | User profile page (/user/profile.php) |
     *
     * @param string $type identifies which type of page this is, e.g. 'Editing'.
     * @param string $identifier identifies the user, e.g. 'student1'.
     * @return moodle_url the corresponding URL.
     * @throws Exception with a meaningful error message if the specified page cannot be found.
     */
    protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {

        switch (strtolower($type)) {
            case 'editing':
                $userid = $this->get_user_id_by_identifier($identifier);
                if (!$userid) {
                    throw new Exception('The specified user with username or email "' .
                        $identifier . '" does not exist');
                }
                return new moodle_url('/user/editadvanced.php', ['id' => $userid]);
            case 'profile':
                $userid = $this->get_user_id_by_identifier($identifier);
                if (!$userid) {
                    throw new Exception('The specified user with username or email "' . $identifier . '" does not exist');
                }
                return new moodle_url('/user/profile.php', ['id' => $userid]);
            default:
                throw new Exception("Unrecognised page type '{$type}'.");
        }
    }
}
