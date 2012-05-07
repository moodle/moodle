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
 * PHPUnit integration unit tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Test basic_testcase extra features and PHPUnit Moodle integration.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_phpunit_basic_testcase extends basic_testcase {

    /**
     * Tests that bootstrapping has occurred correctly
     * @return void
     */
    public function test_bootstrap() {
        global $CFG;
        $this->assertTrue(isset($CFG->httpswwwroot));
        $this->assertEquals($CFG->httpswwwroot, $CFG->wwwroot);
        $this->assertEquals($CFG->prefix, $CFG->phpunit_prefix);
    }

    /**
     * This is just a verification if I understand the PHPUnit assert docs right --skodak
     * @return void
     */
    public function test_assert_behaviour() {
        // arrays
        $a = array('a', 'b', 'c');
        $b = array('a', 'c', 'b');
        $c = array('a', 'b', 'c');
        $d = array('a', 'b', 'C');
        $this->assertNotEquals($a, $b);
        $this->assertNotEquals($a, $d);
        $this->assertEquals($a, $c);
        $this->assertEquals($a, $b, '', 0, 10, true);

        // objects
        $a = new stdClass();
        $a->x = 'x';
        $a->y = 'y';
        $b = new stdClass(); // switched order
        $b->y = 'y';
        $b->x = 'x';
        $c = $a;
        $d = new stdClass();
        $d->x = 'x';
        $d->y = 'y';
        $d->z = 'z';
        $this->assertEquals($a, $b);
        $this->assertNotSame($a, $b);
        $this->assertEquals($a, $c);
        $this->assertSame($a, $c);
        $this->assertNotEquals($a, $d);

        // string comparison
        $this->assertEquals(1, '1');
        $this->assertEquals(null, '');

        $this->assertNotEquals(1, '1 ');
        $this->assertNotEquals(0, '');
        $this->assertNotEquals(null, '0');
        $this->assertNotEquals(array(), '');

        // other comparison
        $this->assertEquals(null, null);
        $this->assertEquals(false, null);
        $this->assertEquals(0, null);

        // emptiness
        $this->assertEmpty(0);
        $this->assertEmpty(0.0);
        $this->assertEmpty('');
        $this->assertEmpty('0');
        $this->assertEmpty(false);
        $this->assertEmpty(null);
        $this->assertEmpty(array());

        $this->assertNotEmpty(1);
        $this->assertNotEmpty(0.1);
        $this->assertNotEmpty(-1);
        $this->assertNotEmpty(' ');
        $this->assertNotEmpty('0 ');
        $this->assertNotEmpty(true);
        $this->assertNotEmpty(array(null));
        $this->assertNotEmpty(new stdClass());
    }

// Uncomment following tests to see logging of unexpected changes in global state and database
/*
    public function test_db_modification() {
        global $DB;
        $DB->set_field('user', 'confirmed', 1, array('id'=>-1));
    }

    public function test_cfg_modification() {
        global $CFG;
        $CFG->xx = 'yy';
        unset($CFG->admin);
        $CFG->rolesactive = 0;
    }

    public function test_user_modification() {
        global $USER;
        $USER->id = 10;
    }

    public function test_course_modification() {
        global $COURSE;
        $COURSE->id = 10;
    }

    public function test_all_modifications() {
        global $DB, $CFG, $USER, $COURSE;
        $DB->set_field('user', 'confirmed', 1, array('id'=>-1));
        $CFG->xx = 'yy';
        unset($CFG->admin);
        $CFG->rolesactive = 0;
        $USER->id = 10;
        $COURSE->id = 10;
    }

    public function test_transaction_problem() {
        global $DB;
        $DB->start_delegated_transaction();
    }
*/
}


