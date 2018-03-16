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
 * Provides {@link mock_incomplete_sitepolicy_handler} class.
 *
 * @package     core_privacy
 * @category    test
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Incomplete mock handler for site policies not implementing all required methods.
 *
 * Particularly, get_embed_url() is missing.
 *
 * @package     core_privacy
 * @copyright   2018 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mock_incomplete_sitepolicy_handler extends \core_privacy\local\sitepolicy\handler {

    /**
     * Returns URL to redirect user to when user needs to agree to site policy
     *
     * @param bool $forguests
     * @return moodle_url|string|null
     */
    public static function get_redirect_url($forguests = false) {
        return 'http://example.com/policy.php';
    }
}
