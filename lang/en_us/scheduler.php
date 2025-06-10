<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'scheduler', language 'en_us', version '4.1'.
 *
 * @package     scheduler
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['email_applied_html'] = '<p>An appointment has been applied for on {$a->date} at {$a->time},<br/>
by the student <a href="{$a->attendee_url}">{$a->attendee}</a> for the course:</p>

<p>{$a->course_short}: <a href="{$a->course_url}">{$a->course}</a></p>

<p>using the scheduler titled "<em><a href="{$a->scheduler_url}">{$a->module}</a></em>" on the website: <a href="{$a->site_url}">{$a->site}</a>.</p>';
