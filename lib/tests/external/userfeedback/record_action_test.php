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
 * External functions test for record_action.
 *
 * @package    core
 * @category   test
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\external\userfeedback;

defined('MOODLE_INTERNAL') || die();

use externallib_advanced_testcase;
use context_system;

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Class record_action_testcase
 *
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass record_action
 */
class record_action_testcase extends externallib_advanced_testcase {

    /**
     * Data provider for test_record_action.
     *
     * @return  array
     */
    public function record_action_provider() {
        return [
            'give action' => ['give'],
            'remind action' => ['remind'],
        ];
    }

    /**
     * Test the behaviour of record_action().
     *
     * @dataProvider record_action_provider
     * @param string $action The action taken by the user
     *
     * @covers ::execute
     */
    public function test_record_action(string $action) {
        $this->resetAfterTest();

        $context = context_system::instance();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $eventsink = $this->redirectEvents();

        $now = time();

        // Call the WS and check the action is recorded as expected.
        $result = record_action::execute($action, $context->id);
        $this->assertNull($result);

        $preference = get_user_preferences('core_userfeedback_' . $action);
        $this->assertGreaterThanOrEqual($now, $preference);

        $events = $eventsink->get_events();
        $this->assertCount(1, $events);
        $this->assertInstanceOf('\core\event\userfeedback_' . $action, $events[0]);
        $eventsink->clear();
    }
}
