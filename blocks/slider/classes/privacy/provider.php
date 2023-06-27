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
 * Privacy Subsystem implementation for block_slider.
 *
 * @package   block_slider
 * @copyright 2015-2020 Kamil Łuczak    www.limsko.pl     kamil@limsko.pl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_slider\privacy;

defined('MOODLE_INTERNAL') || die();

use \core_privacy\local\request\userlist;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\approved_userlist;
use \core_privacy\local\request\writer;
use \core_privacy\local\request\helper;
use \core_privacy\local\request\deletion_criteria;
use \core_privacy\local\metadata\collection;

/**
 * Privacy Subsystem implementation for block_slider.
 *
 * @copyright 2015-2020 Kamil Łuczak    www.limsko.pl     kamil@limsko.pl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
        // The block_slider block stores user provided data.
        \core_privacy\local\metadata\provider,

        // This plugin is capable of determining which users have data within it.
        \core_privacy\local\request\core_userlist_provider,

        // The block_slider block provides data directly to core.
        \core_privacy\local\request\plugin\provider {

    /**
     * Returns information about how block_slider stores its data.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return  collection     A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('slider_slides', [
                'slide_link' => 'privacy:metadata:slide_link',
                'slide_title' => 'privacy:metadata:slide_title',
                'slide_desc' => 'privacy:metadata:slide_desc',
                'slide_image' => 'privacy:metadata:slide_image'
        ], 'privacy:metadata:slider_slides');

        $collection->link_subsystem('block', 'privacy:metadata:block');

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return  contextlist   $contextlist  The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        // This block doesn't know who information is stored against unless it
        // is at the user context.
        $contextlist = new \core_privacy\local\request\contextlist();

        $sql = "SELECT c.id
                  FROM {block_instances} b
            INNER JOIN {context} c ON c.instanceid = b.id AND c.contextlevel = :contextblock
            INNER JOIN {context} bpc ON bpc.id = b.parentcontextid
                 WHERE b.blockname = 'slider'
                   AND bpc.contextlevel = :contextuser
                   AND bpc.instanceid = :userid";

        $params = [
                'contextblock' => CONTEXT_BLOCK,
                'contextuser' => CONTEXT_USER,
                'userid' => $userid,
        ];

        $contextlist->add_from_sql($sql, $params);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        // This block doesn't know who information is stored against unless it
        // is at the user context.
        $context = $userlist->get_context();

        if (!$context instanceof \context_block) {
            return;
        }

        $sql = "SELECT bpc.instanceid AS userid
                  FROM {block_instances} bi
                  JOIN {context} bpc ON bpc.id = bi.parentcontextid
                 WHERE bi.blockname = 'html'
                   AND bpc.contextlevel = :contextuser
                   AND bi.id = :blockinstanceid";

        $params = [
                'contextuser' => CONTEXT_USER,
                'blockinstanceid' => $context->instanceid
        ];

        $userlist->add_from_sql('userid', $sql, $params);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $CFG, $DB;

        $user = $contextlist->get_user();

        list($contextsql, $contextparams) = $DB->get_in_or_equal($contextlist->get_contextids(), SQL_PARAMS_NAMED);

        $sql = "SELECT
                    c.id AS contextid,
                    bi.*
                  FROM {context} c
            INNER JOIN {block_instances} bi ON bi.id = c.instanceid AND c.contextlevel = :contextlevel
                 WHERE bi.blockname = 'slider'
                   AND(
                    c.id {$contextsql}
                )
        ";

        $params = [
                'contextlevel' => CONTEXT_BLOCK,
        ];
        $params += $contextparams;

        $instances = $DB->get_recordset_sql($sql, $params);
        foreach ($instances as $instance) {
            $context = \context_block::instance($instance->id);
            $block = block_instance('slider', $instance);
            if (!$slides = $DB->get_records('slider_slides', array('sliderid' => $instance->id), 'slide_order ASC')) {
                return;
            } else {
                $html = '';
                foreach ($slides as $slide) {
                    $imageurl =
                            $CFG->wwwroot . '/pluginfile.php/' . $context->id . '/block_slider/slider_slides/' . $slide->id .
                            '/' . $slide->slide_image;
                    $html .= '<h3>Slide ' . $slide->id . '</h3><br />Slide link: ' . $slide->slide_link;
                    $html .= '<br />Slide title: ' . $slide->slide_title;
                    $html .= '<br />Slide desc: ' . $slide->slide_desc;
                    $html .= '<br />Slide image: ' . html_writer::empty_tag('img',
                                    array('src' => $imageurl, 'class' => 'img', 'alt' => $slide->slide_image,
                                            'title' => $slide->slide_title));
                }
            }
            $data = helper::get_context_data($context, $user);
            helper::export_context_files($context, $user);
            $data->title = $block->config->title;
            $data->content = $html;

            writer::with_context($context)->export_data([], $data);
        }
        $instances->close();
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {

        if (!$context instanceof \context_block) {
            return;
        }

        // The only way to delete data for the html block is to delete the block instance itself.
        if ($blockinstance = static::get_instance_from_context($context)) {
            blocks_delete_instance($blockinstance);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        if ($context instanceof \context_block && ($blockinstance = static::get_instance_from_context($context))) {
            blocks_delete_instance($blockinstance);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        // The only way to delete data for the html block is to delete the block instance itself.
        foreach ($contextlist as $context) {

            if (!$context instanceof \context_block) {
                continue;
            }
            if ($blockinstance = static::get_instance_from_context($context)) {
                blocks_delete_instance($blockinstance);
            }
        }
    }

    /**
     * Get the block instance record for the specified context.
     *
     * @param \context_block $context The context to fetch
     * @return  \stdClass
     */
    protected static function get_instance_from_context(\context_block $context) {
        global $DB;

        return $DB->get_record('block_instances', ['id' => $context->instanceid, 'blockname' => 'slider']);
    }
}
