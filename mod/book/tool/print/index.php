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
 * Book printing
 *
 * @package    booktool_print
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__).'/../../../../config.php');
require_once(dirname(__FILE__).'/locallib.php');

$id        = required_param('id', PARAM_INT);           // Course Module ID
$chapterid = optional_param('chapterid', 0, PARAM_INT); // Chapter ID

// =========================================================================
// security checks START - teachers and students view
// =========================================================================

$cm = get_coursemodule_from_id('book', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
$book = $DB->get_record('book', array('id'=>$cm->instance), '*', MUST_EXIST);

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/book:read', $context);
require_capability('booktool/print:print', $context);

// Check all variables.
if ($chapterid) {
    // Single chapter printing - only visible!
    $chapter = $DB->get_record('book_chapters', array('id'=>$chapterid, 'bookid'=>$book->id), '*', MUST_EXIST);
} else {
    // Complete book.
    $chapter = false;
}

$PAGE->set_url('/mod/book/print.php', array('id'=>$id, 'chapterid'=>$chapterid));

unset($id);
unset($chapterid);

// Security checks END.

// read chapters
$chapters = book_preload_chapters($book);

$strbooks = get_string('modulenameplural', 'mod_book');
$strbook  = get_string('modulename', 'mod_book');
$strtop   = get_string('top', 'mod_book');

@header('Cache-Control: private, pre-check=0, post-check=0, max-age=0');
@header('Pragma: no-cache');
@header('Expires: ');
@header('Accept-Ranges: none');
@header('Content-type: text/html; charset=utf-8');

if ($chapter) {

    if ($chapter->hidden) {
        require_capability('mod/book:viewhiddenchapters', $context);
    }
    \booktool_print\event\chapter_printed::create_from_chapter($book, $context, $chapter)->trigger();

    // page header
    ?>
    <!DOCTYPE HTML>
    <html>
    <head>
      <title><?php echo format_string($book->name, true, array('context'=>$context)) ?></title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="description" content="<?php echo s(format_string($book->name, true, array('context'=>$context))) ?>" />
      <link rel="stylesheet" type="text/css" href="print.css" />
    </head>
    <body>
    <?php
    // Print dialog link.
    $printtext = get_string('printchapter', 'booktool_print');
    $printicon = $OUTPUT->pix_icon('chapter', $printtext, 'booktool_print', array('class' => 'book_print_icon'));
    $printlinkatt = array('onclick' => 'window.print();return false;', 'class' => 'book_no_print');
    echo html_writer::link('#', $printicon.$printtext, $printlinkatt);
    ?>
    <a name="top"></a>
    <?php
    echo $OUTPUT->heading(format_string($book->name, true, array('context'=>$context)), 1);
    ?>
    <div class="chapter">
    <?php


    if (!$book->customtitles) {
        if (!$chapter->subchapter) {
            $currtitle = book_get_chapter_title($chapter->id, $chapters, $book, $context);
            echo $OUTPUT->heading($currtitle);
        } else {
            $currtitle = book_get_chapter_title($chapters[$chapter->id]->parent, $chapters, $book, $context);
            $currsubtitle = book_get_chapter_title($chapter->id, $chapters, $book, $context);
            echo $OUTPUT->heading($currtitle);
            echo $OUTPUT->heading($currsubtitle, 3);
        }
    }

    $chaptertext = file_rewrite_pluginfile_urls($chapter->content, 'pluginfile.php', $context->id, 'mod_book', 'chapter', $chapter->id);
    echo format_text($chaptertext, $chapter->contentformat, array('noclean'=>true, 'context'=>$context));
    echo '</div>';
    echo '</body> </html>';

} else {
    \booktool_print\event\book_printed::create_from_book($book, $context)->trigger();

    $allchapters = $DB->get_records('book_chapters', array('bookid'=>$book->id), 'pagenum');
    $book->intro = file_rewrite_pluginfile_urls($book->intro, 'pluginfile.php', $context->id, 'mod_book', 'intro', null);

    // page header
    ?>
    <!DOCTYPE HTML>
    <html>
    <head>
      <title><?php echo format_string($book->name, true, array('context'=>$context)) ?></title>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="description" content="<?php echo s(format_string($book->name, true, array('noclean'=>true, 'context'=>$context))) ?>" />
      <link rel="stylesheet" type="text/css" href="print.css" />
    </head>
    <body>
    <?php
    // Print dialog link.
    $printtext = get_string('printbook', 'booktool_print');
    $printicon = $OUTPUT->pix_icon('book', $printtext, 'booktool_print', array('class' => 'book_print_icon'));
    $printlinkatt = array('onclick' => 'window.print();return false;', 'class' => 'book_no_print');
    echo html_writer::link('#', $printicon.$printtext, $printlinkatt);
    ?>
    <a name="top"></a>
    <?php
    echo $OUTPUT->heading(format_string($book->name, true, array('context'=>$context)), 1);
    ?>
    <p class="book_summary"><?php echo format_text($book->intro, $book->introformat, array('noclean'=>true, 'context'=>$context)) ?></p>
    <div class="book_info"><table>
    <tr>
    <td><?php echo get_string('site') ?>:</td>
    <td><a href="<?php echo $CFG->wwwroot ?>"><?php echo format_string($SITE->fullname, true, array('context'=>$context)) ?></a></td>
    </tr><tr>
    <td><?php echo get_string('course') ?>:</td>
    <td><?php echo format_string($course->fullname, true, array('context'=>$context)) ?></td>
    </tr><tr>
    <td><?php echo get_string('modulename', 'mod_book') ?>:</td>
    <td><?php echo format_string($book->name, true, array('context'=>$context)) ?></td>
    </tr><tr>
    <td><?php echo get_string('printedby', 'booktool_print') ?>:</td>
    <td><?php echo fullname($USER, true) ?></td>
    </tr><tr>
    <td><?php echo get_string('printdate', 'booktool_print') ?>:</td>
    <td><?php echo userdate(time()) ?></td>
    </tr>
    </table></div>

    <?php
    list($toc, $titles) = booktool_print_get_toc($chapters, $book, $cm);
    echo $toc;
    // chapters
    $link1 = $CFG->wwwroot.'/mod/book/view.php?id='.$course->id.'&chapterid=';
    $link2 = $CFG->wwwroot.'/mod/book/view.php?id='.$course->id;
    foreach ($chapters as $ch) {
        $chapter = $allchapters[$ch->id];
        if ($chapter->hidden) {
            continue;
        }
        echo '<div class="book_chapter"><a name="ch'.$ch->id.'"></a>';
        if (!$book->customtitles) {
            if (!$chapter->subchapter) {
                echo $OUTPUT->heading($titles[$ch->id]);
            } else {
                echo $OUTPUT->heading($titles[$ch->id], 3);
            }
        }
        $content = str_replace($link1, '#ch', $chapter->content);
        $content = str_replace($link2, '#top', $content);
        $content = file_rewrite_pluginfile_urls($content, 'pluginfile.php', $context->id, 'mod_book', 'chapter', $ch->id);
        echo format_text($content, $chapter->contentformat, array('noclean'=>true, 'context'=>$context));
        echo '</div>';
        // echo '<a href="#toc">'.$strtop.'</a>';
    }
    echo '</body> </html>';
}

