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

namespace core;

/**
 * Tests for \core\authentication.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(authentication::class)]
final class authentication_test extends \advanced_testcase {
    public function test_plugin_exists_valid(): void {
        $auth = di::get(authentication::class);

        // Manual auth plugin always exists.
        $this->assertTrue($auth->plugin_exists('manual'));
        // Nologin auth plugin always exists.
        $this->assertTrue($auth->plugin_exists('nologin'));
    }

    public function test_plugin_exists_invalid(): void {
        $auth = di::get(authentication::class);

        $this->assertFalse($auth->plugin_exists('nonexistentplugin'));
        $this->assertFalse($auth->plugin_exists(''));
    }

    public function test_is_enabled_manual_always_enabled(): void {
        $auth = di::get(authentication::class);

        // Manual is always enabled (it's in the default list).
        $this->assertTrue($auth->is_enabled('manual'));
    }

    public function test_is_enabled_nologin_always_enabled(): void {
        $auth = di::get(authentication::class);

        // Nologin is always enabled (it's in the default list).
        $this->assertTrue($auth->is_enabled('nologin'));
    }

    public function test_is_enabled_empty_string(): void {
        $auth = di::get(authentication::class);

        $this->assertFalse($auth->is_enabled(''));
    }

    public function test_is_enabled_configured_plugin(): void {
        global $CFG;
        $this->resetAfterTest();

        $auth = di::get(authentication::class);

        // Email plugin exists but might not be enabled by default.
        $CFG->auth = 'email';
        $this->assertTrue($auth->is_enabled('email'));
    }

    public function test_is_enabled_not_configured(): void {
        global $CFG;
        $this->resetAfterTest();

        $auth = di::get(authentication::class);

        // Clear all auth config — only manual and nologin should be enabled.
        $CFG->auth = '';
        $this->assertFalse($auth->is_enabled('email'));
    }

    public function test_get_plugin_valid(): void {
        $auth = di::get(authentication::class);

        $plugin = $auth->get_plugin('manual');
        $this->assertInstanceOf(\auth_plugin_base::class, $plugin);
        $this->assertInstanceOf(\auth_plugin_manual::class, $plugin);
    }

    public function test_get_plugin_invalid_throws(): void {
        $auth = di::get(authentication::class);

        $this->expectException(\moodle_exception::class);
        $auth->get_plugin('nonexistentplugin');
    }

    public function test_get_enabled_plugins_defaults(): void {
        global $CFG;
        $this->resetAfterTest();

        $auth = di::get(authentication::class);

        // With empty config, manual and nologin are always returned.
        $CFG->auth = '';
        $enabled = $auth->get_enabled_plugins();

        $this->assertContains('manual', $enabled);
        $this->assertContains('nologin', $enabled);
    }

    public function test_get_enabled_plugins_includes_configured(): void {
        global $CFG;
        $this->resetAfterTest();

        $auth = di::get(authentication::class);

        $CFG->auth = 'email';
        $enabled = $auth->get_enabled_plugins();

        $this->assertContains('manual', $enabled);
        $this->assertContains('nologin', $enabled);
        $this->assertContains('email', $enabled);
    }

    public function test_get_enabled_plugins_removes_nonexistent(): void {
        global $CFG;
        $this->resetAfterTest();

        $auth = di::get(authentication::class);

        $CFG->auth = 'email,nonexistentplugin';
        $enabled = $auth->get_enabled_plugins();

        $this->assertContains('email', $enabled);
        $this->assertNotContains('nonexistentplugin', $enabled);
        $this->assertDebuggingCalled();
    }

    public function test_get_enabled_plugins_deduplicates(): void {
        global $CFG;
        $this->resetAfterTest();

        $auth = di::get(authentication::class);

        $CFG->auth = 'email,email';
        $enabled = $auth->get_enabled_plugins();

        // Should only appear once (plus the two defaults).
        $this->assertCount(3, $enabled);
    }

    public function test_get_enabled_plugins_fix_updates_config(): void {
        global $CFG;
        $this->resetAfterTest();

        $auth = di::get(authentication::class);

        // Add a non-existent plugin to the config.
        $CFG->auth = 'email,nonexistentplugin';
        $auth->get_enabled_plugins(fix: true);
        $this->assertDebuggingCalled();

        // Config should now only contain 'email'.
        $this->assertEquals('email', $CFG->auth);
    }

    public function test_is_internal_manual(): void {
        $auth = di::get(authentication::class);

        // Manual auth is internal.
        $this->assertTrue($auth->is_internal('manual'));
    }

    public function test_is_internal_nologin(): void {
        $auth = di::get(authentication::class);

        // Nologin prevents local passwords, but is still considered "internal".
        $this->assertTrue($auth->is_internal('nologin'));
    }

    public function test_is_restored_user_true(): void {
        global $DB;
        $this->resetAfterTest();

        $auth = di::get(authentication::class);

        $user = $this->getDataGenerator()->create_user(['username' => 'restoreduser']);
        $DB->set_field('user', 'password', 'restored', ['id' => $user->id]);

        $this->assertTrue($auth->is_restored_user('restoreduser'));
    }

    public function test_is_restored_user_false(): void {
        $this->resetAfterTest();

        $auth = di::get(authentication::class);

        $user = $this->getDataGenerator()->create_user(['username' => 'normaluser']);

        $this->assertFalse($auth->is_restored_user('normaluser'));
    }

    public function test_is_restored_user_nonexistent(): void {
        $auth = di::get(authentication::class);

        $this->assertFalse($auth->is_restored_user('nosuchuser'));
    }

    public function test_di_resolution(): void {
        // Verify the class can be resolved through DI.
        $instance = di::get(authentication::class);
        $this->assertInstanceOf(authentication::class, $instance);
    }
}
