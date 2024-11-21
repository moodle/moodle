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
 * Test filters.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/fixtures/events.php');

use block_xp\local\config\course_world_config;
use block_xp\tests\base_testcase;
use block_xp_filter;
use block_xp_rule_base;
use block_xp_rule_property;
use block_xp_ruleset;

/**
 * Filters testcase.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \block_xp\local\xp\course_filter_manager
 */
final class filters_test extends base_testcase {

    /**
     * Get the filter manager.
     *
     * @param int $courseid The course ID.
     * @return local\xp\course_filter_manager
     */
    protected function get_filter_manager($courseid) {
        $world = $this->get_world($courseid);
        $world->get_config()->set('defaultfilters', course_world_config::DEFAULT_FILTERS_STATIC);
        return $world->get_filter_manager();
    }

    public function test_filter_match(): void {
        $rule = new block_xp_rule_property(block_xp_rule_base::EQ, 'c', 'crud');
        $filter = block_xp_filter::load_from_data(['rule' => $rule]);

        $e = \block_xp\event\something_happened::mock(['crud' => 'c']);
        $this->assertTrue($filter->match($e));

        $e = \block_xp\event\something_happened::mock(['crud' => 'd']);
        $this->assertFalse($filter->match($e));
    }

    public function test_filter_load_rule(): void {
        $rulec = new block_xp_rule_property(block_xp_rule_base::EQ, 'c', 'crud');
        $e = \block_xp\event\something_happened::mock(['crud' => 'c']);

        $filter = block_xp_filter::load_from_data(['rule' => $rulec]);
        $this->assertTrue($filter->match($e));

        $filter = block_xp_filter::load_from_data(['ruledata' => json_encode($rulec->export())]);
        $this->assertTrue($filter->match($e));

        $filter = block_xp_filter::load_from_data([]);
        $filter->set_rule($rulec);
        $this->assertTrue($filter->match($e));
    }

    public function test_standard_filters(): void {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $fm = $this->get_filter_manager($course->id);

        $c = \block_xp\event\something_happened::mock(['crud' => 'c']);
        $r = \block_xp\event\something_happened::mock(['crud' => 'r']);
        $u = \block_xp\event\something_happened::mock(['crud' => 'u']);
        $d = \block_xp\event\something_happened::mock(['crud' => 'd']);

        $this->assertSame(45, $fm->get_points_for_event($c));
        $this->assertSame(9, $fm->get_points_for_event($r));
        $this->assertSame(3, $fm->get_points_for_event($u));
        $this->assertSame(0, $fm->get_points_for_event($d));
    }

    public function test_custom_filters(): void {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();
        $fm = $this->get_filter_manager($course->id);

        // Define some custom rules, the sortorder and IDs are mixed here.
        $rule = new block_xp_rule_property(block_xp_rule_base::EQ, 'c', 'crud');
        $data = ['courseid' => $course->id, 'sortorder' => -20, 'points' => 100, 'rule' => $rule];
        block_xp_filter::load_from_data($data)->save();
        $fm->invalidate_filters_cache();

        $rule = new block_xp_ruleset([
            new block_xp_rule_property(block_xp_rule_base::EQ, 2, 'objectid'),
            new block_xp_rule_property(block_xp_rule_base::EQ, 'u', 'crud'),
        ], block_xp_ruleset::ANY);
        $data = ['courseid' => $course->id, 'sortorder' => -10, 'points' => 120, 'rule' => $rule];
        block_xp_filter::load_from_data($data)->save();

        $rule = new block_xp_ruleset([
            new block_xp_rule_property(block_xp_rule_base::GTE, 100, 'objectid'),
            new block_xp_rule_property(block_xp_rule_base::EQ, 'r', 'crud'),
        ], block_xp_ruleset::ALL);
        $data = ['courseid' => $course->id, 'sortorder' => -30, 'points' => 130, 'rule' => $rule];
        block_xp_filter::load_from_data($data)->save();
        $fm->invalidate_filters_cache();

        // We can override default filters.
        $e = \block_xp\event\something_happened::mock(['crud' => 'c', 'objectid' => 2]);
        $this->assertSame(100, $fm->get_points_for_event($e));

        // We can still fallback on default filters.
        $e = \block_xp\event\something_happened::mock(['crud' => 'd']);
        $this->assertSame(0, $fm->get_points_for_event($e));

        // Sort order is respected.
        $e = \block_xp\event\something_happened::mock(['crud' => 'u', 'objectid' => 2]);
        $this->assertSame(120, $fm->get_points_for_event($e));
        $e = \block_xp\event\something_happened::mock(['crud' => 'r']);
        $this->assertSame(9, $fm->get_points_for_event($e));
        $e = \block_xp\event\something_happened::mock(['crud' => 'r', 'objectid' => 100]);
        $this->assertSame(130, $fm->get_points_for_event($e));

        // This filter will catch everything before the default rules.
        $rule = new block_xp_rule_property(block_xp_rule_base::CT, 'something', 'eventname');
        $data = ['courseid' => $course->id, 'sortorder' => -5, 'points' => 110, 'rule' => $rule];
        block_xp_filter::load_from_data($data)->save();
        $fm->invalidate_filters_cache();

        $e = \block_xp\event\something_happened::mock(['crud' => 'd']);
        $this->assertSame(110, $fm->get_points_for_event($e));
        $e = \block_xp\event\something_happened::mock(['crud' => 'r']);
        $this->assertSame(110, $fm->get_points_for_event($e));

        // This filter will catch everything.
        $rule = new block_xp_rule_property(block_xp_rule_base::CT, 'something', 'eventname');
        $data = ['courseid' => $course->id, 'sortorder' => -999, 'points' => 1, 'rule' => $rule];
        block_xp_filter::load_from_data($data)->save();
        $fm->invalidate_filters_cache();

        $e = \block_xp\event\something_happened::mock(['crud' => 'u', 'objectid' => 2]);
        $this->assertSame(1, $fm->get_points_for_event($e));
        $e = \block_xp\event\something_happened::mock(['crud' => 'r', 'objectid' => 100]);
        $this->assertSame(1, $fm->get_points_for_event($e));
        $e = \block_xp\event\something_happened::mock(['crud' => 'd']);
        $this->assertSame(1, $fm->get_points_for_event($e));
        $e = \block_xp\event\something_happened::mock(['crud' => 'r']);
        $this->assertSame(1, $fm->get_points_for_event($e));

    }

