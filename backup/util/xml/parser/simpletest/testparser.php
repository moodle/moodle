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
 * @package moodlecore
 * @subpackage backup-tests
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevent direct access to this file
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

// Include all the needed stuff
require_once($CFG->dirroot . '/backup/util/xml/parser/progressive_parser.class.php');
require_once($CFG->dirroot . '/backup/util/xml/parser/processors/progressive_parser_processor.class.php');
require_once($CFG->dirroot . '/backup/util/xml/parser/processors/simplified_parser_processor.class.php');

/*
 * progressive_parser and progressive_parser_processor tests
 */
class progressive_parser_test extends UnitTestCase {

    public static $includecoverage = array('backup/util/xml/parser');
    public static $excludecoverage = array('backup/util/xml/parser/simpletest');

    /*
     * test progressive_parser public methods
     */
    function test_parser_public_api() {
        global $CFG;
        // Instantiate progressive_parser
        $pp = new progressive_parser();
        $this->assertTrue($pp instanceof progressive_parser);
        $pr = new mock_parser_processor();
        $this->assertTrue($pr instanceof progressive_parser_processor);

        // Try to process without processor
        try {
            $pp->process();
            $this->assertTrue(false);
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEqual($e->errorcode, 'undefined_parser_processor');
        }

        // Assign processor to parser
        $pp->set_processor($pr);

        // Try to process without file and contents
        try {
            $pp->process();
            $this->assertTrue(false);
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEqual($e->errorcode, 'undefined_xml_to_parse');
        }

        // Assign *invalid* processor to parser
        try {
            $pp->set_processor(new stdClass());
            $this->assertTrue(false);
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEqual($e->errorcode, 'invalid_parser_processor');
        }

        // Set file from fixtures (test1.xml) and process it
        $pp = new progressive_parser();
        $pr = new mock_parser_processor();
        $pp->set_processor($pr);
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/simpletest/fixtures/test1.xml');
        $pp->process();
        $serfromfile = serialize($pr->get_chunks()); // Get serialized results (to compare later)
        // Set *unexisting* file from fixtures
        try {
            $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/simpletest/fixtures/test0.xml');
            $this->assertTrue(false);
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEqual($e->errorcode, 'invalid_file_to_parse');
        }

        // Set contents from fixtures (test1.xml) and process it
        $pp = new progressive_parser();
        $pr = new mock_parser_processor();
        $pp->set_processor($pr);
        $pp->set_contents(file_get_contents($CFG->dirroot . '/backup/util/xml/parser/simpletest/fixtures/test1.xml'));
        $pp->process();
        $serfrommemory = serialize($pr->get_chunks()); // Get serialized results (to compare later)
        // Set *empty* contents
        try {
            $pp->set_contents('');
            $this->assertTrue(false);
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEqual($e->errorcode, 'invalid_contents_to_parse');
        }

        // Check that both results from file processing and content processing are equal
        $this->assertEqual($serfromfile, $serfrommemory);

        // Check case_folding is working ok
        $pp = new progressive_parser(true);
        $pr = new mock_parser_processor();
        $pp->set_processor($pr);
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/simpletest/fixtures/test1.xml');
        $pp->process();
        $chunks = $pr->get_chunks();
        $this->assertTrue($chunks[0]['path'] === '/FIRSTTAG');
        $this->assertTrue($chunks[0]['tags']['SECONDTAG']['name'] === 'SECONDTAG');
        $this->assertTrue($chunks[0]['tags']['SECONDTAG']['attrs']['NAME'] === 'secondtag');

        // Check invalid XML exception is working ok
        $pp = new progressive_parser(true);
        $pr = new mock_parser_processor();
        $pp->set_processor($pr);
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/simpletest/fixtures/test2.xml');
        try {
            $pp->process();
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEqual($e->errorcode, 'xml_parsing_error');
        }

        // Check double process throws exception
        $pp = new progressive_parser(true);
        $pr = new mock_parser_processor();
        $pp->set_processor($pr);
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/simpletest/fixtures/test1.xml');
        $pp->process();
        try { // Second process, will throw exception
            $pp->process();
            $this->assertTrue(false);
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEqual($e->errorcode, 'progressive_parser_already_used');
        }
    }

