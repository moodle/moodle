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
 * Ally event hooks.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => \core\event\course_created::class,
        'callback'  => '\tool_ally\event_handlers::course_created',
    ],
    [
        'eventname' => \core\event\course_updated::class,
        'callback'  => '\tool_ally\event_handlers::course_updated',
    ],
    [
        'eventname' => \core\event\course_deleted::class,
        'callback'  => '\tool_ally\event_handlers::course_deleted'
    ],
    [
        'eventname' => \core\event\course_restored::class,
        'callback'  => '\tool_ally\event_handlers::course_restored'
    ],
    [
        'eventname' => \core\event\course_section_created::class,
        'callback'  => '\tool_ally\event_handlers::course_section_created'
    ],
    [
        'eventname' => \core\event\course_section_updated::class,
        'callback'  => '\tool_ally\event_handlers::course_section_updated'
    ],
    [
        'eventname' => \core\event\course_section_deleted::class,
        'callback'  => '\tool_ally\event_handlers::course_section_deleted'
    ],
    [
        'eventname' => \core\event\course_module_created::class,
        'callback'  => '\tool_ally\event_handlers::course_module_created'
    ],
    [
        'eventname' => \core\event\course_module_updated::class,
        'callback'  => '\tool_ally\event_handlers::course_module_updated'
    ],
    [
        'eventname' => \core\event\course_module_deleted::class,
        'callback'  => '\tool_ally\event_handlers::course_module_deleted'
    ],
    [
        'eventname' => \core\event\group_created::class,
        'callback'  => '\tool_ally\event_handlers::group_created'
    ],
    [
        'eventname' => \core\event\group_updated::class,
        'callback'  => '\tool_ally\event_handlers::group_updated'
    ],
    [
        'eventname' => \mod_forum\event\discussion_created::class,
        'callback'  => '\tool_ally\event_handlers::forum_discussion_created'
    ],
    [
        'eventname' => \mod_forum\event\discussion_updated::class,
        'callback'  => '\tool_ally\event_handlers::forum_discussion_updated'
    ],
    [
        'eventname' => \mod_forum\event\discussion_deleted::class,
        'callback'  => '\tool_ally\event_handlers::forum_discussion_deleted'
    ],
    [
        'eventname' => \mod_forum\event\post_updated::class,
        'callback' => '\tool_ally\event_handlers::forum_post_updated'
    ],
    [
        'eventname' => \mod_hsuforum\event\discussion_created::class,
        'callback'  => '\tool_ally\event_handlers::hsuforum_discussion_created'
    ],
    [
        'eventname' => \mod_hsuforum\event\discussion_updated::class,
        'callback'  => '\tool_ally\event_handlers::hsuforum_discussion_updated'
    ],
    [
        'eventname' => \mod_hsuforum\event\discussion_deleted::class,
        'callback'  => '\tool_ally\event_handlers::hsuforum_discussion_deleted'
    ],
    [
        'eventname' => \mod_hsuforum\event\post_updated::class,
        'callback' => '\tool_ally\event_handlers::hsuforum_post_updated'
    ],
    [
        'eventname' => \mod_glossary\event\entry_created::class,
        'callback'  => '\tool_ally\event_handlers::glossary_entry_created'
    ],
    [
        'eventname' => \mod_glossary\event\entry_updated::class,
        'callback'  => '\tool_ally\event_handlers::glossary_entry_updated'
    ],
    [
        'eventname' => \mod_glossary\event\entry_deleted::class,
        'callback'  => '\tool_ally\event_handlers::glossary_entry_deleted'
    ],
    [
        'eventname' => \mod_book\event\chapter_created::class,
        'callback'  => '\tool_ally\event_handlers::book_chapter_created'
    ],
    [
        'eventname' => \mod_book\event\chapter_updated::class,
        'callback'  => '\tool_ally\event_handlers::book_chapter_updated'
    ],
    [
        'eventname' => \mod_book\event\chapter_deleted::class,
        'callback'  => '\tool_ally\event_handlers::book_chapter_deleted'
    ],
    [
        'eventname' => \mod_lesson\event\page_created::class,
        'callback'  => '\tool_ally\event_handlers::lesson_page_created'
    ],
    [
        'eventname' => \mod_lesson\event\page_updated::class,
        'callback'  => '\tool_ally\event_handlers::lesson_page_updated'
    ],
    [
        'eventname' => \mod_lesson\event\page_deleted::class,
        'callback'  => '\tool_ally\event_handlers::lesson_page_deleted'
    ]
];
