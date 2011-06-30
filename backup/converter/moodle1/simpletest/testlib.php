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
 * Unit tests for the moodle1 converter
 *
 * @package    core
 * @subpackage backup-convert
 * @copyright  2011 Mark Nielsen <mark@moodlerooms.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/converter/moodle1/lib.php');

class moodle1_converter_test extends UnitTestCase {

    public static $includecoverage = array();

    /** @var string the name of the directory containing the unpacked Moodle 1.9 backup */
    protected $tempdir;

    public function setUp() {
        global $CFG;

        $this->tempdir = convert_helper::generate_id('simpletest');
        check_dir_exists("$CFG->dataroot/temp/backup/$this->tempdir/course_files/sub1");
        check_dir_exists("$CFG->dataroot/temp/backup/$this->tempdir/moddata/unittest/4/7");
        copy(
            "$CFG->dirroot/backup/converter/moodle1/simpletest/files/moodle.xml",
            "$CFG->dataroot/temp/backup/$this->tempdir/moodle.xml"
        );
        copy(
            "$CFG->dirroot/backup/converter/moodle1/simpletest/files/icon.gif",
            "$CFG->dataroot/temp/backup/$this->tempdir/course_files/file1.gif"
        );
        copy(
            "$CFG->dirroot/backup/converter/moodle1/simpletest/files/icon.gif",
            "$CFG->dataroot/temp/backup/$this->tempdir/course_files/sub1/file2.gif"
        );
        copy(
            "$CFG->dirroot/backup/converter/moodle1/simpletest/files/icon.gif",
            "$CFG->dataroot/temp/backup/$this->tempdir/moddata/unittest/4/file1.gif"
        );
        copy(
            "$CFG->dirroot/backup/converter/moodle1/simpletest/files/icon.gif",
            "$CFG->dataroot/temp/backup/$this->tempdir/moddata/unittest/4/icon.gif"
        );
        copy(
            "$CFG->dirroot/backup/converter/moodle1/simpletest/files/icon.gif",
            "$CFG->dataroot/temp/backup/$this->tempdir/moddata/unittest/4/7/icon.gif"
        );
    }

    public function tearDown() {
        global $CFG;
        if (empty($CFG->keeptempdirectoriesonbackup)) {
            fulldelete("$CFG->dataroot/temp/backup/$this->tempdir");
        }
    }

    public function test_detect_format() {
        $detected = moodle1_converter::detect_format($this->tempdir);
        $this->assertEqual(backup::FORMAT_MOODLE1, $detected);
    }

    public function test_convert_factory() {
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $this->assertIsA($converter, 'moodle1_converter');
    }

    public function test_stash_storage_not_created() {
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $this->expectException('moodle1_convert_storage_exception');
        $converter->set_stash('tempinfo', 12);
    }

    public function test_stash_requiring_empty_stash() {
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->create_stash_storage();
        $converter->set_stash('tempinfo', 12);
        $this->expectException('moodle1_convert_empty_storage_exception');
        try {
            $converter->get_stash('anothertempinfo');

        } catch (moodle1_convert_empty_storage_exception $e) {
            // we must drop the storage here so we are able to re-create it in the next test
            $converter->drop_stash_storage();
            throw new moodle1_convert_empty_storage_exception('rethrowing');
        }
    }

    public function test_stash_storage() {
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->create_stash_storage();

        // no implicit stashes
        $stashes = $converter->get_stash_names();
        $this->assertIsA($stashes, 'array');
        $this->assertTrue(empty($stashes));

        // test stashes without itemid
        $converter->set_stash('tempinfo1', 12);
        $converter->set_stash('tempinfo2', array('a' => 2, 'b' => 3));
        $stashes = $converter->get_stash_names();
        $this->assertIsA($stashes, 'array');
        $this->assertEqual(2, count($stashes));
        $this->assertTrue(in_array('tempinfo1', $stashes));
        $this->assertTrue(in_array('tempinfo2', $stashes));
        $this->assertIdentical(12, $converter->get_stash('tempinfo1'));
        $this->assertIdentical(array('a' => 2, 'b' => 3), $converter->get_stash('tempinfo2'));

        // overwriting a stashed value is allowed
        $converter->set_stash('tempinfo1', '13');
        $this->assertNotIdentical(13, $converter->get_stash('tempinfo1'));
        $this->assertIdentical('13', $converter->get_stash('tempinfo1'));

        // repeated reading is allowed
        $this->assertIdentical('13', $converter->get_stash('tempinfo1'));

        // storing empty array
        $converter->set_stash('empty_array_stash', array());
        $restored = $converter->get_stash('empty_array_stash');
        //$this->assertIsA($restored, 'array'); // todo return null now, this needs MDL-27713 to be fixed, then uncomment
        $this->assertTrue(empty($restored));

        // test stashes with itemid
        $converter->set_stash('tempinfo', 'Hello', 1);
        $converter->set_stash('tempinfo', 'World', 2);
        $this->assertIdentical('Hello', $converter->get_stash('tempinfo', 1));
        $this->assertIdentical('World', $converter->get_stash('tempinfo', 2));

        // test get_stash_itemids()
        $ids = $converter->get_stash_itemids('course_fileref');
        $this->assertIsA($ids, 'array');
        $this->assertTrue(empty($ids));

        $converter->set_stash('course_fileref', null, 34);
        $converter->set_stash('course_fileref', null, 52);
        $ids = $converter->get_stash_itemids('course_fileref');
        $this->assertEqual(2, count($ids));
        $this->assertTrue(in_array(34, $ids));
        $this->assertTrue(in_array(52, $ids));

        $converter->drop_stash_storage();
    }

