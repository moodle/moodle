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
 * Defines the renderer for the book print tool.
 *
 * @package    booktool_print
 * @copyright  2019 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace booktool_print\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use html_writer;
use context_module;
use moodle_url;
use moodle_exception;

/**
 * The renderer for the book print tool.
 *
 * @copyright  2019 Mihail Geshoski
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Render the print book page.
     *
     * @param print_book_page $page
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_print_book_page(print_book_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('booktool_print/print_book', $data);
    }

    /**
     * Render the print book chapter page.
     *
     * @param print_book_chapter_page $page
     * @return string html for the page
     * @throws moodle_exception
     */
    public function render_print_book_chapter_page(print_book_chapter_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('booktool_print/print_book_chapter', $data);
    }

    /**
     * Render the print book chapter link.
     *
     * @return string html for the link
     */
    public function render_print_book_chapter_dialog_link() {
        $printtext = get_string('printchapter', 'booktool_print');
        $printicon = $this->output->pix_icon('chapter', $printtext, 'booktool_print',
                array('class' => 'icon'));
        $printlinkatt = array('onclick' => 'window.print();return false;', 'class' => 'hidden-print');
        return html_writer::link('#', $printicon . $printtext, $printlinkatt);
    }

    /**
     * Render the print book link.
     *
     * @return string html for the link
     */
    public function render_print_book_dialog_link() {
        $printtext = get_string('printbook', 'booktool_print');
        $printicon = $this->output->pix_icon('book', $printtext, 'booktool_print',
            array('class' => 'icon'));
        $printlinkatt = array('onclick' => 'window.print();return false;', 'class' => 'hidden-print');
        return html_writer::link('#', $printicon . $printtext, $printlinkatt);
    }

    /**
     * Render the print book table of contents.
     *
     * @param array $chapters Array of book chapters
     * @param object $book The book object
     * @param object $cm The curse module object
     * @return string html for the TOC
     */
    public function render_print_book_toc($chapters, $book, $cm) {

        $first = true;

        $context = context_module::instance($cm->id);

        $toc = ''; // Representation of toc (HTML).

        switch ($book->numbering) {
            case BOOK_NUM_NONE:
                $toc .= html_writer::start_tag('div', array('class' => 'book_toc_none'));
                break;
            case BOOK_NUM_NUMBERS:
                $toc .= html_writer::start_tag('div', array('class' => 'book_toc_numbered'));
                break;
            case BOOK_NUM_BULLETS:
                $toc .= html_writer::start_tag('div', array('class' => 'book_toc_bullets'));
                break;
            case BOOK_NUM_INDENTED:
                $toc .= html_writer::start_tag('div', array('class' => 'book_toc_indented'));
                break;
        }

        $toc .= html_writer::tag('a', '', array('name' => 'toc')); // Representation of toc (HTML).

        $toc .= html_writer::tag('h2', get_string('toc', 'mod_book'), ['class' => 'text-center pb-5']);
        $toc .= html_writer::start_tag('ul');
        foreach ($chapters as $ch) {
            if (!$ch->hidden) {
                $title = book_get_chapter_title($ch->id, $chapters, $book, $context);
                if (!$ch->subchapter) {

                    if ($first) {
                        $toc .= html_writer::start_tag('li');
                    } else {
                        $toc .= html_writer::end_tag('ul');
                        $toc .= html_writer::end_tag('li');
                        $toc .= html_writer::start_tag('li');
                    }

                } else {

                    if ($first) {
                        $toc .= html_writer::start_tag('li');
                        $toc .= html_writer::start_tag('ul');
                        $toc .= html_writer::start_tag('li');
                    } else {
                        $toc .= html_writer::start_tag('li');
                    }

                }

                if (!$ch->subchapter) {
                    $toc .= html_writer::link(new moodle_url('#ch' . $ch->id), $title,
                            array('title' => s($title), 'class' => 'font-weight-bold text-decoration-none'));
                    $toc .= html_writer::start_tag('ul');
                } else {
                    $toc .= html_writer::link(new moodle_url('#ch' . $ch->id), $title,
                            array('title' => s($title), 'class' => 'text-decoration-none'));
                    $toc .= html_writer::end_tag('li');
                }
                $first = false;
            }
        }

        $toc .= html_writer::end_tag('ul');
        $toc .= html_writer::end_tag('li');
        $toc .= html_writer::end_tag('ul');
        $toc .= html_writer::end_tag('div');

        $toc = str_replace('<ul></ul>', '', $toc); // Cleanup of invalid structures.

        return $toc;
    }

    /**
     * Render the print book chapter.
     *
     * @param object $chapter The book chapter object
     * @param array $chapters The array of book chapters
     * @param object $book The book object
     * @param object $cm The course module object
     * @return array The array containing the content of the book chapter and visibility information
     */
    public function render_print_book_chapter($chapter, $chapters, $book, $cm) {
        $context = context_module::instance($cm->id);
        $title = book_get_chapter_title($chapter->id, $chapters, $book, $context);

        $chaptervisible = $chapter->hidden ? false : true;

        $bookchapter = '';
        $bookchapter .= html_writer::start_div('book_chapter pt-3', ['id' => 'ch' . $chapter->id]);
        if (!$book->customtitles) {
            if (!$chapter->subchapter) {
                $bookchapter .= $this->output->heading($title, 2, 'text-center pb-5');
            } else {
                $bookchapter .= $this->output->heading($title, 3, 'text-center pb-5');
            }
        }

        $chaptertext = file_rewrite_pluginfile_urls($chapter->content, 'pluginfile.php', $context->id,
            'mod_book', 'chapter', $chapter->id);
        $bookchapter .= format_text($chaptertext, $chapter->contentformat, array('noclean' => true, 'context' => $context));
        $bookchapter .= html_writer::end_div();

        return array($bookchapter, $chaptervisible);
    }
}
