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
 * Unit tests for /lib/filelib.php.
 *
 * @package   core_files
 * @category  phpunit
 * @copyright 2009 Jerome Mouneyrac
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');
require_once($CFG->dirroot . '/repository/lib.php');

class filelib_testcase extends advanced_testcase {
    public function test_format_postdata_for_curlcall() {

        //POST params with just simple types
        $postdatatoconvert =array( 'userid' => 1, 'roleid' => 22, 'name' => 'john');
        $expectedresult = "userid=1&roleid=22&name=john";
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEquals($postdata, $expectedresult);

        //POST params with a string containing & character
        $postdatatoconvert =array( 'name' => 'john&emilie', 'roleid' => 22);
        $expectedresult = "name=john%26emilie&roleid=22"; //urlencode: '%26' => '&'
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEquals($postdata, $expectedresult);

        //POST params with an empty value
        $postdatatoconvert =array( 'name' => null, 'roleid' => 22);
        $expectedresult = "name=&roleid=22";
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEquals($postdata, $expectedresult);

        //POST params with complex types
        $postdatatoconvert =array( 'users' => array(
            array(
                'id' => 2,
                'customfields' => array(
                    array
                    (
                        'type' => 'Color',
                        'value' => 'violet'
                    )
                )
            )
        )
        );
        $expectedresult = "users[0][id]=2&users[0][customfields][0][type]=Color&users[0][customfields][0][value]=violet";
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEquals($postdata, $expectedresult);

        //POST params with other complex types
        $postdatatoconvert = array ('members' =>
        array(
            array('groupid' => 1, 'userid' => 1)
        , array('groupid' => 1, 'userid' => 2)
        )
        );
        $expectedresult = "members[0][groupid]=1&members[0][userid]=1&members[1][groupid]=1&members[1][userid]=2";
        $postdata = format_postdata_for_curlcall($postdatatoconvert);
        $this->assertEquals($postdata, $expectedresult);
    }

    public function test_download_file_content() {
        $testhtml = "http://download.moodle.org/unittest/test.html";
        $contents = download_file_content($testhtml);
        $this->assertEquals('47250a973d1b88d9445f94db4ef2c97a', md5($contents));
    }

