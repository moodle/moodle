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
 * Test case.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp;

use block_xp\tests\restore_context_mock;
use block_xp\local\xp\level_with_badge;
use block_xp\local\xp\level_with_description;
use block_xp\local\xp\level_with_name;
use block_xp\tests\base_testcase;

/**
 * Test case.
 *
 * @package    block_xp
 * @copyright  2023 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class levels_info_writer_test extends base_testcase {

    /**
     * Data provider.
     *
     * @return array
     */
    public static function update_world_after_restore_provider(): array {
        return [
            [''],
            ['{"xp":{"1":0,"2":120,"3":276,"4":479,"5":742,"6":1085,"7":1531,"8":2110,"9":2863,"10":3842},'
                . '"name":[],"desc":[],"base":120,"coef":1.3,"usealgo":true}', ],
            ['{"xp":{"1":0,"2":120,"3":264,"4":437,"5":644,"6":893,"7":1192,"8":1550,"9":1980,"10":2496},'
                . '"name":[],"desc":[],"base":120,"coef":1.2,"usealgo":true}', ],
            ['{"xp":{"1":0,"2":200,"3":300,"4":400,"5":743,"6":1000,"7":1532,"8":2112,"9":2866,"10":3846},'
                . '"name":{"2":"Num\u00e9ro 2","5":"Name 5"},"desc":{"2":"Desc 2","3":"Desc 3"},'
                . '"base":120,"coef":1.3,"usealgo":false}', ],
            ['{"xp":{"1":0,"2":100,"3":210,"4":331,"5":464,"6":611,"7":772,"8":949,"9":1144,"10":1358},'
                . '"name":{"2":"Niveau 2","3":"Niveau 3","9":"Nom 9"},"desc":{"2":"Desc 2","4":"Desc 4","9":"Desc 9"},'
                . '"base":100,"coef":1.1000000000000001,"usealgo":true}', ],
            ['{"v":2,"xp":[0,120,276,479,742,1085,1531,2110,2863,3842],'
                . '"algo":{"base":120,"coef":1.3,"incr":40,"method":"relative"}}', ],
            ['{"v":2,"xp":[0,80,180,300,440,600],"algo":{"base":80,"coef":1.3,"incr":20,"method":"linear"}}'],
            ['{"v":2,"xp":[0,77,154,231,308,385,462,539],"algo":{"base":77,"coef":1.3,"incr":20,"method":"flat"}}'],
        ];
    }

    /**
     * Test.
     *
     * @covers \block_xp\local\xp\levels_info_writer::update_world_after_restore
     * @dataProvider update_world_after_restore_provider
     * @param string $rawlevelsdata The raw levels data.
     */
    public function test_update_world_after_restore($rawlevelsdata): void {
        $c1 = $this->getDataGenerator()->create_course();
        $world = $this->get_world($c1->id);
        $world->get_config()->set('levelsdata', $rawlevelsdata);
        $prelevels = $world->get_levels_info()->get_levels();

        $writer = di::get('levels_info_writer');
        $writer->update_world_after_restore(new restore_context_mock([]), $world);

        $this->reset_container();

        $world = $this->get_world($c1->id);
        $postlevels = $world->get_levels_info()->get_levels();
        $this->assert_levels_equal($prelevels, $postlevels);
    }

    /**
     * Assert two array of levels are the same.
     *
     * @param level[] $levelsexpected The expected level.
     * @param level[] $levelsactual The actual level.
     */
    protected function assert_levels_equal($levelsexpected, $levelsactual) {
        $this->assertEquals(count($levelsexpected), count($levelsactual));
        foreach ($levelsexpected as $key => $levelexpected) {
            $this->assert_level_equal($levelexpected, $levelsactual[$key]);
        }
    }

    /**
     * Assert two levels are the same.
     *
     * @param level $levelexpected The expected level.
     * @param level $levelactual The actual level.
     */
    protected function assert_level_equal($levelexpected, $levelactual) {
        $this->assertEquals($levelexpected->get_level(), $levelactual->get_level());
        $this->assertEquals($levelexpected->get_xp_required(), $levelactual->get_xp_required());
        if ($levelexpected instanceof level_with_name) {
            $this->assertInstanceOf(level_with_name::class, $levelactual);
            $this->assertEquals($levelexpected->get_name(), $levelactual->get_name());
        } else {
            $this->assertNotInstanceOf(level_with_name::class, $levelactual);
        }
        if ($levelexpected instanceof level_with_description) {
            $this->assertInstanceOf(level_with_description::class, $levelactual);
            $this->assertEquals($levelexpected->get_description(), $levelactual->get_description());
        } else {
            $this->assertNotInstanceOf(level_with_description::class, $levelactual);
        }
        if ($levelexpected instanceof level_with_badge) {
            $this->assertInstanceOf(level_with_badge::class, $levelactual);
            $this->assertEquals($levelexpected->get_badge_url(), $levelactual->get_badge_url());
        } else {
            $this->assertNotInstanceOf(level_with_badge::class, $levelactual);
        }
    }
}
