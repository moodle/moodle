<?PHP // $Id: view.php,v 1.2 2006/10/16 08:05:26 gustav_delius Exp $

require_once('../../config.php');
require_once('lib.php');

$id        = required_param('id', PARAM_INT);           // Course Module ID
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID
$edit      = optional_param('edit', -1, PARAM_BOOL);     // Edit mode

// =========================================================================
// security checks START - teachers edit; students view
// =========================================================================
if ($CFG->forcelogin) {
    require_login();
}

if (!$cm = get_record('course_modules', 'id', $id)) {
    error('Course Module ID is incorrect');
}

if (!$course = get_record('course', 'id', $cm->course)) {
    error('Course is misconfigured');
}

if ($course->category) {
    require_login($course->id);
}

if (!$book = get_record('book', 'id', $cm->instance)) {
    error('Course module is incorrect');
}

$isteacher = isteacheredit($course->id);
if ($isteacher) {
    if($edit != -1) {
        $USER->editing = $edit;
    } else {
        if (isset($USER->editing)) {
            $edit = $USER->editing;
        } else {
            $edit = 0;
        }
    }
} else {
    $edit = 0;
}

if (!$cm->visible and !$isteacher) {
    notice(get_string('activityiscurrentlyhidden'));
}

/// read chapters
$select = $isteacher ? "bookid = $book->id" : "bookid = $book->id AND hidden = 0";
$chapters = get_records_select('book_chapters', $select, 'pagenum', 'id, pagenum, subchapter, title, hidden');

if (!$chapters) {
    if ($isteacher) {
        redirect('edit.php?id='.$cm->id); //no chapters - add new one
        die;
    } else {
        error('Error reading book chapters.');
    }
}
/// check chapterid and read chapter data
if ($chapterid == '0') { // go to first chapter if no given
    foreach($chapters as $ch) {
        if ($isteacher) {
            $chapterid = $ch->id;
            break;
        }
        if (!$ch->hidden) {
            $chapterid = $ch->id;
            break;
        }
    }
}


if (!$chapter = get_record('book_chapters', 'id', $chapterid)) {
    error('Error reading book chapters.');
}

//check all variables
unset($id);
unset($chapterid);

/// chapter is hidden for students
if (!$isteacher and $chapter->hidden) {
    error('Error reading book chapters.');
}

/// chapter not part of this book!
if ($chapter->bookid != $book->id) {
    error('Chapter not part of this book!');
}
// =========================================================================
// security checks  END
// =========================================================================

add_to_log($course->id, 'book', 'view', 'view.php?id='.$cm->id.'&chapterid='.$chapter->id, $book->id, $cm->id);


///read standard strings
$strbooks = get_string('modulenameplural', 'book');
$strbook  = get_string('modulename', 'book');
$strTOC = get_string('TOC', 'book');

/// prepare header
if ($course->category) {
    $navigation = '<a href="../../course/view.php?id='.$course->id.'">'.$course->shortname.'</a> ->';
} else {
    $navigation = '';
}

$buttons = $isteacher ? '<table cellspacing="0" cellpadding="0"><tr><td>'.update_module_button($cm->id, $course->id, $strbook).'</td>'.
           '<td>&nbsp;</td><td>'.book_edit_button($cm->id, $course->id, $chapter->id).'</td></tr></table>'
           : '&nbsp;';

print_header( "$course->shortname: $book->name ($chapter->title)",
              $course->fullname,
              "$navigation <a href=\"index.php?id=$course->id\">$strbooks</a> -> $book->name",
              '',
              '<style type="text/css">@import url('.$CFG->wwwroot.'/mod/book/book_theme.css);</style>',
              true,
              $buttons,
              navmenu($course, $cm)
            );


