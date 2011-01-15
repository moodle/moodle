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
 * Book module local lib functions
 *
 * @package    mod
 * @subpackage book
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

define('BOOK_NUM_NONE',     '0');
define('BOOK_NUM_NUMBERS',  '1');
define('BOOK_NUM_BULLETS',  '2');
define('BOOK_NUM_INDENTED', '3');

require_once($CFG->dirroot.'/mod/book/lib.php');
require_once($CFG->libdir.'/filelib.php');

function book_get_numbering_types() {
    return array (BOOK_NUM_NONE       => get_string('numbering0', 'mod_book'),
                  BOOK_NUM_NUMBERS    => get_string('numbering1', 'mod_book'),
                  BOOK_NUM_BULLETS    => get_string('numbering2', 'mod_book'),
                  BOOK_NUM_INDENTED   => get_string('numbering3', 'mod_book') );
}


//////////////////////////////////////////////////////////////////////////////////////
/// Any other book functions go here.  Each of them must have a name that
/// starts with book_

/**
 * check chapter ordering and make sure subchapter is not first
 * in book hidden chapter must have all subchapters hidden too
 * @param int $id
 * @return void
 */
function book_check_structure($bookid) {
    global $DB;

    if ($chapters = $DB->get_records('book_chapters', array('bookid'=>$bookid), 'pagenum', 'id, pagenum, subchapter, hidden')) {
        $first = true;
        $hidesub = true;
        $i = 1;
        foreach($chapters as $ch) {
            if ($first and $ch->subchapter) {
                $ch->subchapter = 0;
            }
            $first = false;
            if (!$ch->subchapter) {
                $hidesub = $ch->hidden;
            } else {
                $ch->hidden = $hidesub ? true : $ch->hidden;
            }
            $ch->pagenum = $i;
            $DB->update_record('book_chapters', $ch);
            $i++;
        }
    }
}

/// general function for logging to table
function book_log($str1, $str2, $level = 0) {
    switch ($level) {
        case 1:
            echo '<tr><td><span class="dimmed_text">'.$str1.'</span></td><td><span class="dimmed_text">'.$str2.'</span></td></tr>';
            break;
        case 2:
            echo '<tr><td><span style="color: rgb(255, 0, 0);">'.$str1.'</span></td><td><span style="color: rgb(255, 0, 0);">'.$str2.'</span></td></tr>';
            break;
        default:
            echo '<tr><td>'.$str1.'</class></td><td>'.$str2.'</td></tr>';
            break;
    }
}

//=================================================
// import functions
//=================================================

/// normalize relative links (= remove ..)
function book_prepare_link($ref) {
    if ($ref == '') {
        return '';
    }
    $ref = str_replace('\\','/',$ref); //anti MS hack
    $cnt = substr_count($ref, '..');
    for($i=0; $i<$cnt; $i++) {
        $ref = ereg_replace('[^/]+/\.\./', '', $ref);
    }
    //still any '..' left?? == error! error!
    if (substr_count($ref, '..') > 0) {
        return '';
    }
    if (ereg('[\|\`]', $ref)) {  // check for other bad characters
        return '';
    }
    return $ref;
}

/// read chapter content from file
function book_read_chapter($base, $ref) {
    $file = $base.'/'.$ref;
    if (filesize($file) <= 0 or !is_readable($file)) {
        book_log($ref, get_string('error'), 2);
        return;
    }
    //first read data
    $handle = fopen($file, "rb");
    $contents = fread($handle, filesize($file));
    fclose($handle);
    //extract title
    $chapter = new object();
    if (preg_match('/<title>([^<]+)<\/title>/i', $contents, $matches)) {
        $chapter->title = $matches[1];
    } else {
        $chapter->title = $ref;
    }
    //extract page body
    if (preg_match('/<body[^>]*>(.+)<\/body>/is', $contents, $matches)) {
        $chapter->content = $matches[1];
    } else {
        book_log($ref, get_string('error'), 2);
        return;
    }
    book_log($ref, get_string('ok'));
    $chapter->importsrc = $ref;
    //extract page head
    if (preg_match('/<head[^>]*>(.+)<\/head>/is', $contents, $matches)) {
        $head = $matches[1];
        if (preg_match('/charset=([^"]+)/is', $head, $matches)) {
            $enc = $matches[1];
            $textlib = textlib_get_instance();
            $chapter->content = $textlib->convert($chapter->content, $enc, current_charset());
            $chapter->title = $textlib->convert($chapter->title, $enc, current_charset());
        }
        if (preg_match_all('/<link[^>]+rel="stylesheet"[^>]*>/i', $head, $matches)) { //dlnsk extract links to css
            for($i=0; $i<count($matches[0]); $i++){
                $chapter->content = $matches[0][$i]."\n".$chapter->content;
            }
        }
    }
    return $chapter;
}

