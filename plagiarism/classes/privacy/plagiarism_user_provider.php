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
 * Interface for deleting users related to a context.
 *
 * @package    core_plagiarism
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_plagiarism\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface for the plagiarism system.
 *
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface plagiarism_user_provider extends
        // The plagiarism_provider should be implemented by plugins which only provide information to a subsystem.
        \core_privacy\local\request\plugin\subsystem_provider,
        \core_privacy\local\request\shared_userlist_provider {

    /**
     * Delete all user information for the provided users and context.
     *
     * @param  array    $userids   The users to delete
     * @param  \context $context   The context to refine the deletion.
     */
    public static function delete_plagiarism_for_users(array $userids, \context $context);
}
