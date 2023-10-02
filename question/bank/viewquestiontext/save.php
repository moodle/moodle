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
 * Synchronously save the question text display preference, and redirect back to the previous page.
 *
 * This is progressively enhanced by question_text_format.js, but this remains as a fallback.
 *
 * @package   qbank_viewquestiontext
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/question/editlib.php');

$format = required_param('format', PARAM_INT);
$returnurl = required_param('returnurl', PARAM_LOCALURL);

require_login();
require_sesskey();

$validformats = [
    \qbank_viewquestiontext\output\question_text_format::OFF,
    \qbank_viewquestiontext\output\question_text_format::PLAIN,
    \qbank_viewquestiontext\output\question_text_format::FULL,
];
if (!in_array($format, $validformats)) {
    throw new \invalid_parameter_exception('$format must be one of question_text_format::OFF, ::PLAIN or ::FULL.');
}

question_set_or_get_user_preference('qbshowtext', $format, 0, new \moodle_url('/'));

redirect(new moodle_url($returnurl));
