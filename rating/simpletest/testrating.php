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
 * Unit tests for rating/lib.php
 *
 * @package    moodlecore
 * @subpackage rating
 * @copyright  2011 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
require_once($CFG->dirroot . '/rating/lib.php');

/**
 * Unit test case for all the rating/lib.php requiring DB mockup & manipulation
 */
class rating_db_test extends UnitTestCaseUsingDatabase {

    public static $includecoverage = array(
        'rating/lib.php'
    );

    protected $testtables = array(
            'lib' => array(
                'rating', 'scale', 'context', 'capabilities', 'role_assignments', 'role_capabilities', 'course'));

    protected $syscontext;
    protected $neededcaps = array('view', 'viewall', 'viewany', 'rate');
    protected $originaldefaultfrontpageroleid;

    public function setUp() {
        global $CFG;
        parent::setUp();

        // Make sure accesslib has cached a sensible system context object
        // before we switch to the test DB.
        $this->syscontext = get_context_instance(CONTEXT_SYSTEM);

        foreach ($this->testtables as $dir => $tables) {
            $this->create_test_tables($tables, $dir); // Create tables
        }

        $this->switch_to_test_db(); // Switch to test DB for all the execution

        $this->fill_records();

        // Ignore any frontpageroleid, that would require to crete more contexts
        $this->originaldefaultfrontpageroleid = $CFG->defaultfrontpageroleid;
        $CFG->defaultfrontpageroleid = null;
    }

    public function tearDown() {
        global $CFG;
        // Recover original frontpageroleid
        $CFG->defaultfrontpageroleid = $this->originaldefaultfrontpageroleid;
        parent::tearDown();
    }

    private function fill_records() {
        global $DB;

        // Set up systcontext in the test database.
        $this->syscontext->id = $this->testdb->insert_record('context', $this->syscontext);

        // Add the capabilities used by ratings
        foreach ($this->neededcaps as $neededcap) {
            $this->testdb->insert_record('capabilities', (object)array('name' => 'moodle/rating:' . $neededcap,
                                                                 'contextlevel' => CONTEXT_COURSE));
        }
    }

    /**
     * Test the current get_ratings method main sql
     */
    function test_get_ratings_sql() {

        // We load 3 items. Each is rated twice. For simplicity itemid == user id of the item owner
        $ctxid = $this->syscontext->id;
        $this->load_test_data('rating',
                array('contextid', 'component', 'ratingarea', 'itemid', 'scaleid', 'rating', 'userid', 'timecreated', 'timemodified'), array(

                //user 1's items. Average == 2
                array(    $ctxid , 'mod_forum',       'post',       1 ,       10 ,       1 ,       2 ,            1 ,              1),
                array(    $ctxid , 'mod_forum',       'post',       1 ,       10 ,       3 ,       3 ,            1 ,              1),

                //user 2's items. Average == 3
                array(    $ctxid , 'mod_forum',       'post',       2 ,       10 ,       1 ,       1 ,            1 ,              1),
                array(    $ctxid , 'mod_forum',       'post',       2 ,       10 ,       5 ,       3 ,            1 ,              1),

                //user 3's items. Average == 4
                array(    $ctxid , 'mod_forum',       'post',       3 ,       10 ,       3 ,       1 ,            1 ,              1),
                array(    $ctxid , 'mod_forum',       'post',       3 ,       10 ,       5 ,       2 ,            1 ,              1)
                ));

        // a post (item) by user 1 (rated above by user 2 and 3 with average = 2)
        $user1posts = array(
                (object)array('id' => 1, 'userid' => 1, 'message' => 'hello'));
        // a post (item) by user 2 (rated above by user 1 and 3 with average = 3)
        $user2posts = array(
                (object)array('id' => 2, 'userid' => 2, 'message' => 'world'));
        // a post (item) by user 3 (rated above by user 1 and 2 with average = 4)
        $user3posts = array(
                (object)array('id' => 3, 'userid' => 3, 'message' => 'moodle'));

        // Prepare the default options
        $defaultoptions = array (
                'context'    => $this->syscontext,
                'component'  => 'mod_forum',
                'ratingarea' => 'post',
                'scaleid'    => 10,
                'aggregate'  => RATING_AGGREGATE_AVERAGE);

        $rm = new mockup_rating_manager();

        // STEP 1: Retreive ratings using the current user

        // Get results for user 1's item (expected average 1 + 3 / 2 = 2)
        $toptions = (object)array_merge($defaultoptions, array('items' => $user1posts));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($user1posts));
        $this->assertEqual($result[0]->id, $user1posts[0]->id);
        $this->assertEqual($result[0]->userid, $user1posts[0]->userid);
        $this->assertEqual($result[0]->message, $user1posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 2);
        // Note that $result[0]->rating->rating is somewhat random
        // We didn't supply a user ID so $USER was used which will vary depending on who runs the tests

