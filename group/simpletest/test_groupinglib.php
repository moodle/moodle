<?php
/**
 * Unit tests for new Moodle Groups - groupinglib.php
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 */
require_once(dirname(__FILE__) . '/../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->dirroot . '/group/lib/groupinglib.php');

class groupinglib_test extends UnitTestCase {

    var $courseid= 0;
    var $userid  = 0;
    var $groupid = 0;
    var $groupingid = 0;

    function __construct() {
        parent::UnitTestCase();
        //$this->setUpOnce();
    }
    function __destruct() {
        //$this->tearDownOnce();
    }

    /**
     * setUp/tearDown: Better in a constructor/destructor, but PHP4 doesn't do destructors :(
     */
    function setUp() {
        parent::setUp();

        if ($course = groups_get_course_info(1)) {
            $this->courseid = $course->id;   
        }
        if ($user = groups_get_user(2)) { //Primary admin.
            $this->userid = $user->id;
        }

        $this->groupid = groups_create_group($this->courseid);
        $groupinfo = groups_set_default_group_settings();
        $bok = groups_set_group_settings($this->groupid, $groupinfo);
        $bok = groups_add_member($this->groupid, $this->userid);
    }

    function tearDown() {
        parent::tearDown();

        $bok = groups_remove_member($this->groupid, $this->userid);
        $bok = groups_delete_group($this->groupid);
    }

    function test_create_grouping() {      
        $this->assertTrue($this->groupingid = groups_create_grouping($this->courseid));
        $this->assertTrue(groups_grouping_exists($this->groupingid));
        $this->assertTrue(groups_grouping_belongs_to_course($this->groupingid, $this->courseid));

        $this->assertTrue($groupinginfo = groups_set_default_grouping_settings());
        $groupinginfo->name = 'Grouping '. $this->getLabel();
        $this->assertTrue(groups_set_grouping_settings($this->groupingid, $groupinginfo));
    }

    function test_groups_grouping_matches(){
      $groupinginfo->name = 'Grouping Testname:' .  $this->getLabel();
      $groupinginfo->description  = 'Grouing Test Description:' . $this->getLabel();

      $this->assertTrue($this->groupingid = groups_create_grouping($this->courseid, $groupinginfo));
      $this->assertTrue(groups_grouping_matches($this->courseid, $groupinginfo->name, $groupinginfo->description));

    }

    function test_add_group_to_grouping() {
        $this->assertTrue(groups_add_group_to_grouping($this->groupid, $this->groupingid));
        $this->assertTrue(groups_belongs_to_grouping($this->groupid, $this->groupingid));

        $this->assertTrue($groupings = groups_get_groupings_for_group($this->groupid));
        //array...
        $this->assertTrue($groups = groups_get_groups_in_grouping($this->groupingid));
        //...
    }

    function test_remove_group_from_grouping() {
        $this->assertTrue(groups_remove_group_from_grouping($this->groupid, $this->groupingid));
        $this->assertFalse(groups_belongs_to_grouping($this->groupid, $this->groupingid));        
    }

    function test_delete_grouping() {
        $this->assertTrue(groups_delete_grouping($this->groupingid));
        $this->assertFalse(groups_grouping_exists($this->groupingid));
    }
}

?>