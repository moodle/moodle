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
                'rating', 'scale'));

    public function setUp() {
        parent::setUp();

        $this->switch_to_test_db(); // Switch to test DB for all the execution

        foreach ($this->testtables as $dir => $tables) {
            $this->create_test_tables($tables, $dir); // Create tables
        }
    }

    /**
     * Test the current get_ratings method main sql
     */
    function test_get_ratings_sql() {

        // We load 6 ratings, 2 performed by each (1, 2, 3) user to items corresponding to the other users
        $ctxid = SYSCONTEXTID;
        $this->load_test_data('rating',
                array('contextid', 'component', 'ratingarea', 'itemid', 'scaleid', 'rating', 'userid', 'timecreated', 'timemodified'), array(
                array(    $ctxid , 'mod_forum',       'post',       2 ,       10 ,       1 ,       1 ,            1 ,              1),
                array(    $ctxid , 'mod_forum',       'post',       3 ,       10 ,       3 ,       1 ,            1 ,              1),
                array(    $ctxid , 'mod_forum',       'post',       1 ,       10 ,       1 ,       2 ,            1 ,              1),
                array(    $ctxid , 'mod_forum',       'post',       3 ,       10 ,       5 ,       2 ,            1 ,              1),
                array(    $ctxid , 'mod_forum',       'post',       1 ,       10 ,       3 ,       3 ,            1 ,              1),
                array(    $ctxid , 'mod_forum',       'post',       2 ,       10 ,       5 ,       3 ,            1 ,              1)));

        // Create 1 post (item) by user 1 (rated above by user 2 and 3 with average = 2)
        $user1posts = array(
                (object)array('id' => 1, 'userid' => 1, 'message' => 'hello'));
        // Create 1 post (item) by user 2 (rated above by user 1 and 3 with average = 3)
        $user2posts = array(
                (object)array('id' => 2, 'userid' => 2, 'message' => 'world'));
        // Create 1 post (item) by user 3 (rated above by user 1 and 2 with average = 4)
        $user3posts = array(
                (object)array('id' => 3, 'userid' => 3, 'message' => 'moodle'));

        // Prepare the default options
        $defaultoptions = array (
                'context'    => get_context_instance(CONTEXT_SYSTEM),
                'component'  => 'mod_forum',
                'ratingarea' => 'post',
                'scaleid'    => 10,
                'aggregate'  => RATING_AGGREGATE_AVERAGE);

        $rm = new rating_manager();

        // STEP 1: Aparently, agreggation and counts are always returned ok

        // Get results for items of user 1 (expected average 1 + 3 / 2 = 2)
        $toptions = (object)array_merge($defaultoptions, array('items' => $user1posts));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($user1posts));
        $this->assertEqual($result[0]->id, $user1posts[0]->id);
        $this->assertEqual($result[0]->userid, $user1posts[0]->userid);
        $this->assertEqual($result[0]->message, $user1posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 2);

        // Get results for items of user 2 (expected average 1 + 5 / 2 = 3)
        $toptions = (object)array_merge($defaultoptions, array('items' => $user2posts));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($user2posts));
        $this->assertEqual($result[0]->id, $user2posts[0]->id);
        $this->assertEqual($result[0]->userid, $user2posts[0]->userid);
        $this->assertEqual($result[0]->message, $user2posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 3);

        // Get results for items of user 3 (expected average 3 + 5 / 2 = 4)
        $toptions = (object)array_merge($defaultoptions, array('items' => $user3posts));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($user3posts));
        $this->assertEqual($result[0]->id, $user3posts[0]->id);
        $this->assertEqual($result[0]->userid, $user3posts[0]->userid);
        $this->assertEqual($result[0]->message, $user3posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 4);

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

        $this->assertEqual($result[1]->id, $posts[1]->id);
        $this->assertEqual($result[1]->userid, $posts[1]->userid);
        $this->assertEqual($result[1]->message, $posts[1]->message);
        $this->assertEqual($result[1]->rating->count, 2);
        $this->assertEqual($result[1]->rating->aggregate, 3);

        // STEP 2: But what happens if only ratings performed by one user are requested? Do we expect
        //         complete aggregations and counts?

        // Get results for items of user 1 rated by user 2 (averages and counts must be the complete ones?)
        $toptions = (object)array_merge($defaultoptions, array('items' => $user1posts, 'userid' => 2));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($user1posts));
        $this->assertEqual($result[0]->id, $user1posts[0]->id);
        $this->assertEqual($result[0]->userid, $user1posts[0]->userid);
        $this->assertEqual($result[0]->message, $user1posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 2);
        $this->assertEqual($result[0]->rating->userid, $toptions->userid); // Must be the passed userid

        // Get results for items of user 1 rated by user 3 (averages and counts must be the complete ones?)
        $toptions = (object)array_merge($defaultoptions, array('items' => $user1posts, 'userid' => 3));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($user1posts));
        $this->assertEqual($result[0]->id, $user1posts[0]->id);
        $this->assertEqual($result[0]->userid, $user1posts[0]->userid);
        $this->assertEqual($result[0]->message, $user1posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 2);
        $this->assertEqual($result[0]->rating->userid, $toptions->userid); // Must be the passed userid

        // Get results for items of user 1 & 2 together rated by user 3 (averages and counts must be the complete ones?)
        $posts = array_merge($user1posts, $user2posts);
        $toptions = (object)array_merge($defaultoptions, array('items' => $posts, 'userid' => 3));
        $result = $rm->get_ratings($toptions);
        $this->assertEqual(count($result), count($posts));
        $this->assertEqual($result[0]->id, $posts[0]->id);
        $this->assertEqual($result[0]->userid, $posts[0]->userid);
        $this->assertEqual($result[0]->message, $posts[0]->message);
        $this->assertEqual($result[0]->rating->count, 2);
        $this->assertEqual($result[0]->rating->aggregate, 2);
        $this->assertEqual($result[0]->rating->userid, $toptions->userid); // Must be the passed userid

        $this->assertEqual($result[1]->id, $posts[1]->id);
        $this->assertEqual($result[1]->userid, $posts[1]->userid);
        $this->assertEqual($result[1]->message, $posts[1]->message);
        $this->assertEqual($result[1]->rating->count, 2);
        $this->assertEqual($result[1]->rating->aggregate, 3);
        $this->assertEqual($result[1]->rating->userid, $toptions->userid); // Must be the passed userid

        // STEP 3: But there are a lot of information that shouldn't be there at all, these shouldn't fail

        // Get results for items of user 1 (expected average 1 + 3 / 2 = 2)
        // (we have tested this above, but now we are going to look to other variables)
        $toptions = (object)array_merge($defaultoptions, array('items' => $user1posts));
        $result = $rm->get_ratings($toptions);
        // This test fails! Let's name it T1
        $this->assertNull($result[0]->rating->userid); // There are 2 users rating this, no way we are returning "randomly" one here
        // Same for rating->[scaleid, permissions, rating, id.... all them are not unique in this example (there are 2 ratings) so we should nullify them
        // or they will be failing here

        // Get results for items of user 2 (expected average 1 + 5 / 2 = 3)
        // (we have tested this above, but now we are going to look to other variables)
        $toptions = (object)array_merge($defaultoptions, array('items' => $user2posts));
        $result = $rm->get_ratings($toptions);
        // This test pass! It's exactly the same than T1 above, so random results are returned!!
        $this->assertNull($result[0]->rating->userid); // There are 2 users rating this, no way we are returning "randomly" one here
        // Same for rating->[scaleid, permissions, rating, id.... all them are not unique in this example (there are 2 ratings) so we should nullify them
        // or they will be failing here
    }
}
