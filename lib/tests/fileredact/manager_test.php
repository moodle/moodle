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

namespace core\fileredact;

use stored_file;

/**
 * Tests for fileredact manager class.
 *
 * @package   core
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \core\fileredact\manager
 */
final class manager_test extends \advanced_testcase {

    /** @var stored_file Stored file object. */
    private stored_file $storedfile;

    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        $file = new \stdClass;
        $file->contextid = \context_user::instance(get_admin()->id)->id;
        $file->component = 'user';
        $file->filearea  = 'private';
        $file->itemid    = 0;
        $file->filepath  = '/';
        $file->filename  = 'test.jpg';
        $file->source    = 'test';

        $fs = get_file_storage();
        $this->storedfile = $fs->create_file_from_string($file, 'file1 content');
    }

    /**
     * Tests the `get_services` method.
     *
     * This test initializes the `manager` and verifies that the `get_services` method.
     */
    public function test_get_services(): void {
        // Init the manager.
        $manager = new \core\fileredact\manager($this->storedfile);

        $rc = new \ReflectionClass(\core\fileredact\manager::class);
        $rcm = $rc->getMethod('get_services');
        $services = $rcm->invoke($manager);

        $this->assertGreaterThan(0, count($services));

    }

    /**
     * Tests the `execute` method and error handling.
     *
     * This test mocks the `manager` class to return a dummy service for `get_services`
     * and verifies that the `execute` method runs without errors.
     */
    public function test_execute(): void {
        $managermock = $this->getMockBuilder(\core\fileredact\manager::class)
            ->onlyMethods(['get_services'])
            ->setConstructorArgs([$this->storedfile])
            ->getMock();

        $managermock->expects($this->once())
            ->method('get_services')
            ->willReturn(['\\core\fileredact\\services\\dummy_service']);

        /** @var \core\fileredact\manager $managermock */
        $managermock->execute();
        $errors = $managermock->get_errors();

        // If execution is OK, then no errors.
        $this->assertEquals([], $errors);
    }
}
