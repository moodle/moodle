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
 * Book toc printing
 *
 * @package    mod
 * @subpackage book
 * @copyright  2004-2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/// included from mod/book/view.php and print.php
///
/// uses:
///   $chapters - all book chapters
///   $chapter - may be false
///   $cm - course module
///   $book - book
///   $edit - force editing view


/// fills:
///   $toc
///   $title (not for print)

$currtitle = '';    //active chapter title (plain text)
$currsubtitle = ''; //active subchapter if any
$prevtitle = '&nbsp;';
$toc = '';          //representation of toc (HTML)

$nch = 0; //chapter number
$ns = 0;  //subchapter number
$title = '';
$first = 1;

switch ($book->numbering) {
  case BOOK_NUM_NONE:
      $toc .= '<div class="book_toc_none">';
      break;
  case BOOK_NUM_NUMBERS:
      $toc .= '<div class="book_toc_numbered">';
      break;
  case BOOK_NUM_BULLETS:
      $toc .= '<div class="book_toc_bullets">';
      break;
  case BOOK_NUM_INDENTED:
      $toc .= '<div class="book_toc_indented">';
      break;
}


if ($print) { ///TOC for printing
    $toc .= '<a name="toc"></a>';
    if ($book->customtitles) {
        $toc .= '<h1>'.get_string('toc', 'book').'</h1>';
    } else {
        $toc .= '<p class="book_chapter_title">'.get_string('toc', 'book').'</p>';
    }
    $titles = array();
    $toc .= '<ul>';
    foreach($chapters as $ch) {
        $title = trim(strip_tags($ch->title));
        if (!$ch->hidden) {
            if (!$ch->subchapter) {
                $nch++;
                $ns = 0;
                $toc .= ($first) ? '<li>' : '</ul></li><li>';
                if ($book->numbering == BOOK_NUM_NUMBERS) {
                      $title = "$nch $title";
                }
            } else {
                $ns++;
                $toc .= ($first) ? '<li><ul><li>' : '<li>';
                if ($book->numbering == BOOK_NUM_NUMBERS) {
                      $title = "$nch.$ns $title";
                }
            }
            $titles[$ch->id] = $title;
            $toc .= '<a title="'.s($title).'" href="#ch'.$ch->id.'">'.$title.'</a>';
            $toc .= (!$ch->subchapter) ? '<ul>' : '</li>';
            $first = 0;
        }
    }
    $toc .= '</ul></li></ul>';
} else if ($edit) { ///teacher's TOC
    $toc .= '<ul>';
    $i = 0;
    foreach($chapters as $ch) {
        $i++;
        $title = trim(strip_tags($ch->title));
        if (!$ch->subchapter) {
            $toc .= ($first) ? '<li>' : '</ul></li><li>';
            if (!$ch->hidden) {
                $nch++;
                $ns = 0;
                if ($book->numbering == BOOK_NUM_NUMBERS) {
                    $title = "$nch $title";
                }
            } else {
                if ($book->numbering == BOOK_NUM_NUMBERS) {
                    $title = "x $title";
                }
                $title = '<span class="dimmed_text">'.$title.'</span>';
            }
            $prevtitle = $title;
        } else {
            $toc .= ($first) ? '<li><ul><li>' : '<li>';
            if (!$ch->hidden) {
                $ns++;
                if ($book->numbering == BOOK_NUM_NUMBERS) {
                    $title = "$nch.$ns $title";
                }
            } else {
                if ($book->numbering == BOOK_NUM_NUMBERS) {
                    $title = "x.x $title";
                }
                $title = '<span class="dimmed_text">'.$title.'</span>';
            }
        }

        if ($ch->id == $chapter->id) {
            $toc .= '<strong>'.$title.'</strong>';
            if ($ch->subchapter) {
                $currtitle = $prevtitle;
                $currsubtitle = $title;
            } else {
                $currtitle = $title;
                $currsubtitle = '&nbsp;';
            }
        } else {
            $toc .= '<a title="'.s($title).'" href="view.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'">'.$title.'</a>';
        }
        $toc .=  '&nbsp;&nbsp;';
        if ($i != 1) {
            $toc .=  ' <a title="'.get_string('up').'" href="move.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'&amp;up=1&amp;sesskey='.$USER->sesskey.'"><img src="'.$OUTPUT->pix_url('t/up').'" height="11" class="iconsmall" alt="'.get_string('up').'" /></a>';
        }
        if ($i != count($chapters)) {
            $toc .=  ' <a title="'.get_string('down').'" href="move.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'&amp;up=0&amp;sesskey='.$USER->sesskey.'"><img src="'.$OUTPUT->pix_url('t/down').'" height="11" class="iconsmall" alt="'.get_string('down').'" /></a>';
        }
        $toc .=  ' <a title="'.get_string('edit').'" href="edit.php?cmid='.$cm->id.'&amp;id='.$ch->id.'"><img src="'.$OUTPUT->pix_url('t/edit').'" height="11" class="iconsmall" alt="'.get_string('edit').'" /></a>';
        $toc .=  ' <a title="'.get_string('delete').'" href="delete.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'&amp;sesskey='.$USER->sesskey.'"><img src="'.$OUTPUT->pix_url('t/delete').'" height="11" class="iconsmall" alt="'.get_string('delete').'" /></a>';
        if ($ch->hidden) {
            $toc .= ' <a title="'.get_string('show').'" href="show.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'&amp;sesskey='.$USER->sesskey.'"><img src="'.$OUTPUT->pix_url('t/show').'" height="11" class="iconsmall" alt="'.get_string('show').'" /></a>';
        } else {
            $toc .= ' <a title="'.get_string('hide').'" href="show.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'&amp;sesskey='.$USER->sesskey.'"><img src="'.$OUTPUT->pix_url('t/hide').'" height="11" class="iconsmall" alt="'.get_string('hide').'" /></a>';
        }
        $toc .= ' <a title="'.get_string('addafter', 'book').'" href="edit.php?cmid='.$cm->id.'&amp;pagenum='.$ch->pagenum.'&amp;subchapter='.$ch->subchapter.'"><img src="'.$OUTPUT->pix_url('add', 'mod_book').'" height="11" class="iconsmall" alt="'.get_string('addafter', 'book').'" /></a>';

        $toc .= (!$ch->subchapter) ? '<ul>' : '</li>';
        $first = 0;
    }
    $toc .= '</ul></li></ul>';
} else { //normal students view
    $toc .= '<ul>';
    foreach($chapters as $ch) {
        $title = trim(strip_tags($ch->title));
        if (!$ch->hidden) {
            if (!$ch->subchapter) {
                $nch++;
                $ns = 0;
                $toc .= ($first) ? '<li>' : '</ul></li><li>';
                if ($book->numbering == BOOK_NUM_NUMBERS) {
                      $title = "$nch $title";
                }
            $prevtitle = $title;
            } else {
                $ns++;
                $toc .= ($first) ? '<li><ul><li>' : '<li>';
                if ($book->numbering == BOOK_NUM_NUMBERS) {
                      $title = "$nch.$ns $title";
                }
            }
            if ($ch->id == $chapter->id) {
                $toc .= '<strong>'.$title.'</strong>';
                if ($ch->subchapter) {
                    $currtitle = $prevtitle;
                    $currsubtitle = $title;
                } else {
                    $currtitle = $title;
                    $currsubtitle = '&nbsp;';
                }
            } else {
                $toc .= '<a title="'.s($title).'" href="view.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'">'.$title.'</a>';
            }
            $toc .= (!$ch->subchapter) ? '<ul>' : '</li>';
            $first = 0;
        }
    }
    $toc .= '</ul></li></ul>';
}

$toc .= '</div>';

$toc = str_replace('<ul></ul>', '', $toc); //cleanup of invalid structures

