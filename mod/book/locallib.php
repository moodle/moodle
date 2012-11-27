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
    $bc->attributes['class'] = 'block block_book_toc';
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

    $toc = '';
    $nch = 0;   // Chapter number
    $ns = 0;    // Subchapter number
    $first = 1;

    $context = context_module::instance($cm->id);

    switch ($book->numbering) {
        case BOOK_NUM_NONE:
            $toc .= html_writer::start_tag('div', array('class' => 'book_toc_none clearfix'));
            break;
        case BOOK_NUM_NUMBERS:
            $toc .= html_writer::start_tag('div', array('class' => 'book_toc_numbered clearfix'));
            break;
        case BOOK_NUM_BULLETS:
            $toc .= html_writer::start_tag('div', array('class' => 'book_toc_bullets clearfix'));
            break;
        case BOOK_NUM_INDENTED:
            $toc .= html_writer::start_tag('div', array('class' => 'book_toc_indented clearfix'));
            break;
    }

    if ($edit) { // Teacher's TOC
        $toc .= html_writer::start_tag('ul');
        $i = 0;
        foreach ($chapters as $ch) {
            $i++;
            $title = trim(format_string($ch->title, true, array('context'=>$context)));
            if (!$ch->subchapter) {

                if ($first) {
                    $toc .= html_writer::start_tag('li', array('class' => 'clearfix'));
                } else {
                    $toc .= html_writer::end_tag('ul');
                    $toc .= html_writer::end_tag('li');
                    $toc .= html_writer::start_tag('li', array('class' => 'clearfix'));
                }

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
                    $title = html_writer::tag('span', $title, array('class' => 'dimmed_text'));
                }
            } else {

                if ($first) {
                    $toc .= html_writer::start_tag('li', array('class' => 'clearfix'));
                    $toc .= html_writer::start_tag('ul');
                    $toc .= html_writer::start_tag('li', array('class' => 'clearfix'));
                } else {
                    $toc .= html_writer::start_tag('li', array('class' => 'clearfix'));
                }

                if (!$ch->hidden) {
                    $ns++;
                    if ($book->numbering == BOOK_NUM_NUMBERS) {
                        $title = "$nch.$ns $title";
                    }
                } else {
                    if ($book->numbering == BOOK_NUM_NUMBERS) {
                        if (empty($chapters[$ch->parent]->hidden)) {
                            $title = "$nch.x $title";
                        } else {
                            $title = "x.x $title";
                        }
                    }
                    $title = html_writer::tag('span', $title, array('class' => 'dimmed_text'));
                }
            }

            if ($ch->id == $chapter->id) {
                $toc .= html_writer::tag('strong', $title);
            } else {
                $toc .= html_writer::link(new moodle_url('view.php', array('id' => $cm->id, 'chapterid' => $ch->id)), $title, array('title' => s($title)));
            }

            $toc .= html_writer::start_tag('div', array('class' => 'action-list'));
            if ($i != 1) {
                $toc .= html_writer::link(new moodle_url('move.php', array('id' => $cm->id, 'chapterid' => $ch->id, 'up' => '1', 'sesskey' => $USER->sesskey)),
                                            $OUTPUT->pix_icon('t/up', get_string('up')), array('title' => get_string('up')));
            }
            if ($i != count($chapters)) {
                $toc .= html_writer::link(new moodle_url('move.php', array('id' => $cm->id, 'chapterid' => $ch->id, 'up' => '0', 'sesskey' => $USER->sesskey)),
                                            $OUTPUT->pix_icon('t/down', get_string('down')), array('title' => get_string('down')));
            }
            $toc .= html_writer::link(new moodle_url('edit.php', array('cmid' => $cm->id, 'id' => $ch->id)),
                                        $OUTPUT->pix_icon('t/edit', get_string('edit')), array('title' => get_string('edit')));
            $toc .= html_writer::link(new moodle_url('delete.php', array('id' => $cm->id, 'chapterid' => $ch->id, 'sesskey' => $USER->sesskey)),
                                        $OUTPUT->pix_icon('t/delete', get_string('delete')), array('title' => get_string('delete')));
            if ($ch->hidden) {
                $toc .= html_writer::link(new moodle_url('show.php', array('id' => $cm->id, 'chapterid' => $ch->id, 'sesskey' => $USER->sesskey)),
                                            $OUTPUT->pix_icon('t/show', get_string('show')), array('title' => get_string('show')));
            } else {
                $toc .= html_writer::link(new moodle_url('show.php', array('id' => $cm->id, 'chapterid' => $ch->id, 'sesskey' => $USER->sesskey)),
                                            $OUTPUT->pix_icon('t/hide', get_string('hide')), array('title' => get_string('hide')));
            }
            $toc .= html_writer::link(new moodle_url('edit.php', array('cmid' => $cm->id, 'pagenum' => $ch->pagenum, 'subchapter' => $ch->subchapter)),
                                            $OUTPUT->pix_icon('add', get_string('addafter', 'mod_book'), 'mod_book'), array('title' => get_string('addafter', 'mod_book')));
            $toc .= html_writer::end_tag('div');

            if (!$ch->subchapter) {
                $toc .= html_writer::start_tag('ul');
            } else {
                $toc .= html_writer::end_tag('li');
            }
            $first = 0;
        }

        $toc .= html_writer::end_tag('ul');
        $toc .= html_writer::end_tag('li');
        $toc .= html_writer::end_tag('ul');

    } else { // Normal students view
        $toc .= html_writer::start_tag('ul');
        foreach ($chapters as $ch) {
            $title = trim(format_string($ch->title, true, array('context'=>$context)));
            if (!$ch->hidden) {
                if (!$ch->subchapter) {
                    $nch++;
                    $ns = 0;

                    if ($first) {
                        $toc .= html_writer::start_tag('li', array('class' => 'clearfix'));
                    } else {
                        $toc .= html_writer::end_tag('ul');
                        $toc .= html_writer::end_tag('li');
                        $toc .= html_writer::start_tag('li', array('class' => 'clearfix'));
                    }

                    if ($book->numbering == BOOK_NUM_NUMBERS) {
                          $title = "$nch $title";
                    }
                } else {
                    $ns++;

                    if ($first) {
                        $toc .= html_writer::start_tag('li', array('class' => 'clearfix'));
                        $toc .= html_writer::start_tag('ul');
                        $toc .= html_writer::start_tag('li', array('class' => 'clearfix'));
                    } else {
                        $toc .= html_writer::start_tag('li', array('class' => 'clearfix'));
                    }

                    if ($book->numbering == BOOK_NUM_NUMBERS) {
                          $title = "$nch.$ns $title";
                    }
                }
                if ($ch->id == $chapter->id) {
                    $toc .= html_writer::tag('strong', $title);
                } else {
                    $toc .= html_writer::link(new moodle_url('view.php', array('id' => $cm->id, 'chapterid' => $ch->id)), $title, array('title' => s($title)));
                }

                if (!$ch->subchapter) {
                    $toc .= html_writer::start_tag('ul');
                } else {
                    $toc .= html_writer::end_tag('li');
                }

                $first = 0;
            }
        }

        $toc .= html_writer::end_tag('ul');
        $toc .= html_writer::end_tag('li');
        $toc .= html_writer::end_tag('ul');

    }

    $toc .= html_writer::end_tag('div');

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
        return $this->get_filtered_children('*', false, true);
    }

    /**
     * Help function to return files matching extensions or their count
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @param bool|int $countonly if false returns the children, if an int returns just the
     *    count of children but stops counting when $countonly number of children is reached
     * @param bool $returnemptyfolders if true returns items that don't have matching files inside
     * @return array|int array of file_info instances or the count
     */
    private function get_filtered_children($extensions = '*', $countonly = false, $returnemptyfolders = false) {
        global $DB;
        $params = array('contextid' => $this->context->id,
            'component' => 'mod_book',
            'filearea' => $this->filearea,
            'bookid' => $this->cm->instance);
        $sql = 'SELECT DISTINCT bc.id, bc.pagenum
                    FROM {files} f, {book_chapters} bc
                    WHERE f.contextid = :contextid
                    AND f.component = :component
                    AND f.filearea = :filearea
                    AND bc.bookid = :bookid
                    AND bc.id = f.itemid';
        if (!$returnemptyfolders) {
            $sql .= ' AND filename <> :emptyfilename';
            $params['emptyfilename'] = '.';
        }
        list($sql2, $params2) = $this->build_search_files_sql($extensions, 'f');
        $sql .= ' '.$sql2;
        $params = array_merge($params, $params2);
        if ($countonly === false) {
            $sql .= ' ORDER BY bc.pagenum';
        }

        $rs = $DB->get_recordset_sql($sql, $params);
        $children = array();
        foreach ($rs as $record) {
            if ($child = $this->browser->get_file_info($this->context, 'mod_book', $this->filearea, $record->id)) {
                if ($returnemptyfolders || $child->count_non_empty_children($extensions)) {
                    $children[] = $child;
                }
            }
            if ($countonly !== false && count($children) >= $countonly) {
                break;
            }
        }
        $rs->close();
        if ($countonly !== false) {
            return count($children);
        }
        return $children;
    }

    /**
     * Returns list of children which are either files matching the specified extensions
     * or folders that contain at least one such file.
     *
     * @param string|array $extensions, either '*' or array of lowercase extensions, i.e. array('.gif','.jpg')
     * @return array of file_info instances
     */
    public function get_non_empty_children($extensions = '*') {
        return $this->get_filtered_children($extensions, false);
    }

    /**
     * Returns the number of children which are either files matching the specified extensions
     * or folders containing at least one such file.
     *
     * @param string|array $extensions, for example '*' or array('.gif','.jpg')
     * @param int $limit stop counting after at least $limit non-empty children are found
     * @return int
     */
    public function count_non_empty_children($extensions = '*', $limit = 1) {
        return $this->get_filtered_children($extensions, $limit);
    }

    /**
     * Returns parent file_info instance
     * @return file_info or null for root
     */
    public function get_parent() {
        return $this->browser->get_file_info($this->context);
    }
}
