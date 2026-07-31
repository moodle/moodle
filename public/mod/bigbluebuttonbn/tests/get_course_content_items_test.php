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

namespace mod_bigbluebuttonbn;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/bigbluebuttonbn/lib.php');

use core_course\local\entity\content_item;
use core_course\local\entity\lang_string_title;

/**
 * Tests for the bigbluebuttonbn_get_course_content_items() callback.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2026 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    ::bigbluebuttonbn_get_course_content_items
 */
final class get_course_content_items_test extends \advanced_testcase {
    /**
     * Build a minimal content_item to pass as $defaultitem.
     *
     * @param \stdClass $course
     * @return content_item
     */
    private function make_default_item(\stdClass $course): content_item {
        return new content_item(
            id: 1,
            name: 'bigbluebuttonbn',
            title: new lang_string_title('modulename', 'mod_bigbluebuttonbn'),
            link: new \moodle_url('/course/mod.php', ['id' => $course->id, 'add' => 'bigbluebuttonbn']),
            icon: '',
            help: '',
            archetype: MOD_ARCHETYPE_OTHER,
            componentname: 'mod_bigbluebuttonbn',
            purpose: MOD_PURPOSE_COMMUNICATION,
        );
    }

    /**
     * When both server_url and shared_secret are set, the default item is returned unchanged.
     */
    public function test_returns_enabled_item_when_configured(): void {
        $this->resetAfterTest();

        set_config('bigbluebuttonbn_server_url', 'https://bbb.example.com/bigbluebutton/');
        set_config('bigbluebuttonbn_shared_secret', 'some-secret-value');

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $defaultitem = $this->make_default_item($course);

        $result = bigbluebuttonbn_get_course_content_items($defaultitem, $user, $course);

        $this->assertCount(1, $result);
        $this->assertFalse($result[0]->is_disabled());
        $this->assertNull($result[0]->get_disabled_reason());
    }

    /**
     * When server_url is empty, the returned item is disabled.
     */
    public function test_returns_disabled_item_when_server_url_empty(): void {
        $this->resetAfterTest();

        set_config('bigbluebuttonbn_server_url', '');
        set_config('bigbluebuttonbn_shared_secret', 'some-secret-value');

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $defaultitem = $this->make_default_item($course);

        $result = bigbluebuttonbn_get_course_content_items($defaultitem, $user, $course);

        $this->assertCount(1, $result);
        $this->assertTrue($result[0]->is_disabled());
        $this->assertNotEmpty($result[0]->get_disabled_reason());
    }

    /**
     * When shared_secret is empty, the returned item is disabled.
     */
    public function test_returns_disabled_item_when_shared_secret_empty(): void {
        $this->resetAfterTest();

        set_config('bigbluebuttonbn_server_url', 'https://bbb.example.com/bigbluebutton/');
        set_config('bigbluebuttonbn_shared_secret', '');

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $defaultitem = $this->make_default_item($course);

        $result = bigbluebuttonbn_get_course_content_items($defaultitem, $user, $course);

        $this->assertCount(1, $result);
        $this->assertTrue($result[0]->is_disabled());
    }

    /**
     * An admin sees the admin-specific disabled reason.
     */
    public function test_disabled_reason_for_admin_when_unconfigured(): void {
        $this->resetAfterTest();

        set_config('bigbluebuttonbn_server_url', '');
        set_config('bigbluebuttonbn_shared_secret', '');

        $course = $this->getDataGenerator()->create_course();
        $admin = get_admin();
        $defaultitem = $this->make_default_item($course);

        $result = bigbluebuttonbn_get_course_content_items($defaultitem, $admin, $course);

        $this->assertTrue($result[0]->is_disabled());
        $this->assertEquals(
            get_string('unconfigured_chooser_admin', 'mod_bigbluebuttonbn'),
            $result[0]->get_disabled_reason()
        );
    }

    /**
     * A non-admin user sees the user-facing disabled reason.
     */
    public function test_disabled_reason_for_non_admin_when_unconfigured(): void {
        $this->resetAfterTest();

        set_config('bigbluebuttonbn_server_url', '');
        set_config('bigbluebuttonbn_shared_secret', '');

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $defaultitem = $this->make_default_item($course);

        $result = bigbluebuttonbn_get_course_content_items($defaultitem, $teacher, $course);

        $this->assertTrue($result[0]->is_disabled());
        $this->assertEquals(
            get_string('unconfigured_chooser_user', 'mod_bigbluebuttonbn'),
            $result[0]->get_disabled_reason()
        );
    }

    /**
     * The returned item preserves all properties from the default item.
     */
    public function test_disabled_item_preserves_default_item_properties(): void {
        $this->resetAfterTest();

        set_config('bigbluebuttonbn_server_url', '');
        set_config('bigbluebuttonbn_shared_secret', '');

        $course = $this->getDataGenerator()->create_course();
        $user = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $defaultitem = $this->make_default_item($course);

        $result = bigbluebuttonbn_get_course_content_items($defaultitem, $user, $course);

        $this->assertEquals($defaultitem->get_id(), $result[0]->get_id());
        $this->assertEquals($defaultitem->get_name(), $result[0]->get_name());
        $this->assertEquals($defaultitem->get_component_name(), $result[0]->get_component_name());
        $this->assertEquals($defaultitem->get_purpose(), $result[0]->get_purpose());
    }
}
