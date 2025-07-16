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
use mod_forum\local\exporters\forum as forum_exporter;

/**
 * Web Service to control the state of a forum tracking.
 *
 * @package   mod_forum
 * @category  external
 * @copyright 2025 Sara Arjona <sara@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_forum_tracking extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'forumid' => new external_value(PARAM_INT, 'Forum that the user wants tracking for'),
                'targetstate' => new external_value(PARAM_BOOL, 'The target state'),
            ]
        );
    }

    /**
     * Set the forum tracking state.
     *
     * @param int $forumid The forum identifier.
     * @param bool $targetstate Whether to track or not the unread posts in the forum.
     * @return  \stdClass
     */
    public static function execute(
        int $forumid,
        bool $targetstate,
    ): \stdClass {
        global $PAGE, $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'forumid' => $forumid,
            'targetstate' => $targetstate,
        ]);

        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $forumvault = $vaultfactory->get_forum_vault();
        $forum = $forumvault->get_from_id($params['forumid']);
        if (!$forum) {
            throw new \moodle_exception('invalidforumid', 'mod_forum', '', $params['forumid']);
        }
        $context = $forum->get_context();

        self::validate_context($context);

        $legacydatamapperfactory = \mod_forum\local\container::get_legacy_data_mapper_factory();
        $forumrecord = $legacydatamapperfactory->get_forum_data_mapper()->to_legacy_object($forum);

        $usetracking = forum_tp_can_track_forums($forum);
        if (!$usetracking) {
            // Nothing to do. We won't actually output any content here though.
            throw new \moodle_exception('cannottrack', 'mod_forum');
        }

        $istracked = forum_tp_is_tracked($forumrecord);
        // If the current state doesn't equal the desired state then update the current
        // state to the desired state.
        if ($istracked != (bool) $params['targetstate']) {
            if ($params['targetstate']) {
                forum_tp_start_tracking($forumrecord->id);
            } else {
                forum_tp_stop_tracking($forumrecord->id);
            }
            $cache = \cache::make('mod_forum', 'forum_is_tracked');
            $cache->purge();
        }

        /** @var \mod_forum\local\factories\exporter $exporterfactory */
        $exporterfactory = \mod_forum\local\container::get_exporter_factory();
        $exporter = $exporterfactory->get_forum_exporter(
            user: $USER,
            forum: $forum,
            currentgroup: null,
        );
        return $exporter->export($PAGE->get_renderer('mod_forum'));
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return forum_exporter::get_read_structure();
    }
}
