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
 * Unit tests for file_system_filedir.
 *
 * @package   core_files
 * @category  phpunit
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filestorage/file_system.php');
require_once($CFG->libdir . '/filestorage/file_system_filedir.php');

/**
 * Unit tests for file_system_filedir.
 *
 * @package   core_files
 * @category  files
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_files_file_system_filedir_testcase extends advanced_testcase {

    /**
     * Shared test setUp.
     */
    public function setUp() {
        // Reset the file storage so that subsequent fetches to get_file_storage are called after
        // configuration is prepared.
        get_file_storage(true);
    }

    /**
     * Shared teset tearDown.
     */
    public function tearDown() {
        // Reset the file storage so that subsequent tests will use the standard file storage.
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
    protected function setup_vfile_root($filedir = [], $trashdir = [], $sourcedir = null) {
        global $CFG;
        $this->resetAfterTest();

        $content = [];
        if ($filedir !== null) {
            $content['filedir'] = $filedir;
        }

        if ($trashdir !== null) {
            $content['trashdir'] = $trashdir;
        }

        if ($sourcedir !== null) {
            $content['sourcedir'] = $sourcedir;
        }

        $vfileroot = \org\bovigo\vfs\vfsStream::setup('root', null, $content);

        $CFG->filedir = \org\bovigo\vfs\vfsStream::url('root/filedir');
        $CFG->trashdir = \org\bovigo\vfs\vfsStream::url('root/trashdir');

        return $vfileroot;
    }

    /**
     * Helper to create a stored file objectw with the given supplied content.
     *
     * @param   string  $filecontent The content of the mocked file
     * @param   string  $filename The file name to use in the stored_file
     * @param   array   $mockedmethods A list of methods you intend to override
     *                  If no methods are specified, only abstract functions are mocked.
     * @return stored_file
     */
    protected function get_stored_file($filecontent, $filename = null, $mockedmethods = null) {
        $contenthash = file_storage::hash_from_string($filecontent);
        if (empty($filename)) {
            $filename = $contenthash;
        }

        $file = $this->getMockBuilder(stored_file::class)
            ->setMethods($mockedmethods)
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
     * Get a testable mock of the file_system_filedir class.
     *
     * @param   array   $mockedmethods A list of methods you intend to override
     *                  If no methods are specified, only abstract functions are mocked.
     * @return file_system
     */
    protected function get_testable_mock($mockedmethods = []) {
        $fs = $this->getMockBuilder(file_system_filedir::class)
            ->setMethods($mockedmethods)
            ->getMock();

        return $fs;
    }

    /**
     * Ensure that an appropriate error is shown when the filedir directory
     * is not writable.
     */
    public function test_readonly_filesystem_filedir() {
        $this->resetAfterTest();

        // Setup the filedir but remove permissions.
        $vfileroot = $this->setup_vfile_root(null);

        // Make the target path readonly.
        $vfileroot->chmod(0444)
            ->chown(\org\bovigo\vfs\vfsStream::OWNER_USER_2);

        // This should generate an exception.
        $this->expectException('file_exception');
        $this->expectExceptionMessageRegExp(
            '/Can not create local file pool directories, please verify permissions in dataroot./');

        new file_system_filedir();
    }

    /**
     * Ensure that an appropriate error is shown when the trash directory
     * is not writable.
     */
    public function test_readonly_filesystem_trashdir() {
        $this->resetAfterTest();

        // Setup the trashdir but remove permissions.
        $vfileroot = $this->setup_vfile_root([], null);

        // Make the target path readonly.
        $vfileroot->chmod(0444)
            ->chown(\org\bovigo\vfs\vfsStream::OWNER_USER_2);

        // This should generate an exception.
        $this->expectException('file_exception');
        $this->expectExceptionMessageRegExp(
            '/Can not create local file pool directories, please verify permissions in dataroot./');

        new file_system_filedir();
    }

    /**
     * Test that the standard Moodle warning message is put into the filedir.
     */
    public function test_warnings_put_in_place() {
        $this->resetAfterTest();

        $vfileroot = $this->setup_vfile_root(null);

        new file_system_filedir();
        $this->assertTrue($vfileroot->hasChild('filedir/warning.txt'));
        $this->assertEquals(
            'This directory contains the content of uploaded files and is controlled by Moodle code. ' .
                'Do not manually move, change or rename any of the files and subdirectories here.',
            $vfileroot->getChild('filedir/warning.txt')->getContent()
        );
    }

    /**
     * Ensure that the default implementation of get_remote_path_from_hash
     * simply calls get_local_path_from_hash.
     */
    public function test_get_remote_path_from_hash() {
        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $expectedresult = (object) [];

        $fs = $this->get_testable_mock([
            'get_local_path_from_hash',
        ]);

        $fs->expects($this->once())
            ->method('get_local_path_from_hash')
            ->with($this->equalTo($contenthash), $this->equalTo(false))
            ->willReturn($expectedresult);

        $method = new ReflectionMethod(file_system_filedir::class, 'get_remote_path_from_hash');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, [$contenthash]);

        $this->assertEquals($expectedresult, $result);
    }

    /**
     * Test the stock implementation of get_local_path_from_storedfile_with_recovery with no file found and
     * a failed recovery.
     */
    public function test_get_local_path_from_storedfile_with_recovery() {
        $filecontent = 'example content';
        $file = $this->get_stored_file($filecontent);
        $fs = $this->get_testable_mock([
            'get_local_path_from_hash',
            'recover_file',
        ]);
        $filepath = '/path/to/nonexistent/file';

        $fs->method('get_local_path_from_hash')
            ->willReturn($filepath);

        $fs->expects($this->once())
            ->method('recover_file')
            ->with($this->equalTo($file));

        $file = $this->get_stored_file('example content');
        $method = new ReflectionMethod(file_system_filedir::class, 'get_local_path_from_storedfile');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($file, true));

        $this->assertEquals($filepath, $result);
    }

    /**
     * Test the stock implementation of get_local_path_from_storedfile_with_recovery with no file found and
     * a failed recovery.
     */
    public function test_get_local_path_from_storedfile_without_recovery() {
        $filecontent = 'example content';
        $file = $this->get_stored_file($filecontent);
        $fs = $this->get_testable_mock([
            'get_local_path_from_hash',
            'recover_file',
        ]);
        $filepath = '/path/to/nonexistent/file';

        $fs->method('get_local_path_from_hash')
            ->willReturn($filepath);

        $fs->expects($this->never())
            ->method('recover_file');

        $file = $this->get_stored_file('example content');
        $method = new ReflectionMethod(file_system_filedir::class, 'get_local_path_from_storedfile');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($file, false));

        $this->assertEquals($filepath, $result);
    }

    /**
     * Test that the correct path is generated for the supplied content
     * hashes.
     *
     * @dataProvider contenthash_dataprovider
     * @param   string  $hash contenthash to test
     * @param   string  $hashdir Expected format of content directory
     */
    public function test_get_fulldir_from_hash($hash, $hashdir) {
        global $CFG;

        $fs = new file_system_filedir();
        $method = new ReflectionMethod(file_system_filedir::class, 'get_fulldir_from_hash');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($hash));

        $expectedpath = sprintf('%s/filedir/%s', $CFG->dataroot, $hashdir);
        $this->assertEquals($expectedpath, $result);
    }

    /**
     * Test that the correct path is generated for the supplied content
     * hashes when used with a stored_file.
     *
     * @dataProvider contenthash_dataprovider
     * @param   string  $hash contenthash to test
     * @param   string  $hashdir Expected format of content directory
     */
    public function test_get_fulldir_from_storedfile($hash, $hashdir) {
        global $CFG;

        $file = $this->getMockBuilder('stored_file')
            ->disableOriginalConstructor()
            ->setMethods([
                'sync_external_file',
                'get_contenthash',
            ])
            ->getMock();

        $file->method('get_contenthash')->willReturn($hash);

        $fs = new file_system_filedir();
        $method = new ReflectionMethod('file_system_filedir', 'get_fulldir_from_storedfile');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($file));

        $expectedpath = sprintf('%s/filedir/%s', $CFG->dataroot, $hashdir);
        $this->assertEquals($expectedpath, $result);
    }

    /**
     * Test that the correct content directory is generated for the supplied
     * content hashes.
     *
     * @dataProvider contenthash_dataprovider
     * @param   string  $hash contenthash to test
     * @param   string  $hashdir Expected format of content directory
     */
    public function test_get_contentdir_from_hash($hash, $hashdir) {
        $method = new ReflectionMethod(file_system_filedir::class, 'get_contentdir_from_hash');
        $method->setAccessible(true);

        $fs = new file_system_filedir();
        $result = $method->invokeArgs($fs, array($hash));

        $this->assertEquals($hashdir, $result);
    }

    /**
     * Test that the correct content path is generated for the supplied
     * content hashes.
     *
     * @dataProvider contenthash_dataprovider
     * @param   string  $hash contenthash to test
     * @param   string  $hashdir Expected format of content directory
     */
    public function test_get_contentpath_from_hash($hash, $hashdir) {
        $method = new ReflectionMethod(file_system_filedir::class, 'get_contentpath_from_hash');
        $method->setAccessible(true);

        $fs = new file_system_filedir();
        $result = $method->invokeArgs($fs, array($hash));

        $expectedpath = sprintf('%s/%s', $hashdir, $hash);
        $this->assertEquals($expectedpath, $result);
    }

    /**
     * Test that the correct trash path is generated for the supplied
     * content hashes.
     *
     * @dataProvider contenthash_dataprovider
     * @param   string  $hash contenthash to test
     * @param   string  $hashdir Expected format of content directory
     */
    public function test_get_trash_fullpath_from_hash($hash, $hashdir) {
        global $CFG;

        $fs = new file_system_filedir();
        $method = new ReflectionMethod(file_system_filedir::class, 'get_trash_fullpath_from_hash');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($hash));

        $expectedpath = sprintf('%s/trashdir/%s/%s', $CFG->dataroot, $hashdir, $hash);
        $this->assertEquals($expectedpath, $result);
    }

    /**
     * Test that the correct trash directory is generated for the supplied
     * content hashes.
     *
     * @dataProvider contenthash_dataprovider
     * @param   string  $hash contenthash to test
     * @param   string  $hashdir Expected format of content directory
     */
    public function test_get_trash_fulldir_from_hash($hash, $hashdir) {
        global $CFG;

        $fs = new file_system_filedir();
        $method = new ReflectionMethod(file_system_filedir::class, 'get_trash_fulldir_from_hash');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($hash));

        $expectedpath = sprintf('%s/trashdir/%s', $CFG->dataroot, $hashdir);
        $this->assertEquals($expectedpath, $result);
    }

    /**
     * Ensure that copying a file to a target from a stored_file works as anticipated.
     */
    public function test_copy_content_from_storedfile() {
        $this->resetAfterTest();
        global $CFG;

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $filedircontent = [
            $contenthash => $filecontent,
        ];
        $vfileroot = $this->setup_vfile_root($filedircontent, [], []);

        $fs = $this->getMockBuilder(file_system_filedir::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'get_local_path_from_storedfile',
            ])
            ->getMock();

        $file = $this->getMockBuilder(stored_file::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sourcefile = \org\bovigo\vfs\vfsStream::url('root/filedir/' . $contenthash);
        $fs->method('get_local_path_from_storedfile')->willReturn($sourcefile);

        $targetfile = \org\bovigo\vfs\vfsStream::url('root/targetfile');
        $CFG->preventfilelocking = true;
        $result = $fs->copy_content_from_storedfile($file, $targetfile);

        $this->assertTrue($result);
        $this->assertEquals($filecontent, $vfileroot->getChild('targetfile')->getContent());
    }

    /**
     * Ensure that content recovery works.
     */
    public function test_recover_file() {
        $this->resetAfterTest();

        // Setup the filedir.
        // This contains a virtual file which has a cache mismatch.
        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);

        $trashdircontent = [
            '0f' => [
                'f3' => [
                    $contenthash => $filecontent,
                ],
            ],
        ];

        $vfileroot = $this->setup_vfile_root([], $trashdircontent);

        $file = new stored_file(get_file_storage(), (object) [
            'contenthash' => $contenthash,
            'filesize' => strlen($filecontent),
        ]);

        $fs = new file_system_filedir();
        $method = new ReflectionMethod(file_system_filedir::class, 'recover_file');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($file));

        // Test the output.
        $this->assertTrue($result);

        $this->assertEquals($filecontent, $vfileroot->getChild('filedir/0f/f3/' . $contenthash)->getContent());

    }

    /**
     * Ensure that content recovery works.
     */
    public function test_recover_file_already_present() {
        $this->resetAfterTest();

        // Setup the filedir.
        // This contains a virtual file which has a cache mismatch.
        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);

        $filedircontent = $trashdircontent = [
            '0f' => [
                'f3' => [
                    $contenthash => $filecontent,
                ],
            ],
        ];

        $vfileroot = $this->setup_vfile_root($filedircontent, $trashdircontent);

        $file = new stored_file(get_file_storage(), (object) [
            'contenthash' => $contenthash,
            'filesize' => strlen($filecontent),
        ]);

        $fs = new file_system_filedir();
        $method = new ReflectionMethod(file_system_filedir::class, 'recover_file');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($file));

        // Test the output.
        $this->assertTrue($result);

        $this->assertEquals($filecontent, $vfileroot->getChild('filedir/0f/f3/' . $contenthash)->getContent());
    }

    /**
     * Ensure that content recovery works.
     */
    public function test_recover_file_size_mismatch() {
        $this->resetAfterTest();

        // Setup the filedir.
        // This contains a virtual file which has a cache mismatch.
        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);

        $trashdircontent = [
            '0f' => [
                'f3' => [
                    $contenthash => $filecontent,
                ],
            ],
        ];
        $vfileroot = $this->setup_vfile_root([], $trashdircontent);

        $file = new stored_file(get_file_storage(), (object) [
            'contenthash' => $contenthash,
            'filesize' => strlen($filecontent) + 1,
        ]);

        $fs = new file_system_filedir();
        $method = new ReflectionMethod(file_system_filedir::class, 'recover_file');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($file));

        // Test the output.
        $this->assertFalse($result);
        $this->assertFalse($vfileroot->hasChild('filedir/0f/f3/' . $contenthash));
    }

    /**
     * Ensure that content recovery works.
     */
    public function test_recover_file_has_mismatch() {
        $this->resetAfterTest();

        // Setup the filedir.
        // This contains a virtual file which has a cache mismatch.
        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);

        $trashdircontent = [
            '0f' => [
                'f3' => [
                    $contenthash => $filecontent,
                ],
            ],
        ];
        $vfileroot = $this->setup_vfile_root([], $trashdircontent);

        $file = new stored_file(get_file_storage(), (object) [
            'contenthash' => $contenthash . " different",
            'filesize' => strlen($filecontent),
        ]);

        $fs = new file_system_filedir();
        $method = new ReflectionMethod(file_system_filedir::class, 'recover_file');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($file));

        // Test the output.
        $this->assertFalse($result);
        $this->assertFalse($vfileroot->hasChild('filedir/0f/f3/' . $contenthash));
    }

    /**
     * Ensure that content recovery works when the content file is in the
     * alt trash directory.
     */
    public function test_recover_file_alttrash() {
        $this->resetAfterTest();

        // Setup the filedir.
        // This contains a virtual file which has a cache mismatch.
        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);

        $trashdircontent = [
            $contenthash => $filecontent,
        ];
        $vfileroot = $this->setup_vfile_root([], $trashdircontent);

        $file = new stored_file(get_file_storage(), (object) [
            'contenthash' => $contenthash,
            'filesize' => strlen($filecontent),
        ]);

        $fs = new file_system_filedir();
        $method = new ReflectionMethod(file_system_filedir::class, 'recover_file');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($file));

        // Test the output.
        $this->assertTrue($result);

        $this->assertEquals($filecontent, $vfileroot->getChild('filedir/0f/f3/' . $contenthash)->getContent());
    }

    /**
     * Test that an appropriate error message is generated when adding a
     * file to the pool when the pool directory structure is not writable.
     */
    public function test_recover_file_contentdir_readonly() {
        $this->resetAfterTest();

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $filedircontent = [
            '0f' => [],
        ];
        $trashdircontent = [
            $contenthash => $filecontent,
        ];
        $vfileroot = $this->setup_vfile_root($filedircontent, $trashdircontent);

        // Make the target path readonly.
        $vfileroot->getChild('filedir/0f')
            ->chmod(0444)
            ->chown(\org\bovigo\vfs\vfsStream::OWNER_USER_2);

        $file = new stored_file(get_file_storage(), (object) [
            'contenthash' => $contenthash,
            'filesize' => strlen($filecontent),
        ]);

        $fs = new file_system_filedir();
        $method = new ReflectionMethod(file_system_filedir::class, 'recover_file');
        $method->setAccessible(true);
        $result = $method->invokeArgs($fs, array($file));

        // Test the output.
        $this->assertFalse($result);
    }

    /**
     * Test adding a file to the pool.
     */
    public function test_add_file_from_path() {
        $this->resetAfterTest();
        global $CFG;

        // Setup the filedir.
        // This contains a virtual file which has a cache mismatch.
        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $sourcedircontent = [
            'file' => $filecontent,
        ];

        $vfileroot = $this->setup_vfile_root([], [], $sourcedircontent);

        // Note, the vfs file system does not support locks - prevent file locking here.
        $CFG->preventfilelocking = true;

        // Attempt to add the file to the file pool.
        $fs = new file_system_filedir();
        $sourcefile = \org\bovigo\vfs\vfsStream::url('root/sourcedir/file');
        $result = $fs->add_file_from_path($sourcefile);

        // Test the output.
        $this->assertEquals($contenthash, $result[0]);
        $this->assertEquals(core_text::strlen($filecontent), $result[1]);
        $this->assertTrue($result[2]);

        $this->assertEquals($filecontent, $vfileroot->getChild('filedir/0f/f3/' . $contenthash)->getContent());
    }

    /**
     * Test that an appropriate error message is generated when adding an
     * unavailable file to the pool is attempted.
     */
    public function test_add_file_from_path_file_unavailable() {
        $this->resetAfterTest();

        // Setup the filedir.
        $vfileroot = $this->setup_vfile_root();

        $this->expectException('file_exception');
        $this->expectExceptionMessageRegExp(
            '/Cannot read file\. Either the file does not exist or there is a permission problem\./');

        $fs = new file_system_filedir();
        $fs->add_file_from_path(\org\bovigo\vfs\vfsStream::url('filedir/file'));
    }

    /**
     * Test that an appropriate error message is generated when specifying
     * the wrong contenthash when adding a file to the pool.
     */
    public function test_add_file_from_path_mismatched_hash() {
        $this->resetAfterTest();

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $sourcedir = [
            'file' => $filecontent,
        ];
        $vfileroot = $this->setup_vfile_root([], [], $sourcedir);

        $fs = new file_system_filedir();
        $filepath = \org\bovigo\vfs\vfsStream::url('root/sourcedir/file');
        $fs->add_file_from_path($filepath, 'eee4943847a35a4b6942c6f96daafde06bcfdfab');
        $this->assertDebuggingCalled("Invalid contenthash submitted for file $filepath");
    }

    /**
     * Test that an appropriate error message is generated when an existing
     * file in the pool has the wrong contenthash
     */
    public function test_add_file_from_path_existing_content_invalid() {
        $this->resetAfterTest();

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $filedircontent = [
            '0f' => [
                'f3' => [
                    // This contains a virtual file which has a cache mismatch.
                    '0ff30941ca5acd879fd809e8c937d9f9e6dd1615' => 'different example content',
                ],
            ],
        ];
        $sourcedir = [
            'file' => $filecontent,
        ];
        $vfileroot = $this->setup_vfile_root($filedircontent, [], $sourcedir);

        // Check that we hit the jackpot.
        $fs = new file_system_filedir();
        $filepath = \org\bovigo\vfs\vfsStream::url('root/sourcedir/file');
        $result = $fs->add_file_from_path($filepath);

        // We provided a bad hash. Check that the file was replaced.
        $this->assertDebuggingCalled("Replacing invalid content file $contenthash");

        // Test the output.
        $this->assertEquals($contenthash, $result[0]);
        $this->assertEquals(core_text::strlen($filecontent), $result[1]);
        $this->assertFalse($result[2]);

        // Fetch the new file structure.
        $structure = \org\bovigo\vfs\vfsStream::inspect(
            new \org\bovigo\vfs\visitor\vfsStreamStructureVisitor()
        )->getStructure();

        $this->assertEquals($filecontent, $structure['root']['filedir']['0f']['f3'][$contenthash]);
    }

    /**
     * Test that an appropriate error message is generated when adding a
     * file to the pool when the pool directory structure is not writable.
     */
    public function test_add_file_from_path_existing_cannot_write_hashpath() {
        $this->resetAfterTest();

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $filedircontent = [
            '0f' => [],
        ];
        $sourcedir = [
            'file' => $filecontent,
        ];
        $vfileroot = $this->setup_vfile_root($filedircontent, [], $sourcedir);

        // Make the target path readonly.
        $vfileroot->getChild('filedir/0f')
            ->chmod(0444)
            ->chown(\org\bovigo\vfs\vfsStream::OWNER_USER_2);

        $this->expectException('file_exception');
        $this->expectExceptionMessageRegExp(
            "/Can not create local file pool directories, please verify permissions in dataroot./");

        // Attempt to add the file to the file pool.
        $fs = new file_system_filedir();
        $sourcefile = \org\bovigo\vfs\vfsStream::url('root/sourcedir/file');
        $fs->add_file_from_path($sourcefile);
    }

    /**
     * Test adding a string to the pool.
     */
    public function test_add_file_from_string() {
        $this->resetAfterTest();
        global $CFG;

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $vfileroot = $this->setup_vfile_root();

        // Note, the vfs file system does not support locks - prevent file locking here.
        $CFG->preventfilelocking = true;

        // Attempt to add the file to the file pool.
        $fs = new file_system_filedir();
        $result = $fs->add_file_from_string($filecontent);

        // Test the output.
        $this->assertEquals($contenthash, $result[0]);
        $this->assertEquals(core_text::strlen($filecontent), $result[1]);
        $this->assertTrue($result[2]);
    }

    /**
     * Test that an appropriate error message is generated when adding a
     * string to the pool when the pool directory structure is not writable.
     */
    public function test_add_file_from_string_existing_cannot_write_hashpath() {
        $this->resetAfterTest();

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);

        $filedircontent = [
            '0f' => [],
        ];
        $vfileroot = $this->setup_vfile_root($filedircontent);

        // Make the target path readonly.
        $vfileroot->getChild('filedir/0f')
            ->chmod(0444)
            ->chown(\org\bovigo\vfs\vfsStream::OWNER_USER_2);

        $this->expectException('file_exception');
        $this->expectExceptionMessageRegExp(
            "/Can not create local file pool directories, please verify permissions in dataroot./");

        // Attempt to add the file to the file pool.
        $fs = new file_system_filedir();
        $fs->add_file_from_string($filecontent);
    }

    /**
     * Test adding a string to the pool when an item with the same
     * contenthash is already present.
     */
    public function test_add_file_from_string_existing_matches() {
        $this->resetAfterTest();
        global $CFG;

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $filedircontent = [
            '0f' => [
                'f3' => [
                    $contenthash => $filecontent,
                ],
            ],
        ];

        $vfileroot = $this->setup_vfile_root($filedircontent);

        // Note, the vfs file system does not support locks - prevent file locking here.
        $CFG->preventfilelocking = true;

        // Attempt to add the file to the file pool.
        $fs = new file_system_filedir();
        $result = $fs->add_file_from_string($filecontent);

        // Test the output.
        $this->assertEquals($contenthash, $result[0]);
        $this->assertEquals(core_text::strlen($filecontent), $result[1]);
        $this->assertFalse($result[2]);
    }

    /**
     * Test the cleanup of deleted files when there are no files to delete.
     */
    public function test_remove_file_missing() {
        $this->resetAfterTest();

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $vfileroot = $this->setup_vfile_root();

        $fs = new file_system_filedir();
        $fs->remove_file($contenthash);

        $this->assertFalse($vfileroot->hasChild('filedir/0f/f3/' . $contenthash));
        // No file to move to trash, so the trash path will also be empty.
        $this->assertFalse($vfileroot->hasChild('trashdir/0f'));
        $this->assertFalse($vfileroot->hasChild('trashdir/0f/f3'));
        $this->assertFalse($vfileroot->hasChild('trashdir/0f/f3/' . $contenthash));
    }

    /**
     * Test the cleanup of deleted files when a file already exists in the
     * trash for that path.
     */
    public function test_remove_file_existing_trash() {
        $this->resetAfterTest();

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);

        $filedircontent = $trashdircontent = [
            '0f' => [
                'f3' => [
                    $contenthash => $filecontent,
                ],
            ],
        ];
        $trashdircontent['0f']['f3'][$contenthash] .= 'different';
        $vfileroot = $this->setup_vfile_root($filedircontent, $trashdircontent);

        $fs = new file_system_filedir();
        $fs->remove_file($contenthash);

        $this->assertFalse($vfileroot->hasChild('filedir/0f/f3/' . $contenthash));
        $this->assertTrue($vfileroot->hasChild('trashdir/0f/f3/' . $contenthash));
        $this->assertNotEquals($filecontent, $vfileroot->getChild('trashdir/0f/f3/' . $contenthash)->getContent());
    }

    /**
     * Ensure that remove_file does nothing with an empty file.
     */
    public function test_remove_file_empty() {
        $this->resetAfterTest();
        global $DB;

        $DB = $this->getMockBuilder(\moodle_database::class)
            ->setMethods(['record_exists'])
            ->getMockForAbstractClass();

        $DB->expects($this->never())
            ->method('record_exists');

        $fs = new file_system_filedir();

        $result = $fs->remove_file(file_storage::hash_from_string(''));
        $this->assertNull($result);
    }

    /**
     * Ensure that remove_file does nothing when a file is still
     * in use.
     */
    public function test_remove_file_in_use() {
        $this->resetAfterTest();
        global $DB;

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $filedircontent = [
            '0f' => [
                'f3' => [
                    $contenthash => $filecontent,
                ],
            ],
        ];
        $vfileroot = $this->setup_vfile_root($filedircontent);

        $DB = $this->getMockBuilder(\moodle_database::class)
            ->setMethods(['record_exists'])
            ->getMockForAbstractClass();

        $DB->method('record_exists')->willReturn(true);

        $fs = new file_system_filedir();
        $result = $fs->remove_file($contenthash);
        $this->assertTrue($vfileroot->hasChild('filedir/0f/f3/' . $contenthash));
        $this->assertFalse($vfileroot->hasChild('trashdir/0f/f3/' . $contenthash));
    }

    /**
     * Ensure that remove_file removes the file when it is no
     * longer in use.
     */
    public function test_remove_file_expired() {
        $this->resetAfterTest();
        global $DB;

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);
        $filedircontent = [
            '0f' => [
                'f3' => [
                    $contenthash => $filecontent,
                ],
            ],
        ];
        $vfileroot = $this->setup_vfile_root($filedircontent);

        $DB = $this->getMockBuilder(\moodle_database::class)
            ->setMethods(['record_exists'])
            ->getMockForAbstractClass();

        $DB->method('record_exists')->willReturn(false);

        $fs = new file_system_filedir();
        $result = $fs->remove_file($contenthash);
        $this->assertFalse($vfileroot->hasChild('filedir/0f/f3/' . $contenthash));
        $this->assertTrue($vfileroot->hasChild('trashdir/0f/f3/' . $contenthash));
    }

    /**
     * Test purging the cache.
     */
    public function test_empty_trash() {
        $this->resetAfterTest();

        $filecontent = 'example content';
        $contenthash = file_storage::hash_from_string($filecontent);

        $filedircontent = $trashdircontent = [
            '0f' => [
                'f3' => [
                    $contenthash => $filecontent,
                ],
            ],
        ];
        $vfileroot = $this->setup_vfile_root($filedircontent, $trashdircontent);

        $fs = new file_system_filedir();
        $method = new ReflectionMethod(file_system_filedir::class, 'empty_trash');
        $method->setAccessible(true);
        $result = $method->invoke($fs);

        $this->assertTrue($vfileroot->hasChild('filedir/0f/f3/' . $contenthash));
        $this->assertFalse($vfileroot->hasChild('trashdir'));
        $this->assertFalse($vfileroot->hasChild('trashdir/0f'));
        $this->assertFalse($vfileroot->hasChild('trashdir/0f/f3'));
        $this->assertFalse($vfileroot->hasChild('trashdir/0f/f3/' . $contenthash));
    }

    /**
     * Data Provider for contenthash to contendir conversion.
     *
     * @return  array
     */
    public function contenthash_dataprovider() {
        return array(
            array(
                'contenthash'   => 'eee4943847a35a4b6942c6f96daafde06bcfdfab',
                'contentdir'    => 'ee/e4',
            ),
            array(
                'contenthash'   => 'aef05a62ae81ca0005d2569447779af062b7cda0',
                'contentdir'    => 'ae/f0',
            ),
        );
    }
}
