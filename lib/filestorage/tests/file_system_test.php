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

use file_archive;
use file_packer;
use file_system;
use file_system_filedir;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filestorage/file_system.php');

/**
 * Unit tests for file_system.
 *
 * @package   core
 * @category  test
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \file_system
 */
class file_system_test extends \advanced_testcase {

    public function setUp(): void {
        get_file_storage(true);
    }

    public function tearDown(): void {
        get_file_storage(true);
    }

    /**
     * Helper function to help setup and configure the virtual file system stream.
     *
     * @param   array $filedir Directory structure and content of the filedir
     * @param   array $trashdir Directory structure and content of the sourcedir
     * @param   array $sourcedir Directory structure and content of a directory used for source files for tests
     * @return  \org\bovigo\vfs\vfsStream
     */
    protected function setup_vfile_root($content = []) {
        $vfileroot = \org\bovigo\vfs\vfsStream::setup('root', null, $content);

        return $vfileroot;
    }

    /**
     * Helper to create a stored file objectw with the given supplied content.
     *
     * @param   string  $filecontent The content of the mocked file
     * @param   string  $filename The file name to use in the stored_file
     * @param   array   $mockedmethods A list of methods you intend to override
     *                  If no methods are specified, only abstract functions are mocked.
     * @return \stored_file
     */
    protected function get_stored_file($filecontent, $filename = null, $mockedmethods = []) {
        $contenthash = \file_storage::hash_from_string($filecontent);
        if (empty($filename)) {
            $filename = $contenthash;
        }

        $file = $this->getMockBuilder(\stored_file::class)
            ->onlyMethods($mockedmethods)
            ->setConstructorArgs([
                get_file_storage(),
                (object) [
                    'contenthash' => $contenthash,
                    'filesize' => strlen($filecontent),
                    'filename' => $filename,
                ]
            ])
            ->getMock();

        return $file;
    }

    /**
     * Get a testable mock of the abstract file_system class.
     *
     * @param   array   $mockedmethods A list of methods you intend to override
     *                  If no methods are specified, only abstract functions are mocked.
     * @return file_system
     */
    protected function get_testable_mock($mockedmethods = []) {
        $fs = $this->getMockBuilder(file_system::class)
            ->onlyMethods($mockedmethods)
            ->getMockForAbstractClass();

        return $fs;
    }

    /**
     * Ensure that the file system is not clonable.
     *
     */
    public function test_not_cloneable() {
        $reflection = new \ReflectionClass('file_system');
        $this->assertFalse($reflection->isCloneable());
    }

    /**
     * Ensure that the filedir file_system extension is used by default.
     *
     */
    public function test_default_class() {
        $this->resetAfterTest();

        // Ensure that the alternative_file_system_class is null.
        global $CFG;
        $CFG->alternative_file_system_class = null;

        $storage = get_file_storage();
        $fs = $storage->get_file_system();
        $this->assertInstanceOf(file_system::class, $fs);
        $this->assertEquals(file_system_filedir::class, get_class($fs));
    }

    /**
     * Ensure that the specified file_system extension class is used.
     *
     */
    public function test_supplied_class() {
        global $CFG;
        $this->resetAfterTest();

        // Mock the file_system.
        // Mocks create a new child of the mocked class which is perfect for this test.
        $filesystem = $this->getMockBuilder('file_system')
            ->disableOriginalConstructor()
            ->getMock();
        $CFG->alternative_file_system_class = get_class($filesystem);

        $storage = get_file_storage();
        $fs = $storage->get_file_system();
        $this->assertInstanceOf(file_system::class, $fs);
        $this->assertEquals(get_class($filesystem), get_class($fs));
    }

    /**
     * Test that the readfile function outputs content to disk.
     *
     * @covers ::readfile
     */
    public function test_readfile_remote() {
        global $CFG;

        // Mock the filesystem.
        $filecontent = 'example content';
        $vfileroot = $this->setup_vfile_root(['sourcefile' => $filecontent]);
        $filepath = \org\bovigo\vfs\vfsStream::url('root/sourcefile');

        $file = $this->get_stored_file($filecontent);

        // Mock the file_system class.
        // We need to override the get_remote_path_from_storedfile function.
        $fs = $this->get_testable_mock([
            'get_remote_path_from_storedfile',
            'is_file_readable_locally_by_storedfile',
            'get_local_path_from_storedfile',
        ]);
        $fs->method('get_remote_path_from_storedfile')->willReturn($filepath);
        $fs->method('is_file_readable_locally_by_storedfile')->willReturn(false);
        $fs->expects($this->never())->method('get_local_path_from_storedfile');

        // Note: It is currently not possible to mock readfile_allow_large
        // because file_system is in the global namespace.
        // We must therefore check for expected output. This is not ideal.
        $this->expectOutputString($filecontent);
        $fs->readfile($file);
    }

