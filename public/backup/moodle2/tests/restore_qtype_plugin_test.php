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

namespace core;

/**
 * Tests for question type restore methods
 *
 * @package   core
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \restore_qtype_plugin
 */
final class restore_qtype_plugin_test extends \basic_testcase {
    /**
     * All default and specified fields should be removed from the provided data structure.
     */
    public function test_remove_excluded_question_data(): void {
        global $CFG;
        require_once($CFG->dirroot . '/backup/moodle2/restore_plugin.class.php');
        require_once($CFG->dirroot . '/backup/moodle2/restore_qtype_plugin.class.php');
        $data = (object) [
            // Default excluded fields should be removed.
            'id' => 1,
            'createdby' => 2,
            'modifiedby' => 3,
            // This field is not specified for removal, it should remain.
            'questiontext' => 'Some question text',
            // Excluded paths that address an array should operate on all items in the array.
            'hints' => [
                (object) [
                    'id' => 4,
                    'questionid' => 1,
                    // This field is not specified for removal.
                    'text' => 'Lorem ipsum',
                ],
                (object) [
                    'id' => 5,
                    'questionid' => 1,
                    'text' => 'Lorem ipsum',
                ],
            ],
            'options' => [ // This is an array of arrays, rather than an array of objects. It should be handled the same.
                [
                    'id' => 6,
                    'questionid' => 1,
                    // This field is not specified for removal.
                    'option' => true,
                ],
                [
                    'id' => 7,
                    'questionid' => 1,
                    'option' => false,
                ],
                [
                    'id' => 8,
                    'questionid' => 1,
                    'option' => false,
                ],
            ],
            'custom1' => 'Some custom text',
            // This field is not specified for removal.
            'custom2' => 'Some custom text2',
            // Fields specified for removal should be removed even if they contain null values.
            'custom3' => null,
            'customarray' => [
                (object) [
                    // Null values should also be removed.
                    'id' => null,
                    // This field is not specified for removal.
                    'text' => 'Custom item text',
                ],
                (object) [
                    'id' => null,
                    'text' => 'Custom item text2',
                ],
            ],
            'customstructure' => [ // This array contains scalar values, not a list of objects/arrays.
                'id' => null,
                'text' => 'Custom structure text',
                'number' => 1,
                'bool' => true,
            ],
        ];

        $expecteddata = (object) [
            'questiontext' => 'Some question text',
            'hints' => [
                (object) [
                    'text' => 'Lorem ipsum',
                ],
                (object) [
                    'text' => 'Lorem ipsum',
                ],
            ],
            'options' => [
                [
                    'option' => true,
                ],
                [
                    'option' => false,
                ],
                [
                    'option' => false,
                ],
            ],
            'custom2' => 'Some custom text2',
            'customarray' => [
                (object) [
                    'text' => 'Custom item text',
                ],
                (object) [
                    'text' => 'Custom item text2',
                ],
            ],
            'customstructure' => [
                'text' => 'Custom structure text',
                'number' => 1,
            ],
        ];

        $excludedfields = [
            '/custom1',
            '/custom3',
            '/customarray/id',
            '/customstructure/id',
            '/customstructure/bool',
            // A field that is not in the data structure will be ignored.
            '/custom4',
        ];
        $this->assertEquals($expecteddata, \restore_qtype_plugin::remove_excluded_question_data($data, $excludedfields));
    }
}
