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
 * Behat message-related steps definitions.
 *
 * @package    core_message
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

/**
 * Messaging system steps definitions.
 *
 * @package    core_message
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_message extends behat_base {

    /**
     * Open the messaging UI.
     *
     * @Given /^I open messaging$/
     */
    public function i_open_messaging() {
        // Visit home page and follow messages.
        $this->execute("behat_general::i_am_on_homepage");
        $this->execute("behat_general::i_click_on", [get_string('togglemessagemenu', 'core_message'), 'link']);
    }

    /**
     * Open the messaging UI.
     *
     * @Given /^I open messaging information$/
     */
    public function i_open_messaging_information() {
        $this->execute('behat_general::i_click_on', ["[data-action='view-group-info']", 'css_element']);
    }

    /**
     * View the contact information of a user in the messages ui.
     *
     * @Given /^I view the "(?P<user_full_name_string>(?:[^"]|\\")*)" contact in the message area$/
     * @param string $userfullname
     */
    public function i_view_contact_in_messages($userfullname) {
        // Visit home page and follow messages.
        $this->i_select_user_in_messaging($userfullname);

        $this->execute('behat_general::i_click_on_in_the',
            array(
                "//button[@data-action='view-contact-profile']
                [contains(normalize-space(.), '" . $this->escape($userfullname) . "')]",
                'xpath_element',
                ".messages-header",
                "css_element",
            )
        );

        $this->execute('behat_general::wait_until_the_page_is_ready');
    }

    /**
     * Select a user in the messaging UI.
     *
     * @Given /^I select "(?P<user_full_name_string>(?:[^"]|\\")*)" user in messaging$/
     * @param string $userfullname
     */
    public function i_select_user_in_messaging($userfullname) {
        $this->i_open_messaging();

        $this->execute('behat_general::i_click_on', [get_string('search', 'core'), 'field']);

        $this->execute('behat_forms::i_set_the_field_with_xpath_to',
            [
                "//*[@data-region='message-drawer']//input[@data-region='search-input']",
                $this->escape($userfullname)
            ]
        );

        $this->execute('behat_general::i_click_on', ['[data-action="search"]', 'css_element']);

        $this->execute('behat_general::wait_until_the_page_is_ready');

        // Need to limit the click to the search results because the 'view-contact-profile' elements
        // can occur in two separate divs on the page.
        $this->execute('behat_general::i_click_on_in_the',
            [
                $this->escape($userfullname),
                'link',
                "[data-region='message-drawer'] [data-region='search-results-container']",
                "css_element",
            ]
        );

        $this->execute('behat_general::wait_until_the_page_is_ready');
    }

    /**
     * Sends a message to the specified user from the logged user. The user full name should contain the first and last names.
     *
     * @Given /^I send "(?P<message_contents_string>(?:[^"]|\\")*)" message to "(?P<user_full_name_string>(?:[^"]|\\")*)" user$/
     * @param string $messagecontent
     * @param string $userfullname
     */
    public function i_send_message_to_user($messagecontent, $userfullname) {
        $this->i_select_user_in_messaging($userfullname);

        $this->execute('behat_forms::i_set_the_field_with_xpath_to',
            array("//textarea[@data-region='send-message-txt']", $this->escape($messagecontent))
        );

        $this->execute('behat_general::i_click_on_in_the',
            [
                '[data-action="send-message"]',
                'css_element',
                "[data-region='message-drawer'] [data-region='footer-container'] [data-region='view-conversation']",
                "css_element",
            ]
        );
    }

    /**
     * Select messages from a user in the messaging ui.
     *
     * @Given /^I send "(?P<message_contents_string>(?:[^"]|\\")*)" message in the message area$/
     * @param string $messagecontent
     */
    public function i_send_message_in_the_message_area($messagecontent) {
        $this->execute('behat_general::wait_until_the_page_is_ready');

        $this->execute('behat_forms::i_set_the_field_with_xpath_to',
            array("//textarea[@data-region='send-message-txt']", $this->escape($messagecontent))
        );

        $this->execute("behat_forms::press_button", get_string('send', 'message'));
    }

    /**
     * Navigate back in the messages ui drawer.
     *
     * @Given /^I go back in "(?P<parent_element_string>(?:[^"]|\\")*)" message drawer$/
     * @param string $parentelement
     */
    public function i_go_back_in_message_drawer($parentelement) {
        $this->execute('behat_general::i_click_on_in_the',
            array(
                'a[data-route-back]',
                'css_element',
                '[data-region="'.$this->escape($parentelement).'"]',
                'css_element',
            )
        );
    }

    /**
     * Select a user in the messaging UI.
     *
     * @Given /^I select "(?P<conversation_name_string>(?:[^"]|\\")*)" conversation in messaging$/
     * @param string $conversationname
     */
    public function i_select_conversation_in_messaging($conversationname) {
        $this->execute('behat_general::i_click_on',
            array(
                $this->escape($conversationname),
                'group_message',
            )
        );
    }

    /**
     * Open the contact menu.
     *
     * @Given /^I open contact menu$/
     */
    public function i_open_contact_menu() {
        $this->execute('behat_general::wait_until_the_page_is_ready');
        $this->execute('behat_general::i_click_on_in_the',
            array(
                'button',
                'css_element',
                '[data-region="message-drawer"] [data-region="header-container"]',
                'css_element',
            )
        );
    }
}
