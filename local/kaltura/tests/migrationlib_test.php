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
 * Kaltura local_kaltura_migration_progress class phpunit tests.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/local/kaltura/locallib.php');
require_once($CFG->dirroot.'/local/kaltura/migrationlib.php');

/**
 * @group local_kaltura
 */
class local_kaltura_migrationlib_testcase extends advanced_testcase {
    /**
     * Test initialization of config values.
     */
    public function test_initialization_of_config_values() {
        $this->resetAfterTest(true);

        $result = new local_kaltura_migration_progress();
        $result = null;

        $configsettings = get_config(KALTURA_PLUGIN_NAME);

        $this->assertObjectHasAttribute('migrationstarted', $configsettings);
        $this->assertObjectHasAttribute('existingcategoryrun', $configsettings);
        $this->assertObjectHasAttribute('sharedcategoryrun', $configsettings);
        $this->assertObjectHasAttribute('categoriescreated', $configsettings);
        $this->assertObjectHasAttribute('entriesmigrated', $configsettings);
        $this->assertObjectHasAttribute('kafcategoryrootid', $configsettings);
        $this->assertEquals(0, $configsettings->migrationstarted);
        $this->assertEquals(0, $configsettings->existingcategoryrun);
        $this->assertEquals(0, $configsettings->sharedcategoryrun);
        $this->assertEquals(0, $configsettings->categoriescreated);
        $this->assertEquals(0, $configsettings->entriesmigrated);
        $this->assertEquals(0, $configsettings->kafcategoryrootid);
    }

    /**
     * Test existingcategory accessor functions.
     */
    public function test_existingcategoryrun() {
        $this->resetAfterTest(true);

        $result = new local_kaltura_migration_progress();
        local_kaltura_migration_progress::set_existingcategoryrun(11);
        $value = local_kaltura_migration_progress::get_existingcategoryrun();

        $this->assertEquals(11, $value);

        $result = null;
        $value = get_config(KALTURA_PLUGIN_NAME, 'existingcategoryrun');

        $this->assertEquals(11, $value);
    }

    /**
     * Test sharedcategoryrun accessor functions.
     */
    public function test_sharedcategoryrun() {
        $this->resetAfterTest(true);

        $result = new local_kaltura_migration_progress();
        local_kaltura_migration_progress::set_sharedcategoryrun(11);
        $value = local_kaltura_migration_progress::get_sharedcategoryrun();

        $this->assertEquals(11, $value);

        $result = null;
        $value = get_config(KALTURA_PLUGIN_NAME, 'sharedcategoryrun');

        $this->assertEquals(11, $value);
    }

    /**
     * Test kafcategoryrootid functions.
     */
    public function test_kafcategoryrootid() {
        $this->resetAfterTest(true);

        $result = new local_kaltura_migration_progress();
        local_kaltura_migration_progress::set_kafcategoryrootid(11);
        $value = local_kaltura_migration_progress::get_kafcategoryrootid();

        $this->assertEquals(11, $value);

        $result = null;
        $value = get_config(KALTURA_PLUGIN_NAME, 'kafcategoryrootid');

        $this->assertEquals(11, $value);
    }

    /**
     * Test categoriescreated functions.
     */
    public function test_categoriescreated() {
        $this->resetAfterTest(true);

        $result = new local_kaltura_migration_progress();
        local_kaltura_migration_progress::increment_categoriescreated();
        $value = local_kaltura_migration_progress::get_categoriescreated();

        $this->assertEquals(1, $value);

        $result = null;
        $value = get_config(KALTURA_PLUGIN_NAME, 'categoriescreated');

        $this->assertEquals(1, $value);
    }

    /**
     * Test entriesmigrated functions.
     */
    public function test_entriesmigrated() {
        $this->resetAfterTest(true);

        $result = new local_kaltura_migration_progress();
        local_kaltura_migration_progress::increment_entriesmigrated();
        $value = local_kaltura_migration_progress::get_entriesmigrated();

        $this->assertEquals(1, $value);

        $result = null;
        $value = get_config(KALTURA_PLUGIN_NAME, 'entriesmigrated');

        $this->assertEquals(1, $value);
    }

    /**
     * Test migrationstarted functions.
     */
    public function test_migrationstarted() {
        $this->resetAfterTest(true);

        $result = new local_kaltura_migration_progress();
        local_kaltura_migration_progress::init_migrationstarted();
        $value = local_kaltura_migration_progress::get_migrationstarted();

        $this->assertNotEquals(0, $value);

        $result = null;
        $value = get_config(KALTURA_PLUGIN_NAME, 'migrationstarted');

        $this->assertNotEquals(0, $value);
    }

    /**
     * Test resetting all migration progress properties functions.
     */
    public function test_resetall() {
        $this->resetAfterTest(true);

        $result = new local_kaltura_migration_progress();
        local_kaltura_migration_progress::set_existingcategoryrun(11);
        local_kaltura_migration_progress::set_sharedcategoryrun(11);
        local_kaltura_migration_progress::increment_categoriescreated();
        local_kaltura_migration_progress::increment_entriesmigrated();
        local_kaltura_migration_progress::init_migrationstarted();
        local_kaltura_migration_progress::set_kafcategoryrootid(11);
        local_kaltura_migration_progress::reset_all();

        $result = null;

        $configsettings = get_config(KALTURA_PLUGIN_NAME);
        $this->assertEquals(0, $configsettings->migrationstarted);
        $this->assertEquals(0, $configsettings->existingcategoryrun);
        $this->assertEquals(0, $configsettings->sharedcategoryrun);
        $this->assertEquals(0, $configsettings->categoriescreated);
        $this->assertEquals(0, $configsettings->entriesmigrated);
        $this->assertEquals(0, $configsettings->kafcategoryrootid);
    }
}