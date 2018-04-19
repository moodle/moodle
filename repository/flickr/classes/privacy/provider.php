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
 * Privacy Subsystem implementation for repository_flickr.
 *
 * @package    repository_flickr
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_flickr\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\context;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for repository_flickr implementing metadata, plugin, and user_preference providers.
 *
 * @copyright  2018 Zig Tan <zig@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\user_preference_provider
{

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        $collection->add_external_location_link(
            'flickr.com',
            [
                'text' => 'privacy:metadata:repository_flickr:text'
            ],
            'privacy:metadata:repository_flickr'
        );

        // Flickr preferences.
        $collection->add_user_preference(
            'repository_flickr_access_token',
            'privacy:metadata:repository_flickr:preference:repository_flickr_access_token'
        );
        $collection->add_user_preference(
            'repository_flickr_access_token_secret',
            'privacy:metadata:repository_flickr:preference:repository_flickr_access_token_secret'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param   int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid($userid) {
        return new contextlist();
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param   context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param   approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
    }

    /**
     * Export all user preferences for the plugin.
     *
     * @param   int $userid The userid of the user whose data is to be exported.
     */
    public static function export_user_preferences($userid) {
        $accesstoken = get_user_preferences('repository_flickr_access_token', null, $userid);
        if ($accesstoken !== null) {
            writer::export_user_preference(
                'repository_flickr',
                'repository_flickr_access_token',
                $accesstoken,
                get_string('privacy:metadata:repository_flickr:preference:repository_flickr_access_token', 'repository_flickr')
            );
        }
        $accesstokensecret = get_user_preferences('repository_flickr_access_token_secret', null, $userid);
        if ($accesstokensecret !== null) {
            writer::export_user_preference(
                'repository_flickr',
                'repository_flickr_access_token_secret',
                '',
                get_string('privacy:metadata:repository_flickr:preference:repository_flickr_access_token_secret', 'repository_flickr')
            );
        }
    }
}
