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

namespace tool_usertours;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Tests for step.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \tool_usertours\step
 */
final class step_test extends \advanced_testcase {
    /**
     * @var moodle_database
     */
    protected $db;

    /**
     * Setup to store the DB reference.
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();

        $this->db = $DB;
    }

    /**
     * Tear down to restore the original DB reference.
     */
    public function tearDown(): void {
        global $DB;

        $DB = $this->db;
        parent::tearDown();
    }

    /**
     * Helper to mock the database.
     *
     * @return moodle_database
     */
    public function mock_database() {
        global $DB;

        $DB = $this->getMockBuilder('moodle_database')
            ->getMock();

        return $DB;
    }

    /**
     * Data provider for the dirty value tester.
     *
     * @return array
     */
    public static function dirty_value_provider(): array {
        return
            [
                'tourid' => [
                    'tourid',
                    [1],
                ],
                'title' => [
                    'title',
                    ['Lorem'],
                ],
                'content' => [
                    'content',
                    ['Lorem'],
                ],
                'targettype' => [
                    'targettype',
                    ['Lorem'],
                ],
                'targetvalue' => [
                    'targetvalue',
                    ['Lorem'],
                ],
                'sortorder' => [
                    'sortorder',
                    [1],
                ],
                'config' => [
                    'config',
                    ['key', 'value'],
                ],
            ];
    }

    /**
     * Test the fetch function.
     */
    public function test_fetch(): void {
        $step = $this->getMockBuilder(\tool_usertours\step::class)
            ->onlyMethods(['reload_from_record'])
            ->getMock();

        $idretval = rand(1, 100);
        $DB = $this->mock_database();
        $DB->method('get_record')
            ->willReturn($idretval);

        $retval = rand(1, 100);
        $step->expects($this->once())
            ->method('reload_from_record')
            ->with($this->equalTo($idretval))
            ->wilLReturn($retval);

        $rc = new \ReflectionClass(\tool_usertours\step::class);
        $rcm = $rc->getMethod('fetch');

        $id = rand(1, 100);
        $this->assertEquals($retval, $rcm->invoke($step, 'fetch', $id));
    }

    /**
     * Test that setters mark things as dirty.
     *
     * @dataProvider dirty_value_provider
     * @param   string  $name       The key to update
     * @param   string  $value      The value to set
     */
    public function test_dirty_values($name, $value): void {
        $step = new \tool_usertours\step();
        $method = 'set_' . $name;
        call_user_func_array([$step, $method], $value);

        $rc = new \ReflectionClass(\tool_usertours\step::class);
        $rcp = $rc->getProperty('dirty');

        $this->assertTrue($rcp->getValue($step));
    }

    /**
     * Provider for is_first_step.
     *
     * @return array
     */
    public static function step_sortorder_provider(): array {
        return [
            [0, 5, true, false],
            [1, 5, false, false],
            [4, 5, false, true],
        ];
    }

    /**
     * Test is_first_step.
     *
     * @dataProvider step_sortorder_provider
     * @param   int     $sortorder      The sortorder to check
     * @param   int     $count          Unused in this function
     * @param   bool    $isfirst        Whether this is the first step
     * @param   bool    $islast         Whether this is the last step
     */
    public function test_is_first_step($sortorder, $count, $isfirst, $islast): void {
        $step = $this->getMockBuilder(\tool_usertours\step::class)
            ->onlyMethods(['get_sortorder'])
            ->getMock();

        $step->expects($this->once())
            ->method('get_sortorder')
            ->willReturn($sortorder);

        $this->assertEquals($isfirst, $step->is_first_step());
    }

