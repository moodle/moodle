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
 * Class containing data for the view book page.
 *
 * @package    booktool_print
 * @copyright  2019 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace booktool_print\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use stdClass;
use templatable;
use context_module;

/**
 * Class containing data for the print book page.
 *
 * @copyright  2019 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class print_book_chapter_page implements renderable, templatable {

    /**
     * @var object $book The book object.
     */
    protected $book;

    /**
     * @var object $cm The course module object.
     */
    protected $cm;

    /**
     * @var object $chapter The book chapter object.
     */
    protected $chapter;

    /**
     * Construct this renderable.
     *
     * @param object $book The book
     * @param object $cm The course module
     * @param object $chapter The book chapter
     */
    public function __construct($book, $cm, $chapter) {
        $this->book = $book;
        $this->cm = $cm;
        $this->chapter = $chapter;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $OUTPUT;

        $context = context_module::instance($this->cm->id);
        $chapters = book_preload_chapters($this->book);

        $data = new stdClass();
        // Print dialog link.
        $data->printdialoglink = $output->render_print_book_chapter_dialog_link();
        $data->booktitle = $OUTPUT->heading(format_string($this->book->name, true,
                array('context' => $context)), 1);
        if (!$this->book->customtitles) {
            // If the current chapter is a subchapter, get the title of the parent chapter.
            if ($this->chapter->subchapter) {
                $parentchaptertitle = book_get_chapter_title($chapters[$this->chapter->id]->parent, $chapters,
                        $this->book, $context);
                $data->parentchaptertitle = $OUTPUT->heading(format_string($parentchaptertitle, true,
                        array('context' => $context)), 2);
            }
        }

        list($chaptercontent, $chaptervisible) = $output->render_print_book_chapter($this->chapter, $chapters,
                $this->book, $this->cm);
        $chapter = new stdClass();
        $chapter->content = $chaptercontent;
        $chapter->visible = $chaptervisible;
        $data->chapter = $chapter;

        return $data;
    }
}