    /**
     * Test that the readfile function outputs content to disk.
     *
     * @covers ::readfile
     */
    public function test_readfile_local() {
        global $CFG;

        // Mock the filesystem.
        $filecontent = 'example content';
        $vfileroot = $this->setup_vfile_root(['sourcefile' => $filecontent]);
        $filepath = \org\bovigo\vfs\vfsStream::url('root/sourcefile');

        $file = $this->get_stored_file($filecontent);

        // Mock the file_system class.
        // We need to override the get_remote_path_from_storedfile function.
        $fs = $this->get_testable_mock([
            'get_remote_path_from_storedfile',
            'is_file_readable_locally_by_storedfile',
            'get_local_path_from_storedfile',
        ]);
        $fs->method('is_file_readable_locally_by_storedfile')->willReturn(true);
        $fs->expects($this->never())->method('get_remote_path_from_storedfile');
        $fs->expects($this->once())->method('get_local_path_from_storedfile')->willReturn($filepath);

        // Note: It is currently not possible to mock readfile_allow_large
        // because file_system is in the global namespace.
        // We must therefore check for expected output. This is not ideal.
        $this->expectOutputString($filecontent);
        $fs->readfile($file);
    }

    /**
     * Test that the get_local_path_from_storedfile function functions
     * correctly when called with various args.
     *
     * @dataProvider get_local_path_from_storedfile_provider
     * @param   array   $args The additional args to pass to get_local_path_from_storedfile
     * @param   bool    $fetch Whether the combination of args should have caused a fetch
     *
     * @covers ::get_local_path_from_storedfile
     */
    public function test_get_local_path_from_storedfile($args, $fetch) {
        $filepath = '/path/to/file';
        $filecontent = 'example content';

        // Get the filesystem mock.
        $fs = $this->get_testable_mock([
            'get_local_path_from_hash',
        ]);
        $fs->expects($this->once())
            ->method('get_local_path_from_hash')
            ->with($this->equalTo(\file_storage::hash_from_string($filecontent)), $this->equalTo($fetch))
            ->willReturn($filepath);

        $file = $this->get_stored_file($filecontent);

        $result = $fs->get_local_path_from_storedfile($file, $fetch);

        $this->assertEquals($filepath, $result);
    }

    /**
     * Ensure that the default implementation of get_remote_path_from_storedfile
     * simply calls get_local_path_from_storedfile without requiring a
     * fetch.
     *
     * @covers ::get_remote_path_from_storedfile
     */
    public function test_get_remote_path_from_storedfile() {
        $filepath = '/path/to/file';
        $filecontent = 'example content';

        $fs = $this->get_testable_mock([
            'get_remote_path_from_hash',
        ]);

        $fs->expects($this->once())
            ->method('get_remote_path_from_hash')
            ->with($this->equalTo(\file_storage::hash_from_string($filecontent)), $this->equalTo(false))
            ->willReturn($filepath);

        $file = $this->get_stored_file($filecontent);

        $result = $fs->get_remote_path_from_storedfile($file);

        $this->assertEquals($filepath, $result);
    }

    /**
     * Test the stock implementation of is_file_readable_locally_by_hash with a valid file.
     *
     * This should call get_local_path_from_hash and check the readability
     * of the file.
     *
     * Fetching the file is optional.
     *
     * @covers ::is_file_readable_locally_by_hash
     */
    public function test_is_file_readable_locally_by_hash() {
        $filecontent = 'example content';
        $contenthash = \file_storage::hash_from_string($filecontent);
        $filepath = __FILE__;

        $fs = $this->get_testable_mock([
            'get_local_path_from_hash',
        ]);

        $fs->method('get_local_path_from_hash')
            ->with($this->equalTo($contenthash), $this->equalTo(false))
            ->willReturn($filepath);

        $this->assertTrue($fs->is_file_readable_locally_by_hash($contenthash));
    }

    /**
     * Test the stock implementation of is_file_readable_locally_by_hash with an empty file.
     *
     * @covers ::is_file_readable_locally_by_hash
     */
    public function test_is_file_readable_locally_by_hash_empty() {
        $filecontent = '';
        $contenthash = \file_storage::hash_from_string($filecontent);

        $fs = $this->get_testable_mock([
            'get_local_path_from_hash',
        ]);

        $fs->expects($this->never())
            ->method('get_local_path_from_hash');

        $this->assertTrue($fs->is_file_readable_locally_by_hash($contenthash));
    }

    /**
     * Test the stock implementation of is_file_readable_remotely_by_storedfile with a valid file.
     *
     * @covers ::is_file_readable_remotely_by_hash
     */
    public function test_is_file_readable_remotely_by_hash() {
        $filecontent = 'example content';
        $contenthash = \file_storage::hash_from_string($filecontent);

        $fs = $this->get_testable_mock([
            'get_remote_path_from_hash',
        ]);

        $fs->method('get_remote_path_from_hash')
            ->with($this->equalTo($contenthash), $this->equalTo(false))
            ->willReturn(__FILE__);

        $this->assertTrue($fs->is_file_readable_remotely_by_hash($contenthash));
    }

