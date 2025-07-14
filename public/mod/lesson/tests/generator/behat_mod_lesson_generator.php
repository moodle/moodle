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
 * Behat data generator for mod_lesson.
 *
 * @package   mod_lesson
 * @category  test
 * @copyright 2023 Dani Palou <dani@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Behat data generator for mod_lesson.
 *
 * @copyright 2023 Dani Palou <dani@nmoodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_mod_lesson_generator extends behat_generator_base {

    /**
     * Get a list of the entities that can be created.
     *
     * @return array entity name => information about how to generate.
     */
    protected function get_creatable_entities(): array {
        return [
            'pages' => [
                'singular' => 'page',
                'datagenerator' => 'page',
                'required' => ['lesson', 'qtype'],
                'switchids' => ['lesson' => 'lessonid'],
            ],
            'answers' => [
                'singular' => 'answer',
                'datagenerator' => 'answer',
                'required' => ['page'],
            ],
            'submissions' => [
                'singular' => 'submission',
                'datagenerator' => 'submission',
                'required' => ['lesson', 'user'],
                'switchids' => ['lesson' => 'lessonid', 'user' => 'userid'],
            ],
        ];
    }

    /**
     * Look up the id of a lesson from its name.
     *
     * @param string $idnumberorname the lesson idnumber or name, for example 'Test lesson'.
     * @return int corresponding id.
     */
    protected function get_lesson_id(string $idnumberorname): int {
        return $this->get_cm_by_activity_name('lesson', $idnumberorname)->instance;
    }

}
