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

namespace mod_forum;

use mod_forum\local\entities\sorter as sorter_entity;

/**
 * The discussion entity tests.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class entities_sorter_test extends \advanced_testcase {
    /**
     * Test the entity returns expected values.
     */
    public function test_entity_sort_into_children() {
        $this->resetAfterTest();
        $sorter = new sorter_entity(
            function($entity) {
                return $entity['id'];
            },
            function($entity) {
                return $entity['parent'];
            }
        );

        $a = ['id' => 1, 'parent' => 0];
        $b = ['id' => 2, 'parent' => 1];
        $c = ['id' => 3, 'parent' => 1];
        $d = ['id' => 4, 'parent' => 2];
        $e = ['id' => 5, 'parent' => 0];

        $expected = [
            [$e, []],
            [$a, [[$b, [[$d, []]]], [$c, []]]],
        ];

        $actual = $sorter->sort_into_children([$d, $b, $e, $a, $c]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the entity returns expected values.
     */
    public function test_entity_flatten_children() {
        $this->resetAfterTest();
        $sorter = new sorter_entity(
            function($entity) {
                return $entity['id'];
            },
            function($entity) {
                return $entity['parent'];
            }
        );

        $a = ['id' => 1, 'parent' => 0];
        $b = ['id' => 2, 'parent' => 1];
        $c = ['id' => 3, 'parent' => 1];
        $d = ['id' => 4, 'parent' => 3];

        $sorted = [
            [$a, [[$b, [[$d, []]]], [$c, []]]]
        ];

        $expected = [$a, $b, $d, $c];
        $actual = $sorter->flatten_children($sorted);

        $this->assertEquals($expected, $actual);
    }
}
