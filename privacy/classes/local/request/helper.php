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
 * This file contains the core_privacy\local\request helper.
 *
 * @package core_privacy
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_privacy\local\request;

use \core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/modinfolib.php');
require_once($CFG->dirroot . '/course/modlib.php');

/**
 * The core_privacy\local\request\helper class with useful shared functionality.
 *
 * @package core_privacy
 * @copyright 2018 Andrew Nicols <andrew@nicols.co.uk>
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Add core-controlled contexts which are related to a component but that component may know about.
     *
     * For example, most activities are not aware of activity completion, but the course implements it for them.
     * These should be included.
     *
     * @param   int             $userid The user being added for.
     * @param   contextlist     $contextlist The contextlist being appended to.
     * @return  contextlist     The final contextlist
     */
    public static function add_shared_contexts_to_contextlist_for($userid, contextlist $contextlist) {
        if (strpos($contextlist->get_component(), 'mod_') === 0) {
            // Activity modules support data stored by core about them - for example, activity completion.
            $contextlist = static::add_shared_contexts_to_contextlist_for_course_module($userid, $contextlist);
        }

        return $contextlist;
    }

    /**
     * Handle export of standard data for a plugin which implements the null provider and does not normally store data
     * of its own.
     *
     * This is used in cases such as activities like mod_resource, which do not store their own data, but may still have
     * data on them (like Activity Completion).
     *
     * Any context provided in a contextlist should have base data exported as a minimum.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts to export information for.
     */
    public static function export_data_for_null_provider(approved_contextlist $contextlist) {
        $user = $contextlist->get_user();
        foreach ($contextlist as $context) {
            $data = static::get_context_data($context, $user);
            static::export_context_files($context, $user);

            writer::with_context($context)->export_data([], $data);
        }
    }

    /**
     * Handle removal of 'standard' data for any plugin.
     *
     * This will handle deletion for things such as activity completion.
     *
     * @param   string          $component The component being deleted for.
     * @param   context         $context   The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context($component, \context $context) {
        // Activity modules support data stored by core about them - for example, activity completion.
        static::delete_data_for_all_users_in_context_course_module($component, $context);
    }

    /**
     * Delete all 'standard' user data for the specified user, in the specified contexts.
     *
     * This will handle deletion for things such as activity completion.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        $component = $contextlist->get_component();

        // Activity modules support data stored by core about them - for example, activity completion.
        static::delete_data_for_user_in_course_module($contextlist);
    }

    /**
     * Get all general data for this context.
     *
     * @param   \context        $context The context to retrieve data for.
     * @param   \stdClass       $user The user being written.
     * @return  \stdClass
     */
    public static function get_context_data(\context $context, \stdClass $user) {
        global $DB;

        $basedata = (object) [];
        if ($context instanceof \context_module) {
            return static::get_context_module_data($context, $user);
        }
        if ($context instanceof \context_block) {
            return static::get_context_block_data($context, $user);
        }

        return $basedata;
    }

    /**
     * Export all files for this context.
     *
     * @param   \context        $context The context to export files for.
     * @param   \stdClass       $user The user being written.
     * @return  \stdClass
     */
    public static function export_context_files(\context $context, \stdClass $user) {
        if ($context instanceof \context_module) {
            return static::export_context_module_files($context, $user);
        }
    }

    /**
     * Add core-controlled contexts which are related to a component but that component may know about.
     *
     * For example, most activities are not aware of activity completion, but the course implements it for them.
     * These should be included.
     *
     * @param   int             $userid The user being added for.
     * @param   contextlist     $contextlist The contextlist being appended to.
     * @return  contextlist     The final contextlist
     */
    protected static function add_shared_contexts_to_contextlist_for_course_module($userid, contextlist $contextlist) {
        // Fetch all contexts where the user has activity completion enabled.
        $sql = "SELECT
                c.id
                  FROM {course_modules_completion} cmp
            INNER JOIN {course_modules} cm ON cm.id = cmp.coursemoduleid
            INNER JOIN {modules} m ON m.id = cm.module
            INNER JOIN {context} c ON c.instanceid = cm.id AND c.contextlevel = :contextlevel
                 WHERE cmp.userid = :userid
                   AND m.name = :modname";
        $params = [
            'userid' => $userid,
            // Strip the mod_ from the name.
            'modname' => substr($contextlist->get_component(), 4),
            'contextlevel' => CONTEXT_MODULE,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get all general data for the activity module at this context.
     *
     * @param   \context_module $context The context to retrieve data for.
     * @param   \stdClass       $user The user being written.
     * @return  \stdClass
     */
    protected static function get_context_module_data(\context_module $context, \stdClass $user) {
        global $DB;

        $coursecontext = $context->get_course_context();
        $modinfo = get_fast_modinfo($coursecontext->instanceid);
        $cm = $modinfo->cms[$context->instanceid];
        $component = "mod_{$cm->modname}";
        $course = $cm->get_course();
        $moduledata = $DB->get_record($cm->modname, ['id' => $cm->instance]);

        $basedata = (object) [
            'name' => $cm->get_formatted_name(),
        ];

        if (plugin_supports('mod', $cm->modname, FEATURE_MOD_INTRO, true)) {
            $intro = $moduledata->intro;

            $intro = writer::with_context($context)
                ->rewrite_pluginfile_urls([], $component, 'intro', 0, $intro);

            $options = [
                'noclean' => true,
                'para' => false,
                'context' => $context,
                'overflowdiv' => true,
            ];
            $basedata->intro = format_text($intro, $moduledata->introformat, $options);
        }

        // Completion tracking.
        $completiondata = \core_completion\privacy\provider::get_activity_completion_info($user, $course, $cm);
        if (isset($completiondata->completionstate)) {
            $basedata->completion = (object) [
                'state' => $completiondata->completionstate,
            ];
        }

        return $basedata;
    }

    /**
     * Get all general data for the block at this context.
     *
     * @param   \context_block $context The context to retrieve data for.
     * @param   \stdClass $user The user being written.
     * @return  \stdClass General data about this block instance.
     */
    protected static function get_context_block_data(\context_block $context, \stdClass $user) {
        global $DB;

        $block = $DB->get_record('block_instances', ['id' => $context->instanceid]);

        $basedata = (object) [
            'blocktype' => get_string('pluginname', 'block_' . $block->blockname)
        ];

        return $basedata;
    }

    /**
     * Get all general data for the activity module at this context.
     *
     * @param   \context_module $context The context to retrieve data for.
     * @param   \stdClass       $user The user being written.
     * @return  \stdClass
     */
    protected static function export_context_module_files(\context_module $context, \stdClass $user) {
        $coursecontext = $context->get_course_context();
        $modinfo = get_fast_modinfo($coursecontext->instanceid);
        $cm = $modinfo->cms[$context->instanceid];
        $component = "mod_{$cm->modname}";

        writer::with_context($context)
            // Export the files for the intro.
            ->export_area_files([], $component, 'intro', 0);
    }

    /**
     * Handle removal of 'standard' data for course modules.
     *
     * This will handle deletion for things such as activity completion.
     *
     * @param   string              $component The component being deleted for.
     * @param   \context            $context The context to delete all data for.
     */
    public static function delete_data_for_all_users_in_context_course_module($component, \context $context) {
        global $DB;

        if ($context instanceof \context_module) {
            // Delete course completion data for this context.
            \core_completion\privacy\provider::delete_completion(null, null, $context->instanceid);
        }
    }

    /**
     * Delete all 'standard' user data for the specified user in course modules.
     *
     * This will handle deletion for things such as activity completion.
     *
     * @param   approved_contextlist    $contextlist    The approved contexts and user information to delete information for.
     */
    protected static function delete_data_for_user_in_course_module(approved_contextlist $contextlist) {
        global $DB;

        foreach ($contextlist as $context) {
            if ($context instanceof \context_module) {
                // Delete course completion data for this context.
                \core_completion\privacy\provider::delete_completion($contextlist->get_user(), null, $context->instanceid);
            }
        }

    }
}
