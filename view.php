<?php
// This file is part of Book module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Book view page
 *
 * @package    mod
 * @subpackage book
 * @copyright  2004-2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/mod/book/locallib.php');

$id        = optional_param('id', 0, PARAM_INT);        // Course Module ID
$bid       = optional_param('b', 0, PARAM_INT);         // Book id
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID
$edit      = optional_param('edit', -1, PARAM_BOOL);    // Edit mode

// =========================================================================
// security checks START - teachers edit; students view
// =========================================================================
if ($id) {
    $cm = get_coursemodule_from_id('book', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $book = $DB->get_record('book', array('id'=>$cm->instance), '*', MUST_EXIST);
} else {
    $book = $DB->get_record('book', array('id'=>$bid), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('book', $book->id, 0, false, MU<ST_EXIST);
    $course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
    $id = $cm->id;
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
    if ($edit != -1 and confirm_sesskey()) {
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
$select = $viewhidden ? array('bookid' => $book->id) : array('bookid' => $book->id, 'hidden' => 0);
$chapters = $DB->get_records('book_chapters', $select, 'pagenum', 'id, pagenum, subchapter, title, hidden');

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

$PAGE->set_url('/mod/book/view.php', array('id'=>$id, 'chapterid'=>$chapterid));

if (!$chapter = $DB->get_record('book_chapters', array('id'=>$chapterid, 'bookid'=>$book->id))) {
    error('Error reading book chapters.');
}

//check all variables
unset($id);
unset($bid);
unset($chapterid);

/// chapter is hidden for students
if (!$viewhidden and $chapter->hidden) {
    error('Error reading book chapters.');
}

// =========================================================================
// security checks  END
// =========================================================================

add_to_log($course->id, 'book', 'view', 'view.php?id='.$cm->id.'&amp;chapterid='.$chapter->id, $book->id, $cm->id);


///read standard strings
$strbooks = get_string('modulenameplural', 'book');
$strbook  = get_string('modulename', 'book');
$strtoc   = get_string('toc', 'book');

/// prepare header
$PAGE->set_title(format_string($book->name));
$PAGE->add_body_class('mod_book');
$PAGE->set_heading(format_string($course->fullname));
echo $OUTPUT->header();

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
    $chnavigation .= '<a title="'.get_string('navprev', 'book').'" href="view.php?id='.$cm->id.'&amp;chapterid='.$previd.'"><img src="'.$OUTPUT->pix_url('nav_prev', 'mod_book').'" class="bigicon" alt="'.get_string('navprev', 'book').'"/></a>';
} else {
    $chnavigation .= '<img src="pix/nav_prev_dis.gif" class="bigicon" alt="" />';
}
if ($nextid) {
    $chnavigation .= '<a title="'.get_string('navnext', 'book').'" href="view.php?id='.$cm->id.'&amp;chapterid='.$nextid.'"><img src="'.$OUTPUT->pix_url('nav_next', 'mod_book').'" class="bigicon" alt="'.get_string('navnext', 'book').'" /></a>';
} else {
    $sec = '';
    if ($section = $DB->get_record('course_sections', array('id'=>$cm->section))) {
        $sec = $section->section;
    }
    $chnavigation .= '<a title="'.get_string('navexit', 'book').'" href="../../course/view.php?id='.$course->id.'#section-'.$sec.'"><img src="'.$OUTPUT->pix_url('nav_exit', 'mod_book').'" class="bigicon" alt="'.get_string('navexit', 'book').'" /></a>';
}

/// prepare print icons
if (!$allowprint) {
    $printbook = '';
    $printchapter = '';
} else {
    $printbook = '<a title="'.get_string('printbook', 'book').'" href="print.php?id='.$cm->id.'" onclick="this.target=\'_blank\'"><img src="'.$OUTPUT->pix_url('print_book', 'mod_book').'" class="bigicon" alt="'.get_string('printbook', 'book').'"/></a>';
    $printchapter = '<a title="'.get_string('printchapter', 'book').'" href="print.php?id='.$cm->id.'&amp;chapterid='.$chapter->id.'" onclick="this.target=\'_blank\'"><img src="'.$OUTPUT->pix_url('print_chapter', 'mod_book').'" class="bigicon" alt="'.get_string('printchapter', 'book').'"/></a>';
}

// prepare $toc and $currtitle, $currsubtitle
$print = 0;
include('toc.php');

if ($edit) {
    $tocwidth = $CFG->book_tocwidth + 80;
} else {
    $tocwidth = $CFG->book_tocwidth;
}

//$doimport = ($allowimport and $edit) ? '<div>(<a href="import.php?id='.$cm->id.'">'.get_string('doimport', 'book').'</a>)</div>' : '';
$doimport = ''; //TODO: after new file handling

/// Enable the IMS CP button
//$generateimscp = ($allowexport) ? '<a title="'.get_string('generateimscp', 'book').'" href="generateimscp.php?id='.$cm->id.'"><img class="bigicon" src="'.$OUTPUT->pix_url('generateimscp', 'mod_book').'" alt="'.get_string('generateimscp', 'book').'"></img></a>' : '';
$generateimscp = ''; //TODO after new file handling


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
<tr class="tocandchapter" valign="top">
    <td style="width:<?php echo $tocwidth ?>px" align="left"><div class="clearer">&nbsp;</div>
        <?php
        echo $OUTPUT->box_start('generalbox');
        echo $toc;
        echo $OUTPUT->box_end();
        if ($allowedit and $edit) {
            echo '<div class="faq">';
            echo $OUTPUT->help_icon('faq', 'mod_book', get_string('faq', 'mod_book'));
            echo '</div>';
        }
        ?>
    </td>
    <td align="right" valign="top"><div class="clearer">&nbsp;</div>
        <?php
        echo $OUTPUT->box_start('generalbox');
        echo '<div class="book_content">';
        if (!$book->customtitles) {
          if ($currsubtitle == '&nbsp;') {
              echo '<p class="book_chapter_title">'.$currtitle.'</p>';
          } else {
              echo '<p class="book_chapter_title">'.$currtitle.'<br />'.$currsubtitle.'</p>';
          }
        }
        $chaptertext = file_rewrite_pluginfile_urls($chapter->content, 'pluginfile.php', $context->id, 'mod_book', 'chapter', $chapter->id);
        echo format_text($chaptertext, $chapter->contentformat, array('noclean'=>true, 'context'=>$context));
        echo '</div>';
        echo $OUTPUT->box_end();
        /// lower navigation
        echo '<div class="booknav">'.$chnavigation.'</div>';
        ?>
    </td>
</tr>
</table>

<?php

echo $OUTPUT->footer();

