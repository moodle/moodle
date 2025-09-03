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

use core_ai\ai_test_trait;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../../tests/ai_test_trait.php');

/**
 * Text editor placement utils test.
 *
 * @package    aiplacement_editor
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \aiplacement_editor\utils
 */
final class utils_test extends \advanced_testcase {
    use ai_test_trait;

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
     * Data provider for supported placement tests.
     *
     * @return array
     */
    public static function html_editor_placement_actions_available_provider(): array {
        return [
            'Two actions' => [
                'actionstouse' => [
                    'generate_text',
                    'generate_image',
                ],
                'expectedcount' => 2,
            ],
            'Generate text only' => [
                'actionstouse' => [
                    'generate_text',
                ],
                'expectedcount' => 1,
            ],
            'Generate image only' => [
                'actionstouse' => [
                    'generate_image',
                ],
                'expectedcount' => 1,
            ],
            'No actions' => [
                'actionstouse' => [],
                'expectedcount' => 0,
            ],
        ];
    }

    /**
     * Test is_html_editor_placement_action_available method.
     */
    public function test_is_html_editor_placement_action_available(): void {
        // Everything is disabled to begin with, and user does not have capability.
        // Sequentially enable settings until all conditions are met.
        $actionname = 'generate_text';
        $actionclass = 'core_ai\\aiactions\\' . $actionname;
        set_config('enabled', 0, 'aiplacement_editor');
        set_config($actionname, 0, 'aiplacement_editor');
        assign_capability("aiplacement/editor:{$actionname}", CAP_PROHIBIT, $this->teacherrole->id, $this->context);
        $this->setUser($this->users[2]);

        // Enable the placement plugin.
        set_config('enabled', 1, 'aiplacement_editor');
        $this->assertFalse(utils::is_html_editor_placement_action_available(
            context: $this->context,
            actionname: $actionname,
            actionclass: $actionclass
        ));

        // Enable the provider.
        $this->create_ai_provider([$actionname], \aiprovider_openai\provider::class);
        $this->assertFalse(utils::is_html_editor_placement_action_available(
            context: $this->context,
            actionname: $actionname,
            actionclass: $actionclass
        ));

        // Switch to a user with the required capability.
        $this->setUser($this->users[1]);
        $this->assertFalse(utils::is_html_editor_placement_action_available(
            context: $this->context,
            actionname: $actionname,
            actionclass: $actionclass
        ));

        // Enable the action for the placement plugin.
        // All requirements should now be met.
        set_config($actionname, 1, 'aiplacement_editor');
        $this->assertTrue(utils::is_html_editor_placement_action_available(
            context: $this->context,
            actionname: $actionname,
            actionclass: $actionclass
        ));
    }

    /**
     * Test get_actions_available method.
     *
     * @param array $actionstouse The actions to use.
     * @param int $expectedcount Expected count of actions.
     * @dataProvider html_editor_placement_actions_available_provider
     */
    public function test_get_actions_available(
        array $actionstouse,
        int $expectedcount,
    ): void {
        $this->setUser($this->users[2]);
        // Set up the provider with the required action config.
        $this->create_ai_provider($actionstouse, \aiprovider_openai\provider::class);
        set_config('enabled', 1, 'aiplacement_editor');

        // Enable the actions and check the count.
        foreach ($actionstouse as $action) {
            set_config($action, 1, 'aiplacement_editor');
        }
        $actions = utils::get_actions_available($this->context, true);
        $this->assertCount($expectedcount, $actions);

        // Prohibit the user and check again.
        foreach ($actionstouse as $action) {
            assign_capability("aiplacement/editor:{$action}", CAP_PROHIBIT, $this->teacherrole->id, $this->context);
        }
        $actions = utils::get_actions_available($this->context, true);
        $this->assertCount(0, $actions);
    }

    /**
     * Test is_html_editor_placement_available method.
     */
    public function test_is_html_editor_placement_available(): void {
        // Plugin is not enabled.
        set_config('enabled', 0, 'aiplacement_editor');
        $this->assertFalse(utils::is_html_editor_placement_available());

        // Plugin is enabled.
        set_config('enabled', 1, 'aiplacement_editor');
        $this->assertTrue(utils::is_html_editor_placement_available());
    }
}
