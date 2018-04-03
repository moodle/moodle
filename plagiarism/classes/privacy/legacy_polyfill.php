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
 * This file contains the polyfill to allow a plugin to operate with Moodle 3.3 up.
 *
 * @package core_plagiarism
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_plagiarism\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;

/**
 * The trait used to provide a backwards compatibility for third-party plugins.
 *
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait legacy_polyfill {

    /**
     * Export all plagiarism data from each plagiarism plugin for the specified userid and context.
     *
     * @param   int         $userid The user to export.
     * @param   \context    $context The context to export.
     * @param   array       $subcontext The subcontext within the context to export this information to.
     * @param   array       $linkarray The weird and wonderful link array used to display information for a specific item
     */
    public static function export_plagiarism_user_data($userid, \context $context, array $subcontext, array $linkarray) {
        static::_export_plagiarism_user_data($userid, $context, $subcontext, $linkarray);
    }

    /**
     * Delete all user information for the provided context.
     *
     * @param  \context $context The context to delete user data for.
     */
    public static function delete_plagiarism_for_context(\context $context) {
        static::_delete_plagiarism_for_context($context);
    }

    /**
     * Delete all user information for the provided user and context.
     *
     * @param  int      $userid    The user to delete
     * @param  \context $context   The context to refine the deletion.
     */
    public static function delete_plagiarism_for_user($userid, \context $context) {
        static::_delete_plagiarism_for_user($userid, $context);
    }
}