    /**
     * Test the stock implementation of is_file_readable_remotely_by_storedfile with a valid file.
     *
     * @covers ::is_file_readable_remotely_by_hash
     */
    public function test_is_file_readable_remotely_by_hash_empty() {
        $filecontent = '';
        $contenthash = \file_storage::hash_from_string($filecontent);

        $fs = $this->get_testable_mock([
            'get_remote_path_from_hash',
        ]);

        $fs->expects($this->never())
            ->method('get_remote_path_from_hash');

        $this->assertTrue($fs->is_file_readable_remotely_by_hash($contenthash));
    }

    /**
     * Test the stock implementation of is_file_readable_remotely_by_storedfile with a valid file.
     *
     * @covers ::is_file_readable_remotely_by_hash
     */
    public function test_is_file_readable_remotely_by_hash_not_found() {
        $filecontent = 'example content';
        $contenthash = \file_storage::hash_from_string($filecontent);

        $fs = $this->get_testable_mock([
            'get_remote_path_from_hash',
        ]);

        $fs->method('get_remote_path_from_hash')
            ->with($this->equalTo($contenthash), $this->equalTo(false))
            ->willReturn('/path/to/nonexistent/file');

        $this->assertFalse($fs->is_file_readable_remotely_by_hash($contenthash));
    }

    /**
     * Test the stock implementation of is_file_readable_remotely_by_storedfile with a valid file.
     *
     * @covers ::is_file_readable_remotely_by_storedfile
     */
    public function test_is_file_readable_remotely_by_storedfile() {
        $file = $this->get_stored_file('example content');

        $fs = $this->get_testable_mock([
            'get_remote_path_from_storedfile',
        ]);

        $fs->method('get_remote_path_from_storedfile')
            ->willReturn(__FILE__);

        $this->assertTrue($fs->is_file_readable_remotely_by_storedfile($file));
    }

    /**
     * Test the stock implementation of is_file_readable_remotely_by_storedfile with a valid file.
     *
     * @covers ::is_file_readable_remotely_by_storedfile
     */
    public function test_is_file_readable_remotely_by_storedfile_empty() {
        $fs = $this->get_testable_mock([
            'get_remote_path_from_storedfile',
        ]);

        $fs->expects($this->never())
            ->method('get_remote_path_from_storedfile');

        $file = $this->get_stored_file('');
        $this->assertTrue($fs->is_file_readable_remotely_by_storedfile($file));
    }

    /**
     * Test the stock implementation of is_file_readable_locally_by_storedfile with an empty file.
     *
     * @covers ::is_file_readable_locally_by_storedfile
     */
    public function test_is_file_readable_locally_by_storedfile_empty() {
        $fs = $this->get_testable_mock([
            'get_local_path_from_storedfile',
        ]);

        $fs->expects($this->never())
            ->method('get_local_path_from_storedfile');

        $file = $this->get_stored_file('');
        $this->assertTrue($fs->is_file_readable_locally_by_storedfile($file));
    }

    /**
     * Test the stock implementation of is_file_readable_remotely_by_storedfile with a valid file.
     *
     * @covers ::is_file_readable_locally_by_storedfile
     */
    public function test_is_file_readable_remotely_by_storedfile_not_found() {
        $file = $this->get_stored_file('example content');

        $fs = $this->get_testable_mock([
            'get_remote_path_from_storedfile',
        ]);

        $fs->method('get_remote_path_from_storedfile')
            ->willReturn(__LINE__);

        $this->assertFalse($fs->is_file_readable_remotely_by_storedfile($file));
    }

    /**
     * Test the stock implementation of is_file_readable_locally_by_storedfile with a valid file.
     *
     * @covers ::is_file_readable_locally_by_storedfile
     */
    public function test_is_file_readable_locally_by_storedfile_unreadable() {
        $fs = $this->get_testable_mock([
            'get_local_path_from_storedfile',
        ]);
        $file = $this->get_stored_file('example content');

        $fs->method('get_local_path_from_storedfile')
            ->with($this->equalTo($file), $this->equalTo(false))
            ->willReturn('/path/to/nonexistent/file');

        $this->assertFalse($fs->is_file_readable_locally_by_storedfile($file));
    }

    /**
     * Test the stock implementation of is_file_readable_locally_by_storedfile with a valid file should pass fetch.
     *
     * @covers ::is_file_readable_locally_by_storedfile
     */
    public function test_is_file_readable_locally_by_storedfile_passes_fetch() {
        $fs = $this->get_testable_mock([
            'get_local_path_from_storedfile',
        ]);
        $file = $this->get_stored_file('example content');

        $fs->method('get_local_path_from_storedfile')
            ->with($this->equalTo($file), $this->equalTo(true))
            ->willReturn('/path/to/nonexistent/file');

        $this->assertFalse($fs->is_file_readable_locally_by_storedfile($file, true));
    }

