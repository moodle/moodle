<?php
// This file is part of Moodle - https://moodle.org/
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

namespace mlbackend_python;

/**
 * Unit tests for the {@link \mlbackend_python\processor} class.
 *
 * @package   mlbackend_python
 * @category  test
 * @copyright 2019 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class processor_test extends \advanced_testcase {

    /**
     * Test implementation of the {@link \mlbackend_python\processor::check_pip_package_version()} method.
     *
     * @dataProvider check_pip_package_versions
     * @param string $actual A sample of the actual package version
     * @param string $required A sample of the required package version
     * @param int $result Expected value returned by the tested method
     */
    public function test_check_pip_package_version($actual, $required, $result) {
        $this->assertSame($result, \mlbackend_python\processor::check_pip_package_version($actual, $required));
    }

    /**
     * Check that the {@link \mlbackend_python\processor::check_pip_package_version()} can be called with single argument.
     */
    public function test_check_pip_package_version_default() {

        $this->assertSame(-1, \mlbackend_python\processor::check_pip_package_version('0.0.1'));
        $this->assertSame(0, \mlbackend_python\processor::check_pip_package_version(
            \mlbackend_python\processor::REQUIRED_PIP_PACKAGE_VERSION));
    }

    /**
     * Provides data samples for the {@link self::test_check_pip_package_version()}.
     *
     * @return array
     */
    public function check_pip_package_versions() {
        return [
            // Exact match.
            [
                '0.0.5',
                '0.0.5',
                0,
            ],
            [
                '1.0.0',
                '1.0.0',
                0,
            ],
            // Actual version higher than required, yet still API compatible.
            [
                '1.0.3',
                '1.0.1',
                0,
            ],
            [
                '2.1.3',
                '2.0.0',
                0,
            ],
            [
                '1.1.5',
                '1.1',
                0,
            ],
            [
                '2.0.3',
                '2',
                0,
            ],
            // Actual version not high enough to meet the requirements.
            [
                '0.0.5',
                '1.0.0',
                -1,
            ],
            [
                '0.37.0',
                '1.0.0',
                -1,
            ],
            [
                '0.0.5',
                '0.37.0',
                -1,
            ],
            [
                '2.0.0',
                '2.0.2',
                -1,
            ],
            [
                '2.7.0',
                '3.0',
                -1,
            ],
            [
                '2.8.9-beta1',
                '3.0',
                -1,
            ],
            [
                '1.1.0-rc1',
                '1.1.0',
                -1,
            ],
            // Actual version too high and no longer API compatible.
            [
                '2.0.0',
                '1.0.0',
                1,
            ],
            [
                '3.1.5',
                '2.0',
                1,
            ],
            [
                '3.0.0',
                '1.0',
                1,
            ],
            [
                '2.0.0',
                '0.0.5',
                1,
            ],
            [
                '3.0.2',
                '0.37.0',
                1,
            ],
            // Zero major version requirement is fulfilled with 1.x API (0.x are not considered stable APIs).
            [
                '1.0.0',
                '0.0.5',
                0,
            ],
            [
                '1.8.6',
                '0.37.0',
                0,
            ],
            // Empty version is never good enough.
            [
                '',
                '1.0.0',
                -1,
            ],
            [
                '0.0.0',
                '0.37.0',
                -1,
            ],
        ];
    }
}