    /**
     * Test is_last_step.
     *
     * @dataProvider step_sortorder_provider
     * @param   int     $sortorder      The sortorder to check
     * @param   int     $count          Total number of steps for this test
     * @param   bool    $isfirst        Whether this is the first step
     * @param   bool    $islast         Whether this is the last step
     */
    public function test_is_last_step($sortorder, $count, $isfirst, $islast): void {
        $step = $this->getMockBuilder(\tool_usertours\step::class)
            ->onlyMethods(['get_sortorder', 'get_tour'])
            ->getMock();

        $tour = $this->getMockBuilder(\tool_usertours\tour::class)
            ->onlyMethods(['count_steps'])
            ->getMock();

        $step->expects($this->once())
            ->method('get_tour')
            ->willReturn($tour);

        $tour->expects($this->once())
            ->method('count_steps')
            ->willReturn($count);

        $step->expects($this->once())
            ->method('get_sortorder')
            ->willReturn($sortorder);

        $this->assertEquals($islast, $step->is_last_step());
    }

    /**
     * Test get_config with no keys provided.
     */
    public function test_get_config_no_keys(): void {
        $step = new \tool_usertours\step();

        $rc = new \ReflectionClass(\tool_usertours\step::class);
        $rcp = $rc->getProperty('config');

        $allvalues = (object) [
            'some' => 'value',
            'another' => 42,
            'key' => [
                'somethingelse',
            ],
        ];

        $rcp->setValue($step, $allvalues);

        $this->assertEquals($allvalues, $step->get_config());
    }

    /**
     * Data provider for get_config.
     *
     * @return array
     */
    public static function get_config_provider(): array {
        $allvalues = (object) [
            'some' => 'value',
            'another' => 42,
            'key' => [
                'somethingelse',
            ],
        ];

        $tourconfig = rand(1, 100);
        $forcedconfig = rand(1, 100);

        return [
            'No initial config' => [
                null,
                null,
                null,
                $tourconfig,
                false,
                $forcedconfig,
                (object) [],
            ],
            'All values' => [
                $allvalues,
                null,
                null,
                $tourconfig,
                false,
                $forcedconfig,
                $allvalues,
            ],
            'Valid string value' => [
                $allvalues,
                'some',
                null,
                $tourconfig,
                false,
                $forcedconfig,
                'value',
            ],
            'Valid array value' => [
                $allvalues,
                'key',
                null,
                $tourconfig,
                false,
                $forcedconfig,
                ['somethingelse'],
            ],
            'Invalid value' => [
                $allvalues,
                'notavalue',
                null,
                $tourconfig,
                false,
                $forcedconfig,
                $tourconfig,
            ],
            'Configuration value' => [
                $allvalues,
                'placement',
                null,
                $tourconfig,
                false,
                $forcedconfig,
                $tourconfig,
            ],
            'Invalid value with default' => [
                $allvalues,
                'notavalue',
                'somedefault',
                $tourconfig,
                false,
                $forcedconfig,
                'somedefault',
            ],
            'Value forced at target' => [
                $allvalues,
                'somevalue',
                'somedefault',
                $tourconfig,
                true,
                $forcedconfig,
                $forcedconfig,
            ],
        ];
    }

    /**
     * Test get_config with valid keys provided.
     *
     * @dataProvider get_config_provider
     * @param   object  $values     The config values
     * @param   string  $key        The key
     * @param   mixed   $default    The default value
     * @param   mixed   $tourconfig The tour config
     * @param   bool    $isforced   Whether the setting is forced
     * @param   mixed   $forcedvalue    The example value
     * @param   mixed   $expected   The expected value
     */
    public function test_get_config_valid_keys($values, $key, $default, $tourconfig, $isforced, $forcedvalue, $expected): void {
        $step = $this->getMockBuilder(\tool_usertours\step::class)
            ->onlyMethods(['get_target', 'get_targettype', 'get_tour'])
            ->getMock();

        $rc = new \ReflectionClass(\tool_usertours\step::class);
        $rcp = $rc->getProperty('config');
        $rcp->setValue($step, $values);

        $target = $this->getMockBuilder(\tool_usertours\local\target\base::class)
            ->disableOriginalConstructor()
            ->getMock();

        $target->expects($this->any())
            ->method('is_setting_forced')
            ->willReturn($isforced);

        $target->expects($this->any())
            ->method('get_forced_setting_value')
            ->with($this->equalTo($key))
            ->willReturn($forcedvalue);

        $step->expects($this->any())
            ->method('get_targettype')
            ->willReturn('type');

        $step->expects($this->any())
            ->method('get_target')
            ->willReturn($target);

        $tour = $this->getMockBuilder(\tool_usertours\tour::class)
            ->getMock();

        $tour->expects($this->any())
            ->method('get_config')
            ->willReturn($tourconfig);

        $step->expects($this->any())
            ->method('get_tour')
            ->willReturn($tour);

        $this->assertEquals($expected, $step->get_config($key, $default));
    }

