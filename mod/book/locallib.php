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
 * @package    mod_book
 * @copyright  2010-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(dirname(__FILE__).'/lib.php');
require_once($CFG->libdir.'/filelib.php');

/**
 * The following defines are used to define how the chapters and subchapters of a book should be displayed in that table of contents.
 * BOOK_NUM_NONE        No special styling will applied and the editor will be able to do what ever thay want in the title
 * BOOK_NUM_NUMBERS     Chapters and subchapters are numbered (1, 1.1, 1.2, 2, ...)
 * BOOK_NUM_BULLETS     Subchapters are indented and displayed with bullets
 * BOOK_NUM_INDENTED    Subchapters are indented
 */
define('BOOK_NUM_NONE',     '0');
define('BOOK_NUM_NUMBERS',  '1');
define('BOOK_NUM_BULLETS',  '2');
define('BOOK_NUM_INDENTED', '3');

/**
 * Preload book chapters and fix toc structure if necessary.
 *
 * Returns array of chapters with standard 'pagenum', 'id, pagenum, subchapter, title, hidden'
 * and extra 'parent, number, subchapters, prev, next'.
 * Please note the content/text of chapters is not included.
 *
 * @param  stdClass $book
 * @return array of id=>chapter
 */
function book_preload_chapters($book) {
    global $DB;
    $chapters = $DB->get_records('book_chapters', array('bookid'=>$book->id), 'pagenum', 'id, pagenum, subchapter, title, hidden');
    if (!$chapters) {
        return array();
    }

    $prev = null;
    $prevsub = null;

    $first = true;
    $hidesub = true;
    $parent = null;
    $pagenum = 0; // chapter sort
    $i = 0;       // main chapter num
    $j = 0;       // subchapter num
    foreach ($chapters as $id => $ch) {
        $oldch = clone($ch);
        $pagenum++;
        $ch->pagenum = $pagenum;
        if ($first) {
            // book can not start with a subchapter
            $ch->subchapter = 0;
            $first = false;
        }
        if (!$ch->subchapter) {
            $ch->prev = $prev;
            $ch->next = null;
            if ($prev) {
                $chapters[$prev]->next = $ch->id;
            }
            if ($ch->hidden) {
                if ($book->numbering == BOOK_NUM_NUMBERS) {
                    $ch->number = 'x';
                } else {
                    $ch->number = null;
                }
            } else {
                $i++;
                $ch->number = $i;
            }
            $j = 0;
            $prevsub = null;
            $hidesub = $ch->hidden;
            $parent = $ch->id;
            $ch->parent = null;
            $ch->subchapters = array();
        } else {
            $ch->prev = $prevsub;
            $ch->next = null;
            if ($prevsub) {
                $chapters[$prevsub]->next = $ch->id;
            }
            $ch->parent = $parent;
            $ch->subchapters = null;
            $chapters[$parent]->subchapters[$ch->id] = $ch->id;
            if ($hidesub) {
                // all subchapters in hidden chapter must be hidden too
                $ch->hidden = 1;
            }
            if ($ch->hidden) {
                if ($book->numbering == BOOK_NUM_NUMBERS) {
                    $ch->number = 'x';
                } else {
                    $ch->number = null;
                }
            } else {
                $j++;
                $ch->number = $j;
            }
        }
        if ($oldch->subchapter != $ch->subchapter or $oldch->pagenum != $ch->pagenum or $oldch->hidden != $ch->hidden) {
            // update only if something changed
            $DB->update_record('book_chapters', $ch);
        }
        $chapters[$id] = $ch;
    }

    return $chapters;
}

/**
 * Returns the title for a given chapter
 *
 * @param int $chid
 * @param array $chapters
 * @param stdClass $book
 * @param context_module $context
 * @return string
 */
