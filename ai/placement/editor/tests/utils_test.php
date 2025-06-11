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

namespace aiplacement_editor;

use core_ai\aiactions\generate_image;
use core_ai\aiactions\generate_text;

/**
 * Text editor placement utils test.
 *
 * @package    aiplacement_editor
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \aiplacement_courseassist\utils
 */
final class utils_test extends \advanced_testcase {
    /** @var array List of users. */
    private array $users;
    /** @var \stdClass Course object. */
    private \stdClass $course;
    /** @var \context_course Course context. */
    private \context_course $context;
    /** @var \stdClass Teacher role. */
    private \stdClass $teacherrole;

    public function setUp(): void {
        global $DB;
        parent::setUp();

        $this->resetAfterTest();
        $this->users[1] = $this->getDataGenerator()->create_user();
        $this->users[2] = $this->getDataGenerator()->create_user();
        $this->course = $this->getDataGenerator()->create_course();
        $this->context = \context_course::instance($this->course->id);
        $this->teacherrole = $DB->get_record('role', ['shortname' => 'editingteacher']);
        $this->getDataGenerator()->enrol_user($this->users[1]->id, $this->course->id, 'manager');
        $this->getDataGenerator()->enrol_user($this->users[2]->id, $this->course->id, 'editingteacher');
    }

    /**
     * Test is_html_editor_placement_action_available method.
     *
     * @param string $actionname Action name.
     * @param string $actionclass Action class.
     * @dataProvider html_editor_placement_action_available_provider
     */
    public function test_is_html_editor_placement_action_available(
        string $actionname,
        string $actionclass,
    ): void {
        // Provider is not enabled.
        $this->setUser($this->users[1]);
        set_config('enabled', 0, 'aiprovider_openai');
        $this->assertFalse(utils::is_html_editor_placement_action_available(
            context: $this->context,
            actionname: $actionname,
            actionclass: $actionclass
        ));

        set_config('enabled', 1, 'aiprovider_openai');
        set_config('apikey', '123', 'aiprovider_openai');

        // Plugin is not enabled.
        $this->setUser($this->users[1]);
        set_config('enabled', 0, 'aiplacement_editor');
        $this->assertFalse(utils::is_html_editor_placement_action_available(
            context: $this->context,
            actionname: $actionname,
            actionclass: $actionclass
        ));

        // Plugin is enabled but user does not have capability.
        assign_capability("aiplacement/editor:{$actionname}", CAP_PROHIBIT, $this->teacherrole->id, $this->context);
        $this->setUser($this->users[2]);
        set_config('enabled', 1, 'aiplacement_editor');
        $this->assertFalse(utils::is_html_editor_placement_action_available(
            context: $this->context,
            actionname: $actionname,
            actionclass: $actionclass
        ));

        // Plugin is enabled, user has capability and placement action is not available.
        $this->setUser($this->users[1]);
        set_config($actionname, 0, 'aiplacement_editor');
        $this->assertFalse(utils::is_html_editor_placement_action_available(
            context: $this->context,
            actionname: $actionname,
            actionclass: $actionclass
        ));

        // Plugin is enabled, user has capability and provider action is not available.
        $this->setUser($this->users[1]);
        set_config($actionname, 0, 'aiprovider_openai');
        set_config($actionname, 1, 'aiplacement_editor');
        $this->assertFalse(utils::is_html_editor_placement_action_available(
            context: $this->context,
            actionname: $actionname,
            actionclass: $actionclass
        ));

        // Plugin is enabled, user has capability, placement action is available and provider action is available.
        $this->setUser($this->users[1]);
        set_config($actionname, 1, 'aiprovider_openai');
        set_config($actionname, 1, 'aiplacement_editor');
        $this->assertTrue(utils::is_html_editor_placement_action_available(
            context: $this->context,
            actionname: $actionname,
            actionclass: $actionclass
        ));
    }

    /**
     * Data provider for {@see test_is_html_editor_placement_action_available}
     *
     * @return array
     */
    public static function html_editor_placement_action_available_provider(): array {
        return [
            'Text generation' => [
                'generate_text',
                generate_text::class,
            ],
            'Image generation' => [
                'generate_image',
                generate_image::class,
            ],
        ];
    }
}
