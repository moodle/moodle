<?PHP // $Id: index.php,v 1.2 2007/05/20 06:00:29 skodak Exp $

/// This page lists all the instances of book in a particular course

require_once('../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);           // Course Module ID

// =========================================================================
// security checks START - teachers and students view
// =========================================================================

if (!$course = get_record('course', 'id', $id)) {
    error('Course ID is incorrect');
}

require_course_login($course, true);

//check all variables
unset($id);

// =========================================================================
// security checks END
// =========================================================================

/// Get all required strings
$strbooks = get_string('modulenameplural', 'book');
$strbook  = get_string('modulename', 'book');

/// Print the header
if ($course->category) {
    $navigation = '<a href="../../course/view.php?id='.$course->id.'">'.$course->shortname.'</a> ->';
} else {
    $navigation = '';
}

print_header( "$course->shortname: $strbooks",
               $course->fullname,
               "$navigation $strbooks",
               '',
               '',
               true,
               '',
               navmenu($course)
             );

add_to_log($course->id, 'book', 'view all', 'index.php?id='.$course->id, '');

/// Get all the appropriate data
if (!$books = get_all_instances_in_course('book', $course)) {
    notice('There are no books', '../../course/view.php?id='.$course->id);
    die;
}

/// Print the list of instances
$strname  = get_string('name');
$strweek  = get_string('week');
$strtopic  = get_string('topic');
$strsummary = get_string('summary');
$strchapters  = get_string('chapterscount', 'book');

if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $strname, $strsummary, $strchapters);
    $table->align = array ('center', 'left', 'left', 'center');
} else if ($course->format == 'topics') {
    $table->head  = array ($strtopic, $strname, $strsummary, $strchapters);
    $table->align = array ('center', 'left', 'left', 'center');
} else {
    $table->head  = array ($strname, $strsummary, $strchapters);
    $table->align = array ('left', 'left', 'left');
}

$currentsection = '';
foreach ($books as $book) {
    $nocleanoption = new object();
    $nocleanoption->noclean = true;
    $book->summary = format_text($book->summary, FORMAT_HTML, $nocleanoption, $course->id);
    $book->summary = '<span style="font-size:x-small;">'.$book->summary.'</span>';

    if (!$book->visible) {
        //Show dimmed if the mod is hidden
        $link = '<a class="dimmed" href="view.php?id='.$book->coursemodule.'">'.$book->name.'</a>';
    } else {
        //Show normal if the mod is visible
        $link = '<a href="view.php?id='.$book->coursemodule.'">'.$book->name.'</a>';
    }

    $count = count_records('book_chapters', 'bookid', $book->id, 'hidden', '0');

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $printsection = '';
        if ($book->section !== $currentsection) {
            if ($book->section) {
                $printsection = $book->section;
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $book->section;
        }
        $table->data[] = array ($printsection, $link, $book->summary, $count);
    } else {
        $table->data[] = array ($link, $book->summary, $count);
    }
}

echo '<br />';
print_table($table);

print_footer($course);

?>
