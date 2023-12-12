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
 * Setting tests (all).
 *
 * @package   core_backup
 * @category  test
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_backup;

use activity_backup_setting;
use backup_setting;
use backup_setting_exception;
use base_setting;
use base_setting_exception;
use course_backup_setting;
use section_backup_setting;
use setting_dependency;
use setting_dependency_disabledif_empty;

defined('MOODLE_INTERNAL') || die();

// Include all the needed stuff
global $CFG;
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
require_once($CFG->dirroot . '/backup/backup.class.php');
require_once($CFG->dirroot . '/backup/util/settings/base_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/setting_dependency.class.php');
require_once($CFG->dirroot . '/backup/util/settings/root/root_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/activity/activity_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/section/section_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/course/course_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/ui/backup_ui_setting.class.php');

/**
 * Setting tests (all).
 *
 * @package   core_backup
 * @category  test
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings_test extends \basic_testcase {

    /**
     * test base_setting class
     */
    public function test_base_setting() {
        // Instantiate base_setting and check everything
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN);
        $this->assertTrue($bs instanceof base_setting);
        $this->assertEquals($bs->get_name(), 'test');
        $this->assertEquals($bs->get_vtype(), base_setting::IS_BOOLEAN);
        $this->assertTrue(is_null($bs->get_value()));
        $this->assertEquals($bs->get_visibility(), base_setting::VISIBLE);
        $this->assertEquals($bs->get_status(), base_setting::NOT_LOCKED);

        // Instantiate base_setting with explicit nulls
        $bs = new mock_base_setting('test', base_setting::IS_FILENAME, 'filename.txt', null, null);
        $this->assertEquals($bs->get_value() , 'filename.txt');
        $this->assertEquals($bs->get_visibility(), base_setting::VISIBLE);
        $this->assertEquals($bs->get_status(), base_setting::NOT_LOCKED);

        // Instantiate base_setting and set value, visibility and status
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN);
        $bs->set_value(true);
        $this->assertNotEmpty($bs->get_value());
        $bs->set_visibility(base_setting::HIDDEN);
        $this->assertEquals($bs->get_visibility(), base_setting::HIDDEN);
        $bs->set_status(base_setting::LOCKED_BY_HIERARCHY);
        $this->assertEquals($bs->get_status(), base_setting::LOCKED_BY_HIERARCHY);

        // Instantiate with wrong vtype
        try {
            $bs = new mock_base_setting('test', 'one_wrong_type');
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'setting_invalid_type');
        }

        // Instantiate with wrong integer value
        try {
            $bs = new mock_base_setting('test', base_setting::IS_INTEGER, 99.99);
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'setting_invalid_integer');
        }

        // Instantiate with wrong filename value
        try {
            $bs = new mock_base_setting('test', base_setting::IS_FILENAME, '../../filename.txt');
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'setting_invalid_filename');
        }

        // Instantiate with wrong visibility
        try {
            $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN, null, 'one_wrong_visibility');
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'setting_invalid_visibility');
        }

        // Instantiate with wrong status
        try {
            $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN, null, null, 'one_wrong_status');
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'setting_invalid_status');
        }

        // Instantiate base_setting and try to set wrong ui_type
        // We need a custom error handler to catch the type hinting error
        // that should return incorrect_object_passed
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN);
        set_error_handler('\core_backup\backup_setting_error_handler', E_RECOVERABLE_ERROR);
        try {
            $bs->set_ui('one_wrong_ui_type', 'label', array(), array());
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'incorrect_object_passed');
        } catch (\TypeError $e) {
            // On PHP7+ we get a TypeError raised, lets check we've the right error.
            $this->assertMatchesRegularExpression('/must be (of type|an instance of) backup_setting_ui/', $e->getMessage());
        }
        restore_error_handler();

        // Instantiate base_setting and try to set wrong ui_label
        // We need a custom error handler to catch the type hinting error
        // that should return incorrect_object_passed
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN);
        set_error_handler('\core_backup\backup_setting_error_handler', E_RECOVERABLE_ERROR);
        try {
            $bs->set_ui(base_setting::UI_HTML_CHECKBOX, 'one/wrong/label', array(), array());
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'incorrect_object_passed');
        } catch (\TypeError $e) {
            // On PHP7+ we get a TypeError raised, lets check we've the right error.
            $this->assertMatchesRegularExpression('/must be (of type|an instance of) backup_setting_ui/', $e->getMessage());
        }
        restore_error_handler();

        // Try to change value of locked setting by permission
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN, null, null, base_setting::LOCKED_BY_PERMISSION);
        try {
            $bs->set_value(true);
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'setting_locked_by_permission');
        }

        // Try to change value of locked setting by config
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN, null, null, base_setting::LOCKED_BY_CONFIG);
        try {
            $bs->set_value(true);
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'setting_locked_by_config');
        }

        // Try to add same setting twice
        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        $bs2 = new mock_base_setting('test2', base_setting::IS_INTEGER, null);
        $bs1->add_dependency($bs2, null, array('value'=>0));
        try {
            $bs1->add_dependency($bs2);
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'setting_already_added');
        }

        // Try to create one circular reference
        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        try {
            $bs1->add_dependency($bs1); // self
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'setting_circular_reference');
            $this->assertTrue($e->a instanceof \stdClass);
            $this->assertEquals($e->a->main, 'test1');
            $this->assertEquals($e->a->alreadydependent, 'test1');
        }

        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        $bs2 = new mock_base_setting('test2', base_setting::IS_INTEGER, null);
        $bs3 = new mock_base_setting('test3', base_setting::IS_INTEGER, null);
        $bs4 = new mock_base_setting('test4', base_setting::IS_INTEGER, null);
        $bs1->add_dependency($bs2, null, array('value'=>0));
        $bs2->add_dependency($bs3, null, array('value'=>0));
        $bs3->add_dependency($bs4, null, array('value'=>0));
        try {
            $bs4->add_dependency($bs1, null, array('value'=>0));
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'setting_circular_reference');
            $this->assertTrue($e->a instanceof \stdClass);
            $this->assertEquals($e->a->main, 'test1');
            $this->assertEquals($e->a->alreadydependent, 'test4');
        }

        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        $bs2 = new mock_base_setting('test2', base_setting::IS_INTEGER, null);
        $bs1->register_dependency(new setting_dependency_disabledif_empty($bs1, $bs2));
        try {
            // $bs1 is already dependent on $bs2 so this should fail.
            $bs2->register_dependency(new setting_dependency_disabledif_empty($bs2, $bs1));
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEquals($e->errorcode, 'setting_circular_reference');
            $this->assertTrue($e->a instanceof \stdClass);
            $this->assertEquals($e->a->main, 'test1');
            $this->assertEquals($e->a->alreadydependent, 'test2');
        }

        // Create 3 settings and observe between them, last one must
        // automatically inherit all the settings defined in the main one
        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        $bs2 = new mock_base_setting('test2', base_setting::IS_INTEGER, null);
        $bs3 = new mock_base_setting('test3', base_setting::IS_INTEGER, null);
        $bs1->add_dependency($bs2, setting_dependency::DISABLED_NOT_EMPTY);
        $bs2->add_dependency($bs3, setting_dependency::DISABLED_NOT_EMPTY);
        // Check values are spreaded ok
        $bs1->set_value(123);
        $this->assertEquals($bs1->get_value(), 123);
        $this->assertEquals($bs2->get_value(), $bs1->get_value());
        $this->assertEquals($bs3->get_value(), $bs1->get_value());

        // Add one more setting and set value again
        $bs4 = new mock_base_setting('test4', base_setting::IS_INTEGER, null);
        $bs2->add_dependency($bs4, setting_dependency::DISABLED_NOT_EMPTY);
        $bs2->set_value(321);
        // The above change should change
        $this->assertEquals($bs1->get_value(), 123);
        $this->assertEquals($bs2->get_value(), 321);
        $this->assertEquals($bs3->get_value(), 321);
        $this->assertEquals($bs4->get_value(), 321);

        // Check visibility is spreaded ok
        $bs1->set_visibility(base_setting::HIDDEN);
        $this->assertEquals($bs2->get_visibility(), $bs1->get_visibility());
        $this->assertEquals($bs3->get_visibility(), $bs1->get_visibility());
        // Check status is spreaded ok
        $bs1->set_status(base_setting::LOCKED_BY_HIERARCHY);
        $this->assertEquals($bs2->get_status(), $bs1->get_status());
        $this->assertEquals($bs3->get_status(), $bs1->get_status());

        // Create 3 settings and observe between them, put them in one array,
        // force serialize/deserialize to check the observable pattern continues
        // working after that
        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        $bs2 = new mock_base_setting('test2', base_setting::IS_INTEGER, null);
        $bs3 = new mock_base_setting('test3', base_setting::IS_INTEGER, null);
        $bs1->add_dependency($bs2, null, array('value'=>0));
        $bs2->add_dependency($bs3, null, array('value'=>0));
        // Serialize
        $arr = array($bs1, $bs2, $bs3);
        $ser = base64_encode(serialize($arr));
        // Unserialize and copy to new objects
        $newarr = unserialize(base64_decode($ser));
        $ubs1 = $newarr[0];
        $ubs2 = $newarr[1];
        $ubs3 = $newarr[2];
        // Must continue being base settings
        $this->assertTrue($ubs1 instanceof base_setting);
        $this->assertTrue($ubs2 instanceof base_setting);
        $this->assertTrue($ubs3 instanceof base_setting);
        // Set parent setting
        $ubs1->set_value(1234);
        $ubs1->set_visibility(base_setting::HIDDEN);
        $ubs1->set_status(base_setting::LOCKED_BY_HIERARCHY);
        // Check changes have been spreaded
        $this->assertEquals($ubs2->get_visibility(), $ubs1->get_visibility());
        $this->assertEquals($ubs3->get_visibility(), $ubs1->get_visibility());
        $this->assertEquals($ubs2->get_status(), $ubs1->get_status());
        $this->assertEquals($ubs3->get_status(), $ubs1->get_status());
    }

    /**
     * Test that locked and unlocked states on dependent backup settings at the same level
     * correctly do not flow from the parent to the child setting when the setting is locked by permissions.
     */
    public function test_dependency_empty_locked_by_permission_child_is_not_unlocked() {
        // Check dependencies are working ok.
        $bs1 = new mock_backup_setting('test1', base_setting::IS_INTEGER, 2);
        $bs1->set_level(1);
        $bs2 = new mock_backup_setting('test2', base_setting::IS_INTEGER, 2);
        $bs2->set_level(1); // Same level *must* work.
        $bs1->add_dependency($bs2, setting_dependency::DISABLED_EMPTY);

        $bs1->set_status(base_setting::LOCKED_BY_PERMISSION);
        $this->assertEquals(base_setting::LOCKED_BY_HIERARCHY, $bs2->get_status());
        $this->assertEquals(base_setting::LOCKED_BY_PERMISSION, $bs1->get_status());
        $bs2->set_status(base_setting::LOCKED_BY_PERMISSION);
        $this->assertEquals(base_setting::LOCKED_BY_PERMISSION, $bs1->get_status());

        // Unlocking the parent should NOT unlock the child.
        $bs1->set_status(base_setting::NOT_LOCKED);

        $this->assertEquals(base_setting::LOCKED_BY_PERMISSION, $bs2->get_status());
    }

    /**
     * Test that locked and unlocked states on dependent backup settings at the same level
     * correctly do flow from the parent to the child setting when the setting is locked by config.
     */
    public function test_dependency_not_empty_locked_by_config_parent_is_unlocked() {
        $bs1 = new mock_backup_setting('test1', base_setting::IS_INTEGER, 0);
        $bs1->set_level(1);
        $bs2 = new mock_backup_setting('test2', base_setting::IS_INTEGER, 0);
        $bs2->set_level(1); // Same level *must* work.
        $bs1->add_dependency($bs2, setting_dependency::DISABLED_NOT_EMPTY);

        $bs1->set_status(base_setting::LOCKED_BY_CONFIG);
        $this->assertEquals(base_setting::LOCKED_BY_HIERARCHY, $bs2->get_status());
        $this->assertEquals(base_setting::LOCKED_BY_CONFIG, $bs1->get_status());

        // Unlocking the parent should unlock the child.
        $bs1->set_status(base_setting::NOT_LOCKED);
        $this->assertEquals(base_setting::NOT_LOCKED, $bs2->get_status());
    }

    /**
     * test backup_setting class
     */
    public function test_backup_setting() {
        // Instantiate backup_setting class and set level
        $bs = new mock_backup_setting('test', base_setting::IS_INTEGER, null);
        $bs->set_level(1);
        $this->assertEquals($bs->get_level(), 1);

        // Instantiate backup setting class and try to add one non backup_setting dependency
        set_error_handler('\core_backup\backup_setting_error_handler', E_RECOVERABLE_ERROR);
        $bs = new mock_backup_setting('test', base_setting::IS_INTEGER, null);
        try {
            $bs->add_dependency(new \stdClass());
            $this->assertTrue(false, 'backup_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof backup_setting_exception);
            $this->assertEquals($e->errorcode, 'incorrect_object_passed');
        } catch (\TypeError $e) {
            // On PHP7+ we get a TypeError raised, lets check we've the right error.
            $this->assertMatchesRegularExpression('/must be (an instance of|of type) base_setting/', $e->getMessage());
        }
        restore_error_handler();

        // Try to assing upper level dependency
        $bs1 = new mock_backup_setting('test1', base_setting::IS_INTEGER, null);
        $bs1->set_level(1);
        $bs2 = new mock_backup_setting('test2', base_setting::IS_INTEGER, null);
        $bs2->set_level(2);
        try {
            $bs2->add_dependency($bs1);
            $this->assertTrue(false, 'backup_setting_exception expected');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof backup_setting_exception);
            $this->assertEquals($e->errorcode, 'cannot_add_upper_level_dependency');
        }

        // Check dependencies are working ok
        $bs1 = new mock_backup_setting('test1', base_setting::IS_INTEGER, null);
        $bs1->set_level(1);
        $bs2 = new mock_backup_setting('test2', base_setting::IS_INTEGER, null);
        $bs2->set_level(1); // Same level *must* work
        $bs1->add_dependency($bs2, setting_dependency::DISABLED_NOT_EMPTY);
        $bs1->set_value(123456);
        $this->assertEquals($bs2->get_value(), $bs1->get_value());
    }

    /**
     * test activity_backup_setting class
     */
    public function test_activity_backup_setting() {
        $bs = new mock_activity_backup_setting('test', base_setting::IS_INTEGER, null);
        $this->assertEquals($bs->get_level(), backup_setting::ACTIVITY_LEVEL);

        // Check checksum implementation is working
        $bs1 = new mock_activity_backup_setting('test', base_setting::IS_INTEGER, null);
        $bs1->set_value(123);
        $checksum = $bs1->calculate_checksum();
        $this->assertNotEmpty($checksum);
        $this->assertTrue($bs1->is_checksum_correct($checksum));
    }

    /**
     * test section_backup_setting class
     */
    public function test_section_backup_setting() {
        $bs = new mock_section_backup_setting('test', base_setting::IS_INTEGER, null);
        $this->assertEquals($bs->get_level(), backup_setting::SECTION_LEVEL);

        // Check checksum implementation is working
        $bs1 = new mock_section_backup_setting('test', base_setting::IS_INTEGER, null);
        $bs1->set_value(123);
        $checksum = $bs1->calculate_checksum();
        $this->assertNotEmpty($checksum);
        $this->assertTrue($bs1->is_checksum_correct($checksum));
    }

    /**
     * test course_backup_setting class
     */
    public function test_course_backup_setting() {
        $bs = new mock_course_backup_setting('test', base_setting::IS_INTEGER, null);
        $this->assertEquals($bs->get_level(), backup_setting::COURSE_LEVEL);

        // Check checksum implementation is working
        $bs1 = new mock_course_backup_setting('test', base_setting::IS_INTEGER, null);
        $bs1->set_value(123);
        $checksum = $bs1->calculate_checksum();
        $this->assertNotEmpty($checksum);
        $this->assertTrue($bs1->is_checksum_correct($checksum));
    }
}

