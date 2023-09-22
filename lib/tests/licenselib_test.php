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
 * licenselib tests.
 *
 * @package    core
 * @copyright  2020 Tom Dickman <tom.dickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../licenselib.php');

/**
 * licenselib tests.
 *
 * @package    core
 * @copyright  2020 Tom Dickman <tom.dickman@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class licenselib_test extends advanced_testcase {

    /**
     * Test getting licenses from database or cache.
     */
    public function test_get_licenses() {
        $this->resetAfterTest();

        // Reset the cache, to make sure we are not getting cached licenses.
        $cache = \cache::make('core', 'license');
        $cache->delete('licenses');

        $licenses = license_manager::get_licenses();

        $this->assertArrayHasKey('unknown', $licenses);
        $this->assertArrayHasKey('allrightsreserved', $licenses);
        $this->assertArrayHasKey('public', $licenses);
        $this->assertArrayHasKey('cc-4.0', $licenses);
        $this->assertArrayHasKey('cc-nd-4.0', $licenses);
        $this->assertArrayHasKey('cc-nc-nd-4.0', $licenses);
        $this->assertArrayHasKey('cc-nc-4.0', $licenses);
        $this->assertArrayHasKey('cc-nc-sa-4.0', $licenses);
        $this->assertArrayHasKey('cc-sa-4.0', $licenses);

        // Get the licenses from cache and check again.
        $licenses = license_manager::get_licenses();

        $this->assertArrayHasKey('unknown', $licenses);
        $this->assertArrayHasKey('allrightsreserved', $licenses);
        $this->assertArrayHasKey('public', $licenses);
        $this->assertArrayHasKey('cc-4.0', $licenses);
        $this->assertArrayHasKey('cc-nd-4.0', $licenses);
        $this->assertArrayHasKey('cc-nc-nd-4.0', $licenses);
        $this->assertArrayHasKey('cc-nc-4.0', $licenses);
        $this->assertArrayHasKey('cc-nc-sa-4.0', $licenses);
        $this->assertArrayHasKey('cc-sa-4.0', $licenses);
    }

    /**
     * Test saving a license.
     */
    public function test_save() {
        global $DB;

        $this->resetAfterTest();

        $license = new stdClass();
        $license->shortname = 'mit';
        $license->fullname = 'MIT';
        $license->source = 'https://opensource.org/licenses/MIT';
        $license->version = '2020020200';
        $license->custom = license_manager::CUSTOM_LICENSE;

        license_manager::save($license);

        $license = $DB->get_record('license', ['shortname' => 'mit']);
        $this->assertNotEmpty($license);
        $this->assertEquals('mit', $license->shortname);

        // Attempting to update a core license should only update sortorder.
        $license->shortname = 'cc-4.0';
        $license->sortorder = 33;
        license_manager::save($license);

        $record = $DB->get_record('license', ['id' => $license->id]);
        $this->assertNotEquals('cc', $record->shortname);
        $record = $DB->get_record('license', ['shortname' => 'cc-4.0']);
        $this->assertEquals(33, $record->sortorder);

        // Adding a license with existing custom license shortname should update existing license.
        $updatelicense = new stdClass();
        $updatelicense->shortname = 'mit';
        $updatelicense->fullname = 'MIT updated';
        $updatelicense->source = 'https://en.wikipedia.org/wiki/MIT_License';

        license_manager::save($updatelicense);
        $actual = $DB->get_record('license', ['shortname' => 'mit']);

        $this->assertEquals($updatelicense->fullname, $actual->fullname);
        $this->assertEquals($updatelicense->source, $actual->source);
        // Fields not updated should remain the same.
        $this->assertEquals($license->version, $actual->version);
    }

    /**
     * Test ability to get a license by it's short name.
     */
    public function test_get_license_by_shortname() {

        $license = license_manager::get_license_by_shortname('cc-nc-4.0');
        $actual = $license->fullname;

        $this->assertEquals('Creative Commons - NonCommercial 4.0 International', $actual);
        $this->assertNull(license_manager::get_license_by_shortname('somefakelicense'));
    }

    /**
     * Test disabling a license.
     */
    public function test_disable_license() {
        global $DB;

        $this->resetAfterTest();

        // Manually set license record to enabled for testing.
        $DB->set_field('license', 'enabled', license_manager::LICENSE_ENABLED, ['shortname' => 'cc-nc-4.0']);

        $this->assertTrue(license_manager::disable('cc-nc-4.0'));

        $license = license_manager::get_license_by_shortname('cc-nc-4.0');
        $actual = $license->enabled;

        $this->assertEquals(license_manager::LICENSE_DISABLED, $actual);
    }

    /**
     * Test enabling a license.
     */
    public function test_enable_license() {
        global $DB;

        $this->resetAfterTest();

        // Manually set license record to disabled for testing.
        $DB->set_field('license', 'enabled', license_manager::LICENSE_DISABLED, ['shortname' => 'cc-nc-4.0']);

        $this->assertTrue(license_manager::enable('cc-nc-4.0'));

        $license = license_manager::get_license_by_shortname('cc-nc-4.0');
        $actual = $license->enabled;

        $this->assertEquals(license_manager::LICENSE_ENABLED, $actual);
    }

    /**
     * Test deleting a custom license.
     */
    public function test_delete() {
        $this->resetAfterTest();

        // Create a custom license.
        $license = new stdClass();
        $license->shortname = 'mit';
        $license->fullname = 'MIT';
        $license->source = 'https://opensource.org/licenses/MIT';
        $license->version = '2020020200';
        $license->custom = license_manager::CUSTOM_LICENSE;

        license_manager::save($license);

        // Should be able to delete a custom license.
        license_manager::delete($license->shortname);
        $this->assertNull(license_manager::get_license_by_shortname($license->shortname));
    }

    /**
     * Test trying to delete a license currently in use by a file.
     */
    public function test_delete_license_in_use_by_file() {
        $this->resetAfterTest();

        // Create a custom license.
        $license = new stdClass();
        $license->shortname = 'mit';
        $license->fullname = 'MIT';
        $license->source = 'https://opensource.org/licenses/MIT';
        $license->version = '2020020200';
        $license->custom = license_manager::CUSTOM_LICENSE;

        license_manager::save($license);

        // Create a test file with custom license selected.
        $fs = get_file_storage();
        $syscontext = context_system::instance();
        $filerecord = array(
            'contextid' => $syscontext->id,
            'component' => 'tool_metadata',
            'filearea' => 'unittest',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'test.doc',
        );
        $file = $fs->create_file_from_string($filerecord, 'Test file');
        $file->set_license($license->shortname);

        // Should not be able to delete a license when in use by a file.
        $this->expectException(moodle_exception::class);
        license_manager::delete($license->shortname);
    }

    /**
     * Test trying to delete a core license.
     */
    public function test_delete_license_core() {
        // Should not be able to delete a standard/core license.
        $this->expectException(moodle_exception::class);
        license_manager::delete('cc-nc-4.0');
    }

    /**
     * Test trying to delete a license which doesn't exist.
     */
    public function test_delete_license_not_exists() {
        // Should throw an exception if license with shortname doesn't exist.
        $this->expectException(moodle_exception::class);
        license_manager::delete('somefakelicense');
    }

    /**
     * Test setting active licenses.
     */
    public function test_set_active_licenses() {
        $this->resetAfterTest();

        // Private method used internally, test through disable and enable public methods.
        license_manager::disable('allrightsreserved');
        $this->assertStringNotContainsString('allrightsreserved', get_config('', 'licenses'));

        license_manager::enable('allrightsreserved');
        $this->assertStringContainsString('allrightsreserved', get_config('', 'licenses'));
    }

    /**
     * Test getting active licenses.
     */
    public function test_get_active_licenses() {
        $this->resetAfterTest();

        license_manager::disable('allrightsreserved');
        license_manager::reset_license_cache();

        $licenses = license_manager::get_active_licenses();
        $this->assertArrayNotHasKey('allrightsreserved', $licenses);

        license_manager::enable('allrightsreserved');
        license_manager::reset_license_cache();

        $licenses = license_manager::get_active_licenses();
        $this->assertArrayHasKey('allrightsreserved', $licenses);
    }

    /**
     * Test getting active licenses as array.
     */
    public function test_get_active_licenses_as_array() {
        $this->resetAfterTest();

        license_manager::disable('allrightsreserved');
        license_manager::reset_license_cache();

        $licenses = license_manager::get_active_licenses_as_array();
        $this->assertIsArray($licenses);
        $this->assertNotContains('All rights reserved', $licenses);

        license_manager::enable('allrightsreserved');
        license_manager::reset_license_cache();

        $licenses = license_manager::get_active_licenses_as_array();
        $this->assertIsArray($licenses);
        $this->assertContains('All rights reserved', $licenses);
    }

    /**
     * Test resetting the license cache.
     */
    public function test_reset_license_cache() {
        global $DB;

        $this->resetAfterTest();

        $licenses = license_manager::get_licenses();

        $cache = \cache::make('core', 'license');
        $cachedlicenses = $cache->get('licenses');

        $this->assertNotFalse($cachedlicenses);
        $this->assertEquals($licenses, $cachedlicenses);

        // Manually delete a license to see if cache persists.
        $DB->delete_records('license', ['shortname' => 'cc-nc-4.0']);
        $licenses = license_manager::get_licenses();

        $this->assertArrayHasKey('cc-nc-4.0', $licenses);

        license_manager::reset_license_cache();

        $licenses = license_manager::get_licenses();
        $this->assertArrayNotHasKey('cc-nc-4.0', $licenses);
    }

    /**
     * Test that all licenses are installed correctly.
     */
    public function test_install_licenses() {
        global $DB;

        $this->resetAfterTest();

        $DB->delete_records('license');

        license_manager::install_licenses();

        $expectedshortnames = [
            'allrightsreserved', 'public', 'unknown',
            'cc-4.0', 'cc-nc-4.0', 'cc-nc-nd-4.0', 'cc-nc-sa-4.0', 'cc-nd-4.0', 'cc-sa-4.0',
        ];
        $actualshortnames = $DB->get_records_menu('license', null, '', 'id, shortname');

        foreach ($expectedshortnames as $expectedshortname) {
            $this->assertContains($expectedshortname, $actualshortnames);
        }
    }
}
