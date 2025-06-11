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

namespace search_simpledb;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
require_once($CFG->dirroot . '/search/tests/fixtures/mock_search_area.php');

/**
 * Simple search engine base unit tests.
 *
 * @package     search_simpledb
 * @category    test
 * @copyright   2016 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class engine_test extends \advanced_testcase {

    /**
     * @var \core_search::manager
     */
    protected $search = null;

    /**
     * @var \
     */
    protected $engine = null;

    /**
     * @var \core_search_generator
     */
    protected $generator = null;

    /**
     * Initial stuff.
     *
     * @return void
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        if ($this->requires_manual_index_update()) {
            // We need to update fulltext index manually, which requires an alter table statement.
            $this->preventResetByRollback();
        }

        set_config('enableglobalsearch', true);

        // Inject search_simpledb engine into the testable core search as we need to add the mock
        // search component to it.

        $this->engine = new \search_simpledb\engine();
        $this->search = \testable_core_search::instance($this->engine);

        $this->generator = self::getDataGenerator()->get_plugin_generator('core_search');
        $this->generator->setup();

        $this->setAdminUser();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void {
        // For unit tests before PHP 7, teardown is called even on skip. So only do our teardown if we did setup.
        if ($this->generator) {
            // Moodle DML freaks out if we don't teardown the temp table after each run.
            $this->generator->teardown();
            $this->generator = null;
        }
        parent::tearDown();
    }

    /**
     * Test indexing process.
     *
     * @return void
     */
    public function test_index(): void {
        global $DB;

        $this->add_mock_search_area();

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
     * Test search filters.
     *
     * @return void
     */
    public function test_search(): void {
        global $USER, $DB;

        $this->add_mock_search_area();

        $this->generator->create_record();
        $record = new \stdClass();
        $record->title = "Special title";
        $this->generator->create_record($record);

        $this->search->index();
        $this->update_index();

        $querydata = new \stdClass();
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
        $this->update_index();

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
        $this->update_index();
        unset($querydata->title);
        $querydata->q = '';
        $this->assertCount(0, $this->search->search($querydata));
    }

    /**
     * Test delete function
     *
     * @return void
     */
    public function test_delete(): void {

        $this->add_mock_search_area();

        $this->generator->create_record();
        $this->generator->create_record();
        $this->search->index();
        $this->update_index();

        $querydata = new \stdClass();
        $querydata->q = 'message';

        $this->assertCount(2, $this->search->search($querydata));

        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'mock_search_area');
        $this->search->delete_index($areaid);
        $this->update_index();
        $this->assertCount(0, $this->search->search($querydata));
    }

    /**
     * Test user is allowed.
     *
     * @return void
     */
    public function test_alloweduserid(): void {

        $this->add_mock_search_area();

        $area = new \core_mocksearch\search\mock_search_area();

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
        $this->update_index();

        $this->engine->area_index_complete($area->get_area_id());

        $querydata = new \stdClass();
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

    public function test_delete_by_id(): void {

        $this->add_mock_search_area();

        $this->generator->create_record();
        $this->generator->create_record();
        $this->search->index();
        $this->update_index();

        $querydata = new \stdClass();

        // Then search to make sure they are there.
        $querydata->q = 'message';
        $results = $this->search->search($querydata);
        $this->assertCount(2, $results);

        $first = reset($results);
        $deleteid = $first->get('id');

        $this->engine->delete_by_id($deleteid);
        $this->update_index();

        // Check that we don't get a result for it anymore.
        $results = $this->search->search($querydata);
        $this->assertCount(1, $results);
        $result = reset($results);
        $this->assertNotEquals($deleteid, $result->get('id'));
    }

    /**
     * Tries out deleting data for a context or a course.
     */
    public function test_deleted_contexts_and_courses(): void {
        // Create some courses and activities.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course(['fullname' => 'C1', 'summary' => 'xyzzy']);
        $course1page1 = $generator->create_module('page', ['course' => $course1, 'name' => 'C1P1', 'content' => 'xyzzy']);
        $generator->create_module('page', ['course' => $course1, 'name' => 'C1P2', 'content' => 'xyzzy']);
        $course2 = $generator->create_course(['fullname' => 'C2', 'summary' => 'xyzzy']);
        $course2page = $generator->create_module('page', ['course' => $course2, 'name' => 'C2P', 'content' => 'xyzzy']);
        $course2pagecontext = \context_module::instance($course2page->cmid);

        $this->search->index();

        // By default we have all data in the index.
        $this->assert_raw_index_contents('xyzzy', ['C1', 'C1P1', 'C1P2', 'C2', 'C2P']);

        // Say we delete the course2pagecontext...
        $this->engine->delete_index_for_context($course2pagecontext->id);
        $this->assert_raw_index_contents('xyzzy', ['C1', 'C1P1', 'C1P2', 'C2']);

        // Now delete the second course...
        $this->engine->delete_index_for_course($course2->id);
        $this->assert_raw_index_contents('xyzzy', ['C1', 'C1P1', 'C1P2']);

        // Finally let's delete using Moodle functions to check that works. Single context first.
        course_delete_module($course1page1->cmid);
        $this->assert_raw_index_contents('xyzzy', ['C1', 'C1P2']);
        delete_course($course1, false);
        $this->assert_raw_index_contents('xyzzy', []);
    }

    /**
     * Check the contents of the index.
     *
     * @param string $searchword Word to match within the content field
     * @param string[] $expected Array of expected result titles, in alphabetical order
     */
    protected function assert_raw_index_contents(string $searchword, array $expected) {
        global $DB;
        $results = $DB->get_records_select('search_simpledb_index',
                $DB->sql_like('content', '?'), ['%' . $searchword . '%'], "id, {$DB->sql_order_by_text('title')}");
        $titles = array_map(function($x) {
            return $x->title;
        }, $results);
        sort($titles);
        $this->assertEquals($expected, $titles);
    }

    /**
     * Adds a mock search area to the search system.
     */
    protected function add_mock_search_area() {
        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'mock_search_area');
        $this->search->add_search_area($areaid, new \core_mocksearch\search\mock_search_area());
    }

    /**
     * Updates mssql fulltext index if necessary.
     *
     * @return bool
     */
    private function update_index() {
        global $DB;

        if (!$this->requires_manual_index_update()) {
            return;
        }

        $DB->execute("ALTER FULLTEXT INDEX ON {search_simpledb_index} START UPDATE POPULATION");

        $catalogname = $DB->get_prefix() . 'search_simpledb_catalog';
        $retries = 0;
        do {
            // 0.2 seconds.
            usleep(200000);

            $record = $DB->get_record_sql("SELECT FULLTEXTCATALOGPROPERTY(cat.name, 'PopulateStatus') AS [PopulateStatus]
                                             FROM sys.fulltext_catalogs AS cat
                                            WHERE cat.name = ?", array($catalogname));
            $retries++;

        } while ($retries < 100 && $record->populatestatus != '0');

        if ($retries === 100) {
            // No update after 20 seconds...
            $this->fail('Sorry, your SQL server fulltext search index is too slow.');
        }
    }

    /**
     * Mssql with fulltext support requires manual updates.
     *
     * @return bool
     */
    private function requires_manual_index_update() {
        global $DB;
        return ($DB->get_dbfamily() === 'mssql' && $DB->is_fulltext_search_supported());
    }
}
