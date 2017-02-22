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
 * Behat message popup related steps definitions.
 *
 * @package    message_popup
 * @category   test
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Message popup steps definitions.
 *
 * @package    message_popup
 * @category   test
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_message_popup extends behat_base {

    /**
     * Open the notification popover in the nav bar.
     *
     * @Given /^I open the notification popover$/
     */
    public function i_open_the_notification_popover() {
        $this->execute('behat_general::i_click_on',
            array("#nav-notification-popover-container [data-region='popover-region-toggle']", 'css_element'));

        $node = $this->get_selected_node('css_element',
            '#nav-notification-popover-container [data-region="popover-region-content"]');
        $this->ensure_node_is_visible($node);
    }

    /**
     * Open the message popover in the nav bar.
     *
     * @Given /^I open the message popover$/
     */
    public function i_open_the_message_popover() {
        $this->execute('behat_general::i_click_on',
            array("#nav-message-popover-container [data-region='popover-region-toggle']", 'css_element'));

        $node = $this->get_selected_node('css_element', '#nav-message-popover-container [data-region="popover-region-content"]');
        $this->ensure_node_is_visible($node);
    }
}
