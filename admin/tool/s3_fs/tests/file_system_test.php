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
 * Test for file_system class.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_s3_fs;
use org\bovigo\vfs\vfsStream;
use tool_s3_fs\config;
use tool_s3_fs\file_system;
use tool_s3_fs\s3_client;

/**
 * Test for file_system class.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_system_test extends \advanced_testcase {
    /**
     * Virtual directory.
     *
     * @var string
     */
    protected $dir;

    protected function setUp(): void {
        $this->dir = vfsStream::setup('root')->url();
    }

    /**
     * Make a fake stored file.
     *
     * @param string $filecontent
     * @return stored_file
     */
    private function get_stored_file($filecontent = 'content') {
        $contenthash = \file_storage::hash_from_string($filecontent);

        return new \stored_file(get_file_storage(), (object) [
            'contenthash' => $contenthash,
            'filesize'    => strlen($filecontent),
            'component' => ''
        ]);
    }

    /**
     * Indirect testing of local path from hash via is readable.
     */
    public function test_get_local_path_from_hash() {
        $client = $this->createMock(s3_client::class);
        $client->expects($this->once())->method('download')->willReturnCallback(function () {
            file_put_contents($this->dir.'/abc123', 'content');
        });

        $fs = new file_system(new config(), $client, $this->dir);

        $this->assertFalse($fs->is_file_readable_locally_by_hash('abc123'));
        $this->assertTrue($fs->is_file_readable_locally_by_hash('abc123', true));
        $this->assertTrue($fs->is_file_readable_locally_by_hash('abc123'), 'Tests cache hit');
    }

    /**
     * Indirect testing of remote path from hash via is readable.
     */
    public function test_get_remote_path_from_hash() {
        $contenthash = 'abc123';
        $remotepath  = $this->dir.'/remote-'.$contenthash;

        $client = $this->createMock(s3_client::class);
        $client->expects($this->exactly(2))->method('get_stream_path')->willReturn($remotepath);

        $fs = new file_system(new config(), $client, $this->dir);
        $this->assertFalse($fs->is_file_readable_remotely_by_hash($contenthash), 'Remote file should not exist yet');

        file_put_contents($remotepath, 'content');
        $this->assertTrue($fs->is_file_readable_remotely_by_hash($contenthash), 'Remote file should exist');

        file_put_contents($this->dir.'/'.$contenthash, 'content');
        $this->assertTrue($fs->is_file_readable_remotely_by_hash($contenthash), 'Cached file should exist');
    }

    /**
     * Test copying content from a stored file into target file.
     */
    public function test_copy_content_from_storedfile() {
        $target = $this->dir.'/target.txt';

        $client = $this->createMock(s3_client::class);
        $client->expects($this->once())->method('download');

        $file = $this->get_stored_file();

        $fs     = new file_system(new config(), $client, $this->dir);
        $result = $fs->copy_content_from_storedfile($file, $target);

        $this->assertTrue($result);
        $this->assertFileDoesNotExist($target, 'File should not exist yet due to S3 mock');

        $file = $this->get_stored_file('');

        $fs     = new file_system(new config(), $client, $this->dir);
        $result = $fs->copy_content_from_storedfile($file, $target);

        $this->assertTrue($result);

    }

    /**
     * Test copying content from a stored file into target file but uses cache.
     */
    public function test_copy_content_from_storedfile_from_cache() {
        $target = $this->dir.'/target.txt';
        $file   = $this->get_stored_file();

        file_put_contents($this->dir.'/'.$file->get_contenthash(), 'content');

        $client = $this->createMock(s3_client::class);
        $client->expects($this->never())->method('download');

        $fs     = new file_system(new config(), $client, $this->dir);
        $result = $fs->copy_content_from_storedfile($file, $target);

        $this->assertTrue($result);
        $this->assertFileExists($target, 'File copied from cache');
    }

    /**
     * Test removing a file.
     */
    public function test_remove_file() {
        $client = $this->createMock(s3_client::class);
        $client->expects($this->once())->method('delete');

        $contenthash = 'abc123';

        file_put_contents($this->dir.'/'.$contenthash, 'content');
        $this->assertFileExists($this->dir.'/'.$contenthash);

        $fs = new file_system(new config(), $client, $this->dir);
        $fs->remove_file($contenthash);

        $this->assertFileDoesNotExist($this->dir.'/'.$contenthash);
    }

    /**
     * Test removing a file that's still in use (EG: it not actually deleted).
     */
    public function test_remove_file_in_use() {
        global $DB;

        $this->resetAfterTest();

        $DB = $this->createMock(\moodle_database::class);
        $DB->method('record_exists')->willReturn(true);

        $client = $this->createMock(s3_client::class);
        $client->expects($this->never())->method('delete');

        $fs = new file_system(new config(), $client, $this->dir);
        $fs->remove_file('abc123');
    }

    /**
     * Test removing a file but deletion is turned off via config.
     */
    public function test_remove_file_no_delete() {
        $client = $this->createMock(s3_client::class);
        $client->expects($this->never())->method('delete');

        $config         = new config();
        $config->delete = false;

        $fs = new file_system($config, $client, $this->dir);
        $fs->remove_file('abc123');
    }

    /**
     * Test adding a file from a file path.
     */
    public function test_add_file_from_path() {
        $path    = $this->dir.'/example.txt';
        $content = 'content';

        file_put_contents($path, $content);

        $client = $this->createMock(s3_client::class);
        $client->expects($this->once())->method('upload');

        $fs     = new file_system(new config(), $client, $this->dir);
        $result = $fs->add_file_from_path($path);
        unset($result[3]); // Removing last element b/c it's the file to be deleted when running unit tests.
        $this->assertSame([\file_storage::hash_from_string($content), strlen($content), true], $result);
    }

    /**
     * Test adding a file from directory path but it already exists in the file system.
     */
    public function test_add_file_from_path_duplicate() {
        $path    = $this->dir.'/example.txt';
        $content = 'content';
        $size    = strlen($content);

        file_put_contents($path, $content);

        $client = $this->createMock(s3_client::class);
        $client->expects($this->once())->method('file_size')->willReturn($size);
        $client->expects($this->never())->method('upload');

        $fs     = new file_system(new config(), $client, $this->dir);
        $result = $fs->add_file_from_path($path);
        unset($result[3]); // Removing last element b/c it's the file to be deleted when running unit tests.
        $this->assertSame([\file_storage::hash_from_string($content), $size, false], $result);
    }

    /**
     * Test a file hash collision.
     */
    public function test_add_file_from_path_hash_collision() {
        $this->expectException(\file_pool_content_exception::class);

        $path        = $this->dir.'/example.txt';
        $content     = 'content';
        $contenthash = \file_storage::hash_from_string($content);

        file_put_contents($path, $content);
        file_put_contents($this->dir.'/jackpot-'.$contenthash, $content);

        $client = $this->createMock(s3_client::class);
        $client->expects($this->once())->method('file_size')->willReturn(2);

        $fs = new file_system(new config(), $client, $this->dir);
        $fs->add_file_from_path($path);
    }

    /**
     * Test adding a file from directory path but the file is empty.
     */
    public function test_add_file_from_path_empty() {
        $path    = $this->dir.'/example.txt';
        $content = '';
        $size    = strlen($content);

        file_put_contents($path, $content);

        $client = $this->createMock(s3_client::class);
        $client->expects($this->never())->method('file_size');

        $fs     = new file_system(new config(), $client, $this->dir);
        $result = $fs->add_file_from_path($path);
        unset($result[3]); // Removing last element b/c it's the file to be deleted when running unit tests.
        $this->assertSame([\file_storage::hash_from_string($content), $size, false], $result);
    }

    /**
     * Test adding a file from a string.
     */
    public function test_add_file_from_string() {
        $content = 'content';

        $client = $this->createMock(s3_client::class);
        $client->expects($this->once())->method('upload');

        $fs     = new file_system(new config(), $client, $this->dir);
        $result = $fs->add_file_from_string($content);
        unset($result[3]); // Removing last element b/c it's the file to be deleted when running unit tests.
        $this->assertSame([\file_storage::hash_from_string($content), strlen($content), true], $result);
    }

    /**
     * Test adding an empty string.
     */
    public function test_add_file_from_string_empty() {
        $client = $this->createMock(s3_client::class);
        $client->expects($this->never())->method('upload');

        $fs     = new file_system(new config(), $client, $this->dir);
        $result = $fs->add_file_from_string('');

        $this->assertSame([\file_storage::hash_from_string(''), 0, false], $result);
    }

    /**
     * Test fopen on a local file.
     */
    public function test_get_content_file_handle_fopen() {
        $this->resetAfterTest();
        $dir = make_request_directory();

        $file = $this->get_stored_file();
        file_put_contents($dir.'/'.$file->get_contenthash(), 'content');

        $client = $this->createPartialMock(s3_client::class, []);

        $fs = new file_system(new config(), $client, $dir);
        $fh = $fs->get_content_file_handle($file, \stored_file::FILE_HANDLE_FOPEN);

        $this->assertIsResource($fh);
        $this->assertSame('content', fread($fh, 1000));
        $this->assertTrue(fclose($fh));
    }

    /**
     * Test gzopen on a local file.
     */
    public function test_get_content_file_handle_gzopen() {
        $this->resetAfterTest();
        $dir = make_request_directory();

        $file = $this->get_stored_file();
        file_put_contents($dir.'/'.$file->get_contenthash(), gzencode('data data data data', 9));

        $client = $this->createMock(s3_client::class);
        $client->expects($this->never())->method('open_gz_stream');

        $fs = new file_system(new config(), $client, $dir);
        $fh = $fs->get_content_file_handle($file, \stored_file::FILE_HANDLE_GZOPEN);

        $this->assertIsResource($fh);
        $this->assertSame('data data data data', gzread($fh, 1000));
        $this->assertTrue(gzclose($fh));
    }

    /**
     * Test gzopen using \tool_s3_fs\s3_client::open_gz_stream.
     */
    public function test_get_content_file_handle_gzopen_with_stream() {
        $this->resetAfterTest();
        $dir = make_request_directory();

        $file       = $this->get_stored_file();
        $remotepath = $dir.'/remote-'.$file->get_contenthash();
        file_put_contents($remotepath, gzencode('data data data data', 9));

        $client = $this->createPartialMock(s3_client::class, ['get_stream_path']);
        $client->expects($this->once())->method('get_stream_path')->willReturn($remotepath);

        $fs = new file_system(new config(), $client, $dir);
        $fh = $fs->get_content_file_handle($file, \stored_file::FILE_HANDLE_GZOPEN);

        $this->assertIsResource($fh);
        $this->assertSame('data data data data', gzread($fh, 1000));
        $this->assertTrue(gzclose($fh));
    }

    public function test_is_file_readable_remotely_by_storedfile_emptyfile() {
        $storedfile = $this->get_stored_file('');
        $client     = $this->createMock(s3_client::class);
        $client->expects($this->never())->method('get_stream_path');

        $fs = new file_system(new config(), $client, $this->dir);
        $this->assertTrue($fs->is_file_readable_remotely_by_storedfile($storedfile));
    }

    public function test_is_file_readable_remotely_by_storedfile() {
        $storedfile = $this->get_stored_file();
        $client = $this->createPartialMock(s3_client::class, ['get_stream_path']);
        $client->expects($this->once())->method('get_stream_path')->with($storedfile->get_contenthash());

        $fs = new file_system(new config(), $client, $this->dir);
        $fs->is_file_readable_remotely_by_storedfile($storedfile);
    }

    public function test_is_file_readable_locally_by_storedfile_emptyfile() {
        $storedfile = $this->get_stored_file('');
        $client     = $this->createMock(s3_client::class);
        $client->expects($this->never())->method('download');

        $fs = new file_system(new config(), $client, $this->dir);
        $this->assertTrue($fs->is_file_readable_locally_by_storedfile($storedfile, true));
    }

    public function test_is_file_readable_locally_by_storedfile() {
        $storedfile = $this->get_stored_file();
        $path = $this->dir . '/' . $storedfile->get_contenthash();
        $client = $this->createPartialMock(s3_client::class, ['download']);
        $client->expects($this->once())->method('download')->with($storedfile->get_contenthash(), $path);

        $fs = new file_system(new config(), $client, $this->dir);
        $fs->is_file_readable_locally_by_storedfile($storedfile, true);
    }

    /**
     * Test delete a file from local cache after upload it.
     */
    public function test_delete_from_localcache() {
        $this->resetAfterTest();
        global $CFG;
        $path    = $this->dir.'/example.txt';
        $content = 'content';

        file_put_contents($path, $content);

        $CFG->s3_fs_deletecacheoff = true;
        $client = $this->createMock(s3_client::class);
        $client->expects($this->once())->method('upload');
        $fs     = new file_system(new config(), $client, $this->dir);
        $result = $fs->add_file_from_path($path);
        // Checkout we didn't delete on the original path.
        $this->assertFileExists($path);

        // Checkout is not deleted from localcache.
        $this->assertFileExists($result[3]);

        $CFG->s3_fs_deletecacheoff = false;
        $client = $this->createMock(s3_client::class);
        $client->expects($this->once())->method('upload');
        $fs     = new file_system(new config(), $client, $this->dir);
        $result = $fs->add_file_from_path($path);
        // Checkout we didn't delete on the original path.
        $this->assertFileExists($path);

        // Checkout is deleted from localcache.
        $this->assertFileDoesNotExist($result[3]);
    }
}
