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

use core\composer\status;
use core\composer\package_status;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Composer helper tests.
 *
 * @package    core
 * @copyright  2026 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(\core\composer::class)]
final class composer_test extends \advanced_testcase {
    /**
     * Setup test environment.
     */
    #[\PHPUnit\Framework\Attributes\Before]
    protected function setup_di(): void {
        $tempdir = make_request_directory();

        \core\di::set(
            \core\composer::class,
            new \core\tests\testable_composer(
                $tempdir . '/vendor',
                $tempdir . '/composer.lock'
            )
        );
    }

    /**
     * Test is_installed().
     *
     * @param bool $installed
     */
    #[DataProvider('is_installed_provider')]
    public function test_is_installed(bool $installed): void {
        $composer = \core\di::get(\core\composer::class);

        if ($installed) {
            $composer->create_composer_installed_files();
        }

        $this->assertSame($installed, $composer->is_installed());
    }

    /**
     * Data provider for test_is_installed().
     *
     * @return array[]
     */
    public static function is_installed_provider(): array {
        return [
            'Composer install has been run' => [true],
            'Composer install has not been run' => [false],
        ];
    }

    /**
     * Test get_status().
     *
     * @param bool $composerinstalled Whether Composer is installed.
     * @param array $requiredpackages Required packages.
     * @param array $installedpackages Installed packages.
     * @param status $expected Expected status.
     */
    #[DataProvider('get_status_provider')]
    public function test_get_status(
        bool $composerinstalled,
        array $requiredpackages,
        array $installedpackages,
        status $expected
    ): void {
        $composer = \core\di::get(\core\composer::class);

        if ($composerinstalled) {
            $composer->create_composer_installed_files();
        }

        $composer->set_composer_lock($requiredpackages);
        $composer->set_installed_versions($installedpackages);

        $this->assertEquals($expected, $composer->get_status());
    }

    /**
     * Data provider for test_get_status().
     *
     * @return array[]
     */
    public static function get_status_provider(): array {
        return [
            'Composer install has not been run' => [
                false,
                [
                    'package/test1' => 'v1.0.0',
                    'package/test2' => '2.0.0',
                ],
                [],
                new status(
                    false,
                    false,
                    [
                        'package/test1' => new package_status(
                            false,
                            false,
                            '1.0.0',
                            null
                        ),
                        'package/test2' => new package_status(
                            false,
                            false,
                            '2.0.0',
                            null
                        ),

                    ]
                ),
            ],
            'Packages not up-to-date (missing packages)' => [
                true,
                [
                    'package/current' => '2.0.0',
                    'package/missing1' => 'v1.0.0',
                    'package/missing2' => 'v1.5.0',
                ],
                [
                    'package/current' => '2.0.0',
                ],
                new status(
                    true,
                    false,
                    [
                        'package/current' => new package_status(
                            true,
                            true,
                            '2.0.0',
                            '2.0.0'
                        ),
                        'package/missing1' => new package_status(
                            false,
                            false,
                            '1.0.0',
                            null
                        ),
                        'package/missing2' => new package_status(
                            false,
                            false,
                            '1.5.0',
                            null
                        ),

                    ]
                ),
            ],
            'Packages not up-to-date (missing and outdated packages)' => [
                true,
                [
                    'package/missing1' => 'v1.0.0',
                    'package/missing2' => 'v1.5.0',
                    'package/outdated' => '2.0.0',
                ],
                [
                    'package/outdated' => '1.0.0',
                ],
                new status(
                    true,
                    false,
                    [
                        'package/missing1' => new package_status(
                            false,
                            false,
                            '1.0.0',
                            null
                        ),
                        'package/missing2' => new package_status(
                            false,
                            false,
                            '1.5.0',
                            null
                        ),
                        'package/outdated' => new package_status(
                            true,
                            false,
                            '2.0.0',
                            '1.0.0'
                        ),
                    ]
                ),
            ],
            'Packages not up-to-date (outdated packages)' => [
                true,
                [
                    'package/current1' => 'v1.0.0',
                    'package/current2' => 'v1.5.0',
                    'package/outdated' => '2.0.0',
                ],
                [
                    'package/current1' => '1.0.0',
                    'package/current2' => '1.5.0',
                    'package/outdated' => '1.0.0',
                ],
                new status(
                    true,
                    false,
                    [
                        'package/current1' => new package_status(
                            true,
                            true,
                            '1.0.0',
                            '1.0.0'
                        ),
                        'package/current2' => new package_status(
                            true,
                            true,
                            '1.5.0',
                            '1.5.0'
                        ),
                        'package/outdated' => new package_status(
                            true,
                            false,
                            '2.0.0',
                            '1.0.0'
                        ),
                    ]
                ),
            ],
            'Packages up-to-date' => [
                true,
                [
                    'package/current1' => 'v1.0.0',
                    'package/current2' => 'v1.5.0',
                    'package/current3' => '2.0.0',
                ],
                [
                    'package/current1' => '1.0.0',
                    'package/current2' => '1.5.0',
                    'package/current3' => '2.0.0',
                ],
                new status(
                    true,
                    true,
                    [
                        'package/current1' => new package_status(
                            true,
                            true,
                            '1.0.0',
                            '1.0.0'
                        ),
                        'package/current2' => new package_status(
                            true,
                            true,
                            '1.5.0',
                            '1.5.0'
                        ),
                        'package/current3' => new package_status(
                            true,
                            true,
                            '2.0.0',
                            '2.0.0'
                        ),
                    ]
                ),
            ],
        ];
    }

