<?PHP // $Id: toc.php,v 1.3 2007/05/20 06:00:30 skodak Exp $

defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');
 
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
} else if ($edit) { ///teacher's TOC
    $toc .= '<font size="-1"><ul>';
    $i = 0;
    foreach($chapters as $ch) {
        $i++;
        $title = trim(strip_tags($ch->title));
        if (!$ch->subchapter) {
            $toc .= ($first) ? '<li>' : '</ul></li><li>';
            if (!$ch->hidden) {
                $nch++;
                $ns = 0;
                if ($book->numbering == NUM_NUMBERS) {
                    $title = "$nch $title";
                }
            } else {
                if ($book->numbering == NUM_NUMBERS) {
                    $title = "x $title";
                }
                $title = '<span class="dimmed_text">'.$title.'</span>';
            }
            $prevtitle = $title;
        } else {
            $toc .= ($first) ? '<li><ul><li>' : '<li>';
            if (!$ch->hidden) {
                $ns++;
                if ($book->numbering == NUM_NUMBERS) {
                    $title = "$nch.$ns $title";
                }
            } else {
                if ($book->numbering == NUM_NUMBERS) {
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
            $toc .= '<a title="'.htmlspecialchars($title).'" href="view.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'">'.$title.'</a>';
        }
        $toc .=  '&nbsp;&nbsp;';
        if ($i != 1) {
            $toc .=  ' <a title="'.get_string('up').'" href="move.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'&amp;up=1&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/up.gif" height="11" class="iconsmall" alt="'.get_string('up').'" /></a>';
        }
        if ($i != count($chapters)) {
            $toc .=  ' <a title="'.get_string('down').'" href="move.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'&amp;up=0&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/down.gif" height="11" class="iconsmall" alt="'.get_string('down').'" /></a>';
        }
        $toc .=  ' <a title="'.get_string('edit').'" href="edit.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'"><img src="'.$CFG->pixpath.'/t/edit.gif" height="11" class="iconsmall" alt="'.get_string('edit').'" /></a>';
        $toc .=  ' <a title="'.get_string('delete').'" href="delete.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/delete.gif" height="11" class="iconsmall" alt="'.get_string('delete').'" /></a>';
        if ($ch->hidden) {
            $toc .= ' <a title="'.get_string('show').'" href="show.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/show.gif" height="11" class="iconsmall" alt="'.get_string('show').'" /></a>';
        } else {
            $toc .= ' <a title="'.get_string('hide').'" href="show.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'&amp;sesskey='.$USER->sesskey.'"><img src="'.$CFG->pixpath.'/t/hide.gif" height="11" class="iconsmall" alt="'.get_string('hide').'" /></a>';
        }
        $toc .= ' <a title="'.get_string('addafter', 'book').'" href="edit.php?id='.$cm->id.'&amp;pagenum='.$ch->pagenum.'&amp;subchapter='.$ch->subchapter.'"><img src="pix/add.gif" height="11" class="iconsmall" alt="'.get_string('addafter', 'book').'" /></a>';

        $toc .= (!$ch->subchapter) ? '<ul>' : '</li>';
        $first = 0;
    }
    $toc .= '</ul></li></ul></font>';
} else { //normal students view
    $toc .= '<font size="-1"><ul>';
    foreach($chapters as $ch) {
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
                $toc .= '<a title="'.htmlspecialchars($title).'" href="view.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'">'.$title.'</a>';
            }
            $toc .= (!$ch->subchapter) ? '<ul>' : '</li>';
            $first = 0;
        }
    }
    $toc .= '</ul></li></ul></font>';
}

$toc .= '</div>';

$toc = str_replace('<ul></ul>', '', $toc); //cleanup of invalid structures

?>
