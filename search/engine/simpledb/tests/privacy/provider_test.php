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
 * Unit tests for privacy.
 *
 * @package   search_simpledb
 * @copyright 2018 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace search_simpledb\privacy;

use search_simpledb\privacy\provider;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
require_once($CFG->dirroot . '/search/tests/fixtures/mock_search_area.php');

/**
 * Unit tests for privacy.
 *
 * @package   search_simpledb
 * @copyright 2018 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    /** @var \search_simpledb\engine database engine. */
    protected $engine;

    /** @var \testable_core_search core search class adapted to unit test. */
    protected $search;

    /** @var \core_search_generator core search generator class adapted to unit test. */
    protected $generator = null;

    /** @var \stdClass course record.*/
    protected $c1;

    /** @var \stdClass course record.*/
    protected $c2;

    /** @var \context_course context instance. */
    protected $c1context;

    /** @var \context_course context instance. */
    protected $c2context;

    /** @var \stdClass user record. */
    protected $u1;

    /** @var \stdClass user record. */
    protected $u2;

    public function setUp(): void {
        global $DB;
        parent::setUp();

        if ($this->requires_manual_index_update()) {
            // We need to update fulltext index manually, which requires an alter table statement.
            $this->preventResetByRollback();
        }

        $this->resetAfterTest();
        set_config('enableglobalsearch', true);

        // Inject search_simpledb engine into the testable core search as we need to add the mock
        // search component to it.

        $this->engine = new \search_simpledb\engine();
        $this->search = \testable_core_search::instance($this->engine);
        $areaid = \core_search\manager::generate_areaid('core_mocksearch', 'mock_search_area');
        $this->search->add_search_area($areaid, new \core_mocksearch\search\mock_search_area());

        $this->generator = self::getDataGenerator()->get_plugin_generator('core_search');
        $this->generator->setup();

        $this->c1 = $this->getDataGenerator()->create_course();
        $this->c2 = $this->getDataGenerator()->create_course();

        $this->c1context = \context_course::instance($this->c1->id);
        $this->c2context = \context_course::instance($this->c2->id);

        $this->u1 = $this->getDataGenerator()->create_user();
        $this->u2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($this->u1->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u1->id, $this->c2->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u2->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u2->id, $this->c2->id, 'student');

        $record = (object)[
            'userid' => $this->u1->id,
            'contextid' => $this->c1context->id,
            'title' => 'vi',
            'content' => 'va',
            'description1' => 'san',
            'description2' => 'jose'
        ];
        $this->generator->create_record($record);
        $this->generator->create_record((object)['userid' => $this->u1->id, 'contextid' => $this->c2context->id]);
        $this->generator->create_record((object)['userid' => $this->u2->id, 'contextid' => $this->c2context->id]);
        $this->generator->create_record((object)['userid' => $this->u2->id, 'contextid' => $this->c1context->id]);
        $this->generator->create_record((object)['owneruserid' => $this->u1->id, 'contextid' => $this->c1context->id]);
        $this->generator->create_record((object)['owneruserid' => $this->u1->id, 'contextid' => $this->c2context->id]);
        $this->generator->create_record((object)['owneruserid' => $this->u2->id, 'contextid' => $this->c1context->id]);
        $this->generator->create_record((object)['owneruserid' => $this->u2->id, 'contextid' => $this->c2context->id]);
        $this->search->index();

        $this->setAdminUser();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void {
        // Call parent tearDown() first.
        parent::tearDown();

        // For unit tests before PHP 7, teardown is called even on skip. So only do our teardown if we did setup.
        if ($this->generator) {
            // Moodle DML freaks out if we don't teardown the temp table after each run.
            $this->generator->teardown();
            $this->generator = null;
        }
    }

    /**
     * Test fetching contexts for a given user ID.
     */
    public function test_get_contexts_for_userid(): void {
        // Ensure both contexts are found for both users.
        $expected = [$this->c1context->id, $this->c2context->id];
        sort($expected);

        // User 1.
        $contextlist = provider::get_contexts_for_userid($this->u1->id);
        $this->assertCount(2, $contextlist);

        $actual = $contextlist->get_contextids();
        sort($actual);
        $this->assertEquals($expected, $actual);

        // User 2.
        $contextlist = provider::get_contexts_for_userid($this->u2->id);
        $this->assertCount(2, $contextlist);

        $actual = $contextlist->get_contextids();
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test fetching user IDs for a given context.
     */
    public function test_get_users_in_context(): void {
        $component = 'search_simpledb';

        // Ensure both users are found for both contexts.
        $expected = [$this->u1->id, $this->u2->id];
        sort($expected);

        // User 1.
        $userlist = new \core_privacy\local\request\userlist($this->c1context, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(2, $userlist);

        $actual = $userlist->get_userids();
        sort($actual);
        $this->assertEquals($expected, $actual);

        // User 2.
        $userlist = new \core_privacy\local\request\userlist($this->c2context, $component);
        provider::get_users_in_context($userlist);
        $this->assertCount(2, $userlist);

        $actual = $userlist->get_userids();
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test export user data.
     *
     * @return null
     */
    public function test_export_user_data(): void {
        global $DB;

        $contextlist = new \core_privacy\local\request\approved_contextlist($this->u1, 'search_simpledb',
                                                                            [$this->c1context->id]);
        provider::export_user_data($contextlist);
        $writer = \core_privacy\local\request\writer::with_context($this->c1context);
        $this->assertTrue($writer->has_any_data());
        $u1c1 = $DB->get_record('search_simpledb_index', ['userid' => $this->u1->id, 'contextid' => $this->c1context->id]);
        $data = $writer->get_data([get_string('search', 'search'), $u1c1->docid]);

        $this->assertEquals($this->c1context->get_context_name(true, true), $data->context);
        $this->assertEquals('vi', $data->title);
        $this->assertEquals('va', $data->content);
        $this->assertEquals('san', $data->description1);
        $this->assertEquals('jose', $data->description2);
    }

    /**
     * Test delete search for context.
     *
     * @return null
     */
    public function test_delete_data_for_all_users(): void {
        global $DB;

        $this->assertEquals(8, $DB->count_records('search_simpledb_index'));

        provider::delete_data_for_all_users_in_context($this->c1context);
        $this->assertEquals(0, $DB->count_records('search_simpledb_index', ['contextid' => $this->c1context->id]));
        $this->assertEquals(4, $DB->count_records('search_simpledb_index'));

        $u2context = \context_user::instance($this->u2->id);
        provider::delete_data_for_all_users_in_context($u2context);
        $this->assertEquals(0, $DB->count_records('search_simpledb_index', ['contextid' => $u2context->id]));
        $this->assertEquals(2, $DB->count_records('search_simpledb_index'));
    }

    /**
     * Test delete search for user.
     *
     * @return null
     */
    public function test_delete_data_for_user(): void {
        global $DB;

        $contextlist = new \core_privacy\local\request\approved_contextlist($this->u1, 'search_simpledb',
                                                                            [$this->c1context->id]);
        provider::delete_data_for_user($contextlist);
        $select = 'contextid = :contextid AND (owneruserid = :owneruserid OR userid = :userid)';
        $params = ['contextid' => $this->c1context->id, 'owneruserid' => $this->u1->id, 'userid' => $this->u1->id];
        $this->assertEquals(0, $DB->count_records_select('search_simpledb_index', $select, $params));
        $this->assertEquals(2, $DB->count_records('search_simpledb_index', ['contextid' => $this->c1context->id]));
        $this->assertEquals(6, $DB->count_records('search_simpledb_index'));

        $contextlist = new \core_privacy\local\request\approved_contextlist($this->u2, 'search_simpledb',
                                                                            [$this->c2context->id]);
        provider::delete_data_for_user($contextlist);
        $select = 'contextid = :contextid AND (owneruserid = :owneruserid OR userid = :userid)';
        $params = ['contextid' => $this->c2context->id, 'owneruserid' => $this->u2->id, 'userid' => $this->u2->id];
        $this->assertEquals(0, $DB->count_records_select('search_simpledb_index', $select, $params));
        $this->assertEquals(2, $DB->count_records('search_simpledb_index', ['contextid' => $this->c2context->id]));
        $this->assertEquals(4, $DB->count_records('search_simpledb_index'));
    }

    /**
     * Test deleting data for an approved userlist.
     */
    public function test_delete_data_for_users(): void {
        global $DB;
        $component = 'search_simpledb';
        $select = 'contextid = :contextid AND (owneruserid = :owneruserid OR userid = :userid)';

        // Ensure expected amount of data for both users exists in each context.
        $this->assertEquals(4, $DB->count_records('search_simpledb_index', ['contextid' => $this->c1context->id]));
        $this->assertEquals(4, $DB->count_records('search_simpledb_index', ['contextid' => $this->c2context->id]));

        // Delete user 1's data in context 1.
        $approveduserids = [$this->u1->id];
        $approvedlist = new \core_privacy\local\request\approved_userlist($this->c1context, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        $params = ['contextid' => $this->c1context->id, 'owneruserid' => $this->u1->id, 'userid' => $this->u1->id];
        $this->assertEquals(0, $DB->count_records_select('search_simpledb_index', $select, $params));

        // Ensure user 2's data in context 1 is retained.
        $params = ['contextid' => $this->c1context->id, 'owneruserid' => $this->u2->id, 'userid' => $this->u2->id];
        $this->assertEquals(2, $DB->count_records_select('search_simpledb_index', $select, $params));

        // Ensure both users' data in context 2 is retained.
        $params = ['contextid' => $this->c2context->id, 'owneruserid' => $this->u1->id, 'userid' => $this->u1->id];
        $this->assertEquals(2, $DB->count_records_select('search_simpledb_index', $select, $params));
        $params = ['contextid' => $this->c2context->id, 'owneruserid' => $this->u2->id, 'userid' => $this->u2->id];
        $this->assertEquals(2, $DB->count_records_select('search_simpledb_index', $select, $params));
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
