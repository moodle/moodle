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

namespace aiprovider_gemini\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;

/**
 * Privacy provider implementation for Gemini provider
 *
 * @package    aiprovider_gemini
 * @copyright  2025 University of Ferrara, Italy
 * @author     Andrea Bertelli <andrea.bertelli@unife.it>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @codeCoverageIgnore
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {
    #[\Override]
    public static function get_metadata(collection $collection): collection {
        $collection->add_external_location_link('aiprovider_gemini', [
            'prompttext' => 'privacy:metadata:aiprovider_gemini:prompttext',
            'model' => 'privacy:metadata:aiprovider_gemini:model',
            'numberimages' => 'privacy:metadata:aiprovider_gemini:numberimages',
            'responseformat' => 'privacy:metadata:aiprovider_gemini:responseformat',
        ], 'privacy:metadata:aiprovider_gemini:externalpurpose');
        return $collection;
    }

    #[\Override]
    public static function get_contexts_for_userid(int $userid): contextlist {
        return new contextlist();
    }

    #[\Override]
    public static function get_users_in_context(userlist $userlist) {
    }

    #[\Override]
    public static function export_user_data(approved_contextlist $contextlist) {
    }

    #[\Override]
    public static function delete_data_for_all_users_in_context(\context $context) {
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
    }

    #[\Override]
    public static function delete_data_for_user(approved_contextlist $contextlist) {
    }
}
