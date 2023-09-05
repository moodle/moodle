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
 * qbank_viewquestiontext external functions and service definitions.
 *
 * @package    qbank_viewquestiontext
 * @category   webservice
 * @copyright  2023 Catalyst IT Europe Ltd
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'qbank_viewquestiontext_set_question_text_format' => [
        'classname' => 'qbank_viewquestiontext\external\set_question_text_format',
        'description' => 'Sets the preference for displaying and formatting the question text',
        'type' => 'write',
        'ajax' => true,
    ],
];