   public function test_get_stash_or_default() {
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
        $this->assertIsA($stashed, 'array');
        $this->assertTrue(is_object($stashed[42]));
        $this->assertTrue($stashed[42]->id === 42);

        $converter->drop_stash_storage();
    }

    public function test_get_contextid() {
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);

        // stash storage must be created in advance
        $converter->create_stash_storage();

        // ids are generated on the first call
        $id1 = $converter->get_contextid(CONTEXT_BLOCK, 10);
        $id2 = $converter->get_contextid(CONTEXT_BLOCK, 11);
        $id3 = $converter->get_contextid(CONTEXT_MODULE, 10);

        $this->assertNotEqual($id1, $id2);
        $this->assertNotEqual($id1, $id3);
        $this->assertNotEqual($id2, $id3);

        // and then re-used if called with the same params
        $this->assertEqual($id1, $converter->get_contextid(CONTEXT_BLOCK, 10));
        $this->assertEqual($id2, $converter->get_contextid(CONTEXT_BLOCK, 11));
        $this->assertEqual($id3, $converter->get_contextid(CONTEXT_MODULE, 10));

        // for system and course level, the instance is irrelevant
        // as we need only one system and one course
        $id1 = $converter->get_contextid(CONTEXT_COURSE);
        $id2 = $converter->get_contextid(CONTEXT_COURSE, 10);
        $id3 = $converter->get_contextid(CONTEXT_COURSE, 14);

        $this->assertEqual($id1, $id2);
        $this->assertEqual($id1, $id3);

        $id1 = $converter->get_contextid(CONTEXT_SYSTEM);
        $id2 = $converter->get_contextid(CONTEXT_SYSTEM, 11);
        $id3 = $converter->get_contextid(CONTEXT_SYSTEM, 15);

        $this->assertEqual($id1, $id2);
        $this->assertEqual($id1, $id3);

