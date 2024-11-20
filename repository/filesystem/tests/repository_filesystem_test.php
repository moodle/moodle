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
 * Tests for the repository_filesystem plugin.
 *
 * @package    repository_filesystem
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \repository_filesystem
 */
class repository_filesystem_test extends \advanced_testcase {
    public function test_get_listing(): void {
        global $CFG;

        $this->resetAfterTest(true);

        $user = get_admin();
        $this->setUser($user);

        $this->getDataGenerator()->create_repository_type('filesystem');

        mkdir($CFG->dataroot . '/repository/test/0', recursive: true);
        file_put_contents($CFG->dataroot . '/repository/test/0/test.txt', 'test');

        $record = $this->getDataGenerator()->create_repository('filesystem', [
            'fs_path' => 'test',
        ]);

        /** @var repository_filesystem $repository */
        $repository = repository::get_repository_by_id($record->id, \core\context\user::instance($user->id));

        $listing = $repository->get_listing();
        $this->assertCount(1, $listing['list']);
        $this->assertEquals(['0'], array_column($listing['list'], 'title'));
    }
}
