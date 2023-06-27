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
require_once(__DIR__.'/fixtures/structure_fixtures.php');

global $CFG;
require_once($CFG->dirroot . '/backup/util/xml/output/memory_xml_output.class.php');


/**
 * Unit test case the all the backup structure classes. Note: Uses database
 */
class backup_structure_testcase extends advanced_testcase {

    /** @var int Store the inserted forum->id for use in test functions */
    protected $forumid;
    /** @var int Store the inserted discussion1->id for use in test functions */
    protected $discussionid1;
    /** @var int Store the inserted discussion2->id for use in test functions */
    protected $discussionid2;
    /** @var int Store the inserted post1->id for use in test functions */
    protected $postid1;
    /** @var int Store the inserted post2->id for use in test functions */
    protected $postid2;
    /** @var int Store the inserted post3->id for use in test functions */
    protected $postid3;
    /** @var int Store the inserted post4->id for use in test functions */
    protected $postid4;
    /** @var int Official contextid for these tests */
    protected $contextid;


    protected function setUp(): void {
        parent::setUp();

        $this->resetAfterTest(true);

        $this->contextid = 666; // Let's assume this is the context for the forum
        $this->fill_records(); // Add common stuff needed by various test methods
    }

    private function fill_records() {
        global $DB;

        // Create one forum
        $forum_data = (object)array('course' => 1, 'name' => 'Test forum', 'intro' => 'Intro forum');
        $this->forumid = $DB->insert_record('forum', $forum_data);
        // With two related file
        $f1_forum_data = (object)array(
            'contenthash' => 'testf1', 'contextid' => $this->contextid,
            'component'=>'mod_forum', 'filearea' => 'intro', 'filename' => 'tf1', 'itemid' => 0,
            'filesize' => 123, 'timecreated' => 0, 'timemodified' => 0,
            'pathnamehash' => 'testf1'
        );
        $DB->insert_record('files', $f1_forum_data);
        $f2_forum_data = (object)array(
            'contenthash' => 'tesft2', 'contextid' => $this->contextid,
            'component'=>'mod_forum', 'filearea' => 'intro', 'filename' => 'tf2', 'itemid' => 0,
            'filesize' => 123, 'timecreated' => 0, 'timemodified' => 0,
            'pathnamehash' => 'testf2'
        );
        $DB->insert_record('files', $f2_forum_data);

        // Create two discussions
        $discussion1 = (object)array('course' => 1, 'forum' => $this->forumid, 'name' => 'd1', 'userid' => 100, 'groupid' => 200);
        $this->discussionid1 = $DB->insert_record('forum_discussions', $discussion1);
        $discussion2 = (object)array('course' => 1, 'forum' => $this->forumid, 'name' => 'd2', 'userid' => 101, 'groupid' => 201);
        $this->discussionid2 = $DB->insert_record('forum_discussions', $discussion2);

        // Create four posts
        $post1 = (object)array('discussion' => $this->discussionid1, 'userid' => 100, 'subject' => 'p1', 'message' => 'm1');
        $this->postid1 = $DB->insert_record('forum_posts', $post1);
        $post2 = (object)array('discussion' => $this->discussionid1, 'parent' => $this->postid1, 'userid' => 102, 'subject' => 'p2', 'message' => 'm2');
        $this->postid2 = $DB->insert_record('forum_posts', $post2);
        $post3 = (object)array('discussion' => $this->discussionid1, 'parent' => $this->postid2, 'userid' => 103, 'subject' => 'p3', 'message' => 'm3');
        $this->postid3 = $DB->insert_record('forum_posts', $post3);
        $post4 = (object)array('discussion' => $this->discussionid2, 'userid' => 101, 'subject' => 'p4', 'message' => 'm4');
        $this->postid4 = $DB->insert_record('forum_posts', $post4);
        // With two related file
        $f1_post1 = (object)array(
            'contenthash' => 'testp1', 'contextid' => $this->contextid, 'component'=>'mod_forum',
            'filearea' => 'post', 'filename' => 'tp1', 'itemid' => $this->postid1,
            'filesize' => 123, 'timecreated' => 0, 'timemodified' => 0,
            'pathnamehash' => 'testp1'
        );
        $DB->insert_record('files', $f1_post1);
        $f1_post2 = (object)array(
            'contenthash' => 'testp2', 'contextid' => $this->contextid, 'component'=>'mod_forum',
            'filearea' => 'attachment', 'filename' => 'tp2', 'itemid' => $this->postid2,
            'filesize' => 123, 'timecreated' => 0, 'timemodified' => 0,
            'pathnamehash' => 'testp2'
        );
        $DB->insert_record('files', $f1_post2);

        // Create two ratings
        $rating1 = (object)array(
            'contextid' => $this->contextid, 'userid' => 104, 'itemid' => $this->postid1, 'rating' => 2,
            'scaleid' => -1, 'timecreated' => time(), 'timemodified' => time());
        $r1id = $DB->insert_record('rating', $rating1);
        $rating2 = (object)array(
            'contextid' => $this->contextid, 'userid' => 105, 'itemid' => $this->postid1, 'rating' => 3,
            'scaleid' => -1, 'timecreated' => time(), 'timemodified' => time());
        $r2id = $DB->insert_record('rating', $rating2);

        // Create 1 reads
        $read1 = (object)array('userid' => 102, 'forumid' => $this->forumid, 'discussionid' => $this->discussionid2, 'postid' => $this->postid4);
        $DB->insert_record('forum_read', $read1);
    }

