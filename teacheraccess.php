<?PHP

///standard routine to allow only teachers in
///check of $id and $chapterid parameters

require('../../config.php');
require_once('lib.php');

$id        = required_param('id', PARAM_INT);        // Course Module ID
$chapterid = required_param('chapterid', PARAM_INT); // Chapter ID

if (!confirm_sesskey()) {
    error(get_string('confirmsesskeybad', 'error'));
}
if (!$cm = get_coursemodule_from_id('book', $id)) {
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

if (!$chapter = get_record('book_chapters', 'id', $chapterid)) {
    error('Incorrect chapter ID');
}

if ($chapter->bookid != $book->id) {//chapter id not in this book!!!!
    error('Chapter not in this book!');
}

//check all variables
unset($id);
unset($chapterid);
