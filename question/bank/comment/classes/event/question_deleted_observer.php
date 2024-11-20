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

namespace qbank_comment\event;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/comment/lib.php');

use core\event\question_deleted;

/**
 * Event observer for question deletion
 *
 * @package   qbank_comment
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_deleted_observer {

    /**
     * Delete any comments for the deleted question.
     *
     * @param question_deleted $event
     * @return void
     */
    public static function delete_question_comments(question_deleted $event): void {
        \comment::delete_comments([
            'contextid' => \context_system::instance()->id,
            'component' => 'qbank_comment',
            'commentarea' => 'question',
            'itemid' => $event->objectid,
        ]);
    }
}
