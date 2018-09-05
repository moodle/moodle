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
 * Testcase for providers implementing parts of the core_privacy subsystem.
 *
 * @package    core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_privacy\tests;

defined('MOODLE_INTERNAL') || die();

global $CFG;

/**
 * Testcase for providers implementing parts of the core_privacy subsystem.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class provider_testcase extends \advanced_testcase {

    /**
     * Test tearDown.
     */
    public function tearDown() {
        \core_privacy\local\request\writer::reset();
    }

    /**
     * Export all data for a component for the specified user.
     *
     * @param   int         $userid     The userid of the user to fetch.
     * @param   string      $component  The component to get context data for.
     * @return  \core_privacy\local\request\contextlist
     */
    public function get_contexts_for_userid(int $userid, string $component) {
        $classname = $this->get_provider_classname($component);

        return $classname::get_contexts_for_userid($userid);
    }

    /**
     * Export all data for a component for the specified user.
     *
     * @param   int         $userid     The userid of the user to fetch.
     * @param   string      $component  The component to get export data for.
     */
    public function export_all_data_for_user(int $userid, string $component) {
        $contextlist = $this->get_contexts_for_userid($userid, $component);

        $approvedcontextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($userid),
            $component,
            $contextlist->get_contextids()
        );

        $classname = $this->get_provider_classname($component);
        $classname::export_user_data($approvedcontextlist);
    }

    /**
     * Export all daa within a context for a component for the specified user.
     *
     * @param   int         $userid     The userid of the user to fetch.
     * @param   \context    $context    The context to export data for.
     * @param   string      $component  The component to get export data for.
     */
    public function export_context_data_for_user(int $userid, \context $context, string $component) {
        $contextlist = new \core_privacy\tests\request\approved_contextlist(
            \core_user::get_user($userid),
            $component,
            [$context->id]
        );

        $classname = $this->get_provider_classname($component);
        $classname::export_user_data($contextlist);
    }

    /**
     * Determine the classname and ensure that it is a provider.
     *
     * @param   string      $component      The classname.
     * @return  string
     */
    protected function get_provider_classname($component) {
        $classname = "\\${component}\\privacy\\provider";

        if (!class_exists($classname)) {
            throw new \coding_exception("{$component} does not implement any provider");
        }

        $rc = new \ReflectionClass($classname);
        if (!$rc->implementsInterface(\core_privacy\local\metadata\provider::class)) {
            throw new \coding_exception("{$component} does not implement metadata provider");
        }

        if (!$rc->implementsInterface(\core_privacy\local\request\core_user_data_provider::class)) {
            throw new \coding_exception("{$component} does not declare that it provides any user data");
        }

        return $classname;
    }
}
