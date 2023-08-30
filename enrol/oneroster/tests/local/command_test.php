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
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/oneroster_testcase.php');
use enrol_oneroster\local\oneroster_testcase;

/**
 * One Roster tests for the `command` class.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\local\command
 */
class command_testcase extends oneroster_testcase {

    /**
     * Test the URL construction via the constructor.
     *
     * @dataProvider param_and_url_provider
     * @param   string $url
     * @param   array|null $params
     * @param   string $expectedurl
     * @param   array $finalparams
     */
    public function test_construct_url($url, $params, $expectedurl, array $finalparams): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->setMethods(['get_url_for_command'])
            ->getMock();

        $endpoint
            ->method('get_url_for_command')
            ->will($this->returnArgument(1));

        $command = new command(
            $endpoint,
            $url,
            'someMethod',
            'Description of some example test method',
            null,
            null,
            null,
            $params
        );

        $this->assertEquals($expectedurl, $command->get_url(''));

        $this->assertIsArray($command->get_params());
        $this->assertSame($finalparams, $command->get_params());
    }

    /**
     * Data provider for URL construction when valid parameters are provided.
     *
     * @return  array
     */
    public function param_and_url_provider(): array {
        return [
            'URL without params' => [
                '/someMethod',
                [],
                '/someMethod',
                [],
            ],
            'Normal params do not do anything to the URL' => [
                '/someMethod',
                [
                    'someValye' => 'dateLastModified',
                ],
                '/someMethod',
                [
                    'someValye' => 'dateLastModified',
                ],
            ],
            'URL param gets replaced' => [
                '/someMethod/:some_id',
                [
                    ':some_id' => 'someValue',
                    'limit' => '5',
                ],
                '/someMethod/someValue',
                [
                    'limit' => '5',
                ],
            ],
        ];
    }

    /**
     * Test the URL construction via the constructor when the params and URL or incorrect.
     *
     * @dataProvider invalid_param_and_url_provider
     * @param   string $url
     * @param   array|null $params
     */
    public function test_construct_url_invalid_params($url, $params): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->setMethods(['get_url_for_command'])
            ->getMock();

        $endpoint
            ->method('get_url_for_command')
            ->will($this->returnArgument(1));

        $this->expectException(\OutOfRangeException::class);
        $command = new command(
            $endpoint,
            $url,
            'someMethod',
            'Description of some example test method',
            null,
            null,
            null,
            $params
        );
    }

    /**
     * Data provider for URL construction when invalid parameters are provided.
     *
     * @return  array
     */
    public function invalid_param_and_url_provider(): array {
        return [
            'Value provided in params without a placeholder' => [
                '/someMethod',
                [
                    ':some_id' => 'someValue',
                ],
            ],
            'Placeholder without a param' => [
                '/someMethod/:some_id',
                [],
            ],
        ];
    }

    /**
     * Ensure that the get_collection_names function return the list of possible collections.
     *
     * @dataProvider get_collection_names_provider
     * @param   array|null $collectionnames
     */
    public function test_get_collections(?array $collectionnames): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new command(
            $endpoint,
            '/someMethod',
            'someMethod',
            'Description of some example test method',
            $collectionnames,
            null,
            null,
            []
        );

        $this->assertEquals($collectionnames, $command->get_collection_names());
    }

    /**
     * Data provider for the `get_collection_names` for function.
     *
     * @return  array
     */
    public function get_collection_names_provider(): array {
        return [
            [null],
            [['org']],
            [['org', 'school']],
            [['1', 2]],
        ];
    }

    /**
     * Ensure that the is_collection function returns correctly for a range of collection values.
     *
     * @dataProvider is_collection_provider
     * @param   array|null $collectionnames
     * @param   bool $iscollection
     */
    public function test_is_collection(?array $collectionnames, bool $iscollection): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new command(
            $endpoint,
            '/someMethod',
            'someMethod',
            'Description of some example test method',
            $collectionnames,
            null,
            null,
            []
        );

        $this->assertEquals($iscollection, $command->is_collection());
    }

    /**
     * Data provider for the `is_collection` tests.
     *
     * @return  array
     */
    public function is_collection_provider(): array {
        return [
            [null, false],
            [[], false],
            [[0], true],
            [['org'], true],
            [['org', 'school'], true],
            [['1', 2], true],
        ];
    }

    /**
     * Ensure that the require_collection function does not except.
     *
     * @dataProvider require_collection_valid_provider
     * @param   array|null $collectionnames
     */
    public function test_require_collection_valid(?array $collectionnames): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new command(
            $endpoint,
            '/someMethod',
            'someMethod',
            'Description of some example test method',
            $collectionnames,
            null,
            null,
            []
        );

        $this->assertNull($command->require_collection());
    }

    /**
     * Data provider for the `require_collection` for valid values.
     *
     * @return  array
     */
    public function require_collection_valid_provider(): array {
        return array_filter(
            $this->is_collection_provider(),
            function($values) {
                return $values[1];
            }
        );
    }

    /**
     * Ensure that the require_collection function does not except.
     *
     * @dataProvider require_collection_invalid_provider
     * @param   array|null $collectionnames
     */
    public function test_require_collection_invalid(?array $collectionnames): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new command(
            $endpoint,
            '/someMethod',
            'someMethod',
            'Description of some example test method',
            $collectionnames,
            null,
            null,
            []
        );

        $this->expectException(\BadMethodCallException::class);
        $command->require_collection();
    }

    /**
     * Data provider for the `require_collection` for invalid values.
     *
     * @return  array
     */
    public function require_collection_invalid_provider(): array {
        return array_filter(
            $this->is_collection_provider(),
            function($values) {
                return !$values[1];
            }
        );
    }

    /**
     * Tests for `get_method` function.
     */
    public function test_get_method(): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new command(
            $endpoint,
            '/testMethod',
            'someMethod',
            'Description of some example test method',
            null,
            null,
            null,
            []
        );

        $this->assertEquals('someMethod', $command->get_method());
    }

    /**
     * Tests for `get_description` function.
     */
    public function test_get_description(): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new command(
            $endpoint,
            '/testDescription',
            'someDescription',
            'Description of some example test description',
            null,
            null,
            null,
            []
        );

        $this->assertEquals('Description of some example test description', $command->get_description());
    }
}
