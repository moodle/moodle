<?php

require_once('../../config.php');
require_once('lib.php');
require_once('edit_form.php');

$cmid       = required_param('cmid', PARAM_INT);  // Book Course Module ID
$chapterid  = optional_param('id', 0, PARAM_INT); // Chapter ID
$pagenum    = optional_param('pagenum', 0, PARAM_INT);
$subchapter = optional_param('subchapter', 0, PARAM_BOOL);

// =========================================================================
// security checks START - only teachers edit
// =========================================================================

if (!$cm = get_coursemodule_from_id('book', $cmid)) {
    error('Course Module ID was incorrect');
}

if (!$course = get_record('course', 'id', $cm->course)) {
    error('Course is misconfigured');
}

require_login($course, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/book:edit', $context);

if (!$book = get_record('book', 'id', $cm->instance)) {
    error('Course module is incorrect');
}

if ($chapterid) {
    if (!$chapter = get_record('book_chapters', 'id', $chapterid)) {
        error('Incorrect chapter id');
    }
    if ($chapter->bookid != $book->id) {//chapter id not in this book!!!!
        error('Chapter not part of this book!');
    }
} else {
    $chapter = null;
}

//check all variables
unset($cmid);
unset($chapterid);

// =========================================================================
// security checks END
// =========================================================================

$mform = new book_chapter_edit_form(null, $cm);

if ($chapter) {
    $chapter->cmid = $cm->id;
    $mform->set_data($chapter);
} else {
    $mform->set_data(array('cmid'=>$cm->id, 'pagenum'=>($pagenum+1), 'subchapter'=>$subchapter));
}

/// If data submitted, then process and store.
if ($mform->is_cancelled()) {
    if (empty($chapter->id)) {
        redirect("view.php?id=$cm->id");
    } else {
        redirect("view.php?id=$cm->id&chapterid=$chapter->id");
    }

} else if ($data = $mform->get_data(false)) {

    if ($data->id) {
        if (!update_record('book_chapters', addslashes_recursive($data))) {
            error('Could not update your book');
         }
        add_to_log($course->id, 'course', 'update mod', '../mod/book/view.php?id='.$cm->id, 'book '.$book->id);
        add_to_log($course->id, 'book', 'update', 'view.php?id='.$cm->id.'&chapterid='.$data->id, $book->id, $cm->id);

    } else {
        /// adding new chapter
        $data->bookid       = $book->id;
        $data->hidden       = 0;
        $data->timecreated  = time();
        $data->timemodified = time();
        $data->importsrc    = '';

        // make room for new page
        $sql = "UPDATE {$CFG->prefix}book_chapters
                   SET pagenum = pagenum + 1
                 WHERE bookid = $book->id AND pagenum >= $data->pagenum";
        execute_sql($sql, false);

        if (!$data->id = insert_record('book_chapters', addslashes_recursive($data))) {
            error('Could not insert a new chapter');
        }
        add_to_log($course->id, 'course', 'update mod', '../mod/book/view.php?id='.$cm->id, 'book '.$book->id);
        add_to_log($course->id, 'book', 'update', 'view.php?id='.$cm->id.'&chapterid='.$data->id, $book->id, $cm->id);
    }

    book_check_structure($book->id);
    redirect("view.php?id=$cm->id&chapterid=$data->id");
    die;
}

/// Otherwise fill and print the form.
$strbook = get_string('modulename', 'book');
$strbooks = get_string('modulenameplural', 'book');
$stredit = get_string('edit');
$pageheading = get_string('editingchapter', 'book');

///prepare the page header
$navlinks = array();
$navlinks[] = array('name' => $stredit, 'link' => '', 'type' => 'title');

$navigation = build_navigation($navlinks, $cm);

print_header("$course->shortname: $book->name", $course->fullname, $navigation);

$icon = '<img class="icon" src="icon_chapter.gif" alt="" />&nbsp;';
print_heading_with_help($pageheading, 'edit', 'book', $icon);

$mform->display();

print_footer($course);