    /*
     * test progressive_parser parsing results using testing_parser_processor and test1.xml
     * auto-described file from fixtures
     */
    function test_parser_results() {
        global $CFG;
        // Instantiate progressive_parser
        $pp = new progressive_parser();
        // Instantiate processor, passing the unit test as param
        $pr = new mock_auto_parser_processor($this);
        $this->assertTrue($pr instanceof progressive_parser_processor);
        // Assign processor to parser
        $pp->set_processor($pr);
        // Set file from fixtures
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/simpletest/fixtures/test3.xml');
        // Process the file, the autotest processor will perform a bunch of automatic tests
        $pp->process();
        // Get processor debug info
        $debug = $pr->debug_info();
        $this->assertTrue(is_array($debug));
        $this->assertTrue(array_key_exists('chunks', $debug));
        // Check the number of chunks is correct for the file
        $this->assertEqual($debug['chunks'], 10);
    }

    /*
     * test progressive_parser parsing results using simplified_parser_processor and test4.xml
     * (one simple glossary backup file example)
     */
    function test_simplified_parser_results() {
        global $CFG;
        // Instantiate progressive_parser
        $pp =  new progressive_parser();
        // Instantiate simplified_parser_processor declaring the interesting paths
        $pr = new mock_simplified_parser_processor(array(
            '/activity',
            '/activity/glossary',
            '/activity/glossary/entries/entry',
            '/activity/glossary/entries/entry/aliases/alias',
            '/activity/glossary/entries/entry/ratings/rating',
            '/activity/glossary/categories/category'));
        $this->assertTrue($pr instanceof progressive_parser_processor);
        // Assign processor to parser
        $pp->set_processor($pr);
        // Set file from fixtures
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/simpletest/fixtures/test4.xml');
        // Process the file
        $pp->process();
        // Get processor debug info
        $debug = $pr->debug_info();
        $this->assertTrue(is_array($debug));
        $this->assertTrue(array_key_exists('chunks', $debug));

        // Check the number of chunks is correct for the file
        $this->assertEqual($debug['chunks'], 8);
        // Get all the simplified chunks and perform various validations
        $chunks = $pr->get_chunks();

        // chunk[0] (/activity) tests
        $this->assertEqual(count($chunks[0]), 3);
        $this->assertEqual($chunks[0]['path'], '/activity');
        $this->assertEqual($chunks[0]['level'],'2');
        $tags = $chunks[0]['tags'];
        $this->assertEqual(count($tags), 4);
        $this->assertEqual($tags['id'], 1);
        $this->assertEqual($tags['moduleid'], 5);
        $this->assertEqual($tags['modulename'], 'glossary');
        $this->assertEqual($tags['contextid'], 26);
        $this->assertEqual($chunks[0]['level'],'2');

        // chunk[1] (/activity/glossary) tests
        $this->assertEqual(count($chunks[1]), 3);
        $this->assertEqual($chunks[1]['path'], '/activity/glossary');
        $this->assertEqual($chunks[1]['level'],'3');
        $tags = $chunks[1]['tags'];
        $this->assertEqual(count($tags), 24);
        $this->assertEqual($tags['id'], 1);
        $this->assertEqual($tags['intro'], '<p>One simple glossary to test backup &amp; restore. Here it\'s the standard image:</p>'.
                                           "\n".
                                           '<p><img src="@@PLUGINFILE@@/88_31.png" alt="pwd by moodle" width="88" height="31" /></p>');
        $this->assertEqual($tags['timemodified'], 1275639747);
        $this->assertTrue(!isset($tags['categories']));

        // chunk[5] (second /activity/glossary/entries/entry) tests
        $this->assertEqual(count($chunks[5]), 3);
        $this->assertEqual($chunks[5]['path'], '/activity/glossary/entries/entry');
        $this->assertEqual($chunks[5]['level'],'5');
        $tags = $chunks[5]['tags'];
        $this->assertEqual(count($tags), 15);
        $this->assertEqual($tags['id'], 2);
        $this->assertEqual($tags['concept'], 'cat');
        $this->assertTrue(!isset($tags['aliases']));
        $this->assertTrue(!isset($tags['entries']));

        // chunk[6] (second /activity/glossary/entries/entry/aliases/alias) tests
        $this->assertEqual(count($chunks[6]), 3);
        $this->assertEqual($chunks[6]['path'], '/activity/glossary/entries/entry/aliases/alias');
        $this->assertEqual($chunks[6]['level'],'7');
        $tags = $chunks[6]['tags'];
        $this->assertEqual(count($tags), 2);
        $this->assertEqual($tags['id'], 2);
        $this->assertEqual($tags['alias_text'], 'cats');

        // chunk[7] (second /activity/glossary/entries/entry/ratings/rating) tests
        $this->assertEqual(count($chunks[7]), 3);
        $this->assertEqual($chunks[7]['path'], '/activity/glossary/entries/entry/ratings/rating');
        $this->assertEqual($chunks[7]['level'],'7');
        $tags = $chunks[7]['tags'];
        $this->assertEqual(count($tags), 6);
        $this->assertEqual($tags['id'], 1);
        $this->assertEqual($tags['timemodified'], '1275639779');
    }
}

