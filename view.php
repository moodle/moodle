<?php

require('../../config.php');
require_once('lib.php');

$id        = required_param('id', PARAM_INT);           // Course Module ID
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID
$edit      = optional_param('edit', -1, PARAM_BOOL);    // Edit mode

// =========================================================================
// security checks START - teachers edit; students view
// =========================================================================
if (!$cm = get_coursemodule_from_id('book', $id)) {
    error('Course Module ID was incorrect');
}

if (!$course = get_record('course', 'id', $cm->course)) {
    error('Course is misconfigured');
}

if (!$book = get_record('book', 'id', $cm->instance)) {
    error('Course module is incorrect');
}

require_course_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/book:read', $context);

$allowedit   = has_capability('mod/book:edit', $context);
$allowimport = has_capability('mod/book:import', $context);
$allowprint  = has_capability('mod/book:print', $context) and !$book->disableprinting;
$allowexport = has_capability('mod/book:exportimscp', $context);
$viewhidden  = has_capability('mod/book:viewhiddenchapters', $context);

if ($allowedit) {
    if ($edit != -1) {
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

/// read chapters
$select = $viewhidden ? "bookid = $book->id" : "bookid = $book->id AND hidden = 0";
$chapters = get_records_select('book_chapters', $select, 'pagenum', 'id, pagenum, subchapter, title, hidden');

if (!$chapters) {
    if ($allowedit) {
        redirect('edit.php?cmid='.$cm->id); //no chapters - add new one
        die;
    } else {
        error('Error reading book chapters.');
    }
}
/// check chapterid and read chapter data
if ($chapterid == '0') { // go to first chapter if no given
    foreach($chapters as $ch) {
        if ($allowedit) {
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
if (!$viewhidden and $chapter->hidden) {
    error('Error reading book chapters.');
}

/// chapter not part of this book!
if ($chapter->bookid != $book->id) {
    error('Chapter not part of this book!');
}
// =========================================================================
// security checks  END
// =========================================================================

add_to_log($course->id, 'book', 'view', 'view.php?id='.$cm->id.'&amp;chapterid='.$chapter->id, $book->id, $cm->id);


///read standard strings
$strbooks = get_string('modulenameplural', 'book');
$strbook  = get_string('modulename', 'book');
$strTOC = get_string('TOC', 'book');

/// prepare header
$navigation = build_navigation('', $cm);

$buttons = $allowedit ? '<table cellspacing="0" cellpadding="0"><tr><td>'.update_module_button($cm->id, $course->id, $strbook).'</td>'.
           '<td>&nbsp;</td><td>'.book_edit_button($cm->id, $course->id, $chapter->id).'</td></tr></table>'
           : '&nbsp;';

print_header( "$course->shortname: $book->name ($chapter->title)",
              $course->fullname,
              $navigation,
              '',
              '',
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
    $chnavigation .= '<a title="'.get_string('navprev', 'book').'" href="view.php?id='.$cm->id.'&amp;chapterid='.$previd.'"><img src="pix/nav_prev.gif" class="bigicon" alt="'.get_string('navprev', 'book').'"/></a>';
} else {
    $chnavigation .= '<img src="pix/nav_prev_dis.gif" class="bigicon" alt="" />';
}
if ($nextid) {
    $chnavigation .= '<a title="'.get_string('navnext', 'book').'" href="view.php?id='.$cm->id.'&amp;chapterid='.$nextid.'"><img src="pix/nav_next.gif" class="bigicon" alt="'.get_string('navnext', 'book').'" /></a>';
} else {
    $sec = '';
    if ($section = get_record('course_sections', 'id', $cm->section)) {
        $sec = $section->section;
    }
    $chnavigation .= '<a title="'.get_string('navexit', 'book').'" href="../../course/view.php?id='.$course->id.'#section-'.$sec.'"><img src="pix/nav_exit.gif" class="bigicon" alt="'.get_string('navexit', 'book').'" /></a>';
}

/// prepare print icons
if (!$allowprint) {
    $printbook = '';
    $printchapter = '';
} else {
    $printbook = '<a title="'.get_string('printbook', 'book').'" href="print.php?id='.$cm->id.'" onclick="this.target=\'_blank\'"><img src="pix/print_book.gif" class="bigicon" alt="'.get_string('printbook', 'book').'"/></a>';
    $printchapter = '<a title="'.get_string('printchapter', 'book').'" href="print.php?id='.$cm->id.'&amp;chapterid='.$chapter->id.'" onclick="this.target=\'_blank\'"><img src="pix/print_chapter.gif" class="bigicon" alt="'.get_string('printchapter', 'book').'"/></a>';
}

// prepare $toc and $currtitle, $currsubtitle
$print = 0;
require('toc.php');

if ($edit) {
    $tocwidth = $CFG->book_tocwidth + 80;
} else {
    $tocwidth = $CFG->book_tocwidth;
}

$doimport = ($allowimport and $edit) ? '<div>(<a href="import.php?id='.$cm->id.'">'.get_string('doimport', 'book').'</a>)</div>' : '';

/// Enable the IMS CP button
$generateimscp = ($allowexport) ? '<a title="'.get_string('generateimscp', 'book').'" href="generateimscp.php?id='.$cm->id.'"><img class="bigicon" src="pix/generateimscp.gif" alt="'.get_string('generateimscp', 'book').'"></img></a>' : '';


// =====================================================
// Book display HTML code
// =====================================================

?>
<table class="booktable" width="100%" cellspacing="0" cellpadding="2">

<!-- subchapter title and upper navigation row //-->
<tr>
    <td style="width:<?php echo $tocwidth ?>px" valign="bottom">
        <?php
        print_string('toc', 'book');
        echo $doimport;
        ?>
    </td>
    <td>
        <div class="bookexport"><?php echo $printbook.$printchapter.$generateimscp ?></div>
        <div class="booknav"><?php echo $chnavigation ?></div>
    </td>
</tr>

<!-- toc and chapter row //-->
<tr class="tocandchapter">
    <td style="width:<?php echo $tocwidth ?>px" align="left"><div class="clearer">&nbsp;</div>
        <?php
        print_box_start('generalbox');
        echo $toc;
        print_box_end();
        if ($allowedit and $edit) {
            echo '<div class="faq">';
            helpbutton('faq', get_string('faq','book'), 'book', true, true);
            echo '</div>';
        }
        ?>
    </td>
    <td align="right"><div class="clearer">&nbsp;</div>
        <?php
        print_box_start('generalbox');
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
        print_box_end();
        /// lower navigation
        echo '<div class="booknav">'.$chnavigation.'</div>';
        ?>
    </td>
</tr>
</table>

<?php
print_footer($course);

