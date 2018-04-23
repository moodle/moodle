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
 * @package    core_portfolio
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_portfolio\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\context;

/**
 * Provider for the portfolio API.
 *
 * @copyright  2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // The Portfolio subsystem does not store any data itself.
        // It has no database tables, and it purely acts as a conduit to the various portfolio plugins.
        \core_privacy\local\metadata\provider,

        // The portfolio subsystem will be called by other components.
        \core_privacy\local\request\subsystem\plugin_provider {

    /**
     * Returns meta data about this system.
     *
     * @param   collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection) {
        return $collection->add_plugintype_link('portfolio', [], 'privacy:metadata');
    }

    /**
     * Export all portfolio data from each portfolio plugin for the specified userid and context.
     *
     * @param   int         $userid The user to export.
     * @param   \context    $context The context to export.
     * @param   array       $subcontext The subcontext within the context to export this information to.
     * @param   array       $linkarray The weird and wonderful link array used to display information for a specific item
     */
    public static function export_portfolio_user_data($userid, \context $context, array $subcontext, array $linkarray) {
        static::call_plugin_method('export_portfolio_user_data', [$userid, $context, $subcontext, $linkarray]);
    }

    /**
     * Deletes all user content for a context in all portfolio plugins.
     *
     * @param  \context $context The context to delete user data for.
     */
    public static function delete_portfolio_for_context(\context $context) {
        static::call_plugin_method('delete_portfolio_for_context', [$context]);
    }

    /**
     * Deletes all user content for a user in a context in all portfolio plugins.
     *
     * @param  int      $userid    The user to delete
     * @param  \context $context   The context to refine the deletion.
     */
    public static function delete_portfolio_for_user($userid, \context $context) {
        static::call_plugin_method('delete_portfolio_for_user', [$userid, $context]);
    }

    /**
     * Internal method for looping through all of the portfolio plugins and calling a method.
     *
     * @param  string $methodname Name of the method to call on the plugins.
     * @param  array $params     The parameters that go with the method being called.
     */
    protected static function call_plugin_method($methodname, $params) {
        // Note: Even if portfolio is _now_ disabled, there may be legacy data to export.
        \core_privacy\manager::plugintype_class_callback('portfolio', portfolio_provider::class, $methodname, $params);
    }
}
