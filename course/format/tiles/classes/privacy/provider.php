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
 * This class implements the Privacy API explained at https://docs.moodle.org/dev/Privacy_API
 * @package format_tiles
 * @copyright 2018 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * This plugin only stores a single setting as a user_preference as indicated below
 * so that is call that is covered
 *
 * Other data which are not covered by the privacy provider (because they do not need to be)
 * are stored in the user's browser if the user has javascript enabled.  These include data
 * such as the id of the last section the user visited, the contents of that section, which
 * filter button (if any) they had pressed in that course.  These data all remain in the browser and
 * are used to improve UX but are not sent elsewhere or used for tracking.  They are cleared when the
 * user clears their browser cache.
 *
 * The user is presented with a consent box on which explains the local strorage  on first use and can
 * change their preference later if they wish by clicking "Data preference" in the course menu
 *
 * This class requires PHP7, unlike the others in this plugin. Moodle 3.5 and higher do require this file and also require PHP7
 * However if this plugin is used with Moodle 3.3 (which allows PHP 5.6)
 * or Moodle 3.4 then this file serves no purpose and be deleted
 *
 */

/**
 * @package    format_tiles
 * @category   privacy
 * @copyright  2018 David Watson {@link http://evolutioncode.uk} based upon work done by Andrew Nicols <andrew@nicols.co.uk>.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
namespace format_tiles\privacy;

use core_privacy\local\metadata\collection;
use \core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem implementation for format_tiles
 *
 * @copyright  2018 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {
    /**
     * Indicate that this plugin only uses one user_preference to store personal data
     * and nothing else (e.g. no other database tables)
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection) : collection {
        $collection->add_user_preference('format_tiles_stopjsnav', 'privacy:metadata:preference:format_tiles_stopjsnav');
        return $collection;
    }

    /**
     * Export all user preferences for the plugin.
     * @param int $userid The userid of the user whose data is to be exported.
     * @throws \coding_exception
     */
    public static function export_user_preferences(int $userid) {
        $preference = get_user_preferences('format_tiles_stopjsnav', 0, $userid);
        if (isset($preference)) {
            $value = $preference ? get_string('yes') : get_string('no');
            \core_privacy\local\request\writer::export_user_preference('format_tiles', 'format_tiles_stopjsnav',
                $value, get_string('privacy:metadata:preference:format_tiles_stopjsnav', 'format_tiles'));
        }
    }
}
