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
     * Return the list of partial named selectors.
     *
     * @return array
     */
    public static function get_partial_named_selectors(): array {
        return [
            new behat_component_named_selector('Message', [".//*[@data-conversation-id]//img[%altMatch%]/.."]),
            new behat_component_named_selector('Message conversation', [
                <<<XPATH
    .//*[@data-region='message-drawer' and contains(., %locator%)]//div[@data-region='content-message-container']
XPATH
            ], false),
            new behat_component_named_selector('Message header', [
                <<<XPATH
    .//*[@data-region='message-drawer']//div[@data-region='header-content' and contains(., %locator%)]
XPATH
            ]),
            new behat_component_named_selector('Message member', [
                <<<XPATH
    .//*[@data-region='message-drawer']//div[@data-region='group-info-content-container']
    //div[@class='list-group' and not(contains(@class, 'hidden'))]//*[%core_message/textMatch%]
XPATH
                , <<<XPATH
    .//*[@data-region='message-drawer']//div[@data-region='group-info-content-container']
    //div[@data-region='empty-message-container' and not(contains(@class, 'hidden')) and contains(., %locator%)]
XPATH
            ], false),
            new behat_component_named_selector('Message tab', [
                <<<XPATH
    .//*[@data-region='message-drawer']//button[@data-toggle='collapse' and contains(string(), %locator%)]
XPATH
            ], false),
            new behat_component_named_selector('Message list area', [
                <<<XPATH
    .//*[@data-region='message-drawer']//*[contains(@data-region, concat('view-overview-', %locator%))]
XPATH
            ], false),
            new behat_component_named_selector('Message content', [
                <<<XPATH
    .//*[@data-region='message-drawer']//*[@data-region='message' and @data-message-id and contains(., %locator%)]
XPATH
            ], false),
        ];
    }

    /**
     * Return a list of the Mink named replacements for the component.
     *
     * Named replacements allow you to define parts of an xpath that can be reused multiple times, or in multiple
     * xpaths.
     *
     * This method should return a list of {@link behat_component_named_replacement} and the docs on that class explain
     * how it works.
     *
     * @return behat_component_named_replacement[]
     */
    public static function get_named_replacements(): array {
        return [
            new behat_component_named_replacement('textMatch', 'text()[contains(., %locator%)]'),
        ];
    }

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
     * Open the messaging conversation list.
     *
     * @Given /^I open the "(?P<tab_string>(?:[^"]|\\")*)" conversations list/
     * @param string $tab
     */
    public function i_open_the_conversations_list(string $tab) {
        $this->execute('behat_general::i_click_on', [
            $this->escape($tab),
            'core_message > Message tab'
        ]);
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
        $this->execute('behat_message::i_select_user_in_messaging', [$userfullname]);

        $this->execute('behat_general::i_click_on_in_the',
            array(
                "//a[@data-action='view-contact']",
                "xpath_element",
                "//*[@data-region='message-drawer']//div[@data-region='header-container']",
                "xpath_element",
            )
        );
        $this->execute('behat_general::i_click_on_in_the',
            array(
                "//img[@title='Picture of ". $this->escape($userfullname) . "']",
                "xpath_element",
                "//*[@data-region='message-drawer']//*[@data-region='view-contact']",
                "xpath_element",
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
        $this->execute('behat_message::i_open_messaging', []);

        $this->execute('behat_message::i_search_for_string_in_messaging', [$userfullname]);

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
     * Search for a string using the messaging search.
     *
     * @Given /^I search for "(?P<string>(?:[^"]|\\")*)" in messaging$/
     * @param string $string the search string.
     */
    public function i_search_for_string_in_messaging($string) {
        $messagedrawer = $this->find('css', '[data-region="message-drawer"]');
        $this->execute('behat_general::i_click_on_in_the', [
            get_string('search', 'core'), 'field',
            $messagedrawer, 'NodeElement'
        ]);

        $this->execute('behat_forms::i_set_the_field_with_xpath_to', [
            "//*[@data-region='message-drawer']//input[@data-region='search-input']",
            $this->escape($string)
        ]);

        $this->execute('behat_general::i_click_on_in_the', [
            '[data-action="search"]', 'css_element',
            $messagedrawer, 'NodeElement'
        ]);

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
        $this->execute('behat_message::i_select_user_in_messaging', [$userfullname]);

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

        $this->execute("behat_forms::press_button", get_string('sendmessage', 'message'));
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
                'core_message > Message',
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

    /**
     * Select a user in a specific messaging UI conversations list.
     *
     * @Given /^I select "(?P<conv_name_string>(?:[^"]|\\")*)" conversation in the "(?P<list_name_string>(?:[^"]|\\")*)" conversations list$/
     * @param string $convname
     * @param string $listname
     */
    public function i_select_conversation_in_the_conversations_list(string $convname, string $listname) {
        $xpath = '//*[@data-region="message-drawer"]//div[@data-region="view-overview-'.
            $this->escape($listname).
            '"]//*[@data-conversation-id]//img[contains(@alt,"'.
            $this->escape($convname).'")]';
        $this->execute('behat_general::i_click_on', array($xpath, 'xpath_element'));
    }

    /**
     * Open the settings preferences.
     *
     * @Given /^I open messaging settings preferences$/
     */
    public function i_open_messaging_settings_preferences() {
        $this->execute('behat_general::wait_until_the_page_is_ready');
        $this->execute('behat_general::i_click_on',
            array(
                '//*[@data-region="message-drawer"]//a[@data-route="view-settings"]',
                'xpath_element',
                '',
                '',
            )
        );
    }
}
