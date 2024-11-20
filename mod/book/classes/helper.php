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
namespace mod_book;

/**
 * Book helper
 *
 * @package    mod_book
 * @copyright  2023 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /**
     * Check if we are on the last visible chapter of the book.
     *
     * @param int $chapterid
     * @param array $chapters chapter list provided by book_preload_chapters
     * @see book_preload_chapters
     * @return bool
     */
    public static function is_last_visible_chapter(int $chapterid, array $chapters): bool {
        $lastchapterid = 0;
        foreach ($chapters as $ch) {
            if ($ch->hidden) {
                continue;
            }
            $lastchapterid = $ch->id;
        }
        return $chapterid == $lastchapterid;
    }
}
