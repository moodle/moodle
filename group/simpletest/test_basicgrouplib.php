<?php
/**
 * Unit tests for new Moodle Groups - basicgrouplib.php and some of utillib.php.
 * 
 * /admin/report/simpletest/index.php?showpasses=1&showsearch=1&path=course%2Fgroups
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear AT open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package groups
 *
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once(dirname(__FILE__) . '/../../config.php');

global $CFG;
require_once($CFG->libdir . '/simpletestlib.php');
require_once($CFG->dirroot . '/group/lib/basicgrouplib.php');
require_once($CFG->dirroot . '/group/lib/utillib.php');

class basicgrouplib_test extends UnitTestCase {

    var $courseid= 0;
    var $userid  = 0;
    var $userid_2= 0;
    var $groupid = 0;

    function __construct() {
       parent::UnitTestCase();
    }

    function test_get_user() {
        $this->assertTrue($user = groups_get_user(2)); //Primary admin.
        if (isset($user)) {
            $this->userid = $user->id;
        }
        $this->assertTrue($user_2 = groups_get_user(1)); //Guest.
        if (isset($user_2)) {
            $this->userid_2 = $user_2->id;
        }
    }

    function test_get_course_info() {
        $this->assertTrue($course = groups_get_course_info(1));
        if (isset($course)) {
            $this->courseid = $course->id;   
        }
    }

    function test_create_group() {      
        $this->assertTrue($this->groupid = groups_create_group($this->courseid));
        $this->assertTrue(groups_group_exists($this->groupid));
        $this->assertTrue(groups_group_belongs_to_course($this->groupid, $this->courseid));
        $this->assertTrue($groupids = groups_get_groups($this->courseid));
        //array...
        $this->assertTrue($groupinfo = groups_set_default_group_settings());
        $groupinfo->name = 'Group '. $this->getLabel();  //'Temporary Group Name'
        $this->assertTrue(groups_set_group_settings($this->groupid, $groupinfo));
        $this->assertTrue($groupinfo->name == groups_get_group_name($this->groupid));
        $this->assertTrue($this->courseid == groups_get_course($this->groupid));
    }

    function test_group_matches(){
      $groupinfo->name = 'Group Testname:' .  $this->getLabel();
      $groupinfo->description  = 'Group Test Description:' . $this->getLabel();

      $this->assertTrue($this->groupid = groups_create_group($this->courseid, $groupinfo));
      $this->assertTrue(groups_group_matches($this->courseid, $groupinfo->name, $groupinfo->description));

    }

    function test_add_member() {
        // NOTE, interface change on add_member, remove_member. 
        $this->assertTrue(groups_add_member($this->groupid, $this->userid));
        $this->assertTrue(groups_is_member($this->groupid, $this->userid));
        $this->assertTrue($userids = groups_get_members($this->groupid));
        //...
        $this->assertTrue($groupids= groups_get_groups_for_user($this->userid, $this->courseid));
        //...        
        $this->assertTrue(1 == groups_count_group_members($this->groupid)); //Utillib.
    }

    function test_remove_member() {
        $this->assertTrue(groups_remove_member($this->groupid, $this->userid));
        $this->assertFalse(groups_is_member($this->groupid, $this->userid));
    }

    function test_delete_group() {
        $this->assertTrue(groups_delete_group($this->groupid));
        $this->assertFalse(groups_group_exists($this->groupid));
    }
}

?>
