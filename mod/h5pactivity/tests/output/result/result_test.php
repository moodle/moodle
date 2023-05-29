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
namespace mod_h5pactivity\output\result;

use core_xapi\local\statement\item_result;
use core_xapi\xapi_exception;
use mod_h5pactivity\local\manager;

/**
 * Result test class for H5P activity.
 *
 * @package    mod_h5pactivity
 * @covers     \mod_h5pactivity\output\result
 * @category   test
 * @copyright  2023 Laurent David <laurent.david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class result_test extends \advanced_testcase {
    /**
     * Test result export_options
     *
     * @param array $providedresultdata
     * @param array $expecteduseranswers
     * @return void
     * @dataProvider result_data_provider
     * @covers       \mod_h5pactivity\output\result::export_options
     */
    public function test_result_options(array $providedresultdata, array $expecteduseranswers): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();
        $activity = $this->getDataGenerator()->create_module('h5pactivity',
            ['course' => $course]);

        $manager = manager::create_from_instance($activity);
        $cm = $manager->get_coursemodule();
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $generator = $this->getDataGenerator()->get_plugin_generator('mod_h5pactivity');

        $params = ['cmid' => $cm->id, 'userid' => $student->id];
        $attempt = $generator->create_content($activity, $params);
        $resultdata = [
            'attemptid' => $attempt->id,
            'subcontent' => '',
            'timecreated' => time(),
            'completion' => true,
            'success' => true,
            'score' => (object) ['min' => 0, 'max' => 2, 'raw' => 2, 'scaled' => 1],
            'duration' => 'PT25S',
        ];
        $resultdata = array_merge($resultdata, $providedresultdata);
        $result = item_result::create_from_data((object) $resultdata);
        $classname = "mod_h5pactivity\\output\\result\\{$providedresultdata['interactiontype']}";
        $classname = str_replace('-', '', $classname);

        $reflectionoutput = new \ReflectionClass($classname);
        $constructor = $reflectionoutput->getConstructor();
        $constructor->setAccessible(true);
        $resultoutput = $reflectionoutput->newInstanceWithoutConstructor();
        $constructor->invoke($resultoutput, $result->get_data());

        $exportoptions = $reflectionoutput->getMethod('export_options');
        $exportoptions->setAccessible(true);

        $data = $exportoptions->invoke($resultoutput);
        $useranswersdata = array_map(function($item) {
            return $item->useranswer;
        }, $data);
        $this->assertEquals($expecteduseranswers, $useranswersdata);
    }

    /**
     * Data provider for result export_options test
     * @return array[]
     */
    public function result_data_provider(): array {
        return [
            'fill-in with case sensitive' => [
                'result' => [
                    'interactiontype' => 'fill-in',
                    'description' => '<p>Fill in the missing words</p>
                              <p>Meow .... this is a __________</p>
                              <p>Bark... this is a __________</p>',
                    'correctpattern' => '["{case_matters=true}cat[,]dog"]',
                    'response' => 'Cat[,]dog',
                    'additionals' => '{"extensions":{"http:\\/\\/h5p.org\\/x-api\\/h5p-local-content-id":31,'
                        . '"https:\\/\\/h5p.org\\/x-api\\/case-sensitivity":true,'
                        . '"https:\\/\\/h5p.org\\/x-api\\/alternatives":[["cat"],["dog"]]},"contextExtensions":{}}',
                ],
                'useranswers' => [
                    (object) ['answer' => 'Cat', 'incorrect' => true],
                    (object) ['answer' => 'dog', 'correct' => true],
                ],
            ],
            'fill-in with case insensitive' => [
                'result' => [
                    'interactiontype' => 'fill-in',
                    'description' => '<p>Fill in the missing words</p>
                              <p>Meow .... this is a __________</p>
                              <p>Bark... this is a __________</p>',
                    'correctpattern' => '["{case_matters=false}cat[,]dog"]',
                    'response' => 'Cat[,]dog',
                    'additionals' => '{"extensions":{"http:\\/\\/h5p.org\\/x-api\\/h5p-local-content-id":31,'
                        . '"https:\\/\\/h5p.org\\/x-api\\/case-sensitivity":false,'
                        . '"https:\\/\\/h5p.org\\/x-api\\/alternatives":[["cat"],["dog"]]},"contextExtensions":{}}',
                ],
                'useranswers' => [
                    (object) ['answer' => 'Cat', 'correct' => true],
                    (object) ['answer' => 'dog', 'correct' => true],
                ],
            ]
        ];
    }
}
