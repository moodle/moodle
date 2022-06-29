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
 * Add event handlers for the accessibility report
 *
 * @package    tool_brickfield
 * @category   event
 * @copyright  2020 Brickfield Education Labs https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [
    [
        'eventname' => '\core\event\course_category_created',
        'callback'  => '\tool_brickfield\eventobservers::course_category_created',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_category_deleted',
        'callback'  => '\tool_brickfield\eventobservers::course_category_deleted',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_category_restored',
        'callback'  => '\tool_brickfield\eventobservers::course_category_restored',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_category_updated',
        'callback'  => '\tool_brickfield\eventobservers::course_category_updated',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_created',
        'callback'  => '\tool_brickfield\eventobservers::course_created',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_deleted',
        'callback'  => '\tool_brickfield\eventobservers::course_deleted',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_restored',
        'callback'  => '\tool_brickfield\eventobservers::course_restored',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_updated',
        'callback'  => '\tool_brickfield\eventobservers::course_updated',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_section_created',
        'callback'  => '\tool_brickfield\eventobservers::course_section_created',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_section_deleted',
        'callback'  => '\tool_brickfield\eventobservers::course_section_deleted',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_section_updated',
        'callback'  => '\tool_brickfield\eventobservers::course_section_updated',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_module_created',
        'callback'  => '\tool_brickfield\eventobservers::course_module_created',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_module_restored',
        'callback'  => '\tool_brickfield\eventobservers::course_module_restored',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_module_updated',
        'callback'  => '\tool_brickfield\eventobservers::course_module_updated',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\course_module_deleted',
        'callback'  => '\tool_brickfield\eventobservers::course_module_deleted',
        'internal'  => false,
    ],
    [
        'eventname' => '\mod_lesson\event\page_created',
        'callback'  => '\tool_brickfield\eventobservers::mod_lesson_page_created',
        'internal'  => false,
    ],
    [
        'eventname' => '\mod_lesson\event\page_deleted',
        'callback'  => '\tool_brickfield\eventobservers::mod_lesson_page_deleted',
        'internal'  => false,
    ],
    [
        'eventname' => '\mod_lesson\event\page_updated',
        'callback'  => '\tool_brickfield\eventobservers::mod_lesson_page_updated',
        'internal'  => false,
    ],
    [
        'eventname' => '\mod_book\event\chapter_created',
        'callback'  => '\tool_brickfield\eventobservers::book_chapter_created',
        'internal'  => false,
    ],
    [
        'eventname' => '\mod_book\event\chapter_deleted',
        'callback'  => '\tool_brickfield\eventobservers::book_chapter_deleted',
        'internal'  => false,
    ],
    [
        'eventname' => '\mod_book\event\chapter_updated',
        'callback'  => '\tool_brickfield\eventobservers::book_chapter_updated',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\question_created',
        'callback'  => '\tool_brickfield\eventobservers::core_question_created',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\question_updated',
        'callback'  => '\tool_brickfield\eventobservers::core_question_updated',
        'internal'  => false,
    ],
    [
        'eventname' => '\core\event\question_deleted',
        'callback'  => '\tool_brickfield\eventobservers::core_question_deleted',
        'internal'  => false,
    ],
];
