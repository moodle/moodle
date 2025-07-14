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

namespace qbank_customfields\event;

use core\event\question_deleted;
use qbank_customfields\customfield\question_handler;

/**
 * Event observer for question deletion
 *
 * @package   qbank_customfields
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_deleted_observer {

    /**
     * Delete any custom field data for the deleted question.
     *
     * @param question_deleted $event
     * @return void
     */
    public static function delete_question_customfields(question_deleted $event): void {
        question_handler::create()->delete_instance($event->objectid);
    }
}
