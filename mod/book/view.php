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
 * Book view page
 *
 * @package    mod_book
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id        = optional_param('id', 0, PARAM_INT);        // Course Module ID
$bid       = optional_param('b', 0, PARAM_INT);         // Book id
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID
$edit      = optional_param('edit', -1, PARAM_BOOL);    // Edit mode

// =========================================================================
// security checks START - teachers edit; students view
// =========================================================================
if ($id) {
    $cm = get_coursemodule_from_id('book', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $book = $DB->get_record('book', array('id'=>$cm->instance), '*', MUST_EXIST);
} else {
    $book = $DB->get_record('book', array('id'=>$bid), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('book', $book->id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $id = $cm->id;
}

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/book:read', $context);

$allowedit  = has_capability('mod/book:edit', $context);
$viewhidden = has_capability('mod/book:viewhiddenchapters', $context);

if ($allowedit) {
    if ($edit != -1 and confirm_sesskey()) {
        $USER->editing = $edit;
    } else {
        if (isset($USER->editing)) {
            $edit = $USER->editing;
        } else {
            $edit = 0;
        }
    }
} else {
    $edit = 0;
}

// read chapters
$chapters = book_preload_chapters($book);

if ($allowedit and !$chapters) {
    redirect('edit.php?cmid='.$cm->id); // No chapters - add new one.
}
// Check chapterid and read chapter data
if ($chapterid == '0') { // Go to first chapter if no given.
    // Trigger course module viewed event.
    book_view($book, null, false, $course, $cm, $context);

    foreach ($chapters as $ch) {
        if ($edit || ($ch->hidden && $viewhidden)) {
            $chapterid = $ch->id;
            break;
        }
        if (!$ch->hidden) {
            $chapterid = $ch->id;
            break;
        }
    }
}

// Prepare header.
$pagetitle = $book->name;
if ($chapter = $DB->get_record('book_chapters', ['id' => $chapterid, 'bookid' => $book->id])) {
    $pagetitle .= ": {$chapter->title}";
}
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);
$PAGE->add_body_class('limitedwidth');

// No content in the book.
if (!$chapterid) {
    $PAGE->set_url('/mod/book/view.php', array('id' => $id));
    echo $OUTPUT->header();
    echo $OUTPUT->notification(get_string('nocontent', 'mod_book'), 'info', false);
} else {
    $PAGE->set_url('/mod/book/view.php', ['id' => $id, 'chapterid' => $chapterid]);
    // The chapter doesnt exist or it is hidden for students.
    if (!$chapter or ($chapter->hidden and !$viewhidden)) {
        $courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);
        throw new moodle_exception('errorchapter', 'mod_book', $courseurl);
    }
    // Add the Book TOC block.
    book_add_fake_block($chapters, $chapter, $book, $cm, $edit);
    // We need to discover if this is the last chapter to mark activity as completed.
    $islastchapter = $chapter->pagenum + 1 > count($chapters);
    book_view($book, $chapter, $islastchapter, $course, $cm, $context);

    echo $OUTPUT->header();

    $renderer = $PAGE->get_renderer('mod_book');
    $actionmenu = new \mod_book\output\main_action_menu($cm->id, $chapters, $chapter, $book);
    $renderedmenu = $renderer->render($actionmenu);
    echo $renderedmenu;

    // The chapter itself.
    $hidden = $chapter->hidden ? ' dimmed_text' : null;
    echo $OUTPUT->box_start('generalbox book_content' . $hidden);

    if (!$book->customtitles) {
        if (!$chapter->subchapter) {
            $currtitle = book_get_chapter_title($chapter->id, $chapters, $book, $context);
            echo $OUTPUT->heading($currtitle, 3);
        } else {
            $currtitle = book_get_chapter_title($chapters[$chapter->id]->parent, $chapters, $book, $context);
            $currsubtitle = book_get_chapter_title($chapter->id, $chapters, $book, $context);
            echo $OUTPUT->heading($currtitle, 3);
            echo $OUTPUT->heading($currsubtitle, 4);
        }
    }
    $chaptertext = file_rewrite_pluginfile_urls($chapter->content, 'pluginfile.php', $context->id, 'mod_book',
        'chapter', $chapter->id);
    echo format_text($chaptertext, $chapter->contentformat, ['noclean' => true, 'overflowdiv' => true,
        'context' => $context]);

    echo $OUTPUT->box_end();

    if (core_tag_tag::is_enabled('mod_book', 'book_chapters')) {
        echo $OUTPUT->tag_list(core_tag_tag::get_item_tags('mod_book', 'book_chapters', $chapter->id), null, 'book-tags');
    }
    echo $renderedmenu;
}
echo $OUTPUT->footer();
