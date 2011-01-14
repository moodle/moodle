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
 * Unit tests for the opaque question type class.
 *
 * @package qtype_opaque
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/type/opaque/questiontype.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');


class qtype_opaque_engine_manager_mock extends qtype_opaque_engine_manager {
    protected $knownengines = array();

    public function add_test_engine($id, $engine) {
        $this->knownengines[$id] = $engine;
    }

    public function load_engine_def($engineid) {
        if (isset($this->knownengines[$engineid])) {
            return $this->knownengines[$engineid];
        } else {
            return format_opaque_error('unrecognisedservertype', $engineid);
        }
    }

    public function save_engine_def($engine) {
        $this->knownengines[] = $engine;
        return end(array_keys($this->knownengines));
    }

    protected function store_opaque_servers($urls, $type, $engineid) {
        // Should not be used, but override to avoid accidental DB writes.
    }

    public function delete_engine_def($engineid) {
        unset($this->knownengines[$engineid]);
        return true;
    }

    protected function get_possibly_matching_engines($engine) {
        return $this->knownengines;
    }
}


/**
 * Unit tests for the opaque question type class.
 *
 * @copyright 2010 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_opaque_test extends UnitTestCase {
    var $qtype;

    public function setUp() {
        $this->qtype = new qtype_opaque();
    }

    public function tearDown() {
        $this->qtype = null;
    }

    public function assert_same_xml($expectedxml, $xml) {
        $this->assertEqual(str_replace("\r\n", "\n", $expectedxml),
                str_replace("\r\n", "\n", $xml));
    }

    public function test_name() {
        $this->assertEqual($this->qtype->name(), 'opaque');
    }

    public function test_can_analyse_responses() {
        $this->assertFalse($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $this->assertNull($this->qtype->get_random_guess_score(null));
    }

    public function test_get_possible_responses() {
        $this->assertEqual(array(), $this->qtype->get_possible_responses(null));
    }

    public function test_xml_import_known_engine() {
        // This relies on the fact that the question_bank only creates one
        // copy of each question type class.
        $manager = new qtype_opaque_engine_manager_mock();
        question_bank::get_qtype('opaque')->set_engine_manager($manager);

        $engine = new stdClass;
        $engine->name = 'A question engine';
        $engine->questionengines = array('http://example.com/');
        $engine->questionbanks = array();
        $engine->passkey = 'secret';
        $manager->add_test_engine(123, $engine);

        $xml = '  <question type="opaque">
    <name>
      <text>An Opaque question</text>
    </name>
    <questiontext format="moodle_auto_format">
      <text></text>
    </questiontext>
    <generalfeedback>
      <text></text>
    </generalfeedback>
    <defaultgrade>3</defaultgrade>
    <penalty>0</penalty>
    <hidden>0</hidden>
    <remoteid>example.question</remoteid>
    <remoteversion>1.0</remoteversion>
    <engine>
      <name>
        <text>Question engine</text>
      </name>
      <passkey>
        <text>secret</text>
      </passkey>
      <qe>
        <text>http://example.com/</text>
      </qe>
    </engine>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->try_importing_using_qtypes(
                $xmldata['question'], null, null, 'opaque');

        $expectedq = new stdClass;
        $expectedq->qtype = 'opaque';
        $expectedq->name = 'An Opaque question';
        $expectedq->questiontext = '';
        $expectedq->questiontextformat = FORMAT_MOODLE;
        $expectedq->generalfeedback = '';
        $expectedq->defaultmark = 3;
        $expectedq->length = 1;
        $expectedq->penalty = 0;

        $expectedq->remoteid = 'example.question';
        $expectedq->remoteversion = '1.0';
        $expectedq->engineid = 123;

        $this->assert(new CheckSpecifiedFieldsExpectation($expectedq), $q);
    }

    public function test_xml_import_unknown_engine() {
        // This relies on the fact that the question_bank only creates one
        // copy of each question type class.
        $manager = new qtype_opaque_engine_manager_mock();
        question_bank::get_qtype('opaque')->set_engine_manager($manager);

        $engine = new stdClass;
        $engine->name = 'A question engine';
        $engine->questionengines = array('http://example.com/qe2', 'http://example.com/qe1');
        $engine->questionbanks = array('http://example.com/qb');
        $engine->passkey = 'secret';

        $xml = '  <question type="opaque">
    <name>
      <text>An Opaque question</text>
    </name>
    <questiontext format="moodle_auto_format">
      <text></text>
    </questiontext>
    <generalfeedback>
      <text></text>
    </generalfeedback>
    <defaultgrade>3</defaultgrade>
    <penalty>0</penalty>
    <hidden>0</hidden>
    <remoteid>example.question</remoteid>
    <remoteversion>1.0</remoteversion>
    <engine>
      <name>
        <text>Question engine</text>
      </name>
      <passkey>
        <text>secret</text>
      </passkey>
      <qe>
        <text>http://example.com/qe1</text>
      </qe>
      <qe>
        <text>http://example.com/qe2</text>
      </qe>
      <qb>
        <text>http://example.com/qb</text>
      </qb>
    </engine>
  </question>';
        $xmldata = xmlize($xml);

        $importer = new qformat_xml();
        $q = $importer->try_importing_using_qtypes(
                $xmldata['question'], null, null, 'opaque');

        $expectedq = new stdClass;
        $expectedq->qtype = 'opaque';
        $expectedq->name = 'An Opaque question';
        $expectedq->questiontext = '';
        $expectedq->questiontextformat = FORMAT_MOODLE;
        $expectedq->generalfeedback = '';
        $expectedq->defaultmark = 3;
        $expectedq->length = 1;
        $expectedq->penalty = 0;

        $expectedq->remoteid = 'example.question';
        $expectedq->remoteversion = '1.0';
        $expectedq->engineid = 0;

        $this->assert(new CheckSpecifiedFieldsExpectation($expectedq), $q);
        $this->assertTrue($manager->is_same_engine(
                $engine, $manager->load_engine_def($q->engineid)));
    }

    public function test_xml_export() {
        // This relies on the fact that the question_bank only creates one
        // copy of each question type class.
        $manager = new qtype_opaque_engine_manager_mock();
        question_bank::get_qtype('opaque')->set_engine_manager($manager);

        $engine = new stdClass;
        $engine->name = 'A question engine';
        $engine->questionengines = array('http://example.com/');
        $engine->questionbanks = array();
        $engine->passkey = 'secret';
        $manager->add_test_engine(123, $engine);

        $qdata = new stdClass;
        $qdata->id = 321;
        $qdata->qtype = 'opaque';
        $qdata->name = 'An Opaque question';
        $qdata->questiontext = '';
        $qdata->questiontextformat = FORMAT_MOODLE;
        $qdata->generalfeedback = '';
        $qdata->defaultmark = 3;
        $qdata->length = 1;
        $qdata->penalty = 0;
        $qdata->hidden = 0;

        $qdata->options->remoteid = 'example.question';
        $qdata->options->remoteversion = '1.0';
        $qdata->options->engineid = 123;

        $exporter = new qformat_xml();
        $xml = $exporter->writequestion($qdata);

        $expectedxml = '<!-- question: 321  -->
  <question type="opaque">
    <name>
      <text>An Opaque question</text>
    </name>
    <questiontext format="moodle_auto_format">
      <text></text>
    </questiontext>
    <generalfeedback>
      <text></text>
    </generalfeedback>
    <defaultgrade>3</defaultgrade>
    <penalty>0</penalty>
    <hidden>0</hidden>
    <remoteid>example.question</remoteid>
    <remoteversion>1.0</remoteversion>
    <engine>
      <name>
        <text>A question engine</text>
      </name>
      <passkey>
        <text>secret</text>
      </passkey>
      <qe>
        <text>http://example.com/</text>
      </qe>
    </engine>
  </question>
';

        $this->assert_same_xml($expectedxml, $xml);
    }
}
