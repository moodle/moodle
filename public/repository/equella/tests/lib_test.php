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
 * This file contains tests for the repository_equella class.
 *
 * @package     repository_equella
 *
 * @author  Guillaume BARAT <guillaumebarat@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace repository_equella;

use repository_equella;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->libdir . '/webdavlib.php');

/**
 * Class repository_equella_lib_testcase
 *
 * @group repository_equella
 * @copyright  Catalyst IT
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class lib_test extends \advanced_testcase {

    /** @var null|\repository_equella the repository_equella object, which the tests are run on. */
    private $repo = null;

    /**
     * Create some data for repository.
     *
     * @return stdClass
     */
    private function create_new_form_data(): stdClass {
        $record = new stdClass();
        $record->equella_url = 'http://dummy.url.com';
        $record->equella_userfield = 'default';
        $record->equella_select_restriction = 'none';
        $record->equella_options = '';
        $record->equella_shareid = 'id';
        $record->equella_sharedsecret = 'secret';
        $record->equella_manager_shareid = '';
        $record->equella_manager_sharedsecret = '';
        $record->equella_editingteacher_shareid = '';
        $record->equella_editingteacher_sharedsecret = '';

        return $record;
    }

    /**
     * Create repository for testing.
     *
     * @return repository_equella
     */
    private function create_repository(): repository_equella {
        $record = new \stdClass();
        $this->getDataGenerator()->create_repository_type('equella', $record);
        $generator = $this->getDataGenerator()->get_plugin_generator('repository_equella');
        $instance = $generator->create_instance();
        $this->repo = new repository_equella($instance->id);
        return $this->repo;
    }

    /**
     * Test that environment is created.
     * @covers \repository_equella::get_repository_by_id
     * @return void
     */
    public function test_repository_is_created(): void {
        $this->initialise_repository();
        $actual = repository_equella::get_repository_by_id($this->repo->id, $this->repo->context);
        $this->assertEquals($this->repo->options['equella_url'], $actual->get_option('equella_url'));
        $this->assertEquals($this->repo->options['equella_userfield'], $actual->get_option('equella_userfield'));
        $this->assertEquals($this->repo->options['equella_select_restriction'],
                $actual->get_option('equella_select_restriction'));
        $this->assertEquals($this->repo->options['equella_options'], $actual->get_option('equella_options'));
        $this->assertEquals($this->repo->options['equella_shareid'], $actual->get_option('equella_shareid'));
        $this->assertEquals($this->repo->options['equella_sharedsecret'], $actual->get_option('equella_sharedsecret'));
        $this->assertEquals($this->repo->options['equella_manager_shareid'], $actual->get_option('equella_manager_shareid'));
        $this->assertEquals($this->repo->options['equella_manager_sharedsecret'],
                $actual->get_option('equella_manager_sharedsecret'));
        $this->assertEquals($this->repo->options['equella_editingteacher_shareid'],
                $actual->get_option('equella_editingteacher_shareid'));
        $this->assertEquals($this->repo->options['equella_editingteacher_sharedsecret'],
                $actual->get_option('equella_editingteacher_sharedsecret'));
        $this->resetAfterTest(true);
    }

    /**
     * Data provider for get_userfield_value.
     *
     * @return array
     * @covers ::get_userfield_value
     */
    public static function get_userfield_value_provider(): array {
        return [
                [
                        'input' => [
                                'userfield' => 'nickname',
                                'value' => 'administrator',
                        ],
                        'expected' => [
                                'username' => 'administrator',
                        ],
                ], [
                        'input' => [
                                'userfield' => 'default',
                                'value' => 'default',
                        ],
                        'expected' => [
                                'username' => 'admin',
                        ],
                ], [
                        'input' => [
                                'userfield' => 'test',
                                'value' => 'test',
                        ],
                        'expected' => [
                                'username' => 'test',
                        ],
                ],
        ];
    }

    /**
     * Test method get_userfield_value.
     *
     * @dataProvider get_userfield_value_provider
     *
     * @param array $input
     * @param array $expected
     * @covers ::get_userfield_value
     *
     * @return void
     */
    public function test_get_userfield_value($input, $expected): void {
        global $USER;
        $this->initialise_repository();
        $USER->profile[$input['userfield']] = $input['value'];

        $this->repo->set_option(['equella_userfield' => $input['userfield']]);
        $return = $this->repo->get_userfield_value();
        $this->assertEquals($expected['username'], $return);
    }

    /**
     * Data provider for get_listing.
     *
     * @return array
     * @covers ::get_listing
     */
    public static function get_listing_provider(): array {
        return [
                [
                        'input' => [
                                'url' => 'http://dummy.url.com',
                                'userfield' => 'nickname',
                                'value' => 'administrator',
                        ],
                        'expected' => [
                                'username' => 'administrator',
                        ],
                ], [
                        'input' => [
                                'url' => 'http://dummy.url.com',
                                'userfield' => 'default',
                                'value' => '',
                        ],
                        'expected' => [
                                'username' => 'admin',
                        ],
                ], [
                        'input' => [
                                'url' => 'http://dummy.url.com',
                                'userfield' => 'test',
                                'value' => 'test',
                        ],
                        'expected' => [
                                'username' => 'test',
                        ],
                ],
        ];
    }

    /**
     * Test that the method get_listing return the correct array.
     *
     * @dataProvider get_listing_provider
     *
     * @param array $input
     * @param array $expected
     * @covers ::get_listing
     *
     * @return void
     */
    public function test_get_listing($input, $expected): void {
        global $USER;
        $this->initialise_repository();
        $USER->profile[$input['userfield']] = $input['value'];
        $this->repo->set_option(['url' => $input['url'],
                'equella_userfield' => $input['userfield']]);
        $listing = $this->repo->get_listing();
        $this->assertArrayHasKey('manage', $listing);
        $this->assertStringContainsString($expected['username'], $listing['manage']);
    }

    /**
     * Create and initialise the repository for test.
     * @return void
     */
    public function initialise_repository(): void {
        $this->resetAfterTest(true);
        // Admin is neccessary to create repository.
        $this->setAdminUser();
        $this->create_repository();
        $this->create_new_form_data();
    }
}
