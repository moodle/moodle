<?php
// This file is part of Book module for Moodle - http://moodle.org/
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
 * @package    booktool
 * @subpackage importhtml
 * @copyright  2004-2011 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

die('TODO');

require('../../config.php');
require_once($CFG->dirroot.'/mod/book/locallib.php');
require_once('import_form.php');

$id        = required_param('id', PARAM_INT);           // Course Module ID
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID

$cm = get_coursemodule_from_id('book', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$book = $DB->get_record('book', array('id'=>$cm->instance), '*', MUST_EXIST);

require_login($course, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/book:import', $context);

$PAGE->set_url('/mod/book/import.php', array('id'=>$id, 'chapterid'=>$chapterid));

if ($chapterid) {
    if (!$chapter = $DB->get_record('book_chapters', array('id'=>$chapterid, 'bookid'=>$book->id))) {
        $chapterid = 0;
    }
} else {
    $chapter = false;
}

$PAGE->set_title(format_string($book->name));
$PAGE->add_body_class('mod_book');
$PAGE->set_heading(format_string($course->fullname));

///prepare the page header
$strbook = get_string('modulename', 'mod_book');
$strbooks = get_string('modulenameplural', 'mod_book');

$mform = new book_import_form(null, array('id'=>$id, 'chapterid'=>$chapterid));

/// If data submitted, then process and store.
if ($mform->is_cancelled()) {
    if (empty($chapter->id)) {
        redirect("view.php?id=$cm->id");
    } else {
        redirect("view.php?id=$cm->id&chapterid=$chapter->id");
    }

} else if ($data = $mform->get_data()) {



/*

    $coursebase = $CFG->dataroot.'/'.$book->course;

    $reference = book_prepare_link($data->reference);

    if ($reference == '') {
        $base = $coursebase;
    } else {
        $base = $coursebase.'/'.$reference;
    }

    //prepare list of html files in $refs
    $refs = array();
    $htmlpat = '/\.html$|\.htm$/i';
    if (is_dir($base)) { //import whole directory
        $basedir = opendir($base);
        while ($file = readdir($basedir)) {
            $path = $base.'/'.$file;
            if (filetype($path) == 'file' and preg_match($htmlpat, $file)) {
                $refs[] = str_replace($coursebase, '', $path);
            }
        }
        asort($refs);
    } else if (is_file($base)) { //import single file
        $refs[] = '/'.$reference;
    } else { //what is it???
        error('Incorrect file/directory specified!');
    }

    print_header("$course->shortname: $book->name", $course->fullname, $navigation);

    //import files
    print_box_start('generalbox boxaligncenter centerpara');
    echo '<strong>'.get_string('importing', 'book').':</strong>';
    echo '<table cellpadding="2" cellspacing="2" border="1">';
    book_preload_chapters($book); // fix structure
    foreach($refs as $ref) {
        $chapter = book_read_chapter($coursebase, $ref);
        if ($chapter) {
            $chapter->bookid       = $book->id;
            $chapter->pagenum      = $DB->count_records('book_chapters', array('bookid'=>$book->id)+1);
            $chapter->timecreated  = time();
            $chapter->timemodified = time();
            echo "imsrc:".$chapter->importsrc;
            if (($data->subchapter) || preg_match('/_sub\.htm/i', $chapter->importsrc)) { //if filename or directory starts with sub_* treat as subdirecotories
                $chapter->subchapter = 1;
            } else {
                $chapter->subchapter = 0;
            }
            $chapter->id = $DB->insert_record('book_chapters', $chapter);

            add_to_log($course->id, 'course', 'update mod', '../mod/book/view.php?id='.$cm->id, 'book '.$book->id);
            add_to_log($course->id, 'book', 'update', 'view.php?id='.$cm->id.'&chapterid='.$chapter->id, $book->id, $cm->id);
        }
    }
    echo '</table><br />';
    echo '<strong>'.get_string('relinking', 'book').':</strong>';
    echo '<table cellpadding="2" cellspacing="2" border="1">';
    //relink whole book = all chapters
    book_relink($cm->id, $book->id, $course->id);
    echo '</table><br />';
    print_box_end();
    print_continue('view.php?id='.$cm->id);
    print_footer($course);
    die;

 */
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('importingchapters', 'mod_book'));

$mform->display();

echo $OUTPUT->footer();