    /**
     * Test get_package_status().
     *
     * @param bool $composerinstalled Whether Composer is installed.
     * @param array $requiredpackages Required packages.
     * @param array $installedpackages Installed packages.
     * @param string $packagename Package name.
     * @param package_status|null $expected Expected package status.
     * @param string|null $expectedexception Expected exception message.
     */
    #[DataProvider('get_package_status_provider')]
    public function test_get_package_status(
        bool $composerinstalled,
        array $requiredpackages,
        array $installedpackages,
        string $packagename,
        ?package_status $expected,
        ?string $expectedexception = null
    ): void {
        $composer = \core\di::get(\core\composer::class);

        if ($composerinstalled) {
            $composer->create_composer_installed_files();
        }

        $composer->set_composer_lock($requiredpackages);

        if ($expectedexception) {
            $this->expectExceptionMessage($expectedexception);
        }

        $composer->set_installed_versions($installedpackages);

        $this->assertEquals($expected, $composer->get_package_status($packagename));
    }

    /**
     * Data provider for test_get_package_status().
     *
     * @return array[]
     */
    public static function get_package_status_provider(): array {
        return [
            'Composer install has not been run' => [
                false,
                [
                    'package/test1' => 'v1.0.0',
                    'package/test2' => '2.0.0',
                ],
                [],
                'package/test2',
                new package_status(
                    false,
                    false,
                    '2.0.0',
                    null
                ),
            ],
            'Missing (not installed) package' => [
                true,
                [
                    'package/current' => '2.0.0',
                    'package/missing' => 'v1.0.0',
                ],
                [
                    'package/current' => '2.0.0',
                ],
                'package/missing',
                new package_status(
                    false,
                    false,
                    '1.0.0',
                    null
                ),
            ],
            'Outdated package' => [
                true,
                [
                    'package/missing' => 'v1.0.0',
                    'package/outdated' => '2.0.0',
                ],
                [
                    'package/outdated' => '1.0.0',
                ],
                'package/outdated',
                new package_status(
                    true,
                    false,
                    '2.0.0',
                    '1.0.0'
                ),
            ],
            'Invalid package' => [
                true,
                [
                    'package/current1' => 'v1.0.0',
                    'package/current2' => 'v1.5.0',
                ],
                [
                    'package/current1' => '1.0.0',
                    'package/current2' => '1.5.0',
                ],
                'package/invalid',
                null,
                "Package 'package/invalid' not found in composer.lock",
            ],
            'Up-to-date package' => [
                true,
                [
                    'package/outdated' => 'v2.0.0',
                    'package/current' => 'v1.5.0',
                ],
                [
                    'package/outdated' => '1.0.0',
                    'package/current' => '1.5.0',
                ],
                'package/current',
                new package_status(
                    true,
                    true,
                    '1.5.0',
                    '1.5.0'
                ),
            ],
        ];
    }
}
