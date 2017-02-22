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
 * Book import
 *
 * @package    booktool_importhtml
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../../config.php');
require_once(__DIR__.'/locallib.php');
require_once(__DIR__.'/import_form.php');

$id        = required_param('id', PARAM_INT);           // Course Module ID
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID

$cm = get_coursemodule_from_id('book', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$book = $DB->get_record('book', array('id'=>$cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);

$context = context_module::instance($cm->id);
require_capability('booktool/importhtml:import', $context);

$PAGE->set_url('/mod/book/tool/importhtml/index.php', array('id'=>$id, 'chapterid'=>$chapterid));

if ($chapterid) {
    if (!$chapter = $DB->get_record('book_chapters', array('id'=>$chapterid, 'bookid'=>$book->id))) {
        $chapterid = 0;
    }
} else {
    $chapter = false;
}

$PAGE->set_title($book->name);
$PAGE->set_heading($course->fullname);

// Prepare the page header.
$strbook = get_string('modulename', 'mod_book');
$strbooks = get_string('modulenameplural', 'mod_book');

$mform = new booktool_importhtml_form(null, array('id'=>$id, 'chapterid'=>$chapterid));

// If data submitted, then process and store.
if ($mform->is_cancelled()) {
    if (empty($chapter->id)) {
        redirect($CFG->wwwroot."/mod/book/view.php?id=$cm->id");
    } else {
        redirect($CFG->wwwroot."/mod/book/view.php?id=$cm->id&chapterid=$chapter->id");
    }

} else if ($data = $mform->get_data()) {
    echo $OUTPUT->header();
    echo $OUTPUT->heading($book->name);
    echo $OUTPUT->heading(get_string('importingchapters', 'booktool_importhtml'), 3);

    // this is a bloody hack - children do not try this at home!
    $fs = get_file_storage();
    $draftid = file_get_submitted_draft_itemid('importfile');
    if (!$files = $fs->get_area_files(context_user::instance($USER->id)->id, 'user', 'draft', $draftid, 'id DESC', false)) {
        redirect($PAGE->url);
    }
    $file = reset($files);
    toolbook_importhtml_import_chapters($file, $data->type, $book, $context);

    echo $OUTPUT->continue_button(new moodle_url('/mod/book/view.php', array('id'=>$id)));
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->header();
echo $OUTPUT->heading($book->name);

$mform->display();

echo $OUTPUT->footer();
