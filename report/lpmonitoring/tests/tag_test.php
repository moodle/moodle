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
 * Tags tests.
 *
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2018 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_lpmonitoring;
defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_competency\plan;
use report_lpmonitoring\api;
use core_competency\api as core_competency_api;
use tool_cohortroles\api as tool_cohortroles_api;
use report_lpmonitoring\report_competency_config;
use core\invalid_persistent_exception;

/**
 * Tags tests.
 *
 * @covers     \report_lpmonitoring
 * @package    report_lpmonitoring
 * @author     Issam Taboubi <issam.taboubi@umontreal.ca>
 * @copyright  2018 Université de Montréal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tag_test extends \advanced_testcase {

    /**
     * manage tags.
     */
    public function test_plan_tags() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('core_competency');

        $user1 = $dg->create_user(array('lastname' => 'Austin', 'firstname' => 'Sharon'));
        $user2 = $dg->create_user(array('lastname' => 'Cortez', 'firstname' => 'Jonathan'));
        $user3 = $dg->create_user(array('lastname' => 'Underwood', 'firstname' => 'Alicia'));
        $user1context = \context_user::instance($user1->id);
        $user2context = \context_user::instance($user2->id);
        $user3context = \context_user::instance($user3->id);

        $plan1 = $lpg->create_plan(array('userid' => $user1->id));
        $plan2 = $lpg->create_plan(array('userid' => $user2->id));
        $plan3 = $lpg->create_plan(array('userid' => $user3->id));
        // Test add tags.
        \core_tag_tag::add_item_tag('report_lpmonitoring', 'competency_plan', $plan1->get('id'), $user1context, 'Tag plan 1 and 2');
        \core_tag_tag::add_item_tag('report_lpmonitoring', 'competency_plan', $plan2->get('id'), $user2context, 'Tag plan 1 and 2');
        \core_tag_tag::add_item_tag('report_lpmonitoring', 'competency_plan', $plan3->get('id'), $user3context, 'Tag plan 3');

        $collid = \core_tag_collection::get_default();
        $tag12 = \core_tag_tag::get_by_name($collid, 'Tag plan 1 and 2', '*');
        $this->assertEquals('Tag plan 1 and 2', $tag12->get_display_name());
        $items = $tag12->get_tagged_items('report_lpmonitoring', 'competency_plan');
        $this->assertCount(2, $items);
        $this->assertEquals($plan1->get('id'), $items[$plan1->get('id')]->id);
        $this->assertEquals($plan2->get('id'), $items[$plan2->get('id')]->id);
        // Test tag : Tag plan 3.
        $items = \core_tag_tag::get_by_name($collid, 'Tag plan 3', '*')->get_tagged_items('report_lpmonitoring', 'competency_plan');
        $this->assertCount(1, $items);
        $this->assertEquals($plan3->get('id'), $items[$plan3->get('id')]->id);
        // Test delete tags.
        \core_tag_tag::remove_item_tag('report_lpmonitoring', 'competency_plan', $plan1->get('id'), 'Tag plan 1 and 2');
        $tags = \core_tag_tag::get_item_tags('report_lpmonitoring', 'competency_plan', $plan1->get('id'));
        $this->assertEmpty($tags);
        \core_tag_tag::remove_item_tag('report_lpmonitoring', 'competency_plan', $plan2->get('id'), 'Tag plan 1 and 2');
        $tags = \core_tag_tag::get_item_tags('report_lpmonitoring', 'competency_plan', $plan2->get('id'));
        $this->assertEmpty($tags);
        \core_tag_tag::remove_item_tag('report_lpmonitoring', 'competency_plan', $plan3->get('id'), 'Tag plan 3');
        $tags = \core_tag_tag::get_item_tags('report_lpmonitoring', 'competency_plan', $plan3->get('id'));
        $this->assertEmpty($tags);

        $tag = \core_tag_tag::get_by_name($collid, 'Tag plan 1 and 2', '*');
        $this->assertFalse($tag);
        $tag = \core_tag_tag::get_by_name($collid, 'Tag plan 3', '*');
        $this->assertFalse($tag);
    }
}
