<?PHP

require('teacheraccess.php'); //page only for teachers
$confirm = optional_param('confirm', 0, PARAM_BOOL);


///header and strings
$strbooks = get_string('modulenameplural', 'book');
$strbook  = get_string('modulename', 'book');

$navigation = build_navigation('', $cm);

print_header("$course->shortname: $book->name", $course->fullname, $navigation);

///form processing
if ($confirm) {  // the operation was confirmed.
    if (!$chapter->subchapter) { //delete all its subchapters if any
        $chapters = get_records('book_chapters', 'bookid', $book->id, 'pagenum', 'id, subchapter');
        $found = false;
        foreach($chapters as $ch) {
            if ($ch->id == $chapter->id) {
                $found = true;
            } else if ($found and $ch->subchapter) {
                if (!delete_records('book_chapters', 'id', $ch->id)) {
                    error('Could not update your book');
                }
            } else if ($found) {
                break;
            }
        }
    }
    if (!delete_records('book_chapters', 'id', $chapter->id)) {
        error('Could not update your book');
    }

    add_to_log($course->id, 'course', 'update mod', '../mod/book/view.php?id='.$cm->id, 'book '.$book->id);
    add_to_log($course->id, 'book', 'update', 'view.php?id='.$cm->id, $book->id, $cm->id);
    book_check_structure($book->id);
    redirect('view.php?id='.$cm->id);
    die;
} else {
    // the operation has not been confirmed yet so ask the user to do so
    if ($chapter->subchapter) {
        $strconfirm = get_string('confchapterdelete','book');
    } else {
        $strconfirm = get_string('confchapterdeleteall','book');
    }
    echo '<br />';
    notice_yesno("<b>$chapter->title</b><p>$strconfirm</p>",
                  "delete.php?id=$cm->id&chapterid=$chapter->id&confirm=1&sesskey=$USER->sesskey",
                  "view.php?id=$cm->id&chapterid=$chapter->id");
}

print_footer($course);

