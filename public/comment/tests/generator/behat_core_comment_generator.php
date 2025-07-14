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

declare(strict_types=1);

/**
 * Behat data generator for comments
 *
 * @package     core_comment
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_core_comment_generator extends behat_generator_base {

    /**
     * Get a list of the entities that can be created for this component
     *
     * @return array[]
     */
    protected function get_creatable_entities(): array {
        return [
            'Comments' => [
                'singular' => 'Comment',
                'datagenerator' => 'comment',
                'required' => [
                    'contextlevel',
                    'reference',
                    'component',
                    'area',
                    'content',
                ],
            ],
        ];
    }

    /**
     * Pre-process comment, populate context property
     *
     * @param array $comment
     * @return array
     */
    protected function preprocess_comment(array $comment): array {
        $comment['context'] = $this->get_context($comment['contextlevel'], $comment['reference']);
        unset($comment['contextlevel'], $comment['reference']);

        return $comment;
    }
}
