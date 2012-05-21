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
        $this->assertEquals($userrepository->id, $newstoredfile->repository->id);
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
}
