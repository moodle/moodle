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

namespace mod_forum\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

/**
 * Web Service to control the read/unread state of a forum post.
 *
 * @package   mod_forum
 * @category  external
 * @copyright 2026 Daniel Urena <daniel.urena@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_read_state extends external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'postid' => new external_value(PARAM_INT, 'Identifier of the post whose read state will be changed'),
                'targetstate' => new external_value(PARAM_BOOL, 'Target read state (true = read, false = unread)'),
            ]
        );
    }

    /**
     * Set the read/unread state of a forum post for the current user.
     *
     * @param int $postid The post identifier.
     * @param bool $targetstate Whether to mark the post as read (true) or unread (false).
     * @return array Status of the operation and warnings.
     */
    public static function execute(
        int $postid,
        bool $targetstate,
    ): array {
        global $CFG, $USER;

        require_once($CFG->dirroot . '/mod/forum/lib.php');

        $params = self::validate_parameters(self::execute_parameters(), [
            'postid' => $postid,
            'targetstate' => $targetstate,
        ]);

        $warnings = [];

        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $forumvault = $vaultfactory->get_forum_vault();
        $discussionvault = $vaultfactory->get_discussion_vault();
        $postvault = $vaultfactory->get_post_vault();

        $postentity = $postvault->get_from_id($params['postid']);
        if (empty($postentity)) {
            throw new \moodle_exception('invalidpostid', 'forum');
        }

        $discussionentity = $discussionvault->get_from_id($postentity->get_discussion_id());
        if (empty($discussionentity)) {
            throw new \moodle_exception('notpartofdiscussion', 'forum');
        }

        $forumentity = $forumvault->get_from_id($discussionentity->get_forum_id());
        if (empty($forumentity)) {
            throw new \moodle_exception('invalidforumid', 'forum');
        }

        $context = $forumentity->get_context();
        self::validate_context($context);

        $managerfactory = \mod_forum\local\container::get_manager_factory();
        $capabilitymanager = $managerfactory->get_capability_manager($forumentity);

        // Ensure the user has access to this post.
        if (!$capabilitymanager->can_view_post($USER, $discussionentity, $postentity)) {
            throw new \moodle_exception('noviewdiscussionspermission', 'forum');
        }

        // Only attempt to change the read state when manual marking is enabled and tracking is active.
        $cancontrol = $capabilitymanager->can_manually_control_post_read_status($USER);
        if (!$cancontrol) {
            $warnings[] = [
                'item' => 'post',
                'itemid' => $postentity->get_id(),
                'warningcode' => 'cannotcontrolreadstatus',
                'message' => 'The user cannot manually control the read status for this post.',
            ];

            return [
                'status' => false,
                'warnings' => $warnings,
            ];
        }

        if ($params['targetstate']) {
            forum_tp_add_read_record($USER->id, $postentity->get_id());
        } else {
            forum_tp_delete_read_records($USER->id, $postentity->get_id());
        }
        return [
            'status' => true,
            'warnings' => $warnings,
        ];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'True if the operation succeeded'),
            'warnings' => new \core_external\external_warnings(),
        ]);
    }
}
