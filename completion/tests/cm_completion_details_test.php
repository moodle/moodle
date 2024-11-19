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
 * Contains unit tests for core_completion/cm_completion_details.
 *
 * @package   core_completion
 * @copyright 2021 Jun Pataleta <jun@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);

namespace core_completion;

use advanced_testcase;
use cm_info;
use completion_info;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/completionlib.php');

/**
 * Class for unit testing core_completion/cm_completion_details.
 *
 * @package   core_completion
 * @copyright 2021 Jun Pataleta <jun@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core_completion\cm_completion_details
 */
class cm_completion_details_test extends advanced_testcase {

    /** @var completion_info A completion object. */
    protected $completioninfo = null;

    /**
     * Fetches a mocked cm_completion_details instance.
     *
     * @param int|null $completion The completion tracking mode for the module.
     * @param array $completionoptions Completion options (e.g. completionview, completionusegrade, etc.)
     * @param object $mockcompletiondata Mock data to be returned by get_data.
     * @param string $modname The modname to set in the cm if a specific one is required.
     * @return cm_completion_details
     */
    protected function setup_data(?int $completion, array $completionoptions = [],
            ?object $mockcompletiondata = null, $modname = 'somenonexistentmod'): cm_completion_details {
        if (is_null($completion)) {
            $completion = COMPLETION_TRACKING_AUTOMATIC;
        }

        // Mock a completion_info instance so we can simply mock the returns of completion_info::get_data() later.
        $this->completioninfo = $this->getMockBuilder(completion_info::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Mock return of completion_info's is_enabled() method to match the expected completion tracking for the module.
        $this->completioninfo->expects($this->any())
            ->method('is_enabled')
            ->willReturn($completion);

        if (!empty($mockcompletiondata)) {
            $this->completioninfo->expects($this->any())
                ->method('get_data')
                ->willReturn($mockcompletiondata);
        }

        // Build a mock cm_info instance.
        $mockcminfo = $this->getMockBuilder(cm_info::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__get'])
            ->getMock();

        // Mock the return of the magic getter method when fetching the cm_info object's customdata and instance values.
        $mockcminfo->expects($this->any())
            ->method('__get')
            ->will($this->returnValueMap([
                ['completion', $completion],
                ['instance', 1],
                ['modname', $modname],
                ['completionview', $completionoptions['completionview'] ?? COMPLETION_VIEW_NOT_REQUIRED],
                ['completiongradeitemnumber', $completionoptions['completionusegrade'] ?? null],
                ['completionpassgrade', $completionoptions['completionpassgrade'] ?? null],
            ]));

        return new cm_completion_details($this->completioninfo, $mockcminfo, 2);
    }

    /**
     * Provides data for test_has_completion().
     *
     * @return array[]
     */
    public static function has_completion_provider(): array {
        return [
            'Automatic' => [
                COMPLETION_TRACKING_AUTOMATIC, true
            ],
            'Manual' => [
                COMPLETION_TRACKING_MANUAL, true
            ],
            'None' => [
                COMPLETION_TRACKING_NONE, false
            ],
        ];
    }

    /**
     * Test for has_completion().
     *
     * @covers ::has_completion
     * @dataProvider has_completion_provider
     * @param int $completion The completion tracking mode.
     * @param bool $expectedresult Expected result.
     */
    public function test_has_completion(int $completion, bool $expectedresult): void {
        $cmcompletion = $this->setup_data($completion);

        $this->assertEquals($expectedresult, $cmcompletion->has_completion());
    }

    /**
     * Provides data for test_is_automatic().
     *
     * @return array[]
     */
    public static function is_automatic_provider(): array {
        return [
            'Automatic' => [
                COMPLETION_TRACKING_AUTOMATIC, true
            ],
            'Manual' => [
                COMPLETION_TRACKING_MANUAL, false
            ],
            'None' => [
                COMPLETION_TRACKING_NONE, false
            ],
        ];
    }

    /**
     * Test for is_available().
     *
     * @covers ::is_automatic
     * @dataProvider is_automatic_provider
     * @param int $completion The completion tracking mode.
     * @param bool $expectedresult Expected result.
     */
    public function test_is_automatic(int $completion, bool $expectedresult): void {
        $cmcompletion = $this->setup_data($completion);

        $this->assertEquals($expectedresult, $cmcompletion->is_automatic());
    }

    /**
     * Provides data for test_is_manual().
     *
     * @return array[]
     */
    public static function is_manual_provider(): array {
        return [
            'Automatic' => [
                COMPLETION_TRACKING_AUTOMATIC, false
            ],
            'Manual' => [
                COMPLETION_TRACKING_MANUAL, true
            ],
            'None' => [
                COMPLETION_TRACKING_NONE, false
            ],
        ];
    }

    /**
     * Test for is_manual().
     *
     * @covers ::is_manual
     * @dataProvider is_manual_provider
     * @param int $completion The completion tracking mode.
     * @param bool $expectedresult Expected result.
     */
    public function test_is_manual(int $completion, bool $expectedresult): void {
        $cmcompletion = $this->setup_data($completion);

        $this->assertEquals($expectedresult, $cmcompletion->is_manual());
    }

    /**
     * Data provider for test_get_overall_completion().
     * @return array[]
     */
    public static function overall_completion_provider(): array {
        return [
            'Complete' => [COMPLETION_COMPLETE],
            'Incomplete' => [COMPLETION_INCOMPLETE],
        ];
    }

    /**
     * Test for get_overall_completion().
     *
     * @covers ::get_overall_completion
     * @dataProvider overall_completion_provider
     * @param int $state
     */
    public function test_get_overall_completion(int $state): void {
        $completiondata = (object)['completionstate' => $state];
        $cmcompletion = $this->setup_data(COMPLETION_TRACKING_AUTOMATIC, [], $completiondata);
        $this->assertEquals($state, $cmcompletion->get_overall_completion());
    }

    /**
     * Data provider for test_is_overall_complete().
     * @return array[]
     */
    public static function is_overall_complete_provider(): array {
        return [
            'Automatic, require view, not viewed' => [
                'expected' => false,
                'completion' => COMPLETION_TRACKING_AUTOMATIC,
                'completionstate' => COMPLETION_INCOMPLETE,
                'completionview' => COMPLETION_INCOMPLETE,
                'completiongrade' => null,
                'completionpassgrade' => null,
            ],
            'Automatic, require view, viewed' => [
                'expected' => true,
                'completion' => COMPLETION_TRACKING_AUTOMATIC,
                'completionstate' => COMPLETION_COMPLETE,
                'completionview' => COMPLETION_COMPLETE,
                'completiongrade' => null,
                'completionpassgrade' => null,
            ],
            'Automatic, require grade, not graded' => [
                'expected' => false,
                'completion' => COMPLETION_TRACKING_AUTOMATIC,
                'completionstate' => COMPLETION_INCOMPLETE,
                'completionview' => null,
                'completiongrade' => COMPLETION_INCOMPLETE,
                'completionpassgrade' => null,
            ],
            'Automatic, require grade, graded with fail' => [
                'expected' => true,
                'completion' => COMPLETION_TRACKING_AUTOMATIC,
                'completionstate' => COMPLETION_COMPLETE_FAIL,
                'completionview' => null,
                'completiongrade' => COMPLETION_COMPLETE_FAIL,
                'completionpassgrade' => null,
            ],
            'Automatic, require grade, graded with passing' => [
                'expected' => true,
                'completion' => COMPLETION_TRACKING_AUTOMATIC,
                'completionstate' => COMPLETION_COMPLETE_PASS,
                'completionview' => null,
                'completiongrade' => COMPLETION_COMPLETE_PASS,
                'completionpassgrade' => null,
            ],
            'Automatic, require passgrade, not graded' => [
                'expected' => false,
                'completion' => COMPLETION_TRACKING_AUTOMATIC,
                'completionstate' => COMPLETION_INCOMPLETE,
                'completionview' => null,
                'completiongrade' => null,
                'completionpassgrade' => COMPLETION_INCOMPLETE,
            ],
            'Automatic, require passgrade, graded with fail' => [
                'expected' => false,
                'completion' => COMPLETION_TRACKING_AUTOMATIC,
                'completionstate' => COMPLETION_COMPLETE_FAIL,
                'completionview' => null,
                'completiongrade' => null,
                'completionpassgrade' => COMPLETION_COMPLETE_FAIL,
            ],
            'Automatic, require passgrade, graded with passing' => [
                'expected' => true,
                'completion' => COMPLETION_TRACKING_AUTOMATIC,
                'completionstate' => COMPLETION_COMPLETE_PASS,
                'completionview' => null,
                'completiongrade' => null,
                'completionpassgrade' => COMPLETION_COMPLETE_PASS,
            ],
            'Manual, incomplete' => [
                'expected' => false,
                'completion' => COMPLETION_TRACKING_MANUAL,
                'completionstate' => COMPLETION_INCOMPLETE,
            ],
            'Manual, complete' => [
                'expected' => true,
                'completion' => COMPLETION_TRACKING_MANUAL,
                'completionstate' => COMPLETION_COMPLETE,
            ],
            'None, incomplete' => [
                'expected' => false,
                'completion' => COMPLETION_TRACKING_NONE,
                'completionstate' => COMPLETION_INCOMPLETE,
            ],
            'None, complete' => [
                'expected' => false,
                'completion' => COMPLETION_TRACKING_NONE,
                'completionstate' => COMPLETION_COMPLETE,
            ],
        ];
    }

    /**
     * Test for is_overall_complete().
     *
     * @covers ::is_overall_complete
     * @dataProvider is_overall_complete_provider
     * @param bool $expected Expected result returned by is_overall_complete().
     * @param int $completion The completion tracking mode.
     * @param int $completionstate The overall completion state.
     * @param int|null $completionview Completion status of the "view" completion condition.
     * @param int|null $completiongrade Completion status of the "must receive grade" completion condition.
     * @param int|null $completionpassgrade Completion status of the "must receive passing grade" completion condition.
     */
    public function test_is_overall_complete(
        bool $expected,
        int $completion,
        int $completionstate,
        ?int $completionview = null,
        ?int $completiongrade = null,
        ?int $completionpassgrade = null,
    ): void {
        $options = [];
        $getdatareturn = (object)[
            'completionstate' => $completionstate,
            'viewed' => $completionview,
            'completiongrade' => $completiongrade,
            'passgrade' => $completionpassgrade,
        ];

        if (!is_null($completionview)) {
            $options['completionview'] = true;
        }
        if (!is_null($completiongrade)) {
            $options['completionusegrade'] = true;
        }
        if (!is_null($completionpassgrade)) {
            $options['completionpassgrade'] = true;
        }

        $cmcompletion = $this->setup_data($completion, $options, $getdatareturn);
        $this->assertEquals($expected, $cmcompletion->is_overall_complete());
    }

    /**
     * Data provider for test_get_details().
     * @return array[]
     */
    public static function get_details_provider(): array {
        return [
            'No completion tracking' => [
                COMPLETION_TRACKING_NONE, null, null, null, []
            ],
            'Manual completion tracking' => [
                COMPLETION_TRACKING_MANUAL, null, null, null, []
            ],
            'Automatic, require view, not viewed' => [
                COMPLETION_TRACKING_AUTOMATIC, COMPLETION_INCOMPLETE, null, null, [
                    'completionview' => (object)[
                        'status' => COMPLETION_INCOMPLETE,
                        'description' => get_string('detail_desc:view', 'completion'),
                    ]
                ]
            ],
            'Automatic, require view, viewed' => [
                COMPLETION_TRACKING_AUTOMATIC, COMPLETION_COMPLETE, null, null, [
                    'completionview' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:view', 'completion'),
                    ]
                ]
            ],
            'Automatic, require grade, incomplete' => [
                COMPLETION_TRACKING_AUTOMATIC, null, COMPLETION_INCOMPLETE, null, [
                    'completionusegrade' => (object)[
                        'status' => COMPLETION_INCOMPLETE,
                        'description' => get_string('detail_desc:receivegrade', 'completion'),
                    ]
                ]
            ],
            'Automatic, require grade, complete' => [
                COMPLETION_TRACKING_AUTOMATIC, null, COMPLETION_COMPLETE, null, [
                    'completionusegrade' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:receivegrade', 'completion'),
                    ]
                ]
            ],
            'Automatic, require view (complete) and grade (incomplete)' => [
                COMPLETION_TRACKING_AUTOMATIC, COMPLETION_COMPLETE, COMPLETION_INCOMPLETE, null, [
                    'completionview' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:view', 'completion'),
                    ],
                    'completionusegrade' => (object)[
                        'status' => COMPLETION_INCOMPLETE,
                        'description' => get_string('detail_desc:receivegrade', 'completion'),
                    ]
                ]
            ],
            'Automatic, require view (incomplete) and grade (complete)' => [
                COMPLETION_TRACKING_AUTOMATIC, COMPLETION_INCOMPLETE, COMPLETION_COMPLETE, null, [
                    'completionview' => (object)[
                        'status' => COMPLETION_INCOMPLETE,
                        'description' => get_string('detail_desc:view', 'completion'),
                    ],
                    'completionusegrade' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:receivegrade', 'completion'),
                    ]
                ]
            ],
            'Automatic, require grade, require pass grade, complete' => [
                COMPLETION_TRACKING_AUTOMATIC, null, COMPLETION_COMPLETE, COMPLETION_COMPLETE, [
                    'completionusegrade' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:receivegrade', 'completion'),
                    ],
                    'completionpassgrade' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:receivepassgrade', 'completion'),
                    ],
                ]
            ],
            'Automatic, require grade, require pass grade, incomplete' => [
                COMPLETION_TRACKING_AUTOMATIC, null, COMPLETION_COMPLETE, COMPLETION_INCOMPLETE, [
                    'completionusegrade' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:receivegrade', 'completion'),
                    ],
                    'completionpassgrade' => (object)[
                        'status' => COMPLETION_INCOMPLETE,
                        'description' => get_string('detail_desc:receivepassgrade', 'completion'),
                    ],
                ]
            ],
            'Automatic, require view (complete), require grade(complete), require pass grade(complete)' => [
                COMPLETION_TRACKING_AUTOMATIC, COMPLETION_COMPLETE, COMPLETION_COMPLETE, COMPLETION_COMPLETE, [
                    'completionview' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:view', 'completion'),
                    ],
                    'completionusegrade' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:receivegrade', 'completion'),
                    ],
                    'completionpassgrade' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:receivepassgrade', 'completion'),
                    ],
                ]
            ],
            'Automatic, require view (incomplete), require grade(complete), require pass grade(complete)' => [
                COMPLETION_TRACKING_AUTOMATIC, COMPLETION_INCOMPLETE, COMPLETION_COMPLETE, COMPLETION_COMPLETE, [
                    'completionview' => (object)[
                        'status' => COMPLETION_INCOMPLETE,
                        'description' => get_string('detail_desc:view', 'completion'),
                    ],
                    'completionusegrade' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:receivegrade', 'completion'),
                    ],
                    'completionpassgrade' => (object)[
                        'status' => COMPLETION_COMPLETE,
                        'description' => get_string('detail_desc:receivepassgrade', 'completion'),
                    ],
                ]
            ],
        ];
    }

    /**
     * Test for \core_completion\cm_completion_details::get_details().
     *
     * @covers ::get_details
     * @dataProvider get_details_provider
     * @param int $completion The completion tracking mode.
     * @param int|null $completionview Completion status of the "view" completion condition.
     * @param int|null $completiongrade Completion status of the "must receive grade" completion condition.
     * @param int|null $completionpassgrade Completion status of the "must receive passing grade" completion condition.
     * @param array $expecteddetails Expected completion details returned by get_details().
     */
    public function test_get_details(int $completion, ?int $completionview,
             ?int $completiongrade, ?int $completionpassgrade, array $expecteddetails): void {
        $options = [];
        $getdatareturn = (object)[
            'viewed' => $completionview,
            'completiongrade' => $completiongrade,
            'passgrade' => $completionpassgrade,
        ];

        if (!is_null($completionview)) {
            $options['completionview'] = true;
        }
        if (!is_null($completiongrade)) {
            $options['completionusegrade'] = true;
        }
        if (!is_null($completionpassgrade)) {
            $options['completionpassgrade'] = true;
        }

        $cmcompletion = $this->setup_data($completion, $options, $getdatareturn);
        $this->assertEquals($expecteddetails, $cmcompletion->get_details());
    }

    /**
     * Data provider for test_get_details_custom_order().
     * @return array[]
     */
    public static function get_details_custom_order_provider(): array {
        return [
            'Custom and view/grade standard conditions, view first and grade last' => [
                true,
                true,
                [
                    'completionsubmit' => true,
                ],
                'assign',
                ['completionview', 'completionsubmit', 'completionusegrade'],
            ],
            'Custom and view/grade standard conditions, grade not last' => [
                true,
                true,
                [
                    'completionminattempts' => 2,
                    'completionusegrade' => 50,
                    'completionpassorattemptsexhausted' => 1,
                ],
                'quiz',
                ['completionview', 'completionminattempts', 'completionusegrade', 'completionpassorattemptsexhausted'],
            ],
            'Custom and grade standard conditions only, no view condition' => [
                false,
                true,
                [
                    'completionsubmit' => true,
                ],
                'assign',
                ['completionsubmit', 'completionusegrade'],
            ],
            'Custom and view standard conditions only, no grade condition' => [
                true,
                false,
                [
                    'completionsubmit' => true
                ],
                'assign',
                ['completionview', 'completionsubmit'],
            ],
            'View and grade conditions only, activity with no custom conditions' => [
                true,
                true,
                [
                    'completionview' => true,
                    'completionusegrade' => true
                ],
                'workshop',
                ['completionview', 'completionusegrade'],
            ],
            'View condition only, activity with no custom conditions' => [
                true,
                false,
                [
                    'completionview' => true,
                ],
                'workshop',
                ['completionview'],
            ],
        ];
    }

    /**
     * Test custom sort order is functioning in \core_completion\cm_completion_details::get_details().
     *
     * @covers ::get_details
     * @dataProvider get_details_custom_order_provider
     * @param bool $completionview Completion status of the "view" completion condition.
     * @param bool $completiongrade Completion status of the "must receive grade" completion condition.
     * @param array $customcompletionrules Custom completion requirements, along with their values.
     * @param string $modname The name of the module having data fetched.
     * @param array $expectedorder The expected order of completion conditions returned about the module.
     */
    public function test_get_details_custom_order(bool $completionview, bool $completiongrade, array $customcompletionrules,
            string $modname, array $expectedorder): void {

        $options['customcompletion'] = [];
        $customcompletiondata = [];

        if ($completionview) {
            $options['completionview'] = true;
        }

        if ($completiongrade) {
            $options['completionusegrade'] = true;
        }

        // Set up the completion rules for the completion info.
        foreach ($customcompletionrules as $customtype => $isenabled) {
            $customcompletiondata[$customtype] = COMPLETION_COMPLETE;
        }

        $getdatareturn = (object)[
            'viewed' => $completionview ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE,
            'completiongrade' => $completiongrade ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE,
            'customcompletion' => $customcompletiondata,
        ];

        $cmcompletion = $this->setup_data(COMPLETION_TRACKING_AUTOMATIC, $options, $getdatareturn, $modname);

        $this->completioninfo->expects($this->any())
            ->method('get_data')
            ->willReturn($getdatareturn);

        $fetcheddetails = $cmcompletion->get_details();

        // Check the expected number of items are returned, and sorted in the correct order.
        $this->assertCount(count($expectedorder), $fetcheddetails);
        $this->assertTrue((array_keys($fetcheddetails) === $expectedorder));
    }
}
