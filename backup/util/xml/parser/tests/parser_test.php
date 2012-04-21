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
 * @package   core_backup
 * @category  phpunit
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
global $CFG;
require_once($CFG->dirroot . '/backup/util/xml/parser/progressive_parser.class.php');
require_once($CFG->dirroot . '/backup/util/xml/parser/processors/progressive_parser_processor.class.php');
require_once($CFG->dirroot . '/backup/util/xml/parser/processors/simplified_parser_processor.class.php');
require_once($CFG->dirroot . '/backup/util/xml/parser/processors/grouped_parser_processor.class.php');

/*
 * progressive_parser and progressive_parser_processor tests
 */
class progressive_parser_test extends advanced_testcase {

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
            $this->assertEquals($e->errorcode, 'undefined_parser_processor');
        }

        // Assign processor to parser
        $pp->set_processor($pr);

        // Try to process without file and contents
        try {
            $pp->process();
            $this->assertTrue(false);
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEquals($e->errorcode, 'undefined_xml_to_parse');
        }

        // Assign *invalid* processor to parser
        try {
            $pp->set_processor(new stdClass());
            $this->assertTrue(false);
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEquals($e->errorcode, 'invalid_parser_processor');
        }

        // Set file from fixtures (test1.xml) and process it
        $pp = new progressive_parser();
        $pr = new mock_parser_processor();
        $pp->set_processor($pr);
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/tests/fixtures/test1.xml');
        $pp->process();
        $serfromfile = serialize($pr->get_chunks()); // Get serialized results (to compare later)
        // Set *unexisting* file from fixtures
        try {
            $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/tests/fixtures/test0.xml');
            $this->assertTrue(false);
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEquals($e->errorcode, 'invalid_file_to_parse');
        }

        // Set contents from fixtures (test1.xml) and process it
        $pp = new progressive_parser();
        $pr = new mock_parser_processor();
        $pp->set_processor($pr);
        $pp->set_contents(file_get_contents($CFG->dirroot . '/backup/util/xml/parser/tests/fixtures/test1.xml'));
        $pp->process();
        $serfrommemory = serialize($pr->get_chunks()); // Get serialized results (to compare later)
        // Set *empty* contents
        try {
            $pp->set_contents('');
            $this->assertTrue(false);
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEquals($e->errorcode, 'invalid_contents_to_parse');
        }

        // Check that both results from file processing and content processing are equal
        $this->assertEquals($serfromfile, $serfrommemory);

        // Check case_folding is working ok
        $pp = new progressive_parser(true);
        $pr = new mock_parser_processor();
        $pp->set_processor($pr);
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/tests/fixtures/test1.xml');
        $pp->process();
        $chunks = $pr->get_chunks();
        $this->assertTrue($chunks[0]['path'] === '/FIRSTTAG');
        $this->assertTrue($chunks[0]['tags']['SECONDTAG']['name'] === 'SECONDTAG');
        $this->assertTrue($chunks[0]['tags']['SECONDTAG']['attrs']['NAME'] === 'secondtag');

        // Check invalid XML exception is working ok
        $pp = new progressive_parser(true);
        $pr = new mock_parser_processor();
        $pp->set_processor($pr);
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/tests/fixtures/test2.xml');
        try {
            $pp->process();
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEquals($e->errorcode, 'xml_parsing_error');
        }

        // Check double process throws exception
        $pp = new progressive_parser(true);
        $pr = new mock_parser_processor();
        $pp->set_processor($pr);
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/tests/fixtures/test1.xml');
        $pp->process();
        try { // Second process, will throw exception
            $pp->process();
            $this->assertTrue(false);
        } catch (exception $e) {
            $this->assertTrue($e instanceof progressive_parser_exception);
            $this->assertEquals($e->errorcode, 'progressive_parser_already_used');
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
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/tests/fixtures/test3.xml');
        // Process the file, the autotest processor will perform a bunch of automatic tests
        $pp->process();
        // Get processor debug info
        $debug = $pr->debug_info();
        $this->assertTrue(is_array($debug));
        $this->assertTrue(array_key_exists('chunks', $debug));
        // Check the number of chunks is correct for the file
        $this->assertEquals($debug['chunks'], 10);
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
            '/activity/glossary/categories/category',
            '/activity/glossary/onetest',
            '/activity/glossary/othertest'));
        $this->assertTrue($pr instanceof progressive_parser_processor);
        // Assign processor to parser
        $pp->set_processor($pr);
        // Set file from fixtures
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/tests/fixtures/test4.xml');
        // Process the file
        $pp->process();
        // Get processor debug info
        $debug = $pr->debug_info();
        $this->assertTrue(is_array($debug));
        $this->assertTrue(array_key_exists('chunks', $debug));

        // Check the number of chunks is correct for the file
        $this->assertEquals($debug['chunks'], 12);
        // Get all the simplified chunks and perform various validations
        $chunks = $pr->get_chunks();
        // Check we have received the correct number of chunks
        $this->assertEquals(count($chunks), 12);

        // chunk[0] (/activity) tests
        $this->assertEquals(count($chunks[0]), 3);
        $this->assertEquals($chunks[0]['path'], '/activity');
        $this->assertEquals($chunks[0]['level'],'2');
        $tags = $chunks[0]['tags'];
        $this->assertEquals(count($tags), 4);
        $this->assertEquals($tags['id'], 1);
        $this->assertEquals($tags['moduleid'], 5);
        $this->assertEquals($tags['modulename'], 'glossary');
        $this->assertEquals($tags['contextid'], 26);
        $this->assertEquals($chunks[0]['level'],'2');

        // chunk[1] (/activity/glossary) tests
        $this->assertEquals(count($chunks[1]), 3);
        $this->assertEquals($chunks[1]['path'], '/activity/glossary');
        $this->assertEquals($chunks[1]['level'],'3');
        $tags = $chunks[1]['tags'];
        $this->assertEquals(count($tags), 24);
        $this->assertEquals($tags['id'], 1);
        $this->assertEquals($tags['intro'], '<p>One simple glossary to test backup &amp; restore. Here it\'s the standard image:</p>'.
                                           "\n".
                                           '<p><img src="@@PLUGINFILE@@/88_31.png" alt="pwd by moodle" width="88" height="31" /></p>');
        $this->assertEquals($tags['timemodified'], 1275639747);
        $this->assertTrue(!isset($tags['categories']));

        // chunk[5] (second /activity/glossary/entries/entry) tests
        $this->assertEquals(count($chunks[5]), 3);
        $this->assertEquals($chunks[5]['path'], '/activity/glossary/entries/entry');
        $this->assertEquals($chunks[5]['level'],'5');
        $tags = $chunks[5]['tags'];
        $this->assertEquals(count($tags), 15);
        $this->assertEquals($tags['id'], 2);
        $this->assertEquals($tags['concept'], 'cat');
        $this->assertTrue(!isset($tags['aliases']));
        $this->assertTrue(!isset($tags['entries']));

        // chunk[6] (second /activity/glossary/entries/entry/aliases/alias) tests
        $this->assertEquals(count($chunks[6]), 3);
        $this->assertEquals($chunks[6]['path'], '/activity/glossary/entries/entry/aliases/alias');
        $this->assertEquals($chunks[6]['level'],'7');
        $tags = $chunks[6]['tags'];
        $this->assertEquals(count($tags), 2);
        $this->assertEquals($tags['id'], 2);
        $this->assertEquals($tags['alias_text'], 'cats');

        // chunk[7] (second /activity/glossary/entries/entry/aliases/alias) tests
        $this->assertEquals(count($chunks[7]), 3);
        $this->assertEquals($chunks[7]['path'], '/activity/glossary/entries/entry/aliases/alias');
        $this->assertEquals($chunks[7]['level'],'7');
        $tags = $chunks[7]['tags'];
        $this->assertEquals(count($tags), 2);
        $this->assertEquals($tags['id'], 3);
        $this->assertEquals($tags['alias_text'], 'felines');

        // chunk[8] (second /activity/glossary/entries/entry/ratings/rating) tests
        $this->assertEquals(count($chunks[8]), 3);
        $this->assertEquals($chunks[8]['path'], '/activity/glossary/entries/entry/ratings/rating');
        $this->assertEquals($chunks[8]['level'],'7');
        $tags = $chunks[8]['tags'];
        $this->assertEquals(count($tags), 6);
        $this->assertEquals($tags['id'], 1);
        $this->assertEquals($tags['timemodified'], '1275639779');

        // chunk[9] (first /activity/glossary/onetest) tests
        $this->assertEquals(count($chunks[9]), 3);
        $this->assertEquals($chunks[9]['path'], '/activity/glossary/onetest');
        $this->assertEquals($chunks[9]['level'],'4');
        $tags = $chunks[9]['tags'];
        $this->assertEquals(count($tags), 2);
        $this->assertEquals($tags['name'], 1);
        $this->assertEquals($tags['value'], 1);

        // chunk[10] (second /activity/glossary/onetest) tests
        $this->assertEquals(count($chunks[10]), 3);
        $this->assertEquals($chunks[10]['path'], '/activity/glossary/onetest');
        $this->assertEquals($chunks[10]['level'],'4');
        $tags = $chunks[10]['tags'];
        $this->assertEquals(count($tags), 2);
        $this->assertEquals($tags['name'], 2);
        $this->assertEquals($tags['value'], 2);

        // chunk[11] (first /activity/glossary/othertest) tests
        // note we don't allow repeated "final" element, so we only return the last one
        $this->assertEquals(count($chunks[11]), 3);
        $this->assertEquals($chunks[11]['path'], '/activity/glossary/othertest');
        $this->assertEquals($chunks[11]['level'],'4');
        $tags = $chunks[11]['tags'];
        $this->assertEquals(count($tags), 2);
        $this->assertEquals($tags['name'], 4);
        $this->assertEquals($tags['value'], 5);

        // Now check start notifications
        $snotifs = $pr->get_start_notifications();
        // Check we have received the correct number of notifications
        $this->assertEquals(count($snotifs), 12);
        // Check first, sixth and last notifications
        $this->assertEquals($snotifs[0], '/activity');
        $this->assertEquals($snotifs[5], '/activity/glossary/entries/entry');
        $this->assertEquals($snotifs[11], '/activity/glossary/othertest');

        // Now check end notifications
        $enotifs = $pr->get_end_notifications();
        // Check we have received the correct number of notifications
        $this->assertEquals(count($snotifs), 12);
        // Check first, sixth and last notifications
        $this->assertEquals($enotifs[0], '/activity/glossary/entries/entry/aliases/alias');
        $this->assertEquals($enotifs[5], '/activity/glossary/entries/entry/ratings/rating');
        $this->assertEquals($enotifs[11], '/activity');

        // Check start and end notifications are balanced
        sort($snotifs);
        sort($enotifs);
        $this->assertEquals($snotifs, $enotifs);

        // Now verify that the start/process/end order is correct
        $allnotifs = $pr->get_all_notifications();
        $this->assertEquals(count($allnotifs), count($snotifs) + count($enotifs) + count($chunks)); // The count
        // Check integrity of the notifications
        $errcount = $this->helper_check_notifications_order_integrity($allnotifs);
        $this->assertEquals($errcount, 0); // No errors found, plz
    }

    /**
     * test how the simplified processor and the order of start/process/end events happens
     * with one real fragment of one backup 1.9 file, where some problems
     * were found by David, hence we honor him in the name of the test ;-)
     */
    function test_simplified_david_backup19_file_fragment() {
        global $CFG;
        // Instantiate progressive_parser
        $pp =  new progressive_parser();
        // Instantiate grouped_parser_processor
        $pr = new mock_simplified_parser_processor();
        // Add interesting paths
        $pr->add_path('/MOODLE_BACKUP/COURSE');
        $pr->add_path('/MOODLE_BACKUP/COURSE/SECTIONS/SECTION');
        $pr->add_path('/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD');
        $pr->add_path('/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD/ROLES_OVERRIDES');
        $this->assertTrue($pr instanceof progressive_parser_processor);
        // Assign processor to parser
        $pp->set_processor($pr);
        // Set file from fixtures
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/tests/fixtures/test5.xml');
        // Process the file
        $pp->process();

        // Get all the simplified chunks and perform various validations
        $chunks = $pr->get_chunks();
        $this->assertEquals(count($chunks), 3); // Only 3, because 7 (COURSE, ROLES_OVERRIDES and 5 MOD) are empty, aka no chunk

        // Now check start notifications
        $snotifs = $pr->get_start_notifications();
        // Check we have received the correct number of notifications
        $this->assertEquals(count($snotifs), 10); // Start tags are dispatched for empties (ROLES_OVERRIDES)
        // Check first and last notifications
        $this->assertEquals($snotifs[0], '/MOODLE_BACKUP/COURSE');
        $this->assertEquals($snotifs[1], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION');
        $this->assertEquals($snotifs[2], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD');
        $this->assertEquals($snotifs[3], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD/ROLES_OVERRIDES');
        $this->assertEquals($snotifs[7], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD');
        $this->assertEquals($snotifs[8], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD');
        $this->assertEquals($snotifs[9], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD');

        // Now check end notifications
        $enotifs = $pr->get_end_notifications();
        // Check we have received the correct number of notifications
        $this->assertEquals(count($snotifs), 10); // End tags are dispatched for empties (ROLES_OVERRIDES)
        // Check first, and last notifications
        $this->assertEquals($enotifs[0], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD/ROLES_OVERRIDES');
        $this->assertEquals($enotifs[1], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD');
        $this->assertEquals($enotifs[2], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD');
        $this->assertEquals($enotifs[3], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD');
        $this->assertEquals($enotifs[7], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD');
        $this->assertEquals($enotifs[8], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION');
        $this->assertEquals($enotifs[9], '/MOODLE_BACKUP/COURSE');

        // Check start and end notifications are balanced
        sort($snotifs);
        sort($enotifs);
        $this->assertEquals($snotifs, $enotifs);

        // Now verify that the start/process/end order is correct
        $allnotifs = $pr->get_all_notifications();
        $this->assertEquals(count($allnotifs), count($snotifs) + count($enotifs) + count($chunks)); // The count
        // Check integrity of the notifications
        $errcount = $this->helper_check_notifications_order_integrity($allnotifs);
        $this->assertEquals($errcount, 0); // No errors found, plz
    }

    /*
     * test progressive_parser parsing results using grouped_parser_processor and test4.xml
     * (one simple glossary backup file example)
     */
    function test_grouped_parser_results() {
        global $CFG;
        // Instantiate progressive_parser
        $pp =  new progressive_parser();
        // Instantiate grouped_parser_processor
        $pr = new mock_grouped_parser_processor();
        // Add interesting paths
        $pr->add_path('/activity');
        $pr->add_path('/activity/glossary', true);
        $pr->add_path('/activity/glossary/entries/entry');
        $pr->add_path('/activity/glossary/entries/entry/aliases/alias');
        $pr->add_path('/activity/glossary/entries/entry/ratings/rating');
        $pr->add_path('/activity/glossary/categories/category');
        $pr->add_path('/activity/glossary/onetest');
        $pr->add_path('/activity/glossary/othertest');
        $this->assertTrue($pr instanceof progressive_parser_processor);
        // Assign processor to parser
        $pp->set_processor($pr);
        // Set file from fixtures
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/tests/fixtures/test4.xml');
        // Process the file
        $pp->process();
        // Get processor debug info
        $debug = $pr->debug_info();
        $this->assertTrue(is_array($debug));
        $this->assertTrue(array_key_exists('chunks', $debug));

        // Check the number of chunks is correct for the file
        $this->assertEquals($debug['chunks'], 2);
        // Get all the simplified chunks and perform various validations
        $chunks = $pr->get_chunks();
        // Check we have received the correct number of chunks
        $this->assertEquals(count($chunks), 2);

        // chunk[0] (/activity) tests
        $this->assertEquals(count($chunks[0]), 3);
        $this->assertEquals($chunks[0]['path'], '/activity');
        $this->assertEquals($chunks[0]['level'],'2');
        $tags = $chunks[0]['tags'];
        $this->assertEquals(count($tags), 4);
        $this->assertEquals($tags['id'], 1);
        $this->assertEquals($tags['moduleid'], 5);
        $this->assertEquals($tags['modulename'], 'glossary');
        $this->assertEquals($tags['contextid'], 26);
        $this->assertEquals($chunks[0]['level'],'2');

        // chunk[1] (grouped /activity/glossary tests)
        $this->assertEquals(count($chunks[1]), 3);
        $this->assertEquals($chunks[1]['path'], '/activity/glossary');
        $this->assertEquals($chunks[1]['level'],'3');
        $tags = $chunks[1]['tags'];
        $this->assertEquals(count($tags), 27);
        $this->assertEquals($tags['id'], 1);
        $this->assertEquals($tags['intro'], '<p>One simple glossary to test backup &amp; restore. Here it\'s the standard image:</p>'.
                                           "\n".
                                           '<p><img src="@@PLUGINFILE@@/88_31.png" alt="pwd by moodle" width="88" height="31" /></p>');
        $this->assertEquals($tags['timemodified'], 1275639747);
        $this->assertTrue(!isset($tags['categories']));
        $this->assertTrue(isset($tags['entries']));
        $this->assertTrue(isset($tags['onetest']));
        $this->assertTrue(isset($tags['othertest']));

        // Various tests under the entries
        $entries = $chunks[1]['tags']['entries']['entry'];
        $this->assertEquals(count($entries), 2);

        // First entry
        $entry1 = $entries[0];
        $this->assertEquals(count($entry1), 17);
        $this->assertEquals($entry1['id'], 1);
        $this->assertEquals($entry1['userid'], 2);
        $this->assertEquals($entry1['concept'], 'dog');
        $this->assertEquals($entry1['definition'], '<p>Traditional enemies of cats</p>');
        $this->assertTrue(isset($entry1['aliases']));
        $this->assertTrue(isset($entry1['ratings']));
        // aliases of first entry
        $aliases = $entry1['aliases']['alias'];
        $this->assertEquals(count($aliases), 1);
        // first alias
        $alias1 = $aliases[0];
        $this->assertEquals(count($alias1), 2);
        $this->assertEquals($alias1['id'], 1);
        $this->assertEquals($alias1['alias_text'], 'dogs');
        // ratings of first entry
        $ratings = $entry1['ratings']['rating'];
        $this->assertEquals(count($ratings), 1);
        // first rating
        $rating1 = $ratings[0];
        $this->assertEquals(count($rating1), 6);
        $this->assertEquals($rating1['id'], 2);
        $this->assertEquals($rating1['value'], 6);
        $this->assertEquals($rating1['timemodified'], '1275639797');

        // Second entry
        $entry2 = $entries[1];
        $this->assertEquals(count($entry2), 17);
        $this->assertEquals($entry2['id'], 2);
        $this->assertEquals($entry2['userid'], 2);
        $this->assertEquals($entry2['concept'], 'cat');
        $this->assertEquals($entry2['definition'], '<p>traditional enemies of dogs</p>');
        $this->assertTrue(isset($entry2['aliases']));
        $this->assertTrue(isset($entry2['ratings']));
        // aliases of first entry
        $aliases = $entry2['aliases']['alias'];
        $this->assertEquals(count($aliases), 2);
        // first alias
        $alias1 = $aliases[0];
        $this->assertEquals(count($alias1), 2);
        $this->assertEquals($alias1['id'], 2);
        $this->assertEquals($alias1['alias_text'], 'cats');
        // second alias
        $alias2 = $aliases[1];
        $this->assertEquals(count($alias2), 2);
        $this->assertEquals($alias2['id'], 3);
        $this->assertEquals($alias2['alias_text'], 'felines');
        // ratings of first entry
        $ratings = $entry2['ratings']['rating'];
        $this->assertEquals(count($ratings), 1);
        // first rating
        $rating1 = $ratings[0];
        $this->assertEquals(count($rating1), 6);
        $this->assertEquals($rating1['id'], 1);
        $this->assertEquals($rating1['value'], 5);
        $this->assertEquals($rating1['scaleid'], 10);

        // Onetest test (only 1 level nested)
        $onetest = $tags['onetest'];
        $this->assertEquals(count($onetest), 2);
        $this->assertEquals(count($onetest[0]), 2);
        $this->assertEquals($onetest[0]['name'], 1);
        $this->assertEquals($onetest[0]['value'], 1);
        $this->assertEquals(count($onetest[1]), 2);
        $this->assertEquals($onetest[1]['name'], 2);
        $this->assertEquals($onetest[1]['value'], 2);

        // Other test (0 level nested, only last one is retrieved)
        $othertest = $tags['othertest'];
        $this->assertEquals(count($othertest), 1);
        $this->assertEquals(count($othertest[0]), 2);
        $this->assertEquals($othertest[0]['name'], 4);
        $this->assertEquals($othertest[0]['value'], 5);

        // Now check start notifications
        $snotifs = $pr->get_start_notifications();
        // Check we have received the correct number of notifications
        $this->assertEquals(count($snotifs), 2);
        // Check first and last notifications
        $this->assertEquals($snotifs[0], '/activity');
        $this->assertEquals($snotifs[1], '/activity/glossary');

        // Now check end notifications
        $enotifs = $pr->get_end_notifications();
        // Check we have received the correct number of notifications
        $this->assertEquals(count($snotifs), 2);
        // Check first, and last notifications
        $this->assertEquals($enotifs[0], '/activity/glossary');
        $this->assertEquals($enotifs[1], '/activity');

        // Check start and end notifications are balanced
        sort($snotifs);
        sort($enotifs);
        $this->assertEquals($snotifs, $enotifs);

        // Now verify that the start/process/end order is correct
        $allnotifs = $pr->get_all_notifications();
        $this->assertEquals(count($allnotifs), count($snotifs) + count($enotifs) + count($chunks)); // The count
        // Check integrity of the notifications
        $errcount = $this->helper_check_notifications_order_integrity($allnotifs);
        $this->assertEquals($errcount, 0); // No errors found, plz
    }

    /**
     * test how the grouped processor and the order of start/process/end events happens
     * with one real fragment of one backup 1.9 file, where some problems
     * were found by David, hence we honor him in the name of the test ;-)
     */
    function test_grouped_david_backup19_file_fragment() {
        global $CFG;
        // Instantiate progressive_parser
        $pp =  new progressive_parser();
        // Instantiate grouped_parser_processor
        $pr = new mock_grouped_parser_processor();
        // Add interesting paths
        $pr->add_path('/MOODLE_BACKUP/COURSE');
        $pr->add_path('/MOODLE_BACKUP/COURSE/SECTIONS/SECTION', true);
        $pr->add_path('/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD');
        $pr->add_path('/MOODLE_BACKUP/COURSE/SECTIONS/SECTION/MODS/MOD/ROLES_OVERRIDES');
        $this->assertTrue($pr instanceof progressive_parser_processor);
        // Assign processor to parser
        $pp->set_processor($pr);
        // Set file from fixtures
        $pp->set_file($CFG->dirroot . '/backup/util/xml/parser/tests/fixtures/test5.xml');
        // Process the file
        $pp->process();

        // Get all the simplified chunks and perform various validations
        $chunks = $pr->get_chunks();
        $this->assertEquals(count($chunks), 1); // Only 1, the SECTION one

        // Now check start notifications
        $snotifs = $pr->get_start_notifications();
        // Check we have received the correct number of notifications
        $this->assertEquals(count($snotifs), 2);
        // Check first and last notifications
        $this->assertEquals($snotifs[0], '/MOODLE_BACKUP/COURSE');
        $this->assertEquals($snotifs[1], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION');

        // Now check end notifications
        $enotifs = $pr->get_end_notifications();
        // Check we have received the correct number of notifications
        $this->assertEquals(count($snotifs), 2); // End tags are dispatched for empties (ROLES_OVERRIDES)
        // Check first, and last notifications
        $this->assertEquals($enotifs[0], '/MOODLE_BACKUP/COURSE/SECTIONS/SECTION');
        $this->assertEquals($enotifs[1], '/MOODLE_BACKUP/COURSE');

        // Check start and end notifications are balanced
        sort($snotifs);
        sort($enotifs);
        $this->assertEquals($snotifs, $enotifs);

        // Now verify that the start/process/end order is correct
        $allnotifs = $pr->get_all_notifications();
        $this->assertEquals(count($allnotifs), count($snotifs) + count($enotifs) + count($chunks)); // The count
        // Check integrity of the notifications
        $errcount = $this->helper_check_notifications_order_integrity($allnotifs);
        $this->assertEquals($errcount, 0); // No errors found, plz
    }


    /**
     * Helper function that given one array of ordered start/process/end notifications will
     * check it of integrity like:
     *    - process only happens if start is the previous notification
     *    - end only happens if dispatch is the previous notification
     *    - start only happen with level > than last one and if there is no already started like that
     *
     * @param array $notifications ordered array of notifications with format [start|process|end]:path
     * @return int number of integrity problems found (errors)
     */
    function helper_check_notifications_order_integrity($notifications) {
        $numerrors = 0;
        $notifpile = array('pilebase' => 'start');
        $lastnotif = 'start:pilebase';
        foreach ($notifications as $notif) {

            $lastpiletype = end($notifpile);
            $lastpilepath = key($notifpile);
            $lastpilelevel = strlen(preg_replace('/[^\/]/', '', $lastpilepath));

            $lastnotiftype  = preg_replace('/:.*/', '', $lastnotif);
            $lastnotifpath  = preg_replace('/.*:/', '', $lastnotif);
            $lastnotiflevel = strlen(preg_replace('/[^\/]/', '', $lastnotifpath));

            $notiftype  = preg_replace('/:.*/', '', $notif);
            $notifpath  = preg_replace('/.*:/', '', $notif);
            $notiflevel = strlen(preg_replace('/[^\/]/', '', $notifpath));

            switch ($notiftype) {
                case 'process':
                    if ($lastnotifpath != $notifpath or $lastnotiftype != 'start') {
                        $numerrors++; // Only start for same path from last notification is allowed before process
                    }
                    $notifpile[$notifpath] = 'process'; // Update the status in the pile
                    break;
                case 'end':
                    if ($lastpilepath != $notifpath or ($lastpiletype != 'process' and $lastpiletype != 'start')) {
                        $numerrors++; // Only process and start for same path from last pile is allowed before end
                    }
                    unset($notifpile[$notifpath]); // Delete from the pile
                    break;
                case 'start':
                    if (array_key_exists($notifpath, $notifpile) or $notiflevel <= $lastpilelevel) {
                        $numerrors++; // Only non existing in pile and with level > last pile is allowed on start
                    }
                    $notifpile[$notifpath] = 'start'; // Add to the pile
                    break;
                default:
                    $numerrors++; // Incorrect type of notification => error
            }
            // Update lastnotif
            $lastnotif = $notif;
        }
        return $numerrors;
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
                    $this->utc->assertEquals($tag['name'], $tag['attrs']['name']);
                }
                if (isset($tag['attrs']['level'])) { // level tests
                    $this->utc->assertEquals($data['level'], $tag['attrs']['level']);
                }
                if (isset($tag['attrs']['path'])) { // path tests
                    $this->utc->assertEquals(rtrim($data['path'], '/') . '/' . $tag['name'], $tag['attrs']['path']);
                }
                if (!empty($tag['cdata'])) { // cdata tests
                    if (isset($tag['attrs']['value'])) {
                        $this->utc->assertEquals($tag['cdata'], $tag['attrs']['value']);
                    } else {
                        $this->utc->assertEquals($tag['cdata'], $tag['name']);
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
    private $startarr  = array(); // To accumulate all the notified path starts
    private $endarr    = array(); // To accumulate all the notified path ends
    private $allnotif  = array(); // To accumulate all the notified and dispatched events in an ordered way

    public function dispatch_chunk($data) {
        $this->chunksarr[] = $data;
        $this->allnotif[] = 'process:' . $data['path'];
    }

    public function notify_path_start($path) {
        $this->startarr[] = $path;
        $this->allnotif[] = 'start:' . $path;
    }

    public function notify_path_end($path) {
        $this->endarr[] = $path;
        $this->allnotif[] = 'end:' . $path;
    }

    public function get_chunks() {
        return $this->chunksarr;
    }

    public function get_start_notifications() {
        return $this->startarr;
    }

    public function get_end_notifications() {
        return $this->endarr;
    }

    public function get_all_notifications() {
        return $this->allnotif;
    }
}

/*
 * helper processor that accumulates grouped chunks, returning them with the get_chunks() method
 */
class mock_grouped_parser_processor extends grouped_parser_processor {

    private $chunksarr = array(); // To accumulate the found chunks
    private $startarr  = array(); // To accumulate all the notified path starts
    private $endarr    = array(); // To accumulate all the notified path ends
    private $allnotif  = array(); // To accumulate all the notified and dispatched events in an ordered way

    public function dispatch_chunk($data) {
        $this->chunksarr[] = $data;
        $this->allnotif[] = 'process:' . $data['path'];
    }

    public function notify_path_start($path) {
        $this->startarr[] = $path;
        $this->allnotif[] = 'start:' . $path;
    }

    public function notify_path_end($path) {
        $this->endarr[] = $path;
        $this->allnotif[] = 'end:' . $path;
    }

    public function get_chunks() {
        return $this->chunksarr;
    }

    public function get_start_notifications() {
        return $this->startarr;
    }

    public function get_end_notifications() {
        return $this->endarr;
    }

    public function get_all_notifications() {
        return $this->allnotif;
    }
}