    public function test_validate_data(): void {
        $this->assertTrue(block_xp_filter::validate_data([]));

        // Rule data.
        $this->assertTrue(block_xp_filter::validate_data([
            'ruledata' => json_encode(['_class' => 'block_xp_rule_property']),
        ]));
        $this->assertFalse(block_xp_filter::validate_data([
            'ruledata' => json_encode(['_class' => 'core_user']),
        ]));

        // IDs where loose empty values are OK.
        $this->assertTrue(block_xp_filter::validate_data(['id' => ""]));
        $this->assertTrue(block_xp_filter::validate_data(['id' => "0"]));
        $this->assertTrue(block_xp_filter::validate_data(['id' => 0]));
        $this->assertTrue(block_xp_filter::validate_data(['id' => "2"]));
        $this->assertTrue(block_xp_filter::validate_data(['id' => 2]));

        // Course IDs where loose empty values are OK.
        $this->assertTrue(block_xp_filter::validate_data(['courseid' => ""]));
        $this->assertTrue(block_xp_filter::validate_data(['courseid' => "0"]));
        $this->assertTrue(block_xp_filter::validate_data(['courseid' => 0]));
        $this->assertTrue(block_xp_filter::validate_data(['courseid' => "2"]));
        $this->assertTrue(block_xp_filter::validate_data(['courseid' => 2]));

        // Points, must be valid integers.
        $this->assertTrue(block_xp_filter::validate_data(['points' => ""]));
        $this->assertTrue(block_xp_filter::validate_data(['points' => "0"]));
        $this->assertTrue(block_xp_filter::validate_data(['points' => 0]));
        $this->assertTrue(block_xp_filter::validate_data(['points' => "2"]));
        $this->assertTrue(block_xp_filter::validate_data(['points' => 2]));

        // Category.
        $this->assertFalse(block_xp_filter::validate_data(['category' => 20]));
        $this->assertTrue(block_xp_filter::validate_data(['category' => ""])); // Auto cast to 0.
        $this->assertTrue(block_xp_filter::validate_data(['category' => "abc"])); // Auto cast to 0.
        $this->assertTrue(block_xp_filter::validate_data(['category' => block_xp_filter::CATEGORY_EVENTS]));
        $this->assertTrue(block_xp_filter::validate_data(['category' => block_xp_filter::CATEGORY_GRADES]));
        $this->assertTrue(block_xp_filter::validate_data(['category' => (string) block_xp_filter::CATEGORY_EVENTS]));
        $this->assertTrue(block_xp_filter::validate_data(['category' => (string) block_xp_filter::CATEGORY_GRADES]));
    }