function book_get_chapter_title($chid, $chapters, $book, $context) {
    $ch = $chapters[$chid];
    $title = trim(format_string($ch->title, true, array('context'=>$context)));
    $numbers = array();
    if ($book->numbering == BOOK_NUM_NUMBERS) {
        if ($ch->parent and $chapters[$ch->parent]->number) {
            $numbers[] = $chapters[$ch->parent]->number;
        }
        if ($ch->number) {
            $numbers[] = $ch->number;
        }
    }

    if ($numbers) {
        $title = implode('.', $numbers).' '.$title;
    }

    return $title;
}

/**
 * General logging to table
 * @param string $str1
 * @param string $str2
 * @param int $level
 * @return void
 */
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

/**
 * Add the book TOC sticky block to the 1st region available
 *
 * @param array $chapters
 * @param stdClass $chapter
 * @param stdClass $book
 * @param stdClass $cm
 * @param bool $edit
 */
function book_add_fake_block($chapters, $chapter, $book, $cm, $edit) {
    global $OUTPUT, $PAGE;

    $toc = book_get_toc($chapters, $chapter, $book, $cm, $edit, 0);

    $bc = new block_contents();
    $bc->title = get_string('toc', 'mod_book');
    $bc->attributes['class'] = 'block';
    $bc->content = $toc;

    $regions = $PAGE->blocks->get_regions();
    $firstregion = reset($regions);
    $PAGE->blocks->add_fake_block($bc, $firstregion);
}

/**
 * Generate toc structure
 *
 * @param array $chapters
 * @param stdClass $chapter
 * @param stdClass $book
 * @param stdClass $cm
 * @param bool $edit
 * @return string
 */
