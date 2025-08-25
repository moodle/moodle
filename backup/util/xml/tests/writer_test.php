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
 * Test xml_writer tests.
 *
 * @package   core_backup
 * @category  test
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_backup;

use memory_xml_output;
use phpunit_util;
use xml_contenttransformer;
use xml_output;
use xml_writer;
use xml_writer_exception;

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
global $CFG;
require_once($CFG->dirroot . '/backup/util/xml/xml_writer.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/xml_output.class.php');
require_once($CFG->dirroot . '/backup/util/xml/output/memory_xml_output.class.php');
require_once($CFG->dirroot . '/backup/util/xml/contenttransformer/xml_contenttransformer.class.php');

/**
 * Test xml_writer tests.
 *
 * @package   core_backup
 * @category  test
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class writer_test extends \basic_testcase {

    /**
     * test xml_writer public methods
     */
    function test_xml_writer_public_api(): void {
        global $CFG;
        // Instantiate xml_output
        $xo = new memory_xml_output();
        $this->assertTrue($xo instanceof xml_output);

        // Instantiate xml_writer with null xml_output
        try {
            $xw = new mock_xml_writer(null);
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'invalid_xml_output');
        }

        // Instantiate xml_writer with wrong xml_output object
        try {
            $xw = new mock_xml_writer(new \stdClass());
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'invalid_xml_output');
        }

        // Instantiate xml_writer with wrong xml_contenttransformer object
        try {
            $xw = new mock_xml_writer($xo, new \stdClass());
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'invalid_xml_contenttransformer');
        }

        // Instantiate xml_writer and start it twice
        $xw = new mock_xml_writer($xo);
        $xw->start();
        try {
            $xw->start();
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'xml_writer_already_started');
        }

        // Instantiate xml_writer and stop it twice
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->start();
        $xw->stop();
        try {
            $xw->stop();
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'xml_writer_already_stopped');
        }

        // Stop writer without starting it
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        try {
            $xw->stop();
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'xml_writer_not_started');
        }

        // Start writer after stopping it
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->start();
        $xw->stop();
        try {
            $xw->start();
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'xml_writer_already_stopped');
        }

        // Try to set prologue/schema after start
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->start();
        try {
            $xw->set_nonamespace_schema('http://moodle.org');
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'xml_writer_already_started');
        }
        try {
            $xw->set_prologue('sweet prologue');
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'xml_writer_already_started');
        }

        // Instantiate properly with memory_xml_output, start and stop.
        // Must get default UTF-8 prologue
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->start();
        $xw->stop();
        $this->assertEquals($xo->get_allcontents(), $xw->get_default_prologue());

        // Instantiate, set prologue and schema, put 1 full tag and get results
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->set_prologue('CLEARLY WRONG PROLOGUE');
        $xw->set_nonamespace_schema('http://moodle.org/littleschema');
        $xw->start();
        $xw->full_tag('TEST', 'Hello World!', array('id' => 1));
        $xw->stop();
        $result = $xo->get_allcontents();
        // Perform various checks
        $this->assertEquals(strpos($result, 'WRONG'), 8);
        $this->assertEquals(strpos($result, '<TEST id="1"'), 22);
        $this->assertEquals(strpos($result, 'xmlns:xsi='), 39);
        $this->assertEquals(strpos($result, 'http://moodle.org/littleschema'), 128);
        $this->assertEquals(strpos($result, 'Hello World'), 160);
        $this->assertFalse(strpos($result, $xw->get_default_prologue()));

        // Try to close one tag in wrong order
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->start();
        $xw->begin_tag('first');
        $xw->begin_tag('second');
        try {
            $xw->end_tag('first');
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'xml_writer_end_tag_no_match');
        }

        // Try to close one tag before starting any tag
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->start();
        try {
            $xw->end_tag('first');
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'xml_writer_end_tag_no_match');
        }

        // Full tag without contents (null and empty string)
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->set_prologue(''); // empty prologue for easier matching
        $xw->start();
        $xw->full_tag('tagname', null, array('attrname' => 'attrvalue'));
        $xw->full_tag('tagname2', '', array('attrname' => 'attrvalue'));
        $xw->stop();
        $result = $xo->get_allcontents();
        $this->assertEquals($result, '<tagname attrname="attrvalue" /><tagname2 attrname="attrvalue"></tagname2>');


        // Test case-folding is working
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo, null, true);
        $xw->set_prologue(''); // empty prologue for easier matching
        $xw->start();
        $xw->full_tag('tagname', 'textcontent', array('attrname' => 'attrvalue'));
        $xw->stop();
        $result = $xo->get_allcontents();
        $this->assertEquals($result, '<TAGNAME ATTRNAME="attrvalue">textcontent</TAGNAME>');

        // Test UTF-8 chars in tag and attribute names, attr values and contents
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->set_prologue(''); // empty prologue for easier matching
        $xw->start();
        $xw->full_tag('áéíóú', 'ÁÉÍÓÚ', array('àèìòù' => 'ÀÈÌÒÙ'));
        $xw->stop();
        $result = $xo->get_allcontents();
        $this->assertEquals($result, '<áéíóú àèìòù="ÀÈÌÒÙ">ÁÉÍÓÚ</áéíóú>');

        // Try non-safe content in attributes
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->set_prologue(''); // empty prologue for easier matching
        $xw->start();
        $xw->full_tag('tagname', 'textcontent', array('attrname' => 'attr' . chr(27) . '\'"value'));
        $xw->stop();
        $result = $xo->get_allcontents();
        $this->assertEquals($result, '<tagname attrname="attr\'&quot;value">textcontent</tagname>');

        // Try non-safe content in text
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->set_prologue(''); // empty prologue for easier matching
        $xw->start();
        $xw->full_tag('tagname', "text\r\ncontent\rwith" . chr(27), array('attrname' => 'attrvalue'));
        $xw->stop();
        $result = $xo->get_allcontents();
        $this->assertEquals($result, '<tagname attrname="attrvalue">text' . "\ncontent\n" . 'with</tagname>');

        // Try to stop the writer without clossing all the open tags
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->start();
        $xw->begin_tag('first');
        try {
            $xw->stop();
            $this->assertTrue(false, 'xml_writer_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof xml_writer_exception);
            $this->assertEquals($e->errorcode, 'xml_writer_open_tags_remaining');
        }

        // Test simple transformer
        $xo = new memory_xml_output();
        $xt = new mock_xml_contenttransformer();
        $xw = new mock_xml_writer($xo, $xt);
        $xw->set_prologue(''); // empty prologue for easier matching
        $xw->start();
        $xw->full_tag('tagname', null, array('attrname' => 'attrvalue'));
        $xw->full_tag('tagname2', 'somecontent', array('attrname' => 'attrvalue'));
        $xw->stop();
        $result = $xo->get_allcontents();
        $this->assertEquals($result, '<tagname attrname="attrvalue" /><tagname2 attrname="attrvalue">testsomecontent</tagname2>');

        // Test nullcontent reset.
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->set_prologue('');
        $xw->start();
        $xw->full_tag('tagname', null);
        $xw->begin_tag('tagname2');
        $xw->full_tag('tagname3', 'somecontent');
        $xw->end_tag('tagname2');
        $xw->stop();
        $result = $xo->get_allcontents();
        $expected = <<<XML
        <tagname /><tagname2>
          <tagname3>somecontent</tagname3>
        </tagname2>
        XML;
        $this->assertEquals($expected, $result);

        // Build a complex XML file and test results against stored file in fixtures
        $xo = new memory_xml_output();
        $xw = new mock_xml_writer($xo);
        $xw->start();
        $xw->begin_tag('toptag', array('name' => 'toptag', 'level' => 1, 'path' => '/toptag'));
        $xw->full_tag('secondtag', 'secondvalue', array('name' => 'secondtag', 'level' => 2, 'path' => '/toptag/secondtag', 'value' => 'secondvalue'));
        $xw->begin_tag('thirdtag', array('name' => 'thirdtag', 'level' => 2, 'path' => '/toptag/thirdtag'));
        $xw->full_tag('onevalue', 'onevalue', array('name' => 'onevalue', 'level' => 3, 'path' => '/toptag/thirdtag/onevalue'));
        $xw->full_tag('onevalue', 'anothervalue', array('name' => 'onevalue', 'level' => 3, 'value' => 'anothervalue'));
        $xw->full_tag('onevalue', 'yetanothervalue', array('name' => 'onevalue', 'level' => 3, 'value' => 'yetanothervalue'));
        $xw->full_tag('twovalue', 'twovalue', array('name' => 'twovalue', 'level' => 3, 'path' => '/toptag/thirdtag/twovalue'));
        $xw->begin_tag('forthtag', array('name' => 'forthtag', 'level' => 3, 'path' => '/toptag/thirdtag/forthtag'));
        $xw->full_tag('innervalue', 'innervalue');
        $xw->begin_tag('innertag');
        $xw->begin_tag('superinnertag', array('name' => 'superinnertag', 'level' => 5));
        $xw->full_tag('superinnervalue', 'superinnervalue', array('name' => 'superinnervalue', 'level' => 6));
        $xw->end_tag('superinnertag');
        $xw->end_tag('innertag');
        $xw->end_tag('forthtag');
        $xw->begin_tag('fifthtag', array('level' => 3));
        $xw->begin_tag('sixthtag', array('level' => 4));
        $xw->full_tag('seventh', 'seventh', array('level' => 5));
        $xw->end_tag('sixthtag');
        $xw->end_tag('fifthtag');
        $xw->full_tag('finalvalue', 'finalvalue', array('name' => 'finalvalue', 'level' => 3, 'path' => '/toptag/thirdtag/finalvalue'));
        $xw->full_tag('finalvalue');
        $xw->end_tag('thirdtag');
        $xw->end_tag('toptag');
        $xw->stop();
        $result = $xo->get_allcontents();
        $fcontents = file_get_contents($CFG->dirroot . '/backup/util/xml/tests/fixtures/test1.xml');

        // Normalise carriage return characters.
        $fcontents = phpunit_util::normalise_line_endings($fcontents);
        $this->assertEquals(trim($result), trim($fcontents));
    }
}

/*
 * helper extended xml_writer class that makes some methods public for testing
 */
class mock_xml_writer extends xml_writer {
    public function get_default_prologue() {
        return parent::get_default_prologue();
    }
}

/*
 * helper extended xml_contenttransformer prepending "test" to all the notnull contents
 */
class mock_xml_contenttransformer extends xml_contenttransformer {
    public function process($content) {
        return is_null($content) ? null : 'test' . $content;
    }
}
