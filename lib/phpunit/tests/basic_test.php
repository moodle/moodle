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
 * PHPUnit integration tests
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
    protected $testassertexecuted = false;

    protected function setUp() {
        parent::setUp();
        if ($this->getName() === 'test_setup_assert') {
            $this->assertTrue(true);
            $this->testassertexecuted = true;
            return;
        }
    }

    /**
     * Tests that bootstrapping has occurred correctly
     * @return void
     */
    public function test_bootstrap() {
        global $CFG;
        // The use of httpswwwroot is deprecated, but we are still setting it for backwards compatibility.
        $this->assertTrue(isset($CFG->httpswwwroot));
        $this->assertEquals($CFG->httpswwwroot, $CFG->wwwroot);
        $this->assertEquals($CFG->prefix, $CFG->phpunit_prefix);
    }

    /**
     * This is just a verification if I understand the PHPUnit assert docs right --skodak
     * @return void
     */
    public function test_assert_behaviour() {
        // Arrays.
        $a = array('a', 'b', 'c');
        $b = array('a', 'c', 'b');
        $c = array('a', 'b', 'c');
        $d = array('a', 'b', 'C');
        $this->assertNotEquals($a, $b);
        $this->assertNotEquals($a, $d);
        $this->assertEquals($a, $c);
        $this->assertEquals($a, $b, '', 0, 10, true);

        // Objects.
        $a = new stdClass();
        $a->x = 'x';
        $a->y = 'y';
        $b = new stdClass(); // Switched order.
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

        // String comparison.
        $this->assertEquals(1, '1');
        $this->assertEquals(null, '');

        $this->assertNotEquals(1, '1 ');
        $this->assertNotEquals(0, '');
        $this->assertNotEquals(null, '0');
        $this->assertNotEquals(array(), '');

        // Other comparison.
        $this->assertEquals(null, null);
        $this->assertEquals(false, null);
        $this->assertEquals(0, null);

        // Emptiness.
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

    /**
     * Make sure there are no sloppy Windows line endings
     * that would break our tests.
     */
    public function test_lineendings() {
        $string = <<<STRING
a
b
STRING;
        $this->assertSame("a\nb", $string, 'Make sure all project files are checked out with unix line endings.');

    }

    /**
     * Make sure asserts in setUp() do not create problems.
     */
    public function test_setup_assert() {
        $this->assertTrue($this->testassertexecuted);
        $this->testassertexecuted = false;
    }

    // Uncomment following tests to see logging of unexpected changes in global state and database.
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