    /**
     * Data provider for set_config.
     */
    public static function set_config_provider(): array {
        $allvalues = (object) [
            'some' => 'value',
            'another' => 42,
            'key' => [
                'somethingelse',
            ],
        ];

        $randvalue = rand(1, 100);

        $provider = [];

        $newvalues = $allvalues;
        $newvalues->some = 'unset';
        $provider['Unset an existing value'] = [
            $allvalues,
            'some',
            null,
            $newvalues,
        ];

        $newvalues = $allvalues;
        $newvalues->some = $randvalue;
        $provider['Set an existing value'] = [
            $allvalues,
            'some',
            $randvalue,
            $newvalues,
        ];

        $provider['Set a new value'] = [
            $allvalues,
            'newkey',
            $randvalue,
            (object) array_merge((array) $allvalues, ['newkey' => $randvalue]),
        ];

        return $provider;
    }

    /**
     * Test that set_config works in the anticipated fashion.
     *
     * @dataProvider set_config_provider
     * @param   mixed   $initialvalues  The inital value to set
     * @param   string  $key        The key to test
     * @param   mixed   $newvalue   The new value to set
     * @param   mixed   $expected   The expected value
     */
    public function test_set_config($initialvalues, $key, $newvalue, $expected): void {
        $step = new \tool_usertours\step();

        $rc = new \ReflectionClass(\tool_usertours\step::class);
        $rcp = $rc->getProperty('config');
        $rcp->setValue($step, $initialvalues);

        $target = $this->getMockBuilder(\tool_usertours\local\target\base::class)
            ->disableOriginalConstructor()
            ->getMock();

        $target->expects($this->any())
            ->method('is_setting_forced')
            ->willReturn(false);

        $step->set_config($key, $newvalue);

        $this->assertEquals($expected, $rcp->getValue($step));
    }

    /**
     * Ensure that non-dirty tours are not persisted.
     */
    public function test_persist_non_dirty(): void {
        $step = $this->getMockBuilder(\tool_usertours\step::class)
            ->onlyMethods([
                'to_record',
                'reload',
            ])
            ->getMock();

        $step->expects($this->never())
            ->method('to_record');

        $step->expects($this->never())
            ->method('reload');

        $this->assertSame($step, $step->persist());
    }

    /**
     * Ensure that new dirty steps are persisted.
     */
    public function test_persist_dirty_new(): void {
        // Mock the database.
        $DB = $this->mock_database();
        $DB->expects($this->once())
            ->method('insert_record')
            ->willReturn(42);

        // Mock the tour.
        $step = $this->getMockBuilder(\tool_usertours\step::class)
            ->onlyMethods([
                'to_record',
                'calculate_sortorder',
                'reload',
            ])
            ->getMock();

        $step->expects($this->once())
            ->method('to_record')
            ->willReturn((object)['id' => 42]);

        $step->expects($this->once())
            ->method('calculate_sortorder');

        $step->expects($this->once())
            ->method('reload');

        $rc = new \ReflectionClass(\tool_usertours\step::class);
        $rcp = $rc->getProperty('dirty');
        $rcp->setValue($step, true);

        $tour = $this->createMock(\tool_usertours\tour::class);
        $rcp = $rc->getProperty('tour');
        $rcp->setValue($step, $tour);

        $this->assertSame($step, $step->persist());
    }

