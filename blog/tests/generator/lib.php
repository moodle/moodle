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
 * Generator for blog area.
 *
 * @package   core_blog
 * @category  test
 * @copyright 2022 Noel De Martin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blog/locallib.php');

/**
 * Blog module test data generator class
 *
 * @package    core_blog
 * @category   test
 * @copyright  2022 Noel De Martin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_blog_generator extends component_generator_base {

    /**
     * Create a blog entry
     *
     * @param array $data Entry data.
     * @return blog_entry Entry instance.
     */
    public function create_entry(array $data = []): blog_entry {
        $data['publishstate'] = $data['publishstate'] ?? 'site';
        $data['summary'] = $data['summary'] ?? $data['body'];

        $entry = new blog_entry(null, $data);
        $entry->add();

        return $entry;
    }
}
