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
 * Mustache helper that will convert a timestamp to a date string.
 *
 * @package    core
 * @category   output
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

defined('MOODLE_INTERNAL') || die();

use Mustache_LambdaHelper;

/**
 * Mustache helper that will convert a timestamp to a date string.
 *
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mustache_user_date_helper {

    /**
     * Read a timestamp and format from the string.
     *
     * {{#userdate}}1487655635, %Y %m %d{{/userdate}}
     *
     * There is a list of formats in lang/en/langconfig.php that can be used as the date format.
     *
     * Both args are required. The timestamp must come first.
     *
     * @param string $args The text to parse for arguments.
     * @param Mustache_LambdaHelper $helper Used to render nested mustache variables.
     * @return string
     */
    public function transform($args, Mustache_LambdaHelper $helper) {
        // Split the text into an array of variables.
        list($timestamp, $format) = explode(',', $args, 2);
        $timestamp = trim($timestamp);
        $format = trim($format);

        $timestamp = $helper->render($timestamp);
        $format = $helper->render($format);

        return userdate($timestamp, $format);
    }
}