    /**
     * Backup structures tests (construction, definition and execution)
     */
    function test_backup_structure_construct() {
        global $DB;

        $backupid = 'Testing Backup ID'; // Official backupid for these tests

        // Create all the elements that will conform the tree
        $forum = new backup_nested_element('forum',
            array('id'),
            array(
                'type', 'name', 'intro', 'introformat',
                'assessed', 'assesstimestart', 'assesstimefinish', 'scale',
                'maxbytes', 'maxattachments', 'forcesubscribe', 'trackingtype',
                'rsstype', 'rssarticles', 'timemodified', 'warnafter',
                'blockafter',
                new backup_final_element('blockperiod'),
                new mock_skip_final_element('completiondiscussions'),
                new mock_modify_final_element('completionreplies'),
                new mock_final_element_interceptor('completionposts'))
        );
        $discussions = new backup_nested_element('discussions');
        $discussion = new backup_nested_element('discussion',
            array('id'),
            array(
                'forum', 'name', 'firstpost', 'userid',
                'groupid', 'assessed', 'timemodified', 'usermodified',
                'timestart', 'timeend')
        );
        $posts = new backup_nested_element('posts');
        $post = new backup_nested_element('post',
            array('id'),
            array(
                'discussion', 'parent', 'userid', 'created',
                'modified', 'mailed', 'subject', 'message',
                'messageformat', 'messagetrust', 'attachment', 'totalscore',
                'mailnow')
        );
        $ratings = new backup_nested_element('ratings');
        $rating = new backup_nested_element('rating',
            array('id'),
            array('userid', 'itemid', 'time', 'post_rating')
        );
        $reads = new backup_nested_element('readposts');
        $read = new backup_nested_element('read',
            array('id'),
            array(
                'userid', 'discussionid', 'postid',
                'firstread', 'lastread')
        );
        $inventeds = new backup_nested_element('invented_elements',
            array('reason', 'version')
        );
        $invented = new backup_nested_element('invented',
            null,
            array('one', 'two', 'three')
        );
        $one = $invented->get_final_element('one');
        $one->add_attributes(array('attr1', 'attr2'));

        // Build the tree
        $forum->add_child($discussions);
        $discussions->add_child($discussion);

        $discussion->add_child($posts);
        $posts->add_child($post);

        $post->add_child($ratings);
        $ratings->add_child($rating);

        $forum->add_child($reads);
        $reads->add_child($read);

        $forum->add_child($inventeds);
        $inventeds->add_child($invented);

        // Let's add 1 optigroup with 4 elements
        $alternative1 = new backup_optigroup_element('alternative1',
            array('name', 'value'), '../../id', $this->postid1);
        $alternative2 = new backup_optigroup_element('alternative2',
            array('name', 'value'), backup::VAR_PARENTID, $this->postid2);
        $alternative3 = new backup_optigroup_element('alternative3',
            array('name', 'value'), '/forum/discussions/discussion/posts/post/id', $this->postid3);
        $alternative4 = new backup_optigroup_element('alternative4',
            array('forumtype', 'forumname')); // Alternative without conditions
        // Create the optigroup, adding one element
        $optigroup = new backup_optigroup('alternatives', $alternative1, false);
        // Add second opti element
        $optigroup->add_child($alternative2);

        // Add optigroup to post element
        $post->add_optigroup($optigroup);
        // Add third opti element, on purpose after the add_optigroup() line above to check param evaluation works ok
        $optigroup->add_child($alternative3);
        // Add 4th opti element (the one without conditions, so will be present always)
        $optigroup->add_child($alternative4);

        /// Create some new nested elements, both named 'dupetest1', and add them to alternative1 and alternative2
        /// (not problem as far as the optigroup in not unique)
        $dupetest1 = new backup_nested_element('dupetest1', null, array('field1', 'field2'));
        $dupetest2 = new backup_nested_element('dupetest2', null, array('field1', 'field2'));
        $dupetest3 = new backup_nested_element('dupetest3', null, array('field1', 'field2'));
        $dupetest4 = new backup_nested_element('dupetest1', null, array('field1', 'field2'));
        $dupetest1->add_child($dupetest3);
        $dupetest2->add_child($dupetest4);
        $alternative1->add_child($dupetest1);
        $alternative2->add_child($dupetest2);

        // Define sources
        $forum->set_source_table('forum', array('id' => backup::VAR_ACTIVITYID));
        $discussion->set_source_sql('SELECT *
                                       FROM {forum_discussions}
                                      WHERE forum = ?',
            array('/forum/id')
        );
        $post->set_source_table('forum_posts', array('discussion' => '/forum/discussions/discussion/id'));
        $rating->set_source_sql('SELECT *
                                   FROM {rating}
                                  WHERE itemid = ?',
            array(backup::VAR_PARENTID)
        );

        $read->set_source_table('forum_read', array('forumid' => '../../id'));

        $inventeds->set_source_array(array((object)array('reason' => 'I love Moodle', 'version' => '1.0'),
            (object)array('reason' => 'I love Moodle', 'version' => '2.0'))); // 2 object array
        $invented->set_source_array(array((object)array('one' => 1, 'two' => 2, 'three' => 3),
            (object)array('one' => 11, 'two' => 22, 'three' => 33))); // 2 object array

        // Set optigroup_element sources
        $alternative1->set_source_array(array((object)array('name' => 'alternative1', 'value' => 1))); // 1 object array
        // Skip alternative2 source definition on purpose (will be tested)
        // $alternative2->set_source_array(array((object)array('name' => 'alternative2', 'value' => 2))); // 1 object array
        $alternative3->set_source_array(array((object)array('name' => 'alternative3', 'value' => 3))); // 1 object array
        // Alternative 4 source is the forum type and name, so we'll get that in ALL posts (no conditions) that
        // have not another alternative (post4 in our testing data in the only not matching any other alternative)
        $alternative4->set_source_sql('SELECT type AS forumtype, name AS forumname
                                         FROM {forum}
                                        WHERE id = ?',
            array('/forum/id')
        );
        // Set children of optigroup_element source
        $dupetest1->set_source_array(array((object)array('field1' => '1', 'field2' => 1))); // 1 object array
        $dupetest2->set_source_array(array((object)array('field1' => '2', 'field2' => 2))); // 1 object array
        $dupetest3->set_source_array(array((object)array('field1' => '3', 'field2' => 3))); // 1 object array
        $dupetest4->set_source_array(array((object)array('field1' => '4', 'field2' => 4))); // 1 object array

        // Define some aliases
        $rating->set_source_alias('rating', 'post_rating'); // Map the 'rating' value from DB to 'post_rating' final element

        // Mark to detect files of type 'forum_intro' in forum (and not item id)
        $forum->annotate_files('mod_forum', 'intro', null);

        // Mark to detect file of type 'forum_post' and 'forum_attachment' in post (with itemid being post->id)
        $post->annotate_files('mod_forum', 'post', 'id');
        $post->annotate_files('mod_forum', 'attachment', 'id');

        // Mark various elements to be annotated
        $discussion->annotate_ids('user1', 'userid');
        $post->annotate_ids('forum_post', 'id');
        $rating->annotate_ids('user2', 'userid');
        $rating->annotate_ids('forum_post', 'itemid');

        // Create the backup_ids_temp table
        backup_controller_dbops::create_backup_ids_temp_table($backupid);

        // Instantiate in memory xml output
        $xo = new memory_xml_output();

        // Instantiate xml_writer and start it
        $xw = new xml_writer($xo);
        $xw->start();

        // Instantiate the backup processor
        $processor = new backup_structure_processor($xw);

        // Set some variables
        $processor->set_var(backup::VAR_ACTIVITYID, $this->forumid);
        $processor->set_var(backup::VAR_BACKUPID, $backupid);
        $processor->set_var(backup::VAR_CONTEXTID,$this->contextid);

        // Process the backup structure with the backup processor
        $forum->process($processor);

        // Stop the xml_writer
        $xw->stop();

        // Check various counters
        $this->assertEquals($forum->get_counter(), $DB->count_records('forum'));
        $this->assertEquals($discussion->get_counter(), $DB->count_records('forum_discussions'));
        $this->assertEquals($rating->get_counter(), $DB->count_records('rating'));
        $this->assertEquals($read->get_counter(), $DB->count_records('forum_read'));
        $this->assertEquals($inventeds->get_counter(), 2); // Array

        // Perform some validations with the generated XML
        $dom = new DomDocument();
        $dom->loadXML($xo->get_allcontents());
        $xpath = new DOMXPath($dom);
        // Some more counters
        $query = '/forum/discussions/discussion/posts/post';
        $posts = $xpath->query($query);
        $this->assertEquals($posts->length, $DB->count_records('forum_posts'));
        $query = '/forum/invented_elements/invented';
        $inventeds = $xpath->query($query);
        $this->assertEquals($inventeds->length, 2*2);

        // Check ratings information against DB
        $ratings = $dom->getElementsByTagName('rating');
        $this->assertEquals($ratings->length, $DB->count_records('rating'));
        foreach ($ratings as $rating) {
            $ratarr = array();
            $ratarr['id'] = $rating->getAttribute('id');
            foreach ($rating->childNodes as $node) {
                if ($node->nodeType != XML_TEXT_NODE) {
                    $ratarr[$node->nodeName] = $node->nodeValue;
                }
            }
            $this->assertEquals($DB->get_field('rating', 'userid', array('id' => $ratarr['id'])), $ratarr['userid']);
            $this->assertEquals($DB->get_field('rating', 'itemid', array('id' => $ratarr['id'])), $ratarr['itemid']);
            $this->assertEquals($DB->get_field('rating', 'rating', array('id' => $ratarr['id'])), $ratarr['post_rating']);
        }

        // Check forum has "blockeperiod" with value 0 (was declared by object instead of name)
        $query = '/forum[blockperiod="0"]';
        $result = $xpath->query($query);
        $this->assertEquals(1, $result->length);

        // Check forum is missing "completiondiscussions" (as we are using mock_skip_final_element)
        $query = '/forum/completiondiscussions';
        $result = $xpath->query($query);
        $this->assertEquals(0, $result->length);

        // Check forum has "completionreplies" with value "original was 0, now changed" (because of mock_modify_final_element)
        $query = '/forum[completionreplies="original was 0, now changed"]';
        $result = $xpath->query($query);
        $this->assertEquals(1, $result->length);

        // Check forum has "completionposts" with value "intercepted!" (because of mock_final_element_interceptor)
        $query = '/forum[completionposts="intercepted!"]';
        $result = $xpath->query($query);
        $this->assertEquals(1, $result->length);

        // Check there isn't any alternative2 tag, as far as it hasn't source defined
        $query = '//alternative2';
        $result = $xpath->query($query);
        $this->assertEquals(0, $result->length);

        // Check there are 4 "field1" elements
        $query = '/forum/discussions/discussion/posts/post//field1';
        $result = $xpath->query($query);
        $this->assertEquals(4, $result->length);

        // Check first post has one name element with value "alternative1"
        $query = '/forum/discussions/discussion/posts/post[@id="'.$this->postid1.'"][name="alternative1"]';
        $result = $xpath->query($query);
        $this->assertEquals(1, $result->length);

        // Check there are two "dupetest1" elements
        $query = '/forum/discussions/discussion/posts/post//dupetest1';
        $result = $xpath->query($query);
        $this->assertEquals(2, $result->length);

        // Check second post has one name element with value "dupetest2"
        $query = '/forum/discussions/discussion/posts/post[@id="'.$this->postid2.'"]/dupetest2';
        $result = $xpath->query($query);
        $this->assertEquals(1, $result->length);

        // Check element "dupetest2" of second post has one field1 element with value "2"
        $query = '/forum/discussions/discussion/posts/post[@id="'.$this->postid2.'"]/dupetest2[field1="2"]';
        $result = $xpath->query($query);
        $this->assertEquals(1, $result->length);

        // Check forth post has no name element
        $query = '/forum/discussions/discussion/posts/post[@id="'.$this->postid4.'"]/name';
        $result = $xpath->query($query);
        $this->assertEquals(0, $result->length);

        // Check 1st, 2nd and 3rd posts have no forumtype element
        $query = '/forum/discussions/discussion/posts/post[@id="'.$this->postid1.'"]/forumtype';
        $result = $xpath->query($query);
        $this->assertEquals(0, $result->length);
        $query = '/forum/discussions/discussion/posts/post[@id="'.$this->postid2.'"]/forumtype';
        $result = $xpath->query($query);
        $this->assertEquals(0, $result->length);
        $query = '/forum/discussions/discussion/posts/post[@id="'.$this->postid3.'"]/forumtype';
        $result = $xpath->query($query);
        $this->assertEquals(0, $result->length);

        // Check 4th post has one forumtype element with value "general"
        // (because it doesn't matches alternatives 1, 2, 3, then alternative 4,
        // the one without conditions is being applied)
        $query = '/forum/discussions/discussion/posts/post[@id="'.$this->postid4.'"][forumtype="general"]';
        $result = $xpath->query($query);
        $this->assertEquals(1, $result->length);

        // Check annotations information against DB
        // Count records in original tables
        $c_postsid    = $DB->count_records_sql('SELECT COUNT(DISTINCT id) FROM {forum_posts}');
        $c_dissuserid = $DB->count_records_sql('SELECT COUNT(DISTINCT userid) FROM {forum_discussions}');
        $c_ratuserid  = $DB->count_records_sql('SELECT COUNT(DISTINCT userid) FROM {rating}');
        // Count records in backup_ids_table
        $f_forumpost = $DB->count_records('backup_ids_temp', array('backupid' => $backupid, 'itemname' => 'forum_post'));
        $f_user1     = $DB->count_records('backup_ids_temp', array('backupid' => $backupid, 'itemname' => 'user1'));
        $f_user2     = $DB->count_records('backup_ids_temp', array('backupid' => $backupid, 'itemname' => 'user2'));
        $c_notbackupid = $DB->count_records_select('backup_ids_temp', 'backupid != ?', array($backupid));
        // Peform tests by comparing counts
        $this->assertEquals($c_notbackupid, 0); // there isn't any record with incorrect backupid
        $this->assertEquals($c_postsid, $f_forumpost); // All posts have been registered
        $this->assertEquals($c_dissuserid, $f_user1); // All users coming from discussions have been registered
        $this->assertEquals($c_ratuserid, $f_user2); // All users coming from ratings have been registered

        // Check file annotations against DB
        $fannotations = $DB->get_records('backup_ids_temp', array('backupid' => $backupid, 'itemname' => 'file'));
        $ffiles       = $DB->get_records('files', array('contextid' => $this->contextid));
        $this->assertEquals(count($fannotations), count($ffiles)); // Same number of recs in both (all files have been annotated)
        foreach ($fannotations as $annotation) { // Check ids annotated
            $this->assertTrue($DB->record_exists('files', array('id' => $annotation->itemid)));
        }

        // Drop the backup_ids_temp table
        backup_controller_dbops::drop_backup_ids_temp_table('testingid');
    }

