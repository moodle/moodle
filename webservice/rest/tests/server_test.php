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

namespace webservice_rest;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/rest/locallib.php');

/**
 * Rest server testcase.
 *
 * @package    webservice_rest
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class server_test extends \advanced_testcase {

    /**
     * Data provider for test_xmlize.
     * @return array
     */
    public function xmlize_provider() {
        $data = [];
        $data[] = [null, null, ''];
        $data[] = [new \external_value(PARAM_BOOL), false, "<VALUE>0</VALUE>\n"];
        $data[] = [new \external_value(PARAM_BOOL), true, "<VALUE>1</VALUE>\n"];
        $data[] = [new \external_value(PARAM_ALPHA), null, "<VALUE null=\"null\"/>\n"];
        $data[] = [new \external_value(PARAM_ALPHA), 'a', "<VALUE>a</VALUE>\n"];
        $data[] = [new \external_value(PARAM_INT), 123, "<VALUE>123</VALUE>\n"];
        $data[] = [
            new \external_multiple_structure(new \external_value(PARAM_INT)),
            [1, 2, 3],
            "<MULTIPLE>\n" .
            "<VALUE>1</VALUE>\n" .
            "<VALUE>2</VALUE>\n" .
            "<VALUE>3</VALUE>\n" .
            "</MULTIPLE>\n"
        ];
        $data[] = [ // Multiple structure with null value.
            new \external_multiple_structure(new \external_value(PARAM_ALPHA)),
            ['A', null, 'C'],
            "<MULTIPLE>\n" .
            "<VALUE>A</VALUE>\n" .
            "<VALUE null=\"null\"/>\n" .
            "<VALUE>C</VALUE>\n" .
            "</MULTIPLE>\n"
        ];
        $data[] = [ // Multiple structure without values.
            new \external_multiple_structure(new \external_value(PARAM_ALPHA)),
            [],
            "<MULTIPLE>\n" .
            "</MULTIPLE>\n"
        ];
        $data[] = [
            new \external_single_structure([
                'one' => new \external_value(PARAM_INT),
                'two' => new \external_value(PARAM_INT),
                'three' => new \external_value(PARAM_INT),
            ]),
            ['one' => 1, 'two' => 2, 'three' => 3],
            "<SINGLE>\n" .
            "<KEY name=\"one\"><VALUE>1</VALUE>\n</KEY>\n" .
            "<KEY name=\"two\"><VALUE>2</VALUE>\n</KEY>\n" .
            "<KEY name=\"three\"><VALUE>3</VALUE>\n</KEY>\n" .
            "</SINGLE>\n"
        ];
        $data[] = [ // Single structure with null value.
            new \external_single_structure([
                'one' => new \external_value(PARAM_INT),
                'two' => new \external_value(PARAM_INT),
                'three' => new \external_value(PARAM_INT),
            ]),
            ['one' => 1, 'two' => null, 'three' => 3],
            "<SINGLE>\n" .
            "<KEY name=\"one\"><VALUE>1</VALUE>\n</KEY>\n" .
            "<KEY name=\"two\"><VALUE null=\"null\"/>\n</KEY>\n" .
            "<KEY name=\"three\"><VALUE>3</VALUE>\n</KEY>\n" .
            "</SINGLE>\n"
        ];
        $data[] = [ // Single structure missing keys.
            new \external_single_structure([
                'one' => new \external_value(PARAM_INT),
                'two' => new \external_value(PARAM_INT),
                'three' => new \external_value(PARAM_INT),
            ]),
            ['two' => null, 'three' => 3],
            "<SINGLE>\n" .
            "<KEY name=\"one\"><VALUE null=\"null\"/>\n</KEY>\n" .
            "<KEY name=\"two\"><VALUE null=\"null\"/>\n</KEY>\n" .
            "<KEY name=\"three\"><VALUE>3</VALUE>\n</KEY>\n" .
            "</SINGLE>\n"
        ];
        $data[] = [ // Nested structure.
            new \external_single_structure([
                'one' => new \external_multiple_structure(
                    new \external_value(PARAM_INT)
                ),
                'two' => new \external_multiple_structure(
                    new \external_single_structure([
                        'firstname' => new \external_value(PARAM_RAW),
                        'lastname' => new \external_value(PARAM_RAW),
                    ])
                ),
                'three' => new \external_single_structure([
                    'firstname' => new \external_value(PARAM_RAW),
                    'lastname' => new \external_value(PARAM_RAW),
                ]),
            ]),
            [
                'one' => [2, 3, 4],
                'two' => [
                    ['firstname' => 'Louis', 'lastname' => 'Armstrong'],
                    ['firstname' => 'Neil', 'lastname' => 'Armstrong'],
                ],
                'three' => ['firstname' => 'Neil', 'lastname' => 'Armstrong'],
            ],
            "<SINGLE>\n" .
            "<KEY name=\"one\"><MULTIPLE>\n".
                "<VALUE>2</VALUE>\n" .
                "<VALUE>3</VALUE>\n" .
                "<VALUE>4</VALUE>\n" .
            "</MULTIPLE>\n</KEY>\n" .
            "<KEY name=\"two\"><MULTIPLE>\n".
                "<SINGLE>\n" .
                    "<KEY name=\"firstname\"><VALUE>Louis</VALUE>\n</KEY>\n" .
                    "<KEY name=\"lastname\"><VALUE>Armstrong</VALUE>\n</KEY>\n" .
                "</SINGLE>\n" .
                "<SINGLE>\n" .
                    "<KEY name=\"firstname\"><VALUE>Neil</VALUE>\n</KEY>\n" .
                    "<KEY name=\"lastname\"><VALUE>Armstrong</VALUE>\n</KEY>\n" .
                "</SINGLE>\n" .
            "</MULTIPLE>\n</KEY>\n" .
            "<KEY name=\"three\"><SINGLE>\n" .
                "<KEY name=\"firstname\"><VALUE>Neil</VALUE>\n</KEY>\n" .
                "<KEY name=\"lastname\"><VALUE>Armstrong</VALUE>\n</KEY>\n" .
            "</SINGLE>\n</KEY>\n" .
            "</SINGLE>\n"
        ];
        $data[] = [ // Nested structure with missing keys.
            new \external_single_structure([
                'one' => new \external_multiple_structure(
                    new \external_value(PARAM_INT)
                ),
                'two' => new \external_multiple_structure(
                    new \external_single_structure([
                        'firstname' => new \external_value(PARAM_RAW),
                        'lastname' => new \external_value(PARAM_RAW),
                    ])
                ),
                'three' => new \external_single_structure([
                    'firstname' => new \external_value(PARAM_RAW),
                    'lastname' => new \external_value(PARAM_RAW),
                ]),
            ]),
            [
                'two' => [
                    ['firstname' => 'Louis'],
                    ['lastname' => 'Armstrong'],
                ],
                'three' => ['lastname' => 'Armstrong'],
            ],
            "<SINGLE>\n" .
            "<KEY name=\"one\"><MULTIPLE>\n</MULTIPLE>\n</KEY>\n" .
            "<KEY name=\"two\"><MULTIPLE>\n".
                "<SINGLE>\n" .
                    "<KEY name=\"firstname\"><VALUE>Louis</VALUE>\n</KEY>\n" .
                    "<KEY name=\"lastname\"><VALUE null=\"null\"/>\n</KEY>\n" .
                "</SINGLE>\n" .
                "<SINGLE>\n" .
                    "<KEY name=\"firstname\"><VALUE null=\"null\"/>\n</KEY>\n" .
                    "<KEY name=\"lastname\"><VALUE>Armstrong</VALUE>\n</KEY>\n" .
                "</SINGLE>\n" .
            "</MULTIPLE>\n</KEY>\n" .
            "<KEY name=\"three\"><SINGLE>\n" .
                "<KEY name=\"firstname\"><VALUE null=\"null\"/>\n</KEY>\n" .
                "<KEY name=\"lastname\"><VALUE>Armstrong</VALUE>\n</KEY>\n" .
            "</SINGLE>\n</KEY>\n" .
            "</SINGLE>\n"
        ];
        return $data;
    }

    /**
     * @dataProvider xmlize_provider
     * @param external_description $description The data structure.
     * @param mixed $value The value to xmlise.
     * @param mixed $expected The expected output.
     */
    public function test_xmlize($description, $value, $expected) {
        $method = new \ReflectionMethod('webservice_rest_server', 'xmlize_result');
        $method->setAccessible(true);
        $this->assertEquals($expected, $method->invoke(null, $value, $description));
    }

}
