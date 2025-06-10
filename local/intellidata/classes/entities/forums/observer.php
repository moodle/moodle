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
 * Observer
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_intellidata\entities\forums;

use local_intellidata\helpers\TrackingHelper;
use local_intellidata\services\events_service;

/**
 * Event observer for transcripts.
 */
class observer {

    /**
     * Triggered when 'discussion_created' event is triggered.
     *
     * @param \mod_forum\event\discussion_created $event
     */
    public static function discussion_created(\mod_forum\event\discussion_created $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $discussion = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);
            self::export_discussion_event($eventdata, $discussion);
        }
    }

    /**
     * Triggered when 'discussion_updated' event is triggered.
     *
     * @param \mod_forum\event\discussion_updated $event
     */
    public static function discussion_updated(\mod_forum\event\discussion_updated $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $discussion = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);
            self::export_discussion_event($eventdata, $discussion);
        }
    }

    /**
     * Triggered when 'discussion_moved' event is triggered.
     *
     * @param \mod_forum\event\discussion_moved $event
     */
    public static function discussion_moved(\mod_forum\event\discussion_moved $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $discussion = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);
            $discussion->forum = $eventdata['other']['toforumid'];

            self::export_discussion_event($eventdata, $discussion);
        }
    }

    /**
     * Triggered when 'discussion_deleted' event is triggered.
     *
     * @param \mod_forum\event\discussion_deleted $event
     */
    public static function discussion_deleted(\mod_forum\event\discussion_deleted $event) {
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();

            $discussion = new \stdClass();
            $discussion->id = $eventdata['objectid'];

            self::export_discussion_event($eventdata, $discussion);
        }
    }

    /**
     * Triggered when 'post_created' event is triggered.
     *
     * @param \mod_forum\event\post_created $event
     */
    public static function post_created(\mod_forum\event\post_created $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $post = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);
            $post->forum = $eventdata['other']['forumid'];

            self::export_post_event($eventdata, $post);
        }
    }

    /**
     * Triggered when 'post_updated' event is triggered.
     *
     * @param \mod_forum\event\post_updated $event
     */
    public static function post_updated(\mod_forum\event\post_updated $event) {
        if (TrackingHelper::eventstracking_enabled()) {
            $eventdata = $event->get_data();

            $post = $event->get_record_snapshot($eventdata['objecttable'], $eventdata['objectid']);
            $post->forum = $eventdata['other']['forumid'];

            if ($post->parent == 0) {
                $discussion = $event->get_record_snapshot('forum_discussions', $post->discussion);
                self::export_discussion_event(
                    ['eventname' => '\mod_forum\event\discussion_updated', 'crud' => 'u'],
                    $discussion
                );
            }

            self::export_post_event($eventdata, $post);
        }
    }

    /**
     * Triggered when 'post_deleted' event is triggered.
     *
     * @param \mod_forum\event\post_deleted $event
     */
    public static function post_deleted(\mod_forum\event\post_deleted $event) {
        if (TrackingHelper::enabled()) {
            $eventdata = $event->get_data();

            $post = new \stdClass();
            $post->id = $eventdata['objectid'];

            self::export_post_event($eventdata, $post);
        }
    }

    /**
     * Export discussion event.
     *
     * @param $eventdata
     * @param $discussion
     * @param $fields
     * @return void
     */
    private static function export_discussion_event($eventdata, $discussion, $fields = []) {
        $discussion->crud = $eventdata['crud'];

        $entity = new forumdiscussion($discussion, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }

    /**
     * Export post event.
     *
     * @param $eventdata
     * @param $post
     * @param $fields
     * @return void
     */
    private static function export_post_event($eventdata, $post, $fields = []) {
        $post->crud = $eventdata['crud'];

        $entity = new forumpost($post, $fields);
        $data = $entity->export();

        $tracking = new events_service($entity::TYPE);
        $tracking->track($data);
    }

}

