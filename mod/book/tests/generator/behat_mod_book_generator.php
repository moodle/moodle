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
 * Behat data generator for mod_book.
 *
 * @package   mod_book
 * @category  test
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Behat data generator for mod_book.
 *
 * @copyright 2019 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_book_generator extends behat_generator_base {

    protected function get_creatable_entities(): array {
        return [
            'chapters' => [
                'singular' => 'chapter',
                'datagenerator' => 'chapter',
                'required' => ['book', 'title', 'content'],
                'switchids' => ['book' => 'bookid'],
            ],
        ];
    }

    /**
     * Look up the id of a book from its name.
     *
     * @param string $bookname the book name, for example 'Test book'.
     * @return int corresponding id.
     */
    protected function get_book_id(string $bookname): int {
        global $DB;

        $cm = $this->get_cm_by_activity_name('book', $bookname);

        return $cm->instance;
    }
}
