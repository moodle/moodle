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
 * Hook listener callbacks.
 *
 * @package    block_ai_chat
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$callbacks = [
        [
                'hook' => \core_course\hook\after_form_definition::class,
                'callback' => \block_ai_chat\local\hook_callbacks::class . '::handle_after_form_definition',
        ],
        [
                'hook' => \core_course\hook\after_form_submission::class,
                'callback' => \block_ai_chat\local\hook_callbacks::class . '::handle_after_form_submission',
        ],
        [
                'hook' => \core_course\hook\after_form_definition_after_data::class,
                'callback' => \block_ai_chat\local\hook_callbacks::class . '::handle_after_form_definition_after_data',
        ],
        [
                'hook' => \core\hook\output\before_footer_html_generation::class,
                'callback' => \block_ai_chat\local\hook_callbacks::class . '::handle_before_footer_html_generation',
        ],
        [
                'hook' => \core\hook\output\before_http_headers::class,
                'callback' => \block_ai_chat\local\hook_callbacks::class . '::handle_before_http_headers',
        ],
];
