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
 * Privacy helper.
 *
 * @package    tool_log
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_log\local\privacy;
defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\transform;

/**
 * Privacy helper class.
 *
 * @package    tool_log
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    use \tool_log\helper\reader;

    /**
     * Returns an event from a standard record.
     *
     * @see \logstore_standard\log\store::get_log_event()
     * @param object $data Log data.
     * @return \core\event\base
     */
    protected static function restore_event_from_standard_record($data) {
        $extra = ['origin' => $data->origin, 'ip' => $data->ip, 'realuserid' => $data->realuserid];
        $data = (array) $data;
        $id = $data['id'];
        $data['other'] = self::decode_other($data['other']);
        if ($data['other'] === false) {
            $data['other'] = [];
        }
        unset($data['origin']);
        unset($data['ip']);
        unset($data['realuserid']);
        unset($data['id']);

        if (!$event = \core\event\base::restore($data, $extra)) {
            return null;
        }

        return $event;
    }

    /**
     * Transform a standard log record for a user.
     *
     * @param object $record The record.
     * @param int $userid The user ID.
     * @return array
     */
    public static function transform_standard_log_record_for_userid($record, $userid) {

        // Restore the event to try to get the name, description and other field.
        $restoredevent = static::restore_event_from_standard_record($record);
        if ($restoredevent) {
            $name = $restoredevent->get_name();
            $description = $restoredevent->get_description();
            $other = $restoredevent->other;

        } else {
            $name = $record->eventname;
            $description = "Unknown event ({$name})";
            $other = unserialize($record->other);
        }

        $realuserid = $record->realuserid;
        $isauthor = $record->userid == $userid;
        $isrelated = $record->relateduserid == $userid;
        $isrealuser = $realuserid == $userid;
        $ismasqueraded = $realuserid !== null && $record->userid != $realuserid;
        $ismasquerading = $isrealuser && !$isauthor;
        $isanonymous = $record->anonymous;

        $data = [
            'name' => $name,
            'description' => $description,
            'timecreated' => transform::datetime($record->timecreated),
            'origin' => static::transform_origin($record->origin),
            'ip' => $isauthor ? $record->ip : '',
            'other' => $other ? $other : []
        ];

        if ($isanonymous) {
            $data['action_was_done_anonymously'] = transform::yesno($isanonymous);
        }
        if ($isauthor || !$isanonymous) {
            $data['authorid'] = transform::user($record->userid);
            $data['author_of_the_action_was_you'] = transform::yesno($isauthor);
        }

        if ($record->relateduserid) {
            $data['relateduserid'] = transform::user($record->relateduserid);
            $data['related_user_was_you'] = transform::yesno($isrelated);
        }

        if ($ismasqueraded) {
            $data['author_of_the_action_was_masqueraded'] = transform::yesno(true);
            if ($ismasquerading || !$isanonymous) {
                $data['masqueradinguserid'] = transform::user($realuserid);
                $data['masquerading_user_was_you'] = transform::yesno($ismasquerading);
            }
        }

        return $data;
    }

    /**
     * Transform origin.
     *
     * @param string $origin The page request origin.
     * @return string
     */
    public static function transform_origin($origin) {
        switch ($origin) {
            case 'cli':
            case 'restore':
            case 'web':
            case 'ws':
                return get_string('privacy:request:origin:' . $origin, 'tool_log');
                break;
        }
        return $origin;
    }
}
