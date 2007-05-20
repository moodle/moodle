<?PHP // $Id: import.php,v 1.3 2007/05/20 06:00:29 skodak Exp $

require_once('../../config.php');
require_once('lib.php');

$id         = required_param('id', PARAM_INT);           // Course Module ID
$subchapter = optional_param('subchapter', 0, PARAM_BOOL);
$cancel     = optional_param('cancel', 0, PARAM_BOOL);

// =========================================================================
// security checks START - only teachers edit
// =========================================================================
require_login();

if (!$cm = get_coursemodule_from_id('book', $id)) {
    error('Course Module ID was incorrect');
}

if (!$course = get_record('course', 'id', $cm->course)) {
    error('Course is misconfigured');
}

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('moodle/course:manageactivities', $context);

if (!$book = get_record('book', 'id', $cm->instance)) {
    error('Course module is incorrect');
}

//check all variables
unset($id);

// =========================================================================
// security checks END
// =========================================================================

///cancel pressed, go back to book
if ($cancel) {
    redirect('view.php?id='.$cm->id);
    die;
}

///prepare the page header
$strbook = get_string('modulename', 'book');
$strbooks = get_string('modulenameplural', 'book');
$strimport = get_string('import', 'book');

if ($course->category) {
    $navigation = '<a href="../../course/view.php?id='.$course->id.'">'.$course->shortname.'</a> ->';
} else {
    $navigation = '';
}

print_header( "$course->shortname: $book->name",
              $course->fullname,
              "$navigation <a href=\"index.php?id=$course->id\">$strbooks</a> -> <a href=\"view.php?id=$cm->id\">$book->name</a> -> $strimport",
              '',
              '',
              true,
              '',
              ''
            );

/// If data submitted, then process, store and relink.
if (($form = data_submitted()) && (confirm_sesskey())) {
    $form->reference = stripslashes($form->reference);
    if ($form->reference != '') { //null path is root
        $form->reference = book_prepare_link($form->reference);
        if ($form->reference == '') { //evil characters in $ref!
            error('Invalid character detected in given path!');
        }
    }
    $coursebase = $CFG->dataroot.'/'.$book->course;
    if ($form->reference == '') {
        $base = $coursebase;
    } else {
        $base = $coursebase.'/'.$form->reference;
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
        $refs[] = '/'.$form->reference;
    } else { //what is it???
        error('Incorrect file/directory specified!');
    }

    //import files
    echo '<center>';
    echo '<b>'.get_string('importing', 'book').':</b>';
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
            if (($subchapter) || preg_match('/_sub\.htm/i', $chapter->importsrc)) { //if filename or directory starts with sub_* treat as subdirecotories
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
    echo '<b>'.get_string('relinking', 'book').':</b>';
    echo '<table cellpadding="2" cellspacing="2" border="1">';
    //relink whole book = all chapters
    book_relink($cm->id, $book->id, $course->id);
    echo '</table><br />';
    echo '<a href="view.php?id='.$cm->id.'">'.get_string('continue').'</a>';
    echo '</center>';
} else {
/// Otherwise fill and print the form.
    $strdoimport = get_string('doimport', 'book');
    $strchoose = get_string('choose');
    $pageheading = get_string('importingchapters', 'book');

    $icon = '<img align="absmiddle" height="16" width="16" src="icon_chapter.gif" />&nbsp;';
    print_heading_with_help($pageheading, 'import', 'book', $icon);
    print_simple_box_start('center', '');
    ?>
    <form name="theform" method="post" action="import.php">
    <table cellpadding="5" align="center">
    <tr valign="top">
        <td valign="top" align="right">
            <b><?php print_string('fileordir', 'book') ?>:</b>
        </td>
        <td>
            <?php
              echo '<input id="id_reference" name="reference" size="40" value="" />&nbsp;';
              button_to_popup_window ('/mod/book/coursefiles.php?choose=id_reference&id='.$course->id,
                                      'coursefiles', $strchoose, 500, 750, $strchoose);
            ?>
        </td>
    </tr>
    <tr valign="top">
        <td valign="top" align="right">
            <b><?php print_string('subchapter', 'book') ?>:</b>
        </td>
        <td>
        <?php
            echo '<input name="subchapter" type="checkbox" value="1" />';
        ?>
        </td>
    </tr>
    <tr valign="top">
        <td valign="top" align="right">&nbsp;</td>
        <td><p><?php print_string('importinfo', 'book') ?></p></td>
    </tr>
    </table>
    <center>
        <input type="submit" value="<?php echo $strdoimport ?>" />
        <input type="submit" name="cancel" value="<?php print_string("cancel") ?>" />
    </center>
        <input type="hidden" name="id" value="<?php p($cm->id) ?>" />
        <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey ?>" /> 
    </form>

    <?php
    print_simple_box_end();
}

print_footer($course);

?>
