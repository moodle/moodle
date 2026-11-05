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

namespace core\router\scope;

/**
 * Tests for the scope util class.
 *
 * @package    core
 * @category   test
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(util::class)]
final class util_test extends \advanced_testcase {
    use \core\tests\fake_plugins_test_trait;

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_get_all_scopes(): void {
        $this->resetAfterTest();

        // Inject the 'fake' plugin type that contains the test 'oauth2scope' plugin that registers test scopes.
        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'public/lib/tests/fixtures/fakeplugins/fake',
        );

        $scopeutil = \core\di::make(util::class);
        $scopes = $scopeutil->get_all_scopes();

        $this->assertDebuggingCalled(
            "Skipping scope class 'fake_oauth2scope\\route\\scope\\invalidresource\\invalid_scope' due to error: " .
                "Coding error detected, it must be fixed by a programmer: The class " .
                "fake_oauth2scope\\route\\scope\\invalidresource\\invalid_scope must have an #[identifier_attribute] attribute."
        );

        $this->assertNotEmpty($scopes);
        // Verify that the 'test_scope' scope is present in the list.
        $this->assertArrayHasKey('fake_oauth2scope:resource:read', $scopes);
        // Verify that the 'invalid_scope' scope is not in the list.
        $this->assertArrayNotHasKey('fake_oauth2scope:invalid:invalid_scope', $scopes);
    }
}
