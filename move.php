<?PHP // $Id: move.php,v 1.2 2006/11/21 19:26:36 skodak Exp $

require('teacheraccess.php'); //page only for teachers
$up  = optional_param('up', 0, PARAM_BOOL);


$oldchapters = get_records('book_chapters', 'bookid', $book->id, 'pagenum', 'id, pagenum, subchapter');

$nothing = 0;

$chapters = array();
$chs = 0;
$che = 0;
$ts = 0;
$te = 0;
// create new ordered array and find chapters to be moved
$i = 1;
$found = 0;
foreach ($oldchapters as $ch) {
    $chapters[$i] = $ch;
    if ($chapter->id == $ch->id) {
        $chs = $i;
        $che = $chs;
        if ($ch->subchapter) {
            $found = 1;//subchapter moves alone
        }
    } else if ($chs) {
        if ($found) {
            //nothing
        } else if ($ch->subchapter) {
            $che = $i; // chapter with subchapter(s)
        } else {
            $found = 1;
        }
    }
    $i++;
}

// find target chapter(s)
if ($chapters[$chs]->subchapter) { //moving single subchapter up or down
    if ($up) {
        if ($chs == 1) {
            $nothing = 1; //already first
        } else {
            $ts = $chs - 1;
            $te = $ts;
        }
    } else { //down
        if ($che == count($chapters)) {
            $nothing = 1; //already last
        } else {
            $ts = $che + 1;
            $te = $ts;
        }
    }
} else { // moving chapter and looking for next/previous chapter
    if ($up) { //up
        if ($chs == 1) {
            $nothing = 1; //already first
        } else {
            $te = $chs - 1;
            for($i = $chs-1; $i >= 1; $i--) {
                if ($chapters[$i]->subchapter) {
                    $ts = $i;
                } else {
                    $ts = $i;
                    break;
                }
            }
        }
    } else { //down
        if ($che == count($chapters)) {
            $nothing = 1; //already last
        } else {
            $ts = $che + 1;
            $found = 0;
            for($i = $che+1; $i <= count($chapters); $i++) {
                if ($chapters[$i]->subchapter) {
                    $te = $i;
                } else {
                    if ($found) {
                        break;
                    } else {
                        $te = $i;
                        $found = 1;
                    }
                }
            }
        }
    }
}

//recreated newly sorted list of chapters
if (!$nothing) {
    $newchapters = array();

    if ($up) {
        if ($ts > 1) {
            for ($i=1; $i<$ts; $i++) {
                $newchapters[] = $chapters[$i];
            }
        }
        for ($i=$chs; $i<=$che; $i++) {
            $newchapters[$i] = $chapters[$i];
        }
        for ($i=$ts; $i<=$te; $i++) {
            $newchapters[$i] = $chapters[$i];
        }
        if ($che<count($chapters)) {
            for ($i=$che; $i<=count($chapters); $i++) {
                $newchapters[$i] = $chapters[$i];
            }
        }
    } else {
        if ($chs > 1) {
            for ($i=1; $i<$chs; $i++) {
                $newchapters[] = $chapters[$i];
            }
        }
        for ($i=$ts; $i<=$te; $i++) {
            $newchapters[$i] = $chapters[$i];
        }
        for ($i=$chs; $i<=$che; $i++) {
            $newchapters[$i] = $chapters[$i];
        }
        if ($te<count($chapters)) {
            for ($i=$te; $i<=count($chapters); $i++) {
                $newchapters[$i] = $chapters[$i];
            }
        }
    }

    //store chapters in the new order
    $i = 1;
    foreach ($newchapters as $ch) {
        $ch->pagenum = $i;
        update_record('book_chapters', $ch);
        $i++;
    }
}

add_to_log($course->id, 'course', 'update mod', '../mod/book/view.php?id='.$cm->id, 'book '.$book->id);
add_to_log($course->id, 'book', 'update', 'view.php?id='.$cm->id, $book->id, $cm->id);
book_check_structure($book->id);
redirect('view.php?id='.$cm->id.'&chapterid='.$chapter->id);
die;

?>
