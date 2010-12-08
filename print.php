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
 * Book printing
 *
 * @package    mod
 * @subpackage book
 * @copyright  2004-2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/book/locallib.php');

$id        = required_param('id', PARAM_INT);           // Course Module ID
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID

// =========================================================================
// security checks START - teachers and students view
// =========================================================================

$cm = get_coursemodule_from_id('book', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$book = $DB->get_record('book', array('id'=>$cm->instance), '*', MUST_EXIST);

require_course_login($course, true, $cm);

$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/book:read', $context);
require_capability('mod/book:print', $context);

if ($book->disableprinting) {
    error('Printing is disabled');
}

//check all variables
if ($chapterid) {
    //single chapter printing - only visible!
    $chapter = $DB->get_record('book_chapters', array('id'=>$chapterid, 'bookid'=>$book->id, 'hidden'=>0), '*', MUST_EXIST);
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

if ($chapter) {
    add_to_log($course->id, 'book', 'print', 'print.php?id='.$cm->id.'&chapterid='.$chapter->id, $book->id, $cm->id);

    $chapters = $DB->get_records('book_chapters', array('bookid'=>$book->id, 'hidden'=>0), 'pagenum, title');

    $print = 0;
    $edit = 0;
    require('toc.php');

    /// page header
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <html>
    <head>
      <title><?php echo format_string($book->name) ?></title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="description" content="<?php echo s(format_string($book->name)) ?>" />
      <link rel="stylesheet" type="text/css" href="book_print.css" />
    </head>
    <body>
    <a name="top"></a>
    <div class="chapter">
    <?php

    if (!$book->customtitles) {
        if ($currsubtitle == '&nbsp;') {
            echo '<p class="book_chapter_title">'.$currtitle.'<p>';
        } else {
            echo '<p class="book_chapter_title">'.$currtitle.'<br />'.$currsubtitle.'</p>';
        }
    }
    $chaptertext = file_rewrite_pluginfile_urls($chapter->content, 'pluginfile.php', $context->id, 'mod_book', 'chapter', $chapter->id);
    echo format_text($chaptertext, $chapter->contentformat, array('noclean'=>true, 'context'=>$context));
    echo '</div>';
    echo '</body> </html>';

} else {
    add_to_log($course->id, 'book', 'print', 'print.php?id='.$cm->id, $book->id, $cm->id);
    $chapters = $DB->get_records('book_chapters', array('bookid'=>$book->id), 'pagenum');

    /// page header
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <html>
    <head>
      <title><?php echo format_string(name) ?></title>
      <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $encoding ?>" />
      <meta name="description" content="<?php echo s(format_string($book->name)) ?>" />
      <link rel="stylesheet" type="text/css" href="book_print.css" />
    </head>
    <body>
    <a name="top"></a>
    <p class="book_title"><?php echo format_string($book->name) ?></p>
    <p class="book_summary"><?php echo format_text($book->intro, $book->introformat) ?></p>
    <div class="book_info"><table>
    <tr>
    <td><?php echo get_string('site') ?>:</td>
    <td><a href="<?php echo $CFG->wwwroot ?>"><?php echo format_string($SITE->fullname) ?></a></td>
    </tr><tr>
    <td><?php echo get_string('course') ?>:</td>
    <td><?php echo format_string($course->fullname) ?></td>
    </tr><tr>
    <td><?php echo get_string('modulename', 'book') ?>:</td>
    <td><?php echo format_string($book->name) ?></td>
    </tr><tr>
    <td><?php echo get_string('printedby', 'book') ?>:</td>
    <td><?php echo format_string(fullname($USER, true)) ?></td>
    </tr><tr>
    <td><?php echo get_string('printdate','book') ?>:</td>
    <td><?php echo userdate(time()) ?></td>
    </tr>
    </table></div>

    <?php
    $print = 1;
    require('toc.php');
    echo $toc;
    // chapters
    $link1 = $CFG->wwwroot.'/mod/book/view.php?id='.$course->id.'&chapterid=';
    $link2 = $CFG->wwwroot.'/mod/book/view.php?id='.$course->id;
    foreach ($chapters as $ch) {
        echo '<div class="book_chapter"><a name="ch'.$ch->id.'"></a>';
        if (!$book->customtitles) {
            echo '<p class="book_chapter_title">'.$titles[$ch->id].'</p>';
        }
        $content = str_replace($link1, '#ch', $ch->content);
        $content = str_replace($link2, '#top', $content);
        $content = file_rewrite_pluginfile_urls($content, 'pluginfile.php', $context->id, 'mod_book', 'chapter', $ch->id);
        echo format_text($content, $ch->contentformat, array('noclean'=>true, 'context'=>$context));
        echo '</div>';
        //echo '<a href="#toc">'.$strtop.'</a>';
    }
    echo '</body> </html>';
}

