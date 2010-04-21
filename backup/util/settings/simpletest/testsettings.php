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
 * @package moodlecore
 * @subpackage backup-tests
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prevent direct access to this file
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

// Include all the needed stuff
require_once($CFG->dirroot . '/backup/util/interfaces/checksumable.class.php');
require_once($CFG->dirroot . '/backup/backup.class.php');
require_once($CFG->dirroot . '/backup/util/settings/base_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/root/root_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/activity/activity_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/section/section_backup_setting.class.php');
require_once($CFG->dirroot . '/backup/util/settings/course/course_backup_setting.class.php');

/*
 * setting tests (all)
 */
class setting_test extends UnitTestCase {

    public static $includecoverage = array('backup/util/settings');
    public static $excludecoverage = array('backup/util/settings/simpletest');

    /*
     * test base_setting class
     */
    function test_base_setting() {
        // Instantiate base_setting and check everything
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN);
        $this->assertTrue($bs instanceof base_setting);
        $this->assertEqual($bs->get_name(), 'test');
        $this->assertEqual($bs->get_vtype(), base_setting::IS_BOOLEAN);
        $this->assertTrue(is_null($bs->get_value()));
        $this->assertEqual($bs->get_visibility(), base_setting::VISIBLE);
        $this->assertEqual($bs->get_status(), base_setting::NOT_LOCKED);

        // Instantiate base_setting with explicit nulls
        $bs = new mock_base_setting('test', base_setting::IS_FILENAME, 'filename.txt', null, null);
        $this->assertEqual($bs->get_value() , 'filename.txt');
        $this->assertEqual($bs->get_visibility(), base_setting::VISIBLE);
        $this->assertEqual($bs->get_status(), base_setting::NOT_LOCKED);

