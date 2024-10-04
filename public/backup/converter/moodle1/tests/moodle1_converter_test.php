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

namespace core_backup;

use backup;
use convert_path;
use convert_path_exception;
use convert_factory;
use convert_helper;
use moodle1_converter;
use moodle1_convert_empty_storage_exception;
use moodle1_convert_exception;
use moodle1_convert_storage_exception;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/converter/moodle1/lib.php');

/**
 * Unit tests for the moodle1 converter
 *
 * @package    core_backup
 * @subpackage backup-convert
 * @category   test
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class moodle1_converter_test extends \advanced_testcase {

    /** @var string the name of the directory containing the unpacked Moodle 1.9 backup */
    protected $tempdir;

    /** @var string the full name of the directory containing the unpacked Moodle 1.9 backup */
    protected $tempdirpath;

    /** @var string saved hash of an icon file used during testing */
    protected $iconhash;

    protected function setUp(): void {
        global $CFG;
        parent::setUp();

        $this->tempdir = convert_helper::generate_id('unittest');
        $this->tempdirpath = make_backup_temp_directory($this->tempdir);
        check_dir_exists("$this->tempdirpath/course_files/sub1");
        check_dir_exists("$this->tempdirpath/moddata/unittest/4/7");
        copy(
            "$CFG->dirroot/backup/converter/moodle1/tests/fixtures/moodle.xml",
            "$this->tempdirpath/moodle.xml"
        );
        copy(
            "$CFG->dirroot/backup/converter/moodle1/tests/fixtures/icon.gif",
            "$this->tempdirpath/course_files/file1.gif"
        );
        copy(
            "$CFG->dirroot/backup/converter/moodle1/tests/fixtures/icon.gif",
            "$this->tempdirpath/course_files/sub1/file2.gif"
        );
        copy(
            "$CFG->dirroot/backup/converter/moodle1/tests/fixtures/icon.gif",
            "$this->tempdirpath/moddata/unittest/4/file1.gif"
        );
        copy(
            "$CFG->dirroot/backup/converter/moodle1/tests/fixtures/icon.gif",
            "$this->tempdirpath/moddata/unittest/4/icon.gif"
        );
        $this->iconhash = \file_storage::hash_from_path($this->tempdirpath.'/moddata/unittest/4/icon.gif');
        copy(
            "$CFG->dirroot/backup/converter/moodle1/tests/fixtures/icon.gif",
            "$this->tempdirpath/moddata/unittest/4/7/icon.gif"
        );
    }

    protected function tearDown(): void {
        global $CFG;
        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete($this->tempdirpath);
        }
        parent::tearDown();
    }

    public function test_detect_format(): void {
        $detected = moodle1_converter::detect_format($this->tempdir);
        $this->assertEquals(backup::FORMAT_MOODLE1, $detected);
    }

    public function test_convert_factory(): void {
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $this->assertInstanceOf('moodle1_converter', $converter);
    }

    public function test_stash_storage_not_created(): void {
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $this->expectException(moodle1_convert_storage_exception::class);
        $converter->set_stash('tempinfo', 12);
    }

    public function test_stash_requiring_empty_stash(): void {
        $this->resetAfterTest(true);
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->create_stash_storage();
        $converter->set_stash('tempinfo', 12);
        try {
            $converter->get_stash('anothertempinfo');

        } catch (moodle1_convert_empty_storage_exception $e) {
            // we must drop the storage here so we are able to re-create it in the next test
            $this->expectException(moodle1_convert_empty_storage_exception::class);
            $converter->drop_stash_storage();
            throw new moodle1_convert_empty_storage_exception('rethrowing');
        }
    }

    public function test_stash_storage(): void {
        $this->resetAfterTest(true);
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->create_stash_storage();

        // no implicit stashes
        $stashes = $converter->get_stash_names();
        $this->assertEquals(gettype($stashes), 'array');
        $this->assertTrue(empty($stashes));

        // test stashes without itemid
        $converter->set_stash('tempinfo1', 12);
        $converter->set_stash('tempinfo2', array('a' => 2, 'b' => 3));
        $stashes = $converter->get_stash_names();
        $this->assertEquals('array', gettype($stashes));
        $this->assertEquals(2, count($stashes));
        $this->assertTrue(in_array('tempinfo1', $stashes));
        $this->assertTrue(in_array('tempinfo2', $stashes));
        $this->assertEquals(12, $converter->get_stash('tempinfo1'));
        $this->assertEquals(array('a' => 2, 'b' => 3), $converter->get_stash('tempinfo2'));

        // overwriting a stashed value is allowed
        $converter->set_stash('tempinfo1', '13');
        $this->assertNotSame(13, $converter->get_stash('tempinfo1'));
        $this->assertSame('13', $converter->get_stash('tempinfo1'));

        // repeated reading is allowed
        $this->assertEquals('13', $converter->get_stash('tempinfo1'));

        // storing empty array
        $converter->set_stash('empty_array_stash', array());
        $restored = $converter->get_stash('empty_array_stash');
        //$this->assertEquals(gettype($restored), 'array'); // todo return null now, this needs MDL-27713 to be fixed, then uncomment
        $this->assertTrue(empty($restored));

        // test stashes with itemid
        $converter->set_stash('tempinfo', 'Hello', 1);
        $converter->set_stash('tempinfo', 'World', 2);
        $this->assertSame('Hello', $converter->get_stash('tempinfo', 1));
        $this->assertSame('World', $converter->get_stash('tempinfo', 2));

        // test get_stash_itemids()
        $ids = $converter->get_stash_itemids('course_fileref');
        $this->assertEquals(gettype($ids), 'array');
        $this->assertTrue(empty($ids));

        $converter->set_stash('course_fileref', null, 34);
        $converter->set_stash('course_fileref', null, 52);
        $ids = $converter->get_stash_itemids('course_fileref');
        $this->assertEquals(2, count($ids));
        $this->assertTrue(in_array(34, $ids));
        $this->assertTrue(in_array(52, $ids));

        $converter->drop_stash_storage();
    }

    public function test_get_stash_or_default(): void {
        $this->resetAfterTest(true);
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->create_stash_storage();

        $this->assertTrue(is_null($converter->get_stash_or_default('stashname')));
        $this->assertTrue(is_null($converter->get_stash_or_default('stashname', 7)));
        $this->assertTrue('default' === $converter->get_stash_or_default('stashname', 0, 'default'));
        $this->assertTrue(array('foo', 'bar') === $converter->get_stash_or_default('stashname', 42, array('foo', 'bar')));

        //$converter->set_stash('stashname', 0);
        //$this->assertFalse(is_null($converter->get_stash_or_default('stashname'))); // todo returns true now, this needs MDL-27713 to be fixed

        //$converter->set_stash('stashname', '');
        //$this->assertFalse(is_null($converter->get_stash_or_default('stashname'))); // todo returns true now, this needs MDL-27713 to be fixed

        //$converter->set_stash('stashname', array());
        //$this->assertFalse(is_null($converter->get_stash_or_default('stashname'))); // todo returns true now, this needs MDL-27713 to be fixed

        $converter->set_stash('stashname', 42);
        $this->assertTrue(42 === $converter->get_stash_or_default('stashname'));
        $this->assertTrue(is_null($converter->get_stash_or_default('stashname', 1)));
        $this->assertTrue(42 === $converter->get_stash_or_default('stashname', 0, 61));

        $converter->set_stash('stashname', array(42 => (object)array('id' => 42)), 18);
        $stashed = $converter->get_stash_or_default('stashname', 18, 1984);
        $this->assertEquals(gettype($stashed), 'array');
        $this->assertTrue(is_object($stashed[42]));
        $this->assertTrue($stashed[42]->id === 42);

        $converter->drop_stash_storage();
    }

    public function test_get_contextid(): void {
        $this->resetAfterTest(true);

        $converter = convert_factory::get_converter('moodle1', $this->tempdir);

        // stash storage must be created in advance
        $converter->create_stash_storage();

        // ids are generated on the first call
        $id1 = $converter->get_contextid(CONTEXT_BLOCK, 10);
        $id2 = $converter->get_contextid(CONTEXT_BLOCK, 11);
        $id3 = $converter->get_contextid(CONTEXT_MODULE, 10);

        $this->assertNotEquals($id1, $id2);
        $this->assertNotEquals($id1, $id3);
        $this->assertNotEquals($id2, $id3);

        // and then re-used if called with the same params
        $this->assertEquals($id1, $converter->get_contextid(CONTEXT_BLOCK, 10));
        $this->assertEquals($id2, $converter->get_contextid(CONTEXT_BLOCK, 11));
        $this->assertEquals($id3, $converter->get_contextid(CONTEXT_MODULE, 10));

        // for system and course level, the instance is irrelevant
        // as we need only one system and one course
        $id1 = $converter->get_contextid(CONTEXT_COURSE);
        $id2 = $converter->get_contextid(CONTEXT_COURSE, 10);
        $id3 = $converter->get_contextid(CONTEXT_COURSE, 14);

        $this->assertEquals($id1, $id2);
        $this->assertEquals($id1, $id3);

        $id1 = $converter->get_contextid(CONTEXT_SYSTEM);
        $id2 = $converter->get_contextid(CONTEXT_SYSTEM, 11);
        $id3 = $converter->get_contextid(CONTEXT_SYSTEM, 15);

        $this->assertEquals($id1, $id2);
        $this->assertEquals($id1, $id3);

        $converter->drop_stash_storage();
    }

    public function test_get_nextid(): void {
        $this->resetAfterTest(true);

        $converter = convert_factory::get_converter('moodle1', $this->tempdir);

        $id1 = $converter->get_nextid();
        $id2 = $converter->get_nextid();
        $id3 = $converter->get_nextid();

        $this->assertTrue(0 < $id1);
        $this->assertTrue($id1 < $id2);
        $this->assertTrue($id2 < $id3);
    }

    public function test_migrate_file(): void {
        $this->resetAfterTest(true);

        // set-up the file manager
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->create_stash_storage();
        $contextid = $converter->get_contextid(CONTEXT_MODULE, 32);
        $fileman   = $converter->get_file_manager($contextid, 'mod_unittest', 'testarea');
        // this fileman has not converted anything yet
        $fileids = $fileman->get_fileids();
        $this->assertEquals(gettype($fileids), 'array');
        $this->assertEquals(0, count($fileids));
        // try to migrate an invalid file
        $fileman->itemid = 1;
        $thrown = false;
        try {
            $fileman->migrate_file('/../../../../../../../../../../../../../../etc/passwd');
        } catch (moodle1_convert_exception $e) {
            $thrown = true;
        }
        $this->assertTrue($thrown);
        // migrate a single file
        $fileman->itemid = 4;
        $fileman->migrate_file('moddata/unittest/4/icon.gif');
        $subdir = substr($this->iconhash, 0, 2);
        $this->assertTrue(is_file($converter->get_workdir_path().'/files/'.$subdir.'/'.$this->iconhash));
        // get the file id
        $fileids = $fileman->get_fileids();
        $this->assertEquals(gettype($fileids), 'array');
        $this->assertEquals(1, count($fileids));
        // migrate another single file into another file area
        $fileman->filearea = 'anotherarea';
        $fileman->itemid = 7;
        $fileman->migrate_file('moddata/unittest/4/7/icon.gif', '/', 'renamed.gif');
        // get the file records
        $filerecordids = $converter->get_stash_itemids('files');
        foreach ($filerecordids as $filerecordid) {
            $filerecord = $converter->get_stash('files', $filerecordid);
            $this->assertEquals($this->iconhash, $filerecord['contenthash']);
            $this->assertEquals($contextid, $filerecord['contextid']);
            $this->assertEquals('mod_unittest', $filerecord['component']);
            if ($filerecord['filearea'] === 'testarea') {
                $this->assertEquals(4, $filerecord['itemid']);
                $this->assertEquals('icon.gif', $filerecord['filename']);
            }
        }
        // explicitly clear the list of migrated files
        $this->assertTrue(count($fileman->get_fileids()) > 0);
        $fileman->reset_fileids();
        $this->assertTrue(count($fileman->get_fileids()) == 0);
        $converter->drop_stash_storage();
    }

    public function test_migrate_directory(): void {
        $this->resetAfterTest(true);

        // Set-up the file manager.
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->create_stash_storage();
        $contextid = $converter->get_contextid(CONTEXT_MODULE, 32);
        $fileman   = $converter->get_file_manager($contextid, 'mod_unittest', 'testarea');
        // This fileman has not converted anything yet.
        $fileids = $fileman->get_fileids();
        $this->assertEquals(gettype($fileids), 'array');
        $this->assertEquals(0, count($fileids));
        // Try to migrate a non-existing directory.
        $returned = $fileman->migrate_directory('not/existing/directory');
        $this->assertEquals(gettype($returned), 'array');
        $this->assertEquals(0, count($returned));
        $fileids = $fileman->get_fileids();
        $this->assertEquals(gettype($fileids), 'array');
        $this->assertEquals(0, count($fileids));
        // Try to migrate whole course_files.
        $returned = $fileman->migrate_directory('course_files');
        $this->assertEquals(gettype($returned), 'array');
        $this->assertEquals(4, count($returned)); // Two files, two directories.
        $fileids = $fileman->get_fileids();
        $this->assertEquals(gettype($fileids), 'array');
        $this->assertEquals(4, count($fileids));
        $subdir = substr($this->iconhash, 0, 2);
        $this->assertTrue(is_file($converter->get_workdir_path().'/files/'.$subdir.'/'.$this->iconhash));

        // Check the file records.
        $files = array();
        $filerecordids = $converter->get_stash_itemids('files');
        foreach ($filerecordids as $filerecordid) {
            $filerecord = $converter->get_stash('files', $filerecordid);
            $files[$filerecord['filepath'].$filerecord['filename']] = $filerecord;
        }
        $this->assertEquals('array', gettype($files['/.']));
        $this->assertEquals('array', gettype($files['/file1.gif']));
        $this->assertEquals('array', gettype($files['/sub1/.']));
        $this->assertEquals('array', gettype($files['/sub1/file2.gif']));
        $this->assertEquals(\file_storage::hash_from_string(''), $files['/.']['contenthash']);
        $this->assertEquals(\file_storage::hash_from_string(''), $files['/sub1/.']['contenthash']);
        $this->assertEquals($this->iconhash, $files['/file1.gif']['contenthash']);
        $this->assertEquals($this->iconhash, $files['/sub1/file2.gif']['contenthash']);

        $converter->drop_stash_storage();
    }

    public function test_migrate_directory_with_trailing_slash(): void {
        $this->resetAfterTest(true);

        // Set-up the file manager.
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->create_stash_storage();
        $contextid = $converter->get_contextid(CONTEXT_MODULE, 32);
        $fileman   = $converter->get_file_manager($contextid, 'mod_unittest', 'testarea');
        // Try to migrate a subdirectory passed with the trailing slash.
        $returned = $fileman->migrate_directory('course_files/sub1/');
        // Debugging message must be thrown in this case.
        $this->assertDebuggingCalled(null, DEBUG_DEVELOPER);
        $this->assertEquals(gettype($returned), 'array');
        $this->assertEquals(2, count($returned)); // One file, one directory.

        $converter->drop_stash_storage();
    }

    public function test_convert_path(): void {
        $path = new convert_path('foo_bar', '/ROOT/THINGS/FOO/BAR');
        $this->assertEquals('foo_bar', $path->get_name());
        $this->assertEquals('/ROOT/THINGS/FOO/BAR', $path->get_path());
        $this->assertEquals('process_foo_bar', $path->get_processing_method());
        $this->assertEquals('on_foo_bar_start', $path->get_start_method());
        $this->assertEquals('on_foo_bar_end', $path->get_end_method());
    }

    public function test_convert_path_implicit_recipes(): void {
        $path = new convert_path('foo_bar', '/ROOT/THINGS/FOO/BAR');
        $data = array(
            'ID' => 76,
            'ELOY' => 'stronk7',
            'MARTIN' => 'moodler',
            'EMPTY' => null,
        );
        // apply default recipes (converting keys to lowercase)
        $data = $path->apply_recipes($data);
        $this->assertEquals(4, count($data));
        $this->assertEquals(76, $data['id']);
        $this->assertEquals('stronk7', $data['eloy']);
        $this->assertEquals('moodler', $data['martin']);
        $this->assertSame(null, $data['empty']);
    }

    public function test_convert_path_explicit_recipes(): void {
        $path = new convert_path(
            'foo_bar', '/ROOT/THINGS/FOO/BAR',
            array(
                'newfields' => array(
                    'david' => 'mudrd8mz',
                    'petr'  => 'skodak',
                ),
                'renamefields' => array(
                    'empty' => 'nothing',
                ),
                'dropfields' => array(
                    'id'
                ),
            )
        );
        $data = array(
            'ID' => 76,
            'ELOY' => 'stronk7',
            'MARTIN' => 'moodler',
            'EMPTY' => null,
        );
        $data = $path->apply_recipes($data);

        $this->assertEquals(5, count($data));
        $this->assertFalse(array_key_exists('id', $data));
        $this->assertEquals('stronk7', $data['eloy']);
        $this->assertEquals('moodler', $data['martin']);
        $this->assertEquals('mudrd8mz', $data['david']);
        $this->assertEquals('skodak', $data['petr']);
        $this->assertSame(null, $data['nothing']);
    }

    public function test_grouped_data_on_nongrouped_convert_path(): void {
        // prepare some grouped data
        $data = array(
            'ID' => 77,
            'NAME' => 'Pale lagers',
            'BEERS' => array(
                array(
                    'BEER' => array(
                        'ID' => 67,
                        'NAME' => 'Pilsner Urquell',
                    )
                ),
                array(
                    'BEER' => array(
                        'ID' => 34,
                        'NAME' => 'Heineken',
                    )
                ),
            )
        );

        // declare a non-grouped path
        $path = new convert_path('beer_style', '/ROOT/BEER_STYLES/BEER_STYLE');

        // an attempt to apply recipes throws exception because we do not expect grouped data
        $this->expectException(convert_path_exception::class);
        $data = $path->apply_recipes($data);
    }

    public function test_grouped_convert_path_with_recipes(): void {
        // prepare some grouped data
        $data = array(
            'ID' => 77,
            'NAME' => 'Pale lagers',
            'BEERS' => array(
                array(
                    'BEER' => array(
                        'ID' => 67,
                        'NAME' => 'Pilsner Urquell',
                    )
                ),
                array(
                    'BEER' => array(
                        'ID' => 34,
                        'NAME' => 'Heineken',
                    )
                ),
            )
        );

        // implict recipes work for grouped data if the path is declared as grouped
        $path = new convert_path('beer_style', '/ROOT/BEER_STYLES/BEER_STYLE', array(), true);
        $data = $path->apply_recipes($data);
        $this->assertEquals('Heineken', $data['beers'][1]['beer']['name']);

        // an attempt to provide explicit recipes on grouped elements throws exception
        $this->expectException(convert_path_exception::class);
        $path = new convert_path(
            'beer_style', '/ROOT/BEER_STYLES/BEER_STYLE',
            array(
                'renamefields' => array(
                    'name' => 'beername',   // note this is confusing recipe because the 'name' is used for both
                    // beer-style name ('Pale lagers') and beer name ('Pilsner Urquell')
                )
            ), true);
    }

    public function test_referenced_course_files(): void {

        $text = 'This is a text containing links to file.php
as it is parsed from the backup file. <br /><br /><img border="0" width="110" vspace="0" hspace="0" height="92" title="News" alt="News" src="$@FILEPHP@$$@SLASH@$pics$@SLASH@$news.gif" /><a href="$@FILEPHP@$$@SLASH@$pics$@SLASH@$news.gif$@FORCEDOWNLOAD@$">download image</a><br />
    <div><a href=\'$@FILEPHP@$/../../../../../../../../../../../../../../../etc/passwd\'>download passwords</a></div>
    <div><a href=\'$@FILEPHP@$$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$etc$@SLASH@$shadow\'>download shadows</a></div>
    <br /><a href=\'$@FILEPHP@$$@SLASH@$MANUAL.DOC$@FORCEDOWNLOAD@$\'>download manual</a><br />';

        $files = moodle1_converter::find_referenced_files($text);
        $this->assertEquals(gettype($files), 'array');
        $this->assertEquals(2, count($files));
        $this->assertTrue(in_array('/pics/news.gif', $files));
        $this->assertTrue(in_array('/MANUAL.DOC', $files));

        $text = moodle1_converter::rewrite_filephp_usage($text, array('/pics/news.gif', '/another/file/notused.txt'));
        $this->assertEquals($text, 'This is a text containing links to file.php
as it is parsed from the backup file. <br /><br /><img border="0" width="110" vspace="0" hspace="0" height="92" title="News" alt="News" src="@@PLUGINFILE@@/pics/news.gif" /><a href="@@PLUGINFILE@@/pics/news.gif?forcedownload=1">download image</a><br />
    <div><a href=\'$@FILEPHP@$/../../../../../../../../../../../../../../../etc/passwd\'>download passwords</a></div>
    <div><a href=\'$@FILEPHP@$$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$..$@SLASH@$etc$@SLASH@$shadow\'>download shadows</a></div>
    <br /><a href=\'$@FILEPHP@$$@SLASH@$MANUAL.DOC$@FORCEDOWNLOAD@$\'>download manual</a><br />');
    }

    public function test_referenced_files_urlencoded(): void {

        $text = 'This is a text containing links to file.php
as it is parsed from the backup file. <br /><br /><img border="0" width="110" vspace="0" hspace="0" height="92" title="News" alt="News" src="$@FILEPHP@$$@SLASH@$pics$@SLASH@$news.gif" /><a href="$@FILEPHP@$$@SLASH@$pics$@SLASH@$news.gif$@FORCEDOWNLOAD@$">no space</a><br />
    <br /><a href=\'$@FILEPHP@$$@SLASH@$pics$@SLASH@$news%20with%20spaces.gif$@FORCEDOWNLOAD@$\'>with urlencoded spaces</a><br />
<a href="$@FILEPHP@$$@SLASH@$illegal%20pics%2Bmovies$@SLASH@$romeo%2Bjuliet.avi">Download the full AVI for free! (space and plus encoded)</a>
<a href="$@FILEPHP@$$@SLASH@$illegal pics+movies$@SLASH@$romeo+juliet.avi">Download the full AVI for free! (none encoded)</a>
<a href="$@FILEPHP@$$@SLASH@$illegal%20pics+movies$@SLASH@$romeo+juliet.avi">Download the full AVI for free! (only space encoded)</a>
<a href="$@FILEPHP@$$@SLASH@$illegal pics%2Bmovies$@SLASH@$romeo%2Bjuliet.avi">Download the full AVI for free! (only plus)</a>';

        $files = moodle1_converter::find_referenced_files($text);
        $this->assertEquals(gettype($files), 'array');
        $this->assertEquals(3, count($files));
        $this->assertTrue(in_array('/pics/news.gif', $files));
        $this->assertTrue(in_array('/pics/news with spaces.gif', $files));
        $this->assertTrue(in_array('/illegal pics+movies/romeo+juliet.avi', $files));

        $text = moodle1_converter::rewrite_filephp_usage($text, $files);
        $this->assertEquals('This is a text containing links to file.php
as it is parsed from the backup file. <br /><br /><img border="0" width="110" vspace="0" hspace="0" height="92" title="News" alt="News" src="@@PLUGINFILE@@/pics/news.gif" /><a href="@@PLUGINFILE@@/pics/news.gif?forcedownload=1">no space</a><br />
    <br /><a href=\'@@PLUGINFILE@@/pics/news%20with%20spaces.gif?forcedownload=1\'>with urlencoded spaces</a><br />
<a href="@@PLUGINFILE@@/illegal%20pics%2Bmovies/romeo%2Bjuliet.avi">Download the full AVI for free! (space and plus encoded)</a>
<a href="@@PLUGINFILE@@/illegal%20pics%2Bmovies/romeo%2Bjuliet.avi">Download the full AVI for free! (none encoded)</a>
<a href="$@FILEPHP@$$@SLASH@$illegal%20pics+movies$@SLASH@$romeo+juliet.avi">Download the full AVI for free! (only space encoded)</a>
<a href="$@FILEPHP@$$@SLASH@$illegal pics%2Bmovies$@SLASH@$romeo%2Bjuliet.avi">Download the full AVI for free! (only plus)</a>', $text);
    }

    public function test_question_bank_conversion(): void {
        global $CFG;

        $this->resetAfterTest(true);

        copy(
            "$CFG->dirroot/backup/converter/moodle1/tests/fixtures/questions.xml",
            "$this->tempdirpath/moodle.xml"
        );
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->convert();
    }

    public function test_convert_run_convert(): void {
        $this->resetAfterTest(true);
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->convert();
    }

    public function test_inforef_manager(): void {
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $inforef = $converter->get_inforef_manager('unittest');
        $inforef->add_ref('file', 45);
        $inforef->add_refs('file', array(46, 47));
        // todo test the write_refs() via some dummy xml_writer
        $this->expectException('coding_exception');
        $inforef->add_ref('unknown_referenced_item_name', 76);
    }
}
