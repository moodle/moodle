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
 * Unit tests for /lib/filestorage/file_storage.php
 *
 * @package   core
 * @category  test
 * @copyright 2012 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/repository/lib.php');

class filestoragelib_testcase extends advanced_testcase {

    /**
     * Local files can be added to the filepool
     */
    public function test_create_file_from_pathname() {
        global $CFG;

        $this->resetAfterTest(false);

        $filepath = $CFG->dirroot.'/lib/filestorage/tests/fixtures/testimage.jpg';
        $syscontext = context_system::instance();
        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'core',
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/images/',
            'filename'  => 'testimage.jpg',
        );

        $fs = get_file_storage();
        $fs->create_file_from_pathname($filerecord, $filepath);

        $this->assertTrue($fs->file_exists($syscontext->id, 'core', 'unittest', 0, '/images/', 'testimage.jpg'));

        return $fs->get_file($syscontext->id, 'core', 'unittest', 0, '/images/', 'testimage.jpg');
    }

    /**
     * Local images can be added to the filepool and their preview can be obtained
     *
     * @depends test_create_file_from_pathname
     */
    public function test_get_file_preview(stored_file $file) {
        global $CFG;

        $this->resetAfterTest(true);
        $fs = get_file_storage();

        $previewtinyicon = $fs->get_file_preview($file, 'tinyicon');
        $this->assertInstanceOf('stored_file', $previewtinyicon);
        $this->assertEquals('6b9864ae1536a8eeef54e097319175a8be12f07c', $previewtinyicon->get_filename());

        $previewtinyicon = $fs->get_file_preview($file, 'thumb');
        $this->assertInstanceOf('stored_file', $previewtinyicon);
        $this->assertEquals('6b9864ae1536a8eeef54e097319175a8be12f07c', $previewtinyicon->get_filename());
    }

    /**
     * Make sure renaming is working
     *
     * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
     */
    public function test_file_renaming() {
        global $CFG;

        $this->resetAfterTest(true);
        $fs = get_file_storage();
        $syscontext = context_system::instance();
        $component = 'core';
        $filearea  = 'unittest';
        $itemid    = 0;
        $filepath  = '/';
        $filename  = 'test.txt';

        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $itemid,
            'filepath'  => $filepath,
            'filename'  => $filename,
        );

        $originalfile = $fs->create_file_from_string($filerecord, 'Test content');
        $this->assertInstanceOf('stored_file', $originalfile);
        $contenthash = $originalfile->get_contenthash();
        $newpath = '/test/';
        $newname = 'newtest.txt';

        // this should work
        $originalfile->rename($newpath, $newname);
        $file = $fs->get_file($syscontext->id, $component, $filearea, $itemid, $newpath, $newname);
        $this->assertInstanceOf('stored_file', $file);
        $this->assertEquals($contenthash, $file->get_contenthash());

        // try break it
        $this->setExpectedException('file_exception');
        // this shall throw exception
        $originalfile->rename($newpath, $newname);
    }

    /**
     * Create file from reference tests
     *
     * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
     */
    public function test_create_file_from_reference() {
        global $CFG, $DB;

        $this->resetAfterTest(true);
        // create user
        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $usercontext = context_user::instance($user->id);
        $syscontext = context_system::instance();
        $USER = $DB->get_record('user', array('id'=>$user->id));

        $fs = get_file_storage();

        $repositorypluginname = 'user';
        // override repository permission
        $capability = 'repository/' . $repositorypluginname . ':view';
        $allroles = $DB->get_records_menu('role', array(), 'id', 'archetype, id');
        assign_capability($capability, CAP_ALLOW, $allroles['guest'], $syscontext->id, true);


        $args = array();
        $args['type'] = $repositorypluginname;
        $repos = repository::get_instances($args);
        $userrepository = reset($repos);
        $this->assertInstanceOf('repository', $userrepository);

        $component = 'user';
        $filearea  = 'private';
        $itemid    = 0;
        $filepath  = '/';
        $filename  = 'userfile.txt';

        $filerecord = array(
            'contextid' => $usercontext->id,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $itemid,
            'filepath'  => $filepath,
            'filename'  => $filename,
        );

        $content = 'Test content';
        $originalfile = $fs->create_file_from_string($filerecord, $content);
        $this->assertInstanceOf('stored_file', $originalfile);

        $newfilerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'core',
            'filearea'  => 'phpunit',
            'itemid'    => 0,
            'filepath'  => $filepath,
            'filename'  => $filename,
        );
        $ref = $fs->pack_reference($filerecord);
        $newstoredfile = $fs->create_file_from_reference($newfilerecord, $userrepository->id, $ref);
        $this->assertInstanceOf('stored_file', $newstoredfile);
        $this->assertEquals($userrepository->id, $newstoredfile->get_repository_id());
        $this->assertEquals($originalfile->get_contenthash(), $newstoredfile->get_contenthash());
        $this->assertEquals($originalfile->get_filesize(), $newstoredfile->get_filesize());
        $this->assertRegExp('#' . $filename. '$#', $newstoredfile->get_reference_details());

        // Test looking for references
        $count = $fs->get_references_count_by_storedfile($originalfile);
        $this->assertEquals(1, $count);
        $files = $fs->get_references_by_storedfile($originalfile);
        $file = reset($files);
        $this->assertEquals($file, $newstoredfile);

        // Look for references by repository ID
        $files = $fs->get_external_files($userrepository->id);
        $file = reset($files);
        $this->assertEquals($file, $newstoredfile);

        // Try convert reference to local file
        $importedfile = $fs->import_external_file($newstoredfile);
        $this->assertFalse($importedfile->is_external_file());
        $this->assertInstanceOf('stored_file', $importedfile);
        // still readable?
        $this->assertEquals($content, $importedfile->get_content());
    }

    /**
     * TODO: the tests following this line were added to demonstrate specific Oracle problems in
     * MDL-33172. They need to be improved to properly evalulate the results of the tests. This is
     * tracked in MDL-33326.
     */
    private function setup_three_private_files() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $usercontext = context_user::instance($user->id);
        $USER = $DB->get_record('user', array('id'=>$user->id));
        // create a user private file
        $file1 = new stdClass;
        $file1->contextid = $usercontext->id;
        $file1->component = 'user';
        $file1->filearea  = 'private';
        $file1->itemid    = 0;
        $file1->filepath  = '/';
        $file1->filename  = '1.txt';
        $file1->source    = 'test';

        $fs = get_file_storage();
        $userfile1 = $fs->create_file_from_string($file1, 'file1 content');
        $file2 = clone($file1);
        $file2->filename = '2.txt';
        $userfile2 = $fs->create_file_from_string($file2, 'file2 content');

        $file3 = clone($file1);
        $file3->filename = '3.txt';
        $userfile3 = $fs->create_file_from_storedfile($file3, $userfile2);

        $user->ctxid = $usercontext->id;

        return $user;
    }


    public function test_get_area_files() {
        $user = $this->setup_three_private_files();
        $fs = get_file_storage();

        // Get area files with default options.
        $areafiles = $fs->get_area_files($user->ctxid, 'user', 'private');
        // Should be the two files we added plus the folder.
        $this->assertEquals(4, count($areafiles));

        // Get area files without a folder.
        $areafiles = $fs->get_area_files($user->ctxid, 'user', 'private', false, 'sortorder', false);
        // Should be the two files without folder.
        $this->assertEquals(3, count($areafiles));

        // Get area files ordered by id (was breaking on oracle).
        $areafiles = $fs->get_area_files($user->ctxid, 'user', 'private', false, 'id', false);
        // Should be the two files without folder.
        $this->assertEquals(3, count($areafiles));

        // Test with an itemid with no files
        $areafiles = $fs->get_area_files($user->ctxid, 'user', 'private', 666, 'sortorder', false);
        // Should none
        $this->assertEquals(0, count($areafiles));
    }

    public function test_get_area_tree() {
        $user = $this->setup_three_private_files();
        $fs = get_file_storage();

        // Get area files with default options.
        $areafiles = $fs->get_area_tree($user->ctxid, 'user', 'private', 0);
        $areafiles = $fs->get_area_tree($user->ctxid, 'user', 'private', 666);
        //TODO: verify result!! MDL-33326
    }

    public function test_get_file_by_id() {
        $user = $this->setup_three_private_files();
        $fs = get_file_storage();

        $areafiles = $fs->get_area_files($user->ctxid, 'user', 'private');

        // Test get_file_by_id.
        $filebyid = reset($areafiles);
        $shouldbesame = $fs->get_file_by_id($filebyid->get_id());
        $this->assertEquals($filebyid->get_contenthash(), $shouldbesame->get_contenthash());
    }

    public function test_get_file_by_hash() {
        $user = $this->setup_three_private_files();
        $fs = get_file_storage();

        $areafiles = $fs->get_area_files($user->ctxid, 'user', 'private');
        // Test get_file_by_hash
        $filebyhash = reset($areafiles);
        $shouldbesame = $fs->get_file_by_hash($filebyhash->get_pathnamehash());
        $this->assertEquals($filebyhash->get_id(), $shouldbesame->get_id());
    }

    public function test_get_references_by_storedfile() {
        $user = $this->setup_three_private_files();
        $fs = get_file_storage();

        $areafiles = $fs->get_area_files($user->ctxid, 'user', 'private');
        //Test get_file_by_hash

        $testfile = reset($areafiles);
        $references = $fs->get_references_by_storedfile($testfile);
        //TODO: verify result!! MDL-33326
    }

    public function test_get_external_files() {
        $user = $this->setup_three_private_files();
        $fs = get_file_storage();

        $repos = repository::get_instances(array('type'=>'user'));
        $userrepository = reset($repos);
        $this->assertInstanceOf('repository', $userrepository);

        // this should break on oracle
        $fs->get_external_files($userrepository->id, 'id');
        //TODO: verify result!! MDL-33326
     }

    public function test_get_directory_files() {
        $user = $this->setup_three_private_files();
        $fs = get_file_storage();

        // This should also break on oracle.
        $fs->create_directory($user->ctxid, 'user', 'private', 0, '/');
        //TODO: verify result!! MDL-33326

        // Don't recurse with dirs
        $fs->get_directory_files($user->ctxid, 'user', 'private', 0, '/', false, true, 'id');
        //TODO: verify result!! MDL-33326

        // Don't recurse without dirs
        $fs->get_directory_files($user->ctxid, 'user', 'private', 0, '/', false, false, 'id');
        //TODO: verify result!! MDL-33326

        // Recurse with dirs
        $fs->get_directory_files($user->ctxid, 'user', 'private', 0, '/', true, true, 'id');
        //TODO: verify result!! MDL-33326
        // Recurse without dirs
        $fs->get_directory_files($user->ctxid, 'user', 'private', 0, '/', true, false, 'id');
        //TODO: verify result!! MDL-33326
    }

    public function test_search_references() {
        $fs = get_file_storage();
        $references = $fs->search_references('testsearch');
        //TODO: verify result!! MDL-33326
    }

    public function test_search_references_count() {
        $fs = get_file_storage();
        $references = $fs->search_references_count('testsearch');
        //TODO: verify result!! MDL-33326
    }

    public function test_delete_area_files() {
        $user = $this->setup_three_private_files();
        $fs = get_file_storage();

        // Get area files with default options.
        $areafiles = $fs->get_area_files($user->ctxid, 'user', 'private');
        // Should be the two files we added plus the folder.
        $this->assertEquals(4, count($areafiles));
        $fs->delete_area_files($user->ctxid, 'user', 'private');

        $areafiles = $fs->get_area_files($user->ctxid, 'user', 'private');
        // Should be the two files we added plus the folder.
        $this->assertEquals(0, count($areafiles));
    }

    public function test_delete_area_files_select() {
        $user = $this->setup_three_private_files();
        $fs = get_file_storage();

        // Get area files with default options.
        $areafiles = $fs->get_area_files($user->ctxid, 'user', 'private');
        // Should be the two files we added plus the folder.
        $this->assertEquals(4, count($areafiles));
        $fs->delete_area_files_select($user->ctxid, 'user', 'private', '!= :notitemid', array('notitemid'=>9999));

        $areafiles = $fs->get_area_files($user->ctxid, 'user', 'private');
        // Should be the two files we added plus the folder.
        $this->assertEquals(0, count($areafiles));
    }
}
