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
 * Privacy Subsystem implementation for tool_crawler.
 *
 * @package    tool_crawler
 * @copyright  2018 Olivier SECRET <olivier.secret@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_crawler\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\legacy_polyfill;

/**
 * Privacy provider for the authentication manual.
 *
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\null_provider {

    use legacy_polyfill;

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * This function is compatible with old php version. (Diff is the underscore '_' in the beginning)
     * But the get_reason is still available because of the trait legacy_polyfill.
     *
     * @return  string
     */
    public static function _get_reason() {
        return 'privacy:no_data_reason';
    }

}
