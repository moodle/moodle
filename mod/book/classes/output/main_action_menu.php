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
 * Output the action menu for this activity.
 *
 * @package   mod_book
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_book\output;

use templatable;
use renderable;
use moodle_url;
use stdClass;

/**
 * Output the action menu for the book activity.
 *
 * @package   mod_book
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main_action_menu implements templatable, renderable {

    /** @var int The course module ID. */
    protected $cmid;
    /** @var stdClass[] Chapters of the book. */
    protected $chapters;
    /** @var stdClass Current chapter of the book. */
    protected $chapter;

    /**
     * Constructor for this class.
     *
     * @param int      $cmid     The course module ID.
     * @param array    $chapters Chapters of this book.
     * @param stdClass $chapter  The current chapter.
     */
    public function __construct(int $cmid, array $chapters, stdClass $chapter) {
        $this->cmid = $cmid;
        $this->chapters = $chapters;
        $this->chapter = $chapter;
    }

    /**
     * Get the next chapter in the book.
     *
     * @return ?stdClass The next chapter of the book.
     */
    protected function get_next_chapter(): ?stdClass {
        $nextpageid = $this->chapter->pagenum + 1;
        // Early return if the current chapter is also the last chapter.
        if ($nextpageid > count($this->chapters)) {
            return null;
        }
        while ((!$nextchapter = $this->get_chapter($nextpageid))) {
            // Break the loop if this is the last chapter.
            if ($nextpageid === count($this->chapters)) {
                break;
            }
            $nextpageid++;
        }
        return $nextchapter;
    }

    /**
     * Get the previous chapter in the book.
     *
     * @return ?stdClass The previous chapter of the book.
     */
    protected function get_previous_chapter(): ?stdClass {
        $prevpageid = $this->chapter->pagenum - 1;
        // Early return if the current chapter is also the first chapter.
        if ($prevpageid < 1) {
            return null;
        }
        while ((!$prevchapter = $this->get_chapter($prevpageid))) {
            // Break the loop if this is the first chapter.
            if ($prevpageid === 1) {
                break;
            }
            $prevpageid--;
        }
        return $prevchapter;
    }

    /**
     * Get the specific chapter of the book.
     *
     * @param int $id The chapter id to retrieve.
     * @return ?stdClass The requested chapter.
     */
    protected function get_chapter(int $id): ?stdClass {
        $context = \context_module::instance($this->cmid);
        $viewhidden = has_capability('mod/book:viewhiddenchapters', $context);

        foreach ($this->chapters as $chapter) {
            // Also make sure that the chapter is not hidden or the user can view hidden chapters before returning
            // the chapter object.
            if (($chapter->pagenum == $id) && (!$chapter->hidden || $viewhidden)) {
                return $chapter;
            }
        }
        return null;
    }

    /**
     * Exports the navigation buttons around the book.
     *
     * @param \renderer_base $output renderer base output.
     * @return array Data to render.
     */
    public function export_for_template(\renderer_base $output): array {
        $next = $this->get_next_chapter();
        $previous = $this->get_previous_chapter();

        $context = \context_module::instance($this->cmid);
        $data = [];

        if ($next) {
            $nextdata = [
                'title' => get_string('navnext', 'mod_book'),
                'url' => (new moodle_url('/mod/book/view.php', ['id' => $this->cmid, 'chapterid' => $next->id]))->out(false)
            ];
            $data['next'] = $nextdata;
        }
        if ($previous) {
            $previousdata = [
                'title' => get_string('navprev', 'mod_book'),
                'url' => (new moodle_url('/mod/book/view.php', ['id' => $this->cmid, 'chapterid' => $previous->id]))->out(false)
            ];
            $data['previous'] = $previousdata;
        }

        return $data;
    }
}
