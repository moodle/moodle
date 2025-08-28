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

namespace core\external;

use context_system;

/**
 * Class record_userfeedback_action_testcase
 *
 * @package core
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \core\external\record_userfeedback_action
 */
final class record_userfeedback_action_test extends \core_external\tests\externallib_testcase {
    /**
     * Data provider for test_record_userfeedback_action.
     *
     * @return  array
     */
    public static function record_userfeedback_action_provider(): array {
        return [
            'give action' => ['give'],
            'remind action' => ['remind'],
        ];
    }

    /**
     * Test the behaviour of record_userfeedback_action().
     *
     * @dataProvider record_userfeedback_action_provider
     * @param string $action The action taken by the user
     *
     * @covers ::execute
     */
    public function test_record_userfeedback_action(string $action): void {
        $this->resetAfterTest();

        $context = context_system::instance();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $eventsink = $this->redirectEvents();

        $now = time();

        // Call the WS and check the action is recorded as expected.
        $result = record_userfeedback_action::execute($action, $context->id);
        $this->assertNull($result);

        $preference = get_user_preferences('core_userfeedback_' . $action);
        $this->assertGreaterThanOrEqual($now, $preference);

        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\userfeedback_' . $action, $events[0]);
        $eventsink->clear();
    }
}