/*
 * helper processor able to perform various auto-cheks based on attributes while processing
 * the test1.xml file available in the fixtures dir. It performs these checks:
 *    - name equal to "name" attribute of the tag (if present)
 *    - level equal to "level" attribute of the tag (if present)
 *    - path + tagname equal to "path" attribute of the tag (if present)
 *    - cdata, if not empty is:
 *        - equal to "value" attribute of the tag (if present)
 *        - else, equal to tag name
 *
 * We pass the whole UnitTestCase object to the processor in order to be
 * able to perform the tests in the straight in the process
 */
class mock_auto_parser_processor extends progressive_parser_processor {

    private $utc = null; // To store the unit test case

    public function __construct($unit_test_case) {
        parent::__construct();
        $this->utc = $unit_test_case;
    }

    public function process_chunk($data) {
        // Perform auto-checks based in the rules above
        if (isset($data['tags'])) {
            foreach ($data['tags'] as $tag) {
                if (isset($tag['attrs']['name'])) { // name tests
                    $this->utc->assertEqual($tag['name'], $tag['attrs']['name']);
                }
                if (isset($tag['attrs']['level'])) { // level tests
                    $this->utc->assertEqual($data['level'], $tag['attrs']['level']);
                }
                if (isset($tag['attrs']['path'])) { // path tests
                    $this->utc->assertEqual(rtrim($data['path'], '/') . '/' . $tag['name'], $tag['attrs']['path']);
                }
                if (!empty($tag['cdata'])) { // cdata tests
                    if (isset($tag['attrs']['value'])) {
                        $this->utc->assertEqual($tag['cdata'], $tag['attrs']['value']);
                    } else {
                        $this->utc->assertEqual($tag['cdata'], $tag['name']);
                    }
                }
            }
        }
    }
}

/*
 * helper processor that accumulates all the chunks, resturning them with the get_chunks() method
 */
class mock_parser_processor extends progressive_parser_processor {

    private $chunksarr = array(); // To accumulate the found chunks

    public function process_chunk($data) {
        $this->chunksarr[] = $data;
    }

    public function get_chunks() {
        return $this->chunksarr;
    }
}

/*
 * helper processor that accumulates simplified chunks, returning them with the get_chunks() method
 */
class mock_simplified_parser_processor extends simplified_parser_processor {

    private $chunksarr = array(); // To accumulate the found chunks

    public function dispatch_chunk($data) {
        $this->chunksarr[] = $data;
    }

    public function get_chunks() {
        return $this->chunksarr;
    }
}
