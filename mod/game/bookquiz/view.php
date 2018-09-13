<?php
// This file is part of Moodle - http://moodle.org/
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
 * View a bookquiz.
 *
 * @package mod_game
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);                  // Course Module ID.
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID.
$edit = optional_param('edit', -1, PARAM_BOOL);         // Edit mode.

/* =========================================================================
 * security checks START - teachers edit; students view
 * =========================================================================
 */
if (!$cm = get_coursemodule_from_id('book', $id)) {
    print_error('Course Module ID was incorrect');
}

if (!$course = $DB->get_record('course', array( 'id' => $cm->course))) {
    print_error('Course is misconfigured');
}

if (!$book = $DB->get_record('book', array( 'id' => $cm->instance))) {
    print_error('Course module is incorrect');
}

require_course_login($course, true, $cm);

$context = game_get_context_module_instance( $cm->id);

// Read chapters.
$select = $allowedit ? "bookid = $book->id" : "bookid = $book->id AND hidden = 0";
$chapters = $DB->get_records_select('book_chapters', $select, null, 'pagenum', 'id, pagenum, subchapter, title, hidden');

// Check chapterid and read chapter data.
if ($chapterid == '0') {
    // Go to first chapter if no given.
    foreach ($chapters as $ch) {
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

if (!$chapter = $DB->get_record('book_chapters', array('id' => $chapterid))) {
    print_error('Error reading book chapters.');
}

// Check all variables.
unset($id);
unset($chapterid);

// Chapter is hidden for students.
if (!$allowedit && $chapter->hidden) {
    print_error('Error reading book chapters.');
}

// Chapter not part of this book!
if ($chapter->bookid != $book->id) {
    print_error('Chapter not part of this book!');
}
/* =========================================================================
 * security checks  END
 * =========================================================================
 */
add_to_log($course->id, 'book', 'view', 'view.php?id='.$cm->id.'&amp;chapterid='.$chapter->id, $book->id, $cm->id);

// Read standard strings.
$strbooks = get_string('modulenameplural', 'book');
$strbook  = get_string('modulename', 'book');

// Prepare header.
if ($course->category) {
    $navigation = '<a href="../../course/view.php?id='.$course->id.'">'.$course->shortname.'</a> ->';
} else {
    $navigation = '';
}

$buttons = $allowedit ? '<table cellspacing="0" cellpadding="0"><tr><td>'.
        update_module_button($cm->id, $course->id, $strbook).'</td>'.
        '<td>&nbsp;</td><td>'.book_edit_button($cm->id, $course->id, $chapter->id).'</td></tr></table>' : '&nbsp;';

// Prepare chapter navigation icons.
$previd = null;
$nextid = null;
$found = 0;
foreach ($chapters as $ch) {
    if ($found) {
        $nextid = $ch->id;
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
echo "previd=$previd nextid=$nextid<br>";

if ($previd) {
    $chnavigation .= '<a title="'.get_string('navprev', 'book').'" href="view.php?id='.$cm->id.
        '&amp;chapterid='.$previd.'"><img src="'.$OUTPUT->pix_url('bookquiz/nav_prev', 'mod_game').
        '" class="bigicon" alt="'.get_string('navprev', 'book').'"/></a>';
} else {
    $chnavigation .= '<img src="'.$OUTPUT->pix_url('bookquiz/nav_prev_dis', 'mod_game').'" class="bigicon" alt="" />';
}

if ($nextid) {
    $chnavigation .= '<a title="'.get_string('navnext', 'book').'" href="view.php?id='.$cm->id.
        '&amp;chapterid='.$nextid.'"><img src="'.$OUTPUT->pix_url('bookquiz/nav_next', 'mod_game').
        '" class="bigicon" alt="'.get_string('navnext', 'book').'" /></a>';
} else {
    $sec = '';
    if ($section = $DB->get_record('course_sections', array( 'id' => $cm->section))) {
        $sec = $section->section;
    }
    $chnavigation .= '<a title="'.get_string('navexit', 'book').'" href="../../course/view.php?id='.
        $course->id.'#section-'.$sec.'"><img src="'.
        $OUTPUT->pix_url('bookquiz/nav_exit', 'mod_game').'" class="bigicon" alt="'.get_string('navexit', 'book').
        '" /></a>';
}

echo "chnavigation=$chnavigation<br>";

// Prepare print icons.
if ($book->disableprinting) {
    $printbook = '';
    $printchapter = '';
} else {
    $printbook = '<a title="'.get_string('printbook', 'book').'" href="print.php?id='.$cm->id.
        '" onclick="this.target=\'_blank\'"><img src="'.
        $OUTPUT->pix_url('bookquiz/print_book', 'mod_game').
        '" class="bigicon" alt="'.get_string('printbook', 'book').'"/></a>';
    $printchapter = '<a title="'.get_string('printchapter', 'book').'" href="print.php?id='.
        $cm->id.'&amp;chapterid='.$chapter->id.
        '" onclick="this.target=\'_blank\'"><img src="'.
        $OUTPUT->pix_url('bookquiz/print_chapter', 'mod_game').'" class="bigicon" alt="'.
        get_string('printchapter', 'book').'"/></a>';
}


/* =====================================================
 * Book display HTML code
 * =====================================================
 */
echo "OK";
?>
<table border="0" cellspacing="0" width="100%" valign="top" cellpadding="2">

<!-- subchapter title and upper navigation row //-->
<tr>
    <td width="<?php echo  10;?>" valign="bottom">
    </td>
    <td valign="top">
        <table border="0" cellspacing="0" width="100%" valign="top" cellpadding="0">
        <tr>
            <td align="right"><?php echo 'help'.$chnavigation ?></td>
        </tr>
        </table>
    </td>
</tr>

<!-- toc and chapter row //-->
<tr>
    <td width="<?php echo $tocwidth ?>" valign="top" align="left">
<?php
echo $OUTPUT->box_start('generalbox');
echo $toc;
echo $OUTPUT->box_end();
if ($allowedit && $edit) {
    echo '<font size="1"><br />';
    helpbutton('faq', get_string('faq', 'book'), 'book', true, true);
    echo '</font>';
}
?>
    </td>
    <td valign="top" align="right">
<?php
echo $OUTPUT->box_start('generalbox');
$content = '';
if (!$book->customtitles) {
    if ($currsubtitle == '&nbsp;') {
        $content .= '<p class="book_chapter_title">'.$currtitle.'</p>';
    } else {
        $content .= '<p class="book_chapter_title">'.$currtitle.'<br />'.$currsubtitle.'</p>';
    }
}

$content .= $chapter->content;

$nocleanoption = new stdClass;
$nocleanoption->noclean = true;
echo '<div class="book_content">';
echo format_text($content, FORMAT_HTML, $nocleanoption, $course->id);
echo '</div>';
echo $OUTPUT->box_end();
// Lower navigation.
echo '<p>'.$chnavigation.'</p>';
?>
    </td>
</tr>
</table>

<?php
echo $OUTPUT->footer($course);
