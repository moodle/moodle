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
 * Delete book chapter
 *
 * @package    mod_book
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');

// Course Module ID.
$id        = required_param('id', PARAM_INT);

// Chapter ID.
$chapterid = required_param('chapterid', PARAM_INT);

$confirm   = optional_param('confirm', 0, PARAM_BOOL);

$cm = get_coursemodule_from_id('book', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$book = $DB->get_record('book', ['id' => $cm->instance], '*', MUST_EXIST);
$chapter = $DB->get_record('book_chapters', ['id' => $chapterid, 'bookid' => $book->id], '*', MUST_EXIST);

require_login($course, false, $cm);
require_sesskey();

$context = context_module::instance($cm->id);
require_capability('mod/book:edit', $context);

$PAGE->set_url('/mod/book/delete.php', ['id' => $id, 'chapterid' => $chapterid]);

if ($confirm) {
    // The operation was confirmed.
    $fs = get_file_storage();

    $subchaptercount = 0;
    if (!$chapter->subchapter) {
        // This is a top-level chapter.
        // Make sure to remove any sub-chapters if there are any.
        $chapters = $DB->get_recordset_select('book_chapters', 'bookid = :bookid AND pagenum > :pagenum', [
                'bookid' => $book->id,
                'pagenum' => $chapter->pagenum,
            ], 'pagenum');

        foreach ($chapters as $ch) {
            if (!$ch->subchapter) {
                // This is a new chapter. Any subsequent subchapters will be part of a different chapter.
                break;
            } else {
                // This is subchapter of the chapter being removed.
                core_tag_tag::remove_all_item_tags('mod_book', 'book_chapters', $ch->id);
                $fs->delete_area_files($context->id, 'mod_book', 'chapter', $ch->id);
                $DB->delete_records('book_chapters', ['id' => $ch->id]);
                \mod_book\event\chapter_deleted::create_from_chapter($book, $context, $ch)->trigger();

                $subchaptercount++;
            }
        }
        $chapters->close();
    }

    // Now delete the actual chapter.
    core_tag_tag::remove_all_item_tags('mod_book', 'book_chapters', $chapter->id);
    $fs->delete_area_files($context->id, 'mod_book', 'chapter', $chapter->id);
    $DB->delete_records('book_chapters', ['id' => $chapter->id]);

    \mod_book\event\chapter_deleted::create_from_chapter($book, $context, $chapter)->trigger();

    // Ensure that the book structure is correct.
    // book_preload_chapters will fix parts including the pagenum.
    $chapters = book_preload_chapters($book);

    book_add_fake_block($chapters, $chapter, $book, $cm);

    // Bump the book revision.
    $DB->set_field('book', 'revision', $book->revision + 1, ['id' => $book->id]);

    if ($subchaptercount) {
        $message = get_string('chapterandsubchaptersdeleted', 'mod_book', (object) [
            'title' => format_string($chapter->title),
            'subchapters' => $subchaptercount,
        ]);
    } else {
        $message = get_string('chapterdeleted', 'mod_book', (object) [
            'title' => format_string($chapter->title),
        ]);
    }

    redirect(new moodle_url('/mod/book/view.php', ['id' => $cm->id]), $message);
}

redirect(new moodle_url('/mod/book/view.php', ['id' => $cm->id]));