    /**
     * Backup structures wrong tests (trying to do things the wrong way)
     */
    function test_backup_structure_wrong() {

        // Instantiate the backup processor
        $processor = new backup_structure_processor(new xml_writer(new memory_xml_output()));
        $this->assertTrue($processor instanceof base_processor);

        // Set one var twice
        $processor->set_var('onenewvariable', 999);
        try {
            $processor->set_var('onenewvariable', 999);
            $this->assertTrue(false, 'backup_processor_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_processor_exception);
            $this->assertEquals($e->errorcode, 'processorvariablealreadyset');
            $this->assertEquals($e->a, 'onenewvariable');
        }

        // Get non-existing var
        try {
            $var = $processor->get_var('nonexistingvar');
            $this->assertTrue(false, 'backup_processor_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_processor_exception);
            $this->assertEquals($e->errorcode, 'processorvariablenotfound');
            $this->assertEquals($e->a, 'nonexistingvar');
        }

        // Create nested element and try ro get its parent id (doesn't exisit => exception)
        $ne = new backup_nested_element('test', 'one', 'two', 'three');
        try {
            $ne->set_source_table('forum', array('id' => backup::VAR_PARENTID));
            $ne->process($processor);
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'cannotfindparentidforelement');
        }

        // Try to process one nested/final/attribute elements without processor
        $ne = new backup_nested_element('test', 'one', 'two', 'three');
        try {
            $ne->process(new stdclass());
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'incorrect_processor');
        }
        $fe = new backup_final_element('test');
        try {
            $fe->process(new stdclass());
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'incorrect_processor');
        }
        $at = new backup_attribute('test');
        try {
            $at->process(new stdclass());
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'incorrect_processor');
        }

        // Try to put an incorrect alias
        $ne = new backup_nested_element('test', 'one', 'two', 'three');
        try {
            $ne->set_source_alias('last', 'nonexisting');
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'incorrectaliasfinalnamenotfound');
            $this->assertEquals($e->a, 'nonexisting');
        }

        // Try various incorrect paths specifying source
        $ne = new backup_nested_element('test', 'one', 'two', 'three');
        try {
            $ne->set_source_table('forum', array('/test/subtest'));
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'baseelementincorrectfinalorattribute');
            $this->assertEquals($e->a, 'subtest');
        }
        try {
            $ne->set_source_table('forum', array('/wrongtest'));
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'baseelementincorrectgrandparent');
            $this->assertEquals($e->a, 'wrongtest');
        }
        try {
            $ne->set_source_table('forum', array('../nonexisting'));
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'baseelementincorrectparent');
            $this->assertEquals($e->a, '..');
        }

        // Try various incorrect file annotations

        $ne = new backup_nested_element('test', 'one', 'two', 'three');
        $ne->annotate_files('test', 'filearea', null);
        try {
            $ne->annotate_files('test', 'filearea', null); // Try to add annotations twice
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'annotate_files_duplicate_annotation');
            $this->assertEquals($e->a, 'test/filearea/');
        }

        $ne = new backup_nested_element('test', 'one', 'two', 'three');
        try {
            $ne->annotate_files('test', 'filearea', 'four'); // Incorrect element
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'baseelementincorrectfinalorattribute');
            $this->assertEquals($e->a, 'four');
        }

        // Try to add incorrect element to backup_optigroup
        $bog = new backup_optigroup('test');
        try {
            $bog->add_child(new backup_nested_element('test2'));
            $this->assertTrue(false, 'base_optigroup_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_optigroup_exception);
            $this->assertEquals($e->errorcode, 'optigroup_element_incorrect');
            $this->assertEquals($e->a, 'backup_nested_element');
        }

        $bog = new backup_optigroup('test');
        try {
            $bog->add_child('test2');
            $this->assertTrue(false, 'base_optigroup_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_optigroup_exception);
            $this->assertEquals($e->errorcode, 'optigroup_element_incorrect');
            $this->assertEquals($e->a, 'non object');
        }

        try {
            $bog = new backup_optigroup('test', new stdclass());
            $this->assertTrue(false, 'base_optigroup_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_optigroup_exception);
            $this->assertEquals($e->errorcode, 'optigroup_elements_incorrect');
        }

        // Try a wrong processor with backup_optigroup
        $bog = new backup_optigroup('test');
        try {
            $bog->process(new stdclass());
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'incorrect_processor');
        }

        // Try duplicating used elements with backup_optigroup
        // Adding top->down
        $bog = new backup_optigroup('test', null, true);
        $boge1 = new backup_optigroup_element('boge1');
        $boge2 = new backup_optigroup_element('boge2');
        $ne1 = new backup_nested_element('ne1');
        $ne2 = new backup_nested_element('ne1');
        $bog->add_child($boge1);
        $bog->add_child($boge2);
        $boge1->add_child($ne1);
        try {
            $boge2->add_child($ne2);
            $this->assertTrue(false, 'base_optigroup_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_optigroup_exception);
            $this->assertEquals($e->errorcode, 'multiple_optigroup_duplicate_element');
            $this->assertEquals($e->a, 'ne1');
        }
        // Adding down->top
        $bog = new backup_optigroup('test', null, true);
        $boge1 = new backup_optigroup_element('boge1');
        $boge2 = new backup_optigroup_element('boge2');
        $ne1 = new backup_nested_element('ne1');
        $ne2 = new backup_nested_element('ne1');
        $boge1->add_child($ne1);
        $boge2->add_child($ne2);
        $bog->add_child($boge1);
        try {
            $bog->add_child($boge2);
            $this->assertTrue(false, 'base_element_struct_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_element_struct_exception);
            $this->assertEquals($e->errorcode, 'baseelementexisting');
            $this->assertEquals($e->a, 'ne1');
        }
    }
}
