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

namespace core\moodlenet;

use moodle_exception;
use stdClass;

/**
 * Record the sharing of content to MoodleNet.
 *
 * @package   core
 * @copyright 2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class share_recorder {

    /**
     * @var int The content being shared is a course.
     */
    public const TYPE_COURSE = 1;

    /**
     * @var int The content being shared is an activity.
     */
    public const TYPE_ACTIVITY = 2;

    /**
     * @var int The status of the share is 'sent'.
     */
    public const STATUS_SENT = 1;

    /**
     * @var int The status of the share is 'in progress'.
     */
    public const STATUS_IN_PROGRESS = 2;

    /**
     * @var int The status of the share is 'error'.
     */
    public const STATUS_ERROR = 3;

    /**
     * Get all allowed share types.
     *
     * @return array
     */
    protected static function get_allowed_share_types(): array {

        return [
            self::TYPE_ACTIVITY,
            self::TYPE_COURSE
        ];
    }

    /**
     * Get all allowed share statuses.
     * Note that the particular status values aid in sorting.
     *
     * @return array
     */
    protected static function get_allowed_share_statuses(): array {

        return [
            self::STATUS_SENT,
            self::STATUS_IN_PROGRESS,
            self::STATUS_ERROR,
        ];
    }

    /**
     * Create a new share progress record in the DB.
     *
     * @param int $sharetype The type of share (e.g. TYPE_COURSE).
     * @param int $userid The ID of the user performing the share.
     * @param int $courseid The associated course id.
     * @param int|null $cmid The associated course module id (when sharing activity).
     * @return int Returns the inserted record id.
     */
    public static function insert_share_progress(int $sharetype, int $userid, int $courseid, ?int $cmid = null): int {
        global $DB, $USER;

        if (!in_array($sharetype, self::get_allowed_share_types())) {
            throw new moodle_exception('moodlenet:invalidsharetype');
        }

        $data = new stdClass();
        $data->type = $sharetype;
        $data->courseid = $courseid;
        $data->cmid = $cmid;
        $data->userid = $userid;
        $data->timecreated = time();
        $data->status = self::STATUS_IN_PROGRESS;

        return $DB->insert_record('moodlenet_share_progress', $data);
    }

    /**
     * Update the share progress record in the DB.
     *
     * @param int $shareid The id of the share progress row being updated.
     * @param int $status The status of the share progress (e.g. STATUS_SENT).
     * @param string|null $resourceurl The resource url returned from MoodleNet.
     */
    public static function update_share_progress(int $shareid, int $status, ?string $resourceurl = null): void {
        global $DB;

        if (!in_array($status, self::get_allowed_share_statuses())) {
            throw new moodle_exception('moodlenet:invalidsharestatus');
        }

        $data = new stdClass();
        $data->id = $shareid;
        $data->resourceurl = $resourceurl;
        $data->status = $status;

        $DB->update_record('moodlenet_share_progress', $data);
    }
}
