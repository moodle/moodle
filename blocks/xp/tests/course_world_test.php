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
 * Block XP course world test.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/fixtures/events.php');

use block_xp\local\config\config_stack;
use block_xp\local\config\default_course_world_config;
use block_xp\local\config\static_config;
use block_xp\local\course_world;
use block_xp\local\xp\algo_levels_info;
use block_xp\tests\base_testcase;

/**
 * Course world testcase.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \block_xp\local\local\course_world
 */
final class course_world_test extends base_testcase {

    public function test_reset_data(): void {
        global $DB;

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $this->getDataGenerator()->enrol_user($u2->id, $c1->id);
        $this->getDataGenerator()->enrol_user($u1->id, $c2->id);

        $world = $this->get_world($c1->id);
        $world->get_config()->set_many(['enabled' => true, 'timebetweensameactions' => 0]);
        $strategy = $world->get_collection_strategy();

        $e = \block_xp\event\something_happened::mock(['crud' => 'c', 'userid' => $u1->id, 'courseid' => $c1->id]);
        $strategy->collect_event($e);
        $strategy->collect_event($e);

        $e = \block_xp\event\something_happened::mock(['crud' => 'c', 'userid' => $u2->id, 'courseid' => $c1->id]);
        $strategy->collect_event($e);
        $strategy->collect_event($e);

        $world = $this->get_world($c2->id);
        $world->get_config()->set_many(['enabled' => true, 'timebetweensameactions' => 0]);
        $strategy = $world->get_collection_strategy();

        $e = \block_xp\event\something_happened::mock(['crud' => 'c', 'userid' => $u1->id, 'courseid' => $c2->id]);
        $strategy->collect_event($e);

        $this->assertEquals(2, $DB->count_records('block_xp', ['courseid' => $c1->id]));
        $this->assertEquals(4, $DB->count_records('block_xp_log', ['courseid' => $c1->id]));
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c2->id]));
        $this->assertEquals(1, $DB->count_records('block_xp_log', ['courseid' => $c2->id]));

        $world = $this->get_world($c1->id);
        $world->get_store()->reset();

        $this->assertEquals(0, $DB->count_records('block_xp', ['courseid' => $c1->id]));
        $this->assertEquals(0, $DB->count_records('block_xp_log', ['courseid' => $c1->id]));
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c2->id]));
        $this->assertEquals(1, $DB->count_records('block_xp_log', ['courseid' => $c2->id]));
    }

    public function test_reset_data_with_groups(): void {
        global $DB;

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $g1 = $this->getDataGenerator()->create_group(['courseid' => $c1->id]);

        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $this->getDataGenerator()->enrol_user($u2->id, $c1->id);
        $this->getDataGenerator()->enrol_user($u1->id, $c2->id);
        $this->getDataGenerator()->create_group_member(['groupid' => $g1->id, 'userid' => $u1->id]);

        $world = $this->get_world($c1->id);
        $world->get_config()->set_many(['enabled' => true, 'timebetweensameactions' => 0]);
        $strategy = $world->get_collection_strategy();

        $e = \block_xp\event\something_happened::mock(['crud' => 'c', 'userid' => $u1->id, 'courseid' => $c1->id]);
        $strategy->collect_event($e);
        $strategy->collect_event($e);

        $e = \block_xp\event\something_happened::mock(['crud' => 'c', 'userid' => $u2->id, 'courseid' => $c1->id]);
        $strategy->collect_event($e);
        $strategy->collect_event($e);

        $world = $this->get_world($c2->id);
        $world->get_config()->set_many(['enabled' => true, 'timebetweensameactions' => 0]);
        $strategy = $world->get_collection_strategy();

        $e = \block_xp\event\something_happened::mock(['crud' => 'c', 'userid' => $u1->id, 'courseid' => $c2->id]);
        $strategy->collect_event($e);

        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(2, $DB->count_records('block_xp_log', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(2, $DB->count_records('block_xp_log', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c2->id]));
        $this->assertEquals(1, $DB->count_records('block_xp_log', ['courseid' => $c2->id]));

        $world = $this->get_world($c1->id);
        $world->get_store()->reset_by_group($g1->id);

        $this->assertEquals(0, $DB->count_records('block_xp', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(0, $DB->count_records('block_xp_log', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(2, $DB->count_records('block_xp_log', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c2->id]));
        $this->assertEquals(1, $DB->count_records('block_xp_log', ['courseid' => $c2->id]));
    }

    public function test_delete_user_state(): void {
        global $DB;

        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $g1 = $this->getDataGenerator()->create_group(['courseid' => $c1->id]);

        $this->getDataGenerator()->enrol_user($u1->id, $c1->id);
        $this->getDataGenerator()->enrol_user($u2->id, $c1->id);
        $this->getDataGenerator()->enrol_user($u1->id, $c2->id);

        $world = $this->get_world($c1->id);
        $world->get_config()->set_many(['enabled' => true, 'timebetweensameactions' => 0]);
        $strategy = $world->get_collection_strategy();

        $e = \block_xp\event\something_happened::mock(['crud' => 'c', 'userid' => $u1->id, 'courseid' => $c1->id]);
        $strategy->collect_event($e);
        $strategy->collect_event($e);

        $e = \block_xp\event\something_happened::mock(['crud' => 'c', 'userid' => $u2->id, 'courseid' => $c1->id]);
        $strategy->collect_event($e);
        $strategy->collect_event($e);

        $world = $this->get_world($c2->id);
        $world->get_config()->set_many(['enabled' => true, 'timebetweensameactions' => 0]);
        $strategy = $world->get_collection_strategy();

        $e = \block_xp\event\something_happened::mock(['crud' => 'c', 'userid' => $u1->id, 'courseid' => $c2->id]);
        $strategy->collect_event($e);

        $world = $this->get_world($c1->id);

        $this->assertGreaterThan(0, $world->get_store()->get_state($u1->id)->get_xp());
        $this->assertGreaterThan(0, $world->get_store()->get_state($u2->id)->get_xp());
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(2, $DB->count_records('block_xp_log', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(2, $DB->count_records('block_xp_log', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c2->id]));
        $this->assertEquals(1, $DB->count_records('block_xp_log', ['courseid' => $c2->id]));

        $world->get_store()->delete($u1->id);

        $this->assertEquals(0, $world->get_store()->get_state($u1->id)->get_xp());
        $this->assertGreaterThan(0, $world->get_store()->get_state($u2->id)->get_xp());
        $this->assertEquals(0, $DB->count_records('block_xp', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(0, $DB->count_records('block_xp_log', ['courseid' => $c1->id, 'userid' => $u1->id]));
        $this->assertEquals(2, $DB->count_records('block_xp_log', ['courseid' => $c1->id, 'userid' => $u2->id]));
        $this->assertEquals(1, $DB->count_records('block_xp', ['courseid' => $c2->id]));
        $this->assertEquals(1, $DB->count_records('block_xp_log', ['courseid' => $c2->id]));
    }

    public function test_levels_info_loading(): void {
        global $DB;
        $config = new config_stack([
            new static_config([
                'levelsdata' => '{"xp":{"1":0,"2":120,"3":264,"4":437,"5":644,"6":893},"name":{"1":"A","2":"Level Too!",'
                    . '"3":"aaaa","6":"X"},"desc":{"1":"a","2":"bB","3":"3","5":"five","6":"xx"},"base":120,"coef":1.2,'
                    . '"usealgo":false}',
            ]),
            new default_course_world_config(),
        ]);
        $world = new course_world($config, $DB, 1, di::get('badge_url_resolver_course_world_factory'));
        $levelsinfo = $world->get_levels_info();

        $this->assertInstanceOf(algo_levels_info::class, $levelsinfo);
        $this->assertEquals(120, $levelsinfo->get_base());
        $this->assertEquals(1.2, $levelsinfo->get_coef());
        $this->assertEquals(6, $levelsinfo->get_count());
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 264,
            4 => 437,
            5 => 644,
            6 => 893,
        ], array_reduce($levelsinfo->get_levels(), function($carry, $level) {
            $carry[$level->get_level()] = $level->get_xp_required();
            return $carry;
        }, []));

        $this->assertEquals('A', $levelsinfo->get_level(1)->get_name());
        $this->assertEquals('a', $levelsinfo->get_level(1)->get_description());

        $config = new config_stack([
            new static_config([
                'levelsdata' => '{"xp":{"1":0,"2":120,"3":276,"4":479,"5":742,"6":1085,"7":1531,"8":2110,"9":2863,"10":3842},'
                    . '"name":[],"desc":[],"base":120,"coef":1.3,"usealgo":true}',
            ]),
            new default_course_world_config(),
        ]);
        $world = new course_world($config, $DB, 1, di::get('badge_url_resolver_course_world_factory'));
        $levelsinfo = $world->get_levels_info();

        $this->assertInstanceOf(algo_levels_info::class, $levelsinfo);
        $this->assertEquals(120, $levelsinfo->get_base());
        $this->assertEquals(1.3, $levelsinfo->get_coef());
        $this->assertEquals(10, $levelsinfo->get_count());
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 276,
            4 => 479,
            5 => 742,
            6 => 1085,
            7 => 1531,
            8 => 2110,
            9 => 2863,
            10 => 3842,
        ], array_reduce($levelsinfo->get_levels(), function($carry, $level) {
            $carry[$level->get_level()] = $level->get_xp_required();
            return $carry;
        }, []));

        $this->assertEquals('', $levelsinfo->get_level(1)->get_name());
        $this->assertEquals('', $levelsinfo->get_level(1)->get_description());
    }

    public function test_levels_info_loading_with_factory(): void {
        global $DB;
        $config = new config_stack([
            new static_config([
                'levelsdata' => '{"xp":{"1":0,"2":120,"3":264,"4":437,"5":644,"6":893},"name":{"1":"A","2":"Level Too!",'
                    . '"3":"aaaa","6":"X"},"desc":{"1":"a","2":"bB","3":"3","5":"five","6":"xx"},"base":120,"coef":1.2,'
                    . '"usealgo":false}',
            ]),
            new default_course_world_config(),
        ]);
        $world = new course_world($config, $DB, 1, di::get('badge_url_resolver_course_world_factory'),
            di::get('levels_info_factory'));
        $levelsinfo = $world->get_levels_info();

        $this->assertInstanceOf(algo_levels_info::class, $levelsinfo);
        $this->assertEquals(120, $levelsinfo->get_base());
        $this->assertEquals(1.2, $levelsinfo->get_coef());
        $this->assertEquals(6, $levelsinfo->get_count());
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 264,
            4 => 437,
            5 => 644,
            6 => 893,
        ], array_reduce($levelsinfo->get_levels(), function($carry, $level) {
            $carry[$level->get_level()] = $level->get_xp_required();
            return $carry;
        }, []));

        $this->assertEquals('A', $levelsinfo->get_level(1)->get_name());
        $this->assertEquals('a', $levelsinfo->get_level(1)->get_description());

        $config = new config_stack([
            new static_config([
                'levelsdata' => '{"xp":{"1":0,"2":120,"3":276,"4":479,"5":742,"6":1085,"7":1531,"8":2110,"9":2863,"10":3842},'
                    . '"name":[],"desc":[],"base":120,"coef":1.3,"usealgo":true}',
            ]),
            new default_course_world_config(),
        ]);
        $world = new course_world($config, $DB, 1, di::get('badge_url_resolver_course_world_factory'),
            di::get('levels_info_factory'));
        $levelsinfo = $world->get_levels_info();

        $this->assertInstanceOf(algo_levels_info::class, $levelsinfo);
        $this->assertEquals(120, $levelsinfo->get_base());
        $this->assertEquals(1.3, $levelsinfo->get_coef());
        $this->assertEquals(10, $levelsinfo->get_count());
        $this->assertEquals([
            1 => 0,
            2 => 120,
            3 => 276,
            4 => 479,
            5 => 742,
            6 => 1085,
            7 => 1531,
            8 => 2110,
            9 => 2863,
            10 => 3842,
        ], array_reduce($levelsinfo->get_levels(), function($carry, $level) {
            $carry[$level->get_level()] = $level->get_xp_required();
            return $carry;
        }, []));

        $this->assertEquals('', $levelsinfo->get_level(1)->get_name());
        $this->assertEquals('', $levelsinfo->get_level(1)->get_description());
    }

}
