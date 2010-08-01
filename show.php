<?PHP

require('teacheraccess.php'); //page only for teachers

///switch hidden state
$chapter->hidden = $chapter->hidden ? 0 : 1;

///add slashes to all text fields
$chapter->content = addslashes($chapter->content);
$chapter->title = addslashes($chapter->title);
$chapter->importsrc = addslashes($chapter->importsrc);
if (!update_record('book_chapters', $chapter)) {
    error('Could not update your book');
}

///change visibility of subchapters too
if (!$chapter->subchapter) {
    $chapters = get_records('book_chapters', 'bookid', $book->id, 'pagenum', 'id, subchapter, hidden');
    $found = 0;
    foreach($chapters as $ch) {
        if ($ch->id == $chapter->id) {
            $found = 1;
        } else if ($found and $ch->subchapter) {
            $ch->hidden = $chapter->hidden;
            update_record('book_chapters', $ch);
        } else if ($found) {
            break;
        }
    }
}

add_to_log($course->id, 'course', 'update mod', '../mod/book/view.php?id='.$cm->id, 'book '.$book->id);
add_to_log($course->id, 'book', 'update', 'view.php?id='.$cm->id, $book->id, $cm->id);
book_check_structure($book->id);
redirect('view.php?id='.$cm->id.'&chapterid='.$chapter->id);
die;
