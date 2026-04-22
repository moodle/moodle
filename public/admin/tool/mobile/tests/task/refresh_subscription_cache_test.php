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

namespace tool_mobile\task;

/**
 * Tests for the subscription cache refresh task.
 *
 * @covers \tool_mobile\task\refresh_subscription_cache
 * @package    tool_mobile
 * @copyright  2026 Daniel Ureña <daniel.urena@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class refresh_subscription_cache_test extends \advanced_testcase {
    /**
     * Test the task refreshes the cache and logs success.
     */
    public function test_execute_refreshes_cache_and_logs_success(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enablemobilewebservice = 1;

        $this->seed_subscription_cache('free');
        \curl::mock_response($this->wrap_ws_response($this->build_subscription_data('premium')));

        $task = new refresh_subscription_cache();
        $output = $this->capture_task_output($task);

        $this->assertStringContainsString('tool_mobile: Running scheduled subscription cache refresh task...', $output);
        $this->assertStringContainsString('tool_mobile: previous cache plan: free.', $output);
        $this->assertStringContainsString('tool_mobile: scheduled subscription cache refreshed. Plan: premium', $output);
        $this->assertStringContainsString(
            'tool_mobile: scheduled subscription cache refresh completed successfully.',
            $output,
        );

        $cache = \cache::make('tool_mobile', 'subscriptioninfo');
        $cacheddata = $cache->get(0);
        $this->assertSame('premium', $cacheddata['subscription']['plan']);
    }

    /**
     * Test the task logs a fallback when fresh data cannot be parsed but cached data exists.
     */
    public function test_execute_logs_fallback_when_cached_data_is_served(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enablemobilewebservice = 1;

        $this->seed_subscription_cache('bma');
        \curl::mock_response('invalid json');

        $task = new refresh_subscription_cache();
        $output = $this->capture_task_output($task);

        $this->assertDebuggingCalled(
            'Unexpected response from the Moodle Apps Portal server: invalid JSON received.',
        );

        $this->assertStringContainsString('tool_mobile: Running scheduled subscription cache refresh task...', $output);
        $this->assertStringContainsString('tool_mobile: previous cache plan: bma.', $output);
        $this->assertStringContainsString(
            'tool_mobile: Unexpected response from the Moodle Apps Portal server: invalid JSON received.',
            $output,
        );
        $this->assertStringContainsString(
            'tool_mobile: scheduled subscription cache refresh failed, serving previously cached data.',
            $output,
        );
    }

    /**
     * Test the task logs a hard failure when no cached data is available.
     */
    public function test_execute_logs_failure_when_no_cached_data_is_available(): void {
        global $CFG;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $CFG->enablemobilewebservice = 1;

        \curl::mock_response('invalid json');

        $task = new refresh_subscription_cache();
        $output = $this->capture_task_output($task);

        $this->assertDebuggingCalled(
            'Unexpected response from the Moodle Apps Portal server: invalid JSON received.',
        );

        $this->assertStringContainsString('tool_mobile: Running scheduled subscription cache refresh task...', $output);
        $this->assertStringContainsString('tool_mobile: previous cache plan: unknown.', $output);
        $this->assertStringContainsString('tool_mobile: subscription cache refresh failed.', $output);
        $this->assertStringContainsString(
            'tool_mobile: Unexpected response from the Moodle Apps Portal server: invalid JSON received.',
            $output,
        );
    }

    /**
     * Capture the output produced by a task execute call.
     *
     * @param \core\task\task_base $task
     * @return string
     */
    private function capture_task_output(\core\task\task_base $task): string {
        ob_start();
        $task->execute();

        return (string) ob_get_clean();
    }

    /**
     * Seed the subscription info cache with the given plan.
     *
     * @param string $plan
     */
    private function seed_subscription_cache(string $plan): void {
        $cache = \cache::make('tool_mobile', 'subscriptioninfo');
        $cache->set(0, $this->build_subscription_data($plan));
    }

    /**
     * Wrap data in the Apps Portal WS response shape.
     *
     * @param array $data
     * @return string
     */
    private function wrap_ws_response(array $data): string {
        return json_encode([[
            'error' => false,
            'data' => $data,
        ]]);
    }

    /**
     * Build minimal subscription data for testing.
     *
     * @param string $plan
     * @return array
     */
    private function build_subscription_data(string $plan): array {
        return [
            'subscription' => [
                'plan' => $plan,
                'name' => ucfirst($plan),
                'features' => [],
            ],
            'statistics' => [
                'notifications' => [
                    'monthly' => [],
                ],
            ],
        ];
    }
}