/**
 * Test advanced_testcase extra features.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_phpunit_advanced_testcase extends advanced_testcase {

    public function test_set_user() {
        global $USER, $DB;

        $this->assertEquals(0, $USER->id);
        $this->assertSame($_SESSION['USER'], $USER);

        $user = $DB->get_record('user', array('id'=>2));
        $this->setUser($user);
        $this->assertEquals(2, $USER->id);
        $this->assertEquals(2, $_SESSION['USER']->id);
        $this->assertSame($_SESSION['USER'], $USER);

        $USER->id = 3;
        $this->assertEquals(3, $USER->id);
        $this->assertEquals(3, $_SESSION['USER']->id);
        $this->assertSame($_SESSION['USER'], $USER);

        session_set_user($user);
        $this->assertEquals(2, $USER->id);
        $this->assertEquals(2, $_SESSION['USER']->id);
        $this->assertSame($_SESSION['USER'], $USER);

        $USER = $DB->get_record('user', array('id'=>1));
        $this->assertEquals(1, $USER->id);
        $this->assertEquals(1, $_SESSION['USER']->id);
        $this->assertSame($_SESSION['USER'], $USER);

        $this->setUser(null);
        $this->assertEquals(0, $USER->id);
        $this->assertSame($_SESSION['USER'], $USER);
    }

    public function test_database_reset() {
        global $DB;

        $this->resetAfterTest(true);

        $this->preventResetByRollback();

        $this->assertEquals(1, $DB->count_records('course')); // only frontpage in new site

        // this is weird table - id is NOT a sequence here
        $this->assertEquals(0, $DB->count_records('context_temp'));
        $DB->import_record('context_temp', array('id'=>5, 'path'=>'/1/2', 'depth'=>2));
        $record = $DB->get_record('context_temp', array());
        $this->assertEquals(5, $record->id);

        $this->assertEquals(0, $DB->count_records('user_preferences'));
        $originaldisplayid = $DB->insert_record('user_preferences', array('userid'=>2, 'name'=> 'phpunittest', 'value'=>'x'));
        $this->assertEquals(1, $originaldisplayid);

        $course = $this->getDataGenerator()->create_course();
        $this->assertEquals(2, $course->id);

        $this->assertEquals(2, $DB->count_records('user'));
        $DB->delete_records('user', array('id'=>1));
        $this->assertEquals(1, $DB->count_records('user'));

        //=========

        $this->resetAllData();

        $this->assertEquals(1, $DB->count_records('course')); // only frontpage in new site
        $this->assertEquals(0, $DB->count_records('context_temp')); // only frontpage in new site
        $course = $this->getDataGenerator()->create_course();
        $this->assertEquals(2, $course->id);

        $displayid = $DB->insert_record('user_preferences', array('userid'=>2, 'name'=> 'phpunittest', 'value'=>'x'));
        $this->assertEquals($originaldisplayid, $displayid);

        $this->assertEquals(2, $DB->count_records('user'));
        $DB->delete_records('user', array('id'=>2));
        $user = $this->getDataGenerator()->create_user();
        $this->assertEquals(3, $user->id);

        // =========

        $this->resetAllData();

        $course = $this->getDataGenerator()->create_course();
        $this->assertEquals(2, $course->id);

        $this->assertEquals(2, $DB->count_records('user'));
        $DB->delete_records('user', array('id'=>2));

        //==========

        $this->resetAllData();

        $course = $this->getDataGenerator()->create_course();
        $this->assertEquals(2, $course->id);

        $this->assertEquals(2, $DB->count_records('user'));
    }

    public function test_change_detection() {
        global $DB, $CFG, $COURSE, $SITE, $USER;

        $this->preventResetByRollback();
        phpunit_util::reset_all_data(true);

        // database change
        $this->assertEquals(1, $DB->get_field('user', 'confirmed', array('id'=>2)));
        $DB->set_field('user', 'confirmed', 0, array('id'=>2));
        try {
            phpunit_util::reset_all_data(true);
        } catch (Exception $e) {
            $this->assertInstanceOf('PHPUnit_Framework_Error_Warning', $e);
        }
        $this->assertEquals(1, $DB->get_field('user', 'confirmed', array('id'=>2)));

        // config change
        $CFG->xx = 'yy';
        unset($CFG->admin);
        $CFG->rolesactive = 0;
        try {
            phpunit_util::reset_all_data(true);
        } catch (Exception $e) {
            $this->assertInstanceOf('PHPUnit_Framework_Error_Warning', $e);
            $this->assertContains('xx', $e->getMessage());
            $this->assertContains('admin', $e->getMessage());
            $this->assertContains('rolesactive', $e->getMessage());
        }
        $this->assertFalse(isset($CFG->xx));
        $this->assertTrue(isset($CFG->admin));
        $this->assertEquals(1, $CFG->rolesactive);

        //silent changes
        $_SERVER['xx'] = 'yy';
        phpunit_util::reset_all_data(true);
        $this->assertFalse(isset($_SERVER['xx']));

        // COURSE
        $SITE->id = 10;
        $COURSE = new stdClass();
        $COURSE->id = 7;
        try {
            phpunit_util::reset_all_data(true);
        } catch (Exception $e) {
            $this->assertInstanceOf('PHPUnit_Framework_Error_Warning', $e);
            $this->assertEquals(1, $SITE->id);
            $this->assertSame($SITE, $COURSE);
            $this->assertSame($SITE, $COURSE);
        }

        // USER change
        $this->setUser(2);
        try {
            phpunit_util::reset_all_data(true);
        } catch (Exception $e) {
            $this->assertInstanceOf('PHPUnit_Framework_Error_Warning', $e);
            $this->assertEquals(0, $USER->id);
        }
    }

    public function test_getDataGenerator() {
        $generator = $this->getDataGenerator();
        $this->assertInstanceOf('phpunit_data_generator', $generator);
    }

    public function test_database_mock1() {
        global $DB;

        try {
            $DB->get_record('pokus', array());
            $this->fail('Exception expected when accessing non existent table');
        } catch (dml_exception $e) {
            $this->assertTrue(true);
        }
        $DB = $this->getMock(get_class($DB));
        $this->assertNull($DB->get_record('pokus', array()));
        // test continues after reset
    }

    public function test_database_mock2() {
        global $DB;

        // now the database should be back to normal
        $this->assertFalse($DB->get_record('user', array('id'=>9999)));
    }

    public function test_load_dataset() {
        global $DB;

        $this->resetAfterTest(true);

        $this->assertFalse($DB->record_exists('user', array('id'=>5)));
        $this->assertFalse($DB->record_exists('user', array('id'=>7)));
        $dataset = $this->createXMLDataSet(__DIR__.'/fixtures/sample_dataset.xml');
        $this->loadDataSet($dataset);
        $this->assertTrue($DB->record_exists('user', array('id'=>5)));
        $this->assertTrue($DB->record_exists('user', array('id'=>7)));
        $user5 = $DB->get_record('user', array('id'=>5));
        $user7 = $DB->get_record('user', array('id'=>7));
        $this->assertEquals('john.doe', $user5->username);
        $this->assertEquals('jane.doe', $user7->username);

        $dataset = $this->createCsvDataSet(array('user'=>__DIR__.'/fixtures/sample_dataset.csv'));
        $this->loadDataSet($dataset);
        $this->assertEquals(8, $DB->get_field('user', 'id', array('username'=>'pepa.novak')));
        $this->assertEquals(9, $DB->get_field('user', 'id', array('username'=>'bozka.novakova')));

        $data = array(
            'user' => array(
                array('username', 'email'),
                array('top.secret', 'top@example.com'),
                array('low.secret', 'low@example.com'),
            ),
        );
        $dataset = $this->createArrayDataSet($data);
        $this->loadDataSet($dataset);
        $this->assertTrue($DB->record_exists('user', array('email'=>'top@example.com')));
        $this->assertTrue($DB->record_exists('user', array('email'=>'low@example.com')));

        $data = array(
            'user' => array(
                array('username'=>'noidea', 'email'=>'noidea@example.com'),
                array('username'=>'onemore', 'email'=>'onemore@example.com'),
            ),
        );
        $dataset = $this->createArrayDataSet($data);
        $this->loadDataSet($dataset);
        $this->assertTrue($DB->record_exists('user', array('username'=>'noidea')));
        $this->assertTrue($DB->record_exists('user', array('username'=>'onemore')));
    }
}


/**
 * Test data generator
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_phpunit_generator_testcase extends advanced_testcase {
    public function test_create() {
        global $DB;

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $count = $DB->count_records('user');
        $user = $generator->create_user();
        $this->assertEquals($count+1, $DB->count_records('user'));

        $count = $DB->count_records('course_categories');
        $category = $generator->create_category();
        $this->assertEquals($count+1, $DB->count_records('course_categories'));

        $count = $DB->count_records('course');
        $course = $generator->create_course();
        $this->assertEquals($count+1, $DB->count_records('course'));

        $section = $generator->create_course_section(array('course'=>$course->id, 'section'=>3));
        $this->assertEquals($course->id, $section->course);

        $scale = $generator->create_scale();
        $this->assertNotEmpty($scale);
    }

    public function test_create_module() {
        global $CFG, $SITE;
        if (!file_exists("$CFG->dirroot/mod/page/")) {
            $this->markTestSkipped('Can not find standard Page module');
        }

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $page = $generator->create_module('page', array('course'=>$SITE->id));
        $this->assertNotEmpty($page);
    }

    public function test_create_block() {
        global $CFG;
        if (!file_exists("$CFG->dirroot/blocks/online_users/")) {
            $this->markTestSkipped('Can not find standard Online users block');
        }

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $page = $generator->create_block('online_users');
        $this->assertNotEmpty($page);
    }
}