    /**
     * Ensure that new non-dirty, forced steps are persisted.
     */
    public function test_persist_force_new(): void {
        global $DB;

        // Mock the database.
        $DB = $this->mock_database();
        $DB->expects($this->once())
            ->method('insert_record')
            ->willReturn(42);

        // Mock the tour.
        $step = $this->getMockBuilder(\tool_usertours\step::class)
            ->onlyMethods([
                'to_record',
                'calculate_sortorder',
                'reload',
            ])
            ->getMock();

        $step->expects($this->once())
            ->method('to_record')
            ->willReturn((object)['id' => 42]);

        $step->expects($this->once())
            ->method('calculate_sortorder');

        $step->expects($this->once())
            ->method('reload');

        $tour = $this->createMock(\tool_usertours\tour::class);
        $rc = new \ReflectionClass(\tool_usertours\step::class);
        $rcp = $rc->getProperty('tour');
        $rcp->setValue($step, $tour);

        $this->assertSame($step, $step->persist(true));
    }

    /**
     * Ensure that existing dirty steps are persisted.
     */
    public function test_persist_dirty_existing(): void {
        // Mock the database.
        $DB = $this->mock_database();
        $DB->expects($this->once())
            ->method('update_record');

        // Mock the tour.
        $step = $this->getMockBuilder(\tool_usertours\step::class)
            ->onlyMethods([
                'to_record',
                'calculate_sortorder',
                'reload',
            ])
            ->getMock();

        $step->expects($this->once())
            ->method('to_record')
            ->willReturn((object)['id' => 42]);

        $step->expects($this->never())
            ->method('calculate_sortorder');

        $step->expects($this->once())
            ->method('reload');

        $rc = new \ReflectionClass(\tool_usertours\step::class);
        $rcp = $rc->getProperty('id');
        $rcp->setValue($step, 42);

        $rcp = $rc->getProperty('dirty');
        $rcp->setValue($step, true);

        $tour = $this->createMock(\tool_usertours\tour::class);
        $rcp = $rc->getProperty('tour');
        $rcp->setValue($step, $tour);

        $this->assertSame($step, $step->persist());
    }

    /**
     * Ensure that existing non-dirty, forced steps are persisted.
     */
    public function test_persist_force_existing(): void {
        global $DB;

        // Mock the database.
        $DB = $this->mock_database();
        $DB->expects($this->once())
            ->method('update_record');

        // Mock the tour.
        $step = $this->getMockBuilder(\tool_usertours\step::class)
            ->onlyMethods([
                'to_record',
                'calculate_sortorder',
                'reload',
            ])
            ->getMock();

        $step->expects($this->once())
            ->method('to_record')
            ->willReturn((object) ['id' => 42]);

        $step->expects($this->never())
            ->method('calculate_sortorder');

        $step->expects($this->once())
            ->method('reload');

        $rc = new \ReflectionClass(\tool_usertours\step::class);
        $rcp = $rc->getProperty('id');
        $rcp->setValue($step, 42);

        $tour = $this->createMock(\tool_usertours\tour::class);
        $rcp = $rc->getProperty('tour');
        $rcp->setValue($step, $tour);

        $this->assertSame($step, $step->persist(true));
    }

    /**
     * Check that a tour which has never been persisted is removed correctly.
     */
    public function test_remove_non_persisted(): void {
        $step = $this->getMockBuilder(\tool_usertours\step::class)
            ->onlyMethods([])
            ->getMock();

        // Mock the database.
        $DB = $this->mock_database();
        $DB->expects($this->never())
            ->method('delete_records');

        $this->assertNull($step->remove());
    }