        // Get results for items of user 2 (expected average 1 + 5 / 2 = 3)
        $toptions = (object)array_merge($defaultoptions, array('items' => $user2posts));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($user2posts));
        $this->assertEqual($result[0]->id, $user2posts[0]->id);
        $this->assertEqual($result[0]->userid, $user2posts[0]->userid);
        $this->assertEqual($result[0]->message, $user2posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 3);
        // Note that $result[0]->rating->rating is somewhat random
        // We didn't supply a user ID so $USER was used which will vary depending on who runs the tests

        // Get results for items of user 3 (expected average 3 + 5 / 2 = 4)
        $toptions = (object)array_merge($defaultoptions, array('items' => $user3posts));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($user3posts));
        $this->assertEqual($result[0]->id, $user3posts[0]->id);
        $this->assertEqual($result[0]->userid, $user3posts[0]->userid);
        $this->assertEqual($result[0]->message, $user3posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 4);
        // Note that $result[0]->rating->rating is somewhat random
        // We didn't supply a user ID so $USER was used which will vary depending on who runs the tests

        // Get results for items of user 1 & 2 together (expected averages are 2 and 3, as tested above)
        $posts = array_merge($user1posts, $user2posts);
        $toptions = (object)array_merge($defaultoptions, array('items' => $posts));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($posts));
        $this->assertEqual($result[0]->id, $posts[0]->id);
        $this->assertEqual($result[0]->userid, $posts[0]->userid);
        $this->assertEqual($result[0]->message, $posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 2);
        // Note that $result[0]->rating->rating is somewhat random
        // We didn't supply a user ID so $USER was used which will vary depending on who runs the tests

        $this->assertEqual($result[1]->id, $posts[1]->id);
        $this->assertEqual($result[1]->userid, $posts[1]->userid);
        $this->assertEqual($result[1]->message, $posts[1]->message);
        $this->assertEqual($result[1]->rating->count, 2);
        $this->assertEqual($result[1]->rating->aggregate, 3);
        // Note that $result[0]->rating->rating is somewhat random
        // We didn't supply a user ID so $USER was used which will vary depending on who runs the tests

        // STEP 2: Retrieve ratings by a specified user
        //         We still expect complete aggregations and counts

        // Get results for items of user 1 rated by user 2 (avg 2, rating 1)
        $toptions = (object)array_merge($defaultoptions, array('items' => $user1posts, 'userid' => 2));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($user1posts));
        $this->assertEqual($result[0]->id, $user1posts[0]->id);
        $this->assertEqual($result[0]->userid, $user1posts[0]->userid);
        $this->assertEqual($result[0]->message, $user1posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 2);
        $this->assertEqual($result[0]->rating->rating, 1); //user 2 rated user 1 "1"
        $this->assertEqual($result[0]->rating->userid, $toptions->userid); // Must be the passed userid

        // Get results for items of user 1 rated by user 3
        $toptions = (object)array_merge($defaultoptions, array('items' => $user1posts, 'userid' => 3));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($user1posts));
        $this->assertEqual($result[0]->id, $user1posts[0]->id);
        $this->assertEqual($result[0]->userid, $user1posts[0]->userid);
        $this->assertEqual($result[0]->message, $user1posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 2);
        $this->assertEqual($result[0]->rating->rating, 3); //user 3 rated user 1 "3"
        $this->assertEqual($result[0]->rating->userid, $toptions->userid); // Must be the passed userid

        // Get results for items of user 1 & 2 together rated by user 3
        $posts = array_merge($user1posts, $user2posts);
        $toptions = (object)array_merge($defaultoptions, array('items' => $posts, 'userid' => 3));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($posts));
        $this->assertEqual($result[0]->id, $posts[0]->id);
        $this->assertEqual($result[0]->userid, $posts[0]->userid);
        $this->assertEqual($result[0]->message, $posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 2);
        $this->assertEqual($result[0]->rating->rating, 3); //user 3 rated user 1 "3"
        $this->assertEqual($result[0]->rating->userid, $toptions->userid); // Must be the passed userid

        $this->assertEqual($result[1]->id, $posts[1]->id);
        $this->assertEqual($result[1]->userid, $posts[1]->userid);
        $this->assertEqual($result[1]->message, $posts[1]->message);
        $this->assertEqual($result[1]->rating->count, 2);
        $this->assertEqual($result[1]->rating->aggregate, 3);
        $this->assertEqual($result[0]->rating->rating, 3); //user 3 rated user 2 "5"
        $this->assertEqual($result[1]->rating->userid, $toptions->userid); // Must be the passed userid

        // STEP 3: Some special cases

        // Get results for user 1's items (expected average 1 + 3 / 2 = 2)
        // supplying a non-existent user id so no rating from that user should be found
        $toptions = (object)array_merge($defaultoptions, array('items' => $user1posts));
        $toptions->userid = 123456; //non-existent user
        $result = $rm->get_ratings($toptions);
        $this->assertNull($result[0]->rating->userid);
        $this->assertNull($result[0]->rating->rating);
        $this->assertEqual($result[0]->rating->aggregate, 2);//should still get the aggregate

        // Get results for items of user 2 (expected average 1 + 5 / 2 = 3)
        // Supplying the user id of the user who owns the items so no rating should be found
        $toptions = (object)array_merge($defaultoptions, array('items' => $user2posts));
        $toptions->userid = 2; //user 2 viewing the ratings of their own item
        $result = $rm->get_ratings($toptions);
        //these should be null as the user is viewing their own item and thus cannot rate
        $this->assertNull($result[0]->rating->userid);
        $this->assertNull($result[0]->rating->rating);
        $this->assertEqual($result[0]->rating->aggregate, 3);//should still get the aggregate
    }
}

/**
 * rating_manager subclass for unit testing without requiring capabilities to be loaded
 */
class mockup_rating_manager extends rating_manager {

    /**
     * Overwrite get_plugin_permissions_array() so it always return granted perms for unit testing
     */
    public function get_plugin_permissions_array($contextid, $component, $ratingarea) {
        return array(
            'rate' => true,
            'view' => true,
            'viewany' => true,
            'viewall' => true);
    }

}
