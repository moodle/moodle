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
 * Table of contents.
 *
 * @package mod_game
 * @copyright 2007 Vasilis Daloukas
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('NUM_NONE',     '0');
define('NUM_NUMBERS',  '1');
define('NUM_BULLETS',  '2');
define('NUM_INDENTED', '3');

$currtitle = '';    // Active chapter title (plain text).
$currsubtitle = ''; // Active subchapter if any.
$prevtitle = '&nbsp;';
$toc = '';          // Representation of toc (HTML).

$nch = 0; // Chapter number.
$ns = 0;  // Subchapter number.
$title = '';
$first = 1;

if (!isset($print)) {
    $print = 0;
}

switch ($book->numbering) {
    case NUM_NONE:
        $toc .= '<div class="book_toc_none">';
        break;
    case NUM_NUMBERS:
        $toc .= '<div class="book_toc_numbered">';
        break;
    case NUM_BULLETS:
        $toc .= '<div class="book_toc_bullets">';
        break;
    case NUM_INDENTED:
        $toc .= '<div class="book_toc_indented">';
        break;
}

if ($print) {
    // TOC for printing.
    $toc .= '<a name="toc"></a>';
    if ($book->customtitles) {
        $toc .= '<h1>'.get_string('toc', 'book').'</h1>';
    } else {
        $toc .= '<p class="book_chapter_title">'.get_string('toc', 'book').'</p>';
    }
    $titles = array();
    $toc .= '<ul>';
    foreach ($chapters as $ch) {
        $title = trim(strip_tags($ch->title));
        if (!$ch->hidden) {
            if (!$ch->subchapter) {
                $nch++;
                $ns = 0;
                $toc .= ($first) ? '<li>' : '</ul></li><li>';
                if ($book->numbering == NUM_NUMBERS) {
                    $title = "$nch $title";
                }
            } else {
                $ns++;
                $toc .= ($first) ? '<li><ul><li>' : '<li>';
                if ($book->numbering == NUM_NUMBERS) {
                    $title = "$nch.$ns $title";
                }
            }
            $titles[$ch->id] = $title;
            $toc .= '<a title="'.htmlspecialchars($title).'" href="#ch'.$ch->id.'">'.$title.'</a>';
            $toc .= (!$ch->subchapter) ? '<ul>' : '</li>';
            $first = 0;
        }
    }
    $toc .= '</ul></li></ul>';
} else {
    // Normal students view.
    $toc .= '<font size="-1"><ul>';
    foreach ($chapters as $ch) {
        $title = trim(strip_tags($ch->title));
        if (!$ch->hidden) {
            if (!$ch->subchapter) {
                $nch++;
                $ns = 0;
                $toc .= ($first) ? '<li>' : '</ul></li><li>';
                if ($book->numbering == NUM_NUMBERS) {
                      $title = "$nch $title";
                }
                $prevtitle = $title;
            } else {
                $ns++;
                $toc .= ($first) ? '<li><ul><li>' : '<li>';
                if ($book->numbering == NUM_NUMBERS) {
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
                if (array_key_exists( $ch->id, $okchapters)) {
                    $toc .= '<a title="'.htmlspecialchars($title).'" href="attempt.php?id='
                        .$id.'&chapterid='.$ch->id.'">'.$title.'</a>';
                } else {
                    $toc .= htmlspecialchars($title);
                }
            }
            $toc .= (!$ch->subchapter) ? '<ul>' : '</li>';
            $first = 0;
        }
    }
    $toc .= '</ul></li></ul></font>';
}

$toc .= '</div>';

$toc = str_replace('<ul></ul>', '', $toc); // Cleanup of invalid structures.
