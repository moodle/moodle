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

namespace core_backup;

use backup_structure_dbops;

/**
 * Tests for backup_structure_dbops
 *
 * @package    core_backup
 * @category   test
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \backup_structure_dbops
 */
final class backup_structure_dbops_test extends \advanced_testcase {
    public static function setUpBeforeClass(): void {
        global $CFG;
        parent::setUpBeforeClass();
        require_once("{$CFG->dirroot}/backup/util/includes/backup_includes.php");
    }

    /**
     * Tests for convert_params_to_values.
     *
     * @dataProvider convert_params_to_values_provider
     * @param array $params
     * @param mixed $processor
     * @param array $expected
     */
    public function test_convert_params_to_values(
        array $params,
        $processor,
        array $expected,
    ): void {
        if (is_callable($processor)) {
            $newprocessor = $this->createMock(\backup_structure_processor::class);
            $newprocessor->method('get_var')->willReturnCallback($processor);
            $processor = $newprocessor;
        }

        $result = backup_structure_dbops::convert_params_to_values($params, $processor);

        $this->assertEqualsCanonicalizing($expected, $result);
    }

    /**
     * Data provider for convert_params_to_values_provider.
     */
    public static function convert_params_to_values_provider(): array {
        return [
            'String value is not processed' => [
                ['/0/1/2/345'],
                null,
                ['/0/1/2/345'],
            ],
            'Positive integer' => [
                [123, 456],
                null,
                [123, 456],
            ],
            'Negative integer' => [
                [-42],
                function () {
                    return 'Life, the Universe, and Everything';
                },
                ['Life, the Universe, and Everything'],
            ],
            'Mix of strings, and ints with a processor' => [
                ['foo', 123, 'bar', -42],
                function () {
                    return 'Life, the Universe, and Everything';
                },
                ['foo', 123, 'bar', 'Life, the Universe, and Everything'],
            ],
        ];
    }

    /**
     * Tests for convert_params_to_values with an atom.
     */
    public function test_convert_params_to_values_with_atom(): void {
        $atom = $this->createMock(\base_atom::class);
        $atom->method('is_set')->willReturn(true);
        $atom->method('get_value')->willReturn('Some atomised value');

        $result = backup_structure_dbops::convert_params_to_values([$atom], null);

        $this->assertEqualsCanonicalizing(['Some atomised value'], $result);
    }

    /**
     * Tests for convert_params_to_values with an atom without any value.
     */
    public function test_convert_params_to_values_with_atom_no_value(): void {
        $atom = $this->createMock(\base_atom::class);
        $atom->method('is_set')->willReturn(false);
        $atom->method('get_name')->willReturn('Atomisd name');

        $this->expectException(\base_element_struct_exception::class);
        backup_structure_dbops::convert_params_to_values([$atom], null);
    }
}