    /**
     * Ensure that is_file_removable returns correctly for an empty file.
     *
     * @covers ::is_file_removable
     */
    public function test_is_file_removable_empty() {
        $filecontent = '';
        $contenthash = \file_storage::hash_from_string($filecontent);

        $method = new \ReflectionMethod(file_system::class, 'is_file_removable');
        $result = $method->invokeArgs(null, [$contenthash]);
        $this->assertFalse($result);
    }

    /**
     * Ensure that is_file_removable returns false if the file is still in use.
     *
     * @covers ::is_file_removable
     */
    public function test_is_file_removable_in_use() {
        $this->resetAfterTest();
        global $DB;

        $filecontent = 'example content';
        $contenthash = \file_storage::hash_from_string($filecontent);

        $DB = $this->getMockBuilder(\moodle_database::class)
            ->onlyMethods(['record_exists'])
            ->getMockForAbstractClass();
        $DB->method('record_exists')->willReturn(true);

        $method = new \ReflectionMethod(file_system::class, 'is_file_removable');
        $result = $method->invokeArgs(null, [$contenthash]);

        $this->assertFalse($result);
    }

    /**
     * Ensure that is_file_removable returns false if the file is not in use.
     *
     * @covers ::is_file_removable
     */
    public function test_is_file_removable_not_in_use() {
        $this->resetAfterTest();
        global $DB;

        $filecontent = 'example content';
        $contenthash = \file_storage::hash_from_string($filecontent);

        $DB = $this->getMockBuilder(\moodle_database::class)
            ->onlyMethods(['record_exists'])
            ->getMockForAbstractClass();
        $DB->method('record_exists')->willReturn(false);

        $method = new \ReflectionMethod(file_system::class, 'is_file_removable');
        $result = $method->invokeArgs(null, [$contenthash]);

        $this->assertTrue($result);
    }

    /**
     * Test the stock implementation of get_content.
     *
     * @covers ::get_content
     */
    public function test_get_content() {
        global $CFG;

        // Mock the filesystem.
        $filecontent = 'example content';
        $vfileroot = $this->setup_vfile_root(['sourcefile' => $filecontent]);
        $filepath = \org\bovigo\vfs\vfsStream::url('root/sourcefile');

        $file = $this->get_stored_file($filecontent);

        // Mock the file_system class.
        // We need to override the get_remote_path_from_storedfile function.
        $fs = $this->get_testable_mock(['get_remote_path_from_storedfile']);
        $fs->method('get_remote_path_from_storedfile')->willReturn($filepath);

        $result = $fs->get_content($file);

        $this->assertEquals($filecontent, $result);
    }

    /**
     * Test the stock implementation of get_content.
     *
     * @covers ::get_content
     */
    public function test_get_content_empty() {
        global $CFG;

        $filecontent = '';
        $file = $this->get_stored_file($filecontent);

        // Mock the file_system class.
        // We need to override the get_remote_path_from_storedfile function.
        $fs = $this->get_testable_mock(['get_remote_path_from_storedfile']);
        $fs->expects($this->never())
            ->method('get_remote_path_from_storedfile');

        $result = $fs->get_content($file);

        $this->assertEquals($filecontent, $result);
    }

