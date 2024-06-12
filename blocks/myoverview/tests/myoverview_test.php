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

namespace block_myoverview;

/**
 * Online users testcase
 *
 * @package    block_myoverview
 * @category   test
 * @copyright  2019 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class myoverview_test extends \advanced_testcase {

    /**
     * Test getting block configuration
     */
    public function test_get_block_config_for_external(): void {
        global $PAGE, $CFG, $OUTPUT;
        require_once($CFG->dirroot . '/my/lib.php');

        $this->resetAfterTest(true);

        $user = $this->getDataGenerator()->create_user();

        $fieldcategory = self::getDataGenerator()->create_custom_field_category(['name' => 'Other fields']);

        $customfield = ['shortname' => 'test', 'name' => 'Custom field', 'type' => 'text',
            'categoryid' => $fieldcategory->get('id')];
        $field = self::getDataGenerator()->create_custom_field($customfield);

        $customfieldvalue = ['shortname' => 'test', 'value' => 'Test value I'];
        $course1  = self::getDataGenerator()->create_course(['customfields' => [$customfieldvalue]]);
        $customfieldvalue = ['shortname' => 'test', 'value' => 'Test value II'];
        $course2  = self::getDataGenerator()->create_course(['customfields' => [$customfieldvalue]]);
        $this->getDataGenerator()->enrol_user($user->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user->id, $course2->id, 'student');

        // Force a setting change to check the returned blocks settings.
        set_config('displaygroupingcustomfield', 1, 'block_myoverview');
        set_config('customfiltergrouping', $field->get('shortname'), 'block_myoverview');

        $this->setUser($user);
        $context = \context_user::instance($user->id);

        if (!$currentpage = my_get_page($user->id, MY_PAGE_PUBLIC, MY_PAGE_COURSES)) {
            throw new \moodle_exception('mymoodlesetup');
        }

        $PAGE->set_url('/my/courses.php');    // Need this because some internal API calls require the $PAGE url to be set.
        $PAGE->set_context($context);
        $PAGE->set_pagelayout('mydashboard');
        $PAGE->set_pagetype('my-index');
        $PAGE->blocks->add_region('content');   // Need to add this special region to retrieve the central blocks.
        $PAGE->set_subpage($currentpage->id);

        // Load the block instances for all the regions.
        $PAGE->blocks->load_blocks();
        $PAGE->blocks->create_all_block_instances();

        $blocks = $PAGE->blocks->get_content_for_all_regions($OUTPUT);
        $configs = null;
        foreach ($blocks as $region => $regionblocks) {
            $regioninstances = $PAGE->blocks->get_blocks_for_region($region);

            foreach ($regioninstances as $ri) {
                // Look for myoverview block only.
                if ($ri->instance->blockname == 'myoverview') {
                    $configs = $ri->get_config_for_external();
                    break 2;
                }
            }
        }

        // Test we receive all we expect (exact number and values of settings).
        $this->assertNotEmpty($configs);
        $this->assertEmpty((array) $configs->instance);
        $this->assertCount(13, (array) $configs->plugin);
        $this->assertEquals('test', $configs->plugin->customfiltergrouping);
        // Test default values.
        $this->assertEquals(1, $configs->plugin->displaycategories);
        $this->assertEquals(1, $configs->plugin->displaygroupingall);
        $this->assertEquals(0, $configs->plugin->displaygroupingallincludinghidden);
        $this->assertEquals(1, $configs->plugin->displaygroupingcustomfield);
        $this->assertEquals(1, $configs->plugin->displaygroupingfuture);
        $this->assertEquals(1, $configs->plugin->displaygroupinghidden);
        $this->assertEquals(1, $configs->plugin->displaygroupinginprogress);
        $this->assertEquals(1, $configs->plugin->displaygroupingpast);
        $this->assertEquals(1, $configs->plugin->displaygroupingfavourites);
        $this->assertEquals('card,list,summary', $configs->plugin->layouts);
        $this->assertEquals(get_config('block_myoverview', 'version'), $configs->plugin->version);
        // Test custom fields.
        $this->assertJson($configs->plugin->customfieldsexport);
        $fields = json_decode($configs->plugin->customfieldsexport);
        $this->assertEquals('Test value I', $fields[0]->name);
        $this->assertEquals('Test value I', $fields[0]->value);
        $this->assertFalse($fields[0]->active);
        $this->assertEquals('Test value II', $fields[1]->name);
        $this->assertEquals('Test value II', $fields[1]->value);
        $this->assertFalse($fields[1]->active);
        $this->assertEquals('No Custom field', $fields[2]->name);
        $this->assertFalse($fields[2]->active);
    }
}
