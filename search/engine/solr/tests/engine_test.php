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

        set_config('hostname', TEST_SEARCH_SOLR_HOSTNAME, 'search_solr');
        set_config('port', TEST_SEARCH_SOLR_PORT, 'search_solr');
        set_config('indexname', TEST_SEARCH_SOLR_INDEXNAME, 'search_solr');

        if (defined('TEST_SEARCH_SOLR_USERNAME')) {
            set_config('server_username', TEST_SEARCH_SOLR_USERNAME);
        }

        if (defined('TEST_SEARCH_SOLR_PASSWORD')) {
            set_config('server_password', TEST_SEARCH_SOLR_PASSWORD);
        }

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

        // Testing filters we don't purge cache in between assertions because cache key depends on the whole filters set
        // and they are different.
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
    }

    public function test_delete() {
        $this->search->index();

        $querydata = new stdClass();
        $querydata->q = 'message';

        $this->assertCount(2, $this->search->search($querydata));

        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'role_capabilities');
        $this->search->delete_index($areaid);
        cache_helper::purge_by_definition('core', 'search_results');
        $this->assertCount(0, $this->search->search($querydata));
    }
}
