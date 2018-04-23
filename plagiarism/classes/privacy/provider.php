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
 * Privacy class for requesting user data.
 *
 * @package    core_plagiarism
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_plagiarism\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\metadata\collection;

/**
 * Provider for the plagiarism API.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // The Plagiarism subsystem does not store any data itself.
        // It has no database tables, and it purely acts as a conduit to the various plagiarism plugins.
        \core_privacy\local\metadata\provider,

        // The Plagiarism subsystem will be called by other components and will forward requests to each plagiarism plugin implementing its APIs.
        \core_privacy\local\request\subsystem\plugin_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection     $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        $collection->link_plugintype('plagiarism', 'privacy:metadata:plagiarism');

        return $collection;
    }

    /**
     * Export all plagiarism data from each plagiarism plugin for the specified userid and context.
     *
     * @param   int         $userid The user to export.
     * @param   \context    $context The context to export.
     * @param   array       $subcontext The subcontext within the context to export this information to.
     * @param   array       $linkarray The weird and wonderful link array used to display information for a specific item
     */
    public static function export_plagiarism_user_data($userid, \context $context, array $subcontext, array $linkarray) {
        static::call_plugin_method('export_plagiarism_user_data', [$userid, $context, $subcontext, $linkarray]);
    }

    /**
     * Deletes all user content for a context in all plagiarism plugins.
     *
     * @param  \context $context The context to delete user data for.
     */
    public static function delete_plagiarism_for_context(\context $context) {
        static::call_plugin_method('delete_plagiarism_for_context', [$context]);
    }

    /**
     * Deletes all user content for a user in a context in all plagiarism plugins.
     *
     * @param  int      $userid    The user to delete
     * @param  \context $context   The context to refine the deletion.
     */
    public static function delete_plagiarism_for_user($userid, \context $context) {
        static::call_plugin_method('delete_plagiarism_for_user', [$userid, $context]);
    }

    /**
     * Internal method for looping through all of the plagiarism plugins and calling a method.
     *
     * @param  string $methodname Name of the method to call on the plugins.
     * @param  array $params     The parameters that go with the method being called.
     */
    protected static function call_plugin_method($methodname, $params) {
        // Note: Even if plagiarism is _now_ disabled, there may be legacy data to export.
        \core_privacy\manager::plugintype_class_callback('plagiarism', plagiarism_provider::class, $methodname, $params);
    }
}
