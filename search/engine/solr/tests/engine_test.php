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
 * Solr earch engine base unit tests.
 *
 * Required params:
 * - define('TEST_SEARCH_SOLR_HOSTNAME', '127.0.0.1');
 * - define('TEST_SEARCH_SOLR_PORT', '8983');
 * - define('TEST_SEARCH_SOLR_INDEXNAME', 'unittest');
 *
 * Optional params:
 * - define('TEST_SEARCH_SOLR_USERNAME', '');
 * - define('TEST_SEARCH_SOLR_PASSWORD', '');
 * - define('TEST_SEARCH_SOLR_SSLCERT', '');
 * - define('TEST_SEARCH_SOLR_SSLKEY', '');
 * - define('TEST_SEARCH_SOLR_KEYPASSWORD', '');
 * - define('TEST_SEARCH_SOLR_CAINFOCERT', '');
 *
 * @package     search_solr
 * @category    phpunit
 * @copyright   2015 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
require_once($CFG->dirroot . '/search/tests/fixtures/mock_search_area.php');
require_once($CFG->dirroot . '/search/engine/solr/tests/fixtures/testable_engine.php');

/**
 * Solr search engine base unit tests.
 *
 * @package     search_solr
 * @category    phpunit
 * @copyright   2015 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_solr_engine_testcase extends advanced_testcase {

    /**
     * @var \core_search\manager
     */
    protected $search = null;

    /**
     * @var Instace of core_search_generator.
     */
    protected $generator = null;

    /**
     * @var Instace of testable_engine.
     */
    protected $engine = null;

    public function setUp() {
        $this->resetAfterTest();
        set_config('enableglobalsearch', true);
        set_config('searchengine', 'solr');

        if (!function_exists('solr_get_version')) {
            $this->markTestSkipped('Solr extension is not loaded.');
        }

        if (!defined('TEST_SEARCH_SOLR_HOSTNAME') || !defined('TEST_SEARCH_SOLR_INDEXNAME') ||
                !defined('TEST_SEARCH_SOLR_PORT')) {
            $this->markTestSkipped('Solr extension test server not set.');
        }

        set_config('server_hostname', TEST_SEARCH_SOLR_HOSTNAME, 'search_solr');
        set_config('server_port', TEST_SEARCH_SOLR_PORT, 'search_solr');
        set_config('indexname', TEST_SEARCH_SOLR_INDEXNAME, 'search_solr');

        if (defined('TEST_SEARCH_SOLR_USERNAME')) {
            set_config('server_username', TEST_SEARCH_SOLR_USERNAME, 'search_solr');
        }

        if (defined('TEST_SEARCH_SOLR_PASSWORD')) {
            set_config('server_password', TEST_SEARCH_SOLR_PASSWORD, 'search_solr');
        }

        if (defined('TEST_SEARCH_SOLR_SSLCERT')) {
            set_config('secure', true, 'search_solr');
            set_config('ssl_cert', TEST_SEARCH_SOLR_SSLCERT, 'search_solr');
        }

        if (defined('TEST_SEARCH_SOLR_SSLKEY')) {
            set_config('ssl_key', TEST_SEARCH_SOLR_SSLKEY, 'search_solr');
        }

        if (defined('TEST_SEARCH_SOLR_KEYPASSWORD')) {
            set_config('ssl_keypassword', TEST_SEARCH_SOLR_KEYPASSWORD, 'search_solr');
        }

        if (defined('TEST_SEARCH_SOLR_CAINFOCERT')) {
            set_config('ssl_cainfo', TEST_SEARCH_SOLR_CAINFOCERT, 'search_solr');
        }

        set_config('fileindexing', 1, 'search_solr');

        // We are only test indexing small string files, so setting this as low as we can.
        set_config('maxindexfilekb', 1, 'search_solr');

        $this->generator = self::getDataGenerator()->get_plugin_generator('core_search');
        $this->generator->setup();

        // Inject search solr engine into the testable core search as we need to add the mock
        // search component to it.
        $this->engine = new \search_solr\testable_engine();
        $this->search = testable_core_search::instance($this->engine);
        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'mock_search_area');
        $this->search->add_search_area($areaid, new core_mocksearch\search\mock_search_area());

        $this->setAdminUser();

        // Cleanup before doing anything on it as the index it is out of this test control.
        $this->search->delete_index();

        // Add moodle fields if they don't exist.
        $schema = new \search_solr\schema();
        $schema->setup(false);
    }

    public function tearDown() {
        // For unit tests before PHP 7, teardown is called even on skip. So only do our teardown if we did setup.
        if ($this->generator) {
            // Moodle DML freaks out if we don't teardown the temp table after each run.
            $this->generator->teardown();
            $this->generator = null;
        }
    }

    /**
     * Simple data provider to allow tests to be run with file indexing on and off.
     */
    public function file_indexing_provider() {
        return array(
            'file-indexing-on' => array(1),
            'file-indexing-off' => array(0)
        );
    }

    public function test_connection() {
        $this->assertTrue($this->engine->is_server_ready());
    }

    /**
     * @dataProvider file_indexing_provider
     */
    public function test_index($fileindexing) {
        global $DB;

        $this->engine->test_set_config('fileindexing', $fileindexing);

        $record = new \stdClass();
        $record->timemodified = time() - 1;
        $this->generator->create_record($record);

        // Data gets into the search engine.
        $this->assertTrue($this->search->index());

        // Not anymore as everything was already added.
        sleep(1);
        $this->assertFalse($this->search->index());

        $this->generator->create_record();

        // Indexing again once there is new data.
        $this->assertTrue($this->search->index());
    }

    /**
     * Better keep this not very strict about which or how many results are returned as may depend on solr engine config.
     *
     * @dataProvider file_indexing_provider
     *
     * @return void
     */
    public function test_search($fileindexing) {
        global $USER, $DB;

        $this->engine->test_set_config('fileindexing', $fileindexing);

        $this->generator->create_record();
        $record = new \stdClass();
        $record->title = "Special title";
        $this->generator->create_record($record);

        $this->search->index();

        $querydata = new stdClass();
        $querydata->q = 'message';
        $results = $this->search->search($querydata);
        $this->assertCount(2, $results);

        // Based on core_mocksearch\search\indexer.
        $this->assertEquals($USER->id, $results[0]->get('userid'));
        $this->assertEquals(\context_course::instance(SITEID)->id, $results[0]->get('contextid'));

        // Do a test to make sure we aren't searching non-query fields, like areaid.
        $querydata->q = \core_search\manager::generate_areaid('core_mocksearch', 'mock_search_area');
        $this->assertCount(0, $this->search->search($querydata));
        $querydata->q = 'message';

        sleep(1);
        $beforeadding = time();
        sleep(1);
        $this->generator->create_record();
        $this->search->index();

        // Timestart.
        $querydata->timestart = $beforeadding;
        $this->assertCount(1, $this->search->search($querydata));

        // Timeend.
        unset($querydata->timestart);
        $querydata->timeend = $beforeadding;
        $this->assertCount(2, $this->search->search($querydata));

        // Title.
        unset($querydata->timeend);
        $querydata->title = 'Special title';
        $this->assertCount(1, $this->search->search($querydata));

        // Course IDs.
        unset($querydata->title);
        $querydata->courseids = array(SITEID + 1);
        $this->assertCount(0, $this->search->search($querydata));

        $querydata->courseids = array(SITEID);
        $this->assertCount(3, $this->search->search($querydata));

        // Now try some area-id combinations.
        unset($querydata->courseids);
        $forumpostareaid = \core_search\manager::generate_areaid('mod_forum', 'post');
        $mockareaid = \core_search\manager::generate_areaid('core_mocksearch', 'mock_search_area');

        $querydata->areaids = array($forumpostareaid);
        $this->assertCount(0, $this->search->search($querydata));

        $querydata->areaids = array($forumpostareaid, $mockareaid);
        $this->assertCount(3, $this->search->search($querydata));

        $querydata->areaids = array($mockareaid);
        $this->assertCount(3, $this->search->search($querydata));

        $querydata->areaids = array();
        $this->assertCount(3, $this->search->search($querydata));

        // Check that index contents get updated.
        $this->generator->delete_all();
        $this->search->index(true);
        unset($querydata->title);
        $querydata->q = '*';
        $this->assertCount(0, $this->search->search($querydata));
    }

    /**
     * @dataProvider file_indexing_provider
     */
    public function test_delete($fileindexing) {
        $this->engine->test_set_config('fileindexing', $fileindexing);

        $this->generator->create_record();
        $this->generator->create_record();
        $this->search->index();

        $querydata = new stdClass();
        $querydata->q = 'message';

        $this->assertCount(2, $this->search->search($querydata));

        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'mock_search_area');
        $this->search->delete_index($areaid);
        $this->assertCount(0, $this->search->search($querydata));
    }

    /**
     * @dataProvider file_indexing_provider
     */
    public function test_alloweduserid($fileindexing) {
        $this->engine->test_set_config('fileindexing', $fileindexing);

        $area = new core_mocksearch\search\mock_search_area();

        $record = $this->generator->create_record();

        // Get the doc and insert the default doc.
        $doc = $area->get_document($record);
        $this->engine->add_document($doc);

        $users = array();
        $users[] = $this->getDataGenerator()->create_user();
        $users[] = $this->getDataGenerator()->create_user();
        $users[] = $this->getDataGenerator()->create_user();

        // Add a record that only user 100 can see.
        $originalid = $doc->get('id');

        // Now add a custom doc for each user.
        foreach ($users as $user) {
            $doc = $area->get_document($record);
            $doc->set('id', $originalid.'-'.$user->id);
            $doc->set('owneruserid', $user->id);
            $this->engine->add_document($doc);
        }

        $this->engine->area_index_complete($area->get_area_id());

        $querydata = new stdClass();
        $querydata->q = 'message';
        $querydata->title = $doc->get('title');

        // We are going to go through each user and see if they get the original and the owned doc.
        foreach ($users as $user) {
            $this->setUser($user);

            $results = $this->search->search($querydata);
            $this->assertCount(2, $results);

            $owned = 0;
            $notowned = 0;

            // We don't know what order we will get the results in, so we are doing this.
            foreach ($results as $result) {
                $owneruserid = $result->get('owneruserid');
                if (empty($owneruserid)) {
                    $notowned++;
                    $this->assertEquals(0, $owneruserid);
                    $this->assertEquals($originalid, $result->get('id'));
                } else {
                    $owned++;
                    $this->assertEquals($user->id, $owneruserid);
                    $this->assertEquals($originalid.'-'.$user->id, $result->get('id'));
                }
            }

            $this->assertEquals(1, $owned);
            $this->assertEquals(1, $notowned);
        }

        // Now test a user with no owned results.
        $otheruser = $this->getDataGenerator()->create_user();
        $this->setUser($otheruser);

        $results = $this->search->search($querydata);
        $this->assertCount(1, $results);

        $this->assertEquals(0, $results[0]->get('owneruserid'));
        $this->assertEquals($originalid, $results[0]->get('id'));
    }

    /**
     * @dataProvider file_indexing_provider
     */
    public function test_highlight($fileindexing) {
        global $PAGE;

        $this->engine->test_set_config('fileindexing', $fileindexing);

        $this->generator->create_record();
        $this->search->index();

        $querydata = new stdClass();
        $querydata->q = 'message';

        $results = $this->search->search($querydata);
        $this->assertCount(1, $results);

        $result = reset($results);

        $regex = '|'.\search_solr\engine::HIGHLIGHT_START.'message'.\search_solr\engine::HIGHLIGHT_END.'|';
        $this->assertRegExp($regex, $result->get('content'));

        $searchrenderer = $PAGE->get_renderer('core_search');
        $exported = $result->export_for_template($searchrenderer);

        $regex = '|<span class="highlight">message</span>|';
        $this->assertRegExp($regex, $exported['content']);
    }

    public function test_export_file_for_engine() {
        // Get area to work with.
        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'mock_search_area');
        $area = \core_search\manager::get_search_area($areaid);

        $record = $this->generator->create_record();

        $doc = $area->get_document($record);
        $filerecord = new stdClass();
        $filerecord->timemodified  = 978310800;
        $file = $this->generator->create_file($filerecord);
        $doc->add_stored_file($file);

        $filearray = $doc->export_file_for_engine($file);

        $this->assertEquals(\core_search\manager::TYPE_FILE, $filearray['type']);
        $this->assertEquals($file->get_id(), $filearray['solr_fileid']);
        $this->assertEquals($file->get_contenthash(), $filearray['solr_filecontenthash']);
        $this->assertEquals(\search_solr\document::INDEXED_FILE_TRUE, $filearray['solr_fileindexstatus']);
        $this->assertEquals($file->get_filename(), $filearray['title']);
        $this->assertEquals(978310800, \search_solr\document::import_time_from_engine($filearray['modified']));
    }

    public function test_index_file() {
        // Very simple test.
        $file = $this->generator->create_file();

        $record = new \stdClass();
        $record->attachfileids = array($file->get_id());
        $this->generator->create_record($record);

        $this->search->index();
        $querydata = new stdClass();
        $querydata->q = '"File contents"';

        $this->assertCount(1, $this->search->search($querydata));
    }

    public function test_reindexing_files() {
        // Get area to work with.
        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'mock_search_area');
        $area = \core_search\manager::get_search_area($areaid);

        $record = $this->generator->create_record();

        $doc = $area->get_document($record);

        // Now we are going to make some files.
        $fs = get_file_storage();
        $syscontext = \context_system::instance();

        $files = array();

        $filerecord = new \stdClass();
        // We make enough so that we pass the 500 files threashold. That is the boundary when getting files.
        $boundary = 500;
        $top = (int)($boundary * 1.1);
        for ($i = 0; $i < $top; $i++) {
            $filerecord->filename  = 'searchfile'.$i;
            $filerecord->content = 'Some FileContents'.$i;
            $file = $this->generator->create_file($filerecord);
            $doc->add_stored_file($file);
            $files[] = $file;
        }

        // Add the doc with lots of files, then commit.
        $this->engine->add_document($doc, true);
        $this->engine->area_index_complete($area->get_area_id());

        // Indexes we are going to check. 0 means we will delete, 1 means we will keep.
        $checkfiles = array(
            0 => 0,                        // Check the begining of the set.
            1 => 1,
            2 => 0,
            ($top - 3) => 0,               // Check the end of the set.
            ($top - 2) => 1,
            ($top - 1) => 0,
            ($boundary - 2) => 0,          // Check at the boundary between fetch groups.
            ($boundary - 1) => 0,
            $boundary => 0,
            ($boundary + 1) => 0,
            ((int)($boundary * 0.5)) => 1, // Make sure we keep some middle ones.
            ((int)($boundary * 1.05)) => 1
        );

        $querydata = new stdClass();

        // First, check that all the files are currently there.
        foreach ($checkfiles as $key => $unused) {
            $querydata->q = 'FileContents'.$key;
            $this->assertCount(1, $this->search->search($querydata));
            $querydata->q = 'searchfile'.$key;
            $this->assertCount(1, $this->search->search($querydata));
        }

        // Remove the files we want removed from the files array.
        foreach ($checkfiles as $key => $keep) {
            if (!$keep) {
                unset($files[$key]);
            }
        }

        // And make us a new file to add.
        $filerecord->filename  = 'searchfileNew';
        $filerecord->content  = 'Some FileContentsNew';
        $files[] = $this->generator->create_file($filerecord);
        $checkfiles['New'] = 1;

        $doc = $area->get_document($record);
        foreach($files as $file) {
            $doc->add_stored_file($file);
        }

        // Reindex the document with the changed files.
        $this->engine->add_document($doc, true);
        $this->engine->area_index_complete($area->get_area_id());

        // Go through our check array, and see if the file is there or not.
        foreach ($checkfiles as $key => $keep) {
            $querydata->q = 'FileContents'.$key;
            $this->assertCount($keep, $this->search->search($querydata));
            $querydata->q = 'searchfile'.$key;
            $this->assertCount($keep, $this->search->search($querydata));
        }

        // Now check that we get one result when we search from something in all of them.
        $querydata->q = 'Some';
        $this->assertCount(1, $this->search->search($querydata));
    }

    /**
     * Test indexing a file we don't consider indexable.
     */
    public function test_index_filtered_file() {
        // Get area to work with.
        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'mock_search_area');
        $area = \core_search\manager::get_search_area($areaid);

        // Get a single record to make a doc from.
        $record = $this->generator->create_record();

        $doc = $area->get_document($record);

        // Now we are going to make some files.
        $fs = get_file_storage();
        $syscontext = \context_system::instance();

        // We need to make a file greater than 1kB in size, which is the lowest filter size.
        $filerecord = new \stdClass();
        $filerecord->filename = 'largefile';
        $filerecord->content = 'Some LargeFindContent to find.';
        for ($i = 0; $i < 200; $i++) {
            $filerecord->content .= ' The quick brown fox jumps over the lazy dog.';
        }

        $this->assertGreaterThan(1024, strlen($filerecord->content));

        $file = $this->generator->create_file($filerecord);
        $doc->add_stored_file($file);

        $filerecord->filename = 'smallfile';
        $filerecord->content = 'Some SmallFindContent to find.';
        $file = $this->generator->create_file($filerecord);
        $doc->add_stored_file($file);

        $this->engine->add_document($doc, true);
        $this->engine->area_index_complete($area->get_area_id());

        $querydata = new stdClass();
        // We shouldn't be able to find the large file contents.
        $querydata->q = 'LargeFindContent';
        $this->assertCount(0, $this->search->search($querydata));

        // But we should be able to find the filename.
        $querydata->q = 'largefile';
        $this->assertCount(1, $this->search->search($querydata));

        // We should be able to find the small file contents.
        $querydata->q = 'SmallFindContent';
        $this->assertCount(1, $this->search->search($querydata));

        // And we should be able to find the filename.
        $querydata->q = 'smallfile';
        $this->assertCount(1, $this->search->search($querydata));
    }

    public function test_delete_by_id() {
        // First get files in the index.
        $file = $this->generator->create_file();
        $record = new \stdClass();
        $record->attachfileids = array($file->get_id());
        $this->generator->create_record($record);
        $this->generator->create_record($record);
        $this->search->index();

        $querydata = new stdClass();

        // Then search to make sure they are there.
        $querydata->q = '"File contents"';
        $results = $this->search->search($querydata);
        $this->assertCount(2, $results);

        $first = reset($results);
        $deleteid = $first->get('id');

        $this->engine->delete_by_id($deleteid);

        // Check that we don't get a result for it anymore.
        $results = $this->search->search($querydata);
        $this->assertCount(1, $results);
        $result = reset($results);
        $this->assertNotEquals($deleteid, $result->get('id'));
    }

    /**
     * Test that expected results are returned, even with low check_access success rate.
     *
     * @dataProvider file_indexing_provider
     */
    public function test_solr_filling($fileindexing) {
        $this->engine->test_set_config('fileindexing', $fileindexing);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();

        // We are going to create a bunch of records that user 1 can see with 2 keywords.
        // Then we are going to create a bunch for user 2 with only 1 of the keywords.
        // If user 2 searches for both keywords, solr will return all of the user 1 results, then the user 2 results.
        // This is because the user 1 results will match 2 keywords, while the others will match only 1.

        $record = new \stdClass();

        // First create a bunch of records for user 1 to see.
        $record->denyuserids = array($user2->id);
        $record->content = 'Something1 Something2';
        $maxresults = (int)(\core_search\manager::MAX_RESULTS * .75);
        for ($i = 0; $i < $maxresults; $i++) {
            $this->generator->create_record($record);
        }

        // Then create a bunch of records for user 2 to see.
        $record->denyuserids = array($user1->id);
        $record->content = 'Something1';
        for ($i = 0; $i < $maxresults; $i++) {
            $this->generator->create_record($record);
        }

        $this->search->index();

        // Check that user 1 sees all their results.
        $this->setUser($user1);
        $querydata = new stdClass();
        $querydata->q = 'Something1 Something2';
        $results = $this->search->search($querydata);
        $this->assertCount($maxresults, $results);

        // Check that user 2 will see theirs, even though they may be crouded out.
        $this->setUser($user2);
        $results = $this->search->search($querydata);
        $this->assertCount($maxresults, $results);
    }

    /**
     * Create 40 docs, that will be return from Solr in 10 hidden, 10 visible, 10 hidden, 10 visible if you query for:
     * Something1 Something2 Something3 Something4, with the specified user set.
     */
    protected function setup_user_hidden_docs($user) {
        // These results will come first, and will not be visible by the user.
        $record = new \stdClass();
        $record->denyuserids = array($user->id);
        $record->content = 'Something1 Something2 Something3 Something4';
        for ($i = 0; $i < 10; $i++) {
            $this->generator->create_record($record);
        }

        // These results will come second, and will  be visible by the user.
        unset($record->denyuserids);
        $record->content = 'Something1 Something2 Something3';
        for ($i = 0; $i < 10; $i++) {
            $this->generator->create_record($record);
        }

        // These results will come third, and will not be visible by the user.
        $record->denyuserids = array($user->id);
        $record->content = 'Something1 Something2';
        for ($i = 0; $i < 10; $i++) {
            $this->generator->create_record($record);
        }

        // These results will come fourth, and will be visible by the user.
        unset($record->denyuserids);
        $record->content = 'Something1 ';
        for ($i = 0; $i < 10; $i++) {
            $this->generator->create_record($record);
        }
    }

    /**
     * Test that counts are what we expect.
     *
     * @dataProvider file_indexing_provider
     */
    public function test_get_query_total_count($fileindexing) {
        $this->engine->test_set_config('fileindexing', $fileindexing);

        $user = self::getDataGenerator()->create_user();
        $this->setup_user_hidden_docs($user);
        $this->search->index();

        $this->setUser($user);
        $querydata = new stdClass();
        $querydata->q = 'Something1 Something2 Something3 Something4';

        // In this first set, it should have determined the first 10 of 40 are bad, so there could be up to 30 left.
        $results = $this->engine->execute_query($querydata, (object)['everything' => true], 5);
        $this->assertEquals(30, $this->engine->get_query_total_count());
        $this->assertCount(5, $results);

        // To get to 15, it has to process the first 10 that are bad, 10 that are good, 10 that are bad, then 5 that are good.
        // So we now know 20 are bad out of 40.
        $results = $this->engine->execute_query($querydata, (object)['everything' => true], 15);
        $this->assertEquals(20, $this->engine->get_query_total_count());
        $this->assertCount(15, $results);

        // Try to get more then all, make sure we still see 20 count and 20 returned.
        $results = $this->engine->execute_query($querydata, (object)['everything' => true], 30);
        $this->assertEquals(20, $this->engine->get_query_total_count());
        $this->assertCount(20, $results);
    }

    /**
     * Test that paged results are what we expect.
     *
     * @dataProvider file_indexing_provider
     */
    public function test_manager_paged_search($fileindexing) {
        $this->engine->test_set_config('fileindexing', $fileindexing);

        $user = self::getDataGenerator()->create_user();
        $this->setup_user_hidden_docs($user);
        $this->search->index();

        // Check that user 1 sees all their results.
        $this->setUser($user);
        $querydata = new stdClass();
        $querydata->q = 'Something1 Something2 Something3 Something4';

        // On this first page, it should have determined the first 10 of 40 are bad, so there could be up to 30 left.
        $results = $this->search->paged_search($querydata, 0);
        $this->assertEquals(30, $results->totalcount);
        $this->assertCount(10, $results->results);
        $this->assertEquals(0, $results->actualpage);

        // On the second page, it should have found the next 10 bad ones, so we no know there are only 20 total.
        $results = $this->search->paged_search($querydata, 1);
        $this->assertEquals(20, $results->totalcount);
        $this->assertCount(10, $results->results);
        $this->assertEquals(1, $results->actualpage);

        // Try to get an additional page - we should get back page 1 results, since that is the last page with valid results.
        $results = $this->search->paged_search($querydata, 2);
        $this->assertEquals(20, $results->totalcount);
        $this->assertCount(10, $results->results);
        $this->assertEquals(1, $results->actualpage);
    }

    /**
     * Tests searching for results restricted to context id.
     */
    public function test_context_restriction() {
        // Use real search areas.
        $this->search->clear_static();
        $this->search->add_core_search_areas();

        // Create 2 courses and some forums.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course(['fullname' => 'Course 1', 'summary' => 'xyzzy']);
        $contextc1 = \context_course::instance($course1->id);
        $course1forum1 = $generator->create_module('forum', ['course' => $course1,
                'name' => 'C1F1', 'intro' => 'xyzzy']);
        $contextc1f1 = \context_module::instance($course1forum1->cmid);
        $course1forum2 = $generator->create_module('forum', ['course' => $course1,
                'name' => 'C1F2', 'intro' => 'xyzzy']);
        $contextc1f2 = \context_module::instance($course1forum2->cmid);
        $course2 = $generator->create_course(['fullname' => 'Course 2', 'summary' => 'xyzzy']);
        $contextc2 = \context_course::instance($course1->id);
        $course2forum = $generator->create_module('forum', ['course' => $course2,
                'name' => 'C2F', 'intro' => 'xyzzy']);
        $contextc2f = \context_module::instance($course2forum->cmid);

        // Index the courses and forums.
        $this->search->index();

        // Search as admin user should find everything.
        $querydata = new stdClass();
        $querydata->q = 'xyzzy';
        $results = $this->search->search($querydata);
        $this->assert_result_titles(
                ['Course 1', 'Course 2', 'C1F1', 'C1F2', 'C2F'], $results);

        // Admin user manually restricts results by context id to include one course and one forum.
        $querydata->contextids = [$contextc2f->id, $contextc1->id];
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['Course 1', 'C2F'], $results);

        // Student enrolled in only one course, same restriction, only has the available results.
        $student2 = $generator->create_user();
        $generator->enrol_user($student2->id, $course2->id, 'student');
        $this->setUser($student2);
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['C2F'], $results);

        // Student enrolled in both courses, same restriction, same results as admin.
        $student1 = $generator->create_user();
        $generator->enrol_user($student1->id, $course1->id, 'student');
        $generator->enrol_user($student1->id, $course2->id, 'student');
        $this->setUser($student1);
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['Course 1', 'C2F'], $results);

        // Restrict both course and context.
        $querydata->courseids = [$course2->id];
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['C2F'], $results);
        unset($querydata->courseids);

        // Restrict both area and context.
        $querydata->areaids = ['core_course-course'];
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['Course 1'], $results);

        // Restrict area and context, incompatibly - this has no results (and doesn't do a query).
        $querydata->contextids = [$contextc2f->id];
        $results = $this->search->search($querydata);
        $this->assert_result_titles([], $results);
    }

    /**
     * Tests searching for results in groups, either by specified group ids or based on user
     * access permissions.
     */
    public function test_groups() {
        global $USER;

        // Use real search areas.
        $this->search->clear_static();
        $this->search->add_core_search_areas();

        // Create 2 courses and a selection of forums with different group mode.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course(['fullname' => 'Course 1']);
        $forum1nogroups = $generator->create_module('forum', ['course' => $course1, 'groupmode' => NOGROUPS]);
        $forum1separategroups = $generator->create_module('forum', ['course' => $course1, 'groupmode' => SEPARATEGROUPS]);
        $forum1visiblegroups = $generator->create_module('forum', ['course' => $course1, 'groupmode' => VISIBLEGROUPS]);
        $course2 = $generator->create_course(['fullname' => 'Course 2']);
        $forum2separategroups = $generator->create_module('forum', ['course' => $course2, 'groupmode' => SEPARATEGROUPS]);

        // Create two groups on each course.
        $group1a = $generator->create_group(['courseid' => $course1->id]);
        $group1b = $generator->create_group(['courseid' => $course1->id]);
        $group2a = $generator->create_group(['courseid' => $course2->id]);
        $group2b = $generator->create_group(['courseid' => $course2->id]);

        // Create search records in each activity and (where relevant) in each group.
        $forumgenerator = $generator->get_plugin_generator('mod_forum');
        $forumgenerator->create_discussion(['course' => $course1->id, 'userid' => $USER->id,
                'forum' => $forum1nogroups->id, 'name' => 'F1NG', 'message' => 'xyzzy']);
        $forumgenerator->create_discussion(['course' => $course1->id, 'userid' => $USER->id,
                'forum' => $forum1separategroups->id, 'name' => 'F1SG-A',  'message' => 'xyzzy',
                'groupid' => $group1a->id]);
        $forumgenerator->create_discussion(['course' => $course1->id, 'userid' => $USER->id,
                'forum' => $forum1separategroups->id, 'name' => 'F1SG-B', 'message' => 'xyzzy',
                'groupid' => $group1b->id]);
        $forumgenerator->create_discussion(['course' => $course1->id, 'userid' => $USER->id,
                'forum' => $forum1visiblegroups->id, 'name' => 'F1VG-A', 'message' => 'xyzzy',
                'groupid' => $group1a->id]);
        $forumgenerator->create_discussion(['course' => $course1->id, 'userid' => $USER->id,
                'forum' => $forum1visiblegroups->id, 'name' => 'F1VG-B', 'message' => 'xyzzy',
                'groupid' => $group1b->id]);
        $forumgenerator->create_discussion(['course' => $course2->id, 'userid' => $USER->id,
                'forum' => $forum2separategroups->id, 'name' => 'F2SG-A', 'message' => 'xyzzy',
                'groupid' => $group2a->id]);
        $forumgenerator->create_discussion(['course' => $course2->id, 'userid' => $USER->id,
                'forum' => $forum2separategroups->id, 'name' => 'F2SG-B', 'message' => 'xyzzy',
                'groupid' => $group2b->id]);

        $this->search->index();

        // Search as admin user should find everything.
        $querydata = new stdClass();
        $querydata->q = 'xyzzy';
        $results = $this->search->search($querydata);
        $this->assert_result_titles(
                ['F1NG', 'F1SG-A', 'F1SG-B', 'F1VG-A', 'F1VG-B', 'F2SG-A', 'F2SG-B'], $results);

        // Admin user manually restricts results by groups.
        $querydata->groupids = [$group1b->id, $group2a->id];
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['F1SG-B', 'F1VG-B', 'F2SG-A'], $results);

        // Student enrolled in both courses but no groups.
        $student1 = $generator->create_user();
        $generator->enrol_user($student1->id, $course1->id, 'student');
        $generator->enrol_user($student1->id, $course2->id, 'student');
        $this->setUser($student1);

        unset($querydata->groupids);
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['F1NG', 'F1VG-A', 'F1VG-B'], $results);

        // Student enrolled in both courses and group A in both cases.
        $student2 = $generator->create_user();
        $generator->enrol_user($student2->id, $course1->id, 'student');
        $generator->enrol_user($student2->id, $course2->id, 'student');
        groups_add_member($group1a, $student2);
        groups_add_member($group2a, $student2);
        $this->setUser($student2);

        $results = $this->search->search($querydata);
        $this->assert_result_titles(['F1NG', 'F1SG-A', 'F1VG-A', 'F1VG-B', 'F2SG-A'], $results);

        // Manually restrict results to group B in course 1.
        $querydata->groupids = [$group1b->id];
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['F1VG-B'], $results);

        // Manually restrict results to group A in course 1.
        $querydata->groupids = [$group1a->id];
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['F1SG-A', 'F1VG-A'], $results);

        // Manager enrolled in both courses (has access all groups).
        $manager = $generator->create_user();
        $generator->enrol_user($manager->id, $course1->id, 'manager');
        $generator->enrol_user($manager->id, $course2->id, 'manager');
        $this->setUser($manager);
        unset($querydata->groupids);
        $results = $this->search->search($querydata);
        $this->assert_result_titles(
                ['F1NG', 'F1SG-A', 'F1SG-B', 'F1VG-A', 'F1VG-B', 'F2SG-A', 'F2SG-B'], $results);
    }

    /**
     * Tests searching for results restricted to specific user id(s).
     */
    public function test_user_restriction() {
        // Use real search areas.
        $this->search->clear_static();
        $this->search->add_core_search_areas();

        // Create a course, a forum, and a glossary.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $forum = $generator->create_module('forum', ['course' => $course->id]);
        $glossary = $generator->create_module('glossary', ['course' => $course->id]);

        // Create 3 user accounts, all enrolled as students on the course.
        $user1 = $generator->create_user();
        $user2 = $generator->create_user();
        $user3 = $generator->create_user();
        $generator->enrol_user($user1->id, $course->id, 'student');
        $generator->enrol_user($user2->id, $course->id, 'student');
        $generator->enrol_user($user3->id, $course->id, 'student');

        // All users create a forum discussion.
        $forumgen = $generator->get_plugin_generator('mod_forum');
        $forumgen->create_discussion(['course' => $course->id, 'forum' => $forum->id,
            'userid' => $user1->id, 'name' => 'Post1', 'message' => 'plugh']);
        $forumgen->create_discussion(['course' => $course->id, 'forum' => $forum->id,
                'userid' => $user2->id, 'name' => 'Post2', 'message' => 'plugh']);
        $forumgen->create_discussion(['course' => $course->id, 'forum' => $forum->id,
                'userid' => $user3->id, 'name' => 'Post3', 'message' => 'plugh']);

        // Two of the users create entries in the glossary.
        $glossarygen = $generator->get_plugin_generator('mod_glossary');
        $glossarygen->create_content($glossary, ['concept' => 'Entry1', 'definition' => 'plugh',
                'userid' => $user1->id]);
        $glossarygen->create_content($glossary, ['concept' => 'Entry3', 'definition' => 'plugh',
                'userid' => $user3->id]);

        // Index the data.
        $this->search->index();

        // Search without user restriction should find everything.
        $querydata = new stdClass();
        $querydata->q = 'plugh';
        $results = $this->search->search($querydata);
        $this->assert_result_titles(
                ['Entry1', 'Entry3', 'Post1', 'Post2', 'Post3'], $results);

        // Restriction to user 3 only.
        $querydata->userids = [$user3->id];
        $results = $this->search->search($querydata);
        $this->assert_result_titles(
                ['Entry3', 'Post3'], $results);

        // Restriction to users 1 and 2.
        $querydata->userids = [$user1->id, $user2->id];
        $results = $this->search->search($querydata);
        $this->assert_result_titles(
                ['Entry1', 'Post1', 'Post2'], $results);

        // Restriction to users 1 and 2 combined with context restriction.
        $querydata->contextids = [context_module::instance($glossary->cmid)->id];
        $results = $this->search->search($querydata);
        $this->assert_result_titles(
                ['Entry1'], $results);

        // Restriction to users 1 and 2 combined with area restriction.
        unset($querydata->contextids);
        $querydata->areaids = [\core_search\manager::generate_areaid('mod_forum', 'post')];
        $results = $this->search->search($querydata);
        $this->assert_result_titles(
                ['Post1', 'Post2'], $results);
    }

    /**
     * Tests searching for results containing words in italic text. (This used to fail.)
     */
    public function test_italics() {
        global $USER;

        // Use real search areas.
        $this->search->clear_static();
        $this->search->add_core_search_areas();

        // Create a course and a forum.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $forum = $generator->create_module('forum', ['course' => $course->id]);

        // As admin user, create forum discussions with various words in italics or with underlines.
        $this->setAdminUser();
        $forumgen = $generator->get_plugin_generator('mod_forum');
        $forumgen->create_discussion(['course' => $course->id, 'forum' => $forum->id,
                'userid' => $USER->id, 'name' => 'Post1',
                'message' => '<p>This is a post about <i>frogs</i>.</p>']);
        $forumgen->create_discussion(['course' => $course->id, 'forum' => $forum->id,
                'userid' => $USER->id, 'name' => 'Post2',
                'message' => '<p>This is a post about <i>toads and zombies</i>.</p>']);
        $forumgen->create_discussion(['course' => $course->id, 'forum' => $forum->id,
                'userid' => $USER->id, 'name' => 'Post3',
                'message' => '<p>This is a post about toads_and_zombies.</p>']);
        $forumgen->create_discussion(['course' => $course->id, 'forum' => $forum->id,
                'userid' => $USER->id, 'name' => 'Post4',
                'message' => '<p>This is a post about _leading and trailing_ underlines.</p>']);

        // Index the data.
        $this->search->index();

        // Search for 'frogs' should find the post.
        $querydata = new stdClass();
        $querydata->q = 'frogs';
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['Post1'], $results);

        // Search for 'toads' or 'zombies' should find post 2 (and not 3)...
        $querydata->q = 'toads';
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['Post2'], $results);
        $querydata->q = 'zombies';
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['Post2'], $results);

        // Search for 'toads_and_zombies' should find post 3.
        $querydata->q = 'toads_and_zombies';
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['Post3'], $results);

        // Search for '_leading' or 'trailing_' should find post 4.
        $querydata->q = '_leading';
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['Post4'], $results);
        $querydata->q = 'trailing_';
        $results = $this->search->search($querydata);
        $this->assert_result_titles(['Post4'], $results);
    }

    /**
     * Asserts that the returned documents have the expected titles (regardless of order).
     *
     * @param string[] $expected List of expected document titles
     * @param \core_search\document[] $results List of returned documents
     */
    protected function assert_result_titles(array $expected, array $results) {
        $titles = [];
        foreach ($results as $result) {
            $titles[] = $result->get('title');
        }
        sort($titles);
        sort($expected);
        $this->assertEquals($expected, $titles);
    }

    /**
     * Tests the get_supported_orders function for contexts where we can only use relevance
     * (system, category).
     */
    public function test_get_supported_orders_relevance_only() {
        global $DB;

        // System or category context: relevance only.
        $orders = $this->engine->get_supported_orders(\context_system::instance());
        $this->assertCount(1, $orders);
        $this->assertArrayHasKey('relevance', $orders);

        $categoryid = $DB->get_field_sql('SELECT MIN(id) FROM {course_categories}');
        $orders = $this->engine->get_supported_orders(\context_coursecat::instance($categoryid));
        $this->assertCount(1, $orders);
        $this->assertArrayHasKey('relevance', $orders);
    }

    /**
     * Tests the get_supported_orders function for contexts where we support location as well
     * (course, activity, block).
     */
    public function test_get_supported_orders_relevance_and_location() {
        global $DB;

        // Test with course context.
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['fullname' => 'Frogs']);
        $coursecontext = \context_course::instance($course->id);

        $orders = $this->engine->get_supported_orders($coursecontext);
        $this->assertCount(2, $orders);
        $this->assertArrayHasKey('relevance', $orders);
        $this->assertArrayHasKey('location', $orders);
        $this->assertContains('Course: Frogs', $orders['location']);

        // Test with activity context.
        $page = $generator->create_module('page', ['course' => $course->id, 'name' => 'Toads']);

        $orders = $this->engine->get_supported_orders(\context_module::instance($page->cmid));
        $this->assertCount(2, $orders);
        $this->assertArrayHasKey('relevance', $orders);
        $this->assertArrayHasKey('location', $orders);
        $this->assertContains('Page: Toads', $orders['location']);

        // Test with block context.
        $instance = (object)['blockname' => 'html', 'parentcontextid' => $coursecontext->id,
                'showinsubcontexts' => 0, 'pagetypepattern' => 'course-view-*',
                'defaultweight' => 0, 'timecreated' => 1, 'timemodified' => 1,
                'configdata' => ''];
        $blockid = $DB->insert_record('block_instances', $instance);
        $blockcontext = \context_block::instance($blockid);

        $orders = $this->engine->get_supported_orders($blockcontext);
        $this->assertCount(2, $orders);
        $this->assertArrayHasKey('relevance', $orders);
        $this->assertArrayHasKey('location', $orders);
        $this->assertContains('Block: HTML', $orders['location']);
    }

    /**
     * Tests ordering by relevance vs location.
     */
    public function test_ordering() {
        // Create 2 courses and 2 activities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course(['fullname' => 'Course 1']);
        $course1context = \context_course::instance($course1->id);
        $course1page = $generator->create_module('page', ['course' => $course1]);
        $course1pagecontext = \context_module::instance($course1page->cmid);
        $course2 = $generator->create_course(['fullname' => 'Course 2']);
        $course2context = \context_course::instance($course2->id);
        $course2page = $generator->create_module('page', ['course' => $course2]);
        $course2pagecontext = \context_module::instance($course2page->cmid);

        // Create one search record in each activity and course.
        $this->create_search_record($course1->id, $course1context->id, 'C1', 'Xyzzy');
        $this->create_search_record($course1->id, $course1pagecontext->id, 'C1P', 'Xyzzy');
        $this->create_search_record($course2->id, $course2context->id, 'C2', 'Xyzzy');
        $this->create_search_record($course2->id, $course2pagecontext->id, 'C2P', 'Xyzzy plugh');
        $this->search->index();

        // Default search works by relevance so the one with both words should be top.
        $querydata = new stdClass();
        $querydata->q = 'xyzzy plugh';
        $results = $this->search->search($querydata);
        $this->assertCount(4, $results);
        $this->assertEquals('C2P', $results[0]->get('title'));

        // Same if you explicitly specify relevance.
        $querydata->order = 'relevance';
        $results = $this->search->search($querydata);
        $this->assertEquals('C2P', $results[0]->get('title'));

        // If you specify order by location and you are in C2 or C2P then results are the same.
        $querydata->order = 'location';
        $querydata->context = $course2context;
        $results = $this->search->search($querydata);
        $this->assertEquals('C2P', $results[0]->get('title'));
        $querydata->context = $course2pagecontext;
        $results = $this->search->search($querydata);
        $this->assertEquals('C2P', $results[0]->get('title'));

        // But if you are in C1P then you get different results (C1P first).
        $querydata->context = $course1pagecontext;
        $results = $this->search->search($querydata);
        $this->assertEquals('C1P', $results[0]->get('title'));
    }

    /**
     * Tests with bogus content (that can be entered into Moodle) to see if it crashes.
     */
    public function test_bogus_content() {
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course(['fullname' => 'Course 1']);
        $course1context = \context_course::instance($course1->id);

        // It is possible to enter into a Moodle database content containing these characters,
        // which are Unicode non-characters / byte order marks. If sent to Solr, these cause
        // failures.
        $boguscontent = html_entity_decode('&#xfffe;') . 'frog';
        $this->create_search_record($course1->id, $course1context->id, 'C1', $boguscontent);
        $boguscontent = html_entity_decode('&#xffff;') . 'frog';
        $this->create_search_record($course1->id, $course1context->id, 'C1', $boguscontent);

        // Unicode Standard Version 9.0 - Core Specification, section 23.7, lists 66 non-characters
        // in total. Here are some of them - these work OK for me but it may depend on platform.
        $boguscontent = html_entity_decode('&#xfdd0;') . 'frog';
        $this->create_search_record($course1->id, $course1context->id, 'C1', $boguscontent);
        $boguscontent = html_entity_decode('&#xfdef;') . 'frog';
        $this->create_search_record($course1->id, $course1context->id, 'C1', $boguscontent);
        $boguscontent = html_entity_decode('&#x1fffe;') . 'frog';
        $this->create_search_record($course1->id, $course1context->id, 'C1', $boguscontent);
        $boguscontent = html_entity_decode('&#x10ffff;') . 'frog';
        $this->create_search_record($course1->id, $course1context->id, 'C1', $boguscontent);

        // Do the indexing (this will check it doesn't throw warnings).
        $this->search->index();

        // Confirm that all 6 documents are found in search.
        $querydata = new stdClass();
        $querydata->q = 'frog';
        $results = $this->search->search($querydata);
        $this->assertCount(6, $results);
    }

    /**
     * Adds a record to the mock search area, so that the search engine can find it later.
     *
     * @param int $courseid Course id
     * @param int $contextid Context id
     * @param string $title Title for search index
     * @param string $content Content for search index
     */
    protected function create_search_record($courseid, $contextid, $title, $content) {
        $record = new \stdClass();
        $record->content = $content;
        $record->title = $title;
        $record->courseid = $courseid;
        $record->contextid = $contextid;
        $this->generator->create_record($record);
    }

    /**
     * Tries out deleting data for a context or a course.
     *
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function test_deleted_contexts_and_courses() {
        // Create some courses and activities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course(['fullname' => 'Course 1']);
        $course1context = \context_course::instance($course1->id);
        $course1page1 = $generator->create_module('page', ['course' => $course1]);
        $course1page1context = \context_module::instance($course1page1->cmid);
        $course1page2 = $generator->create_module('page', ['course' => $course1]);
        $course1page2context = \context_module::instance($course1page2->cmid);
        $course2 = $generator->create_course(['fullname' => 'Course 2']);
        $course2context = \context_course::instance($course2->id);
        $course2page = $generator->create_module('page', ['course' => $course2]);
        $course2pagecontext = \context_module::instance($course2page->cmid);

        // Create one search record in each activity and course.
        $this->create_search_record($course1->id, $course1context->id, 'C1', 'Xyzzy');
        $this->create_search_record($course1->id, $course1page1context->id, 'C1P1', 'Xyzzy');
        $this->create_search_record($course1->id, $course1page2context->id, 'C1P2', 'Xyzzy');
        $this->create_search_record($course2->id, $course2context->id, 'C2', 'Xyzzy');
        $this->create_search_record($course2->id, $course2pagecontext->id, 'C2P', 'Xyzzy plugh');
        $this->search->index();

        // By default we have all results.
        $this->assert_raw_solr_query_result('content:xyzzy', ['C1', 'C1P1', 'C1P2', 'C2', 'C2P']);

        // Say we delete the course2pagecontext...
        $this->engine->delete_index_for_context($course2pagecontext->id);
        $this->assert_raw_solr_query_result('content:xyzzy', ['C1', 'C1P1', 'C1P2', 'C2']);

        // Now delete the second course...
        $this->engine->delete_index_for_course($course2->id);
        $this->assert_raw_solr_query_result('content:xyzzy', ['C1', 'C1P1', 'C1P2']);

        // Finally let's delete using Moodle functions to check that works. Single context first.
        course_delete_module($course1page1->cmid);
        $this->assert_raw_solr_query_result('content:xyzzy', ['C1', 'C1P2']);
        delete_course($course1, false);
        $this->assert_raw_solr_query_result('content:xyzzy', []);
    }

    /**
     * Carries out a raw Solr query using the Solr basic query syntax.
     *
     * This is used to test data contained in the index without going through Moodle processing.
     *
     * @param string $q Search query
     * @param string[] $expected Expected titles of results, in alphabetical order
     */
    protected function assert_raw_solr_query_result(string $q, array $expected) {
        $solr = $this->engine->get_search_client_public();
        $query = new SolrQuery($q);
        $results = $solr->query($query)->getResponse()->response->docs;
        if ($results) {
            $titles = array_map(function($x) {
                return $x->title;
            }, $results);
            sort($titles);
        } else {
            $titles = [];
        }
        $this->assertEquals($expected, $titles);
    }
}