        $converter->drop_stash_storage();
    }

    public function test_get_nextid() {
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);

        $id1 = $converter->get_nextid();
        $id2 = $converter->get_nextid();
        $id3 = $converter->get_nextid();

        $this->assertTrue(0 < $id1);
        $this->assertTrue($id1 < $id2);
        $this->assertTrue($id2 < $id3);
    }

    public function test_migrate_file() {
        // set-up the file manager
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->create_stash_storage();
        $contextid = $converter->get_contextid(CONTEXT_MODULE, 32);
        $fileman   = $converter->get_file_manager($contextid, 'mod_unittest', 'testarea');
        // this fileman has not converted anything yet
        $fileids = $fileman->get_fileids();
        $this->assertIsA($fileids, 'array');
        $this->assertEqual(0, count($fileids));
        // try to migrate a non-existing directory
        $returned = $fileman->migrate_directory('not/existing/directory');
        $this->assertIsA($returned, 'array');
        $this->assertEqual(0, count($returned));
        $fileids = $fileman->get_fileids();
        $this->assertIsA($fileids, 'array');
        $this->assertEqual(0, count($fileids));
        // migrate a single file
        $fileman->itemid = 4;
        $fileman->migrate_file('moddata/unittest/4/icon.gif');
        $this->assertTrue(is_file($converter->get_workdir_path().'/files/4e/4ea114b0558f53e3af8dd9afc0e0810a95c2a724'));
        // get the file id
        $fileids = $fileman->get_fileids();
        $this->assertIsA($fileids, 'array');
        $this->assertEqual(1, count($fileids));
        // migrate another single file into another file area
        $fileman->filearea = 'anotherarea';
        $fileman->itemid = 7;
        $fileman->migrate_file('moddata/unittest/4/7/icon.gif', '/', 'renamed.gif');
        // get the file records
        $filerecordids = $converter->get_stash_itemids('files');
        foreach ($filerecordids as $filerecordid) {
            $filerecord = $converter->get_stash('files', $filerecordid);
            $this->assertEqual('4ea114b0558f53e3af8dd9afc0e0810a95c2a724', $filerecord['contenthash']);
            $this->assertEqual($contextid, $filerecord['contextid']);
            $this->assertEqual('mod_unittest', $filerecord['component']);
            if ($filerecord['filearea'] === 'testarea') {
                $this->assertEqual(4, $filerecord['itemid']);
                $this->assertEqual('icon.gif', $filerecord['filename']);
            }
        }
        // explicitly clear the list of migrated files
        $this->assertTrue(count($fileman->get_fileids()) > 0);
        $fileman->reset_fileids();
        $this->assertTrue(count($fileman->get_fileids()) == 0);
        $converter->drop_stash_storage();
    }

    public function test_convert_path() {
        $path = new convert_path('foo_bar', '/ROOT/THINGS/FOO/BAR');
        $this->assertEqual('foo_bar', $path->get_name());
        $this->assertEqual('/ROOT/THINGS/FOO/BAR', $path->get_path());
        $this->assertEqual('process_foo_bar', $path->get_processing_method());
        $this->assertEqual('on_foo_bar_start', $path->get_start_method());
        $this->assertEqual('on_foo_bar_end', $path->get_end_method());
    }

    public function test_convert_path_implicit_recipes() {
        $path = new convert_path('foo_bar', '/ROOT/THINGS/FOO/BAR');
        $data = array(
            'ID' => 76,
            'ELOY' => 'stronk7',
            'MARTIN' => 'moodler',
            'EMPTY' => null,
        );
        // apply default recipes (converting keys to lowercase)
        $data = $path->apply_recipes($data);
        $this->assertEqual(4, count($data));
        $this->assertEqual(76, $data['id']);
        $this->assertEqual('stronk7', $data['eloy']);
        $this->assertEqual('moodler', $data['martin']);
        $this->assertIdentical(null, $data['empty']);
    }

    public function test_convert_path_explicit_recipes() {
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

        $this->assertEqual(5, count($data));
        $this->assertFalse(array_key_exists('id', $data));
        $this->assertEqual('stronk7', $data['eloy']);
        $this->assertEqual('moodler', $data['martin']);
        $this->assertEqual('mudrd8mz', $data['david']);
        $this->assertEqual('skodak', $data['petr']);
        $this->assertIdentical(null, $data['nothing']);
    }

    public function test_grouped_data_on_nongrouped_convert_path() {
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
        $this->expectException('convert_path_exception');
        $data = $path->apply_recipes($data);
    }

    public function test_grouped_convert_path_with_recipes() {
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
        $this->assertEqual('Heineken', $data['beers'][1]['beer']['name']);

        // an attempt to provide explicit recipes on grouped elements throws exception
        $this->expectException('convert_path_exception');
        $path = new convert_path(
            'beer_style', '/ROOT/BEER_STYLES/BEER_STYLE',
            array(
                'renamefields' => array(
                    'name' => 'beername',   // note this is confusing recipe because the 'name' is used for both
                                            // beer-style name ('Pale lagers') and beer name ('Pilsner Urquell')
                )
            ), true);
    }

    public function test_referenced_course_files() {

        $text = 'This is a text containing links to file.php
as it is parsed from the backup file. <br /><br /><img border="0" width="110" vspace="0" hspace="0" height="92" title="News" alt="News" src="$@FILEPHP@$$@SLASH@$pics$@SLASH@$news.gif" /><a href="$@FILEPHP@$$@SLASH@$pics$@SLASH@$news.gif$@FORCEDOWNLOAD@$">download image</a><br />
    <br /><a href=\'$@FILEPHP@$$@SLASH@$MANUAL.DOC$@FORCEDOWNLOAD@$\'>download manual</a><br />';

        $files = moodle1_converter::find_referenced_files($text);
        $this->assertIsA($files, 'array');
        $this->assertEqual(2, count($files));
        $this->assertTrue(in_array('/pics/news.gif', $files));
        $this->assertTrue(in_array('/MANUAL.DOC', $files));

        $text = moodle1_converter::rewrite_filephp_usage($text, array('/pics/news.gif', '/another/file/notused.txt'), $files);
        $this->assertEqual($text, 'This is a text containing links to file.php
as it is parsed from the backup file. <br /><br /><img border="0" width="110" vspace="0" hspace="0" height="92" title="News" alt="News" src="@@PLUGINFILE@@/pics/news.gif" /><a href="@@PLUGINFILE@@/pics/news.gif?forcedownload=1">download image</a><br />
    <br /><a href=\'$@FILEPHP@$$@SLASH@$MANUAL.DOC$@FORCEDOWNLOAD@$\'>download manual</a><br />');
    }

    public function test_question_bank_conversion() {
        global $CFG;

        copy(
            "$CFG->dirroot/backup/converter/moodle1/simpletest/files/questions.xml",
            "$CFG->dataroot/temp/backup/$this->tempdir/moodle.xml"
        );
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->convert();
    }

    public function test_convert_run_convert() {
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $converter->convert();
    }

    public function test_inforef_manager() {
        $converter = convert_factory::get_converter('moodle1', $this->tempdir);
        $inforef = $converter->get_inforef_manager('unittest');
        $inforef->add_ref('file', 45);
        $inforef->add_refs('file', array(46, 47));
        // todo test the write_refs() via some dummy xml_writer
        $this->expectException('coding_exception');
        $inforef->add_ref('unknown_referenced_item_name', 76);
    }
}
