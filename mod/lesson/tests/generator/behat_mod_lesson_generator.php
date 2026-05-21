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
            'attempts' => [
                'singular' => 'attempt',
                'datagenerator' => 'attempt',
                'required' => ['lesson', 'user'],
                'switchids' => ['lesson' => 'lessonid', 'user' => 'userid'],
            ],
            'user overrides' => [
                'singular' => 'user override',
                'datagenerator' => 'override',
                'required' => ['lesson', 'user'],
                'switchids' => ['lesson' => 'lessonid', 'user' => 'userid'],
            ],
            'group overrides' => [
                'singular' => 'group override',
                'datagenerator' => 'override',
                'required' => ['lesson', 'group'],
                'switchids' => ['lesson' => 'lessonid', 'group' => 'groupid'],
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

    /**
     * Preprocess attempt data.
     *
     * @param array $data
     * @return array
     */
    protected function preprocess_attempt(array $data): array {
        global $DB;

        if (isset($data['user'])) {
            $data['userid'] = $DB->get_field('user', 'id', ['username' => $data['user']], MUST_EXIST);
            unset($data['user']);
        }

        if (isset($data['lesson'])) {
            $data['lessonid'] = $this->get_lesson_id($data['lesson']);
            unset($data['lesson']);
        }

        if (isset($data['page']) && isset($data['lessonid'])) {
            $data['pageid'] = $DB->get_field(
                'lesson_pages',
                'id',
                ['title' => $data['page'], 'lessonid' => $data['lessonid']],
                MUST_EXIST
            );
            unset($data['page']);
        }

        if (isset($data['answer']) && isset($data['pageid'])) {
            // The 'answer' field is a TEXT column, so we must use sql_compare_text to query it.
            $select = $DB->sql_compare_text('answer') . ' = ' . $DB->sql_compare_text(':answer') .
                ' AND pageid = :pageid';
            $params = [
                'answer' => $data['answer'],
                'pageid' => $data['pageid'],
            ];
            $data['answerid'] = $DB->get_field_select('lesson_answers', 'id', $select, $params, MUST_EXIST);
            unset($data['answer']);
        }

        return $data;
    }
}