function book_get_toc($chapters, $chapter, $book, $cm, $edit) {
    global $USER, $OUTPUT;

    $toc = '';  // Representation of toc (HTML)
    $nch = 0;   // Chapter number
    $ns = 0;    // Subchapter number
    $first = 1;

    $context = context_module::instance($cm->id);

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

    if ($edit) { // Teacher's TOC
        $toc .= '<ul>';
        $i = 0;
        foreach ($chapters as $ch) {
            $i++;
            $title = trim(format_string($ch->title, true, array('context'=>$context)));
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
            } else {
                $toc .= '<a title="'.s($title).'" href="view.php?id='.$cm->id.'&amp;chapterid='.$ch->id.'">'.$title.'</a>';
            }
            $toc .=  '&nbsp;&nbsp;';
            if ($i != 1) {
                $toc .=  ' <a title="'.get_string('up').'" href="move.php?id='.$cm->id.'&amp;chapterid='.$ch->id.
                        '&amp;up=1&amp;sesskey='.$USER->sesskey.'"><img src="'.$OUTPUT->pix_url('t/up').'" class="iconsmall" alt="'.get_string('up').'" /></a>';
            }
            if ($i != count($chapters)) {
                $toc .=  ' <a title="'.get_string('down').'" href="move.php?id='.$cm->id.'&amp;chapterid='.$ch->id.
                        '&amp;up=0&amp;sesskey='.$USER->sesskey.'"><img src="'.$OUTPUT->pix_url('t/down').'" class="iconsmall" alt="'.get_string('down').'" /></a>';
            }
            $toc .=  ' <a title="'.get_string('edit').'" href="edit.php?cmid='.$cm->id.'&amp;id='.$ch->id.'"><img src="'.
                    $OUTPUT->pix_url('t/edit').'" class="iconsmall" alt="'.get_string('edit').'" /></a>';
            $toc .=  ' <a title="'.get_string('delete').'" href="delete.php?id='.$cm->id.'&amp;chapterid='.$ch->id.
                    '&amp;sesskey='.$USER->sesskey.'"><img src="'.$OUTPUT->pix_url('t/delete').'" class="iconsmall" alt="'.get_string('delete').'" /></a>';
            if ($ch->hidden) {
                $toc .= ' <a title="'.get_string('show').'" href="show.php?id='.$cm->id.'&amp;chapterid='.$ch->id.
                        '&amp;sesskey='.$USER->sesskey.'"><img src="'.$OUTPUT->pix_url('t/show').'" class="iconsmall" alt="'.get_string('show').'" /></a>';
            } else {
                $toc .= ' <a title="'.get_string('hide').'" href="show.php?id='.$cm->id.'&amp;chapterid='.$ch->id.
                        '&amp;sesskey='.$USER->sesskey.'"><img src="'.$OUTPUT->pix_url('t/hide').'" class="iconsmall" alt="'.get_string('hide').'" /></a>';
            }
            $toc .= ' <a title="'.get_string('addafter', 'mod_book').'" href="edit.php?cmid='.$cm->id.
                    '&amp;pagenum='.$ch->pagenum.'&amp;subchapter='.$ch->subchapter.'"><img src="'.
                    $OUTPUT->pix_url('add', 'mod_book').'" class="iconsmall" alt="'.get_string('addafter', 'mod_book').'" /></a>';

            $toc .= (!$ch->subchapter) ? '<ul>' : '</li>';
            $first = 0;
        }
        $toc .= '</ul></li></ul>';
    } else { // Normal students view
        $toc .= '<ul>';
        foreach ($chapters as $ch) {
            $title = trim(format_string($ch->title, true, array('context'=>$context)));
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
                if ($ch->id == $chapter->id) {
                    $toc .= '<strong>'.$title.'</strong>';
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

    $toc = str_replace('<ul></ul>', '', $toc); // Cleanup of invalid structures.

    return $toc;
}


/**
 * File browsing support class
 *
 * @copyright  2010-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class book_file_info extends file_info {
    /** @var stdClass Course object */
    protected $course;
    /** @var stdClass Course module object */
    protected $cm;
    /** @var array Available file areas */
    protected $areas;
    /** @var string File area to browse */
    protected $filearea;

    /**
     * Constructor
     *
     * @param file_browser $browser file_browser instance
     * @param stdClass $course course object
     * @param stdClass $cm course module object
     * @param stdClass $context module context
     * @param array $areas available file areas
     * @param string $filearea file area to browse
     */
    public function __construct($browser, $course, $cm, $context, $areas, $filearea) {
        parent::__construct($browser, $context);
        $this->course   = $course;
        $this->cm       = $cm;
        $this->areas    = $areas;
        $this->filearea = $filearea;
    }

    /**
     * Returns list of standard virtual file/directory identification.
     * The difference from stored_file parameters is that null values
     * are allowed in all fields
     * @return array with keys contextid, filearea, itemid, filepath and filename
     */
    public function get_params() {
        return array('contextid'=>$this->context->id,
                     'component'=>'mod_book',
                     'filearea' =>$this->filearea,
                     'itemid'   =>null,
                     'filepath' =>null,
                     'filename' =>null);
    }

    /**
     * Returns localised visible name.
     * @return string
     */
    public function get_visible_name() {
        return $this->areas[$this->filearea];
    }

    /**
     * Can I add new files or directories?
     * @return bool
     */
    public function is_writable() {
        return false;
    }

    /**
     * Is directory?
     * @return bool
     */
    public function is_directory() {
        return true;
    }

    /**
     * Returns list of children.
     * @return array of file_info instances
     */
    public function get_children() {
        global $DB;

        $children = array();
        $chapters = $DB->get_records('book_chapters', array('bookid'=>$this->cm->instance), 'pagenum', 'id, pagenum');
        foreach ($chapters as $itemid => $unused) {
            if ($child = $this->browser->get_file_info($this->context, 'mod_book', $this->filearea, $itemid)) {
                $children[] = $child;
            }
        }
        return $children;
    }

    /**
     * Returns parent file_info instance
     * @return file_info or null for root
     */
    public function get_parent() {
        return $this->browser->get_file_info($this->context);
    }
}