    /**
     * Ensure that the list_files function requires a local copy of the
     * file, and passes the path to the packer.
     *
     * @covers ::list_files
     */
    public function test_list_files() {
        $filecontent = 'example content';
        $file = $this->get_stored_file($filecontent);
        $filepath = __FILE__;
        $expectedresult = (object) [];

        // Mock the file_system class.
        $fs = $this->get_testable_mock(['get_local_path_from_storedfile']);
        $fs->method('get_local_path_from_storedfile')
            ->with($this->equalTo($file), $this->equalTo(true))
            ->willReturn(__FILE__);

        $packer = $this->getMockBuilder(file_packer::class)
            ->onlyMethods(['list_files'])
            ->getMockForAbstractClass();

        $packer->expects($this->once())
            ->method('list_files')
            ->with($this->equalTo($filepath))
            ->willReturn($expectedresult);

        $result = $fs->list_files($file, $packer);

        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Ensure that the extract_to_pathname function requires a local copy of the
     * file, and passes the path to the packer.
     *
     * @covers ::extract_to_pathname
     */
    public function test_extract_to_pathname() {
        $filecontent = 'example content';
        $file = $this->get_stored_file($filecontent);
        $filepath = __FILE__;
        $expectedresult = (object) [];
        $outputpath = '/path/to/output';

        // Mock the file_system class.
        $fs = $this->get_testable_mock(['get_local_path_from_storedfile']);
        $fs->method('get_local_path_from_storedfile')
            ->with($this->equalTo($file), $this->equalTo(true))
            ->willReturn(__FILE__);

        $packer = $this->getMockBuilder(file_packer::class)
            ->onlyMethods(['extract_to_pathname'])
            ->getMockForAbstractClass();

        $packer->expects($this->once())
            ->method('extract_to_pathname')
            ->with($this->equalTo($filepath), $this->equalTo($outputpath), $this->equalTo(null), $this->equalTo(null))
            ->willReturn($expectedresult);

        $result = $fs->extract_to_pathname($file, $packer, $outputpath);

        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Ensure that the extract_to_storage function requires a local copy of the
     * file, and passes the path to the packer.
     *
     * @covers ::extract_to_storage
     */
    public function test_extract_to_storage() {
        $filecontent = 'example content';
        $file = $this->get_stored_file($filecontent);
        $filepath = __FILE__;
        $expectedresult = (object) [];
        $outputpath = '/path/to/output';

        // Mock the file_system class.
        $fs = $this->get_testable_mock(['get_local_path_from_storedfile']);
        $fs->method('get_local_path_from_storedfile')
            ->with($this->equalTo($file), $this->equalTo(true))
            ->willReturn(__FILE__);

        $packer = $this->getMockBuilder(file_packer::class)
            ->onlyMethods(['extract_to_storage'])
            ->getMockForAbstractClass();

        $packer->expects($this->once())
            ->method('extract_to_storage')
            ->with(
                $this->equalTo($filepath),
                $this->equalTo(42),
                $this->equalTo('component'),
                $this->equalTo('filearea'),
                $this->equalTo('itemid'),
                $this->equalTo('pathbase'),
                $this->equalTo('userid'),
                $this->equalTo(null)
            )
            ->willReturn($expectedresult);

        $result = $fs->extract_to_storage($file, $packer, 42, 'component','filearea', 'itemid', 'pathbase', 'userid');

        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Ensure that the add_storedfile_to_archive function requires a local copy of the
     * file, and passes the path to the archive.
     *
     */
    public function test_add_storedfile_to_archive_directory() {
        $file = $this->get_stored_file('', '.');
        $archivepath = 'example';
        $expectedresult = (object) [];

        // Mock the file_system class.
        $fs = $this->get_testable_mock(['get_local_path_from_storedfile']);
        $fs->method('get_local_path_from_storedfile')
            ->with($this->equalTo($file), $this->equalTo(true))
            ->willReturn(__FILE__);

        $archive = $this->getMockBuilder(file_archive::class)
            ->onlyMethods([
                'add_directory',
                'add_file_from_pathname',
            ])
            ->getMockForAbstractClass();

        $archive->expects($this->once())
            ->method('add_directory')
            ->with($this->equalTo($archivepath))
            ->willReturn($expectedresult);

        $archive->expects($this->never())
            ->method('add_file_from_pathname');

        $result = $fs->add_storedfile_to_archive($file, $archive, $archivepath);

        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Ensure that the add_storedfile_to_archive function requires a local copy of the
     * file, and passes the path to the archive.
     *
     */
    public function test_add_storedfile_to_archive_file() {
        $file = $this->get_stored_file('example content');
        $filepath = __LINE__;
        $archivepath = 'example';
        $expectedresult = (object) [];

        // Mock the file_system class.
        $fs = $this->get_testable_mock(['get_local_path_from_storedfile']);
        $fs->method('get_local_path_from_storedfile')
            ->with($this->equalTo($file), $this->equalTo(true))
            ->willReturn($filepath);

        $archive = $this->getMockBuilder(file_archive::class)
            ->onlyMethods([
                'add_directory',
                'add_file_from_pathname',
            ])
            ->getMockForAbstractClass();

        $archive->expects($this->never())
            ->method('add_directory');

        $archive->expects($this->once())
            ->method('add_file_from_pathname')
            ->with(
                $this->equalTo($archivepath),
                $this->equalTo($filepath)
            )
            ->willReturn($expectedresult);

        $result = $fs->add_storedfile_to_archive($file, $archive, $archivepath);

        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Ensure that the add_to_curl_request function requires a local copy of the
     * file, and passes the path to curl_file_create.
     *
     * @covers ::add_to_curl_request
     */
    public function test_add_to_curl_request() {
        $file = $this->get_stored_file('example content');
        $filepath = __FILE__;
        $archivepath = 'example';
        $key = 'myfile';

        // Mock the file_system class.
        $fs = $this->get_testable_mock(['get_local_path_from_storedfile']);
        $fs->method('get_local_path_from_storedfile')
            ->with($this->equalTo($file), $this->equalTo(true))
            ->willReturn($filepath);

        $request = (object) ['_tmp_file_post_params' => []];
        $fs->add_to_curl_request($file, $request, $key);
        $this->assertArrayHasKey($key, $request->_tmp_file_post_params);
        $this->assertEquals($filepath, $request->_tmp_file_post_params[$key]->name);
    }

    /**
     * Ensure that test_get_imageinfo_not_image returns false if the file
     * passed was deemed to not be an image.
     *
     * @covers ::get_imageinfo
     */
    public function test_get_imageinfo_not_image() {
        $filecontent = 'example content';
        $file = $this->get_stored_file($filecontent);

        $fs = $this->get_testable_mock([
            'is_image_from_storedfile',
        ]);

        $fs->expects($this->once())
            ->method('is_image_from_storedfile')
            ->with($this->equalTo($file))
            ->willReturn(false);

        $this->assertFalse($fs->get_imageinfo($file));
    }

    /**
     * Ensure that test_get_imageinfo_not_image returns imageinfo.
     *
     * @covers ::get_imageinfo
     */
    public function test_get_imageinfo() {
        $filepath = '/path/to/file';
        $filecontent = 'example content';
        $expectedresult = (object) [];
        $file = $this->get_stored_file($filecontent);

        $fs = $this->get_testable_mock([
            'is_image_from_storedfile',
            'get_local_path_from_storedfile',
            'get_imageinfo_from_path',
        ]);

        $fs->expects($this->once())
            ->method('is_image_from_storedfile')
            ->with($this->equalTo($file))
            ->willReturn(true);

        $fs->expects($this->once())
            ->method('get_local_path_from_storedfile')
            ->with($this->equalTo($file), $this->equalTo(true))
            ->willReturn($filepath);

        $fs->expects($this->once())
            ->method('get_imageinfo_from_path')
            ->with($this->equalTo($filepath))
            ->willReturn($expectedresult);

        $this->assertEquals($expectedresult, $fs->get_imageinfo($file));
    }

    /**
     * Ensure that is_image_from_storedfile always returns false for an
     * empty file size.
     *
     * @covers ::is_image_from_storedfile
     */
    public function test_is_image_empty_filesize() {
        $filecontent = 'example content';
        $file = $this->get_stored_file($filecontent, null, ['get_filesize']);

        $file->expects($this->once())
            ->method('get_filesize')
            ->willReturn(0);

        $fs = $this->get_testable_mock();
        $this->assertFalse($fs->is_image_from_storedfile($file));
    }

    /**
     * Ensure that is_image_from_storedfile behaves correctly based on
     * mimetype.
     *
     * @dataProvider is_image_from_storedfile_provider
     * @param   string  $mimetype Mimetype to test
     * @param   bool    $isimage Whether this mimetype should be detected as an image
     * @covers ::is_image_from_storedfile
     */
    public function test_is_image_from_storedfile_mimetype($mimetype, $isimage) {
        $filecontent = 'example content';
        $file = $this->get_stored_file($filecontent, null, ['get_mimetype']);

        $file->expects($this->once())
            ->method('get_mimetype')
            ->willReturn($mimetype);

        $fs = $this->get_testable_mock();
        $this->assertEquals($isimage, $fs->is_image_from_storedfile($file));
    }

    /**
     * Test that get_imageinfo_from_path returns an appropriate response
     * for an image.
     *
     * @covers ::get_imageinfo_from_path
     */
    public function test_get_imageinfo_from_path() {
        $filepath = __DIR__ . "/fixtures/testimage.jpg";

        // Get the filesystem mock.
        $fs = $this->get_testable_mock();

        $method = new \ReflectionMethod(file_system::class, 'get_imageinfo_from_path');
        $result = $method->invokeArgs($fs, [$filepath]);

        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('height', $result);
        $this->assertArrayHasKey('mimetype', $result);
        $this->assertEquals('image/jpeg', $result['mimetype']);
    }

    /**
     * Test that get_imageinfo_from_path returns an appropriate response
     * for a file which is not an image.
     *
     * @covers ::get_imageinfo_from_path
     */
    public function test_get_imageinfo_from_path_no_image() {
        $filepath = __FILE__;

        // Get the filesystem mock.
        $fs = $this->get_testable_mock();

        $method = new \ReflectionMethod(file_system::class, 'get_imageinfo_from_path');
        $result = $method->invokeArgs($fs, [$filepath]);

        $this->assertFalse($result);
    }

    /**
     * Test that get_imageinfo_from_path returns an appropriate response
     * for an svg image with viewbox attribute.
     */
    public function test_get_imageinfo_from_path_svg_viewbox() {
        $filepath = __DIR__ . '/fixtures/testimage_viewbox.svg';

        // Get the filesystem mock.
        $fs = $this->get_testable_mock();

        $method = new \ReflectionMethod(file_system::class, 'get_imageinfo_from_path');
        $result = $method->invokeArgs($fs, [$filepath]);

        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('height', $result);
        $this->assertArrayHasKey('mimetype', $result);
        $this->assertEquals(100, $result['width']);
        $this->assertEquals(100, $result['height']);
        $this->assertStringContainsString('image/svg', $result['mimetype']);
    }

    /**
     * Test that get_imageinfo_from_path returns an appropriate response
     * for an svg image with width and height attributes.
     */
    public function test_get_imageinfo_from_path_svg_with_width_height() {
        $filepath = __DIR__ . '/fixtures/testimage_width_height.svg';

        // Get the filesystem mock.
        $fs = $this->get_testable_mock();

        $method = new \ReflectionMethod(file_system::class, 'get_imageinfo_from_path');
        $result = $method->invokeArgs($fs, [$filepath]);

        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('height', $result);
        $this->assertArrayHasKey('mimetype', $result);
        $this->assertEquals(100, $result['width']);
        $this->assertEquals(100, $result['height']);
        $this->assertStringContainsString('image/svg', $result['mimetype']);
    }

    /**
     * Test that get_imageinfo_from_path returns an appropriate response
     * for an svg image without attributes.
     */
    public function test_get_imageinfo_from_path_svg_without_attribute() {
        $filepath = __DIR__ . '/fixtures/testimage.svg';

        // Get the filesystem mock.
        $fs = $this->get_testable_mock();

        $method = new \ReflectionMethod(file_system::class, 'get_imageinfo_from_path');
        $result = $method->invokeArgs($fs, [$filepath]);

        $this->assertArrayHasKey('width', $result);
        $this->assertArrayHasKey('height', $result);
        $this->assertArrayHasKey('mimetype', $result);
        $this->assertEquals(800, $result['width']);
        $this->assertEquals(600, $result['height']);
        $this->assertStringContainsString('image/svg', $result['mimetype']);
    }

    /**
     * Test that get_imageinfo_from_path returns an appropriate response
     * for a file which is not an correct svg.
     */
    public function test_get_imageinfo_from_path_svg_invalid() {
        $filepath = __DIR__ . '/fixtures/testimage_error.svg';

        // Get the filesystem mock.
        $fs = $this->get_testable_mock();

        $method = new \ReflectionMethod(file_system::class, 'get_imageinfo_from_path');
        $result = $method->invokeArgs($fs, [$filepath]);

        $this->assertFalse($result);
    }

    /**
     * Ensure that get_content_file_handle returns a valid file handle.
     *
     * @covers ::get_content_file_handle
     */
    public function test_get_content_file_handle_default() {
        $filecontent = 'example content';
        $file = $this->get_stored_file($filecontent);

        $fs = $this->get_testable_mock(['get_remote_path_from_storedfile']);
        $fs->method('get_remote_path_from_storedfile')
            ->willReturn(__FILE__);

        // Note: We are unable to determine the mode in which the $fh was opened.
        $fh = $fs->get_content_file_handle($file);
        $this->assertTrue(is_resource($fh));
        $this->assertEquals('stream', get_resource_type($fh));
        fclose($fh);
    }

    /**
     * Ensure that get_content_file_handle returns a valid file handle for a gz file.
     *
     * @covers ::get_content_file_handle
     */
    public function test_get_content_file_handle_gz() {
        $filecontent = 'example content';
        $file = $this->get_stored_file($filecontent);

        $fs = $this->get_testable_mock(['get_local_path_from_storedfile']);
        $fs->method('get_local_path_from_storedfile')
            ->willReturn(__DIR__ . "/fixtures/test.tgz");

        // Note: We are unable to determine the mode in which the $fh was opened.
        $fh = $fs->get_content_file_handle($file, \stored_file::FILE_HANDLE_GZOPEN);
        $this->assertTrue(is_resource($fh));
        gzclose($fh);
    }

    /**
     * Ensure that get_content_file_handle returns an exception when calling for a invalid file handle type.
     *
     * @covers ::get_content_file_handle
     */
    public function test_get_content_file_handle_invalid() {
        $filecontent = 'example content';
        $file = $this->get_stored_file($filecontent);

        $fs = $this->get_testable_mock(['get_remote_path_from_storedfile']);
        $fs->method('get_remote_path_from_storedfile')
            ->willReturn(__FILE__);

        $this->expectException('coding_exception', 'Unexpected file handle type');
        $fs->get_content_file_handle($file, -1);
    }

    /**
     * Ensure that get_content_file_handle returns a valid file handle.
     *
     * @covers ::get_psr_stream
     */
    public function test_get_psr_stream(): void {
        $file = $this->get_stored_file('');

        $fs = $this->get_testable_mock(['get_remote_path_from_storedfile']);
        $fs->method('get_remote_path_from_storedfile')
            ->willReturn(__FILE__);

        $stream = $fs->get_psr_stream($file);
        $this->assertInstanceOf(\Psr\Http\Message\StreamInterface::class, $stream);
        $this->assertEquals(file_get_contents(__FILE__), $stream->getContents());
        $this->assertFalse($stream->isWritable());
        $stream->close();
    }

    /**
     * Test that mimetype_from_hash returns the correct mimetype with
     * a file whose filename suggests mimetype.
     *
     * @covers ::mimetype_from_hash
     */
    public function test_mimetype_from_hash_using_filename() {
        $filepath = '/path/to/file/not/currently/on/disk';
        $filecontent = 'example content';
        $filename = 'test.jpg';
        $contenthash = \file_storage::hash_from_string($filecontent);

        $fs = $this->get_testable_mock(['get_remote_path_from_hash']);
        $fs->method('get_remote_path_from_hash')->willReturn($filepath);

        $result = $fs->mimetype_from_hash($contenthash, $filename);
        $this->assertEquals('image/jpeg', $result);
    }

    /**
     * Test that mimetype_from_hash returns the correct mimetype with
     * a locally available file whose filename does not suggest mimetype.
     *
     * @covers ::mimetype_from_hash
     */
    public function test_mimetype_from_hash_using_file_content() {
        $filecontent = 'example content';
        $contenthash = \file_storage::hash_from_string($filecontent);
        $filename = 'example';

        $filepath = __DIR__ . "/fixtures/testimage.jpg";
        $fs = $this->get_testable_mock(['get_local_path_from_hash']);
        $fs->method('get_local_path_from_hash')->willReturn($filepath);

        $result = $fs->mimetype_from_hash($contenthash, $filename);
        $this->assertEquals('image/jpeg', $result);
    }

    /**
     * Test that mimetype_from_hash returns the correct mimetype with
     * a remotely available file whose filename does not suggest mimetype.
     *
     * @covers ::mimetype_from_hash
     */
    public function test_mimetype_from_hash_using_file_content_remote() {
        $filepath = '/path/to/file/not/currently/on/disk';
        $filecontent = 'example content';
        $contenthash = \file_storage::hash_from_string($filecontent);
        $filename = 'example';

        $filepath = __DIR__ . "/fixtures/testimage.jpg";

        $fs = $this->get_testable_mock([
            'get_remote_path_from_hash',
            'is_file_readable_locally_by_hash',
            'get_local_path_from_hash',
        ]);

        $fs->method('get_remote_path_from_hash')->willReturn('/path/to/remote/file');
        $fs->method('is_file_readable_locally_by_hash')->willReturn(false);
        $fs->method('get_local_path_from_hash')->willReturn($filepath);

        $result = $fs->mimetype_from_hash($contenthash, $filename);
        $this->assertEquals('image/jpeg', $result);
    }

    /**
     * Test that mimetype_from_storedfile returns the correct mimetype with
     * a file whose filename suggests mimetype.
     *
     * @covers ::mimetype_from_storedfile
     */
    public function test_mimetype_from_storedfile_empty() {
        $file = $this->get_stored_file('');

        $fs = $this->get_testable_mock();
        $result = $fs->mimetype_from_storedfile($file);
        $this->assertNull($result);
    }

    /**
     * Test that mimetype_from_storedfile returns the correct mimetype with
     * a file whose filename suggests mimetype.
     *
     * @covers ::mimetype_from_storedfile
     */
    public function test_mimetype_from_storedfile_using_filename() {
        $filepath = '/path/to/file/not/currently/on/disk';
        $fs = $this->get_testable_mock(['get_remote_path_from_storedfile']);
        $fs->method('get_remote_path_from_storedfile')->willReturn($filepath);

        $file = $this->get_stored_file('example content', 'test.jpg');

        $result = $fs->mimetype_from_storedfile($file);
        $this->assertEquals('image/jpeg', $result);
    }

    /**
     * Test that mimetype_from_storedfile returns the correct mimetype with
     * a locally available file whose filename does not suggest mimetype.
     *
     * @covers ::mimetype_from_storedfile
     */
    public function test_mimetype_from_storedfile_using_file_content() {
        $filepath = __DIR__ . "/fixtures/testimage.jpg";
        $fs = $this->get_testable_mock(['get_local_path_from_hash']);
        $fs->method('get_local_path_from_hash')->willReturn($filepath);

        $file = $this->get_stored_file('example content');

        $result = $fs->mimetype_from_storedfile($file);
        $this->assertEquals('image/jpeg', $result);
    }

    /**
     * Test that mimetype_from_storedfile returns the correct mimetype with
     * a remotely available file whose filename does not suggest mimetype.
     *
     * @covers ::mimetype_from_storedfile
     */
    public function test_mimetype_from_storedfile_using_file_content_remote() {
        $filepath = __DIR__ . "/fixtures/testimage.jpg";

        $fs = $this->get_testable_mock([
            'is_file_readable_locally_by_hash',
            'get_local_path_from_hash',
        ]);

        $fs->method('is_file_readable_locally_by_hash')->willReturn(false);
        $fs->method('get_local_path_from_hash')->will($this->onConsecutiveCalls('/path/to/remote/file', $filepath));

        $file = $this->get_stored_file('example content');

        $result = $fs->mimetype_from_storedfile($file);
        $this->assertEquals('image/jpeg', $result);
    }

    /**
     * Data Provider for is_image_from_storedfile tests.
     *
     * @return array
     */
    public function is_image_from_storedfile_provider() {
        return array(
            'Standard image'            => array('image/png', true),
            'Made up document/image'    => array('document/image', false),
        );
    }

    /**
     * Data provider for get_local_path_from_storedfile tests.
     *
     * @return array
     */
    public function get_local_path_from_storedfile_provider() {
        return [
            'default args (nofetch)' => [
                'args' => [],
                'fetch' => 0,
            ],
            'explicit: nofetch' => [
                'args' => [false],
                'fetch' => 0,
            ],
            'explicit: fetch' => [
                'args' => [true],
                'fetch' => 1,
            ],
        ];
    }
}