/// prepare chapter navigation icons
$previd = null;
$nextid = null;
$found = 0;
foreach ($chapters as $ch) {
    if ($found) {
        $nextid= $ch->id;
        break;
    }
    if ($ch->id == $chapter->id) {
        $found = 1;
    }
    if (!$found) {
        $previd = $ch->id;
    }
}
if ($ch == current($chapters)) {
    $nextid = $ch->id;
}
$chnavigation = '';
if ($previd) {
    $chnavigation .= '<a title="'.get_string('navprev', 'book').'" href="view.php?id='.$cm->id.'&chapterid='.$previd.'"><img src="pix/nav_prev.gif" height="24" width="24" border="0"></img></a>';
} else {
    $chnavigation .= '<img src="pix/nav_prev_dis.gif" height="24" width="24" border="0"></img>';
}
$chnavigation .= '&nbsp;&nbsp;<img src="pix/nav_sep.gif" height="24" width="2" border="0"></img>&nbsp;&nbsp;';
if ($nextid) {
    $chnavigation .= '<a title="'.get_string('navnext', 'book').'" href="view.php?id='.$cm->id.'&chapterid='.$nextid.'"><img src="pix/nav_next.gif" height="24" width="24" border="0"></img></a>';
} else {
    $sec = '';
    if ($section = get_record('course_sections', 'id', $cm->section)) {
        $sec = $section->section;
    }
    $chnavigation .= '<a title="'.get_string('navexit', 'book').'" href="../../course/view.php?id='.$course->id.'#section-'.$sec.'"><img src="pix/nav_exit.gif" height="24" width="24" border="0"></img></a>';
}

/// prepare print icons
if ($book->disableprinting) {
    $printbook = '';
    $printchapter = '';
} else {
    $printbook = '<a title="'.get_string('printbook', 'book').'" href="print.php?id='.$cm->id.'" target="_blank"><img src="pix/print_book.gif" height="24" width="24" border="0"></img></a>';
    $printchapter = '<a title="'.get_string('printchapter', 'book').'" href="print.php?id='.$cm->id.'&chapterid='.$chapter->id.'" target="_blank"><img src="pix/print_chapter.gif" height="24" width="24" border="0"></img></a>';
}

// prepare $toc and $currtitle, $currsubtitle
require('toc.php');

if ($edit) {
    $tocwidth = $CFG->book_tocwidth + 80;
} else {
    $tocwidth = $CFG->book_tocwidth;
}

$doimport = ($isteacher and $edit) ? '<font size="-1"> (<a href="import.php?id='.$cm->id.'">'.get_string('doimport', 'book').'</a>)</font>' : '';


// =====================================================
// Book display HTML code
// =====================================================

?>
<table border="0" cellspacing="0" width="100%" valign="top" cellpadding="2">

<!-- subchapter title and upper navigation row //-->
<tr>
    <td width="<?php echo $tocwidth ?>" valign="bottom">
        <?php print_string('toc', 'book'); echo $doimport; ?>
    </td>
    <td valign="top">
        <table border="0" cellspacing="0" width="100%" valign="top" cellpadding="0">
        <tr>
            <td nowrap align="left"><?php echo $printbook.'&nbsp'.$printchapter ?></td>
            <td nowrap align="right"><?php echo $chnavigation ?></td>
        </tr>
        </table>
    </td>
</tr>

<!-- toc and chapter row //-->
<tr>
    <td width="<?php echo $tocwidth ?>" valign="top" align="left">
        <?php
        print_simple_box_start('middle', '100%');
        echo $toc;
        print_simple_box_end();
        if ($isteacher and $edit) {
            echo '<font size="1"><br />';
            helpbutton('faq', get_string('faq','book'), 'book', true, true);
            echo '</font>';
        }
        ?>
    </td>
    <td valign="top" align="right">
        <?php
        print_simple_box_start('middle', '100%');
        $content = '';
        if (!$book->customtitles) {
          if ($currsubtitle == '&nbsp;') {
              $content .= '<p class="book_chapter_title">'.$currtitle.'</p>';
          } else {
              $content .= '<p class="book_chapter_title">'.$currtitle.'<br />'.$currsubtitle.'</p>';
          }
        }
        $content .= $chapter->content;

        $nocleanoption = new object();
        $nocleanoption->noclean = true;
        echo '<div class="book_content">';
        echo format_text($content, FORMAT_HTML, $nocleanoption, $course->id);
        echo '</div>';
        print_simple_box_end();
        /// lower navigation
        echo '<p>'.$chnavigation.'<p>';
        ?>
    </td>
</tr>
</table>

<?php
print_footer($course);

?>