    /**
     * Testing prepare draft area
     *
     * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
     * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    public function test_prepare_draft_area() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $usercontext = context_user::instance($user->id);
        $USER = $DB->get_record('user', array('id'=>$user->id));

        $repositorypluginname = 'user';

        $args = array();
        $args['type'] = $repositorypluginname;
        $repos = repository::get_instances($args);
        $userrepository = reset($repos);
        $this->assertInstanceOf('repository', $userrepository);

        $fs = get_file_storage();

        $syscontext = context_system::instance();
        $component = 'core';
        $filearea  = 'unittest';
        $itemid    = 0;
        $filepath  = '/';
        $filename  = 'test.txt';
        $sourcefield = 'Copyright stuff';

        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $itemid,
            'filepath'  => $filepath,
            'filename'  => $filename,
            'source'    => $sourcefield,
        );
        $ref = $fs->pack_reference($filerecord);
        $originalfile = $fs->create_file_from_string($filerecord, 'Test content');
        $fileid = $originalfile->get_id();
        $this->assertInstanceOf('stored_file', $originalfile);

        // create a user private file
        $userfilerecord = new stdClass;
        $userfilerecord->contextid = $usercontext->id;
        $userfilerecord->component = 'user';
        $userfilerecord->filearea  = 'private';
        $userfilerecord->itemid    = 0;
        $userfilerecord->filepath  = '/';
        $userfilerecord->filename  = 'userfile.txt';
        $userfilerecord->source    = 'test';
        $userfile = $fs->create_file_from_string($userfilerecord, 'User file content');
        $userfileref = $fs->pack_reference($userfilerecord);

        $filerefrecord = clone((object)$filerecord);
        $filerefrecord->filename = 'testref.txt';
        // create a file reference
        $fileref = $fs->create_file_from_reference($filerefrecord, $userrepository->id, $userfileref);
        $this->assertInstanceOf('stored_file', $fileref);
        $this->assertEquals($userrepository->id, $fileref->get_repository_id());
        $this->assertEquals($userfile->get_contenthash(), $fileref->get_contenthash());
        $this->assertEquals($userfile->get_filesize(), $fileref->get_filesize());
        $this->assertRegExp('#' . $userfile->get_filename(). '$#', $fileref->get_reference_details());

        $draftitemid = 0;
        file_prepare_draft_area($draftitemid, $syscontext->id, $component, $filearea, $itemid);

        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid);
        $this->assertEquals(3, count($draftfiles));

        $draftfile = $fs->get_file($usercontext->id, 'user', 'draft', $draftitemid, $filepath, $filename);
        $source = unserialize($draftfile->get_source());
        $this->assertEquals($ref, $source->original);
        $this->assertEquals($sourcefield, $source->source);

        $draftfileref = $fs->get_file($usercontext->id, 'user', 'draft', $draftitemid, $filepath, $filerefrecord->filename);
        $this->assertInstanceOf('stored_file', $draftfileref);
        $this->assertEquals(true, $draftfileref->is_external_file());

        // change some information
        $author = 'Dongsheng Cai';
        $draftfile->set_author($author);
        $newsourcefield = 'Get from Flickr';
        $license = 'GPLv3';
        $draftfile->set_license($license);
        // if you want to really just change source field, do this:
        $source = unserialize($draftfile->get_source());
        $newsourcefield = 'From flickr';
        $source->source = $newsourcefield;
        $draftfile->set_source(serialize($source));

        // Save changed file
        file_save_draft_area_files($draftitemid, $syscontext->id, $component, $filearea, $itemid);

        $file = $fs->get_file($syscontext->id, $component, $filearea, $itemid, $filepath, $filename);

        // Make sure it's the original file id
        $this->assertEquals($fileid, $file->get_id());
        $this->assertInstanceOf('stored_file', $file);
        $this->assertEquals($author, $file->get_author());
        $this->assertEquals($license, $file->get_license());
        $this->assertEquals($newsourcefield, $file->get_source());
    }

    /**
     * Testing deleting original files
     *
     * @copyright 2012 Dongsheng Cai {@link http://dongsheng.org}
     * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    public function test_delete_original_file_from_draft() {
        global $USER, $DB;

        $this->resetAfterTest(true);

        $generator = $this->getDataGenerator();
        $user = $generator->create_user();
        $usercontext = context_user::instance($user->id);
        $USER = $DB->get_record('user', array('id'=>$user->id));

        $repositorypluginname = 'user';

        $args = array();
        $args['type'] = $repositorypluginname;
        $repos = repository::get_instances($args);
        $userrepository = reset($repos);
        $this->assertInstanceOf('repository', $userrepository);

        $fs = get_file_storage();
        $syscontext = context_system::instance();

        $filecontent = 'User file content';

        // create a user private file
        $userfilerecord = new stdClass;
        $userfilerecord->contextid = $usercontext->id;
        $userfilerecord->component = 'user';
        $userfilerecord->filearea  = 'private';
        $userfilerecord->itemid    = 0;
        $userfilerecord->filepath  = '/';
        $userfilerecord->filename  = 'userfile.txt';
        $userfilerecord->source    = 'test';
        $userfile = $fs->create_file_from_string($userfilerecord, $filecontent);
        $userfileref = $fs->pack_reference($userfilerecord);
        $contenthash = $userfile->get_contenthash();

        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'core',
            'filearea'  => 'phpunit',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'test.txt',
        );
        // create a file reference
        $fileref = $fs->create_file_from_reference($filerecord, $userrepository->id, $userfileref);
        $this->assertInstanceOf('stored_file', $fileref);
        $this->assertEquals($userrepository->id, $fileref->get_repository_id());
        $this->assertEquals($userfile->get_contenthash(), $fileref->get_contenthash());
        $this->assertEquals($userfile->get_filesize(), $fileref->get_filesize());
        $this->assertRegExp('#' . $userfile->get_filename(). '$#', $fileref->get_reference_details());

        $draftitemid = 0;
        file_prepare_draft_area($draftitemid, $usercontext->id, 'user', 'private', 0);
        $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid);
        $this->assertEquals(2, count($draftfiles));
        $draftfile = $fs->get_file($usercontext->id, 'user', 'draft', $draftitemid, $userfilerecord->filepath, $userfilerecord->filename);
        $draftfile->delete();
        // Save changed file
        file_save_draft_area_files($draftitemid, $usercontext->id, 'user', 'private', 0);

        // The file reference should be a regular moodle file now
        $fileref = $fs->get_file($syscontext->id, 'core', 'phpunit', 0, '/', 'test.txt');
        $this->assertEquals(false, $fileref->is_external_file());
        $this->assertEquals($contenthash, $fileref->get_contenthash());
        $this->assertEquals($filecontent, $fileref->get_content());
    }
}
