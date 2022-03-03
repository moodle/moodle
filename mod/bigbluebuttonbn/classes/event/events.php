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

namespace mod_bigbluebuttonbn\event;

/**
 * The mod_bigbluebuttonbn class for event name definition.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class events {

    /**
     * Event name matcher.
     *
     * @var $events
     */
    public static $events = [
        'create' => 'activity_created',
        'view' => 'course_module_viewed',
        'update' => 'activity_updated',
        'delete' => 'activity_deleted',
        'meeting_create' => 'meeting_created',
        'meeting_end' => 'meeting_ended',
        'meeting_join' => 'meeting_joined',
        'meeting_left' => 'meeting_left',
        'recording_delete' => 'recording_deleted',
        'recording_import' => 'recording_imported',
        'recording_protect' => 'recording_protected',
        'recording_publish' => 'recording_published',
        'recording_unprotect' => 'recording_unprotected',
        'recording_unpublish' => 'recording_unpublished',
        'recording_edit' => 'recording_edited',
        'recording_play' => 'recording_viewed',
        'live_session' => 'live_session'
    ];
}