///relink images and relative links
function book_relink($id, $bookid, $courseid) {
    global $CFG, $DB;

    if ($CFG->slasharguments) {
        $coursebase = $CFG->wwwroot.'/file.php/'.$courseid;
    } else {
        $coursebase = $CFG->wwwroot.'/file.php?file=/'.$courseid;
    }
    $chapters = $DB->get_records('book_chapters', array('bookid'=>$bookid), 'pagenum', 'id, pagenum, title, content, importsrc');
    $originals = array();
    foreach($chapters as $ch) {
        $originals[$ch->importsrc] = $ch;
    }
    foreach($chapters as $ch) {
        $rel = substr($ch->importsrc, 0, strrpos($ch->importsrc, '/')+1);
        $base = $coursebase.strtr(urlencode($rel), array("%2F" => "/"));  //for better internationalization (dlnsk)
        $modified = false;
        //image relinking
        if ($ch->importsrc && preg_match_all('/(<img[^>]+src=")([^"]+)("[^>]*>)/i', $ch->content, $images)) {
            for($i = 0; $i<count($images[0]); $i++) {
                if (!preg_match('/[a-z]+:/i', $images[2][$i])) { // not absolute link
                    $link = book_prepare_link($base.$images[2][$i]);
                    if ($link == '') {
                        continue;
                    }
                    $origtag = $images[0][$i];
                    $newtag = $images[1][$i].$link.$images[3][$i];
                    $ch->content = str_replace($origtag, $newtag, $ch->content);
                    $modified = true;
                    book_log($ch->title, $images[2][$i].' --> '.$link);
                }
            }
        }
        //css relinking (dlnsk)
        if ($ch->importsrc && preg_match_all('/(<link[^>]+href=")([^"]+)("[^>]*>)/i', $ch->content, $csslinks)) {
            for($i = 0; $i<count($csslinks[0]); $i++) {
                if (!preg_match('/[a-z]+:/i', $csslinks[2][$i])) { // not absolute link
                    $link = book_prepare_link($base.$csslinks[2][$i]);
                    if ($link == '') {
                        continue;
                    }
                    $origtag = $csslinks[0][$i];
                    $newtag = $csslinks[1][$i].$link.$csslinks[3][$i];
                    $ch->content = str_replace($origtag, $newtag, $ch->content);
                    $modified = true;
                    book_log($ch->title, $csslinks[2][$i].' --> '.$link);
                }
            }
        }
        //general embed relinking - flash and others??
        if ($ch->importsrc && preg_match_all('/(<embed[^>]+src=")([^"]+)("[^>]*>)/i', $ch->content, $embeds)) {
            for($i = 0; $i<count($embeds[0]); $i++) {
                if (!preg_match('/[a-z]+:/i', $embeds[2][$i])) { // not absolute link
                    $link = book_prepare_link($base.$embeds[2][$i]);
                    if ($link == '') {
                        continue;
                    }
                    $origtag = $embeds[0][$i];
                    $newtag = $embeds[1][$i].$link.$embeds[3][$i];
                    $ch->content = str_replace($origtag, $newtag, $ch->content);
                    $modified = true;
                    book_log($ch->title, $embeds[2][$i].' --> '.$link);
                }
            }
        }
        //flash in IE <param name=movie value="something" - I do hate IE!
        if ($ch->importsrc && preg_match_all('/<param[^>]+name\s*=\s*"?movie"?[^>]*>/i', $ch->content, $params)) {
            for($i = 0; $i<count($params[0]); $i++) {
                if (preg_match('/(value=\s*")([^"]+)(")/i', $params[0][$i], $values)) {
                    if (!preg_match('/[a-z]+:/i', $values[2])) { // not absolute link
                        $link = book_prepare_link($base.$values[2]);
                        if ($link == '') {
                            continue;
                        }
                        $newvalue = $values[1].$link.$values[3];
                        $newparam = str_replace($values[0], $newvalue, $params[0][$i]);
                        $ch->content = str_replace($params[0][$i], $newparam, $ch->content);
                        $modified = true;
                        book_log($ch->title, $values[2].' --> '.$link);
                    }
                }
            }
        }
        //java applet - add code bases if not present!!!!
        if ($ch->importsrc && preg_match_all('/<applet[^>]*>/i', $ch->content, $applets)) {
            for($i = 0; $i<count($applets[0]); $i++) {
                if (!stripos($applets[0][$i], 'codebase')) {
                    $newapplet = str_ireplace('<applet', '<applet codebase="."', $applets[0][$i]);
                    $ch->content = str_replace($applets[0][$i], $newapplet, $ch->content);
                    $modified = true;
                }
            }
        }
        //relink java applet code bases
        if ($ch->importsrc && preg_match_all('/(<applet[^>]+codebase=")([^"]+)("[^>]*>)/i', $ch->content, $codebases)) {
            for($i = 0; $i<count($codebases[0]); $i++) {
                if (!preg_match('/[a-z]+:/i', $codebases[2][$i])) { // not absolute link
                    $link = book_prepare_link($base.$codebases[2][$i]);
                    if ($link == '') {
                        continue;
                    }
                    $origtag = $codebases[0][$i];
                    $newtag = $codebases[1][$i].$link.$codebases[3][$i];
                    $ch->content = str_replace($origtag, $newtag, $ch->content);
                    $modified = true;
                    book_log($ch->title, $codebases[2][$i].' --> '.$link);
                }
            }
        }
        //relative link conversion
        if ($ch->importsrc && preg_match_all('/(<a\s[^>]*href=")([^"^#]*)(#[^"]*)?("[^>]*>)/i', $ch->content, $links)) {
            for($i = 0; $i<count($links[0]); $i++) {
                if ($links[2][$i] != ''                         //check for inner anchor links
                && !preg_match('/[a-z]+:/i', $links[2][$i])) { //not absolute link
                    $origtag = $links[0][$i];
                    $target = book_prepare_link($rel.$links[2][$i]); //target chapter
                    if ($target != '' && array_key_exists($target, $originals)) {
                        $o = $originals[$target];
                        $newtag = $links[1][$i].$CFG->wwwroot.'/mod/book/view.php?id='.$id.'&chapterid='.$o->id.$links[3][$i].$links[4][$i];
                        $newtag = preg_replace('/target=[^\s>]/i','', $newtag);
                        $ch->content = str_replace($origtag, $newtag, $ch->content);
                        $modified = true;
                        book_log($ch->title, $links[2][$i].$links[3][$i].' --> '.$CFG->wwwroot.'/mod/book/view.php?id='.$id.'&chapterid='.$o->id.$links[3][$i]);
                    } else if ($target!='' && (!preg_match('/\.html$|\.htm$/i', $links[2][$i]))) { // other relative non html links converted to download links
                        $target = book_prepare_link($base.$links[2][$i]);
                        $origtag = $links[0][$i];
                        $newtag = $links[1][$i].$target.$links[4][$i];
                        $ch->content = str_replace($origtag, $newtag, $ch->content);
                        $modified = true;
                        book_log($ch->title, $links[2][$i].' --> '.$target);
                    }
                }
            }
        }
        if ($modified) {
            $DB->update_record('book_chapters', $ch);
        }
    }
}

function book_get_toc($chapters, $chapter, $book, $cm, $edit, $print) {
    global $USER, $OUTPUT;

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
    $first = 1;
    $titles = array();

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

    return array($toc, $currtitle, $currsubtitle, $titles);
}