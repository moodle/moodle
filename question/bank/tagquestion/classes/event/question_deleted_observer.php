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

namespace qbank_tagquestion\event;

use core\context;
use core\event\question_deleted;

/**
 * Event observer for question deletion
 *
 * @package   qbank_tagquestion
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_deleted_observer {

    /**
     * Delete any tags defined for the deleted question.
     *
     * This uses {@see \core_tag_tag::set_item_tags} rather than {@see \core_tag_tag::remove_all_item_tags} since the latter
     * will always pass the system context, not the question context that the tag was set in.
     *
     * @param question_deleted $event
     * @return void
     */
    public static function delete_question_tags(question_deleted $event): void {
        $questioncontext = context::instance_by_id($event->contextid);
        \core_tag_tag::set_item_tags('core_question', 'question', $event->objectid, $questioncontext, null, $event->userid);
    }
}