    public function test_load_from_data(): void {
        $filter = block_xp_filter::load_from_data((object) [
            'ruledata' => json_encode(['_class' => 'block_xp_rule_property']),
        ]);
        $this->assertInstanceOf('block_xp_rule_property', $filter->get_rule());

        // IDs where loose empty values are OK.
        $filter = block_xp_filter::load_from_data((object) ['id' => ""]);
        $this->assertEmpty($filter->get_id());
        $this->assertSame(null, $filter->get_id());
        $filter = block_xp_filter::load_from_data((object) ['id' => "0"]);
        $this->assertEmpty($filter->get_id());
        $this->assertSame(null, $filter->get_id());
        $filter = block_xp_filter::load_from_data((object) ['id' => 0]);
        $this->assertEmpty($filter->get_id());
        $this->assertSame(null, $filter->get_id());
        $filter = block_xp_filter::load_from_data((object) ['id' => "2"]);
        $this->assertSame(2, $filter->get_id());
        $filter = block_xp_filter::load_from_data((object) ['id' => 2]);
        $this->assertSame(2, $filter->get_id());

        // Course IDs where loose empty values are OK.
        $filter = block_xp_filter::load_from_data((object) ['courseid' => ""]);
        $this->assertEmpty($filter->export()->courseid);
        $this->assertSame(0, $filter->export()->courseid);
        $filter = block_xp_filter::load_from_data((object) ['courseid' => "0"]);
        $this->assertEmpty($filter->export()->courseid);
        $this->assertSame(0, $filter->export()->courseid);
        $filter = block_xp_filter::load_from_data((object) ['courseid' => 0]);
        $this->assertEmpty($filter->export()->courseid);
        $this->assertSame(0, $filter->export()->courseid);
        $filter = block_xp_filter::load_from_data((object) ['courseid' => "2"]);
        $this->assertSame(2, $filter->export()->courseid);
        $filter = block_xp_filter::load_from_data((object) ['courseid' => 2]);
        $this->assertSame(2, $filter->export()->courseid);

        // Points.
        $filter = block_xp_filter::load_from_data(['points' => ""]);
        $this->assertSame(0, $filter->get_points());
        $filter = block_xp_filter::load_from_data(['points' => "0"]);
        $this->assertSame(0, $filter->get_points());
        $filter = block_xp_filter::load_from_data(['points' => 0]);
        $this->assertSame(0, $filter->get_points());
        $filter = block_xp_filter::load_from_data(['points' => "2"]);
        $this->assertSame(2, $filter->get_points());
        $filter = block_xp_filter::load_from_data(['points' => 2]);
        $this->assertSame(2, $filter->get_points());

        // Sortorder.
        $filter = block_xp_filter::load_from_data(['sortorder' => ""]);
        $this->assertSame(0, $filter->get_sortorder());
        $filter = block_xp_filter::load_from_data(['sortorder' => "0"]);
        $this->assertSame(0, $filter->get_sortorder());
        $filter = block_xp_filter::load_from_data(['sortorder' => 0]);
        $this->assertSame(0, $filter->get_sortorder());
        $filter = block_xp_filter::load_from_data(['sortorder' => "2"]);
        $this->assertSame(2, $filter->get_sortorder());
        $filter = block_xp_filter::load_from_data(['sortorder' => 2]);
        $this->assertSame(2, $filter->get_sortorder());

        // Category.
        $filter = block_xp_filter::load_from_data(['category' => ""]);
        $this->assertEquals(block_xp_filter::CATEGORY_EVENTS, $filter->get_category());
        $filter = block_xp_filter::load_from_data(['category' => "abc"]);
        $this->assertEquals(block_xp_filter::CATEGORY_EVENTS, $filter->get_category());
        $filter = block_xp_filter::load_from_data(['category' => block_xp_filter::CATEGORY_EVENTS]);
        $this->assertEquals(block_xp_filter::CATEGORY_EVENTS, $filter->get_category());
        $filter = block_xp_filter::load_from_data(['category' => block_xp_filter::CATEGORY_GRADES]);
        $this->assertEquals(block_xp_filter::CATEGORY_GRADES, $filter->get_category());
        $filter = block_xp_filter::load_from_data(['category' => (string) block_xp_filter::CATEGORY_EVENTS]);
        $this->assertEquals(block_xp_filter::CATEGORY_EVENTS, $filter->get_category());
        $filter = block_xp_filter::load_from_data(['category' => (string) block_xp_filter::CATEGORY_GRADES]);
        $this->assertEquals(block_xp_filter::CATEGORY_GRADES, $filter->get_category());
    }

}
