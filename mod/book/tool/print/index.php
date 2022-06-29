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
 * Book printing
 *
 * @package    booktool_print
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../../config.php');
require_once(__DIR__.'/locallib.php');

$id        = required_param('id', PARAM_INT);           // Course Module ID
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID

// =========================================================================
// security checks START - teachers and students view
// =========================================================================

$cm = get_coursemodule_from_id('book', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$book = $DB->get_record('book', array('id' => $cm->instance), '*', MUST_EXIST);

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/book:read', $context);
require_capability('booktool/print:print', $context);

// Check all variables.
if ($chapterid) {
    // Single chapter printing - only visible!
    $chapter = $DB->get_record('book_chapters', array('id' => $chapterid, 'bookid' => $book->id), '*',
            MUST_EXIST);
} else {
    // Complete book.
    $chapter = false;
}

$PAGE->set_url('/mod/book/print.php', array('id' => $id, 'chapterid' => $chapterid));

$PAGE->activityheader->disable();
$PAGE->set_pagelayout("embedded");

unset($id);
unset($chapterid);

// Security checks END.

// read chapters
$chapters = book_preload_chapters($book);

$strbooks = get_string('modulenameplural', 'mod_book');
$strbook  = get_string('modulename', 'mod_book');
$strtop   = get_string('top', 'mod_book');

// Page header.
$strtitle = format_string($book->name, true, array('context' => $context));
$PAGE->set_title($strtitle);
$PAGE->set_heading($strtitle);
$PAGE->requires->css('/mod/book/tool/print/print.css');

$renderer = $PAGE->get_renderer('booktool_print');

// Begin page output.
echo $OUTPUT->header();

if ($chapter) {
    if ($chapter->hidden) {
        require_capability('mod/book:viewhiddenchapters', $context);
    }
    \booktool_print\event\chapter_printed::create_from_chapter($book, $context, $chapter)->trigger();
    $page = new booktool_print\output\print_book_chapter_page($book, $cm, $chapter);
} else {
    \booktool_print\event\book_printed::create_from_book($book, $context)->trigger();
    $page = new booktool_print\output\print_book_page($book, $cm);
}

echo $renderer->render($page);

// Finish page output.
echo $OUTPUT->footer();
