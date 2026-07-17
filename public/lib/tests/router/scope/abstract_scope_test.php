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

use core\tests\fake_plugins_test_trait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for {@see abstract_scope}.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(abstract_scope::class)]
final class abstract_scope_test extends \advanced_testcase {
    use fake_plugins_test_trait;

    /**
     * Test get_identifier method with various class hierarchies.
     *
     * @param string $scopeclass The scope class to test.
     * @param string $expectedidentifier The expected identifier for the scope.
     * @param string|null $expectedexception The expected exception message, if any.
     */
    #[DataProvider('get_identifier_provider')]
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_get_identifier(string $scopeclass, string $expectedidentifier, ?string $expectedexception): void {
        $this->resetAfterTest();

        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'public/lib/tests/fixtures/fakeplugins/fake',
        );

        if ($expectedexception !== null) {
            $this->expectException(\coding_exception::class);
            $this->expectExceptionMessage($expectedexception);
        }

        $this->assertSame($expectedidentifier, $scopeclass::get_identifier());
    }

    /**
     * Data provider for test_get_identifier.
     *
     * @return array The test cases.
     */
    public static function get_identifier_provider(): array {
        return [
            'scope with identifier attribute' => [
                \fake_oauth2scope\route\scope\resource\read::class,
                'fake_oauth2scope:resource:read',
                null,
            ],
            'abstract scope with identifier attribute' => [
                \fake_oauth2scope\route\scope\resource\abstract_scope::class,
                'fake_oauth2scope:resource',
                null,
            ],
            'concrete scope with missing identifier attribute throws an exception' => [
                \fake_oauth2scope\route\scope\invalidresource\invalid_scope::class,
                '',
                'The class fake_oauth2scope\route\scope\invalidresource\invalid_scope must have an #[identifier_attribute] ' .
                    'attribute.',
            ],
            'abstract scope with missing identifier attribute throws an exception' => [
                \fake_oauth2scope\route\scope\invalidresource\invalid_abstract_scope::class,
                '',
                'The class fake_oauth2scope\route\scope\invalidresource\invalid_abstract_scope must have an ' .
                    '#[identifier_attribute] attribute.',
            ],
        ];
    }

    /**
     * Test get_summary method.
     *
     * @param string $scopeclass The scope class to test.
     * @param string $expectedsummary The expected summary for the scope.
     * @param string|null $expectedexception The expected exception message, if any.
     */
    #[DataProvider('get_summary_provider')]
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_get_summary(string $scopeclass, string $expectedsummary, ?string $expectedexception = null): void {
        $this->resetAfterTest();

        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'public/lib/tests/fixtures/fakeplugins/fake',
        );

        if ($expectedexception !== null) {
            $this->expectException(\coding_exception::class);
            $this->expectExceptionMessage($expectedexception);
        }

        $this->assertSame($expectedsummary, $scopeclass::get_summary());
    }

    /**
     * Data provider for test_get_summary.
     *
     * @return array The test cases.
     */
    public static function get_summary_provider(): array {
        return [
            'scope with summary attribute' => [
                \fake_oauth2scope\route\scope\resource\read::class,
                'Read scope',
            ],
            'abstract scope without summary is valid' => [
                \fake_oauth2scope\route\scope\resource\abstract_scope::class,
                '',
            ],
            'concrete scope without summary throws exception' => [
                \fake_oauth2scope\route\scope\invalidresource\invalid_scope::class,
                '',
                'The scope class fake_oauth2scope\route\scope\invalidresource\invalid_scope must have an ' .
                    '#[summary_attribute] attribute.',
            ],
        ];
    }

    /**
     * Test get_description method.
     *
     * @param string $scopeclass The scope class to test.
     * @param string $expecteddescription The expected description for the scope.
     */
    #[DataProvider('get_description_provider')]
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_get_description(string $scopeclass, string $expecteddescription): void {
        $this->resetAfterTest();

        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'public/lib/tests/fixtures/fakeplugins/fake',
        );

        $this->assertSame($expecteddescription, $scopeclass::get_description());
    }

    /**
     * Data provider for test_get_summary.
     *
     * @return array The test cases.
     */
    public static function get_description_provider(): array {
        return [
            'scope with description attribute' => [
                \fake_oauth2scope\route\scope\resource\read::class,
                'This is a test scope used for testing OAuth2 scopes in Moodle.',
            ],
            'abstract scope without description is valid' => [
                \fake_oauth2scope\route\scope\resource\abstract_scope::class,
                '',
            ],
            'concrete scope without description is valid' => [
                \fake_oauth2scope\route\scope\invalidresource\invalid_scope::class,
                '',
            ],
        ];
    }

    /**
     * Test instance-specific logic and interface implementations.
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function test_instance_methods_and_satisfaction(): void {
        $this->resetAfterTest();

        $this->add_full_mocked_plugintype(
            plugintype: 'fake',
            path: 'public/lib/tests/fixtures/fakeplugins/fake',
        );

        $scope = new \fake_oauth2scope\route\scope\resource\read();
        $expectedidentifier = 'fake_oauth2scope:resource:read';

        $this->assertTrue($scope->is_satisfied_by(['other:scope', 'fake_oauth2scope:resource:read']));
        $this->assertFalse($scope->is_satisfied_by(['other:scope']));
        $this->assertSame($expectedidentifier, $scope->getIdentifier());
        $this->assertSame($expectedidentifier, (string) $scope);
    }
}
