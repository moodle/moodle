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
 * PHP lang parser test.
 *
 * @package    tool_customlang
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_customlang\local\mlang;

use advanced_testcase;
use moodle_exception;

/**
 * PHP lang parser test class.
 *
 * @package    tool_customlang
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phpparser_test extends advanced_testcase {


    /**
     * Test get instance static method.
     *
     */
    public function test_get_instance(): void {

        $instance = phpparser::get_instance();

        $this->assertInstanceOf('tool_customlang\local\mlang\phpparser', $instance);
        $this->assertEquals($instance, phpparser::get_instance());
    }

    /**
     * Test get instance parse method.
     *
     * @dataProvider parse_provider
     * @param string $phpcode PHP code to test
     * @param array $expected Expected result
     * @param bool $exception if an exception is expected
     */
    public function test_parse(string $phpcode, array $expected, bool $exception): void {

        $instance = phpparser::get_instance();

        if ($exception) {
            $this->expectException(moodle_exception::class);
        }

        $strings = $instance->parse($phpcode);

        $this->assertEquals(count($expected), count($strings));
        foreach ($strings as $key => $langstring) {
            $this->assertEquals($expected[$key][0], $langstring->id);
            $this->assertEquals($expected[$key][1], $langstring->text);
        }
    }

    /**
     * Data provider for the test_parse.
     *
     * @return  array
     */
    public function parse_provider(): array {
        return [
            'Invalid PHP code' => [
                'No PHP code', [], false
            ],
            'No PHP open tag' => [
                "\$string['example'] = 'text';\n", [], false
            ],
            'One string code' => [
                "<?php \$string['example'] = 'text';\n", [['example', 'text']], false
            ],
            'Extra spaces' => [
                "<?php \$string['example']   =   'text';\n", [['example', 'text']], false
            ],
            'Extra tabs' => [
                "<?php \$string['example']\t=\t'text';\n", [['example', 'text']], false
            ],
            'Double quote string' => [
                "<?php
                    \$string['example'] = \"text\";
                    \$string[\"example2\"] = 'text2';
                    \$string[\"example3\"] = \"text3\";
                ", [
                    ['example', 'text'],
                    ['example2', 'text2'],
                    ['example3', 'text3'],
                ], false
            ],
            'Multiple lines strings' => [
                "<?php
                    \$string['example'] = 'First line\nsecondline';
                    \$string['example2'] = \"First line\nsecondline2\";
                ", [
                    ['example', "First line\nsecondline"],
                    ['example2', "First line\nsecondline2"],
                ], false
            ],
            'Two strings code' => [
                "<?php
                    \$string['example'] = 'text';
                    \$string['example2'] = 'text2';
                ", [
                    ['example', 'text'],
                    ['example2', 'text2'],
                ], false
            ],
            'Scaped characters' => [
                "<?php
                    \$string['example'] = 'Thos are \\' quotes \" 1';
                    \$string['example2'] = \"Thos are ' quotes \\\" 2\";
                ", [
                    ['example', "Thos are ' quotes \" 1"],
                    ['example2', "Thos are ' quotes \" 2"],
                ], false
            ],
            'PHP with single line comments' => [
                "<?php
                    // This is a comment.
                    \$string['example'] = 'text';
                    // This is another commment.
                ", [
                    ['example', 'text'],
                ], false
            ],
            'PHP with block comments' => [
                "<?php
                    /* This is a block comment. */
                    \$string['example'] = 'text';
                    /* This is another
                    block comment. */
                ", [
                    ['example', 'text'],
                ], false
            ],
            'Wrong variable name' => [
                "<?php
                    \$stringwrong['example'] = 'text';
                    \$wringstring['example'] = 'text';
                ", [], false
            ],
            'Single line commented valid line' => [
                "<?php
                    // \$string['example'] = 'text';
                ", [], false
            ],
            'Block commented valid line' => [
                "<?php
                    /*
                    \$string['example'] = 'text';
                    */
                ", [], false
            ],
            'Syntax error 1 (double assignation)' => [
                "<?php
                    \$string['example'] = 'text' = 'wrong';
                ", [], true
            ],
            'Syntax error 2 (no closing string)' => [
                "<?php
                    \$string['example'] = 'wrong;
                ", [], true
            ],
            'Syntax error 3 (Array without key)' => [
                "<?php
                    \$string[] = 'wrong';
                ", [], true
            ],
            'Syntax error 4 (Array not open)' => [
                "<?php
                    \$string'example'] = 'wrong';
                ", [], true
            ],
            'Syntax error 5 (Array not closed)' => [
                "<?php
                    \$string['example' = 'wrong';
                ", [], true
            ],
            'Syntax error 6 (Missing assignment)' => [
                "<?php
                    \$string['example'] 'wrong';
                ", [], true
            ],
        ];
    }

}