    /**
     * Check that a tour which has been persisted is removed correctly.
     */
    public function test_remove_persisted(): void {
        $id = rand(1, 100);

        $tour = $this->getMockBuilder(\tool_usertours\tour::class)
            ->onlyMethods([
                'reset_step_sortorder',
            ])
            ->getMock();

        $tour->expects($this->once())
            ->method('reset_step_sortorder');

        $step = $this->getMockBuilder(\tool_usertours\step::class)
            ->onlyMethods([
                'get_tour',
            ])
            ->getMock();

        $step->expects($this->once())
            ->method('get_tour')
            ->willReturn($tour);

        // Mock the database.
        $DB = $this->mock_database();
        $DB->expects($this->once())
            ->method('delete_records')
            ->with($this->equalTo('tool_usertours_steps'), $this->equalTo(['id' => $id]));

        $rc = new \ReflectionClass(\tool_usertours\step::class);
        $rcp = $rc->getProperty('id');
        $rcp->setValue($step, $id);

        $this->assertEquals($id, $step->get_id());
        $this->assertNull($step->remove());
    }

    /**
     * Data provider for the get_ tests.
     *
     * @return array
     */
    public static function getter_provider(): array {
        return [
            'id' => [
                'id',
                rand(1, 100),
            ],
            'tourid' => [
                'tourid',
                rand(1, 100),
            ],
            'title' => [
                'title',
                'Lorem',
            ],
            'content' => [
                'content',
                'Lorem',
            ],
            'targettype' => [
                'targettype',
                'Lorem',
            ],
            'targetvalue' => [
                'targetvalue',
                'Lorem',
            ],
            'sortorder' => [
                'sortorder',
                rand(1, 100),
            ],
        ];
    }

    /**
     * Test that getters return the configured value.
     *
     * @dataProvider getter_provider
     * @param   string  $key        The key to test
     * @param   mixed   $value      The expected value
     */
    public function test_getters($key, $value): void {
        $step = new \tool_usertours\step();

        $rc = new \ReflectionClass(\tool_usertours\step::class);

        $rcp = $rc->getProperty($key);
        $rcp->setValue($step, $value);

        $getter = 'get_' . $key;

        $this->assertEquals($value, $step->$getter());
    }

    /**
     * Ensure that the get_step_image_from_input function replace PIXICON placeholder with the correct images correctly.
     */
    public function test_get_step_image_from_input(): void {
        // Test step content with single image.
        $stepcontent = '@@PIXICON::tour/tour_mycourses::tool_usertours@@<br>Test';
        $stepcontent = \tool_usertours\step::get_step_image_from_input($stepcontent);

        // If the format is correct, PIXICON placeholder will be replaced with the img tag.
        $this->assertStringStartsWith('<img', $stepcontent);
        $this->assertStringEndsWith('Test', $stepcontent);
        $this->assertStringNotContainsString('PIXICON', $stepcontent);

        // Test step content with multiple images.
        $stepcontent =
            '@@PIXICON::tour/tour_mycourses::tool_usertours@@<br>Test<br>@@PIXICON::tour/tour_myhomepage::tool_usertours@@';
        $stepcontent = \tool_usertours\step::get_step_image_from_input($stepcontent);
        // If the format is correct, PIXICON placeholder will be replaced with the img tag.
        $this->assertStringStartsWith('<img', $stepcontent);
        // We should have 2 img tags here.
        $this->assertEquals(2, substr_count($stepcontent, '<img'));
        $this->assertStringNotContainsString('PIXICON', $stepcontent);

        // Test step content with incorrect format.
        $stepcontent = '@@PIXICON::tour/tour_mycourses<br>Test';
        $stepcontent = \tool_usertours\step::get_step_image_from_input($stepcontent);

        // If the format is not correct, PIXICON placeholder will not be replaced with the img tag.
        $this->assertStringStartsNotWith('<img', $stepcontent);
        $this->assertStringStartsWith('@@PIXICON', $stepcontent);
        $this->assertStringEndsWith('Test', $stepcontent);
        $this->assertStringContainsString('PIXICON', $stepcontent);
    }
}
