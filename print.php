<?PHP // $Id: print.php,v 1.2 2007/05/20 06:00:26 skodak Exp $

require_once('../../config.php');
require_once('lib.php');

$id        = required_param('id', PARAM_INT);           // Course Module ID
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID

// =========================================================================
// security checks START - teachers and students view
// =========================================================================
if (!$cm = get_coursemodule_from_id('book', $id)) {
    error('Course Module ID was incorrect');
}

if (!$course = get_record('course', 'id', $cm->course)) {
    error('Course is misconfigured');
}

require_course_login($course, true, $cm);
 
if (!$book = get_record('book', 'id', $cm->instance)) {
    error('Course module is incorrect');
}

if ($book->disableprinting) {
    error('Printing is disabled');
}

//check all variables
if ($chapterid) {
    //single chapter printing
    if (!$chapter = get_record('book_chapters', 'id', $chapterid)) {
        error('Incorrect chapter ID');
    }
    if ($chapter->bookid != $book->id) {//chapter id not in this book!!!!
        error('Chapter not in this book!');
    }
    if ($chapter->hidden) {
        error('Only visible chapters can be printed');
    }
} else {
    //complete book
    $chapter = false;
}
unset($id);
unset($chapterid);
// =========================================================================
// security checks END
// =========================================================================

$strbooks = get_string('modulenameplural', 'book');
$strbook  = get_string('modulename', 'book');
$strtop   = get_string('top', 'book');

@header('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
@header('Pragma: no-cache');
@header('Expires: ');          
@header('Accept-Ranges: none');
@header('Content-type: text/html; charset=utf-8');

$formatoptions = new object();
$formatoptions->noclean = true;

if ($chapter) {
    add_to_log($course->id, 'book', 'print', 'print.php?id='.$cm->id.'&chapterid='.$chapter->id, $book->id, $cm->id);

    $chapters = get_records('book_chapters', 'bookid', $book->id, 'pagenum, title');

    $print = 0;
    $edit = 0;
    require('toc.php');

    /// page header
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <html>
    <head>
      <title><?PHP echo format_string($book->name) ?></title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="description" content="<?PHP echo s(format_string($book->name)) ?>" />
      <link rel="stylesheet" type="text/css" href="book_print.css" />
    </head>
    <body>
    <a name="top"></a>
    <div class="chapter">
    <?PHP

    if (!$book->customtitles) {
        if ($currsubtitle == '&nbsp;') {
            echo '<p class="book_chapter_title">'.$currtitle.'<p>';
        } else {
            echo '<p class="book_chapter_title">'.$currtitle.'<br />'.$currsubtitle.'</p>';
        }
    }
    echo format_text($chapter->content, FORMAT_HTML, $formatoptions, $course->id);
    echo '</div>';
    echo '</body> </html>';

} else {
    add_to_log($course->id, 'book', 'print', 'print.php?id='.$cm->id, $book->id, $cm->id);
    $site = get_record('course','id',1);
    $chapters = get_records('book_chapters', 'bookid', $book->id, 'pagenum');

    /// page header
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <html>
    <head>
      <title><?PHP echo format_string(name) ?></title>
      <meta http-equiv="Content-Type" content="text/html; charset=<?PHP echo $encoding ?>" />
      <meta name="description" content="<?PHP echo s(format_string($book->name)) ?>" />
      <link rel="stylesheet" type="text/css" href="book_print.css" />
    </head>
    <body>
    <a name="top"></a>
    <p class="book_title"><?PHP echo format_string($book->name) ?></p>
    <p class="book_summary"><?PHP echo format_string($book->summary) ?></p>
    <div class="book_info"><table>
    <tr>
    <td><?PHP echo get_string('site') ?>:</td>
    <td><a href="<?PHP echo $CFG->wwwroot ?>"><?PHP echo format_string($site->fullname) ?></a></td>
    </tr><tr>
    <td><?PHP echo get_string('course') ?>:</td>
    <td><?PHP echo format_string($course->fullname) ?></td>
    </tr><tr>
    <td><?PHP echo get_string('modulename', 'book') ?>:</td>
    <td><?PHP echo format_string($book->name) ?></td>
    </tr><tr>
    <td><?PHP echo get_string('printedby', 'book') ?>:</td>
    <td><?PHP echo format_string(fullname($USER, true)) ?></td>
    </tr><tr>
    <td><?PHP echo get_string('printdate','book') ?>:</td>
    <td><?PHP echo userdate(time()) ?></td>
    </tr>
    </table></div>

    <?PHP
    $print = 1;
    require('toc.php');
    echo $toc;
    // chapters
    $link1 = $CFG->wwwroot.'/mod/book/view.php?id='.$course->id.'&chapterid=';
    $link2 = $CFG->wwwroot.'/mod/book/view.php?id='.$course->id;
    foreach ($chapters as $ch) {
        if (!$ch->hidden) {
            echo '<div class="book_chapter"><a name="ch'.$ch->id.'"></a>';
            if (!$book->customtitles) {
                echo '<p class="book_chapter_title">'.$titles[$ch->id].'</p>';
            }
            $content = str_replace($link1, '#ch', $ch->content);
            $content = str_replace($link2, '#top', $content);
            echo format_text($content, FORMAT_HTML, $formatoptions, $course->id);
            echo '</div>';
            //echo '<a href="#toc">'.$strtop.'</a>';

        }
    }
    echo '</body> </html>';
}

?>
