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
 * Utility class for browsing of content bank files in the system context.
 *
 * @package    repository_contentbank
 * @copyright  2020 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_contentbank\browser;

/**
 * Represents the content bank browser in the system context.
 *
 * @package    repository_contentbank
 * @copyright  2020 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contentbank_browser_context_system extends contentbank_browser {

    /**
     * Constructor.
     *
     * @param \context_system $context The current context
     */
    public function __construct(\context_system $context) {
        $this->context = $context;
    }

    /**
     * Define the allowed child context levels.
     *
     * @return int[] The array containing the relevant child context levels
     */
    protected function allowed_child_context_levels(): array {
        // The expected child context in the system context level is the course category context.
        return [\CONTEXT_COURSECAT];
    }

    /**
     * The required condition to enable the user to view/access the content bank content in this context.
     *
     * @return bool Whether the user can view/access the content bank content in the context
     */
    public function can_access_content(): bool {
        // When the following conditions are met, the user would be able to share the content created in the system
        // context level all over the site.
        // The content from the system context level should be available to:
        // * Every user that has a capability to access the 'general' content.

        return has_capability('repository/contentbank:accessgeneralcontent', $this->context);
    }
}
