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
 * @package     core_search
 * @category    phpunit
 * @copyright   2015 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
require_once($CFG->dirroot . '/search/tests/fixtures/mock_search_area.php');

/**
 * Solr search engine base unit tests.
 *
 * @package     core_search
 * @category    phpunit
 * @copyright   2015 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_solr_engine_testcase extends advanced_testcase {

    /**
     * @var \core_search::manager
     */
    protected $search = null;

    public function setUp() {
        $this->resetAfterTest();
        set_config('enableglobalsearch', true);

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

        // Inject search solr engine into the testable core search as we need to add the mock
        // search component to it.
        $searchengine = new \search_solr\engine();
        $this->search = testable_core_search::instance($searchengine);
        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'role_capabilities');
        $this->search->add_search_area($areaid, new core_mocksearch\search\role_capabilities());

        $this->setAdminUser();

        // Cleanup before doing anything on it as the index it is out of this test control.
        $this->search->delete_index();

        // Add moodle fields if they don't exist.
        $schema = new \search_solr\schema();
        $schema->setup(false);
    }

    public function test_connection() {
        $this->assertTrue($this->search->get_engine()->is_server_ready());
    }

    public function test_index() {
        global $DB;

        $noneditingteacherid = $DB->get_field('role', 'id', array('shortname' => 'teacher'));

        // Data gets into the search engine.
        $this->assertTrue($this->search->index());

        // Not anymore as everything was already added.
        sleep(1);
        $this->assertFalse($this->search->index());

        assign_capability('moodle/course:renameroles', CAP_ALLOW, $noneditingteacherid, context_system::instance()->id);
        accesslib_clear_all_caches_for_unit_testing();

        // Indexing again once there is new data.
        $this->assertTrue($this->search->index());
    }

    /**
     * Better keep this not very strict about which or how many results are returned as may depend on solr engine config.
     *
     * @return void
     */
    public function test_search() {
        global $USER, $DB;

        $noneditingteacherid = $DB->get_field('role', 'id', array('shortname' => 'teacher'));

        $this->search->index();

        $querydata = new stdClass();
        $querydata->q = 'message';
        $results = $this->search->search($querydata);
        $this->assertCount(2, $results);

        // Based on core_mocksearch\search\indexer.
        $this->assertEquals($USER->id, $results[0]->get('userid'));
        $this->assertEquals(\context_system::instance()->id, $results[0]->get('contextid'));

        // Do a test to make sure we aren't searching non-query fields, like areaid.
        $querydata->q = \core_search\manager::generate_areaid('core_mocksearch', 'role_capabilities');
        $this->assertCount(0, $this->search->search($querydata));
        $querydata->q = 'message';

        sleep(1);
        $beforeadding = time();
        sleep(1);
        assign_capability('moodle/course:renameroles', CAP_ALLOW, $noneditingteacherid, context_system::instance()->id);
        accesslib_clear_all_caches_for_unit_testing();
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
        $querydata->title = 'moodle/course:renameroles roleid 1';
        $this->assertCount(1, $this->search->search($querydata));

        // Check that index contents get updated.
        $DB->delete_records('role_capabilities', array('capability' => 'moodle/course:renameroles'));
        $this->search->index(true);
        unset($querydata->title);
        $querydata->q = '*renameroles*';
        $this->assertCount(0, $this->search->search($querydata));
    }

    public function test_delete() {
        $this->search->index();

        $querydata = new stdClass();
        $querydata->q = 'message';

        $this->assertCount(2, $this->search->search($querydata));

        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'role_capabilities');
        $this->search->delete_index($areaid);
        $this->assertCount(0, $this->search->search($querydata));
    }

    public function test_alloweduserid() {
        $engine = $this->search->get_engine();
        $area = new core_mocksearch\search\role_capabilities();

        // Get the first record for the recordset.
        $recordset = $area->get_recordset_by_timestamp();
        foreach ($recordset as $r) {
            $record = $r;
            break;
        }
        $recordset->close();

        // Get the doc and insert the default doc.
        $doc = $area->get_document($record);
        $engine->add_document($doc);

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
            $engine->add_document($doc);
        }

        $engine->area_index_complete($area->get_area_id());

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

    public function test_highlight() {
        global $PAGE;

        $this->search->index();

        $querydata = new stdClass();
        $querydata->q = 'message';

        $results = $this->search->search($querydata);
        $this->assertCount(2, $results);

        $result = reset($results);

        $regex = '|'.\search_solr\engine::HIGHLIGHT_START.'message'.\search_solr\engine::HIGHLIGHT_END.'|';
        $this->assertRegExp($regex, $result->get('content'));

        $searchrenderer = $PAGE->get_renderer('core_search');
        $exported = $result->export_for_template($searchrenderer);

        $regex = '|<span class="highlight">message</span>|';
        $this->assertRegExp($regex, $exported['content']);
    }

    public function test_index_file() {
        // Very simple test.
        $this->search->index();
        $querydata = new stdClass();
        $querydata->q = '"File contents"';

        $this->assertCount(2, $this->search->search($querydata));
    }

    public function test_reindexing_files() {
        // Get engine and area to work with.
        $engine = $this->search->get_engine();
        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'role_capabilities');
        $area = \core_search\manager::get_search_area($areaid);

        // Get a single record to make a doc from.
        $recordset = $area->get_recordset_by_timestamp(0);
        $record = $recordset->current();
        $recordset->close();

        $doc = $area->get_document($record);

        // Now we are going to make some files.
        $fs = get_file_storage();
        $syscontext = \context_system::instance();

        $files = array();
        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'core',
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/',
        );

        // We make enough so that we pass the 500 files threashold. That is the boundary when getting files.
        $boundary = 500;
        $top = (int)($boundary * 1.1);
        for ($i = 0; $i < $top; $i++) {
            $filerecord['filename']  = 'searchfile'.$i;
            $file = $fs->create_file_from_string($filerecord, 'Some FileContents'.$i);
            $doc->add_stored_file($file);
            $files[] = $file;
        }

        // Add the doc with lots of files, then commit.
        $engine->add_document($doc, true);
        $engine->area_index_complete($area->get_area_id());

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
        $filerecord['filename']  = 'searchfileNew';
        $files[] = $fs->create_file_from_string($filerecord, 'Some FileContentsNew');
        $checkfiles['New'] = 1;

        $doc = $area->get_document($record);
        foreach($files as $file) {
            $doc->add_stored_file($file);
        }

        // Reindex the document with the changed files.
        $engine->add_document($doc, true);
        $engine->area_index_complete($area->get_area_id());

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

    public function test_index_filtered_file() {
        // Get engine and area to work with.
        $engine = $this->search->get_engine();
        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'role_capabilities');
        $area = \core_search\manager::get_search_area($areaid);

        // Get a single record to make a doc from.
        $recordset = $area->get_recordset_by_timestamp(0);
        $record = $recordset->current();
        $recordset->close();

        $doc = $area->get_document($record);

        // Now we are going to make some files.
        $fs = get_file_storage();
        $syscontext = \context_system::instance();

        $files = array();
        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'core',
            'filearea'  => 'unittest',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => 'largefile'
        );

        // We need to make a file greater than 1kB in size, which is the lowest filter size.
        $contents = 'Some LargeFindContent to find.';
        for ($i = 0; $i < 200; $i++) {
            $contents .= ' The quick brown fox jumps over the lazy dog.';
        }

        $this->assertGreaterThan(1024, strlen($contents));

        $file = $fs->create_file_from_string($filerecord, $contents);
        $doc->add_stored_file($file);

        $filerecord['filename'] = 'smallfile';
        $file = $fs->create_file_from_string($filerecord, 'Some SmallFindContent to find.');
        $doc->add_stored_file($file);

        $engine->add_document($doc, true);
        $engine->area_index_complete($area->get_area_id());

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
        $this->search->index();
        $engine = $this->search->get_engine();

        $querydata = new stdClass();

        // Then search to make sure they are there.
        $querydata->q = '"File contents"';
        $results = $this->search->search($querydata);
        $this->assertCount(2, $results);

        $first = reset($results);
        $deleteid = $first->get('id');

        $engine->delete_by_id($deleteid);

        // Check that we don't get a result for it anymore.
        $results = $this->search->search($querydata);
        $this->assertCount(1, $results);
        $result = reset($results);
        $this->assertNotEquals($deleteid, $result->get('id'));
    }
}