        // Instantiate base_setting and set value, visibility and status
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN);
        $bs->set_value(true);
        $this->assertTrue($bs->get_value());
        $bs->set_visibility(base_setting::HIDDEN);
        $this->assertEqual($bs->get_visibility(), base_setting::HIDDEN);
        $bs->set_status(base_setting::LOCKED_BY_HIERARCHY);
        $this->assertEqual($bs->get_status(), base_setting::LOCKED_BY_HIERARCHY);

        // Instantiate with wrong vtype
        try {
            $bs = new mock_base_setting('test', 'one_wrong_type');
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_invalid_type');
        }

        // Instantiate with wrong integer value
        try {
            $bs = new mock_base_setting('test', base_setting::IS_INTEGER, 99.99);
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_invalid_integer');
        }

        // Instantiate with wrong filename value
        try {
            $bs = new mock_base_setting('test', base_setting::IS_FILENAME, '../../filename.txt');
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_invalid_filename');
        }

        // Instantiate with wrong visibility
        try {
            $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN, null, 'one_wrong_visibility');
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_invalid_visibility');
        }

        // Instantiate with wrong status
        try {
            $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN, null, null, 'one_wrong_status');
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_invalid_status');
        }

        // Instantiate base_setting and try to set wrong ui_type
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN);
        try {
            $bs->set_ui('one_wrong_ui_type', 'label', array(), array());
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_invalid_ui_type');
        }

        // Instantiate base_setting and try to set wrong ui_label
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN);
        try {
            $bs->set_ui(base_setting::UI_HTML_CHECKBOX, 'one/wrong/label', array(), array());
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_invalid_ui_label');
        }
        // Try to change value of locked setting by permission
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN, null, null, base_setting::LOCKED_BY_PERMISSION);
        try {
            $bs->set_value(true);
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_locked_by_permission');
        }

        // Try to change value of locked setting by permission
        $bs = new mock_base_setting('test', base_setting::IS_BOOLEAN, null, null, base_setting::LOCKED_BY_HIERARCHY);
        try {
            $bs->set_value(true);
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_locked_by_hierarchy');
        }

        // Try to add same setting twice
        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        $bs2 = new mock_base_setting('test2', base_setting::IS_INTEGER, null);
        $bs1->add_dependency($bs2);
        try {
            $bs1->add_dependency($bs2);
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_already_added');
        }

        // Try to create one circular reference
        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        try {
            $bs1->add_dependency($bs1); // self
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_circular_reference');
            $this->assertTrue($e->a instanceof stdclass);
            $this->assertEqual($e->a->main, 'test1');
            $this->assertEqual($e->a->alreadydependent, 'test1');
        }

        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        $bs2 = new mock_base_setting('test2', base_setting::IS_INTEGER, null);
        $bs3 = new mock_base_setting('test3', base_setting::IS_INTEGER, null);
        $bs4 = new mock_base_setting('test4', base_setting::IS_INTEGER, null);
        $bs1->add_dependency($bs2);
        $bs2->add_dependency($bs3);
        $bs3->add_dependency($bs4);
        try {
            $bs4->add_dependency($bs1);
            $this->assertTrue(false, 'base_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof base_setting_exception);
            $this->assertEqual($e->errorcode, 'setting_circular_reference');
            $this->assertTrue($e->a instanceof stdclass);
            $this->assertEqual($e->a->main, 'test1');
            $this->assertEqual($e->a->alreadydependent, 'test4');
        }

        // Create 3 settings and observe between them, last one must
        // automatically inherit all the settings defined in the main one
        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        $bs2 = new mock_base_setting('test2', base_setting::IS_INTEGER, null);
        $bs3 = new mock_base_setting('test3', base_setting::IS_INTEGER, null);
        $bs1->add_dependency($bs2);
        $bs2->add_dependency($bs3);
        // Check values are spreaded ok
        $bs1->set_value(123);
        $this->assertEqual($bs1->get_value(), 123);
        $this->assertEqual($bs2->get_value(), $bs1->get_value());
        $this->assertEqual($bs3->get_value(), $bs1->get_value());

        // Add one more setting and set value again
        $bs4 = new mock_base_setting('test4', base_setting::IS_INTEGER, null);
        $bs2->add_dependency($bs4);
        $bs2->set_value(321);
        $this->assertEqual($bs2->get_value(), 321);
        $this->assertEqual($bs3->get_value(), $bs2->get_value());
        $this->assertEqual($bs4->get_value(), $bs3->get_value());

        // Check visibility is spreaded ok
        $bs1->set_visibility(base_setting::HIDDEN);
        $this->assertEqual($bs2->get_visibility(), $bs1->get_visibility());
        $this->assertEqual($bs3->get_visibility(), $bs1->get_visibility());
        // Check status is spreaded ok
        $bs1->set_status(base_setting::LOCKED_BY_HIERARCHY);
        $this->assertEqual($bs2->get_status(), $bs1->get_status());
        $this->assertEqual($bs3->get_status(), $bs1->get_status());

        // Create 3 settings and observe between them, put them in one array,
        // force serialize/deserialize to check the observable pattern continues
        // working after that
        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        $bs2 = new mock_base_setting('test2', base_setting::IS_INTEGER, null);
        $bs3 = new mock_base_setting('test3', base_setting::IS_INTEGER, null);
        $bs1->add_dependency($bs2);
        $bs2->add_dependency($bs3);
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
        $this->assertEqual($ubs2->get_visibility(), $ubs1->get_visibility());
        $this->assertEqual($ubs3->get_visibility(), $ubs1->get_visibility());
        $this->assertEqual($ubs2->get_status(), $ubs1->get_status());
        $this->assertEqual($ubs3->get_status(), $ubs1->get_status());

        // Check ui_attributes
        $bs1 = new mock_base_setting('test1', base_setting::IS_INTEGER, null);
        $bs1->set_ui(base_setting::UI_HTML_DROPDOWN, 'dropdown', array(1 => 'One', 2 => 'Two'), array('opt1' => 1, 'opt2' => 2));
        list($type, $label, $values, $options) = $bs1->get_ui_info();
        $this->assertEqual($type, base_setting::UI_HTML_DROPDOWN);
        $this->assertEqual($label, 'dropdown');
        $this->assertEqual(count($values), 2);
        $this->assertEqual($values[1], 'One');
        $this->assertEqual($values[2], 'Two');
        $this->assertEqual(count($options), 2);
        $this->assertEqual($options['opt1'], 1);
        $this->assertEqual($options['opt2'], 2);
    }

    /*
     * test backup_setting class
     */
    function test_backup_setting() {
        // Instantiate backup_setting class and set level
        $bs = new mock_backup_setting('test', base_setting::IS_INTEGER, null);
        $bs->set_level(1);
        $this->assertEqual($bs->get_level(), 1);

        // Instantiate backup setting class and try to add one non backup_setting dependency
        $bs = new mock_backup_setting('test', base_setting::IS_INTEGER, null);
        try {
            $bs->add_dependency(new stdclass());
            $this->assertTrue(false, 'backup_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_setting_exception);
            $this->assertEqual($e->errorcode, 'dependency_is_not_backkup_setting');
        }

        // Try to assing upper level dependency
        $bs1 = new mock_backup_setting('test1', base_setting::IS_INTEGER, null);
        $bs1->set_level(1);
        $bs2 = new mock_backup_setting('test2', base_setting::IS_INTEGER, null);
        $bs2->set_level(2);
        try {
            $bs2->add_dependency($bs1);
            $this->assertTrue(false, 'backup_setting_exception expected');
        } catch (exception $e) {
            $this->assertTrue($e instanceof backup_setting_exception);
            $this->assertEqual($e->errorcode, 'cannot_add_upper_level_dependency');
        }

        // Check dependencies are working ok
        $bs1 = new mock_backup_setting('test1', base_setting::IS_INTEGER, null);
        $bs1->set_level(1);
        $bs2 = new mock_backup_setting('test2', base_setting::IS_INTEGER, null);
        $bs2->set_level(1); // Same level *must* work
        $bs1->add_dependency($bs2);
        $bs1->set_value(123456);
        $this->assertEqual($bs2->get_value(), $bs1->get_value());
    }

    /*
     * test activity_backup_setting class
     */
    function test_activity_backup_setting() {
        $bs = new mock_activity_backup_setting('test', base_setting::IS_INTEGER, null);
        $this->assertEqual($bs->get_level(), backup_setting::ACTIVITY_LEVEL);

        // Check checksum implementation is working
        $bs1 = new mock_activity_backup_setting('test', base_setting::IS_INTEGER, null);
        $bs1->set_value(123);
        $checksum = $bs1->calculate_checksum();
        $this->assertTrue($checksum);
        $this->assertTrue($bs1->is_checksum_correct($checksum));
    }

    /*
     * test section_backup_setting class
     */
    function test_section_backup_setting() {
        $bs = new mock_section_backup_setting('test', base_setting::IS_INTEGER, null);
        $this->assertEqual($bs->get_level(), backup_setting::SECTION_LEVEL);

        // Check checksum implementation is working
        $bs1 = new mock_section_backup_setting('test', base_setting::IS_INTEGER, null);
        $bs1->set_value(123);
        $checksum = $bs1->calculate_checksum();
        $this->assertTrue($checksum);
        $this->assertTrue($bs1->is_checksum_correct($checksum));
    }

    /*
     * test course_backup_setting class
     */
    function test_course_backup_setting() {
        $bs = new mock_course_backup_setting('test', base_setting::IS_INTEGER, null);
        $this->assertEqual($bs->get_level(), backup_setting::COURSE_LEVEL);

        // Check checksum implementation is working
        $bs1 = new mock_course_backup_setting('test', base_setting::IS_INTEGER, null);
        $bs1->set_value(123);
        $checksum = $bs1->calculate_checksum();
        $this->assertTrue($checksum);
        $this->assertTrue($bs1->is_checksum_correct($checksum));
    }
}

/*
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

    public function get_ui_info() {
        // Return an array with all the ui info to be tested
        return array($this->ui_type, $this->ui_label, $this->ui_values, $this->ui_options);
    }
}

/*
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

/*
 * helper extended activity_backup_setting class that makes some methods public for testing
 */
class mock_activity_backup_setting extends activity_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Do nothing
    }
}

/*
 * helper extended section_backup_setting class that makes some methods public for testing
 */
class mock_section_backup_setting extends section_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Do nothing
    }
}

/*
 * helper extended course_backup_setting class that makes some methods public for testing
 */
class mock_course_backup_setting extends course_backup_setting {
    public function process_change($setting, $ctype, $oldv) {
        // Do nothing
    }
}