/**
 * helper extended base_setting class that makes some methods public for testing
 */
class mock_base_setting extends base_setting {
    public function get_vtype() {
        return $this->vtype;
    }

    public function process_change($setting, $ctype, $oldv) {
        // Simply, inherit from the main object
        $this->set_value($setting->get_value());
        $this->set_visibility($setting->get_visibility());
        $this->set_status($setting->get_status());
    }
}

/**
 * helper extended backup_setting class that makes some methods public for testing
 */
class mock_backup_setting extends backup_setting {
    public function set_level($level) {
        $this->level = $level;
    }

    public function process_change($setting, $ctype, $oldv) {
        // Simply, inherit from the main object
        $this->set_value($setting->get_value());
        $this->set_visibility($setting->get_visibility());
        $this->set_status($setting->get_status());
    }
}

/**
 * helper extended activity_backup_setting class that makes some methods public for testing
 */
class mock_activity_backup_setting extends activity_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Do nothing
    }
}

/**
 * helper extended section_backup_setting class that makes some methods public for testing
 */
class mock_section_backup_setting extends section_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Do nothing
    }
}

/**
 * helper extended course_backup_setting class that makes some methods public for testing
 */
class mock_course_backup_setting extends course_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Do nothing
    }
}

/**
 * This error handler is used to convert errors to excpetions so that simepltest can
 * catch them.
 *
 * This is required in order to catch type hint mismatches that result in a error
 * being thrown. It should only ever be used to catch E_RECOVERABLE_ERROR's.
 *
 * It throws a backup_setting_exception with 'incorrect_object_passed'
 *
 * @param int $errno E_RECOVERABLE_ERROR
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 * @return null
 */
function backup_setting_error_handler($errno, $errstr, $errfile, $errline) {
    if ($errno !== E_RECOVERABLE_ERROR) {
        // Currently we only want to deal with type hinting errors
        return false;
    }
    throw new backup_setting_exception('incorrect_object_passed');
}
