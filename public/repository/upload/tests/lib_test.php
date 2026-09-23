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

namespace repository_upload;

use file_storage;
use repository;

/**
 * Tests for the upload repository.
 *
 * @package    repository_upload
 * @copyright  2026 Muhammad Arnaldo <muhammad.arnaldo@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\repository_upload::class)]
final class lib_test extends \advanced_testcase {
    /**
     * Stage an uploaded file under the given name and return the upload repository to process it.
     *
     * @param string $filename the name the browser reported for the uploaded file
     * @return \repository_upload
     */
    protected function stage_upload(string $filename): \repository_upload {
        $repoid = $this->getDataGenerator()->create_repository('upload')->id;

        $tmpfile = tempnam(make_request_directory(), 'upl');
        file_put_contents($tmpfile, 'Content');
        $_FILES['repo_upload_file'] = [
            'name' => $filename,
            'type' => 'text/plain',
            'tmp_name' => $tmpfile,
            'error' => 0,
            'size' => filesize($tmpfile),
        ];

        return repository::get_repository_by_id($repoid, \context_system::instance());
    }

    /**
     * A name taken from the uploaded file rather than typed into the picker still has to fit files.filename.
     */
    public function test_process_upload_filename_too_long(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $basenamelength = file_storage::MAX_FILENAME_LENGTH - strlen('.txt') + 1;
        $repo = $this->stage_upload(str_repeat('a', $basenamelength) . '.txt');

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('filenametoolong', 'repository', file_storage::MAX_FILENAME_LENGTH));
        $repo->process_upload('', -1);
    }

    /**
     * A name taken from the uploaded file that fills the column exactly is accepted.
     */
    public function test_process_upload_filename_at_limit(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $basenamelength = file_storage::MAX_FILENAME_LENGTH - strlen('.txt');
        $filename = str_repeat('a', $basenamelength) . '.txt';
        $repo = $this->stage_upload($filename);

        $result = $repo->process_upload('', -1);
        $this->assertSame($filename, $result['file']);
    }
}
