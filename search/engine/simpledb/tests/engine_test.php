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
 * Simple db search engine tests.
 *
 * @package     search_simpledb
 * @category    phpunit
 * @copyright   2016 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
require_once($CFG->dirroot . '/search/tests/fixtures/mock_search_area.php');

/**
 * Simple search engine base unit tests.
 *
 * @package     search_simpledb
 * @category    phpunit
 * @copyright   2016 David Monllao {@link http://www.davidmonllao.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class search_simpledb_engine_testcase extends advanced_testcase {

    /**
     * @var \core_search::manager
     */
    protected $search = null;

    public function setUp() {
        $this->resetAfterTest();
        set_config('enableglobalsearch', true);

        // Inject search_simpledb engine into the testable core search as we need to add the mock
        // search component to it.
        $searchengine = new \search_simpledb\engine();
        $this->search = testable_core_search::instance($searchengine);
        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'role_capabilities');
        $this->search->add_search_area($areaid, new core_mocksearch\search\role_capabilities());
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
     * Test search filters.
     *
     * @return void
     */
    public function test_search() {
        global $USER, $DB;

        $this->setAdminUser();

        $noneditingteacherid = $DB->get_field('role', 'id', array('shortname' => 'teacher'));

        $this->search->index();

        // Check that docid - id is respected.
        $rolecaps = $DB->get_records('role_capabilities', array('capability' => 'moodle/course:renameroles'));
        $rolecap = reset($rolecaps);
        $rolecap->timemodified = time();
        $DB->update_record('role_capabilities', $rolecap);

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
        $this->assertCount(2, $this->search->search($querydata));

        // Timeend.
        unset($querydata->timestart);
        $querydata->timeend = $beforeadding;
        $this->assertCount(1, $this->search->search($querydata));

        // Title.
        unset($querydata->timeend);
        $querydata->title = 'moodle/course:renameroles roleid 1';
        $this->assertCount(1, $this->search->search($querydata));

        // Course IDs.
        unset($querydata->title);
        $querydata->courseids = array(SITEID + 1);
        $this->assertCount(0, $this->search->search($querydata));

        $querydata->courseids = array(SITEID);
        $this->assertCount(3, $this->search->search($querydata));

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

    public function test_delete_by_id() {
        // First get files in the index.
        $this->search->index();
        $engine = $this->search->get_engine();

        $querydata = new stdClass();

        // Then search to make sure they are there.
        $querydata->q = 'moodle/course:renameroles';
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
