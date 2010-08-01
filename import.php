<?PHP

require('../../config.php');
require_once('lib.php');
require_once('import_form.php');

$id = required_param('id', PARAM_INT);           // Course Module ID

// =========================================================================
// security checks START - only teachers edit
// =========================================================================

if (!$cm = get_coursemodule_from_id('book', $id)) {
    error('Course Module ID was incorrect');
}

if (!$course = get_record('course', 'id', $cm->course)) {
    error('Course is misconfigured');
}

require_login($course, false, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/book:import', $context);

if (!$book = get_record('book', 'id', $cm->instance)) {
    error('Course module is incorrect');
}

//check all variables
unset($id);

// =========================================================================
// security checks END
// =========================================================================

///prepare the page header
$strbook = get_string('modulename', 'book');
$strbooks = get_string('modulenameplural', 'book');
$strimport = get_string('import', 'book');

$navlinks = array();
$navlinks[] = array('name' => $strimport, 'link' => '', 'type' => 'title');

$navigation = build_navigation($navlinks, $cm);

$mform = new book_import_form(null, $cm);
$mform->set_data(array('id'=>$cm->id));

/// If data submitted, then process and store.
if ($mform->is_cancelled()) {
    if (empty($chapter->id)) {
        redirect("view.php?id=$cm->id");
    } else {
        redirect("view.php?id=$cm->id&chapterid=$chapter->id");
    }

} else if ($data = $mform->get_data(false)) {
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
    book_check_structure($book->id);
    foreach($refs as $ref) {
        $chapter = book_read_chapter($coursebase, $ref);
        if ($chapter) {
            $chapter->title = addslashes($chapter->title);
            $chapter->content = addslashes($chapter->content);
            $chapter->importsrc = addslashes($chapter->importsrc);
            $chapter->bookid = $book->id;
            $chapter->pagenum = count_records('book_chapters', 'bookid', $book->id)+1;
            $chapter->timecreated = time();
            $chapter->timemodified = time();
            echo "imsrc:".$chapter->importsrc;
            if (($data->subchapter) || preg_match('/_sub\.htm/i', $chapter->importsrc)) { //if filename or directory starts with sub_* treat as subdirecotories
                $chapter->subchapter = 1;
            } else {
                $chapter->subchapter = 0;
            }
            if (!$chapter->id = insert_record('book_chapters', $chapter)) {
                error('Could not update your book');
            }
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
}

print_header("$course->shortname: $book->name", $course->fullname, $navigation);

$strdoimport = get_string('doimport', 'book');
$strchoose = get_string('choose');
$pageheading = get_string('importingchapters', 'book');

$icon = '<img class="icon" src="icon_chapter.gif" alt="" />&nbsp;';
print_heading_with_help($pageheading, 'import', 'book', $icon);

$mform->display();

print_footer($course);

