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

namespace local_launchpad;

/**
 * Unit tests for fail-closed --apply target protection (no DB).
 *
 * @package    local_launchpad
 * @category   phpunit
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_launchpad\apply_target
 */
final class apply_target_test extends \basic_testcase {

    public function test_loopback_with_phpunit_dbname_is_local(): void {
        $this->assertTrue(apply_target::is_verified_local(
            'http://localhost:8080',
            'moodle_phpunit',
            []
        ));
        $this->assertTrue(apply_target::is_verified_local(
            'http://127.0.0.1',
            'moodle_phpunit',
            []
        ));
    }

    public function test_loopback_with_docker_env_is_local(): void {
        $this->assertTrue(apply_target::is_verified_local(
            'http://localhost:8080',
            'moodle',
            ['MOODLE_DOCKER_PHPUNIT' => '1']
        ));
    }

    public function test_loopback_with_explicit_allow_env_is_local(): void {
        $this->assertTrue(apply_target::is_verified_local(
            'http://localhost',
            'moodle',
            ['MOODLE_LAUNCHPAD_ALLOW_APPLY' => '1']
        ));
    }

    public function test_loopback_without_positive_signal_is_rejected(): void {
        $this->assertFalse(apply_target::is_verified_local(
            'http://localhost:8080',
            'moodle',
            []
        ));
        $reason = apply_target::refusal_reason('http://localhost:8080', 'moodle', []);
        $this->assertStringContainsString('not positively verified', $reason);
    }

    public function test_production_like_hosts_are_denied(): void {
        $this->assertTrue(apply_target::is_denied_host('learn.toba-tech.ai'));
        $this->assertFalse(apply_target::is_verified_local(
            'https://learn.toba-tech.ai',
            'moodle_phpunit',
            ['MOODLE_LAUNCHPAD_ALLOW_APPLY' => '1', 'MOODLE_DOCKER_PHPUNIT' => '1']
        ));
        $reason = apply_target::refusal_reason(
            'https://learn.toba-tech.ai',
            'moodle_phpunit',
            ['MOODLE_LAUNCHPAD_ALLOW_APPLY' => '1']
        );
        $this->assertStringContainsString('denied', $reason);
    }

    public function test_non_loopback_host_rejected(): void {
        $this->assertFalse(apply_target::is_verified_local(
            'https://staging.example.com',
            'moodle_phpunit',
            []
        ));
    }

    public function test_invalid_wwwroot_rejected(): void {
        $this->assertFalse(apply_target::is_verified_local('', 'moodle_phpunit', []));
        $this->assertFalse(apply_target::is_verified_local('not-a-url', 'moodle_phpunit', []));
    }
}
