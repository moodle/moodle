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

require(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id        = required_param('id', PARAM_INT);        // Course Module ID
$chapterid = required_param('chapterid', PARAM_INT); // Chapter ID
$confirm   = optional_param('confirm', 0, PARAM_BOOL);

$cm = get_coursemodule_from_id('book', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$book = $DB->get_record('book', array('id'=>$cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);
require_sesskey();

$context = context_module::instance($cm->id);
require_capability('mod/book:edit', $context);

$PAGE->set_url('/mod/book/delete.php', array('id'=>$id, 'chapterid'=>$chapterid));

$chapter = $DB->get_record('book_chapters', array('id'=>$chapterid, 'bookid'=>$book->id), '*', MUST_EXIST);


// Header and strings.
$PAGE->set_title($book->name);
$PAGE->set_heading($course->fullname);

// Form processing.
if ($confirm) {  // the operation was confirmed.
    $fs = get_file_storage();
    if (!$chapter->subchapter) { // Delete all its sub-chapters if any
        $chapters = $DB->get_recordset('book_chapters', array('bookid'=>$book->id), 'pagenum');
        $found = false;
        foreach ($chapters as $ch) {
            if ($ch->id == $chapter->id) {
                $found = true;
            } else if ($found and $ch->subchapter) {
                $fs->delete_area_files($context->id, 'mod_book', 'chapter', $ch->id);
                $DB->delete_records('book_chapters', array('id'=>$ch->id));
                \mod_book\event\chapter_deleted::create_from_chapter($book, $context, $ch)->trigger();
            } else if ($found) {
                break;
            }
        }
        $chapters->close();
    }
    $fs->delete_area_files($context->id, 'mod_book', 'chapter', $chapter->id);
    $DB->delete_records('book_chapters', array('id'=>$chapter->id));

    \mod_book\event\chapter_deleted::create_from_chapter($book, $context, $chapter)->trigger();

    book_preload_chapters($book); // Fix structure.
    $DB->set_field('book', 'revision', $book->revision+1, array('id'=>$book->id));

    redirect('view.php?id='.$cm->id);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($book->name);

// The operation has not been confirmed yet so ask the user to do so.
if ($chapter->subchapter) {
    $strconfirm = get_string('confchapterdelete', 'mod_book');
} else {
    $strconfirm = get_string('confchapterdeleteall', 'mod_book');
}
echo '<br />';
$continue = new moodle_url('/mod/book/delete.php', array('id'=>$cm->id, 'chapterid'=>$chapter->id, 'confirm'=>1));
$cancel = new moodle_url('/mod/book/view.php', array('id'=>$cm->id, 'chapterid'=>$chapter->id));
echo $OUTPUT->confirm("<strong>$chapter->title</strong><p>$strconfirm</p>", $continue, $cancel);

echo $OUTPUT->footer();